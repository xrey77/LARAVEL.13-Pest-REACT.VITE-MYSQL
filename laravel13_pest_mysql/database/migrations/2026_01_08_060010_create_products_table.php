<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('descriptions')->unique();
            $table->integer('qty')->default(0);
            $table->string('unit');
            $table->decimal('costprice')->default(0);
            $table->decimal('sellprice')->default(0);
            $table->decimal('saleprice')->default(0);
            $table->string('productpicture');
            $table->integer('alertstocks')->default(0);
            $table->integer('criticalstrocks')->default(0);
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
