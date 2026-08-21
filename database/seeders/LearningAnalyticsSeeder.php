<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityAnswer;
use App\Models\ActivityResult;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsSeeder extends Seeder
{
    // Seed data untuk pengujian Learning Analytics.
    public function run(): void
    {
        DB::transaction(function () {

            // Ambil siswa berdasarkan data DatabaseSeeder.
            $wahyu = User::where('email', 'Wahyu@example.com')->first();
            $norman = User::where('email', 'norman@example.com')->first();

            // Pastikan siswa tersedia.
            if (!$wahyu || !$norman) {
                $this->command->error(
                    'Wahyu atau Norman tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.'
                );

                return;
            }

            // Ambil seluruh aktivitas Informatika.
            $activities = Activity::whereHas(
                'topic.subject',
                fn ($query) => $query->where('name', 'Informatika')
            )
                ->orderBy('id')
                ->get();

            // Pastikan aktivitas tersedia.
            if ($activities->isEmpty()) {
                $this->command->error(
                    'Aktivitas Informatika tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.'
                );

                return;
            }

            // Ambil seluruh soal dari topik Informatika.
            $questions = Question::whereHas(
                'topic.subject',
                fn ($query) => $query->where('name', 'Informatika')
            )
                ->orderBy('id')
                ->get();

            // Pastikan soal tersedia.
            if ($questions->isEmpty()) {
                $this->command->error(
                    'Soal Informatika tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.'
                );

                return;
            }

            // Hapus data Learning Analytics lama
            // agar seeder dapat dijalankan ulang.
            ActivityAnswer::whereIn(
                'id_user',
                [$wahyu->id, $norman->id]
            )->delete();

            ActivityResult::whereIn(
                'id_user',
                [$wahyu->id, $norman->id]
            )->delete();

            // Pola performa Wahyu.
            $wahyuPattern = [
                'mudah' => 0.95,
                'sedang' => 0.80,
                'sulit' => 0.60,
            ];

            // Pola performa Norman.
            $normanPattern = [
                'mudah' => 0.75,
                'sedang' => 0.45,
                'sulit' => 0.20,
            ];

            // Seed jawaban Wahyu.
            foreach ($activities as $activity) {
                $this->createStudentAnswers(
                    $wahyu,
                    $activity,
                    $questions,
                    $wahyuPattern
                );
            }

            // Seed jawaban Norman.
            foreach ($activities as $activity) {
                $this->createStudentAnswers(
                    $norman,
                    $activity,
                    $questions,
                    $normanPattern
                );
            }

            // Tampilkan ringkasan hasil seeder.
            $this->command->info(
                'Learning Analytics test data berhasil dibuat.'
            );

            $this->command->info(
                'Wahyu: performa tinggi.'
            );

            $this->command->info(
                'Norman: performa rendah.'
            );
        });
    }

    /**
     * Membuat jawaban siswa untuk satu aktivitas.
     */
    private function createStudentAnswers(
        User $student,
        Activity $activity,
        $questions,
        array $accuracyPattern
    ): void {

        // Ambil soal yang digunakan oleh aktivitas.
        //
        // Relasi questions() sudah mengembalikan
        // collection berisi model Question.
        $activityQuestions = $activity->questions()->get();

        // Jika aktivitas belum memiliki soal,
        // gunakan seluruh soal dari topik aktivitas tersebut.
        if ($activityQuestions->isEmpty()) {
            $activityQuestions = $questions->filter(
                fn ($question) =>
                    $question->id_topic === $activity->id_topic
            );
        }

        $correctCount = 0;
        $totalCount = 0;

        foreach ($activityQuestions as $question) {

            // Tentukan tingkat kesulitan soal.
            $difficulty = strtolower(
                $question->difficulty ?? 'sedang'
            );

            // Ambil peluang benar berdasarkan
            // tingkat kesulitan siswa.
            $accuracy = $accuracyPattern[$difficulty] ?? 0.50;

            // Tentukan apakah jawaban benar atau salah.
            $isCorrect = mt_rand(1, 100) <= ($accuracy * 100);

            // Buat jawaban siswa.
            $userAnswer = $this->generateAnswer(
                $question,
                $isCorrect
            );

            // Simpan jawaban siswa.
            ActivityAnswer::create([
                'id_activity' => $activity->id,
                'id_user' => $student->id,
                'id_question' => $question->id,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'delta' => $question->delta,
            ]);

            $totalCount++;

            if ($isCorrect) {
                $correctCount++;
            }
        }

        // Hitung nilai aktivitas.
        $score = $totalCount > 0
            ? round(($correctCount / $totalCount) * 100, 2)
            : 0;

        // Tentukan status aktivitas.
        $status = $score >= 70
            ? 'Pass'
            : 'Remedial';

        // Simpan hasil aktivitas.
        ActivityResult::create([
            'id_user' => $student->id,
            'id_activity' => $activity->id,
            'nilai_akhir' => $score,
            'result_status' => $status,
            'result' => $score,
            'real_poin' => $score >= 70 ? 20 : 10,
            'bonus_poin' => 0,
        ]);
    }

    /**
     * Membuat jawaban benar atau salah
     * berdasarkan tipe soal.
     */
    private function generateAnswer(
        Question $question,
        bool $isCorrect
    ): string {

        // Jika jawaban salah.
        if (!$isCorrect) {
            return 'jawaban salah';
        }

        // Multiple Choice.
        if ($question->type === 'MultipleChoice') {
            return $question->MC_answer ?? 'a';
        }

        // Short Answer.
        if ($question->type === 'ShortAnswer') {

            $answers = json_decode(
                $question->SA_answer ?? '[]',
                true
            );

            if (is_array($answers) && !empty($answers)) {
                return $answers[0];
            }
        }

        // Fallback.
        return 'jawaban benar';
    }
}