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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });                        

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('mobile')->nullable();
            $table->string('username')->unique();
            $table->text('password')->nullable();
            $table->integer('role_id')->default(0);
            $table->string('profilepic')->nullable();
            $table->integer('isactivated')->default(0);
            $table->integer('isblocked')->default(0);
            $table->integer('mailtoken')->default(0);
            $table->text('qrcodeurl')->nullable();
            $table->text('secretkey')->nullable();
            $table->rememberToken();            
            $table->timestamps();
        });


        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');        
        Schema::dropIfExists('users');
    }
};
