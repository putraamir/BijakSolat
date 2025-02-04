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
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['RUKUN', 'SUNAT']);
            $table->integer('year');
            $table->integer('sequence');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_items');
    }
};
