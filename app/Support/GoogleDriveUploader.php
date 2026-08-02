<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

/**
 * Uploads a file to a Google Drive folder using a service account.
 *
 * Pure HTTP (Guzzle via Laravel's Http client) and openssl for the JWT, so
 * nothing shells out — this host disables proc_open/shell_exec, which rules
 * out the usual google/apiclient + gRPC path. Scope is drive.file, so the
 * service account can only touch files it creates, not the rest of a Drive.
 */
class GoogleDriveUploader
{
    private const TOKEN_URL  = 'https://oauth2.googleapis.com/token';
    private const UPLOAD_URL = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true';

    public function __construct(
        private string $credentialsPath,
        private string $folderId,
    ) {}

    public static function fromConfig(): ?self
    {
        $path   = (string) config('services.google_drive.credentials');
        $folder = (string) config('services.google_drive.folder_id');

        if ($path === '' || $folder === '' || ! is_file($path)) {
            return null;   // not configured → caller keeps the local backup
        }

        return new self($path, $folder);
    }

    /** Uploads a local file and returns the created Drive file id. */
    public function upload(string $localPath, ?string $remoteName = null): string
    {
        $creds = json_decode((string) file_get_contents($this->credentialsPath), true);
        if (! isset($creds['client_email'], $creds['private_key'])) {
            throw new \RuntimeException('ملف اعتماد Google Drive غير صالح.');
        }

        $token = $this->accessToken($creds);

        $metadata = [
            'name'    => $remoteName ?? basename($localPath),
            'parents' => [$this->folderId],
        ];

        // Multipart upload: metadata part + the file bytes in one request.
        $response = Http::withToken($token)
            ->timeout(600)
            ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
            ->attach('file', file_get_contents($localPath), $metadata['name'])
            ->post(self::UPLOAD_URL);

        if (! $response->successful()) {
            throw new \RuntimeException('فشل رفع الملف إلى Google Drive: ' . mb_substr($response->body(), 0, 300));
        }

        return (string) $response->json('id');
    }

    /** Exchanges the service-account key for a short-lived access token. */
    private function accessToken(array $creds): string
    {
        $now = time();
        $claim = [
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file',
            'aud'   => self::TOKEN_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $jwt = $this->signJwt($claim, $creds['private_key']);

        $response = Http::asForm()->timeout(30)->post(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new \RuntimeException('تعذّر الحصول على توكن Google: ' . mb_substr($response->body(), 0, 300));
        }

        return (string) $response->json('access_token');
    }

    /** Builds and RS256-signs a JWT with the service account's private key. */
    private function signJwt(array $claim, string $privateKey): string
    {
        $segments = [
            $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])),
            $this->base64Url(json_encode($claim)),
        ];
        $signingInput = implode('.', $segments);

        $signature = '';
        if (! openssl_sign($signingInput, $signature, $privateKey, 'sha256')) {
            throw new \RuntimeException('تعذّر توقيع JWT — تحقّق من المفتاح الخاص.');
        }

        $segments[] = $this->base64Url($signature);

        return implode('.', $segments);
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
