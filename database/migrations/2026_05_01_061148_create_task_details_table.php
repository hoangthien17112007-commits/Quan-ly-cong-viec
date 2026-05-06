<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. TASK CHECKLISTS — nhóm checklist (hôm nay, ngày mai)
        // -------------------------------------------------------
        Schema::create('task_checklists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->string('name');
            $table->integer('ordering')->default(0);

            $table->timestamps();

            $table->index('task_id');
        });

        // -------------------------------------------------------
        // 2. TASK CHECKLIST ITEMS — task con bên trong checklist
        // -------------------------------------------------------
        Schema::create('task_checklist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('checklist_id')
                ->constrained('task_checklists')
                ->onDelete('cascade');

            $table->string('name');
            $table->boolean('is_checked')->default(false);
            $table->integer('ordering')->default(0);

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('remind_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->index('checklist_id');
            $table->index('assigned_to');
        });

        // -------------------------------------------------------
        // 3. TASK LABELS — label dùng chung trong project
        // -------------------------------------------------------
        Schema::create('task_labels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('color', 7); // hex: "#EF4444"

            $table->timestamps();

            $table->index('project_id');
        });

        // -------------------------------------------------------
        // 4. TASK LABEL (pivot) — task gắn với label nào
        // -------------------------------------------------------
        Schema::create('task_label', function (Blueprint $table) {
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignId('task_label_id')
                ->constrained('task_labels')
                ->onDelete('cascade');

            $table->primary(['task_id', 'task_label_id']);
        });

        // -------------------------------------------------------
        // 5. TASK MEMBERS — nhiều thành viên tham gia 1 task
        // -------------------------------------------------------
        Schema::create('task_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
            $table->index('task_id');
        });

        // -------------------------------------------------------
        // 6. ALTER TASKS — thêm cover_image, cover_color
        // -------------------------------------------------------
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('description');
            $table->string('cover_color', 7)->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'cover_color']);
        });

        Schema::dropIfExists('task_members');
        Schema::dropIfExists('task_label');
        Schema::dropIfExists('task_labels');
        Schema::dropIfExists('task_checklist_items');
        Schema::dropIfExists('task_checklists');
    }
};