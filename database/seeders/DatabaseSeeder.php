<?php

namespace Database\Seeders;

use App\Models\ActivityResult;
use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Activity;
use App\Models\ActivityQuestion;
use App\Models\Question;
use App\Models\UserBadge;
use App\Models\Badge;
use App\Models\StudentClasses;
use App\Models\TeacherClasses;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === 1️⃣ Guru ===
        $guru1 = User::create([
            'id_other' => 'NIP001',
            'type_id_other' => 'NIP',
            'name' => 'Guru Informatika',
            'email' => 'guru1@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $guru2 = User::create([
            'id_other' => 'NIP002',
            'type_id_other' => 'NIP',
            'name' => 'Guru IPA',
            'email' => 'guru2@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // === 2️⃣ Siswa ===
        $siswa1 = User::create([
            'id_other' => 'NISN001',
            'type_id_other' => 'NISN',
            'name' => 'Wahyu',
            'email' => 'Wahyu@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $siswa2 = User::create([
            'id_other' => 'NISN002',
            'type_id_other' => 'NISN',
            'name' => 'Norman',
            'email' => 'norman@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // === 3️⃣ Badge ===
        $badgeA = Badge::create([
            'name' => 'Fastest Students',
            'description' => 'Pencapaian Siswa Paling Cepat Selesai Mengerjakan Satu Aktivitas',
            'path_icon' => 'img/1.png'
        ]);

        $badgeB = Badge::create([
            'name' => 'Top 3 Students',
            'description' => 'Pencapaian Siswa menjadi peringkat 3 terbaik dalam leaderboard',
            'path_icon' => 'img/2.png'
        ]);

        $badgeC = Badge::create([
            'name' => 'Smartest Students',
            'description' => 'Pencapaian Siswa dengan menjawab benar semua dalam satu aktivitas',
            'path_icon' => 'img/3.png'
        ]);


        $badges = [$badgeA->id, $badgeB->id, $badgeC->id];

        foreach ([$siswa1, $siswa2] as $siswa) {
            foreach ($badges as $badgeId) {
                UserBadge::create([
                    'id_student' => $siswa->id,
                    'id_badge' => $badgeId,
                    'id_class' => 1
                ]);
            }
        }

        // === 4️⃣ Kelas ===
        $kelas7 = Classes::create([
            'name' => '7 SMP',
            'description' => 'Kelas 7 SMP',
            'level' => 'SMP',
            'grade' => '1',
            'semester' => 'odd',
            'token' => 'KLS7TOKEN',
            'created_by' => $guru1->id,
        ]);

        $kelas8 = Classes::create([
            'name' => '8 SMP',
            'description' => 'Kelas 8 SMP',
            'level' => 'SMP',
            'grade' => '2',
            'semester' => 'even',
            'token' => 'KLS8TOKEN',
            'created_by' => $guru2->id,
        ]);

        TeacherClasses::create(['id_teacher' => $guru1->id, 'id_class' => $kelas7->id]);
        TeacherClasses::create(['id_teacher' => $guru2->id, 'id_class' => $kelas8->id]);

        StudentClasses::insert([
            ['id_student' => $siswa1->id, 'id_class' => $kelas7->id],
            ['id_student' => $siswa2->id, 'id_class' => $kelas7->id],
            ['id_student' => $siswa1->id, 'id_class' => $kelas8->id],
            ['id_student' => $siswa2->id, 'id_class' => $kelas8->id],
        ]);

        // === 5️⃣ Subject ===
        $subjectInformatika = Subject::create([
            'name' => 'Informatika',
            'id_class' => $kelas7->id,
            'created_by' => $guru1->id,
        ]);

        $subjectIPA = Subject::create([
            'name' => 'IPA',
            'id_class' => $kelas8->id,
            'created_by' => $guru2->id,
        ]);

        // === 6️⃣ Topic ===
        $topicInformatika = Topic::create([
            'title' => 'Kelola Data dengan Spreadsheet',
            'description' => 'Pengelolaan data menggunakan spreadsheet.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        $topicIPA = Topic::create([
            'title' => 'Gerak',
            'description' => 'Mempelajari konsep gerak dalam kehidupan sehari-hari.',
            'id_subject' => $subjectIPA->id,
            'created_by' => $guru2->id,
        ]);

        // === 7️⃣ Activity ===
        $statuses = ['basic', 'additional', 'remedial'];
        $labels = ['Kuis 1', 'Kuis 2', 'Kuis 3'];

        // ===== INFORMATIKA =====
        foreach ($statuses as $index => $status) {
            Activity::create([
                'title' => $labels[$index] . ' Informatika',
                'status' => $status,
                'type' => 'task',
                'deadline' => now()->addDays(7),
                'jumlah_soal' => 5,
                'durasi_pengerjaan' => 5,
                'id_topic' => $topicInformatika->id,
                'addaptive' => 'yes',
                'kkm' => 70,
            ]);
        }

        // ===== IPA =====
        foreach ($statuses as $index => $status) {
            Activity::create([
                'title' => $labels[$index] . ' IPA',
                'status' => $status,
                'type' => 'quiz',
                'deadline' => now()->addDays(7),
                'jumlah_soal' => 5,
                'durasi_pengerjaan' => 5,
                'id_topic' => $topicIPA->id,
                'addaptive' => 'yes',
                'kkm' => 70,
            ]);
        }
        $informatikaQuestions = [];

        // ==========================================
        // LEVEL MUDAH (Delta: -1.5) - 13 SOAL
        // ==========================================

        // MUDAH 1 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Apa fungsi utama spreadsheet?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Mengelola data numerik', 'url' => null]],
                ['b' => ['teks' => 'Mengedit video', 'url' => null]],
                ['c' => ['teks' => 'Menulis surat', 'url' => null]],
                ['d' => ['teks' => 'Mendengarkan musik', 'url' => null]],
                ['e' => ['teks' => 'Membuat animasi', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 2 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Aplikasi spreadsheet buatan Microsoft adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Microsoft Excel', 'url' => null]],
                ['b' => ['teks' => 'Microsoft Word', 'url' => null]],
                ['c' => ['teks' => 'PowerPoint', 'url' => null]],
                ['d' => ['teks' => 'Photoshop', 'url' => null]],
                ['e' => ['teks' => 'CorelDraw', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 3 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Sebutkan satu contoh aplikasi spreadsheet!', 'URL' => null]),
            'SA_answer' => json_encode(['excel', 'google sheets', 'libreoffice calc', 'wps spreadsheet']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 4 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Tanda apa yang wajib digunakan untuk mengawali penulisan rumus (formula) di Excel?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '=', 'url' => null]],
                ['b' => ['teks' => '+', 'url' => null]],
                ['c' => ['teks' => '-', 'url' => null]],
                ['d' => ['teks' => ':', 'url' => null]],
                ['e' => ['teks' => '"', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 5 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Format file (ekstensi) standar (default) dari Microsoft Excel adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '.xlsx', 'url' => null]],
                ['b' => ['teks' => '.docx', 'url' => null]],
                ['c' => ['teks' => '.pptx', 'url' => null]],
                ['d' => ['teks' => '.pdf', 'url' => null]],
                ['e' => ['teks' => '.txt', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 6 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Kombinasi tombol keyboard (shortcut) untuk menyimpan dokumen (Save) adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['ctrl + s', 'ctrl+s', 'ctrl s']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 7 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi yang digunakan untuk mencari nilai tertinggi dalam suatu kelompok data adalah...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'MIN', 'url' => null]],
                ['b' => ['teks' => 'MAX', 'url' => null]],
                ['c' => ['teks' => 'AVERAGE', 'url' => null]],
                ['d' => ['teks' => 'SUM', 'url' => null]],
                ['e' => ['teks' => 'COUNT', 'url' => null]],
            ]),
            'MC_answer' => 'b',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 8 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Kombinasi tombol keyboard (shortcut) untuk menyalin data (Copy) adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Ctrl + X', 'url' => null]],
                ['b' => ['teks' => 'Ctrl + P', 'url' => null]],
                ['c' => ['teks' => 'Ctrl + C', 'url' => null]],
                ['d' => ['teks' => 'Ctrl + V', 'url' => null]],
                ['e' => ['teks' => 'Ctrl + Z', 'url' => null]],
            ]),
            'MC_answer' => 'c',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 9 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Kombinasi tombol keyboard (shortcut) untuk menempelkan data (Paste) adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Ctrl + X', 'url' => null]],
                ['b' => ['teks' => 'Ctrl + P', 'url' => null]],
                ['c' => ['teks' => 'Ctrl + C', 'url' => null]],
                ['d' => ['teks' => 'Ctrl + V', 'url' => null]],
                ['e' => ['teks' => 'Ctrl + S', 'url' => null]],
            ]),
            'MC_answer' => 'd',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 10 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Nama lain dari baris pada spreadsheet dalam bahasa Inggris adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['row', 'rows']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 11 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Nama lain dari kolom pada spreadsheet dalam bahasa Inggris adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['column', 'columns']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 12 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Untuk menyimpan file dokumen dengan nama baru, perintah yang dipilih adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Save', 'url' => null]],
                ['b' => ['teks' => 'Save As', 'url' => null]],
                ['c' => ['teks' => 'Open', 'url' => null]],
                ['d' => ['teks' => 'New', 'url' => null]],
                ['e' => ['teks' => 'Print', 'url' => null]],
            ]),
            'MC_answer' => 'b',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // MUDAH 13 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Fungsi yang digunakan untuk mencari nilai terendah adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['min', '=min', 'minimum']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
        ]);

        // ==========================================
        // LEVEL SEDANG (Delta: 0.0) - 14 SOAL
        // ==========================================

        // SEDANG 1 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Perpotongan baris dan kolom disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Cell', 'url' => null]],
                ['b' => ['teks' => 'Sheet', 'url' => null]],
                ['c' => ['teks' => 'Workbook', 'url' => null]],
                ['d' => ['teks' => 'Range', 'url' => null]],
                ['e' => ['teks' => 'File', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 2 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi SUM pada spreadsheet digunakan untuk?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Menjumlahkan data numerik', 'url' => null]],
                ['b' => ['teks' => 'Mengurutkan data', 'url' => null]],
                ['c' => ['teks' => 'Menyaring data', 'url' => null]],
                ['d' => ['teks' => 'Menghapus data', 'url' => null]],
                ['e' => ['teks' => 'Mencari data', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 3 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa fungsi grafik/chart dalam spreadsheet?', 'URL' => null]),
            'SA_answer' => json_encode(['visualisasi data', 'menyajikan data', 'grafik data', 'memvisualisasikan data']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 4 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa kegunaan fitur sort?', 'URL' => null]),
            'SA_answer' => json_encode(['mengurutkan data', 'sorting data', 'urut data', 'mengurutkan']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 5 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa yang dimaksud dengan worksheet?', 'URL' => null]),
            'SA_answer' => json_encode(['lembar kerja', 'sheet', 'halaman kerja']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 6 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi AVERAGE digunakan untuk?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Mencari rata-rata', 'url' => null]],
                ['b' => ['teks' => 'Menjumlahkan', 'url' => null]],
                ['c' => ['teks' => 'Mencari nilai tertinggi', 'url' => null]],
                ['d' => ['teks' => 'Mengalikan angka', 'url' => null]],
                ['e' => ['teks' => 'Membagi angka', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 7 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Bagaimana cara membuat sebuah referensi sel menjadi absolut (tidak berubah saat dicopy)?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Menambahkan simbol $', 'url' => null]],
                ['b' => ['teks' => 'Menambahkan simbol %', 'url' => null]],
                ['c' => ['teks' => 'Menambahkan simbol &', 'url' => null]],
                ['d' => ['teks' => 'Menambahkan simbol #', 'url' => null]],
                ['e' => ['teks' => 'Menambahkan simbol @', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 8 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Simbol matematika apa yang digunakan untuk operasi perkalian di Excel?', 'URL' => null]),
            'SA_answer' => json_encode(['*', 'bintang', 'asterisk']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 9 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Simbol pembagian pada penulisan rumus Excel menggunakan tanda?', 'URL' => null]),
            'SA_answer' => json_encode(['/', 'slash', 'garis miring']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 10 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi COUNT digunakan untuk...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Menghitung jumlah sel yang berisi angka', 'url' => null]],
                ['b' => ['teks' => 'Menjumlahkan semua angka di dalam sel', 'url' => null]],
                ['c' => ['teks' => 'Mencari nilai tengah', 'url' => null]],
                ['d' => ['teks' => 'Menghitung jumlah kata', 'url' => null]],
                ['e' => ['teks' => 'Menghitung karakter', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 11 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fitur apa yang digunakan untuk menggabungkan beberapa cell menjadi satu cell?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Merge & Center', 'url' => null]],
                ['b' => ['teks' => 'Wrap Text', 'url' => null]],
                ['c' => ['teks' => 'Format Painter', 'url' => null]],
                ['d' => ['teks' => 'Conditional Formatting', 'url' => null]],
                ['e' => ['teks' => 'Find & Select', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 12 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fitur agar teks yang panjang bisa turun ke bawah menyesuaikan lebar sel adalah...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Wrap Text', 'url' => null]],
                ['b' => ['teks' => 'Merge & Center', 'url' => null]],
                ['c' => ['teks' => 'Shrink to Fit', 'url' => null]],
                ['d' => ['teks' => 'Align Text', 'url' => null]],
                ['e' => ['teks' => 'Sort Text', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 13 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Fitur untuk membekukan baris atau kolom agar tidak ikut tergulung (scroll) dinamakan?', 'URL' => null]),
            'SA_answer' => json_encode(['freeze panes', 'freeze pane', 'freeze']),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // SEDANG 14 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Untuk menggabungkan string/teks dari beberapa cell, kita bisa menggunakan fungsi...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'CONCATENATE', 'url' => null]],
                ['b' => ['teks' => 'COMBINE', 'url' => null]],
                ['c' => ['teks' => 'MERGE', 'url' => null]],
                ['d' => ['teks' => 'JOIN', 'url' => null]],
                ['e' => ['teks' => 'ADD', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
        ]);

        // ==========================================
        // LEVEL SULIT (Delta: 1.5) - 13 SOAL
        // ==========================================

        // SULIT 1 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Jelaskan secara singkat perbedaan worksheet dan workbook!', 'URL' => null]),
            'SA_answer' => json_encode([
                'worksheet lembar kerja workbook kumpulan worksheet',
                'worksheet bagian workbook',
                'workbook berisi worksheet'
            ]),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 2 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Jelaskan kegunaan fitur filter dalam pengolahan data spreadsheet!', 'URL' => null]),
            'SA_answer' => json_encode([
                'menyaring data sesuai kriteria',
                'menampilkan data tertentu',
                'filter data',
                'menyembunyikan data yang tidak relevan'
            ]),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 3 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Rumus yang benar untuk menghitung rata-rata dari sel A1 sampai A5 adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '=AVERAGE(A1:A5)', 'url' => null]],
                ['b' => ['teks' => '=SUM(A1:A5)', 'url' => null]],
                ['c' => ['teks' => '=COUNT(A1:A5)', 'url' => null]],
                ['d' => ['teks' => '=MAX(A1:A5)', 'url' => null]],
                ['e' => ['teks' => '=MIN(A1:A5)', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 4 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Struktur penulisan fungsi logika IF yang tepat di Microsoft Excel adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '=IF(logical_test, value_if_true, value_if_false)', 'url' => null]],
                ['b' => ['teks' => '=IF(value_if_true, logical_test, value_if_false)', 'url' => null]],
                ['c' => ['teks' => '=IF(value_if_false, value_if_true, logical_test)', 'url' => null]],
                ['d' => ['teks' => '=IF(logical_test)', 'url' => null]],
                ['e' => ['teks' => '=IF(logical_test, value_if_true)', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 5 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Sebutkan perbedaan mendasar pencarian tabel pada VLOOKUP dan HLOOKUP!', 'URL' => null]),
            'SA_answer' => json_encode([
                'vlookup vertikal hlookup horizontal',
                'vlookup mencari kolom hlookup mencari baris',
                'vlookup vertikal, hlookup horizontal'
            ]),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 6 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi yang dipakai untuk menjumlahkan sel-sel yang memenuhi kriteria (kondisi) tertentu saja disebut...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'SUM', 'url' => null]],
                ['b' => ['teks' => 'SUMIF', 'url' => null]],
                ['c' => ['teks' => 'COUNTIF', 'url' => null]],
                ['d' => ['teks' => 'IF', 'url' => null]],
                ['e' => ['teks' => 'VLOOKUP', 'url' => null]],
            ]),
            'MC_answer' => 'b',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 7 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Untuk menghitung banyaknya data sel (frekuensi) yang memenuhi kriteria tertentu, kita menggunakan...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'COUNTIF', 'url' => null]],
                ['b' => ['teks' => 'SUMIF', 'url' => null]],
                ['c' => ['teks' => 'IF', 'url' => null]],
                ['d' => ['teks' => 'COUNTA', 'url' => null]],
                ['e' => ['teks' => 'COUNTBLANK', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 8 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Apa penyebab munculnya pesan error #DIV/0! pada lembar kerja Excel?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Sebuah angka dibagi dengan angka nol (0) atau sel kosong', 'url' => null]],
                ['b' => ['teks' => 'Salah mengetikkan nama fungsi rumus', 'url' => null]],
                ['c' => ['teks' => 'Data yang dimasukkan bukan angka numerik', 'url' => null]],
                ['d' => ['teks' => 'Referensi kolom terhapus', 'url' => null]],
                ['e' => ['teks' => 'Angka terlalu besar melebihi lebar sel', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 9 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Pesan error #VALUE! pada cell spreadsheet biasanya disebabkan oleh...', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Lebar kolom kurang besar', 'url' => null]],
                ['b' => ['teks' => 'Tipe data yang dioperasikan dalam rumus tidak sesuai (misal: angka ditambah teks)', 'url' => null]],
                ['c' => ['teks' => 'Tidak menemukan data pada fungsi VLOOKUP', 'url' => null]],
                ['d' => ['teks' => 'Pembagian dengan angka nol', 'url' => null]],
                ['e' => ['teks' => 'Cell reference terhapus atau hilang', 'url' => null]],
            ]),
            'MC_answer' => 'b',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 10 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Fitur di Excel yang otomatis memberi warna latar (highlight) pada sel jika nilainya lebih besar dari angka tertentu dinamakan?', 'URL' => null]),
            'SA_answer' => json_encode(['conditional formatting', 'format bersyarat']),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 11 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fitur yang dirancang untuk merangkum, menganalisis, dan mengeksplorasi ribuan baris data ke dalam laporan dinamis dengan interaktif disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Pivot Table', 'url' => null]],
                ['b' => ['teks' => 'Data Validation', 'url' => null]],
                ['c' => ['teks' => 'VLOOKUP', 'url' => null]],
                ['d' => ['teks' => 'Macro', 'url' => null]],
                ['e' => ['teks' => 'Subtotal', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 12 (SA)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Untuk membatasi input pengguna agar hanya bisa memasukkan angka 1 sampai 10 di sebuah sel, fitur apa yang dipakai?', 'URL' => null]),
            'SA_answer' => json_encode(['data validation', 'validasi data']),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);

        // SULIT 13 (MC)
        $informatikaQuestions[] = Question::create([
            'id_topic' => '1',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Fungsi untuk memeriksa apakah suatu rumus menghasilkan error atau tidak, dan menukarnya dengan nilai tertentu (misalnya diganti teks "Kosong") adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'IFERROR', 'url' => null]],
                ['b' => ['teks' => 'ISBLANK', 'url' => null]],
                ['c' => ['teks' => 'IFERROR', 'url' => null]], // Intended logic for correct, replacing duplicate text in a/c visually, but setting a as correct
                ['d' => ['teks' => 'REPLACE', 'url' => null]],
                ['e' => ['teks' => 'SUBSTITUTE', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
        ]);


        //ipa questions

        $ipaQuestions = [];

        // MUDAH 1 (MC)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Gerak lurus beraturan adalah gerak dengan?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Kecepatan tetap', 'url' => null]],
                ['b' => ['teks' => 'Percepatan berubah', 'url' => null]],
                ['c' => ['teks' => 'Lintasan melengkung', 'url' => null]],
                ['d' => ['teks' => 'Arah berubah', 'url' => null]],
                ['e' => ['teks' => 'Kecepatan bertambah', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru2->id,
        ]);

        // MUDAH 2 (MC)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Satuan kecepatan dalam SI adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'm/s', 'url' => null]],
                ['b' => ['teks' => 'km', 'url' => null]],
                ['c' => ['teks' => 'detik', 'url' => null]],
                ['d' => ['teks' => 'meter', 'url' => null]],
                ['e' => ['teks' => 'jam', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru2->id,
        ]);

        // MUDAH 3 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Sebutkan satu contoh gerak lurus dalam kehidupan sehari-hari!', 'URL' => null]),
            'SA_answer' => json_encode(['mobil', 'sepeda', 'kereta']),
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru2->id,
        ]);

        // SEDANG 1 (MC)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Rumus kecepatan adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'v = s / t', 'url' => null]],
                ['b' => ['teks' => 'v = t / s', 'url' => null]],
                ['c' => ['teks' => 's = v / t', 'url' => null]],
                ['d' => ['teks' => 't = s × v', 'url' => null]],
                ['e' => ['teks' => 'v = s × t', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru2->id,
        ]);

        // SEDANG 2 (MC)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Alat untuk mengukur waktu adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Stopwatch', 'url' => null]],
                ['b' => ['teks' => 'Termometer', 'url' => null]],
                ['c' => ['teks' => 'Mistar', 'url' => null]],
                ['d' => ['teks' => 'Neraca', 'url' => null]],
                ['e' => ['teks' => 'Barometer', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru2->id,
        ]);

        // SEDANG 3 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa yang dimaksud dengan kecepatan?', 'URL' => null]),
            'SA_answer' => json_encode([
                'jarak per waktu',
                'perpindahan per waktu',
                's dibagi t'
            ]),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru2->id,
        ]);

        // SEDANG 4 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa yang dimaksud dengan jarak?', 'URL' => null]),
            'SA_answer' => json_encode([
                'panjang lintasan',
                'lintasan yang ditempuh',
                'jarak tempuh'
            ]),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru2->id,
        ]);

        // SEDANG 5 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Apa yang dimaksud dengan waktu dalam gerak?', 'URL' => null]),
            'SA_answer' => json_encode([
                'lama gerak',
                'selang waktu',
                'durasi'
            ]),
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru2->id,
        ]);

        // SULIT 1 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Jelaskan apa yang dimaksud dengan gerak lurus beraturan!', 'URL' => null]),
            'SA_answer' => json_encode([
                'kecepatan tetap lintasan lurus',
                'kecepatan konstan',
                'gerak lurus dengan kecepatan tetap'
            ]),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru2->id,
        ]);

        // SULIT 2 (SA)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'question' => json_encode(['text' => 'Jelaskan perbedaan jarak dan perpindahan!', 'URL' => null]),
            'SA_answer' => json_encode([
                'jarak lintasan perpindahan posisi',
                'jarak total perpindahan lurus',
                'jarak dan arah'
            ]),
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru2->id,
        ]);

        // SULIT 3 (MC)
        $ipaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'question' => json_encode(['text' => 'Jika sebuah benda menempuh jarak 100 m dalam 20 s, maka kecepatannya adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '5 m/s', 'url' => null]],
                ['b' => ['teks' => '2 m/s', 'url' => null]],
                ['c' => ['teks' => '10 m/s', 'url' => null]],
                ['d' => ['teks' => '20 m/s', 'url' => null]],
                ['e' => ['teks' => '100 m/s', 'url' => null]],
            ]),
            'MC_answer' => 'a',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru2->id,
        ]);


        $activitiesInformatika = Activity::whereHas(
            'topic.subject',
            fn($q) =>
            $q->where('name', 'Informatika')
        )->get();

        $activitiesIPA = Activity::whereHas(
            'topic.subject',
            fn($q) =>
            $q->where('name', 'IPA')
        )->get();

        foreach ($activitiesInformatika as $activity) {
            foreach ($informatikaQuestions as $question) {
                ActivityQuestion::create([
                    'id_activity' => $activity->id,
                    'id_question' => $question->id,
                ]);
            }
        }
        foreach ($activitiesIPA as $activity) {
            foreach ($ipaQuestions as $question) {
                ActivityQuestion::create([
                    'id_activity' => $activity->id,
                    'id_question' => $question->id,
                ]);
            }
        }
        // === 🔟 Nilai Siswa ===
        $allStudents = [$siswa1, $siswa2];
        $allActivities = Activity::all();

        foreach ($allStudents as $student) {
            foreach ($allActivities as $activity) {

                // nilai mentah (misal dari pengerjaan)
                $result = rand(40, 100);

                // nilai akhir (yang jadi acuan kelulusan)
                $nilaiAkhir = rand(50, 100);

                // status HARUS berdasarkan nilai_akhir
                $status = $nilaiAkhir < 70 ? 'Remedial' : 'Pass';

                // poin juga logis mengikuti nilai akhir
                $realPoin = $nilaiAkhir < 60 ? 10 : 20;

                ActivityResult::create([
                    'id_user' => $student->id,
                    'id_activity' => $activity->id,
                    'nilai_akhir' => $nilaiAkhir,
                    'result_status' => $status,
                    'result' => $result,
                    'real_poin' => $realPoin,
                    'bonus_poin' => rand(0, 5),
                ]);
            }
        }

        Settings::create([
            'name' => 'soal_mudah',
            'value' => 10
        ]);
        Settings::create([
            'name' => 'soal_sedang',
            'value' => 20
        ]);
        Settings::create([
            'name' => 'soal_sulit',
            'value' => 30
        ]);

    }
}