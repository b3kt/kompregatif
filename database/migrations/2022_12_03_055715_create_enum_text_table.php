<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enum_texts', function (Blueprint $table) {
            $table->id();
            $table->text('code')->unique();
            $table->text('name');
            $table->text('label')->nullable();
            $table->text('value');
            $table->text('notes')->nullable();
            $table->text('group');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_texts');
    }
};
