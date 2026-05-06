<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. TASK COMMENTS — bình luận, hỗ trợ reply lồng nhau
        // -------------------------------------------------------
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('task_comments')
                ->nullOnDelete();

            $table->text('content');
            $table->timestamp('edited_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('task_id');
            $table->index('parent_id');
        });

        // -------------------------------------------------------
        // 2. TASK ATTACHMENTS — file đính kèm
        // -------------------------------------------------------
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_bytes');
            $table->boolean('is_cover')->default(false);

            $table->timestamps();

            $table->index('task_id');
        });

        // -------------------------------------------------------
        // 3. TASK ACTIVITIES — log tự động mọi thay đổi
        // -------------------------------------------------------
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // created | updated | status_changed | assigned
            // label_added | member_added | checklist_completed | commented
            $table->string('action');

            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('task_id');
            $table->index(['task_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_comments');
    }
};