<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class aturAktivitasController extends Controller
{
    public function halamanAturSoal($idAktivitas, Request $request)
    {
        $aktivitas = Activity::findOrFail($idAktivitas);

        // 1. Ambil daftar ID topik menggunakan relasi Eloquent
        $topicIds = [];

        // Cek apakah ada data di tabel pivot (tipe evaluation/multiple topic)
        if ($aktivitas->topics()->exists()) {
            $topicIds = $aktivitas->topics()->pluck('topics.id')->toArray();
        }
        // Jika tidak ada di tabel pivot, gunakan kolom id_topic di tabel activities (tipe exercise/single topic)
        elseif (!empty($aktivitas->id_topic)) {
            $savedTopics = $aktivitas->id_topic;

            // Antisipasi jika data tunggal tersimpan sebagai array JSON string (contoh: '["1", "2"]')
            if (is_string($savedTopics) && is_array(json_decode($savedTopics, true))) {
                $topicIds = json_decode($savedTopics, true);
            } else {
                $topicIds = [$savedTopics];
            }
        }

        if (empty($topicIds)) {
            abort(404, 'Topik untuk aktivitas ini belum ditentukan.');
        }

        // Ambil semua data topik & pastikan guru punya akses ke subject-nya
        $topics = Topic::with('subject')->whereIn('id', $topicIds)->get();
        if ($topics->isEmpty()) {
            abort(404, 'Topik tidak ditemukan.');
        }

        // Ambil subject pertama (atau sesuaikan validasi kelas Anda)
        $subject = $topics->first()->subject;
        if (!$subject) {
            abort(404, 'Subject untuk topik ini tidak ditemukan.');
        }

        // Validasi guru tergabung di kelas subject ini
        $classId = $subject->id_class;
        $idGuru = Auth::id();
        $isTeacherInClass = DB::table('teacher_classes')
            ->where('id_teacher', $idGuru)
            ->where('id_class', $classId)
            ->exists();

        if (!$isTeacherInClass) {
            abort(403, 'Anda tidak memiliki akses ke kelas/topik ini.');
        }

        // 2. AMBIL SEMUA SOAL DARI SEMUA TOPIK YANG DIPILIH (`whereIn`)
        $questions = Question::whereIn('id_topic', $topicIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil selected ids dari pivot (activity_question)
        $selectedIds = DB::table('activity_question')
            ->where('id_activity', $idAktivitas)
            ->pluck('id_question')
            ->toArray();

        $selectedQuestions = Question::whereIn('id', $selectedIds)->get();

        // Kirim variabel ke view
        return view('guru.atursoal', compact(
            'aktivitas',
            'questions',
            'selectedIds',
            'selectedQuestions',
            'topics',
            'subject'
        ));
    }
    public function ambilSoalAjax(Request $request, $idAktivitas)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1'
        ]);

        $aktivitas = Activity::findOrFail($idAktivitas);

        // Ambil ID topik menggunakan logika yang sama persis via Eloquent
        $topicIds = [];
        if ($aktivitas->topics()->exists()) {
            $topicIds = $aktivitas->topics()->pluck('topics.id')->toArray();
        } elseif (!empty($aktivitas->id_topic)) {
            $savedTopics = $aktivitas->id_topic;
            if (is_string($savedTopics) && is_array(json_decode($savedTopics, true))) {
                $topicIds = json_decode($savedTopics, true);
            } else {
                $topicIds = [$savedTopics];
            }
        }

        if (empty($topicIds)) {
            return response()->json(['success' => false, 'message' => 'Topik tidak ditemukan.'], 404);
        }

        // Validasi keamanan guru
        $topic = Topic::with('subject')->whereIn('id', $topicIds)->first();
        if (!$topic || !$topic->subject) {
            return response()->json(['success' => false, 'message' => 'Topik/subject tidak ditemukan.'], 404);
        }

        $classId = $topic->subject->id_class;
        $idGuru = Auth::id();
        $isTeacherInClass = DB::table('teacher_classes')
            ->where('id_teacher', $idGuru)
            ->where('id_class', $classId)
            ->exists();

        if (!$isTeacherInClass) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke kelas/topik ini.'], 403);
        }

        $n = intval($request->jumlah);

        // AMBIL SOAL SECARA ACAK DARI SEMUA TOPIK TERKAIT (`whereIn`)
        $final = Question::whereIn('id_topic', $topicIds)
            ->inRandomOrder()
            ->take($n)
            ->get();

        //identitas soal
        return response()->json([
            'success' => true,
            'total' => $final->count(),
            'data' => $final->map(function ($q) {
                return [
                    'id' => $q->id,
                    'difficulty' => $q->difficulty,
                    'type' => $q->type,
                    'tags' => $q->tags, // <--- TAMBAHKAN INI
                    'text' => optional(json_decode($q->question))->text ?? '-'
                ];
            })->values()
        ]);
    }

    public function simpanAturSoal(Request $request, $idAktivitas)
    {
        $request->validate([
            'id_question' => 'nullable|array',
            'id_question.*' => 'integer',
            'jumlah' => 'nullable|integer|min:0'
        ]);

        $ids = $request->input('id_question', []);
        $jumlah = $request->input('jumlah', null);

        DB::beginTransaction();
        try {
            // Hapus dulu yang lama untuk aktivitas ini
            DB::table('activity_question')->where('id_activity', $idAktivitas)->delete();

            // Simpan yang baru (jika ada)
            if (!empty($ids)) {
                $insert = [];
                $now = now();
                foreach ($ids as $qid) {
                    $qid = intval($qid);
                    if ($qid <= 0)
                        continue;
                    $insert[] = [
                        'id_activity' => $idAktivitas,
                        'id_question' => $qid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if (!empty($insert)) {
                    DB::table('activity_question')->insert($insert);
                }
            }

            // Jika dikirim jumlah, update kolom jumlah_soal pada activities
            if (!is_null($jumlah)) {
                DB::table('activities')->where('id', $idAktivitas)->update([
                    'jumlah_soal' => intval($jumlah),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tersimpan', 'jumlah' => $jumlah]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('simpanAturSoal error: ' . $e->getMessage() . ' -- trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function tambahSoalManual(Request $req, $idAktivitas)
    {
        DB::table('activity_question')->insert([
            'id_activity' => $idAktivitas,
            'id_question' => $req->id_question,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function hapusSoalManual(Request $req, $idAktivitas)
    {
        DB::table('activity_question')
            ->where('id_activity', $idAktivitas)
            ->where('id_question', $req->id_question)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function getQuestion($id)
    {
        $q = Question::find($id);
        $qData = json_decode($q->question);

        return response()->json([
            'id' => $q->id,
            'difficulty' => $q->difficulty,
            'type' => $q->type,
            'tags' => $q->tags,
            'text' => $qData->text ?? '-',
        ]);
    }
    public function clearAll($id)
    {
        DB::table('activity_question')
            ->where('id_activity', $id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
?>