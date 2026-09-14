<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityResult;
use App\Models\ActivityAnswer;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class aktivitasController extends Controller
{
    public function aktivitasSiswa()
    {
        $user = Auth::user();

        $badge = DB::table('user_badge')
            ->join('badge', 'user_badge.id_badge', '=', 'badge.id')
            ->where('user_badge.id_student', $user->id)
            ->select('badge.name', 'badge.description')
            ->first();

        $kelasList = DB::table('student_classes')
            ->join('classes', 'student_classes.id_class', '=', 'classes.id')
            ->where('student_classes.id_student', $user->id)
            ->select('classes.id', 'classes.name', 'classes.level', 'classes.token')
            ->get();

        $classIds = $kelasList->pluck('id')->toArray();

        $rawActivitiesQuery = DB::table('activities')
            ->leftJoin('topics as single_topic', 'activities.id_topic', '=', 'single_topic.id')
            ->leftJoin('activity_topics', 'activities.id', '=', 'activity_topics.id_activity')
            ->leftJoin('topics as multi_topic', 'activity_topics.id_topic', '=', 'multi_topic.id')
            ->leftJoin('subject as sub1', 'single_topic.id_subject', '=', 'sub1.id')
            ->leftJoin('subject as sub2', 'multi_topic.id_subject', '=', 'sub2.id')
            ->leftJoin('classes as cls1', 'sub1.id_class', '=', 'cls1.id')
            ->leftJoin('classes as cls2', 'sub2.id_class', '=', 'cls2.id')
            ->leftJoin('activity_result', function ($join) use ($user) {
                $join->on('activities.id', '=', 'activity_result.id_activity')
                    ->where('activity_result.id_user', '=', $user->id);
            })
            ->where(function ($query) use ($classIds) {
                $query->whereIn('cls1.id', $classIds)
                    ->orWhereIn('cls2.id', $classIds);
            })
            ->select(
                'activities.id as id_activity',
                'activities.id_topic',
                'activities.title as aktivitas',
                'activities.status',
                'activities.type',
                'activities.created_at',
                'activities.deadline',
                DB::raw('COALESCE(cls1.id, cls2.id) as id_class'),
                DB::raw('COALESCE(cls1.name, cls2.name) as nama_kelas'),
                DB::raw('COALESCE(cls1.level, cls2.level) as level_kelas'),
                DB::raw('COALESCE(sub1.name, sub2.name) as mapel'),
                DB::raw('single_topic.title as single_topik'),
                DB::raw('COALESCE(activity_result.nilai_akhir, "-") as result'),
                DB::raw('COALESCE(activity_result.result_status, "Belum Dikerjakan") as result_status')
            )
            ->get();

        $rawActivities = $rawActivitiesQuery->groupBy('id_activity')->map(function ($group) {
            $item = $group->first();

            if ($item->type === 'evaluation' || empty($item->id_topic)) {
                $topicTitles = DB::table('activity_topics')
                    ->join('topics', 'activity_topics.id_topic', '=', 'topics.id')
                    ->where('activity_topics.id_activity', $item->id_activity)
                    ->pluck('topics.title')
                    ->toArray();

                $item->topik = !empty($topicTitles) ? implode(', ', $topicTitles) : 'Evaluasi Multi-Topik';
            } else {
                $item->topik = $item->single_topik ?? '-';
            }

            $item->nilai_akhir = ($item->result !== '-') ? $item->result : null;

            return $item;
        })->values();

        $belumDikerjakan = $rawActivities
            ->where('result_status', 'Belum Dikerjakan')
            ->sortBy(function ($item) {
                return $item->deadline ?? $item->created_at;
            })
            ->values();

        $activitiesByClass = $rawActivities
            ->groupBy('id_class')
            ->map(function ($group) {
                $sortedList = $group->sortBy(function ($item) {
                    $status = $item->result_status;

                    if ($status === 'Belum Dikerjakan') {
                        $order = 0;
                    } elseif ($status === 'Remedial') {
                        $order = 1;
                    } elseif ($status === 'Pass') {
                        $order = 2;
                    } else {
                        $order = 3;
                    }

                    $tanggal = $item->deadline ?? $item->created_at;

                    return $order . '|' . $tanggal;
                })->values();

                return (object) [
                    'id_class' => $group->first()->id_class,
                    'nama_kelas' => $group->first()->nama_kelas,
                    'level_kelas' => $group->first()->level_kelas,
                    'list' => $sortedList,
                ];
            })
            ->sortBy(function ($kelas) {
                return $kelas->level_kelas . '|' . $kelas->nama_kelas;
            })
            ->values();

        $jumlahAktivitas = $rawActivities->count();
        $jumlahRemedial = $rawActivities->where('result_status', 'Remedial')->count();

        return view('siswa.aktivitas', [
            'user' => $user,
            'badge' => $badge,
            'kelasList' => $kelasList,
            'belumDikerjakan' => $belumDikerjakan,
            'activitiesByClass' => $activitiesByClass,
            'jumlahAktivitas' => $jumlahAktivitas,
            'jumlahRemedial' => $jumlahRemedial
        ]);
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        if (!empty($activity->id_topic)) {
            $info = DB::table('topics')
                ->join('subject', 'topics.id_subject', '=', 'subject.id')
                ->join('classes', 'subject.id_class', '=', 'classes.id')
                ->where('topics.id', $activity->id_topic)
                ->select('topics.title as topik', 'subject.name as mapel', 'classes.name as kelas')
                ->first();

            $kelas = $info->kelas ?? '-';
            $mapel = $info->mapel ?? '-';
            $topik = $info->topik ?? '-';
        } else {
            $topicsData = DB::table('activity_topics')
                ->join('topics', 'activity_topics.id_topic', '=', 'topics.id')
                ->join('subject', 'topics.id_subject', '=', 'subject.id')
                ->join('classes', 'subject.id_class', '=', 'classes.id')
                ->where('activity_topics.id_activity', $activity->id)
                ->select('topics.title as topik', 'subject.name as mapel', 'classes.name as kelas')
                ->get();

            $kelas = $topicsData->pluck('kelas')->first() ?? '-';
            $mapel = $topicsData->pluck('mapel')->unique()->implode(', ') ?: '-';
            $topik = $topicsData->pluck('topik')->unique()->implode(', ') ?: 'Evaluasi Multi-Topik';
        }

        return view('siswa.menjawabSoal', [
            'judul' => $activity->title,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'topik' => $topik,
            'id_activity' => $activity->id,
            'addaptive' => $activity->addaptive,
            'durasi' => $activity->durasi_pengerjaan,
            'jumlah_soal' => $activity->jumlah_soal,
        ]);
    }

    public function start(Request $req, $id)
    {
        try {
            session()->forget("activity.$id");

            $activity = Activity::findOrFail($id);
            $totalDB = $activity->questions()->count();

            if ($totalDB === 0) {
                return response()->json([
                    'totalQuestions' => 0,
                    'message' => 'Soal belum tersedia di aktivitas ini.'
                ], 422);
            }

            $adaptive = ($activity->addaptive === 'yes');
            $settingJumlahSoal = $activity->jumlah_soal ? (int) $activity->jumlah_soal : $totalDB;
            $jumlahSoal = min($totalDB, $settingJumlahSoal);

            $minSoal = min(5, (int) floor($jumlahSoal / 2));
            if ($jumlahSoal >= 10) {
                $minSoal = 10;
            }

            session([
                "activity.$id.theta" => 0.0,
                "activity.$id.se" => 1.0,
                "activity.$id.current_index" => 0,
                "activity.$id.used_questions" => [],
                "activity.$id.history" => [],
                "activity.$id.total_correct" => 0,
                "activity.$id.max_questions" => $jumlahSoal,
                "activity.$id.min_questions" => $minSoal,
            ]);

            $startTime = Carbon::now();
            session(["activity.$id.start_time" => $startTime->toDateTimeString()]);

            $userId = auth()->id();

            ActivityResult::updateOrCreate(
                ['id_activity' => $id, 'id_user' => $userId],
                [
                    'start_time' => $startTime,
                    'waktu_mengerjakan' => null,
                    'end_time' => null,
                    'total_benar' => 0,
                    'nilai_akhir' => null,
                    'skor_logit' => 0.0,
                    'result' => null,
                    'result_status' => null
                ]
            );

            return response()->json([
                'mode' => $adaptive ? 'adaptive' : 'normal',
                'theta_initial' => 0.0,
                'totalQuestions' => $jumlahSoal,
                'target_se' => 0.35, // Updated target SE ke 0.35
                'started_at' => $startTime->toDateTimeString(),
                'durasi_pengerjaan' => $activity->durasi_pengerjaan ? (int) $activity->durasi_pengerjaan : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memulai ujian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuestion(Request $req, $id)
    {
        $activity = Activity::findOrFail($id);
        $adaptive = ($activity->addaptive === 'yes');
        $used = session("activity.$id.used_questions", []);

        if ($adaptive) {
            $theta = session("activity.$id.theta", 0.0);

            $question = $activity->questions()
                ->whereNotIn('question.id', $used)
                ->orderByRaw('ABS(delta - ?)', [$theta])
                ->first();

            if (!$question) {
                $question = $activity->questions()
                    ->whereNotIn('question.id', $used)
                    ->inRandomOrder()
                    ->first();
            }
        } else {
            $index = $req->query('index', 0);
            $question = $activity->questions()
                ->orderBy('question.id')
                ->skip($index)
                ->first();
        }

        if (!$question) {
            return response()->json([
                'end' => true,
                'message' => 'Ujian selesai. Tidak ada soal tersisa.'
            ]);
        }

        $deltaVal = (float) ($question->delta ?? 0.0);
        if ($deltaVal < -0.5) {
            $difficulty = 'Mudah';
        } elseif ($deltaVal <= 0.5) {
            $difficulty = 'Sedang';
        } else {
            $difficulty = 'Sulit';
        }

        $parsedQuestion = is_string($question->question) ? json_decode($question->question) : $question->question;
        $parsedOptions = is_string($question->MC_option) ? json_decode($question->MC_option) : $question->MC_option;

        return response()->json([
            'question_id' => $question->id,
            'type' => $question->type,
            'delta' => $deltaVal,
            'difficulty' => $difficulty,
            'question' => $parsedQuestion,
            'options' => $parsedOptions,
            'hint' => $question->hint ?? null,
        ]);
    }

    public function submitAnswer(Request $req, $id)
    {
        $question = Question::findOrFail($req->question_id);
        $activity = Activity::findOrFail($id);
        $adaptive = ($activity->addaptive === 'yes');

        $correct = false;
        if ($question->type === 'MultipleChoice') {
            $correct = strtolower(trim($req->user_answer)) === strtolower(trim($question->MC_answer));
        } else if ($question->type === 'ShortAnswer') {
            $answersRaw = $question->SA_answer;
            $answers = is_string($answersRaw) ? json_decode($answersRaw, true) : $answersRaw;
            if (!is_array($answers)) {
                $answers = [];
            }

            $userAns = strtolower(trim($req->user_answer));
            $cleanAnswers = array_map(function ($item) {
                return strtolower(trim($item));
            }, $answers);

            $correct = in_array($userAns, $cleanAnswers);
        }

        ActivityAnswer::updateOrCreate(
            [
                'id_activity' => $id,
                'id_user' => auth()->id(),
                'id_question' => $question->id,
            ],
            [
                'user_answer' => $req->user_answer,
                'is_correct' => $correct,
                'delta' => $question->delta ?? 0.0,
            ]
        );

        $totalCorrect = session("activity.$id.total_correct", 0);
        if ($correct) {
            session(["activity.$id.total_correct" => $totalCorrect + 1]);
        }

        $used = session("activity.$id.used_questions", []);
        if (!in_array($question->id, $used)) {
            $used[] = $question->id;
        }
        session(["activity.$id.used_questions" => $used]);

        $history = session("activity.$id.history", []);
        $history[] = [
            'id' => $question->id,
            'delta' => (float) ($question->delta ?? 0.0),
            'is_correct' => $correct ? 1 : 0
        ];
        session(["activity.$id.history" => $history]);

        $shouldStop = false;
        $targetSe = 0.35; // Updated target SE ke 0.35

        if ($adaptive) {
            $thetaLama = session("activity.$id.theta", 0.0);

            $sumNumerator = 0.0;
            $sumDenominator = 0.0;

            foreach ($history as $h) {
                $b = $h['delta'];
                $u = $h['is_correct'];

                $expVal = exp(-max(-20, min(20, $thetaLama - $b)));
                $p = 1.0 / (1.0 + $expVal);
                $info = $p * (1.0 - $p);

                $sumNumerator += ($u - $p);
                $sumDenominator += $info;
            }

            if ($sumDenominator < 0.0001) {
                $sumDenominator = 0.0001;
            }

            $deltaTheta = ($sumNumerator / $sumDenominator) * 0.5;
            $thetaBaru = $thetaLama + $deltaTheta;
            $thetaBaru = max(-2.0, min(2.0, $thetaBaru));

            $sumInfoNew = 0.0;
            foreach ($history as $h) {
                $b = $h['delta'];
                $expValNew = exp(-max(-20, min(20, $thetaBaru - $b)));
                $pNew = 1.0 / (1.0 + $expValNew);
                $itemInfo = $pNew * (1.0 - $pNew);
                $sumInfoNew += $itemInfo;
            }

            if ($sumInfoNew < 0.0001) {
                $sumInfoNew = 0.0001;
            }

            $seBaru = 1.0 / sqrt($sumInfoNew);

            session([
                "activity.$id.theta" => $thetaBaru,
                "activity.$id.se" => $seBaru
            ]);

            $minSoal = session("activity.$id.min_questions", 10);
            $maxSoal = session("activity.$id.max_questions", 40);
            $numSoalDikerjakan = count($history);

            if ($numSoalDikerjakan >= $maxSoal) {
                $shouldStop = true;
            } elseif ($numSoalDikerjakan >= $minSoal && $seBaru <= $targetSe) {
                $shouldStop = true;
            }

            $totalDB = $activity->questions()->count();
            if ($numSoalDikerjakan >= $totalDB) {
                $shouldStop = true;
            }
        }

        $saOptions = [];
        if ($question->type === 'ShortAnswer') {
            $saOptions = is_array($question->SA_answer) ? $question->SA_answer : json_decode($question->SA_answer, true);
        }

        return response()->json([
            'correct' => $correct,
            'correct_answer' => $question->type === 'MultipleChoice' ? strtoupper($question->MC_answer) : implode(', ', $saOptions ?? []),
            'explanation' => $question->explanation ?? null,
            'should_stop' => $shouldStop,
            'current_theta' => session("activity.$id.theta", 0.0),
            'current_se' => session("activity.$id.se", 1.0),
            'target_se' => $targetSe
        ]);
    }

    public function finishTest(Request $req, $id)
    {
        $userId = auth()->id();
        $activity = Activity::findOrFail($id);

        $thetaAkhir = session("activity.$id.theta", 0.0);
        $seAkhir = session("activity.$id.se", 1.0);
        $totalCorrect = session("activity.$id.total_correct", 0);
        $history = session("activity.$id.history", []);

        if (empty($history)) {
            $dbAnswers = ActivityAnswer::where('id_activity', $id)
                ->where('id_user', $userId)
                ->get();

            if ($dbAnswers->count() > 0) {
                $totalCorrect = $dbAnswers->where('is_correct', true)->count();
                $history = $dbAnswers->map(function ($ans) {
                    return [
                        'id' => $ans->id_question,
                        'delta' => (float) $ans->delta,
                        'is_correct' => $ans->is_correct ? 1 : 0
                    ];
                })->toArray();
            }
        }

        $jumlahSoalDikerjakan = max(1, count($history));
        $totalSalah = max(0, $jumlahSoalDikerjakan - $totalCorrect);

        $activityResult = ActivityResult::where('id_activity', $id)
            ->where('id_user', $userId)
            ->first();

        $start = ($activityResult && $activityResult->start_time)
            ? Carbon::parse($activityResult->start_time)
            : Carbon::parse(session("activity.$id.start_time", Carbon::now()));

        $end = Carbon::now();
        $durationSeconds = max(0, $end->getTimestamp() - $start->getTimestamp());

        $expectedScore = 0;
        $allDeltas = $activity->questions()->pluck('delta');
        $totalBankSoal = $allDeltas->count();

        if ($totalBankSoal > 0) {
            foreach ($allDeltas as $delta) {
                $deltaVal = (float) ($delta ?? 0.0);
                $eksponensial = exp($thetaAkhir - $deltaVal);
                $probabilitas = $eksponensial / (1 + $eksponensial);
                $expectedScore += $probabilitas;
            }
        }

        $nilaiExpectedScore = $totalBankSoal > 0 ? round(($expectedScore / $totalBankSoal) * 100, 2) : 0;
        $nilaiAkhir = round(($totalCorrect / $jumlahSoalDikerjakan) * 100, 2);

        $kkm = $activity->kkm ?? 70;
        $status = $nilaiAkhir >= $kkm ? 'Pass' : 'Remedial';

        ActivityResult::updateOrCreate(
            [
                'id_activity' => $id,
                'id_user' => $userId,
            ],
            [
                'skor_logit' => $thetaAkhir,
                'result' => $nilaiExpectedScore,
                'bonus_poin' => 0,
                'real_poin' => $totalCorrect,
                'result_status' => $status,
                'waktu_mengerjakan' => $durationSeconds,
                'total_benar' => $totalCorrect,
                'start_time' => $start,
                'end_time' => $end,
                'status_benar' => ($totalCorrect === $jumlahSoalDikerjakan),
                'nilai_akhir' => $nilaiAkhir,
            ]
        );

        $updatedResult = ActivityResult::where('id_activity', $id)
            ->where('id_user', $userId)
            ->first();

        session()->forget("activity.$id");

        return response()->json([
            'status' => 'saved',
            'duration_seconds' => $durationSeconds,
            'total_correct' => $totalCorrect,
            'total_incorrect' => $totalSalah,
            'jumlah_soal' => $jumlahSoalDikerjakan,
            'result_db' => [
                'theta_akhir' => $thetaAkhir,
                'expected_score' => $nilaiExpectedScore,
                'nilai_akhir' => $updatedResult->nilai_akhir,
                'result_status' => $updatedResult->result_status,
                'total_benar' => $updatedResult->total_benar,
                'start_time' => optional($updatedResult->start_time)->toDateTimeString(),
                'end_time' => optional($updatedResult->end_time)->toDateTimeString(),
            ],
            'debug_info' => [
                'mode_adaptif' => ($activity->addaptive === 'yes'),
                'total_dikerjakan' => $jumlahSoalDikerjakan,
                'benar' => $totalCorrect,
                'salah' => $totalSalah,
                'rumus' => "({$totalCorrect} Benar / {$jumlahSoalDikerjakan} Soal Dikerjakan) * 100",
                'nilai_hitung' => $nilaiAkhir,
                'theta_awal' => 0.0,
                'theta_akhir' => round($thetaAkhir, 4),
                'se_akhir' => round($seAkhir, 4),
                'target_se' => 0.35, // Updated target SE ke 0.35
                'history_detail' => $history
            ]
        ]);
    }
}