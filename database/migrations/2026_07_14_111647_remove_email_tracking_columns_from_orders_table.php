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
        $table->dropColumn([
            'order_email_sent',
            'status_email_sent'
        ]);
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->timestamp('order_email_sent_at')->nullable();
        $table->timestamp('status_email_sent_at')->nullable();

        $table->boolean('order_email_sent')->default(false);
        $table->boolean('status_email_sent')->default(false);
    });
}
};
