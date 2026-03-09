<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete()->after('id');
            $table->string('stage', 32)->default('NEW')->after('lead_id')->index();
            $table->string('priority', 32)->default('medium')->after('stage');
            $table->decimal('value_estimate', 12, 2)->nullable()->after('priority');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete()->after('value_estimate');
            $table->text('notes')->nullable()->after('assigned_user_id');
            $table->json('meta')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropConstrainedForeignId('lead_id');
            $table->dropColumn([
                'stage',
                'priority',
                'value_estimate',
                'notes',
                'meta',
            ]);
        });
    }
};
