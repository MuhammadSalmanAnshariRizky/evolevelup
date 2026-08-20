<?php

namespace App\Services;

use App\Models\ActivityAnswer;
use App\Models\ActivityResult;
use Illuminate\Support\Collection;

class LearningAnalyticsService
{
    // GET FILTERED RESULTS — Mengambil hasil aktivitas berdasarkan filter LA.
    //
    // Filter:
    // - classId
    // - subjectId
    // - topicId
    // - activityId
    public function getFilteredResults(
        ?int $classId = null,
        ?int $subjectId = null,
        ?int $topicId = null,
        ?int $activityId = null,
        ?int $studentId = null
    ): Collection {
        $query = ActivityResult::query()
            ->with([
                'nilaiSiswa',
                'aktivitas.topic.subject',
            ]);

        // Filter berdasarkan kelas.
        //
        // ActivityResult tidak memiliki id_class langsung.
        // Relasinya:
        //
        // ActivityResult
        // -> Activity
        // -> Topic
        // -> Subject
        // -> id_class
        if ($classId !== null) {
            $query->whereHas('aktivitas.topic.subject', function ($q) use ($classId) {
                $q->where('id_class', $classId);
            });
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {
            $query->whereHas('aktivitas.topic', function ($q) use ($subjectId) {
                $q->where('id_subject', $subjectId);
            });
        }

        // Filter topik.
        if ($topicId !== null) {
            $query->whereHas('aktivitas', function ($q) use ($topicId) {
                $q->where('id_topic', $topicId);
            });
        }

        // Filter aktivitas.
        if ($activityId !== null) {
            $query->where('id_activity', $activityId);
        }

        // Filter siswa tertentu.
        if ($studentId !== null) {
            $query->where('id_user', $studentId);
        }

        return $query
            ->orderBy('id_user')
            ->orderBy('id_activity')
            ->get();
    }

    // GET FILTERED ANSWERS — Mengambil jawaban siswa berdasarkan filter LA.
    //
    // Data ini menjadi sumber utama:
    // - accuracy
    // - difficulty analysis
    // - mastery
    // - item analysis
    public function getFilteredAnswers(
        ?int $classId = null,
        ?int $subjectId = null,
        ?int $topicId = null,
        ?int $activityId = null,
        ?int $studentId = null
    ): Collection {
        $query = ActivityAnswer::query()
            ->with([
                'user',
                'activity.topic.subject',
                'question.topic',
            ]);

        // Filter kelas.
        if ($classId !== null) {
            $query->whereHas('activity.topic.subject', function ($q) use ($classId) {
                $q->where('id_class', $classId);
            });
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {
            $query->whereHas('activity.topic', function ($q) use ($subjectId) {
                $q->where('id_subject', $subjectId);
            });
        }

        // Filter topik.
        if ($topicId !== null) {
            $query->whereHas('activity', function ($q) use ($topicId) {
                $q->where('id_topic', $topicId);
            });
        }

        // Filter aktivitas.
        if ($activityId !== null) {
            $query->where('id_activity', $activityId);
        }

        // Filter siswa tertentu.
        if ($studentId !== null) {
            $query->where('id_user', $studentId);
        }

        return $query
            ->orderBy('id_user')
            ->orderBy('id_activity')
            ->get();
    }

    // PERFORMANCE SUMMARY — Ringkasan performa keseluruhan.
    public function getPerformanceSummary(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'total_students' => 0,
                'total_results' => 0,
                'average_score' => 0,
                'average_accuracy' => 0,
                'pass_rate' => 0,
                'average_duration' => 0,
            ];
        }

        $totalResults = $results->count();

        // Nilai akhir.
        $averageScore = round(
            $results->avg(fn($result) => (float) $result->nilai_akhir),
            2
        );

        // Accuracy dihitung dari total_benar / jumlah soal pada activity.

        // Kita menggunakan Activity.jumlah_soal sebagai denominator hasil aktivitas.
        $accuracies = $results->map(function ($result) {
            $totalQuestions = (int) ($result->aktivitas->jumlah_soal ?? 0);

            if ($totalQuestions <= 0) {
                return null;
            }

            return (
                ((int) $result->total_benar / $totalQuestions)
                * 100
            );
        })->filter(fn($value) => $value !== null);

        $averageAccuracy = $accuracies->isNotEmpty()
            ? round($accuracies->avg(), 2)
            : 0;

        // Persentase siswa/hasil yang lulus.
        $passCount = $results
            ->filter(fn($result) => $result->result_status === 'Pass')
            ->count();

        $passRate = round(
            ($passCount / $totalResults) * 100,
            2
        );

        // Durasi tersimpan dalam detik.
        $durations = $results
            ->pluck('waktu_mengerjakan')
            ->filter(fn($value) => $value !== null)
            ->map(fn($value) => (int) $value);

        $averageDuration = $durations->isNotEmpty()
            ? round($durations->avg())
            : 0;

        return [
            'total_students' => $results
                ->pluck('id_user')
                ->unique()
                ->count(),

            'total_results' => $totalResults,

            'average_score' => $averageScore,

            'average_accuracy' => $averageAccuracy,

            'pass_rate' => $passRate,

            'average_duration' => $averageDuration,
        ];
    }

    // STUDENT SUMMARY — Rekap performa setiap siswa.
    public function getStudentSummary(Collection $results): Collection
    {
        return $results
            ->groupBy('id_user')
            ->map(function (Collection $studentResults) {

                $student = $studentResults
                    ->first()
                    ->nilaiSiswa;

                // Rata-rata nilai siswa.
                $averageScore = round(
                    $studentResults->avg(
                        fn($result) => (float) $result->nilai_akhir
                    ),
                    2
                );

                // Accuracy siswa.
                $accuracies = $studentResults
                    ->map(function ($result) {

                        $totalQuestions = (int) (
                            $result->aktivitas->jumlah_soal ?? 0
                        );

                        if ($totalQuestions <= 0) {
                            return null;
                        }

                        return (
                            ((int) $result->total_benar / $totalQuestions)
                            * 100
                        );
                    })
                    ->filter(fn($value) => $value !== null);

                $averageAccuracy = $accuracies->isNotEmpty()
                    ? round($accuracies->avg(), 2)
                    : 0;

                // Rata-rata durasi.
                $durations = $studentResults
                    ->pluck('waktu_mengerjakan')
                    ->filter(fn($value) => $value !== null)
                    ->map(fn($value) => (int) $value);

                $averageDuration = $durations->isNotEmpty()
                    ? round($durations->avg())
                    : 0;

                // Jumlah aktivitas yang dikerjakan.
                $totalActivities = $studentResults
                    ->pluck('id_activity')
                    ->unique()
                    ->count();

                // Jumlah aktivitas yang lulus.
                $passedActivities = $studentResults
                    ->filter(
                        fn($result) =>
                        $result->result_status === 'Pass'
                    )
                    ->count();

                // Status keseluruhan siswa.
                $overallStatus = $averageScore >= 70
                    ? 'Pass'
                    : 'Remedial';

                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,

                    'average_score' => $averageScore,

                    'average_accuracy' => $averageAccuracy,

                    'average_duration' => $averageDuration,

                    'total_activities' => $totalActivities,

                    'passed_activities' => $passedActivities,

                    'overall_status' => $overallStatus,
                ];
            })
            ->values();
    }

    // DIFFICULTY ANALYSIS — Analisis performa berdasarkan tingkat kesulitan soal.
    public function getDifficultyAnalysis(Collection $answers): array
    {
        $difficulties = [
            'mudah',
            'sedang',
            'sulit',
        ];

        $analysis = [];

        foreach ($difficulties as $difficulty) {
            $difficultyAnswers = $answers->filter(
                fn($answer) =>
                $answer->question &&
                    $answer->question->difficulty === $difficulty
            );

            $total = $difficultyAnswers->count();

            $correct = $difficultyAnswers
                ->filter(fn($answer) => (bool) $answer->is_correct)
                ->count();

            $accuracy = $total > 0
                ? round(($correct / $total) * 100, 2)
                : 0;

            $analysis[$difficulty] = [
                'total_answers' => $total,
                'correct_answers' => $correct,
                'incorrect_answers' => $total - $correct,
                'accuracy' => $accuracy,
            ];
        }

        return $analysis;
    }

    // TOPIC MASTERY — Menghitung mastery berdasarkan topik.

    // Mastery sementara dihitung dari proporsi jawaban benar
    // pada seluruh soal yang dikerjakan dalam suatu topik.

    // Output mastery berada pada skala 0–100.
    public function getTopicMastery(Collection $answers): Collection
    {
        return $answers
            ->filter(fn($answer) => $answer->question !== null)
            ->groupBy(function ($answer) {
                return $answer->question->id_topic;
            })
            ->map(function (Collection $topicAnswers, $topicId) {

                $firstAnswer = $topicAnswers->first();

                $topic = $firstAnswer->question->topic;

                $totalAnswers = $topicAnswers->count();

                $correctAnswers = $topicAnswers
                    ->filter(fn($answer) => (bool) $answer->is_correct)
                    ->count();

                $accuracy = $totalAnswers > 0
                    ? $correctAnswers / $totalAnswers
                    : 0;

                // Estimasi theta sederhana.
                //
                // Accuracy 0%   -> theta -1
                // Accuracy 50%  -> theta  0
                // Accuracy 100% -> theta +1
                $theta = (2 * $accuracy) - 1;

                // Transformasi theta ke skala 0–100.
                $mastery = (($theta + 1) / 2) * 100;

                return [
                    'topic_id' => $topicId,
                    'topic_name' => $topic->title,

                    'total_answers' => $totalAnswers,

                    'correct_answers' => $correctAnswers,

                    'incorrect_answers' =>
                    $totalAnswers - $correctAnswers,

                    'accuracy' => round($accuracy * 100, 2),

                    'theta' => round($theta, 4),

                    'mastery' => round($mastery, 2),
                ];
            })
            ->values();
    }

    // STUDENT TOPIC MASTERY — Menghitung mastery setiap siswa pada setiap topik.

    // Struktur:
    // Siswa
    // -> Topik
    // -> jawaban
    // -> accuracy
    // -> theta
    // -> mastery 0-100
    public function getStudentTopicMastery(Collection $answers): Collection
    {
        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                    $answer->user !== null
            )
            ->groupBy(function ($answer) {
                return $answer->user->id
                    . '-' .
                    $answer->question->id_topic;
            })
            ->map(function (Collection $studentTopicAnswers) {

                $firstAnswer = $studentTopicAnswers->first();

                $student = $firstAnswer->user;
                $topic = $firstAnswer->question->topic;

                $totalAnswers = $studentTopicAnswers->count();

                $correctAnswers = $studentTopicAnswers
                    ->filter(fn($answer) => (bool) $answer->is_correct)
                    ->count();

                $incorrectAnswers =
                    $totalAnswers - $correctAnswers;

                $accuracy = $totalAnswers > 0
                    ? $correctAnswers / $totalAnswers
                    : 0;

                // Theta sederhana.
                $theta = (2 * $accuracy) - 1;

                // Transformasi theta ke 0-100.
                $mastery = (($theta + 1) / 2) * 100;

                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,

                    'topic_id' => $topic->id,
                    'topic_name' => $topic->title,

                    'total_answers' => $totalAnswers,

                    'correct_answers' => $correctAnswers,

                    'incorrect_answers' => $incorrectAnswers,

                    'accuracy' => round(
                        $accuracy * 100,
                        2
                    ),

                    'theta' => round(
                        $theta,
                        4
                    ),

                    'mastery' => round(
                        $mastery,
                        2
                    ),
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
            ])
            ->values();
    }

    // Menganalisis performa setiap siswa berdasarkan
    // tingkat kesulitan pada setiap topik.
    public function getStudentTopicDifficulty(Collection $answers): Collection
    {
        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                    $answer->user !== null
            )
            ->groupBy(function ($answer) {
                return $answer->user->id
                    . '-' .
                    $answer->question->id_topic
                    . '-' .
                    $answer->question->difficulty;
            })
            ->map(function (Collection $difficultyAnswers) {

                $firstAnswer = $difficultyAnswers->first();

                $student = $firstAnswer->user;
                $question = $firstAnswer->question;
                $topic = $question->topic;
                $difficulty = $question->difficulty;

                $totalAnswers = $difficultyAnswers->count();

                $correctAnswers = $difficultyAnswers
                    ->filter(fn($answer) => (bool) $answer->is_correct)
                    ->count();

                $accuracy = $totalAnswers > 0
                    ? ($correctAnswers / $totalAnswers) * 100
                    : 0;

                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,

                    'topic_id' => $topic->id,
                    'topic_name' => $topic->title,

                    'difficulty' => $difficulty,

                    'total_answers' => $totalAnswers,
                    'correct_answers' => $correctAnswers,
                    'incorrect_answers' =>
                    $totalAnswers - $correctAnswers,

                    'accuracy' => round($accuracy, 2),
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
                ['difficulty', 'asc'],
            ])
            ->values();
    }

    // Menghasilkan recommendation berbasis aturan
    // untuk setiap siswa pada setiap topik.

    // Recommendation tidak menyebut nama siswa.
    public function getRecommendations(
        Collection $studentMastery,
        Collection $topicDifficulty
    ): Collection {
        return $studentMastery
            ->map(function (array $masteryData) use ($topicDifficulty) {

                $studentId = $masteryData['student_id'];
                $topicId = $masteryData['topic_id'];

                $mastery = (float) $masteryData['mastery'];

                // Ambil analisis difficulty untuk
                // siswa dan topik yang sama.
                $difficultyData = $topicDifficulty
                    ->filter(function ($item) use ($studentId, $topicId) {
                        return
                            $item['student_id'] == $studentId &&
                            $item['topic_id'] == $topicId;
                    });

                // Cari akurasi setiap difficulty.
                $easyAccuracy = $difficultyData
                    ->firstWhere('difficulty', 'mudah')['accuracy'] ?? null;

                $mediumAccuracy = $difficultyData
                    ->firstWhere('difficulty', 'sedang')['accuracy'] ?? null;

                $hardAccuracy = $difficultyData
                    ->firstWhere('difficulty', 'sulit')['accuracy'] ?? null;

                // RECOMMENDATION
                // Recommendation ditentukan berdasarkan:
                // 1. Mastery per topik
                // 2. Akurasi soal tingkat sedang
                // 3. Akurasi soal tingkat sulit

                // Mastery:
                // 0-49   : Penguatan
                // 50-59  : Mulai ditingkatkan
                // 60-69  : Perlu ditingkatkan
                // 70-84  : Pengayaan
                // 85-100 : Pengayaan lanjutan

                $recommendation = '';
                $category = '';


                // RULE 1
                // Mastery 0-49
                // Belum Menguasai
                if ($mastery < 50) {

                    $category = 'penguatan';

                    if (
                        $mediumAccuracy !== null &&
                        $mediumAccuracy < 50
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} masih rendah. "
                            . "Disarankan mempelajari kembali materi dan melakukan latihan "
                            . "dasar pada topik tersebut.";
                    } elseif (
                        $hardAccuracy !== null &&
                        $hardAccuracy < 50
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} masih rendah. "
                            . "Disarankan mempelajari kembali materi dan memperbanyak "
                            . "latihan pada soal tingkat sulit.";
                    } else {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} masih rendah. "
                            . "Disarankan mempelajari kembali materi dan melakukan latihan "
                            . "dasar pada topik tersebut.";
                    }
                }


                // RULE 2
                // Mastery 50-59
                // Mulai Menguasai
                elseif ($mastery < 60) {

                    $category = 'mulai_ditingkatkan';

                    if (
                        $mediumAccuracy !== null &&
                        $mediumAccuracy < 50
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} mulai terbentuk, "
                            . "namun masih perlu diperkuat. Disarankan mempelajari kembali "
                            . "konsep yang belum dikuasai dan berlatih pada soal tingkat sedang.";
                    } elseif (
                        $hardAccuracy !== null &&
                        $hardAccuracy < 50
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} mulai terbentuk. "
                            . "Disarankan memperbanyak latihan pada soal tingkat sulit "
                            . "untuk memperkuat pemahaman.";
                    } else {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} mulai terbentuk. "
                            . "Disarankan memperkuat pemahaman melalui latihan secara bertahap.";
                    }
                }


                // RULE 3
                // Mastery 60-69
                // Cukup Menguasai
                elseif ($mastery < 70) {

                    $category = 'perlu_ditingkatkan';

                    if (
                        $mediumAccuracy !== null &&
                        $mediumAccuracy < 60
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} cukup, "
                            . "namun masih perlu ditingkatkan. Disarankan melakukan "
                            . "latihan tambahan pada soal tingkat sedang.";
                    } elseif (
                        $hardAccuracy !== null &&
                        $hardAccuracy < 60
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} cukup, "
                            . "namun masih perlu ditingkatkan. Disarankan memperbanyak "
                            . "latihan pada soal tingkat sulit.";
                    } else {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} cukup baik. "
                            . "Disarankan mempertahankan pemahaman dan melakukan latihan "
                            . "secara berkala.";
                    }
                }


                // ============================================================
                // RULE 4
                // Mastery 70-84
                // Menguasai
                // ============================================================
                elseif ($mastery < 85) {

                    $category = 'pengayaan';

                    if (
                        $hardAccuracy !== null &&
                        $hardAccuracy < 60
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} sudah baik. "
                            . "Disarankan melakukan latihan tambahan pada soal tingkat "
                            . "sulit untuk memperkuat penguasaan.";
                    } elseif (
                        $mediumAccuracy !== null &&
                        $mediumAccuracy < 60
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} sudah baik. "
                            . "Disarankan melakukan latihan tambahan pada soal tingkat "
                            . "sedang untuk meningkatkan konsistensi pemahaman.";
                    } else {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} sudah baik. "
                            . "Disarankan mempertahankan penguasaan melalui latihan "
                            . "dan mencoba soal dengan tingkat kesulitan lebih tinggi.";
                    }
                }


                // ============================================================
                // RULE 5
                // Mastery 85-100
                // Mahir
                // ============================================================
                else {

                    $category = 'pengayaan_lanjutan';

                    if (
                        $hardAccuracy !== null &&
                        $hardAccuracy < 70
                    ) {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} sangat baik. "
                            . "Untuk memperkuat penguasaan, disarankan melakukan latihan "
                            . "tambahan pada soal tingkat sulit.";
                    } else {

                        $recommendation =
                            "Penguasaan topik {$masteryData['topic_name']} sangat baik. "
                            . "Pertahankan penguasaan melalui latihan dan soal tingkat sulit.";
                    }
                }

                return [
                    'student_id' => $studentId,

                    'topic_id' => $topicId,

                    'topic_name' =>
                    $masteryData['topic_name'],

                    'mastery' => $mastery,

                    'theta' =>
                    $masteryData['theta'],

                    'category' => $category,

                    'recommendation' =>
                    $recommendation,
                ];
            })
            ->values();
    }

    // ============================================================
    // STUDENT ACTIVITY PERFORMANCE
    // ============================================================
    //
    // Menghitung performa setiap siswa pada setiap aktivitas.
    //
    // Output:
    // - student_id
    // - activity_id
    // - activity_name
    // - topic_id
    // - topic_name
    // - total_answers
    // - correct_answers
    // - incorrect_answers
    // - accuracy
    //
    public function getStudentActivityPerformance(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                    $answer->user !== null &&
                    $answer->activity !== null
            )
            ->groupBy(function ($answer) {

                return
                    $answer->user->id
                    . '-'
                    . $answer->activity->id;
            })
            ->map(function (Collection $activityAnswers) {

                $firstAnswer = $activityAnswers->first();

                $student = $firstAnswer->user;
                $activity = $firstAnswer->activity;
                $topic = $activity->topic;

                $totalAnswers = $activityAnswers->count();

                $correctAnswers = $activityAnswers
                    ->filter(
                        fn($answer) =>
                        (bool) $answer->is_correct
                    )
                    ->count();

                $incorrectAnswers =
                    $totalAnswers - $correctAnswers;

                $accuracy = $totalAnswers > 0
                    ? ($correctAnswers / $totalAnswers) * 100
                    : 0;

                return [

                    'student_id' =>
                    $student->id,

                    'student_name' =>
                    $student->name,

                    'activity_id' =>
                    $activity->id,

                    'activity_name' =>
                    $activity->title,

                    'activity_status' =>
                    $activity->status,

                    'topic_id' =>
                    $topic->id,

                    'topic_name' =>
                    $topic->title,

                    'total_answers' =>
                    $totalAnswers,

                    'correct_answers' =>
                    $correctAnswers,

                    'incorrect_answers' =>
                    $incorrectAnswers,

                    'accuracy' =>
                    round($accuracy, 2),

                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
                ['activity_name', 'asc'],
            ])
            ->values();
    }
}
