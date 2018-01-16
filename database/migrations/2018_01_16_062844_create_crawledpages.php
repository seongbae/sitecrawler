<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrawledpages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawled_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();
            $table->string('scheme');
            $table->string('host');
            $table->string('path');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->mediumText('html')->nullable();
            $table->string('status'); // 1 = good, 2 = 404, 3 = 500, etc.
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
        Schema::dropIfExists('crawled_pages');
    }
}
