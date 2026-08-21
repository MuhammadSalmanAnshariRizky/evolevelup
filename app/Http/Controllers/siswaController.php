<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\StudentClasses;
use App\Services\LearningAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class siswaController extends Controller
{
    public function dashboardSiswa(Request $request)
    {
        $user = Auth::user();

        // 🔹 Ambil semua badge siswa (jika ingin menampilkan banyak)
        $userBadges = DB::table('user_badge as ub')
            ->join('badge as b', 'ub.id_badge', '=', 'b.id')
            ->where('ub.id_student', $user->id)
            ->select(
                'b.id',
                'b.name',
                'b.description',
                'b.path_icon',
                'ub.id_class' // penting: scope klaim per kelas (nullable)
            )
            ->orderBy('ub.created_at', 'desc')
            ->get();
        $badgesByClass = [];
        foreach ($userBadges as $ub) {
            $key = is_null($ub->id_class) ? 'general' : 'class_' . $ub->id_class;
            if (!isset($badgesByClass[$key]))
                $badgesByClass[$key] = [];
            $badgesByClass[$key][] = $ub;
        }

        // 🔹 Ambil data kelas siswa login
        $kelasList = DB::table('student_classes')
            ->join('classes', 'student_classes.id_class', '=', 'classes.id')
            ->where('student_classes.id_student', $user->id)
            ->select('classes.id', 'classes.name', 'classes.level', 'classes.token')
            ->get();

        // -----------------------------
        // Aktivitas: KUMPULKAN PER KELAS
        // -----------------------------
        $activitiesByClass = collect();

        foreach ($kelasList as $kelas) {
            $raw = DB::table('activities')
                ->join('topics', 'activities.id_topic', '=', 'topics.id')
                ->join('subject', 'topics.id_subject', '=', 'subject.id')
                ->join('classes', 'subject.id_class', '=', 'classes.id')
                ->where('classes.id', $kelas->id)
                ->leftJoin('activity_result', function ($join) use ($user) {
                    $join->on('activities.id', '=', 'activity_result.id_activity')
                        ->where('activity_result.id_user', '=', $user->id);
                })
                ->select(
                    'activities.id as id_activity',
                    'activities.id_topic',
                    'activities.title as aktivitas',
                    'activities.status',
                    'topics.title as topik',
                    'subject.name as mapel',
                    'activities.created_at',
                    DB::raw('COALESCE(activity_result.result, "-") as result'),
                    DB::raw('COALESCE(activity_result.result_status, "Belum Dikerjakan") as result_status')
                )
                ->orderBy('topics.id')
                ->orderBy('activities.created_at', 'asc')
                ->get();

            $grouped = $raw->groupBy('id_topic')->map(function ($group) {
                $data = [
                    'id_topic' => $group->first()->id_topic,
                    'topik' => $group->first()->topik,
                    'mapel' => $group->first()->mapel,
                    'tanggal' => $group->first()->created_at,
                    'basic' => null,
                    'additional' => null,
                    'remedial' => null,
                ];

                foreach ($group as $act) {
                    $status = strtolower($act->status);
                    if ($status === 'basic')
                        $data['basic'] = $act;
                    if ($status === 'additional')
                        $data['additional'] = $act;
                    if ($status === 'remedial')
                        $data['remedial'] = $act;
                }

                return (object) $data;
            });

            $activitiesByClass->push((object) [
                'class_id' => $kelas->id,
                'class_name' => $kelas->name,
                'activities' => $grouped
            ]);
        }

        // -----------------------------
        // Statistik global
        // -----------------------------
        $rawActivitiesAll = DB::table('activities')
            ->join('topics', 'activities.id_topic', '=', 'topics.id')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->join('classes', 'subject.id_class', '=', 'classes.id')
            ->whereIn('classes.id', $kelasList->pluck('id'))
            ->leftJoin('activity_result', function ($join) use ($user) {
                $join->on('activities.id', '=', 'activity_result.id_activity')
                    ->where('activity_result.id_user', '=', $user->id);
            })
            ->select(
                'activities.id as id_activity',
                'activities.id_topic',
                'activities.title as aktivitas',
                'activities.status',
                'topics.title as topik',
                'subject.name as mapel',
                'activities.created_at',
                DB::raw('COALESCE(activity_result.result, "-") as result'),
                DB::raw('COALESCE(activity_result.result_status, "Belum Dikerjakan") as result_status')
            )
            ->get();

        $jumlahAktivitas = $rawActivitiesAll->count();
        $jumlahRemedial = $rawActivitiesAll->where('result_status', 'Remedial')->count();

        // -----------------------------
        // Leaderboard per kelas (LOGIC BENAR)
        // -----------------------------
        $leaderboardsPerClass = [];

        foreach ($kelasList as $kelas) {
            $students = DB::table('student_classes')
                ->where('id_class', $kelas->id)
                ->pluck('id_student');

            if ($students->isEmpty()) {
                $leaderboardsPerClass[] = (object) [
                    'class_id' => $kelas->id,
                    'class_name' => $kelas->name,
                    'students' => []
                ];
                continue;
            }

            $subjectIds = DB::table('subject')
                ->where('id_class', $kelas->id)
                ->pluck('id');

            if ($subjectIds->isEmpty())
                continue;

            $topicIds = DB::table('topics')
                ->whereIn('id_subject', $subjectIds)
                ->pluck('id');

            if ($topicIds->isEmpty())
                continue;

            $activityIds = DB::table('activities')
                ->whereIn('id_topic', $topicIds)
                ->pluck('id');

            if ($activityIds->isEmpty())
                continue;

            $lb = DB::table('activity_result')
                ->join('users', 'activity_result.id_user', '=', 'users.id')
                ->whereIn('activity_result.id_user', $students)
                ->whereIn('activity_result.id_activity', $activityIds)
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('SUM(activity_result.result) as total_score')
                )
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_score')
                ->get();

            $leaderboardsPerClass[] = (object) [
                'class_id' => $kelas->id,
                'class_name' => $kelas->name,
                'students' => $lb->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->name,
                        'total_score' => (float) $row->total_score
                    ];
                })->toArray()
            ];
        }

        $allBadges = DB::table('badge')
            ->select('id', 'name', 'description', 'path_icon')
            ->orderBy('id')
            ->get();

        $claimedBadgeIds = $userBadges->pluck('id')->toArray();

        // ==========================================
        // FILTER LEARNING ANALYTICS
        // ==========================================

        $filterClassId = $request->filled('class_id')
            ? (int) $request->class_id
            : null;

        $filterSubjectId = $request->filled('subject_id')
            ? (int) $request->subject_id
            : null;

        $filterTopicId = $request->filled('topic_id')
            ? (int) $request->topic_id
            : null;

        $filterActivityId = $request->filled('activity_id')
            ? (int) $request->activity_id
            : null;

        // ==========================================
        // DATA FILTER LA SISWA
        // ==========================================

        $studentClassIds = $kelasList
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();


        // ------------------------------------------
        // Validasi kelas
        // ------------------------------------------

        if (
            $filterClassId !== null &&
            !in_array($filterClassId, $studentClassIds)
        ) {
            $filterClassId = null;
        }


        // ------------------------------------------
        // Mata pelajaran
        // ------------------------------------------

        $subjectQuery = DB::table('subject')
            ->whereIn('id_class', $studentClassIds);

        if ($filterClassId !== null) {
            $subjectQuery->where('id_class', $filterClassId);
        }

        $analyticsSubjects = $subjectQuery
            ->select('id', 'name', 'id_class')
            ->orderBy('name')
            ->get();

        $subjectIds = $analyticsSubjects
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();


        // ------------------------------------------
        // Validasi mata pelajaran
        // ------------------------------------------

        if (
            $filterSubjectId !== null &&
            !in_array($filterSubjectId, $subjectIds)
        ) {
            $filterSubjectId = null;
        }


        // ------------------------------------------
        // Topik
        // ------------------------------------------

        $topicQuery = DB::table('topics')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->whereIn('subject.id_class', $studentClassIds);

        if ($filterClassId !== null) {
            $topicQuery->where('subject.id_class', $filterClassId);
        }

        if ($filterSubjectId !== null) {
            $topicQuery->where('topics.id_subject', $filterSubjectId);
        }

        $analyticsTopics = $topicQuery
            ->select(
                'topics.id',
                'topics.title',
                'topics.id_subject'
            )
            ->orderBy('topics.title')
            ->get();

        $topicIds = $analyticsTopics
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();


        // ------------------------------------------
        // Validasi topik
        // ------------------------------------------

        if (
            $filterTopicId !== null &&
            !in_array($filterTopicId, $topicIds)
        ) {
            $filterTopicId = null;
        }


        // ------------------------------------------
        // Aktivitas
        // ------------------------------------------

        $activityQuery = DB::table('activities')
            ->join('topics', 'activities.id_topic', '=', 'topics.id')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->whereIn('subject.id_class', $studentClassIds);

        if ($filterClassId !== null) {
            $activityQuery->where('subject.id_class', $filterClassId);
        }

        if ($filterSubjectId !== null) {
            $activityQuery->where('topics.id_subject', $filterSubjectId);
        }

        if ($filterTopicId !== null) {
            $activityQuery->where('activities.id_topic', $filterTopicId);
        }

        $analyticsActivities = $activityQuery
            ->select(
                'activities.id',
                'activities.title',
                'activities.id_topic'
            )
            ->orderBy('activities.title')
            ->get();

        $activityIds = $analyticsActivities
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();


        // ------------------------------------------
        // Validasi aktivitas
        // ------------------------------------------

        if (
            $filterActivityId !== null &&
            !in_array($filterActivityId, $activityIds)
        ) {
            $filterActivityId = null;
        }

        // ==========================================
        // LEARNING ANALYTICS
        // ==========================================

        $analyticsService = app(LearningAnalyticsService::class);


        // Hasil aktivitas siswa
        $analyticsResults = $analyticsService->getFilteredResults(
            $filterClassId,
            $filterSubjectId,
            $filterTopicId,
            $filterActivityId,
            $user->id
        );


        // Jawaban soal siswa
        $analyticsAnswers = $analyticsService->getFilteredAnswers(
            $filterClassId,
            $filterSubjectId,
            $filterTopicId,
            $filterActivityId,
            $user->id
        );


        // Ringkasan performa
        $performanceSummary = $analyticsService->getPerformanceSummary(
            $analyticsResults
        );


        // ==========================================
        // MASTERY PER TOPIK
        // ==========================================

        $studentMastery = $analyticsService->getTopicMastery(
            $analyticsAnswers
        );


        // ==========================================
        // DIFFICULTY PER TOPIK
        // ==========================================

        $studentDifficulty =
            $analyticsService->getStudentTopicDifficulty(
                $analyticsAnswers
            );


        // ==========================================
        // PERFORMA PER AKTIVITAS
        // ==========================================

        $studentActivityPerformance =
            $analyticsService->getStudentActivityPerformance(
                $analyticsAnswers
            );


        // ==========================================
        // RECOMMENDATION
        // ==========================================

        $recommendations = $analyticsService->getRecommendations(
            $analyticsService->getStudentTopicMastery($analyticsAnswers),
            $studentDifficulty
        );

        // -----------------------------
        // Tambahan: Daftar Nilai (ambil dari activity_result + relasi)
        // -----------------------------
        // Cari semua activity_result milik user yang berkaitan dengan kelas user
        $kelasIds = $kelasList->pluck('id')->toArray();

        $nilaiList = DB::table('activity_result')
            ->join('activities', 'activity_result.id_activity', '=', 'activities.id')
            ->join('topics', 'activities.id_topic', '=', 'topics.id')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->join('classes', 'subject.id_class', '=', 'classes.id')
            ->where('activity_result.id_user', $user->id)
            ->whereIn('classes.id', $kelasIds)
            ->select(
                'activity_result.id as id_result',
                'activity_result.result as result_value',
                'activity_result.nilai_akhir as nilai_akhir',
                'activity_result.created_at as result_created_at',
                'activities.title as aktivitas',
                'topics.title as topik',
                'subject.name as mapel',
                'classes.name as kelas'
            )
            ->orderByDesc('activity_result.created_at')
            ->get();

        // -----------------------------
        // View
        // -----------------------------
        return view('siswa.dashboardsiswa', [
            'user' => $user,
            'userBadges' => $userBadges,
            'badgesByClass' => $badgesByClass,
            'allBadges' => $allBadges,
            'claimedBadgeIds' => $claimedBadgeIds,
            'kelasList' => $kelasList,
            'activitiesByClass' => $activitiesByClass,
            'jumlahAktivitas' => $jumlahAktivitas,
            'jumlahRemedial' => $jumlahRemedial,
            'leaderboardsPerClass' => $leaderboardsPerClass,
            'nilaiList' => $nilaiList,

            // Learning Analytics
            'performanceSummary' => $performanceSummary,
            'studentMastery' => $studentMastery,
            'studentDifficulty' => $studentDifficulty,
            'studentActivityPerformance' => $studentActivityPerformance,
            'recommendations' => $recommendations,

            // Filter LA
            'analyticsClasses' => $kelasList,
            'analyticsSubjects' => $analyticsSubjects,
            'analyticsTopics' => $analyticsTopics,
            'analyticsActivities' => $analyticsActivities,

            // Filter aktif
            'filterClassId' => $filterClassId,
            'filterSubjectId' => $filterSubjectId,
            'filterTopicId' => $filterTopicId,
            'filterActivityId' => $filterActivityId,
        ]);
    }

    public function gabungKelasSiswa(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);


        $token = trim($request->token);

        $kelas = Classes::whereRaw('LOWER(token) = ?', [strtolower($token)])->first();

        if (!$kelas) {
            return redirect()
                ->back()
                ->with('swal_error', 'Kode kelas tidak ditemukan. Periksa kembali token kelas.');
        }
        $sudahGabung = StudentClasses::where('id_student', Auth::id())
            ->where('id_class', $kelas->id)
            ->exists();

        if ($sudahGabung) {
            return redirect()
                ->back()
                ->with('swal_warning', 'Anda sudah tergabung di kelas ini.');
        }


        StudentClasses::create([
            'id_student' => Auth::id(),
            'id_class' => $kelas->id,
        ]);


        return redirect()
            ->back()
            ->with('swal_success', 'Berhasil bergabung ke kelas: ' . $kelas->name);
    }
}
