<?php

namespace App\Services;

use App\Models\ActivityAnswer;
use App\Models\ActivityResult;
use Illuminate\Support\Collection;

class LearningAnalyticsService
{
    // GET FILTERED RESULTS — Mengambil hasil aktivitas berdasarkan filter LA.
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

        // Filter kelas.
        if ($classId !== null) {

            $query->whereHas(
                'aktivitas.topic.subject',
                function ($q) use ($classId) {

                    $q->where(
                        'id_class',
                        $classId
                    );

                }
            );
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {

            $query->whereHas(
                'aktivitas.topic',
                function ($q) use ($subjectId) {

                    $q->where(
                        'id_subject',
                        $subjectId
                    );

                }
            );
        }

        // Filter topik.
        if ($topicId !== null) {

            $query->whereHas(
                'aktivitas',
                function ($q) use ($topicId) {

                    $q->where(
                        'id_topic',
                        $topicId
                    );

                }
            );
        }

        // Filter aktivitas.
        if ($activityId !== null) {

            $query->where(
                'id_activity',
                $activityId
            );
        }

        // Filter siswa.
        if ($studentId !== null) {

            $query->where(
                'id_user',
                $studentId
            );
        }

        return $query
            ->orderBy('id_user')
            ->orderBy('id_activity')
            ->get();
    }


    // GET FILTERED ANSWERS — Mengambil jawaban sebagai sumber utama LA.
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

            $query->whereHas(
                'activity.topic.subject',
                function ($q) use ($classId) {

                    $q->where(
                        'id_class',
                        $classId
                    );

                }
            );
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {

            $query->whereHas(
                'activity.topic',
                function ($q) use ($subjectId) {

                    $q->where(
                        'id_subject',
                        $subjectId
                    );

                }
            );
        }

        // Filter topik.
        if ($topicId !== null) {

            $query->whereHas(
                'activity',
                function ($q) use ($topicId) {

                    $q->where(
                        'id_topic',
                        $topicId
                    );

                }
            );
        }

        // Filter aktivitas.
        if ($activityId !== null) {

            $query->where(
                'id_activity',
                $activityId
            );
        }

        // Filter siswa.
        if ($studentId !== null) {

            $query->where(
                'id_user',
                $studentId
            );
        }

        return $query
            ->orderBy('id_user')
            ->orderBy('id_activity')
            ->get();
    }


    // PERFORMANCE SUMMARY — Ringkasan performa keseluruhan.
    public function getPerformanceSummary(
        Collection $results
    ): array {

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

        // Jumlah hasil aktivitas.
        $totalResults =
            $results->count();

        // Rata-rata nilai akhir.
        $averageScore =
            round(
                $results->avg(
                    fn($result) =>
                    (float) $result->nilai_akhir
                ),
                2
            );

        // Hitung accuracy setiap aktivitas.
        $accuracies =
            $results
                ->map(
                    function ($result) {

                        $totalQuestions =
                            (int) (
                                $result
                                    ->aktivitas
                                    ->jumlah_soal
                                    ?? 0
                            );

                        if (
                            $totalQuestions <= 0
                        ) {

                            return null;
                        }

                        return (
                            (
                                (int)
                                $result->total_benar
                                /
                                $totalQuestions
                            )
                            * 100
                        );
                    }
                )
                ->filter(
                    fn($value) =>
                    $value !== null
                );

        // Rata-rata accuracy.
        $averageAccuracy =
            $accuracies->isNotEmpty()
                ? round(
                    $accuracies->avg(),
                    2
                )
                : 0;

        // Jumlah hasil yang lulus.
        $passCount =
            $results
                ->filter(
                    fn($result) =>
                    $result->result_status === 'Pass'
                )
                ->count();

        // Tingkat ketuntasan.
        $passRate =
            round(
                (
                    $passCount
                    /
                    $totalResults
                )
                * 100,
                2
            );

        // Rata-rata durasi.
        $durations =
            $results
                ->pluck(
                    'waktu_mengerjakan'
                )
                ->filter(
                    fn($value) =>
                    $value !== null
                )
                ->map(
                    fn($value) =>
                    (int) $value
                );

        $averageDuration =
            $durations->isNotEmpty()
                ? round(
                    $durations->avg()
                )
                : 0;

        return [

            'total_students' =>
                $results
                    ->pluck('id_user')
                    ->unique()
                    ->count(),

            'total_results' =>
                $totalResults,

            'average_score' =>
                $averageScore,

            'average_accuracy' =>
                $averageAccuracy,

            'pass_rate' =>
                $passRate,

            'average_duration' =>
                $averageDuration,
        ];
    }


    // STUDENT SUMMARY — Rekap performa setiap siswa.
    public function getStudentSummary(
        Collection $results
    ): Collection {

        return $results
            ->groupBy('id_user')
            ->map(
                function (
                    Collection $studentResults
                ) {

                    $student =
                        $studentResults
                            ->first()
                            ->nilaiSiswa;

                    // Rata-rata nilai.
                    $averageScore =
                        round(
                            $studentResults->avg(
                                fn($result) =>
                                (float)
                                $result->nilai_akhir
                            ),
                            2
                        );

                    // Accuracy setiap aktivitas.
                    $accuracies =
                        $studentResults
                            ->map(
                                function ($result) {

                                    $totalQuestions =
                                        (int) (
                                            $result
                                                ->aktivitas
                                                ->jumlah_soal
                                                ?? 0
                                        );

                                    if (
                                        $totalQuestions <= 0
                                    ) {

                                        return null;
                                    }

                                    return (
                                        (
                                            (int)
                                            $result->total_benar
                                            /
                                            $totalQuestions
                                        )
                                        * 100
                                    );
                                }
                            )
                            ->filter(
                                fn($value) =>
                                $value !== null
                            );

                    // Rata-rata accuracy.
                    $averageAccuracy =
                        $accuracies->isNotEmpty()
                            ? round(
                                $accuracies->avg(),
                                2
                            )
                            : 0;

                    // Rata-rata durasi.
                    $durations =
                        $studentResults
                            ->pluck(
                                'waktu_mengerjakan'
                            )
                            ->filter(
                                fn($value) =>
                                $value !== null
                            )
                            ->map(
                                fn($value) =>
                                (int) $value
                            );

                    $averageDuration =
                        $durations->isNotEmpty()
                            ? round(
                                $durations->avg()
                            )
                            : 0;

                    // Jumlah aktivitas.
                    $totalActivities =
                        $studentResults
                            ->pluck(
                                'id_activity'
                            )
                            ->unique()
                            ->count();

                    // Jumlah aktivitas lulus.
                    $passedActivities =
                        $studentResults
                            ->filter(
                                fn($result) =>
                                $result->result_status
                                === 'Pass'
                            )
                            ->count();

                    // Status keseluruhan.
                    $overallStatus =
                        $averageScore >= 70
                            ? 'Pass'
                            : 'Remedial';

                    return [

                        'student_id' =>
                            $student->id,

                        'student_name' =>
                            $student->name,

                        'average_score' =>
                            $averageScore,

                        'average_accuracy' =>
                            $averageAccuracy,

                        'average_duration' =>
                            $averageDuration,

                        'total_activities' =>
                            $totalActivities,

                        'passed_activities' =>
                            $passedActivities,

                        'overall_status' =>
                            $overallStatus,
                    ];
                }
            )
            ->values();
    }


    // RASCH PROBABILITY — Menghitung probabilitas menjawab benar.
    private function calculateRaschProbability(
        float $theta,
        float $delta
    ): float {

        $exponent =
            $theta - $delta;

        // Membatasi eksponen untuk menjaga stabilitas numerik.
        $exponent =
            max(
                -50,
                min(
                    50,
                    $exponent
                )
            );

        $e =
            exp($exponent);

        return
            $e /
            (1 + $e);
    }


    // GET DELTA — Mengambil delta dari jawaban atau soal.
    private function getAnswerDelta(
        $answer
    ): float {

        if (
            isset($answer->delta) &&
            $answer->delta !== null
        ) {

            return
                (float)
                $answer->delta;
        }

        return
            (float) (
                $answer
                    ->question
                    ->delta
                    ?? 0
            );
    }


    // ESTIMATE THETA — Estimasi kemampuan menggunakan Newton-Raphson Rasch.
    private function estimateTheta(
        Collection $answers
    ): float {

        if (
            $answers->isEmpty()
        ) {

            return 0.0;
        }

        // Nilai awal theta.
        $theta =
            0.0;

        // Maksimum iterasi.
        $maxIterations =
            30;

        // Toleransi konvergensi.
        $tolerance =
            0.0001;

        for (
            $iteration = 0;
            $iteration < $maxIterations;
            $iteration++
        ) {

            $firstDerivative =
                0.0;

            $secondDerivative =
                0.0;

            foreach (
                $answers as $answer
            ) {

                $delta =
                    $this->getAnswerDelta(
                        $answer
                    );

                $isCorrect =
                    (bool)
                    $answer->is_correct;

                $x =
                    $isCorrect
                        ? 1.0
                        : 0.0;

                $probability =
                    $this->calculateRaschProbability(
                        $theta,
                        $delta
                    );

                $firstDerivative +=
                    $x -
                    $probability;

                $secondDerivative -=
                    $probability *
                    (
                        1 -
                        $probability
                    );
            }

            // Hindari pembagian dengan nilai terlalu kecil.
            if (
                abs(
                    $secondDerivative
                ) < 0.0000001
            ) {

                break;
            }

            $change =
                $firstDerivative
                /
                $secondDerivative;

            $newTheta =
                $theta -
                $change;

            // Batas theta sesuai skala yang digunakan LA.
            $newTheta =
                max(
                    -3.0,
                    min(
                        3.0,
                        $newTheta
                    )
                );

            // Periksa konvergensi.
            if (
                abs(
                    $newTheta -
                    $theta
                )
                <
                $tolerance
            ) {

                $theta =
                    $newTheta;

                break;
            }

            $theta =
                $newTheta;
        }

        return
            round(
                $theta,
                4
            );
    }


    // THETA TO MASTERY — Mengubah theta Rasch menjadi mastery 0-100.
    //
    // Rumus:
    //
    // Mastery =
    // 1 / (1 + e^(-theta)) × 100
    //
    // Mastery tidak lagi dihitung dari expected score.
    private function thetaToMastery(
        float $theta
    ): float {

        $exponent =
            max(
                -50,
                min(
                    50,
                    -$theta
                )
            );

        $mastery =
            (
                1 /
                (
                    1 +
                    exp($exponent)
                )
            )
            * 100;

        return
            round(
                $mastery,
                2
            );
    }


    // MASTERY CATEGORY — Menentukan kategori mastery.
    //
    // 0-49   : Belum Menguasai
    // 50-69  : Cukup
    // 70-84  : Menguasai
    // 85-100 : Mahir
    private function getMasteryCategory(
        float $mastery
    ): array {

        if (
            $mastery >= 85
        ) {

            return [
                'key' =>
                    'mahir',

                'label' =>
                    'Mahir',
            ];
        }

        if (
            $mastery >= 70
        ) {

            return [
                'key' =>
                    'menguasai',

                'label' =>
                    'Menguasai',
            ];
        }

        if (
            $mastery >= 50
        ) {

            return [
                'key' =>
                    'cukup',

                'label' =>
                    'Cukup',
            ];
        }

        return [
            'key' =>
                'belum',

            'label' =>
                'Belum Menguasai',
        ];
    }


    // TOPIC MASTERY — Mastery agregat berdasarkan topik.
    public function getTopicMastery(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null
            )
            ->groupBy(
                fn($answer) =>
                $answer
                    ->question
                    ->id_topic
            )
            ->map(
                function (
                    Collection $topicAnswers,
                    $topicId
                ) {

                    $firstAnswer =
                        $topicAnswers
                            ->first();

                    $topic =
                        $firstAnswer
                            ->question
                            ->topic;

                    $subject =
                        $topic
                            ->subject;

                    $totalAnswers =
                        $topicAnswers
                            ->count();

                    $correctAnswers =
                        $topicAnswers
                            ->filter(
                                fn($answer) =>
                                (bool)
                                $answer->is_correct
                            )
                            ->count();

                    $incorrectAnswers =
                        $totalAnswers -
                        $correctAnswers;

                    // Accuracy aktual.
                    $accuracy =
                        $totalAnswers > 0
                            ? (
                                $correctAnswers
                                /
                                $totalAnswers
                            ) * 100
                            : 0;

                    // Theta Rasch.
                    $theta =
                        $this->estimateTheta(
                            $topicAnswers
                        );

                    // Mastery dari theta.
                    $mastery =
                        $this->thetaToMastery(
                            $theta
                        );

                    // Kategori mastery.
                    $category =
                        $this->getMasteryCategory(
                            $mastery
                        );

                    return [

                        'topic_id' =>
                            $topicId,

                        'topic_name' =>
                            $topic->title,

                        'subject_id' =>
                            $subject->id,

                        'subject_name' =>
                            $subject->name,

                        'total_answers' =>
                            $totalAnswers,

                        'correct_answers' =>
                            $correctAnswers,

                        'incorrect_answers' =>
                            $incorrectAnswers,

                        // Performa aktual.
                        'accuracy' =>
                            round(
                                $accuracy,
                                2
                            ),

                        // Kemampuan Rasch.
                        'theta' =>
                            $theta,

                        // Mastery turunan theta.
                        'mastery' =>
                            $mastery,

                        // Kategori mastery.
                        'mastery_category' =>
                            $category['key'],

                        'mastery_category_label' =>
                            $category['label'],
                    ];
                }
            )
            ->sortBy([
                [
                    'subject_name',
                    'asc',
                ],
                [
                    'topic_name',
                    'asc',
                ],
            ])
            ->values();
    }


    // STUDENT TOPIC MASTERY — Mastery setiap siswa pada setiap topik.
    public function getStudentTopicMastery(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                $answer->user !== null
            )
            ->groupBy(
                function ($answer) {

                    return
                        $answer->user->id
                        . '-'
                        .
                        $answer
                            ->question
                            ->id_topic;
                }
            )
            ->map(
                function (
                    Collection $studentTopicAnswers
                ) {

                    $firstAnswer =
                        $studentTopicAnswers
                            ->first();

                    $student =
                        $firstAnswer
                            ->user;

                    $topic =
                        $firstAnswer
                            ->question
                            ->topic;

                    $subject =
                        $topic
                            ->subject;

                    $totalAnswers =
                        $studentTopicAnswers
                            ->count();

                    $correctAnswers =
                        $studentTopicAnswers
                            ->filter(
                                fn($answer) =>
                                (bool)
                                $answer->is_correct
                            )
                            ->count();

                    $incorrectAnswers =
                        $totalAnswers -
                        $correctAnswers;

                    // Accuracy aktual.
                    $accuracy =
                        $totalAnswers > 0
                            ? (
                                $correctAnswers
                                /
                                $totalAnswers
                            ) * 100
                            : 0;

                    // Theta Rasch siswa pada topik.
                    $theta =
                        $this->estimateTheta(
                            $studentTopicAnswers
                        );

                    // Mastery dari theta.
                    $mastery =
                        $this->thetaToMastery(
                            $theta
                        );

                    // Kategori mastery.
                    $category =
                        $this->getMasteryCategory(
                            $mastery
                        );

                    return [

                        'student_id' =>
                            $student->id,

                        'student_name' =>
                            $student->name,

                        'topic_id' =>
                            $topic->id,

                        'topic_name' =>
                            $topic->title,

                        'subject_id' =>
                            $subject->id,

                        'subject_name' =>
                            $subject->name,

                        'total_answers' =>
                            $totalAnswers,

                        'correct_answers' =>
                            $correctAnswers,

                        'incorrect_answers' =>
                            $incorrectAnswers,

                        // Performa aktual.
                        'accuracy' =>
                            round(
                                $accuracy,
                                2
                            ),

                        // Theta Rasch.
                        'theta' =>
                            $theta,

                        // Mastery dari theta.
                        'mastery' =>
                            $mastery,

                        // Kategori mastery.
                        'mastery_category' =>
                            $category['key'],

                        'mastery_category_label' =>
                            $category['label'],
                    ];
                }
            )
            ->sortBy([
                [
                    'student_name',
                    'asc',
                ],
                [
                    'subject_name',
                    'asc',
                ],
                [
                    'topic_name',
                    'asc',
                ],
            ])
            ->values();
    }


    // DIFFICULTY ANALYSIS — Analisis difficulty keseluruhan.
    public function getDifficultyAnalysis(
        Collection $answers
    ): array {

        $difficulties = [
            'mudah',
            'sedang',
            'sulit',
        ];

        $analysis = [];

        foreach (
            $difficulties as $difficulty
        ) {

            $difficultyAnswers =
                $answers->filter(
                    fn($answer) =>
                    $answer->question &&
                    $answer
                        ->question
                        ->difficulty
                    ===
                    $difficulty
                );

            $total =
                $difficultyAnswers
                    ->count();

            $correct =
                $difficultyAnswers
                    ->filter(
                        fn($answer) =>
                        (bool)
                        $answer->is_correct
                    )
                    ->count();

            $accuracy =
                $total > 0
                    ? round(
                        (
                            $correct
                            /
                            $total
                        ) * 100,
                        2
                    )
                    : 0;

            $analysis[$difficulty] = [

                'total_answers' =>
                    $total,

                'correct_answers' =>
                    $correct,

                'incorrect_answers' =>
                    $total -
                    $correct,

                'accuracy' =>
                    $accuracy,
            ];
        }

        return $analysis;
    }


    // STUDENT TOPIC DIFFICULTY — Performa siswa berdasarkan difficulty.
    public function getStudentTopicDifficulty(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                $answer->user !== null
            )
            ->groupBy(
                function ($answer) {

                    return
                        $answer->user->id
                        . '-'
                        .
                        $answer
                            ->question
                            ->id_topic
                        . '-'
                        .
                        $answer
                            ->question
                            ->difficulty;
                }
            )
            ->map(
                function (
                    Collection $difficultyAnswers
                ) {

                    $firstAnswer =
                        $difficultyAnswers
                            ->first();

                    $student =
                        $firstAnswer
                            ->user;

                    $question =
                        $firstAnswer
                            ->question;

                    $topic =
                        $question
                            ->topic;

                    $difficulty =
                        $question
                            ->difficulty;

                    $totalAnswers =
                        $difficultyAnswers
                            ->count();

                    $correctAnswers =
                        $difficultyAnswers
                            ->filter(
                                fn($answer) =>
                                (bool)
                                $answer->is_correct
                            )
                            ->count();

                    $accuracy =
                        $totalAnswers > 0
                            ? (
                                $correctAnswers
                                /
                                $totalAnswers
                            ) * 100
                            : 0;

                    return [

                        'student_id' =>
                            $student->id,

                        'student_name' =>
                            $student->name,

                        'topic_id' =>
                            $topic->id,

                        'topic_name' =>
                            $topic->title,

                        'difficulty' =>
                            $difficulty,

                        'total_answers' =>
                            $totalAnswers,

                        'correct_answers' =>
                            $correctAnswers,

                        'incorrect_answers' =>
                            $totalAnswers -
                            $correctAnswers,

                        'accuracy' =>
                            round(
                                $accuracy,
                                2
                            ),
                    ];
                }
            )
            ->sortBy([
                [
                    'student_name',
                    'asc',
                ],
                [
                    'topic_name',
                    'asc',
                ],
                [
                    'difficulty',
                    'asc',
                ],
            ])
            ->values();
    }


    // STUDENT ACTIVITY PERFORMANCE — Performa siswa pada setiap aktivitas.
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
            ->groupBy(
                function ($answer) {

                    return
                        $answer->user->id
                        . '-'
                        .
                        $answer->activity->id;
                }
            )
            ->map(
                function (
                    Collection $activityAnswers
                ) {

                    $firstAnswer =
                        $activityAnswers
                            ->first();

                    $student =
                        $firstAnswer
                            ->user;

                    $activity =
                        $firstAnswer
                            ->activity;

                    $topic =
                        $activity
                            ->topic;

                    $totalAnswers =
                        $activityAnswers
                            ->count();

                    $correctAnswers =
                        $activityAnswers
                            ->filter(
                                fn($answer) =>
                                (bool)
                                $answer->is_correct
                            )
                            ->count();

                    $incorrectAnswers =
                        $totalAnswers -
                        $correctAnswers;

                    $accuracy =
                        $totalAnswers > 0
                            ? (
                                $correctAnswers
                                /
                                $totalAnswers
                            ) * 100
                            : 0;

                    // Ambil status aktivitas jika tersedia.
                    $activityStatus =
                        $activity->status
                        ??
                        $activity->activity_status
                        ??
                        'basic';

                    return [

                        'student_id' =>
                            $student->id,

                        'student_name' =>
                            $student->name,

                        'activity_id' =>
                            $activity->id,

                        'activity_name' =>
                            $activity->title,

                        'topic_id' =>
                            $topic->id,

                        'topic_name' =>
                            $topic->title,

                        'activity_status' =>
                            $activityStatus,

                        'total_answers' =>
                            $totalAnswers,

                        'correct_answers' =>
                            $correctAnswers,

                        'incorrect_answers' =>
                            $incorrectAnswers,

                        'accuracy' =>
                            round(
                                $accuracy,
                                2
                            ),
                    ];
                }
            )
            ->sortBy([
                [
                    'student_name',
                    'asc',
                ],
                [
                    'topic_name',
                    'asc',
                ],
                [
                    'activity_name',
                    'asc',
                ],
            ])
            ->values();
    }


    // RECOMMENDATIONS — Recommendation personal berdasarkan mastery dan difficulty.
    public function getRecommendations(
        Collection $studentMastery,
        Collection $topicDifficulty
    ): Collection {

        return $studentMastery
            ->map(
                function (
                    array $masteryData
                ) use (
                    $topicDifficulty
                ) {

                    $studentId =
                        $masteryData[
                            'student_id'
                        ];

                    $topicId =
                        $masteryData[
                            'topic_id'
                        ];

                    $mastery =
                        (float)
                        $masteryData[
                            'mastery'
                        ];

                    // Ambil difficulty siswa dan topik yang sama.
                    $difficultyData =
                        $topicDifficulty
                            ->filter(
                                function ($item)
                                use (
                                    $studentId,
                                    $topicId
                                ) {

                                    return
                                        $item[
                                            'student_id'
                                        ]
                                        ==
                                        $studentId

                                        &&

                                        $item[
                                            'topic_id'
                                        ]
                                        ==
                                        $topicId;
                                }
                            );

                    // Akurasi mudah.
                    $easyAccuracy =
                        data_get(
                            $difficultyData
                                ->firstWhere(
                                    'difficulty',
                                    'mudah'
                                ),
                            'accuracy'
                        );

                    // Akurasi sedang.
                    $mediumAccuracy =
                        data_get(
                            $difficultyData
                                ->firstWhere(
                                    'difficulty',
                                    'sedang'
                                ),
                            'accuracy'
                        );

                    // Akurasi sulit.
                    $hardAccuracy =
                        data_get(
                            $difficultyData
                                ->firstWhere(
                                    'difficulty',
                                    'sulit'
                                ),
                            'accuracy'
                        );

                    $recommendation =
                        '';

                    $category =
                        '';


                    // RULE 1 — Belum Menguasai.
                    if (
                        $mastery < 50
                    ) {

                        $category =
                            'penguatan';

                        if (
                            $mediumAccuracy !== null
                            &&
                            $mediumAccuracy < 50
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " masih rendah. "
                                . "Disarankan mempelajari kembali "
                                . "materi dan melakukan latihan "
                                . "dasar pada topik tersebut.";

                        } elseif (
                            $hardAccuracy !== null
                            &&
                            $hardAccuracy < 50
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " masih rendah. "
                                . "Disarankan mempelajari kembali "
                                . "materi dan memperbanyak latihan "
                                . "pada soal tingkat sulit.";

                        } else {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " masih rendah. "
                                . "Disarankan mempelajari kembali "
                                . "materi dan melakukan latihan "
                                . "dasar pada topik tersebut.";
                        }
                    }


                    // RULE 2 — Cukup.
                    elseif (
                        $mastery < 70
                    ) {

                        $category =
                            'perlu_ditingkatkan';

                        if (
                            $mediumAccuracy !== null
                            &&
                            $mediumAccuracy < 60
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " masih perlu ditingkatkan. "
                                . "Disarankan mempelajari kembali "
                                . "materi dan melakukan latihan "
                                . "pada soal tingkat sedang.";

                        } elseif (
                            $hardAccuracy !== null
                            &&
                            $hardAccuracy < 60
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " masih perlu ditingkatkan. "
                                . "Disarankan memperbanyak latihan "
                                . "pada soal tingkat sulit.";

                        } else {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " cukup baik. "
                                . "Disarankan mempertahankan "
                                . "pemahaman dan melakukan latihan "
                                . "secara berkala.";
                        }
                    }


                    // RULE 3 — Menguasai.
                    elseif (
                        $mastery < 85
                    ) {

                        $category =
                            'pengayaan';

                        if (
                            $hardAccuracy !== null
                            &&
                            $hardAccuracy < 60
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " sudah baik. "
                                . "Disarankan melakukan latihan "
                                . "tambahan pada soal tingkat sulit "
                                . "untuk memperkuat penguasaan.";

                        } elseif (
                            $mediumAccuracy !== null
                            &&
                            $mediumAccuracy < 60
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " sudah baik. "
                                . "Disarankan melakukan latihan "
                                . "tambahan pada soal tingkat sedang "
                                . "untuk meningkatkan konsistensi "
                                . "pemahaman.";

                        } else {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " sudah baik. "
                                . "Disarankan mempertahankan penguasaan "
                                . "melalui latihan dan mencoba soal "
                                . "dengan tingkat kesulitan lebih tinggi.";
                        }
                    }


                    // RULE 4 — Mahir.
                    else {

                        $category =
                            'pengayaan_lanjutan';

                        if (
                            $hardAccuracy !== null
                            &&
                            $hardAccuracy < 70
                        ) {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " sangat baik. "
                                . "Untuk memperkuat penguasaan, "
                                . "disarankan melakukan latihan "
                                . "tambahan pada soal tingkat sulit.";

                        } else {

                            $recommendation =
                                "Penguasaan topik "
                                . $masteryData[
                                    'topic_name'
                                ]
                                . " sangat baik. "
                                . "Pertahankan penguasaan melalui "
                                . "latihan dan soal tingkat sulit.";
                        }
                    }

                    return [

                        'student_id' =>
                            $studentId,

                        'topic_id' =>
                            $topicId,

                        'topic_name' =>
                            $masteryData[
                                'topic_name'
                            ],

                        'mastery' =>
                            $mastery,

                        'theta' =>
                            $masteryData[
                                'theta'
                            ],

                        'category' =>
                            $category,

                        'recommendation' =>
                            $recommendation,
                    ];
                }
            )
            ->values();
    }
}