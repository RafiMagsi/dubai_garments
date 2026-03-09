<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete()->after('id');
            $table->string('quote_number', 32)->nullable()->unique()->after('deal_id');
            $table->json('items_json')->nullable()->after('quote_number');
            $table->decimal('subtotal', 12, 2)->default(0)->after('items_json');
            $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            $table->decimal('total_price', 12, 2)->default(0)->after('discount');
            $table->string('currency', 8)->default('AED')->after('total_price');
            $table->string('status', 32)->default('DRAFT')->after('currency')->index();
            $table->string('pdf_url')->nullable()->after('status');
            $table->timestamp('sent_at')->nullable()->after('pdf_url');
            $table->date('expires_at')->nullable()->after('sent_at');
            $table->text('notes')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deal_id');
            $table->dropColumn([
                'quote_number',
                'items_json',
                'subtotal',
                'discount',
                'total_price',
                'currency',
                'status',
                'pdf_url',
                'sent_at',
                'expires_at',
                'notes',
            ]);
        });
    }
};
