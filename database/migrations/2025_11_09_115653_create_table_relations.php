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
        Schema::table('user_badge', function (Blueprint $table) {
            $table->foreign('id_student')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_badge')->references('id')->on('badge')->onDelete('cascade');
        });

        Schema::table('student_classes', function (Blueprint $table) {
            $table->foreign('id_student')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_class')->references('id')->on('classes')->onDelete('cascade');
        });

        Schema::table('teacher_classes', function (Blueprint $table) {
            $table->foreign('id_teacher')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_class')->references('id')->on('classes')->onDelete('cascade');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('subject', function (Blueprint $table) {
            $table->foreign('id_class')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->foreign('id_subject')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('activities', function (Blueprint $table) {
            // Karena id_topic bisa nullable untuk evaluasi
            $table->foreign('id_topic')->references('id')->on('topics')->onDelete('set null');
        });

        // FOREIGN KEYS UNTUK TABEL PENGHUBUNG activity_topics
        Schema::table('activity_topics', function (Blueprint $table) {
            $table->foreign('id_activity')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('id_topic')->references('id')->on('topics')->onDelete('cascade');
        });

        Schema::table('activity_question', function (Blueprint $table) {
            $table->foreign('id_activity')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('id_question')->references('id')->on('question')->onDelete('cascade');
        });

        Schema::table('activity_result', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_activity')->references('id')->on('activities')->onDelete('cascade');
        });

        Schema::table('question', function (Blueprint $table) {
            $table->foreign('id_topic')->references('id')->on('topics')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('activity_packages', function (Blueprint $table) {
            $table->foreign('id_activity')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_class')->references('id')->on('classes')->onDelete('set null');
        });

        Schema::table('activity_answers', function (Blueprint $table) {
            $table->foreign('id_activity')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_question')->references('id')->on('question')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_badge', function (Blueprint $table) {
            $table->dropForeign(['id_student']);
            $table->dropForeign(['id_badge']);
        });

        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropForeign(['id_student']);
            $table->dropForeign(['id_class']);
        });

        Schema::table('teacher_classes', function (Blueprint $table) {
            $table->dropForeign(['id_teacher']);
            $table->dropForeign(['id_class']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('subject', function (Blueprint $table) {
            $table->dropForeign(['id_class']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['id_subject']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['id_topic']);
        });

        Schema::table('activity_topics', function (Blueprint $table) {
            $table->dropForeign(['id_activity']);
            $table->dropForeign(['id_topic']);
        });

        Schema::table('activity_question', function (Blueprint $table) {
            $table->dropForeign(['id_activity']);
            $table->dropForeign(['id_question']);
        });

        Schema::table('activity_result', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_activity']);
        });

        Schema::table('question', function (Blueprint $table) {
            $table->dropForeign(['id_topic']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('activity_packages', function (Blueprint $table) {
            $table->dropForeign(['id_activity']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['id_class']);
        });

        Schema::table('activity_answers', function (Blueprint $table) {
            $table->dropForeign(['id_activity']);
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_question']);
        });
    }
};