<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Task thuộc về group nào — xác định project qua group
            $table->foreignId('group_id')
                ->constrained('task_groups')
                ->onDelete('cascade');

            $table->string('name');
            $table->integer('ordering')->default(0);
            $table->text('description')->nullable();

            // Trạng thái task — todo/done
            $table->string('status')->default('todo');

            // Người được giao và người tạo
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Mức độ ưu tiên
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low');

            // Thời gian
            $table->dateTime('start_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('remind_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Index tăng tốc truy vấn
            $table->index('group_id');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};