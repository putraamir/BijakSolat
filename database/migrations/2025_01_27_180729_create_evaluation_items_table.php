<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['RUKUN', 'SUNAT', 'SOLAT JENAZAH', 'SOLAT BERJEMAAH']);
            $table->integer('sequence');
            $table->string('category');
            $table->integer('year');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_items');
    }
};
