<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove timestamp columns if they exist
            if (Schema::hasColumn('orders', 'order_email_sent_at')) {
                $table->dropColumn('order_email_sent_at');
            }
            if (Schema::hasColumn('orders', 'status_email_sent_at')) {
                $table->dropColumn('status_email_sent_at');
            }
        });

        // Boolean columns already exist, no need to add them
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back timestamp columns
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('order_email_sent_at')->nullable()->after('transaction_id');
            $table->timestamp('status_email_sent_at')->nullable()->after('order_email_sent_at');
        });
    }
};
