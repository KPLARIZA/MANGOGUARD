<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pest_alerts', function (Blueprint $table) {
            // Drop existing columns
            if (Schema::hasColumn('pest_alerts', 'alert_type')) {
                $table->dropColumn('alert_type');
            }
            if (Schema::hasColumn('pest_alerts', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('pest_alerts', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
            if (Schema::hasColumn('pest_alerts', 'notes')) {
                $table->dropColumn('notes');
            }

            // Add new columns
            if (!Schema::hasColumn('pest_alerts', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('pest_alerts', 'title')) {
                $table->string('title')->after('user_id');
            }
            if (!Schema::hasColumn('pest_alerts', 'description')) {
                $table->text('description')->after('title');
            }
            if (!Schema::hasColumn('pest_alerts', 'location')) {
                $table->string('location')->after('description');
            }
            if (!Schema::hasColumn('pest_alerts', 'pest_type')) {
                $table->string('pest_type')->default('unknown')->after('location');
            }
            if (!Schema::hasColumn('pest_alerts', 'severity')) {
                $table->string('severity')->default('medium')->after('pest_type');
            }
            if (!Schema::hasColumn('pest_alerts', 'status')) {
                $table->string('status')->default('active')->after('severity');
            }
        });
    }

    public function down()
    {
        Schema::table('pest_alerts', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'title',
                'description',
                'location',
                'pest_type',
                'severity',
                'status'
            ]);

            // Restore original columns
            $table->foreignId('pest_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('trap_id')->constrained()->onDelete('cascade');
            $table->string('alert_type');
            $table->text('message');
            $table->timestamp('resolved_at')->nullable();
            $table->text('notes')->nullable();
        });
    }
}; 