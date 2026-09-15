<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityResult;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class nilaicontroller extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

        // 1) Ambil ID kelas yang diaampu guru dari pivot teacher_classes
        $classIds = DB::table('teacher_classes')
            ->where('id_teacher', $teacherId)
            ->pluck('id_class')
            ->toArray();

        if (empty($classIds)) {
            $grouped = collect([]);
            return view('guru.datanilai', [
                'grouped' => $grouped
            ]);
        }

        $resultByClass = collect();

        // 2) Untuk tiap kelas ambil siswa, subject, topic, activity, dan hasil
        foreach ($classIds as $classId) {
            $studentIds = DB::table('student_classes')
                ->where('id_class', $classId)
                ->pluck('id_student')
                ->toArray();

            $students = collect();
            if (!empty($studentIds)) {
                $students = User::whereIn('id', $studentIds)
                    ->select('id', 'name', 'email')
                    ->get();
            }

            // Ambil subjects di kelas ini
            $subjects = Subject::where('id_class', $classId)
                ->with([
                    'topics' => function ($qTopic) {
                        $qTopic->with([
                            'activities' => function ($qAct) {
                                $qAct->with([
                                    'activityResults' => function ($qAR) {
                                        $qAR->select('id', 'id_activity', 'id_user', 'nilai_akhir', 'result');
                                    }
                                ]);
                            }
                        ]);
                    }
                ])
                ->get();

            // Jika kosong, fallback kueri
            if ($subjects->isEmpty()) {
                $subjects = Subject::where('id_class', $classId)
                    ->with(['topics.activities.activityResults'])
                    ->get();
            }

            $classData = [
                'class_id' => $classId,
                'class_name' => null,
                'students' => $students,
                'subjects' => []
            ];

            $classModel = Classes::find($classId);
            $classData['class_name'] = $classModel ? $classModel->name : ('Kelas ' . $classId);

            foreach ($subjects as $subject) {
                $subjectItem = [
                    'id' => $subject->id ?? null,
                    'name' => $subject->name ?? 'Mata Pelajaran',
                    'topics' => []
                ];

                $topics = $subject->topics ?? collect();
                foreach ($topics as $topic) {
                    $topicItem = [
                        'id' => $topic->id ?? null,
                        'title' => $topic->title ?? 'Topik',
                        'activities' => []
                    ];

                    $activities = collect();
                    if (isset($topic->activities)) {
                        $activities = $topic->activities;
                    } else {
                        $activities = Activity::where('id_topic', $topic->id)->get();
                    }

                    // Tambahkan dukungan untuk mengambil aktivitas bertipe 'evaluation' yang terikat melalui activity_topics
                    $evaluationActivities = DB::table('activities')
                        ->join('activity_topics', 'activities.id', '=', 'activity_topics.id_activity')
                        ->where('activity_topics.id_topic', $topic->id)
                        ->get();

                    foreach ($evaluationActivities as $evalAct) {
                        $actObj = Activity::find($evalAct->id);
                        if ($actObj && !$activities->contains('id', $actObj->id)) {
                            $activities->push($actObj);
                        }
                    }

                    foreach ($activities as $activity) {
                        $resultsQuery = DB::table('activity_result')
                            ->where('id_activity', $activity->id)
                            ->whereIn('id_user', $studentIds);

                        $results = $resultsQuery->select('id', 'id_activity', 'id_user', 'nilai_akhir', 'result')->get();

                        $resultsByStudent = [];
                        foreach ($results as $r) {
                            $nilai = null;
                            if (isset($r->nilai_akhir) && !is_null($r->nilai_akhir)) {
                                $nilai = $r->nilai_akhir;
                            } elseif (isset($r->result) && !is_null($r->result)) {
                                $nilai = $r->result;
                            }
                            $resultsByStudent[$r->id_user] = [
                                'id' => $r->id,
                                'nilai' => $nilai
                            ];
                        }

                        $activityItem = [
                            'id' => $activity->id,
                            'title' => $activity->title ?? 'Aktivitas',
                            'results' => $resultsByStudent,
                            'results_count' => count($resultsByStudent)
                        ];

                        $topicItem['activities'][] = $activityItem;
                    }

                    $subjectItem['topics'][] = $topicItem;
                }

                $classData['subjects'][] = $subjectItem;
            }

            $resultByClass->push($classData);
        }

        return view('guru.datanilai', [
            'grouped' => $resultByClass
        ]);
    }

    public function showActivity(Request $request, $id)
    {
        $teacherId = Auth::id();
        $activity = Activity::with('topic.subject')->findOrFail($id);

        $classId = optional(optional($activity->topic)->subject)->id_class;

        if (!$classId) {
            $evalClass = DB::table('activity_topics')
                ->join('topics', 'activity_topics.id_topic', '=', 'topics.id')
                ->join('subject', 'topics.id_subject', '=', 'subject.id')
                ->where('activity_topics.id_activity', $activity->id)
                ->select('subject.id_class')
                ->first();
            $classId = $evalClass ? $evalClass->id_class : null;
        }

        $teaches = DB::table('teacher_classes')
            ->where('id_teacher', $teacherId)
            ->where('id_class', $classId)
            ->exists();

        if (!$teaches) {
            abort(403, 'Tidak diizinkan melihat data ini.');
        }

        $studentIds = DB::table('student_classes')
            ->where('id_class', $classId)
            ->pluck('id_student')
            ->toArray();

        $students = User::whereIn('id', $studentIds)->get(['id', 'name']);

        $results = DB::table('activity_result')
            ->where('id_activity', $activity->id)
            ->whereIn('id_user', $studentIds)
            ->select('id', 'id_activity', 'id_user', 'nilai_akhir', 'result')
            ->get()
            ->keyBy('id_user');

        $studentRows = $students->map(function ($s) use ($results) {
            $res = $results->get($s->id);
            $nilai = null;
            if ($res) {
                if (isset($res->nilai_akhir) && !is_null($res->nilai_akhir)) {
                    $nilai = $res->nilai_akhir;
                } elseif (isset($res->result) && !is_null($res->result)) {
                    $nilai = $res->result;
                }
            }
            return [
                'id' => $s->id,
                'name' => $s->name,
                'nilai' => $nilai
            ];
        });

        if ($request->query('export') === 'xlsx') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama Siswa');
            $sheet->setCellValue('C1', 'Nilai Akhir');

            $row = 2;
            foreach ($studentRows as $index => $stu) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValueExplicit('B' . $row, $stu['name'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                if (is_numeric($stu['nilai'])) {
                    $sheet->setCellValue('C' . $row, (float) $stu['nilai']);
                } else {
                    $sheet->setCellValueExplicit('C' . $row, $stu['nilai'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
                $row++;
            }

            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $safeActivityTitle = preg_replace('/[^A-Za-z0-9\-]/', '_', substr($activity->title ?? 'activity', 0, 30));
            $filename = "nilai_{$safeActivityTitle}_{$activity->id}_" . date('Ymd_His') . ".xlsx";

            $writer = new Xlsx($spreadsheet);
            $response = new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return view('guru.detailnilaisiswa', [
            'activity' => $activity,
            'class_id' => $classId,
            'students' => $studentRows
        ]);
    }

    public function exportClassesExcel(Request $request)
    {
        $teacherId = Auth::id();
        $classIds = DB::table('teacher_classes')
            ->where('id_teacher', $teacherId)
            ->pluck('id_class')
            ->toArray();

        if (empty($classIds)) {
            return redirect()->back()->with('error', 'Tidak ada kelas untuk diexport.');
        }

        $classesData = [];
        foreach ($classIds as $classId) {
            $studentIds = DB::table('student_classes')->where('id_class', $classId)->pluck('id_student')->toArray();
            $students = collect();
            if (!empty($studentIds)) {
                $students = User::whereIn('id', $studentIds)
                    ->select('id', 'name', 'email')
                    ->orderBy('name')
                    ->get();
            }

            $activities = collect();
            $subjects = Subject::where('id_class', $classId)->with(['topics.activities'])->get();
            foreach ($subjects as $subj) {
                foreach ($subj->topics ?? [] as $topic) {
                    foreach ($topic->activities ?? [] as $act) {
                        $activities->push($act);
                    }
                    // Ambil evaluasi multi-topik terkait
                    $evals = DB::table('activities')
                        ->join('activity_topics', 'activities.id', '=', 'activity_topics.id_activity')
                        ->where('activity_topics.id_topic', $topic->id)
                        ->select('activities.*')
                        ->get();
                    foreach ($evals as $ev) {
                        $actObj = Activity::find($ev->id);
                        if ($actObj) {
                            $activities->push($actObj);
                        }
                    }
                }
            }

            $activities = $activities->unique('id')->values();
            $activityIds = $activities->pluck('id')->toArray();
            $results = [];
            if (!empty($activityIds) && !empty($studentIds)) {
                $rows = DB::table('activity_result')
                    ->whereIn('id_activity', $activityIds)
                    ->whereIn('id_user', $studentIds)
                    ->select('id_activity', 'id_user', 'nilai_akhir', 'result')
                    ->get();

                foreach ($rows as $r) {
                    $val = null;
                    if (isset($r->nilai_akhir) && !is_null($r->nilai_akhir))
                        $val = $r->nilai_akhir;
                    elseif (isset($r->result) && !is_null($r->result))
                        $val = $r->result;
                    $results[$r->id_activity][$r->id_user] = $val;
                }
            }

            $className = 'Kelas ' . $classId;
            $c = Classes::find($classId);
            if ($c) {
                $className = $c->name ?? $className;
            }

            $classesData[] = [
                'class_id' => $classId,
                'class_name' => $className,
                'students' => $students,
                'activities' => $activities,
                'results' => $results,
            ];
        }

        $spreadsheet = new Spreadsheet();
        $getUniqueTitle = function ($baseTitle) use ($spreadsheet) {
            $clean = preg_replace('/[\\\|\\/?*\\[\\]:]/', '_', $baseTitle);
            $clean = trim(mb_substr($clean, 0, 28));
            $names = $spreadsheet->getSheetNames();

            $candidate = $clean ?: 'Sheet';
            $suffix = 1;
            while (in_array($candidate, $names)) {
                $suffix++;
                $candidate = mb_substr($clean, 0, max(1, 28 - (strlen((string) $suffix) + 1))) . '_' . $suffix;
            }
            return $candidate;
        };

        $first = true;
        foreach ($classesData as $cdata) {
            if ($first) {
                $sheet = $spreadsheet->getActiveSheet();
                $first = false;
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            $titleBase = $cdata['class_name'] ?? ('Class_' . $cdata['class_id']);
            $title = $getUniqueTitle($titleBase);
            $sheet->setTitle(mb_substr($title, 0, 31));

            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Student ID');
            $sheet->setCellValue('C1', 'Nama Siswa');

            $colIndex = 4;
            $activityMap = [];
            foreach ($cdata['activities'] as $act) {
                $safeTitle = $act->title ?? ('Activity_' . $act->id);
                $header = mb_substr($safeTitle, 0, 50) . " ({$act->id})";
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1';
                $sheet->setCellValue($cell, $header);
                $activityMap[$colIndex] = $act->id;
                $colIndex++;
            }

            $row = 2;
            foreach ($cdata['students'] as $i => $stu) {
                $sheet->setCellValue('A' . $row, ($i + 1));
                $sheet->setCellValueExplicit('B' . $row, (string) $stu->id, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $row, (string) $stu->name, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                foreach ($activityMap as $colIdx => $activityId) {
                    $val = null;
                    if (isset($cdata['results'][$activityId]) && isset($cdata['results'][$activityId][$stu->id])) {
                        $val = $cdata['results'][$activityId][$stu->id];
                    }
                    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx) . $row;
                    if (is_numeric($val)) {
                        $sheet->setCellValue($cell, (float) $val);
                    } else {
                        $sheet->setCellValueExplicit($cell, $val ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                }
                $row++;
            }

            for ($ci = 1; $ci <= ($colIndex - 1); $ci++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $teacher = User::find($teacherId);
        $teacherName = $teacher ? preg_replace('/[^A-Za-z0-9]/', '_', mb_substr($teacher->name, 0, 20)) : 'teacher';
        $filename = "nilai_semua_kelas_{$teacherName}_" . date('Ymd_His') . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    public function exportClassExcel(Request $request, $classId)
    {
        $teacherId = Auth::id();
        $teaches = DB::table('teacher_classes')
            ->where('id_teacher', $teacherId)
            ->where('id_class', $classId)
            ->exists();

        if (!$teaches) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengekspor kelas ini.');
        }

        $studentIds = DB::table('student_classes')->where('id_class', $classId)->pluck('id_student')->toArray();
        $students = collect();
        if (!empty($studentIds)) {
            $students = User::whereIn('id', $studentIds)->select('id', 'name', 'email')->orderBy('name')->get();
        }

        $activities = collect();
        $subjects = Subject::where('id_class', $classId)->with(['topics.activities'])->get();
        foreach ($subjects as $subj) {
            foreach ($subj->topics ?? [] as $topic) {
                foreach ($topic->activities ?? [] as $act) {
                    $activities->push($act);
                }
                $evals = DB::table('activities')
                    ->join('activity_topics', 'activities.id', '=', 'activity_topics.id_activity')
                    ->where('activity_topics.id_topic', $topic->id)
                    ->select('activities.*')
                    ->get();
                foreach ($evals as $ev) {
                    $actObj = Activity::find($ev->id);
                    if ($actObj) {
                        $activities->push($actObj);
                    }
                }
            }
        }

        $activities = $activities->unique('id')->values();
        $activityIds = $activities->pluck('id')->toArray();

        $results = [];
        if (!empty($activityIds) && !empty($studentIds)) {
            $rows = DB::table('activity_result')
                ->whereIn('id_activity', $activityIds)
                ->whereIn('id_user', $studentIds)
                ->select('id_activity', 'id_user', 'nilai_akhir', 'result')
                ->get();

            foreach ($rows as $r) {
                $val = null;
                if (isset($r->nilai_akhir) && !is_null($r->nilai_akhir))
                    $val = $r->nilai_akhir;
                elseif (isset($r->result) && !is_null($r->result))
                    $val = $r->result;
                $results[$r->id_activity][$r->id_user] = $val;
            }
        }

        $className = 'Kelas ' . $classId;
        $c = Classes::find($classId);
        if ($c) {
            $className = $c->name ?? $className;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheetTitle = substr(preg_replace('/[\\\|\\/?*\\[\\]:]/', '_', $className), 0, 31);
        $sheet->setTitle($sheetTitle ?: 'Kelas');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Student ID');
        $sheet->setCellValue('C1', 'Nama Siswa');

        $colIndex = 4;
        $activityMap = [];
        foreach ($activities as $act) {
            $safeTitle = $act->title ?? ('Activity_' . $act->id);
            $header = mb_substr($safeTitle, 0, 50) . " ({$act->id})";
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1';
            $sheet->setCellValue($cell, $header);
            $activityMap[$colIndex] = $act->id;
            $colIndex++;
        }

        $row = 2;
        foreach ($students as $i => $stu) {
            $sheet->setCellValue('A' . $row, ($i + 1));
            $sheet->setCellValueExplicit('B' . $row, (string) $stu->id, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, (string) $stu->name, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            foreach ($activityMap as $colIdx => $activityId) {
                $val = null;
                if (isset($results[$activityId]) && isset($results[$activityId][$stu->id])) {
                    $val = $results[$activityId][$stu->id];
                }
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx) . $row;
                if (is_numeric($val)) {
                    $sheet->setCellValue($cell, (float) $val);
                } else {
                    $sheet->setCellValueExplicit($cell, $val ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
            }
            $row++;
        }

        for ($ci = 1; $ci <= ($colIndex - 1); $ci++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $teacher = User::find($teacherId);
        $teacherName = $teacher ? preg_replace('/[^A-Za-z0-9]/', '_', mb_substr($teacher->name, 0, 20)) : 'teacher';
        $filename = "nilai_kelas_{$sheetTitle}_{$teacherName}_" . date('Ymd_His') . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}