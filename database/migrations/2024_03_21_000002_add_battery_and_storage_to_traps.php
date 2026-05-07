<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('traps', function (Blueprint $table) {
            if (!Schema::hasColumn('traps', 'battery_level')) {
                $table->double('battery_level', 8, 2)->default(100);
            }
            if (!Schema::hasColumn('traps', 'storage_volume')) {
                $table->double('storage_volume', 8, 2)->default(0);
            }
            $table->float('storage_threshold')->default(1.0); // 1kg threshold
            if (!Schema::hasColumn('traps', 'last_maintenance')) {
                $table->timestamp('last_maintenance')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('traps', function (Blueprint $table) {
            $table->dropColumn([
                'battery_level',
                'storage_volume',
                'storage_threshold',
                'last_maintenance'
            ]);
        });
    }
}; 