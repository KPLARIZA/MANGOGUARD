<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pest_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trap_id')->constrained()->onDelete('cascade');
            $table->enum('pest_type', ['cecid_fly', 'fruit_fly', 'unknown']);
            $table->integer('count');
            $table->timestamp('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pest_data');
    }
}; 