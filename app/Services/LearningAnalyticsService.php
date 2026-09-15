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
                'aktivitas.topics.subject',
            ]);

        // Filter kelas.
        if ($classId !== null) {
            $query->whereHas('aktivitas', function ($q) use ($classId) {
                $q->whereHas('topic.subject', function ($sq) use ($classId) {
                    $sq->where('id_class', $classId);
                })->orWhereHas('topics.subject', function ($sq) use ($classId) {
                    $sq->where('id_class', $classId);
                });
            });
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {
            $query->whereHas('aktivitas', function ($q) use ($subjectId) {
                $q->whereHas('topic', function ($tq) use ($subjectId) {
                    $tq->where('id_subject', $subjectId);
                })->orWhereHas('topics', function ($tq) use ($subjectId) {
                    $tq->where('id_subject', $subjectId);
                });
            });
        }

        // Filter topik.
        if ($topicId !== null) {
            $query->whereHas('aktivitas', function ($q) use ($topicId) {
                $q->where('id_topic', $topicId)
                  ->orWhereHas('topics', function ($tq) use ($topicId) {
                      $tq->where('topics.id', $topicId);
                  });
            });
        }

        // Filter aktivitas.
        if ($activityId !== null) {
            $query->where('id_activity', $activityId);
        }

        // Filter siswa.
        if ($studentId !== null) {
            $query->where('id_user', $studentId);
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
                'activity.topics.subject',
                'question.topic',
            ]);

        // Filter kelas.
        if ($classId !== null) {
            $query->whereHas('activity', function ($q) use ($classId) {
                $q->whereHas('topic.subject', function ($sq) use ($classId) {
                    $sq->where('id_class', $classId);
                })->orWhereHas('topics.subject', function ($sq) use ($classId) {
                    $sq->where('id_class', $classId);
                });
            });
        }

        // Filter mata pelajaran.
        if ($subjectId !== null) {
            $query->whereHas('activity', function ($q) use ($subjectId) {
                $q->whereHas('topic', function ($tq) use ($subjectId) {
                    $tq->where('id_subject', $subjectId);
                })->orWhereHas('topics', function ($tq) use ($subjectId) {
                    $tq->where('id_subject', $subjectId);
                });
            });
        }

        // Filter topik.
        if ($topicId !== null) {
            $query->whereHas('activity', function ($q) use ($topicId) {
                $q->where('id_topic', $topicId)
                  ->orWhereHas('topics', function ($tq) use ($topicId) {
                      $tq->where('topics.id', $topicId);
                  });
            });
        }

        // Filter aktivitas.
        if ($activityId !== null) {
            $query->where('id_activity', $activityId);
        }

        // Filter siswa.
        if ($studentId !== null) {
            $query->where('id_user', $studentId);
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

        $totalResults = $results->count();

        $averageScore = round(
            $results->avg(fn($result) => (float) $result->nilai_akhir),
            2
        );

        $accuracies = $results
            ->map(function ($result) {
                $totalQuestions = (int) ($result->aktivitas->jumlah_soal ?? 0);
                if ($totalQuestions <= 0) {
                    return null;
                }
                return (((int) $result->total_benar / $totalQuestions) * 100);
            })
            ->filter(fn($value) => $value !== null);

        $averageAccuracy = $accuracies->isNotEmpty()
            ? round($accuracies->avg(), 2)
            : 0;

        $passCount = $results
            ->filter(fn($result) => $result->result_status === 'Pass')
            ->count();

        $passRate = round(($passCount / $totalResults) * 100, 2);

        $durations = $results
            ->pluck('waktu_mengerjakan')
            ->filter(fn($value) => $value !== null)
            ->map(fn($value) => (int) $value);

        $averageDuration = $durations->isNotEmpty()
            ? round($durations->avg())
            : 0;

        return [
            'total_students'   => $results->pluck('id_user')->unique()->count(),
            'total_results'    => $totalResults,
            'average_score'    => $averageScore,
            'average_accuracy' => $averageAccuracy,
            'pass_rate'        => $passRate,
            'average_duration' => $averageDuration,
        ];
    }


    // STUDENT SUMMARY — Rekap performa setiap siswa.
    public function getStudentSummary(
        Collection $results
    ): Collection {

        return $results
            ->groupBy('id_user')
            ->map(function (Collection $studentResults) {
                $student = $studentResults->first()->nilaiSiswa;

                $averageScore = round(
                    $studentResults->avg(fn($result) => (float) $result->nilai_akhir),
                    2
                );

                $accuracies = $studentResults
                    ->map(function ($result) {
                        $totalQuestions = (int) ($result->aktivitas->jumlah_soal ?? 0);
                        if ($totalQuestions <= 0) {
                            return null;
                        }
                        return (((int) $result->total_benar / $totalQuestions) * 100);
                    })
                    ->filter(fn($value) => $value !== null);

                $averageAccuracy = $accuracies->isNotEmpty()
                    ? round($accuracies->avg(), 2)
                    : 0;

                $durations = $studentResults
                    ->pluck('waktu_mengerjakan')
                    ->filter(fn($value) => $value !== null)
                    ->map(fn($value) => (int) $value);

                $averageDuration = $durations->isNotEmpty()
                    ? round($durations->avg())
                    : 0;

                $totalActivities = $studentResults
                    ->pluck('id_activity')
                    ->unique()
                    ->count();

                $passedActivities = $studentResults
                    ->filter(fn($result) => $result->result_status === 'Pass')
                    ->count();

                $overallStatus = $averageScore >= 70 ? 'Pass' : 'Remedial';

                return [
                    'student_id'        => $student?->id,
                    'student_name'      => $student?->name,
                    'average_score'     => $averageScore,
                    'average_accuracy'  => $averageAccuracy,
                    'average_duration'  => $averageDuration,
                    'total_activities'  => $totalActivities,
                    'passed_activities' => $passedActivities,
                    'overall_status'    => $overallStatus,
                ];
            })
            ->values();
    }


    // RASCH PROBABILITY — Menghitung probabilitas menjawab benar.
    private function calculateRaschProbability(
        float $theta,
        float $delta
    ): float {
        $exponent = max(-50, min(50, $theta - $delta));
        $e = exp($exponent);
        return $e / (1 + $e);
    }


    // GET DELTA — Mengambil delta dari jawaban atau soal.
    private function getAnswerDelta($answer): float
    {
        if (isset($answer->delta) && $answer->delta !== null) {
            return (float) $answer->delta;
        }

        return (float) ($answer->question->delta ?? 0);
    }


    // ESTIMATE THETA — Estimasi kemampuan menggunakan Newton-Raphson Rasch.
    private function estimateTheta(
        Collection $answers
    ): float {

        if ($answers->isEmpty()) {
            return 0.0;
        }

        $theta = 0.0;
        $maxIterations = 30;
        $tolerance = 0.0001;

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $firstDerivative = 0.0;
            $secondDerivative = 0.0;

            foreach ($answers as $answer) {
                $delta = $this->getAnswerDelta($answer);
                $isCorrect = (bool) $answer->is_correct;
                $x = $isCorrect ? 1.0 : 0.0;

                $probability = $this->calculateRaschProbability($theta, $delta);

                $firstDerivative += $x - $probability;
                $secondDerivative -= $probability * (1 - $probability);
            }

            if (abs($secondDerivative) < 0.0000001) {
                break;
            }

            $change = $firstDerivative / $secondDerivative;
            $newTheta = max(-3.0, min(3.0, $theta - $change));

            if (abs($newTheta - $theta) < $tolerance) {
                $theta = $newTheta;
                break;
            }

            $theta = $newTheta;
        }

        return round($theta, 4);
    }


    // THETA TO MASTERY — Mengubah theta Rasch menjadi mastery 0-100.
    private function thetaToMastery(float $theta): float
    {
        $exponent = max(-50, min(50, -$theta));
        $mastery = (1 / (1 + exp($exponent))) * 100;

        return round($mastery, 2);
    }


    // MASTERY CATEGORY — Menentukan kategori mastery.
    private function getMasteryCategory(float $mastery): array
    {
        if ($mastery >= 85) {
            return ['key' => 'mahir', 'label' => 'Mahir'];
        }
        if ($mastery >= 70) {
            return ['key' => 'menguasai', 'label' => 'Menguasai'];
        }
        if ($mastery >= 50) {
            return ['key' => 'cukup', 'label' => 'Cukup'];
        }

        return ['key' => 'belum', 'label' => 'Belum Menguasai'];
    }


    // TOPIC MASTERY — Mastery agregat berdasarkan topik.
    public function getTopicMastery(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(fn($answer) => $answer->question !== null)
            ->groupBy(fn($answer) => $answer->question->id_topic)
            ->map(function (Collection $topicAnswers, $topicId) {
                $firstAnswer = $topicAnswers->first();
                $topic = $firstAnswer->question->topic;
                $subject = $topic?->subject;

                $totalAnswers = $topicAnswers->count();
                $correctAnswers = $topicAnswers->filter(fn($a) => (bool) $a->is_correct)->count();
                $incorrectAnswers = $totalAnswers - $correctAnswers;

                $accuracy = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;
                $theta = $this->estimateTheta($topicAnswers);
                $mastery = $this->thetaToMastery($theta);
                $category = $this->getMasteryCategory($mastery);

                return [
                    'topic_id'               => $topicId,
                    'topic_name'             => $topic?->title ?? 'Tanpa Topik',
                    'subject_id'             => $subject?->id,
                    'subject_name'           => $subject?->name ?? 'Tanpa Mapel',
                    'total_answers'          => $totalAnswers,
                    'correct_answers'        => $correctAnswers,
                    'incorrect_answers'      => $incorrectAnswers,
                    'accuracy'               => round($accuracy, 2),
                    'theta'                  => $theta,
                    'mastery'                => $mastery,
                    'mastery_category'       => $category['key'],
                    'mastery_category_label' => $category['label'],
                ];
            })
            ->sortBy([
                ['subject_name', 'asc'],
                ['topic_name', 'asc'],
            ])
            ->values();
    }


    // STUDENT TOPIC MASTERY — Mastery setiap siswa pada setiap topik.
    public function getStudentTopicMastery(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(fn($answer) => $answer->question !== null && $answer->user !== null)
            ->groupBy(fn($answer) => $answer->user->id . '-' . $answer->question->id_topic)
            ->map(function (Collection $studentTopicAnswers) {
                $firstAnswer = $studentTopicAnswers->first();
                $student = $firstAnswer->user;
                $topic = $firstAnswer->question->topic;
                $subject = $topic?->subject;

                $totalAnswers = $studentTopicAnswers->count();
                $correctAnswers = $studentTopicAnswers->filter(fn($a) => (bool) $a->is_correct)->count();
                $incorrectAnswers = $totalAnswers - $correctAnswers;

                $accuracy = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;
                $theta = $this->estimateTheta($studentTopicAnswers);
                $mastery = $this->thetaToMastery($theta);
                $category = $this->getMasteryCategory($mastery);

                return [
                    'student_id'             => $student->id,
                    'student_name'           => $student->name,
                    'topic_id'               => $topic?->id,
                    'topic_name'             => $topic?->title ?? 'Tanpa Topik',
                    'subject_id'             => $subject?->id,
                    'subject_name'           => $subject?->name ?? 'Tanpa Mapel',
                    'total_answers'          => $totalAnswers,
                    'correct_answers'        => $correctAnswers,
                    'incorrect_answers'      => $incorrectAnswers,
                    'accuracy'               => round($accuracy, 2),
                    'theta'                  => $theta,
                    'mastery'                => $mastery,
                    'mastery_category'       => $category['key'],
                    'mastery_category_label' => $category['label'],
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['subject_name', 'asc'],
                ['topic_name', 'asc'],
            ])
            ->values();
    }


    // DIFFICULTY ANALYSIS — Analisis difficulty keseluruhan.
    public function getDifficultyAnalysis(
        Collection $answers
    ): array {

        $difficulties = ['sangat mudah','mudah', 'sedang', 'sulit', 'sangat sulit'];
        $analysis = [];

        foreach ($difficulties as $difficulty) {
            $difficultyAnswers = $answers->filter(
                fn($answer) => $answer->question && $answer->question->difficulty === $difficulty
            );

            $total = $difficultyAnswers->count();
            $correct = $difficultyAnswers->filter(fn($a) => (bool) $a->is_correct)->count();
            $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

            $analysis[$difficulty] = [
                'total_answers'     => $total,
                'correct_answers'   => $correct,
                'incorrect_answers' => $total - $correct,
                'accuracy'          => $accuracy,
            ];
        }

        return $analysis;
    }


    // STUDENT TOPIC DIFFICULTY — Performa siswa berdasarkan difficulty.
    public function getStudentTopicDifficulty(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(fn($answer) => $answer->question !== null && $answer->user !== null)
            ->groupBy(fn($answer) => $answer->user->id . '-' . $answer->question->id_topic . '-' . $answer->question->difficulty)
            ->map(function (Collection $difficultyAnswers) {
                $firstAnswer = $difficultyAnswers->first();
                $student = $firstAnswer->user;
                $question = $firstAnswer->question;
                $topic = $question->topic;
                $difficulty = $question->difficulty;

                $totalAnswers = $difficultyAnswers->count();
                $correctAnswers = $difficultyAnswers->filter(fn($a) => (bool) $a->is_correct)->count();
                $accuracy = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

                return [
                    'student_id'        => $student->id,
                    'student_name'      => $student->name,
                    'topic_id'          => $topic?->id,
                    'topic_name'        => $topic?->title ?? 'Tanpa Topik',
                    'difficulty'        => $difficulty,
                    'total_answers'     => $totalAnswers,
                    'correct_answers'   => $correctAnswers,
                    'incorrect_answers' => $totalAnswers - $correctAnswers,
                    'accuracy'          => round($accuracy, 2),
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
                ['difficulty', 'asc'],
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
            ->groupBy(fn($answer) => $answer->user->id . '-' . $answer->activity->id)
            ->map(function (Collection $activityAnswers) {
                $firstAnswer = $activityAnswers->first();
                $student     = $firstAnswer->user;
                $activity    = $firstAnswer->activity;

                // Ambil topik dari:
                // 1. Kolom id_topic langsung ($activity->topic)
                // 2. Tabel pivot activity_topics ($activity->topics)
                // 3. Fallback dari question->topic
                $topics = collect();
                if ($activity->id_topic && $activity->topic) {
                    $topics->push($activity->topic);
                } elseif ($activity->topics && $activity->topics->isNotEmpty()) {
                    $topics = $activity->topics;
                } elseif ($firstAnswer->question?->topic) {
                    $topics->push($firstAnswer->question->topic);
                }

                $topicIds  = $topics->pluck('id')->filter()->unique()->values()->all();
                $topicName = $topics->pluck('title')->filter()->unique()->implode(', ') ?: 'Evaluasi (Multi Topik)';

                $totalAnswers     = $activityAnswers->count();
                $correctAnswers   = $activityAnswers->filter(fn($a) => (bool) $a->is_correct)->count();
                $incorrectAnswers = $totalAnswers - $correctAnswers;
                $accuracy         = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

                $activityStatus   = $activity->status ?? $activity->activity_status ?? 'basic';

                return [
                    'student_id'        => $student->id,
                    'student_name'      => $student->name,
                    'activity_id'       => $activity->id,
                    'activity_name'     => $activity->title,
                    'topic_id'          => count($topicIds) === 1 ? $topicIds[0] : $topicIds,
                    'topic_name'        => $topicName,
                    'activity_status'   => $activityStatus,
                    'total_answers'     => $totalAnswers,
                    'correct_answers'   => $correctAnswers,
                    'incorrect_answers' => $incorrectAnswers,
                    'accuracy'          => round($accuracy, 2),
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
                ['activity_name', 'asc'],
            ])
            ->values();
    }


    // TAGS
    public function getQuestionTags(
        Collection $answers
    ): Collection {

        return $answers
            ->filter(
                fn($answer) =>
                $answer->question !== null &&
                    $answer->user !== null &&
                    !empty($this->resolveTags($answer->question))
            )
            ->flatMap(function ($answer) {
                $tags = $this->resolveTags($answer->question);

                return collect($tags)->map(function ($tag) use ($answer) {
                    return [
                        'answer' => $answer,
                        'tag'    => $tag,
                    ];
                });
            })
            ->groupBy(function ($item) {
                $answer = $item['answer'];
                $tag    = $item['tag'];

                return $answer->user->id
                    . '-' . $answer->question->id_topic
                    . '-' . $tag['id'];
            })
            ->map(function (Collection $tagAnswerItems) {
                $firstItem   = $tagAnswerItems->first();
                $firstAnswer = $firstItem['answer'];
                $student     = $firstAnswer->user;
                $question    = $firstAnswer->question;
                $topic       = $question->topic;
                $tag         = $firstItem['tag'];

                $totalAnswers     = $tagAnswerItems->count();
                $correctAnswers   = $tagAnswerItems->filter(fn($item) => (bool) $item['answer']->is_correct)->count();
                $incorrectAnswers = $totalAnswers - $correctAnswers;
                $accuracy         = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

                return [
                    'student_id'        => $student->id,
                    'student_name'      => $student->name,
                    'topic_id'          => $topic?->id,
                    'topic_name'        => $topic?->title ?? 'Tanpa Topik',
                    'tag_id'            => $tag['id'],
                    'tag_name'          => $tag['name'],
                    'total_answers'     => $totalAnswers,
                    'correct_answers'   => $correctAnswers,
                    'incorrect_answers' => $incorrectAnswers,
                    'accuracy'          => round($accuracy, 2),
                ];
            })
            ->sortBy([
                ['student_name', 'asc'],
                ['topic_name', 'asc'],
                ['accuracy', 'asc'],
            ])
            ->values();
    }


    // RESOLVE TAGS
    private function resolveTags($question): array
    {
        $tags = trim((string) data_get($question, 'tags', ''));

        if ($tags === '') {
            return [];
        }

        return collect(explode(',', $tags))
            ->map(fn($tag) => trim($tag))
            ->filter()
            ->unique()
            ->map(function ($tag) {
                return [
                    'id'   => strtolower($tag),
                    'name' => $tag,
                ];
            })
            ->values()
            ->all();
    }


    // RECOMMENDATIONS
    public function getRecommendations(
        Collection $studentMastery,
        Collection $tagsPerformance
    ): Collection {

        return $studentMastery
            ->map(function (array $masteryData) use ($tagsPerformance) {
                $studentId = $masteryData['student_id'];
                $topicId   = $masteryData['topic_id'];
                $topicName = $masteryData['topic_name'];
                $mastery   = (float) $masteryData['mastery'];
                $accuracy  = (float) $masteryData['accuracy'];

                $topicTags = $tagsPerformance
                    ->filter(function ($item) use ($studentId, $topicId) {
                        return (int) $item['student_id'] === (int) $studentId
                            && (int) $item['topic_id'] === (int) $topicId;
                    })
                    ->values();

                $weakTags = $topicTags
                    ->filter(fn($item) => (float) $item['accuracy'] < 70)
                    ->sortBy('accuracy')
                    ->take(3)
                    ->values();

                if ($mastery < 50) {
                    $recommendationType = 'penguatan';
                } elseif ($mastery < 70) {
                    $recommendationType = 'latihan';
                } elseif ($mastery < 85) {
                    $recommendationType = 'lanjutan';
                } else {
                    $recommendationType = 'pengayaan';
                }

                return [
                    'student_id'             => $studentId,
                    'topic_id'               => $topicId,
                    'topic_name'             => $topicName,
                    'accuracy'               => $accuracy,
                    'mastery'                => $mastery,
                    'theta'                  => $masteryData['theta'],
                    'mastery_category'       => $masteryData['mastery_category'],
                    'mastery_category_label' => $masteryData['mastery_category_label'],
                    'recommendation_type'    => $recommendationType,
                    'weak_tags'              => $weakTags->values()->all(),
                ];
            })
            ->values();
    }
}