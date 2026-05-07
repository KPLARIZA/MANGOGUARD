<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pest_advice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('pest_type', ['cecid_fly', 'fruit_fly', 'unknown']);
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->text('preventive_measures');
            $table->text('control_methods');
            $table->text('chemical_treatments')->nullable();
            $table->text('organic_treatments')->nullable();
            $table->text('monitoring_tips');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pest_advice');
    }
}; 