<?php

namespace App\Http\Controllers;

use App\Services\LearningAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsController extends Controller
{
    // INDEX — Menampilkan dashboard Learning Analytics guru.
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        $analyticsService =
            app(LearningAnalyticsService::class);

        // KELAS YANG DIAJAR GURU.

        $teacherClassIds = DB::table('teacher_classes')
            ->where('id_teacher', $teacherId)
            ->pluck('id_class')
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();

        $analyticsClasses = DB::table('classes')
            ->whereIn(
                'id',
                $teacherClassIds
            )
            ->select(
                'id',
                'name'
            )
            ->orderBy('name')
            ->get();

        // FILTER AKTIF.

        $filterClassId =
            $request->filled('class_id')
                ? (int) $request->class_id
                : null;

        $filterSubjectId =
            $request->filled('subject_id')
                ? (int) $request->subject_id
                : null;

        $filterTopicId =
            $request->filled('topic_id')
                ? (int) $request->topic_id
                : null;

        $filterActivityId =
            $request->filled('activity_id')
                ? (int) $request->activity_id
                : null;

        $studentSearch = trim(
            (string) $request->get(
                'student',
                ''
            )
        );

        // VALIDASI KELAS.

        if (
            $filterClassId !== null &&
            !in_array(
                $filterClassId,
                $teacherClassIds
            )
        ) {
            $filterClassId = null;
        }

        // DATA MATA PELAJARAN.

        $subjectQuery = DB::table('subject')
            ->whereIn(
                'id_class',
                $teacherClassIds
            );

        if ($filterClassId !== null) {

            $subjectQuery->where(
                'id_class',
                $filterClassId
            );
        }

        $analyticsSubjects =
            $subjectQuery
                ->select(
                    'id',
                    'name',
                    'id_class'
                )
                ->orderBy('name')
                ->get();

        $subjectIds = $analyticsSubjects
            ->pluck('id')
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();

        if (
            $filterSubjectId !== null &&
            !in_array(
                $filterSubjectId,
                $subjectIds
            )
        ) {
            $filterSubjectId = null;
        }

        // DATA TOPIK.

        $topicQuery = DB::table('topics')
            ->join(
                'subject',
                'topics.id_subject',
                '=',
                'subject.id'
            )
            ->whereIn(
                'subject.id_class',
                $teacherClassIds
            );

        if ($filterClassId !== null) {

            $topicQuery->where(
                'subject.id_class',
                $filterClassId
            );
        }

        if ($filterSubjectId !== null) {

            $topicQuery->where(
                'topics.id_subject',
                $filterSubjectId
            );
        }

        $analyticsTopics =
            $topicQuery
                ->select(
                    'topics.id',
                    'topics.title',
                    'topics.id_subject'
                )
                ->orderBy('topics.title')
                ->get();

        $topicIds = $analyticsTopics
            ->pluck('id')
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();

        if (
            $filterTopicId !== null &&
            !in_array(
                $filterTopicId,
                $topicIds
            )
        ) {
            $filterTopicId = null;
        }

        // DATA AKTIVITAS.

        $activityQuery = DB::table('activities')
            ->join(
                'topics',
                'activities.id_topic',
                '=',
                'topics.id'
            )
            ->join(
                'subject',
                'topics.id_subject',
                '=',
                'subject.id'
            )
            ->whereIn(
                'subject.id_class',
                $teacherClassIds
            );

        if ($filterClassId !== null) {

            $activityQuery->where(
                'subject.id_class',
                $filterClassId
            );
        }

        if ($filterSubjectId !== null) {

            $activityQuery->where(
                'topics.id_subject',
                $filterSubjectId
            );
        }

        if ($filterTopicId !== null) {

            $activityQuery->where(
                'activities.id_topic',
                $filterTopicId
            );
        }

        $analyticsActivities =
            $activityQuery
                ->select(
                    'activities.id',
                    'activities.title',
                    'activities.id_topic'
                )
                ->orderBy('activities.title')
                ->get();

        $activityIds = $analyticsActivities
            ->pluck('id')
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();

        if (
            $filterActivityId !== null &&
            !in_array(
                $filterActivityId,
                $activityIds
            )
        ) {
            $filterActivityId = null;
        }

        // DAFTAR SISWA GURU.

        $studentQuery = DB::table('student_classes')
            ->join(
                'users',
                'student_classes.id_student',
                '=',
                'users.id'
            )
            ->whereIn(
                'student_classes.id_class',
                $teacherClassIds
            )
            ->where(
                'users.role',
                'student'
            );

        if ($filterClassId !== null) {

            $studentQuery->where(
                'student_classes.id_class',
                $filterClassId
            );
        }

        if ($studentSearch !== '') {

            $studentQuery->where(
                'users.name',
                'like',
                '%' . $studentSearch . '%'
            );
        }

        $analyticsStudents =
            $studentQuery
                ->select(
                    'users.id',
                    'users.name'
                )
                ->distinct()
                ->orderBy('users.name')
                ->get();

        $studentIds = $analyticsStudents
            ->pluck('id')
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();

        // AMBIL DATA LEARNING ANALYTICS.

        $results = collect();
        $answers = collect();

        foreach ($teacherClassIds as $classId) {

            // Jika kelas dipilih,
            // hanya proses kelas tersebut.

            if (
                $filterClassId !== null &&
                $classId !== $filterClassId
            ) {
                continue;
            }

            $classResults =
                $analyticsService->getFilteredResults(
                    $classId,
                    $filterSubjectId,
                    $filterTopicId,
                    $filterActivityId
                );

            $classAnswers =
                $analyticsService->getFilteredAnswers(
                    $classId,
                    $filterSubjectId,
                    $filterTopicId,
                    $filterActivityId
                );

            $results =
                $results->merge(
                    $classResults
                );

            $answers =
                $answers->merge(
                    $classAnswers
                );
        }

        // FILTER SISWA.

        if ($studentSearch !== '') {

            $results = $results
                ->filter(
                    fn($result) =>
                    in_array(
                        (int) $result->id_user,
                        $studentIds
                    )
                )
                ->values();

            $answers = $answers
                ->filter(
                    fn($answer) =>
                    in_array(
                        (int) $answer->id_user,
                        $studentIds
                    )
                )
                ->values();
        }

        // RINGKASAN KESELURUHAN.

        $performanceSummary =
            $analyticsService
                ->getPerformanceSummary(
                    $results
                );

        // REKAP PER SISWA.

        $studentSummary =
            $analyticsService
                ->getStudentSummary(
                    $results
                );

        // MASTERY AGREGAT PER TOPIK.

        $topicMastery =
            $analyticsService
                ->getTopicMastery(
                    $answers
                );

        // DIFFICULTY AGREGAT.

        $topicDifficulty =
            $analyticsService
                ->getDifficultyAnalysis(
                    $answers
                );

        // DATA MASTERY PER SISWA.

        $studentTopicMastery =
            $analyticsService
                ->getStudentTopicMastery(
                    $answers
                );

        // DATA DIFFICULTY PER SISWA.

        $studentTopicDifficulty =
            $analyticsService
                ->getStudentTopicDifficulty(
                    $answers
                );

        // DATA PERFORMA PER AKTIVITAS SISWA.

        $studentActivityPerformance =
            $analyticsService
                ->getStudentActivityPerformance(
                    $answers
                );

        // REKOMENDASI PER SISWA.

        $recommendations =
            $analyticsService
                ->getRecommendations(
                    $studentTopicMastery,
                    $studentTopicDifficulty
                );

        // RENDER VIEW.

        return view(
            'guru.learning-analytics',
            [

                // DATA FILTER.

                'analyticsClasses' =>
                    $analyticsClasses,

                'analyticsSubjects' =>
                    $analyticsSubjects,

                'analyticsTopics' =>
                    $analyticsTopics,

                'analyticsActivities' =>
                    $analyticsActivities,

                'analyticsStudents' =>
                    $analyticsStudents,

                'filterClassId' =>
                    $filterClassId,

                'filterSubjectId' =>
                    $filterSubjectId,

                'filterTopicId' =>
                    $filterTopicId,

                'filterActivityId' =>
                    $filterActivityId,

                'studentSearch' =>
                    $studentSearch,

                // DATA LEARNING ANALYTICS.

                'performanceSummary' =>
                    $performanceSummary,

                'studentSummary' =>
                    $studentSummary,

                'topicMastery' =>
                    $topicMastery,

                'topicDifficulty' =>
                    $topicDifficulty,

                'studentTopicMastery' =>
                    $studentTopicMastery,

                'studentTopicDifficulty' =>
                    $studentTopicDifficulty,

                'studentActivityPerformance' =>
                    $studentActivityPerformance,

                'recommendations' =>
                    $recommendations,
            ]
        );
    }
}