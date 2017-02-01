<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_id');
            $table->string('title', 511);
            $table->string('sku');
            $table->string('publisher');
            $table->unsignedInteger('price');
            $table->timestamp('date')->useCurrent = true;
            $table->string('internal_id');
            $table->unsignedInteger('length')->nullable();
            $table->text('abstract')->nullable();
            $table->text('toctext')->nullable();

            $table->index('date');
            $table->index('publisher');
            $table->unique('content_id');
            $table->unique('internal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
