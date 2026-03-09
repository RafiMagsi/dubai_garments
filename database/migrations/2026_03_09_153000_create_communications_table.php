<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction', 20)->default('outgoing');
            $table->string('recipient_email', 190);
            $table->string('subject', 190);
            $table->text('message');
            $table->string('status', 30)->default('sent');
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
            $table->index(['deal_id', 'created_at']);
            $table->index(['quote_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
