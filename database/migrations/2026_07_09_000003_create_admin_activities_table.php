<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('module', 32)->index();
            $table->string('type', 64)->index();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('action_url')->nullable();
            $table->string('icon', 64)->nullable();
            $table->nullableMorphs('subject');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activities');
    }
};
