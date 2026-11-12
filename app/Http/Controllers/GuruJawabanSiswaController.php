<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityAnswer;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruJawabanSiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil daftar kelas yang dibuat oleh guru
        $classes = Classes::where('created_by', $user->id)->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);

        $activities = collect();

        if ($selectedClassId) {
            // 2. Ambil semua ID Subject yang terikat ke id_class pilihan
            $subjectIds = Subject::where('id_class', $selectedClassId)->pluck('id');

            // 3. Ambil aktivitas yang terhubung dengan Subject di kelas tersebut
            $activities = Activity::where(function ($query) use ($subjectIds) {
                $query->where('type', 'evaluation')
                    ->whereHas('topics', function ($q) use ($subjectIds) {
                        $q->whereIn('id_subject', $subjectIds);
                    });
            })->orWhere(function ($query) use ($subjectIds) {
                $query->where('type', '!=', 'evaluation')
                    ->whereHas('topic', function ($q) use ($subjectIds) {
                        $q->whereIn('id_subject', $subjectIds);
                    });
            })->get();
        }

        $selectedActivityId = $request->get('activity_id', $activities->first()?->id);

        // Ambil detail data aktivitas terpilih
        $selectedActivity = Activity::find($selectedActivityId);

        // 4. Rekap jawaban siswa per user
        $studentSummaries = collect();
        if ($selectedActivityId) {
            $answers = ActivityAnswer::with('user')
                ->where('id_activity', $selectedActivityId)
                ->get();

            // Kelompokkan jawaban berdasarkan siswa (id_user)
            $studentSummaries = $answers->groupBy('id_user')->map(function ($userAnswers, $userId) {
                return [
                    'user_id' => $userId,
                    'user_name' => $userAnswers->first()->user->name ?? 'N/A',
                    'total_answered' => $userAnswers->count(),
                    'correct_count' => $userAnswers->where('is_correct', 1)->count(),
                    'wrong_count' => $userAnswers->where('is_correct', 0)->count(),
                ];
            });
        }

        return view('guru.jawaban_siswa', compact(
            'classes',
            'activities',
            'selectedActivity',
            'studentSummaries',
            'selectedClassId',
            'selectedActivityId'
        ));
    }

    /**
     * Method untuk menampilkan detail jawaban satu siswa
     */
    public function detail($activityId, $userId)
    {
        $activity = Activity::find($activityId);
        if (!$activity) {
            return redirect()->back()->with('error', 'Aktivitas tidak ditemukan.');
        }

        $student = User::findOrFail($userId);

        // Ambil seluruh jawaban milik siswa tersebut pada aktivitas ini
        $answers = ActivityAnswer::with('question')
            ->where('id_activity', $activityId)
            ->where('id_user', $userId)
            ->get();

        return view('guru.detail_jawaban_siswa', compact('activity', 'student', 'answers'));
    }

    /**
     * Method Export XLSX Rekap Jawaban per Aktivitas
     */
    public function exportActivityXlsx($activityId)
    {
        // 1. Ambil data aktivitas
        $activity = DB::table('activities')->where('id', $activityId)->first();

        if (!$activity) {
            return redirect()->back()->with('error', 'Aktivitas dengan ID ' . $activityId . ' benar-benar tidak ada di tabel activities!');
        }

        // 2. Ambil data hasil akhir dari tabel `activity_result`
        $results = DB::table('activity_result')
            ->where('id_activity', $activityId)
            ->get()
            ->keyBy('id_user');

        // 3. Ambil rincian jawaban siswa beserta relasi ke user dan question
        $answers = ActivityAnswer::with(['user', 'question'])
            ->where('id_activity', $activityId)
            ->orderBy('id_user', 'asc') // Wajib diurutkan per siswa agar datanya ngumpul
            ->orderBy('id', 'asc')      // Urutan soal sesuai riwayat
            ->get();

        if ($answers->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data jawaban siswa pada aktivitas ini.');
        }

        // 4. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $safeTitle = substr(preg_replace('/[\\\|\\/?*\\[\\]:]/', '_', $activity->title ?? 'Activity'), 0, 31);
        $sheet->setTitle($safeTitle ?: 'Jawaban_Siswa');

        // Header Kolom (6 Kolom Utama)
        $sheet->setCellValue('A1', 'Nama Peserta');
        $sheet->setCellValue('B1', 'ID Soal');
        $sheet->setCellValue('C1', 'Tingkat Kesulitan');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Nilai Akhir');
        $sheet->setCellValue('F1', 'Result');

        // Kelompokkan jawaban berdasarkan id_user agar mudah di-merge
        $groupedAnswers = $answers->groupBy('id_user');

        // 5. Pengisian Data dan Merge
        $row = 2;
        foreach ($groupedAnswers as $userId => $userAnswers) {
            $startRow = $row; // Simpan baris awal untuk siswa ini

            // Ambil data siswa & nilai (cukup sekali per siswa)
            $studentName = $userAnswers->first()->user->name ?? 'N/A';
            $userResult = $results->get($userId);
            $nilaiAkhir = ($userResult && !is_null($userResult->nilai_akhir)) ? $userResult->nilai_akhir : '-';
            $resultVal = ($userResult && !is_null($userResult->result)) ? $userResult->result : '-';

            // Loop untuk setiap jawaban milik siswa tersebut
            foreach ($userAnswers as $ans) {
                $questionId = $ans->id_question;
                $tingkatKesulitan = $ans->question->difficulty ?? '-';
                $statusText = (!empty($ans->is_correct)) ? 'Benar' : 'Salah';

                // Tulis Ke Cell Excel
                $sheet->setCellValueExplicit('A' . $row, (string) $studentName, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $row, (string) $questionId, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $row, (string) $tingkatKesulitan, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D' . $row, (string) $statusText, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('E' . $row, (string) $nilaiAkhir, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('F' . $row, (string) $resultVal, DataType::TYPE_STRING);

                $row++;
            }

            $endRow = $row - 1; // Simpan baris akhir untuk siswa ini

            // Lakukan proses Merge Cell jika siswa menjawab lebih dari 1 soal
            if ($endRow > $startRow) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Kolom Nama Peserta
                $sheet->mergeCells("E{$startRow}:E{$endRow}"); // Merge Kolom Nilai Akhir
                $sheet->mergeCells("F{$startRow}:F{$endRow}"); // Merge Kolom Result
            }
        }

        $maxRow = $row - 1; // Baris terakhir yang terisi data

        // =========================================================================
        // 6. STYLING (Warna Header, Border, dan Rata Tengah)
        // =========================================================================

        // A. Style Header (Bold & Warna Background Abu-abu Muda)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD9D9D9',
                ],
            ],
        ]);

        // B. Border Hitam untuk Seluruh Tabel (A1 sampai F terakhir)
        $sheet->getStyle('A1:F' . $maxRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // C. Rata Tengah (Horizontal & Vertikal) untuk Seluruh Tabel
        $sheet->getStyle('A1:F' . $maxRow)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // =========================================================================

        // 7. Auto-fit Ukuran Kolom (A - F)
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // =========================================================================
        // 8. Output Response Download File Excel
        // =========================================================================
        $cleanActivityName = preg_replace('/[^A-Za-z0-9\-]/', '_', substr($activity->title ?? 'aktivitas', 0, 25));
        $filename = "jawaban_{$cleanActivityName}_{$activityId}_" . date('Ymd_His') . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_export');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}