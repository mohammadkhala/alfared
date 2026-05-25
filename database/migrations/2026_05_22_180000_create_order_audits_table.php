<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();   // snapshot in case user deleted
            $table->string('action', 60);              // status_change, edit, create, delete_item, add_item, cancel
            $table->json('changes')->nullable();        // before/after diff
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });

        // Helpful columns on orders for last-edit summary
        if (! Schema::hasColumn('orders', 'last_edited_by')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('last_edited_by')->nullable()->after('admin_notes')->constrained('users')->nullOnDelete();
                $table->timestamp('last_edited_at')->nullable()->after('last_edited_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_audits');
        if (Schema::hasColumn('orders', 'last_edited_by')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('last_edited_by');
                $table->dropColumn('last_edited_at');
            });
        }
    }
};
