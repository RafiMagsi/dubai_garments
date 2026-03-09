<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('source')->default('storefront')->after('id');
            $table->string('customer_name')->nullable()->after('source');
            $table->string('company')->nullable()->after('customer_name');
            $table->string('email')->nullable()->after('company');
            $table->string('phone')->nullable()->after('email');
            $table->string('product_slug')->nullable()->after('phone');
            $table->string('product_type')->nullable()->after('product_slug');
            $table->unsignedInteger('quantity')->nullable()->after('product_type');
            $table->date('required_delivery_date')->nullable()->after('quantity');
            $table->string('design_file_path')->nullable()->after('required_delivery_date');
            $table->text('message')->nullable()->after('design_file_path');
            $table->unsignedTinyInteger('ai_score')->nullable()->after('message');
            $table->string('classification')->nullable()->after('ai_score');
            $table->string('status')->default('NEW')->after('classification');
            $table->json('meta')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'customer_name',
                'company',
                'email',
                'phone',
                'product_slug',
                'product_type',
                'quantity',
                'required_delivery_date',
                'design_file_path',
                'message',
                'ai_score',
                'classification',
                'status',
                'meta',
            ]);
        });
    }
};
