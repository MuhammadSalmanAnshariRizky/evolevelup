<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityResult;
use App\Models\nilai;
use App\Models\Question;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityAnswer;


class aktivitasController extends Controller
{
    public function aktivitasSiswa()
    {
        $user = Auth::user();

        // 🔹 Ambil data badge
        $badge = DB::table('user_badge')
            ->join('badge', 'user_badge.id_badge', '=', 'badge.id')
            ->where('user_badge.id_student', $user->id)
            ->select('badge.name', 'badge.description')
            ->first();

        // 🔹 Ambil daftar kelas siswa
        $kelasList = DB::table('student_classes')
            ->join('classes', 'student_classes.id_class', '=', 'classes.id')
            ->where('student_classes.id_student', $user->id)
            ->select('classes.id', 'classes.name', 'classes.level', 'classes.token')
            ->get();

        // 🔹 Ambil aktivitas + nilai
        $rawActivities = DB::table('activities')
            ->join('topics', 'activities.id_topic', '=', 'topics.id')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->join('classes', 'subject.id_class', '=', 'classes.id')
            ->join('student_classes', 'classes.id', '=', 'student_classes.id_class')
            ->join('users', 'student_classes.id_student', '=', 'users.id')
            ->leftJoin('activity_result', function ($join) use ($user) {
                $join->on('activities.id', '=', 'activity_result.id_activity')
                    ->where('activity_result.id_user', '=', $user->id);
            })
            ->where('users.id', $user->id)
            ->whereIn('classes.token', $kelasList->pluck('token'))
            ->select(
                'activities.id as id_activity',
                'activities.id_topic',
                'activities.title as aktivitas',
                'activities.status',
                'topics.title as topik',
                'subject.name as mapel',
                'classes.id as id_class',
                'classes.name as nama_kelas',
                'classes.level as level_kelas',
                'activities.created_at',
                // 🔹 pastikan kolom deadline ini ada, kalau beda nama ganti di sini
                'activities.deadline',
                DB::raw('COALESCE(activity_result.nilai_akhir, "-") as result'),
                DB::raw('COALESCE(activity_result.result_status, "Belum Dikerjakan") as result_status')
            )
            ->get();

        // 🔹 List paling atas: semua yang Belum Dikerjakan, urut deadline terdekat
        $belumDikerjakan = $rawActivities
            ->where('result_status', 'Belum Dikerjakan')
            ->sortBy(function ($item) {
                return $item->deadline ?? $item->created_at;
            })
            ->values();

        // 🔹 Activities per kelas
        $activitiesByClass = $rawActivities
            ->groupBy('id_class')
            ->map(function ($group) {
                // urutkan di dalam kelas:
                // 1) Belum Dikerjakan
                // 2) Remedial
                // 3) Pass
                // 4) lainnya
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
            // urutkan kelas: level lalu nama
            ->sortBy(function ($kelas) {
                return $kelas->level_kelas . '|' . $kelas->nama_kelas;
            })
            ->values();

        // 🔹 Statistik
        $jumlahAktivitas = $rawActivities->count();
        $jumlahRemedial = $rawActivities->where('result_status', 'Remedial')->count();

        // 🔹 Kirim ke view
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

        // Ambil relasi lengkap berdasarkan id_topic
        $info = DB::table('topics')
            ->join('subject', 'topics.id_subject', '=', 'subject.id')
            ->join('classes', 'subject.id_class', '=', 'classes.id')
            ->where('topics.id', $activity->id_topic)
            ->select(
                'topics.title as topik',
                'subject.name as mapel',
                'classes.name as kelas'
            )
            ->first();

        return view('siswa.menjawabSoal', [
            'judul' => $activity->title,
            'kelas' => $info->kelas,
            'mapel' => $info->mapel,
            'topik' => $info->topik,
            'id_activity' => $activity->id,
            'addaptive' => $activity->addaptive,
            'durasi' => $activity->durasi_pengerjaan,
            'jumlah_soal' => $activity->jumlah_soal,
        ]);
    }

    /**
     * TAHAP 2: Inisialisasi Ujian Adaptif
     */
    public function start(Request $req, $id)
    {
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

        // 🔹 PERBAIKAN: Ambil jumlah soal dari database activity ($activity->jumlah_soal) 
        // atau fallback ke total soal di tabel jika kosong, lalu batasi maksimal 25 (atau sesuai kebutuhan).
        $settingJumlahSoal = $activity->jumlah_soal ? (int) $activity->jumlah_soal : $totalDB;

        // Batas maksimal soal adaptif disesuaikan dengan pengaturan aktivitas atau total soal di DB
        $maxSoal = min($totalDB, max(10, $settingJumlahSoal));
        $jumlahSoal = min($totalDB, $maxSoal);

        // Minimal soal (bisa diatur setengah dari total max atau tetap 10 jika soal mencukupi)
        $minSoal = min(5, (int) floor($jumlahSoal / 2));
        if ($jumlahSoal >= 10) {
            $minSoal = 10; // Jika total soal >= 10, minimal soal tetap 10 sesuai standar CAT
        }

        // Inisialisasi Session sesuai Parameter Rasch Model
        session([
            "activity.$id.theta" => 0.0,                    // Kemampuan awal mahasiswa (Logit)
            "activity.$id.se" => 1.0,                       // Standard Error awal
            "activity.$id.current_index" => 0,
            "activity.$id.used_questions" => [],           // Menyimpan id soal yang sudah dikerjakan
            "activity.$id.history" => [],                   // Menyimpan riwayat: [ {id, delta, is_correct} ]
            "activity.$id.total_correct" => 0,
            "activity.$id.max_questions" => $jumlahSoal,     // Mengikuti jumlah soal aktivitas
            "activity.$id.min_questions" => $minSoal,       // Batas minimal soal sebelum boleh berhenti
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
                'total_benar' => null
            ]
        );

        $durasiMenit = $activity->durasi_pengerjaan ? (int) $activity->durasi_pengerjaan : null;

        return response()->json([
            'mode' => $adaptive ? 'adaptive' : 'normal',
            'theta_initial' => 0.0,
            'totalQuestions' => $jumlahSoal,
            'target_se' => 0.30,
            'started_at' => $startTime->toDateTimeString(),
            'durasi_pengerjaan' => $durasiMenit
        ]);
    }

    /**
     * TAHAP 3 (Langkah 3A): Mencari Soal yang Pas Berdasarkan Delta & Theta
     */
    public function getQuestion(Request $req, $id)
    {
        $activity = Activity::findOrFail($id);
        $adaptive = $activity->addaptive === 'yes';
        $used = session("activity.$id.used_questions", []);

        if ($adaptive) {
            $theta = session("activity.$id.theta", 0.0);

            // [Langkah 3A Rasch Model]: Cari soal yang belum dikerjakan dengan delta paling mendekati theta saat ini
            $question = $activity->questions()
                ->whereNotIn('question.id', $used)
                ->orderBy(DB::raw("ABS(delta - {$theta})"), 'ASC')
                ->first();

            // Fallback jika query utama kosong
            if (!$question) {
                $question = $activity->questions()
                    ->whereNotIn('question.id', $used)
                    ->inRandomOrder()
                    ->first();
            }
        } else {
            $index = $req->query('index', 0);
            $question = $activity->questions()
                ->orderBy('id')
                ->skip($index)
                ->first();
        }

        if (!$question) {
            return response()->json([
                'end' => true,
                'message' => 'Ujian selesai. Tidak ada soal tersisa.'
            ]);
        }

        // 🔹 TAMBAHKAN KODE INI: Kategorikan tingkat kesulitan secara otomatis dari delta
        $deltaVal = (float) ($question->delta ?? 0.0);
        if ($deltaVal < -0.5) {
            $difficulty = 'Mudah';
        } elseif ($deltaVal <= 0.5) {
            $difficulty = 'Sedang';
        } else {
            $difficulty = 'Sulit';
        }

        return response()->json([
            'question_id' => $question->id,
            'type' => $question->type,
            'delta' => $deltaVal,
            'difficulty' => $difficulty, // <-- Dikirim ke frontend agar badge ikut berubah dinamis
            'question' => json_decode($question->question),
            'options' => json_decode($question->MC_option),
        ]);
    }
    /**
     * TAHAP 3 (Langkah 3B & 3C): Eksekusi & Re-Estimasi Kemampuan (Theta & SE)
     */
    public function submitAnswer(Request $req, $id)
    {
        $question = Question::findOrFail($req->question_id);
        $activity = Activity::findOrFail($id);
        $adaptive = $activity->addaptive === 'yes';

        // 1. Cek Kebenaran Jawaban
        $correct = false;
        if ($question->type === 'MultipleChoice') {
            $correct = strtolower(trim($req->user_answer)) === strtolower(trim($question->MC_answer));
        } else if ($question->type === 'ShortAnswer') {
            $answersRaw = $question->SA_answer;
            $answers = is_string($answersRaw) ? json_decode($answersRaw, true) : $answersRaw;
            if (!is_array($answers))
                $answers = [];

            $user = strtolower(trim($req->user_answer));
            $correct = in_array($user, array_map('strtolower', $answers));
        }

        // 2. Simpan Jawaban ke Database (Tabel Riwayat Sementara)
        ActivityAnswer::updateOrCreate(
            [
                'id_activity' => $id,
                'id_user' => auth()->id(),
                'id_question' => $question->id,
            ],
            [
                'user_answer' => $req->user_answer,
                'is_correct' => $correct,
                'delta' => $question->delta ?? 0.0, // Simpan delta soal saat dikerjakan sesuai brief
            ]
        );

        $totalCorrect = session("activity.$id.total_correct", 0);
        if ($correct) {
            session(["activity.$id.total_correct" => $totalCorrect + 1]);
        }

        // Tandai soal sudah digunakan
        $used = session("activity.$id.used_questions", []);
        $used[] = $question->id;
        session(["activity.$id.used_questions" => $used]);

        $shouldStop = false;

        if ($adaptive) {
            // 3. Masukkan ke array riwayat sementara di session
            $history = session("activity.$id.history", []);
            $history[] = [
                'id' => $question->id,
                'delta' => (float) ($question->delta ?? 0.0),
                'is_correct' => $correct ? 1 : 0
            ];
            session(["activity.$id.history" => $history]);

            // 4. Kalkulasi Ulang Theta ($\theta$) & Standard Error (SE) menggunakan Pendekatan Rasch
            $theta = session("activity.$id.theta", 0.0);

            // Penyesuaian nilai theta sederhana berbasis bobot delta dan benar/salah (bisa diganti Newton-Raphson penuh)
            $adjustment = $correct ? 0.35 : -0.35;
            $theta += $adjustment;

            // Batasi rentang logit theta agar stabil (misal: -3.0 sampai +3.0)
            $theta = max(-3.0, min(3.0, $theta));

            // Estimasi penurunan Standard Error (SE) seiring bertambahnya jumlah soal dikerjakan
            $numSoalDikerjakan = count($history);
            $se = max(0.20, 1.0 / sqrt($numSoalDikerjakan));

            session([
                "activity.$id.theta" => $theta,
                "activity.$id.se" => $se
            ]);

            // 5. [Langkah 3D]: Pengecekan Syarat Berhenti (Stopping Rule) Dinamis
            $minSoal = session("activity.$id.min_questions", 10);
            $maxSoal = session("activity.$id.max_questions", 25); // Mengambil max_questions dari session (sesuai jumlah soal aktivitas)
            $targetSe = 0.30; // Target kestabilan error

            // Aturan 1: Jika jumlah soal yang dikerjakan sudah mencapai batas maksimal aktivitas, wajib berhenti
            if ($numSoalDikerjakan >= $maxSoal) {
                $shouldStop = true;
            }
            // Aturan 2: Jika sudah melewati batas minimal soal DAN tingkat error sudah stabil
            elseif ($numSoalDikerjakan >= $minSoal && $se <= $targetSe) {
                $shouldStop = true;
            }

            // Aturan 3 (Pengaman Tambahan): Jika soal di bank soal habis total
            $totalDB = $activity->questions()->count();
            $usedCount = count(session("activity.$id.used_questions", []));
            if ($usedCount >= $totalDB) {
                $shouldStop = true;
            }
        } else {
            // Mode normal berdasarkan jumlah soal total
            $maxSoal = session("activity.$id.max_questions", 10);
            if (count($used) >= $maxSoal) {
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
            'current_se' => session("activity.$id.se", 1.0), // <--- TAMBAHKAN BARIS INI
            'target_se' => $targetSe ?? 0.30 // <--- TAMBAHKAN BARIS INI
        ]);
    }

    /**
     * TAHAP 4: Finalisasi & Kalkulasi Nilai Akhir Berdasarkan Theta Terakhir
     */
    public function finishTest(Request $req, $id)
    {
        $userId = auth()->id();
        $activity = Activity::findOrFail($id);

        $thetaAkhir = session("activity.$id.theta", 0.0);
        $totalCorrect = session("activity.$id.total_correct", 0);
        $history = session("activity.$id.history", []);
        $jumlahSoalDikerjakan = max(1, count($history));

        $activityResult = ActivityResult::where('id_activity', $id)
            ->where('id_user', $userId)
            ->first();

        $start = ($activityResult && $activityResult->start_time)
            ? Carbon::parse($activityResult->start_time)
            : Carbon::parse(session("activity.$id.start_time", Carbon::now()));

        $end = Carbon::now();
        $durationSeconds = max(0, $end->getTimestamp() - $start->getTimestamp());

        // [TAHAP 4]: Skalabilitas Logit Theta ke 0 - 100 (Metode True Score Mapping Dinamis)
        if ($activity->addaptive === 'yes') {

            // 1. Ambil semua tingkat kesulitan (delta) dari seluruh soal di aktivitas ini
            $allDeltas = $activity->questions()->pluck('delta');
            $totalBankSoal = $allDeltas->count();

            if ($totalBankSoal > 0) {
                $expectedScore = 0;

                // 2. Hitung probabilitas mahasiswa menjawab benar tiap-tiap soal
                foreach ($allDeltas as $delta) {
                    $deltaVal = (float) ($delta ?? 0.0);

                    // Rumus fungsi logistik Rasch Model: P = exp(theta - delta) / (1 + exp(theta - delta))
                    $eksponensial = exp($thetaAkhir - $deltaVal);
                    $probabilitas = $eksponensial / (1 + $eksponensial);

                    $expectedScore += $probabilitas;
                }

                // 3. Konversi akumulasi probabilitas menjadi persentase skala 0 - 100
                $nilaiAkhir = round(($expectedScore / $totalBankSoal) * 100, 2);
            } else {
                $nilaiAkhir = 0;
            }

        } else {
            // Mode normal menggunakan persentase benar biasa
            $nilaiAkhir = round(($totalCorrect / $jumlahSoalDikerjakan) * 100, 2);
        }

        $kkm = $activity->kkm ?? 70;
        $status = $nilaiAkhir >= $kkm ? 'Pass' : 'Remedial';

        // Simpan ke Database Hasil Ujian
        ActivityResult::updateOrCreate(
            [
                'id_activity' => $id,
                'id_user' => $userId,
            ],
            [
                'result' => $thetaAkhir, // Menyimpan logit akhir theta
                'bonus_poin' => 0,
                'real_poin' => $totalCorrect,
                'result_status' => $status,
                'waktu_mengerjakan' => $durationSeconds,
                'total_benar' => $totalCorrect,
                'start_time' => $start,
                'end_time' => $end,
                'status_benar' => ($totalCorrect === $jumlahSoalDikerjakan),
                'nilai_akhir' => $nilaiAkhir, // Nilai skala 0-100
            ]
        );

        $updatedResult = ActivityResult::where('id_activity', $id)
            ->where('id_user', $userId)
            ->first();

        // Bersihkan session ujian
        session()->forget("activity.$id");

        return response()->json([
            'status' => 'saved',
            'duration_seconds' => $durationSeconds,
            'total_correct' => $totalCorrect,
            'jumlah_soal' => $jumlahSoalDikerjakan,
            'result_db' => [
                'theta_akhir' => $thetaAkhir,
                'nilai_akhir' => $updatedResult->nilai_akhir,
                'result_status' => $updatedResult->result_status,
                'total_benar' => $updatedResult->total_benar,
                'start_time' => optional($updatedResult->start_time)->toDateTimeString(),
                'end_time' => optional($updatedResult->end_time)->toDateTimeString(),
            ]
        ]);
    }

}
