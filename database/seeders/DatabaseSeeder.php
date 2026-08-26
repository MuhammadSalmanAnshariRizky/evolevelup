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

        // Topik 1 (Asli)
        $topicInformatika = Topic::create([
            'title' => 'Kelola Data dengan Spreadsheet',
            'description' => 'Pengelolaan data menggunakan spreadsheet.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        // Topik 2
        $topicInformatika2 = Topic::create([
            'title' => 'IP Addressing dan Subnetting',
            'description' => 'Memahami konsep dasar pengalamatan IP, kelas jaringan, sistem bilangan biner, dan perhitungan subnetting.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        // Topik 3
        $topicInformatika3 = Topic::create([
            'title' => 'Pemrograman Web Dasar',
            'description' => 'Membangun antarmuka landing page dan halaman web interaktif menggunakan HTML, CSS, dan framework Bootstrap.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        // Topik 4
        $topicInformatika4 = Topic::create([
            'title' => 'Pengembangan Backend Web',
            'description' => 'Pengenalan bahasa pemrograman PHP dan integrasi framework untuk mengelola logika aplikasi dan database.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        // Topik 5
        $topicInformatika5 = Topic::create([
            'title' => 'Media Pembelajaran Interaktif',
            'description' => 'Merancang modul, kuis interaktif, dan dashboard aktivitas berbasis web untuk kebutuhan edukasi.',
            'id_subject' => $subjectInformatika->id,
            'created_by' => $guru1->id,
        ]);

        // Topik 6
        $topicInformatika6 = Topic::create([
            'title' => 'Pengolahan Citra Digital',
            'description' => 'Konsep dasar grafis, manipulasi piksel, dan pengenalan teknik image segmentation.',
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

        // ==========================================
        // TOPIK 2: IP Addressing dan Subnetting (id_topic: 2)
        // ==========================================

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Berapa panjang bit dari sebuah alamat IPv4?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '16 bit', 'url' => null]],
                ['b' => ['teks' => '32 bit', 'url' => null]],
                ['c' => ['teks' => '64 bit', 'url' => null]],
                ['d' => ['teks' => '128 bit', 'url' => null]],
                ['e' => ['teks' => '256 bit', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Subnet mask default untuk IP Address Kelas C adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '255.0.0.0', 'url' => null]],
                ['b' => ['teks' => '255.255.0.0', 'url' => null]],
                ['c' => ['teks' => '255.255.255.0', 'url' => null]],
                ['d' => ['teks' => '255.255.255.255', 'url' => null]],
                ['e' => ['teks' => '0.0.0.0', 'url' => null]],
            ]),
            'MC_answer' => 'c',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Alamat IP 127.0.0.1 biasanya digunakan untuk pengujian jaringan lokal dan dikenal dengan sebutan?', 'URL' => null]),
            'SA_answer' => json_encode(['localhost', 'loopback', 'loop back']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Berapa panjang bit dari alamat IPv6?', 'URL' => null]),
            'SA_answer' => json_encode(['128', '128 bit', '128bit']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Berapa jumlah host maksimal yang dapat digunakan pada jaringan dengan subnet mask /24?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '254', 'url' => null]],
                ['b' => ['teks' => '255', 'url' => null]],
                ['c' => ['teks' => '256', 'url' => null]],
                ['d' => ['teks' => '128', 'url' => null]],
                ['e' => ['teks' => '512', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Proses memecah jaringan besar menjadi beberapa jaringan kecil disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Routing', 'url' => null]],
                ['b' => ['teks' => 'Switching', 'url' => null]],
                ['c' => ['teks' => 'Subnetting', 'url' => null]],
                ['d' => ['teks' => 'Broadcasting', 'url' => null]],
                ['e' => ['teks' => 'Ping', 'url' => null]],
            ]),
            'MC_answer' => 'c',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Jika sebuah komputer memiliki IP 192.168.10.50/26, berapakah Network ID-nya?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '192.168.10.0', 'url' => null]],
                ['b' => ['teks' => '192.168.10.32', 'url' => null]],
                ['c' => ['teks' => '192.168.10.48', 'url' => null]],
                ['d' => ['teks' => '192.168.10.64', 'url' => null]],
                ['e' => ['teks' => '192.168.10.128', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Nilai subnet mask dari prefiks /26 adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '255.255.255.128', 'url' => null]],
                ['b' => ['teks' => '255.255.255.192', 'url' => null]],
                ['c' => ['teks' => '255.255.255.224', 'url' => null]],
                ['d' => ['teks' => '255.255.255.240', 'url' => null]],
                ['e' => ['teks' => '255.255.255.0', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Alamat IP yang digunakan untuk mengirim data ke seluruh host dalam sebuah jaringan (network) secara bersamaan disebut?', 'URL' => null]),
            'SA_answer' => json_encode(['broadcast', 'broadcast address', 'alamat broadcast']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '2',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Dalam sistem bilangan biner, angka desimal 255 ditulis menjadi?', 'URL' => null]),
            'SA_answer' => json_encode(['11111111']),
        ]);

        // ==========================================
        // TOPIK 3: Pemrograman Web Dasar (id_topic: 3)
        // ==========================================

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Kepanjangan dari HTML adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'HyperText Markup Language', 'url' => null]],
                ['b' => ['teks' => 'Hyperlinks and Text Markup Language', 'url' => null]],
                ['c' => ['teks' => 'Home Tool Markup Language', 'url' => null]],
                ['d' => ['teks' => 'Hyper Tool Markup Language', 'url' => null]],
                ['e' => ['teks' => 'HyperText Machine Language', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Tag HTML yang digunakan untuk membuat judul/heading berukuran paling besar adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '<h6>', 'url' => null]],
                ['b' => ['teks' => '<head>', 'url' => null]],
                ['c' => ['teks' => '<heading>', 'url' => null]],
                ['d' => ['teks' => '<h1>', 'url' => null]],
                ['e' => ['teks' => '<header>', 'url' => null]],
            ]),
            'MC_answer' => 'd',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Tag HTML apa yang digunakan untuk membuat tautan atau hyperlink?', 'URL' => null]),
            'SA_answer' => json_encode(['<a>', 'a']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Pada framework Bootstrap, sistem grid (Grid System) dibagi menjadi berapa kolom maksimal secara default?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '6 kolom', 'url' => null]],
                ['b' => ['teks' => '8 kolom', 'url' => null]],
                ['c' => ['teks' => '10 kolom', 'url' => null]],
                ['d' => ['teks' => '12 kolom', 'url' => null]],
                ['e' => ['teks' => '16 kolom', 'url' => null]],
            ]),
            'MC_answer' => 'd',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan tag HTML untuk membuat daftar/list secara berurutan (menggunakan angka)!', 'URL' => null]),
            'SA_answer' => json_encode(['<ol>', 'ol', 'ordered list']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Properti CSS apa yang digunakan untuk meratakan teks ke tengah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'text-align: center;', 'url' => null]],
                ['b' => ['teks' => 'align-items: center;', 'url' => null]],
                ['c' => ['teks' => 'justify-content: center;', 'url' => null]],
                ['d' => ['teks' => 'vertical-align: middle;', 'url' => null]],
                ['e' => ['teks' => 'margin: auto;', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Class bawaan dari Bootstrap yang digunakan untuk mendesain tombol/button standar adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['btn', '.btn']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Dalam CSS, apa perbedaan utama antara penggunaan atribut ID dan Class?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'ID dipanggil dengan tanda titik (.), sedangkan Class dengan tagar (#)', 'url' => null]],
                ['b' => ['teks' => 'ID hanya boleh digunakan satu kali dalam halaman, sedangkan Class boleh digunakan berkali-kali', 'url' => null]],
                ['c' => ['teks' => 'Class lebih spesifik (hierarchy tinggi) dibanding ID', 'url' => null]],
                ['d' => ['teks' => 'ID hanya untuk JavaScript, Class hanya untuk CSS', 'url' => null]],
                ['e' => ['teks' => 'Tidak ada perbedaan sama sekali', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Agar website menyesuaikan tampilan pada layar HP atau komputer, kita menggunakan fitur CSS yang bernama?', 'URL' => null]),
            'SA_answer' => json_encode(['media query', 'media queries', 'responsive', 'viewport']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '3',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Tag apa yang digunakan untuk menghubungkan file CSS eksternal ke dalam dokumen HTML?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '<style>', 'url' => null]],
                ['b' => ['teks' => '<script>', 'url' => null]],
                ['c' => ['teks' => '<css>', 'url' => null]],
                ['d' => ['teks' => '<link>', 'url' => null]],
                ['e' => ['teks' => '<meta>', 'url' => null]],
            ]),
            'MC_answer' => 'd',
        ]);

        // ==========================================
        // TOPIK 4: Pengembangan Backend Web (id_topic: 4)
        // ==========================================

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Bahasa pemrograman backend apa yang dieksekusi di sisi server?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'HTML', 'url' => null]],
                ['b' => ['teks' => 'CSS', 'url' => null]],
                ['c' => ['teks' => 'Bootstrap', 'url' => null]],
                ['d' => ['teks' => 'PHP', 'url' => null]],
                ['e' => ['teks' => 'XML', 'url' => null]],
            ]),
            'MC_answer' => 'd',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Dalam sintaks PHP, setiap variabel wajib diawali dengan simbol?', 'URL' => null]),
            'SA_answer' => json_encode(['$', 'dolar', 'dollar']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan salah satu software local server yang umum digunakan untuk menjalankan PHP di komputer lokal!', 'URL' => null]),
            'SA_answer' => json_encode(['xampp', 'wampp', 'laragon', 'mamp']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Perintah dasar SQL yang digunakan untuk mengambil atau membaca data dari database adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'INSERT', 'url' => null]],
                ['b' => ['teks' => 'UPDATE', 'url' => null]],
                ['c' => ['teks' => 'SELECT', 'url' => null]],
                ['d' => ['teks' => 'DELETE', 'url' => null]],
                ['e' => ['teks' => 'CREATE', 'url' => null]],
            ]),
            'MC_answer' => 'c',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Apa kelebihan menggunakan metode POST dibandingkan GET saat mengirim data form?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Data POST terlihat di URL', 'url' => null]],
                ['b' => ['teks' => 'Data POST lebih aman dan tidak muncul di URL', 'url' => null]],
                ['c' => ['teks' => 'Data POST lebih cepat dieksekusi server', 'url' => null]],
                ['d' => ['teks' => 'Metode POST hanya bisa teks, tidak bisa gambar', 'url' => null]],
                ['e' => ['teks' => 'Metode POST memerlukan koneksi internet lebih cepat', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Fungsi PHP untuk menghentikan eksekusi script ke baris selanjutnya dinamakan?', 'URL' => null]),
            'SA_answer' => json_encode(['die', 'die()', 'exit', 'exit()']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Untuk menghubungkan PHP ke database MySQL, fungsi bawaan (library) yang aman digunakan saat ini adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'mysql_connect', 'url' => null]],
                ['b' => ['teks' => 'mysqli_connect atau PDO', 'url' => null]],
                ['c' => ['teks' => 'odbc_connect', 'url' => null]],
                ['d' => ['teks' => 'db_connect', 'url' => null]],
                ['e' => ['teks' => 'sql_open', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Teknik paling umum untuk mencegah celah keamanan SQL Injection di PHP adalah dengan menggunakan?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Session', 'url' => null]],
                ['b' => ['teks' => 'Cookies', 'url' => null]],
                ['c' => ['teks' => 'Prepared Statements', 'url' => null]],
                ['d' => ['teks' => 'CSS Injection', 'url' => null]],
                ['e' => ['teks' => 'md5 encryption', 'url' => null]],
            ]),
            'MC_answer' => 'c',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan perintah PHP untuk memulai atau melanjutkan session!', 'URL' => null]),
            'SA_answer' => json_encode(['session_start()', 'session_start']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '4',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan salah satu framework PHP yang populer digunakan!', 'URL' => null]),
            'SA_answer' => json_encode(['laravel', 'codeigniter', 'symfony', 'yii']),
        ]);

        // ==========================================
        // TOPIK 5: Media Pembelajaran Interaktif (id_topic: 5)
        // ==========================================

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Tujuan utama media pembelajaran interaktif dibanding buku teks biasa adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Meningkatkan partisipasi dan keterlibatan (engagement) siswa', 'url' => null]],
                ['b' => ['teks' => 'Memperbesar ukuran file belajar', 'url' => null]],
                ['c' => ['teks' => 'Mengurangi kebutuhan guru sama sekali', 'url' => null]],
                ['d' => ['teks' => 'Membutuhkan biaya server yang mahal', 'url' => null]],
                ['e' => ['teks' => 'Menghindari penggunaan internet', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Istilah LMS dalam media pembelajaran berbasis web kepanjangannya adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Learning Machine System', 'url' => null]],
                ['b' => ['teks' => 'Learning Management System', 'url' => null]],
                ['c' => ['teks' => 'Logical Management System', 'url' => null]],
                ['d' => ['teks' => 'Learning Media System', 'url' => null]],
                ['e' => ['teks' => 'Local Management Server', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan salah satu contoh aplikasi kuis interaktif yang populer di kelas!', 'URL' => null]),
            'SA_answer' => json_encode(['kahoot', 'quizizz', 'mentimeter', 'quizlet']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Penerapan elemen-elemen permainan (seperti poin, badge, leaderboard) pada media pembelajaran disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Evaluasi', 'url' => null]],
                ['b' => ['teks' => 'Gamifikasi', 'url' => null]],
                ['c' => ['teks' => 'Navigasi', 'url' => null]],
                ['d' => ['teks' => 'Simulasi', 'url' => null]],
                ['e' => ['teks' => 'Virtual Reality', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Tampilan rangkuman progres aktivitas belajar siswa pada aplikasi e-learning dinamakan apa?', 'URL' => null]),
            'SA_answer' => json_encode(['dashboard', 'beranda', 'panel kontrol']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Fitur LMS yang memberitahukan benar atau salah sesaat setelah menjawab soal disebut?', 'URL' => null]),
            'SA_answer' => json_encode(['feedback langsung', 'immediate feedback', 'umpan balik']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Model pembelajaran e-learning di mana siswa bebas mengakses modul dan kuis secara mandiri tanpa terikat waktu disebut metode?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Synchronous', 'url' => null]],
                ['b' => ['teks' => 'Asynchronous', 'url' => null]],
                ['c' => ['teks' => 'Blended Learning', 'url' => null]],
                ['d' => ['teks' => 'Face to face', 'url' => null]],
                ['e' => ['teks' => 'Hybrid', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Standar referensi teknis yang paling banyak digunakan agar materi e-learning kompatibel dengan berbagai LMS adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'SCORM', 'url' => null]],
                ['b' => ['teks' => 'HTML5', 'url' => null]],
                ['c' => ['teks' => 'PDF', 'url' => null]],
                ['d' => ['teks' => 'API', 'url' => null]],
                ['e' => ['teks' => 'JSON', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Singkatan dari pengalaman pengguna saat berinteraksi dengan media (mudah, nyaman, intuitif) adalah?', 'URL' => null]),
            'SA_answer' => json_encode(['ux', 'user experience']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '5',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Prinsip Multimedia Learning menyatakan bahwa belajar akan lebih optimal jika menggunakan?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Teks yang panjang saja', 'url' => null]],
                ['b' => ['teks' => 'Gambar/animasi yang dipadukan dengan kata-kata', 'url' => null]],
                ['c' => ['teks' => 'Hanya suara tanpa gambar', 'url' => null]],
                ['d' => ['teks' => 'Soal latihan yang banyak tanpa materi', 'url' => null]],
                ['e' => ['teks' => 'Buku tebal', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        // ==========================================
        // TOPIK 6: Pengolahan Citra Digital (id_topic: 6)
        // ==========================================

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Elemen terkecil pembentuk sebuah gambar digital (citra bitmap) disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Vektor', 'url' => null]],
                ['b' => ['teks' => 'Piksel', 'url' => null]],
                ['c' => ['teks' => 'Resolusi', 'url' => null]],
                ['d' => ['teks' => 'Warna', 'url' => null]],
                ['e' => ['teks' => 'Titik', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sistem pewarnaan dasar yang umum digunakan pada layar monitor adalah?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'CMYK', 'url' => null]],
                ['b' => ['teks' => 'RGB', 'url' => null]],
                ['c' => ['teks' => 'Grayscale', 'url' => null]],
                ['d' => ['teks' => 'HSV', 'url' => null]],
                ['e' => ['teks' => 'Hitam Putih', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'ShortAnswer',
            'difficulty' => 'mudah',
            'delta' => -1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan ekstensi file gambar yang mendukung latar belakang transparan!', 'URL' => null]),
            'SA_answer' => json_encode(['png', '.png', 'gif', '.gif']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'ShortAnswer',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Istilah untuk kerapatan piksel dalam sebuah citra (panjang x lebar) disebut?', 'URL' => null]),
            'SA_answer' => json_encode(['resolusi', 'resolution']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Apa tujuan utama dari Image Segmentation (Segmentasi Citra)?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Memberi warna pada gambar hitam putih', 'url' => null]],
                ['b' => ['teks' => 'Memisahkan objek (foreground) dari latar belakangnya (background)', 'url' => null]],
                ['c' => ['teks' => 'Mengubah gambar menjadi video', 'url' => null]],
                ['d' => ['teks' => 'Memperbesar resolusi tanpa pecah', 'url' => null]],
                ['e' => ['teks' => 'Menggabungkan beberapa gambar', 'url' => null]],
            ]),
            'MC_answer' => 'b',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'sedang',
            'delta' => 0.0,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Gambar bertipe Grayscale (skala keabu-abuan) biasanya hanya memiliki berapa channel warna?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => '1 channel', 'url' => null]],
                ['b' => ['teks' => '2 channel', 'url' => null]],
                ['c' => ['teks' => '3 channel', 'url' => null]],
                ['d' => ['teks' => '4 channel', 'url' => null]],
                ['e' => ['teks' => '0 channel', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Jenis citra digital yang dibentuk menggunakan perhitungan rumus matematika (bukan titik piksel) sehingga tidak pecah saat di-zoom disebut?', 'URL' => null]),
            'SA_answer' => json_encode(['vektor', 'vector', 'citra vektor']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Metode paling sederhana untuk melakukan segmentasi citra (mengubah citra menjadi biner hitam/putih murni) dinamakan?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Thresholding', 'url' => null]],
                ['b' => ['teks' => 'Smoothing', 'url' => null]],
                ['c' => ['teks' => 'Blurring', 'url' => null]],
                ['d' => ['teks' => 'Sharpening', 'url' => null]],
                ['e' => ['teks' => 'Equalization', 'url' => null]],
            ]),
            'MC_answer' => 'a',
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'ShortAnswer',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Sebutkan salah satu contoh algoritma atau filter yang digunakan untuk deteksi tepi (Edge Detection) pada citra!', 'URL' => null]),
            'SA_answer' => json_encode(['sobel', 'canny', 'prewitt', 'roberts']),
        ]);

        $informatikaQuestions[] = Question::create([
            'id_topic' => '6',
            'type' => 'MultipleChoice',
            'difficulty' => 'sulit',
            'delta' => 1.5,
            'created_by' => $guru1->id,
            'question' => json_encode(['text' => 'Teknik untuk mengurangi bintik gangguan (noise) pada sebuah citra digital biasanya menggunakan proses yang disebut?', 'URL' => null]),
            'MC_option' => json_encode([
                ['a' => ['teks' => 'Spatial Filtering / Blurring', 'url' => null]],
                ['b' => ['teks' => 'Contrast Stretching', 'url' => null]],
                ['c' => ['teks' => 'Edge Detection', 'url' => null]],
                ['d' => ['teks' => 'Morphology', 'url' => null]],
                ['e' => ['teks' => 'Cropping', 'url' => null]],
            ]),
            'MC_answer' => 'a',
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