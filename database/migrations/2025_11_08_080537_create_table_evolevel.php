<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_student');
            $table->unsignedBigInteger('id_class');
            $table->timestamps();
        });

        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_teacher');
            $table->unsignedBigInteger('id_class');
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('level', ['SD', 'MI', 'SMP', 'MTs', 'SMA', 'SMK', 'MA', 'PT']);
            $table->enum('grade', ['1', '2', '3', '4', '5', '6'])->nullable(); // Enum 1-6 & Nullable
            $table->enum('semester', ['odd', 'even']);
            $table->string('token')->unique();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('subject', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_class');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('id_subject');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('addaptive', ['yes', 'no']);
            $table->enum('status', ['basic', 'additional', 'remedial']);
            $table->enum('type', ['task', 'quiz', 'evaluation']);
            $table->integer('durasi_pengerjaan')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->integer('jumlah_soal')->nullable();
            $table->integer('kkm')->nullable();
            // id_topic dibuat nullable karena tipe evaluation bisa memiliki banyak topik melalui tabel activity_topics
            $table->unsignedBigInteger('id_topic')->nullable();
            $table->timestamps();
        });

        // TABEL PENGHUBUNG BARU: activity_topics (Many-to-Many Activities & Topics)
        Schema::create('activity_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_activity');
            $table->unsignedBigInteger('id_topic');
            $table->timestamps();

            // Mencegah duplikasi topik pada activity yang sama
            $table->unique(['id_activity', 'id_topic']);
        });

        Schema::create('activity_question', function (Blueprint $table) {
            $table->unsignedBigInteger('id_activity');
            $table->unsignedBigInteger('id_question');
            $table->timestamps();
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['MultipleChoice', 'ShortAnswer']);
            $table->string('tags')->nullable(); 
            $table->string('hint')->nullable();
            $table->json('question');
            $table->json('MC_option')->nullable();
            $table->json('SA_answer')->nullable();
            $table->char('MC_answer')->nullable();
            $table->enum('difficulty', ['mudah', 'sedang', 'sulit']);
            $table->decimal('delta', 8, 2)->default(0.00);
            $table->unsignedBigInteger('id_topic');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('user_badge', function (Blueprint $table) {
            $table->unsignedBigInteger('id_student');
            $table->unsignedBigInteger('id_badge');
            $table->unsignedBigInteger('id_class')->nullable();
            $table->timestamps();
        });

        Schema::create('badge', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('path_icon');
            $table->timestamps();
        });

        Schema::create('activity_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_activity');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('result_status', ['Pass', 'Remedial'])->nullable();
            $table->decimal('result', 5, 2)->nullable();
            $table->decimal('skor_logit', 8, 4)->nullable();
            $table->integer('real_poin')->default(0)->nullable();
            $table->integer('bonus_poin')->default(0)->nullable();
            $table->integer('waktu_mengerjakan')->nullable();
            $table->integer('total_benar')->nullable();
            $table->boolean('status_benar')->default(false)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('value');
        });

        Schema::create('activity_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_activity')->comment('sumber activity');
            $table->unsignedBigInteger('created_by')->nullable()->comment('guru yang membuat paket');
            $table->unsignedBigInteger('id_class')->nullable()->comment('kelas sumber (opsional)');
            $table->string('title')->nullable();
            $table->string('filename')->comment('path file JSON di storage');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('id_activity');
            $table->index('created_by');
            $table->index('id_class');
        });

        Schema::create('activity_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_activity');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_question');
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('delta', 8, 4)->nullable();
            $table->timestamps();

            $table->unique(['id_activity', 'id_user', 'id_question'], 'activity_answer_unique');
            $table->index('id_activity');
            $table->index('id_user');
            $table->index('id_question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_answers');
        Schema::dropIfExists('activity_packages');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_result');
        Schema::dropIfExists('badge');
        Schema::dropIfExists('user_badge');
        Schema::dropIfExists('question');
        Schema::dropIfExists('activity_question');
        Schema::dropIfExists('activity_topics'); // Drop tabel baru
        Schema::dropIfExists('activities');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subject');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('teacher_classes');
        Schema::dropIfExists('student_classes');
    }
};