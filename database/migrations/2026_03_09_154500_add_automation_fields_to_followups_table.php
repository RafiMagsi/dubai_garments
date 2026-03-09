<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->after('deal_id')->constrained()->nullOnDelete();
            $table->string('step', 80)->nullable()->after('quote_id');
            $table->timestamp('next_run')->nullable()->after('step');
            $table->string('status', 30)->default('pending')->after('next_run');
            $table->string('subject', 190)->nullable()->after('status');
            $table->text('message')->nullable()->after('subject');
            $table->timestamp('sent_at')->nullable()->after('message');
            $table->text('error_message')->nullable()->after('sent_at');
            $table->json('meta')->nullable()->after('error_message');

            $table->index(['status', 'next_run']);
            $table->index(['quote_id', 'status']);
            $table->index(['deal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->dropIndex(['status', 'next_run']);
            $table->dropIndex(['quote_id', 'status']);
            $table->dropIndex(['deal_id', 'status']);

            $table->dropConstrainedForeignId('deal_id');
            $table->dropConstrainedForeignId('quote_id');

            $table->dropColumn([
                'step',
                'next_run',
                'status',
                'subject',
                'message',
                'sent_at',
                'error_message',
                'meta',
            ]);
        });
    }
};
