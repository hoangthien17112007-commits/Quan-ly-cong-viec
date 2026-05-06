<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Quan hệ người dùng
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_leader')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Trạng thái dự án
            $table->string('status')->default('pending');

            // Giới hạn số TaskGroup trong dự án — null = không giới hạn
            $table->unsignedInteger('wip_limit')->nullable();

            $table->dateTime('deadline_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};