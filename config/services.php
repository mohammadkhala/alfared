<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'fcm' => [
        'server_key'      => env('FCM_SERVER_KEY'),       // legacy (unused)
        'project_id'      => env('FCM_PROJECT_ID'),       // v1 API: your Firebase project ID
        'service_account' => env('FCM_SERVICE_ACCOUNT_PATH'), // v1 API: path to service account JSON
    ],

    'wasender' => [
        'api_key' => env('WASENDER_API_KEY'),
    ],

    'lahza' => [
        'public_key' => env('LAHZA_PUBLIC_KEY'),
        'secret_key' => env('LAHZA_SECRET_KEY'),
        'currency'   => env('LAHZA_CURRENCY', 'ILS'),
    ],

    // Off-site backup storage. When both are set, backups upload here and the
    // local copy is removed. credentials = absolute path to the service-account
    // JSON key (keep it outside the web root and out of git).
    'google_drive' => [
        'credentials' => env('GOOGLE_DRIVE_CREDENTIALS'),
        'folder_id'   => env('GOOGLE_DRIVE_FOLDER_ID'),
    ],

    'roadfn' => [
        'base_url'    => env('ROADFN_BASE_URL', 'https://api.roadfn.com/'),
        'username'    => env('ROADFN_USERNAME'),
        'password'    => env('ROADFN_PASSWORD'),
        // RoadFN rejects the login when this is empty; it only labels the
        // session, so any stable string works.
        'device_token' => env('ROADFN_DEVICE_TOKEN') ?: 'alfared-server',

        // Shipment type sent on creation (RoadFN /ShipmentsTypes). 29 = "اخرى" (other).
        'default_shipment_type' => (int) env('ROADFN_DEFAULT_SHIPMENT_TYPE', 29),

        // RoadFN StatusId => our Order status. Built from the real /ListStatus
        // catalogue (2026-07-18). Any StatusId not listed here leaves our order
        // status untouched and is logged, so an unknown state never mislabels an
        // order. Adjust freely — this is the single source of truth for the map.
        // Admin owns pending→confirmed→processing→sent_to_delivery. Once the
        // shipment is with RoadFN, RoadFN drives the rest. So the early RoadFN
        // states (received at office/branch) keep the order at sent_to_delivery,
        // and only a driver pickup moves it to "shipped" (مع المندوب).
        'status_map' => [
            2  => 'sent_to_delivery', // مؤكدة (Submitted)
            4  => 'sent_to_delivery', // في المكتب (Picked Up Office)
            5  => 'sent_to_delivery', // بحاجة متابعة (On Hold)
            12 => 'sent_to_delivery', // نقل بين الفروع (Transfer Branch)
            13 => 'sent_to_delivery', // متابعة قبل الإرجاع (Ready for pickup)
            18 => 'sent_to_delivery', // إرسال الشحنات للشركة (need to follow)
            19 => 'sent_to_delivery', // تأجيلات (postponed)
            22 => 'sent_to_delivery', // تحويل بين المكاتب
            14 => 'shipped',          // مع السائق / مع المندوب (With Driver)
            8  => 'delivered',        // تحصيلات مع السائقين — COD collected
            9  => 'delivered',        // في المحاسبة (In Accounting)
            10 => 'delivered',        // مغلق (Closed)
            11 => 'cancelled',        // ملغي (Cancelled)
            3  => 'returned',         // بانتظار الإرجاع من السائق
            7  => 'returned',         // مرتجع للمرسل (Returned)
            17 => 'returned',         // تحويل فرع للطرود المرتجعة
            23 => 'returned',         // طرود مرتجعة مغلقة (Closed Returned)
        ],
    ],

];
