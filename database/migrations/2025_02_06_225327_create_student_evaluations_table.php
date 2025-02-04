<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluation_item_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['passed', 'not_passed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_evaluations');
    }
};
