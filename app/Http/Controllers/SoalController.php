<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SoalController extends Controller
{
  public function showGenerator()
  {
    $guruId = Auth::id();

    // 🔹 Ambil TOPIC + LEVEL (jenjang) via JOIN
    $topics = DB::table('topics')
      ->join('subject', 'topics.id_subject', '=', 'subject.id')
      ->join('classes', 'subject.id_class', '=', 'classes.id')
      ->join('teacher_classes', 'classes.id', '=', 'teacher_classes.id_class')
      ->where('teacher_classes.id_teacher', $guruId)
      ->where('topics.created_by', $guruId)
      ->select(
        'topics.id',
        'topics.title',
        'classes.level as jenjang'
      )
      ->orderBy('topics.title')
      ->get();

    // 🔹 Ambil daftar jenjang dari kelas guru
    $jenjangList = DB::table('classes')
      ->join('teacher_classes', 'classes.id', '=', 'teacher_classes.id_class')
      ->where('teacher_classes.id_teacher', $guruId)
      ->select('classes.level')
      ->distinct()
      ->orderBy('classes.level')
      ->pluck('classes.level');

    return view('guru.generateSoal', compact('topics', 'jenjangList'));
  }

  public function generateAI(Request $request)
  {
    $request->validate([
      'topic' => 'required|integer|exists:topics,id',
      'jenjang' => 'nullable|string',
      'jumlah' => 'required|integer|min:1|max:10',
    ]);

    $topic = Topic::findOrFail($request->topic);
    $jenjangInfo = $request->jenjang ? " jenjang {$request->jenjang}" : "";

    $apiKey = config('services.groq.key');

    if (empty($apiKey)) {
      return response()->json([
        'success' => false,
        'message' => 'GROQ_API_KEY belum terpasang di config/services.php atau .env'
      ], 400);
    }

    // Prompt dilengkapi dengan struktur skema JSON eksplisit
    $prompt = <<<PROMPT
Anda adalah pembuat soal otomatis. Buatkan soal dan jawaban dalam bahasa Indonesia untuk topik "{$topic->title}"{$jenjangInfo} dengan aturan:
- Output WAJIB berupa JSON array murni tanpa pembungkus markdown (tanpa ```json) atau teks salam/tambahan apapun.
- Buatkan total {$request->jumlah} soal untuk SETIAP tingkat kesulitan: "mudah", "sedang", dan "sulit".
- untuk soal dengan type MultipleChoice hint adalah null, dan untuk ShortAnswer hint boleh diisi sesuai kebutuhan menyesuaikan petunjuk ke arah jawaban yang dimaksud.
- Setiap objek soal WAJIB memiliki struktur skema persis seperti contoh berikut:

[
  {
    "id_topic": {$topic->id},
    "difficulty": "mudah",
    "type": "MultipleChoice",
    "tags": "jaringan, ip address",
    "hint": null,
    "pertanyaan": {
      "text": "Apa kepanjangan dari IP?",
      "url": null
    },
    "MC_option": [
      {"a": {"teks": "Internet Protocol", "url": null}},
      {"b": {"teks": "Intranet Protocol", "url": null}},
      {"c": {"teks": "Interconnected Port", "url": null}},
      {"d": {"teks": "Information Process", "url": null}},
      {"e": {"teks": "Internal Program", "url": null}}
    ],
    "MC_Answer": "a"
  },
  {
    "id_topic": {$topic->id},
    "difficulty": "sedang",
    "type": "ShortAnswer",
    "tags": "subnetting, prefix",
    "hint": "Jawaban berupa angka diawali garis miring (contoh: /24)",
    "pertanyaan": {
      "text": "Berapa notasi prefix CIDR untuk subnet mask 255.255.255.0?",
      "url": null
    },
    "SA_option": ["/24", "24"]
  }
]
PROMPT;

    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
      ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'openai/gpt-oss-120b',
            'messages' => [
              ['role' => 'system', 'content' => 'Anda adalah pembuat soal otomatis yang merespon SELALU dalam format JSON array murni.'],
              ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
          ]);

      if ($response->successful()) {
        $rawText = $response->json('choices.0.message.content');

        // Ekstrak string JSON array murni dari respon AI menggunakan Regex
        if (preg_match('/\[.*\]/s', $rawText, $matches)) {
          $cleanJson = $matches[0];
        } else {
          $cleanJson = trim(preg_replace('/^```json|```$/m', '', $rawText));
        }

        $decodedData = json_decode($cleanJson, true);

        if (!is_array($decodedData)) {
          return response()->json([
            'success' => false,
            'message' => 'AI mengembalikan format JSON yang tidak valid.',
            'raw' => $rawText
          ], 422);
        }

        return response()->json([
          'success' => true,
          'data' => $decodedData
        ]);
      }

      return response()->json([
        'success' => false,
        'message' => 'Groq API Error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body())
      ], $response->status());

    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Controller Error: ' . $e->getMessage()
      ], 500);
    }
  }

  public function importQuestionJson(Request $request)
  {
    $json = null;

    // Memeriksa metode kirim: JSON array (langsung), teks paste, atau upload file
    if (is_array($request->json_data)) {
      $json = $request->json_data;
    } elseif ($request->filled('json_text')) {
      $json = json_decode($request->json_text, true);
    } elseif ($request->hasFile('file')) {
      $json = json_decode(file_get_contents($request->file('file')), true);
    }

    if (!is_array($json) || empty($json)) {
      return back()->with('error', 'Format JSON tidak valid atau data kosong.');
    }

    $importedCount = 0;

    try {
      DB::beginTransaction();

      foreach ($json as $item) {
        // Validasi minimal field pertanyaan
        if (!isset($item['pertanyaan'])) {
          continue;
        }

        // Normalisasi pertanyaan (jika AI mengembalikan string murni)
        $pertanyaan = is_array($item['pertanyaan'])
          ? $item['pertanyaan']
          : ['text' => (string) $item['pertanyaan'], 'url' => null];

        // Hitung Rasch Delta berdasarkan difficulty
        $difficulty = strtolower($item['difficulty'] ?? 'sedang');
        $delta = $difficulty === 'mudah' ? -1.50 : ($difficulty === 'sulit' ? 1.50 : 0.00);

        // Proses tags
        $tags = null;
        if (!empty($item['tags'])) {
          $tagsArr = is_array($item['tags']) ? $item['tags'] : explode(',', $item['tags']);
          $tags = json_encode(array_values(array_filter(array_map('trim', $tagsArr))));
        }

        DB::table('question')->insert([
          'id_topic' => $item['id_topic'] ?? $request->id_topic ?? 1,
          'type' => $item['type'] ?? 'MultipleChoice',
          'difficulty' => $difficulty,
          'delta' => $delta,
          'tags' => $tags,
          'hint' => $item['hint'] ?? null,
          'question' => json_encode($pertanyaan),
          'MC_option' => isset($item['MC_option']) ? json_encode($item['MC_option']) : null,
          'SA_answer' => isset($item['SA_option']) ? json_encode($item['SA_option']) : (isset($item['SA_answer']) ? json_encode($item['SA_answer']) : null),
          'MC_answer' => $item['MC_Answer'] ?? $item['MC_answer'] ?? null,
          'created_by' => Auth::id() ?? 1,
          'created_at' => now(),
          'updated_at' => now(),
        ]);

        $importedCount++;
      }

      DB::commit();

      if ($importedCount === 0) {
        return back()->with('error', 'Gagal menyimpan. Tidak ada struktur soal yang cocok dalam JSON.');
      }

      return back()->with('success', "Berhasil menyimpan {$importedCount} soal ke database!");

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'Gagal menyimpan ke database: ' . $e->getMessage());
    }
  }
}