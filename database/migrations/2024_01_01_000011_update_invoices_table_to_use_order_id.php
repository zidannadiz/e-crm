<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecrm_invoices', function (Blueprint $table) {
            // Check if project_id exists, if so, drop it and add order_id
            if (Schema::hasColumn('ecrm_invoices', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
            
            // Add order_id if it doesn't exist
            if (!Schema::hasColumn('ecrm_invoices', 'order_id')) {
                $table->foreignId('order_id')->after('id')->constrained('ecrm_orders')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecrm_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('ecrm_invoices', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            
            // Restore project_id if needed
            if (!Schema::hasColumn('ecrm_invoices', 'project_id')) {
                $table->foreignId('project_id')->after('id')->nullable()->constrained('ecrm_projects')->onDelete('cascade');
            }
        });
    }
};

