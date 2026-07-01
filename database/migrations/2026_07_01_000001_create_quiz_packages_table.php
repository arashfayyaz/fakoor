<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image');
            $table->longText('descriptions')->nullable();
            $table->unsignedBigInteger('const_price')->default(0);
            $table->string('reduction_type')->nullable();
            $table->decimal('reduction_value', 40, 8)->default(0);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->unsignedInteger('enter_count')->default(1);
            $table->boolean('sellable')->default(true);
            $table->string('status')->default('published');
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_packages');
    }
};
