<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Question;
use App\Models\StudentClasses;
use App\Models\Subject;
use App\Models\TeacherClasses;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PMPLSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Akun Pengajar / Dosen ===
        $guru = User::firstOrCreate(
            ['email' => 'novan.saputra@ulm.ac.id'],
            [
                'id_other'      => '199311102020121008',
                'type_id_other' => 'NIP',
                'name'          => 'Novan Alkaf Bahraini Saputra, S.Kom., M.T.',
                'password'      => Hash::make('password'),
                'role'          => 'teacher',
            ]
        );

        // === 2. Data Kelas ===
        $kelas = Classes::firstOrCreate(
            ['name' => 'PMPL 2026'],
            [
                'description' => 'Kelas Penjaminan Mutu Perangkat Lunak',
                'level'       => 'PT',
                'grade'       => null,
                'semester'    => 'odd',
                'token'       => strtoupper(Str::random(8)),
                'created_by'  => $guru->id,
            ]
        );

        // === 3. Relasi Pengajar & Kelas (TeacherClasses) ===
        TeacherClasses::firstOrCreate([
            'id_teacher' => $guru->id,
            'id_class'   => $kelas->id,
        ]);

        // === 4. Data Mahasiswa (Gabungan NIM Ganjil dan Genap) ===
        $students = [
            // Ganjil
            ['nim' => '2410131110007', 'name' => 'MUHAMMAD MUBARAK RAMADHANI'],
            ['nim' => '2410131120001', 'name' => 'NAZWA KHAIRUNISA'],
            ['nim' => '2410131120003', 'name' => 'FATIMAH AZ ZAHRA'],
            ['nim' => '2410131120005', 'name' => 'PUTRI FATIMATUL AMALIA'],
            ['nim' => '2410131120009', 'name' => 'LUNA SHOFA AZZURA'],
            ['nim' => '2410131120011', 'name' => 'TYA DWILEONY'],
            ['nim' => '2410131210001', 'name' => 'MUHAMMAD ERPANSYAH'],
            ['nim' => '2410131210007', 'name' => 'MUHAMMAD FARIS'],
            ['nim' => '2410131210009', 'name' => 'MUHAMMAD INDRA'],
            ['nim' => '2410131210017', 'name' => 'RIDHO JULIANOR RIZKY'],
            ['nim' => '2410131210019', 'name' => 'AHMAD SAUQI'],
            ['nim' => '2410131210021', 'name' => 'RUDINI'],
            ['nim' => '2410131210025', 'name' => 'MUHAMMAD IQBAL RAMADHAN'],
            ['nim' => '2410131210027', 'name' => 'AHMAD RIZQY RAMADHAN'],
            ['nim' => '2410131210029', 'name' => 'MUHAMMAD NAOVAL AL FAJRI'],
            ['nim' => '2410131210035', 'name' => 'MUHAMMAD ALFRIA AFDHA'],
            ['nim' => '2410131210037', 'name' => 'ADRIAN MAULANA YUSUF'],
            ['nim' => '2410131220015', 'name' => 'NURBAITI AQMALI'],
            ['nim' => '2410131220033', 'name' => 'NUR LAILY AZKIYA'],
            ['nim' => '2410131310001', 'name' => 'MUHAMMAD SHUFI RAMADHAN'],
            ['nim' => '2410131320015', 'name' => 'SHINTYA DWI PUTRI MAHENDRA'],
            // Genap
            ['nim' => '2310131210012', 'name' => 'DHIOU BUDI ALFARIZY'],
            ['nim' => '2410131110002', 'name' => 'MUHAMMAD SYAHBANA'],
            ['nim' => '2410131110006', 'name' => 'RAHMAD RIYANDI'],
            ['nim' => '2410131120004', 'name' => "ALIF'FA NUR ADYA"],
            ['nim' => '2410131120008', 'name' => 'YENI ARIANI'],
            ['nim' => '2410131120010', 'name' => 'RIZQA OKTAVIA RAMADHANI'],
            ['nim' => '2410131210002', 'name' => 'MOHAMMAD FAUZI RAHMAN'],
            ['nim' => '2410131210004', 'name' => 'M. RAFLY RAFIANDY'],
            ['nim' => '2410131210012', 'name' => 'MUHAMMAD JAINUDDIN'],
            ['nim' => '2410131210022', 'name' => "MUHAMMAD SYAFI'I BADALI"],
            ['nim' => '2410131210024', 'name' => 'RYAN CANDRA BACHTIAR'],
            ['nim' => '2410131210026', 'name' => 'MASKUARI'],
            ['nim' => '2410131210032', 'name' => 'AHMAD SHOGIE PRATAMA'],
            ['nim' => '2410131210034', 'name' => 'MUHAMMAD RAFLI'],
            ['nim' => '2410131220008', 'name' => 'RIZQA AULIA FAIHA'],
            ['nim' => '2410131220010', 'name' => 'ALYA NUR AINI'],
            ['nim' => '2410131220018', 'name' => 'RISKA AMELIA ZAKIAH'],
            ['nim' => '2410131220028', 'name' => 'NOVIYANTI AGUSTINA PUTRI'],
            ['nim' => '2410131310010', 'name' => 'DAUD AL-KHILAL'],
        ];

        foreach ($students as $student) {
            $siswa = User::firstOrCreate(
                ['id_other' => $student['nim']],
                [
                    'type_id_other' => 'NIM',
                    'name'          => $student['name'],
                    'email'         => $student['nim'] . '@mhs.ulm.ac.id',
                    'password'      => Hash::make($student['nim']),
                    'role'          => 'student',
                ]
            );

            StudentClasses::firstOrCreate([
                'id_student' => $siswa->id,
                'id_class'   => $kelas->id,
            ]);
        }

        // === 5. Data Mata Pelajaran (Subject) ===
        $subject = Subject::firstOrCreate(
            ['name' => 'Penjaminan Mutu Perangkat Lunak', 'id_class' => $kelas->id],
            ['created_by' => $guru->id]
        );

        // === 6. Data Topik (Topic) ===
        $topicsData = [
            'pengantar_pengujian' => [
                'title'       => 'Pengantar Pengujian Perangkat Lunak',
                'description' => 'Materi Bagian I: Prinsip dasar, QA vs QC, tingkat pengujian, dan cost of quality.',
            ],
            'pengujian_manual' => [
                'title'       => 'Pengujian Manual: Black-box dan White-box',
                'description' => 'Materi Bagian II: Equivalence Partitioning, BVA, Cyclomatic Complexity, dan Coverage.',
            ],
            'automation_testing' => [
                'title'       => 'Pengujian Otomatis / Automation Testing',
                'description' => 'Materi Bagian III: Tools otomasi (Selenium, Cypress, JMeter, Postman) dan strateginya.',
            ],
        ];

        $createdTopics = [];
        foreach ($topicsData as $key => $topic) {
            $t = Topic::create([
                'title'       => $topic['title'],
                'description' => $topic['description'],
                'id_subject'  => $subject->id,
                'created_by'  => $guru->id,
            ]);
            $createdTopics[$key] = $t->id;
        }

        // === 7. Data Soal (Question Seeder - 50 Butir Soal) ===
        $questions = [
            // --- Bagian I: Pengantar Pengujian Perangkat Lunak (Soal 1 - 10) ---
            'pengantar_pengujian' => [
                [
                    'text' => 'Menurut prinsip dasar pengujian perangkat lunak, pengujian bertujuan untuk....',
                    'options' => ['a' => 'membuktikan bahwa perangkat lunak 100% bebas dari kesalahan.', 'b' => 'menunjukkan keberadaan cacat (defects) pada perangkat lunak.', 'c' => 'menggantikan seluruh aktivitas pengembangan kode program.', 'd' => 'memastikan perangkat lunak tidak membutuhkan perbaikan lagi.', 'e' => 'menghilangkan kebutuhan dokumen spesifikasi kebutuhan.'],
                    'answer' => 'b', 'difficulty' => 'sangat mudah', 'delta' => -2.0,
                ],
                [
                    'text' => 'Pernyataan yang paling tepat mengenai perbedaan Quality Assurance (QA) dan Quality Control (QC) adalah....',
                    'options' => ['a' => 'QA berorientasi produk dan mendeteksi cacat, sedangkan QC berorientasi proses dan mencegah cacat.', 'b' => 'QA dan QC memiliki fokus, waktu pelaksanaan, dan pendekatan yang persis sama.', 'c' => 'QA hanya berlangsung pada fase coding, sedangkan QC berlangsung di seluruh siklus SDLC.', 'd' => 'QA berorientasi proses dan mencegah cacat di seluruh SDLC, sedangkan QC berorientasi produk dan mendeteksi cacat.', 'e' => 'QA dilakukan oleh pengguna akhir, sedangkan QC dilakukan oleh pimpinan perusahaan.'],
                    'answer' => 'd', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Urutan tingkat pengujian dari cakupan terkecil hingga cakupan terluas yang benar adalah....',
                    'options' => ['a' => 'Acceptance → System → Integration → Unit.', 'b' => 'System → Unit → Acceptance → Integration.', 'c' => 'Integration → Unit → System → Acceptance.', 'd' => 'Unit → System → Integration → Acceptance.', 'e' => 'Unit → Integration → System → Acceptance.'],
                    'answer' => 'e', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Seorang developer menguji fungsi hitungDiskon() secara terisolasi tanpa memanggil modul lain. Tingkat pengujian yang sedang dilakukan adalah....',
                    'options' => ['a' => 'Unit Testing', 'b' => 'Integration Testing', 'c' => 'System Testing', 'd' => 'Acceptance Testing', 'e' => 'Regression Testing'],
                    'answer' => 'a', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Tim A menemukan cacat logika saat unit test, sedangkan tim B menemukan cacat yang sama setelah aplikasi dirilis. Berdasarkan konsep cost of quality, pernyataan yang paling tepat adalah....',
                    'options' => ['a' => 'Biaya perbaikan kedua tim sama karena cacatnya identik.', 'b' => 'Biaya perbaikan lebih murah setelah rilis karena pengguna ikut melapor.', 'c' => 'Biaya perbaikan lebih murah saat unit test; semakin lambat cacat ditemukan, biaya semakin besar.', 'd' => 'Biaya perbaikan hanya ditentukan oleh jumlah baris kode yang diperbaiki.', 'e' => 'Biaya perbaikan tidak relevan karena pengujian tidak dapat menemukan cacat.'],
                    'answer' => 'c', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Seorang manajer meminta tim QA menguji seluruh kombinasi nilai input pada formulir dengan 10 kolom yang masing-masing menerima ribuan nilai. Prinsip pengujian yang paling relevan untuk menolak permintaan tersebut adalah....',
                    'options' => ['a' => 'Testing shows presence of defects.', 'b' => 'Exhaustive testing is impossible.', 'c' => 'Early testing saves time and money.', 'd' => 'Defects cluster together.', 'e' => 'Absence-of-errors fallacy.'],
                    'answer' => 'b', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Di antara aktivitas berikut, yang termasuk aktivitas validasi ("Apakah kita membangun produk yang benar?") adalah....',
                    'options' => ['a' => 'Peninjauan kode program oleh sesama developer.', 'b' => 'Walkthrough dokumen SRS bersama tim analis.', 'c' => 'Inspeksi dokumen desain arsitektur sistem.', 'd' => 'User Acceptance Testing oleh pengguna dengan menjalankan aplikasi.', 'e' => 'Analisis statis kode tanpa menjalankan program.'],
                    'answer' => 'd', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Seluruh unit test modul keranjang dan modul pembayaran lulus 100%. Namun saat keranjang memanggil API pembayaran, data pesanan gagal terkirim. Jenis pengujian yang terlewat dan paling tepat dilakukan adalah....',
                    'options' => ['a' => 'Integration Testing, karena memeriksa interaksi dan antarmuka antar modul/layanan.', 'b' => 'System Testing, karena memeriksa kode dalam fungsi secara terisolasi.', 'c' => 'Acceptance Testing, karena dilakukan oleh developer pada kode terkecil.', 'd' => 'Unit Testing tambahan, karena kegagalan terjadi di antar modul.', 'e' => 'Regression Testing, karena kegagalan hanya terjadi setelah rilis.'],
                    'answer' => 'a', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Hasil pengujian sebelum rilis: Requirement Pass Rate 96%, Branch Coverage 85%, Major Defect terbuka 2 (ada workaround), dan Critical Defect terbuka 1. Keputusan rilis yang paling tepat berdasarkan kriteria terminasi kuantitatif adalah....',
                    'options' => ['a' => 'GO, karena pass rate ≥ 95% dan branch coverage ≥ 80% sudah terpenuhi.', 'b' => 'PASS WITH RISK, karena Major Defect terbuka tidak lebih dari 2.', 'c' => 'GO, dengan mencatat Critical Defect sebagai residual risk.', 'd' => 'NO-GO, karena branch coverage 85% dinilai terlalu rendah.', 'e' => 'NO-GO, karena Critical Defect terbuka wajib 0 meskipun metrik lain terpenuhi.'],
                    'answer' => 'e', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah Sistem Informasi Rumah Sakit memiliki tiga modul: (1) algoritma perhitungan dosis obat, (2) formulir pendaftaran pasien, dan (3) alur pembayaran dengan aturan klaim asuransi. Rancangan strategi pengujian yang paling tepat dan beralasan adalah....',
                    'options' => ['a' => 'Black-box untuk ketiga modul agar cepat, dihentikan setelah seluruh test case dieksekusi.', 'b' => 'White-box untuk ketiga modul agar seluruh baris kode teruji, tanpa kriteria keluar.', 'c' => 'White-box (branch/path coverage) untuk dosis obat, Black-box (EP/BVA) untuk formulir, Decision Table dan Use Case untuk klaim, disertai exit criteria terukur.', 'd' => 'Black-box (error guessing) untuk dosis obat dan White-box untuk formulir pendaftaran, dihentikan saat waktu habis.', 'e' => 'Acceptance testing oleh pasien untuk ketiga modul, tanpa pengujian unit maupun integrasi.'],
                    'answer' => 'c', 'difficulty' => 'sangat sulit', 'delta' => 2.0,
                ],
            ],

            // --- Bagian II: Pengujian Manual (Black-box dan White-box) (Soal 11 - 30) ---
            'pengujian_manual' => [
                [
                    'text' => 'Pada black-box testing, penguji menguji perangkat lunak berdasarkan....',
                    'options' => ['a' => 'struktur internal dan alur kode program.', 'b' => 'jumlah baris kode yang telah ditulis.', 'c' => 'spesifikasi kebutuhan tanpa melihat kode internal.', 'd' => 'diagram alur kontrol (control flow graph).', 'e' => 'arsitektur basis data yang digunakan sistem.'],
                    'answer' => 'c', 'difficulty' => 'sangat mudah', 'delta' => -2.0,
                ],
                [
                    'text' => 'Tujuan utama teknik Equivalence Partitioning (EP) adalah....',
                    'options' => ['a' => 'membagi domain input menjadi kelas data yang diperlakukan sama sehingga jumlah test case berkurang tanpa menurunkan cakupan.', 'b' => 'menguji seluruh nilai pada domain input satu per satu agar tidak ada yang terlewat.', 'c' => 'menghitung kompleksitas logika suatu fungsi berdasarkan jumlah percabangan.', 'd' => 'memastikan setiap pernyataan dalam kode dieksekusi minimal satu kali.', 'e' => 'menebak letak cacat berdasarkan pengalaman dan intuisi penguji.'],
                    'answer' => 'a', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Metrik Statement Coverage mengukur....',
                    'options' => ['a' => 'jumlah cacat per ribu baris kode (KLOC).', 'b' => 'persentase cabang keputusan TRUE dan FALSE yang teruji.', 'c' => 'persentase test case yang berstatus lulus (pass).', 'd' => 'tingkat kompleksitas siklomatis suatu fungsi.', 'e' => 'persentase pernyataan kode yang berhasil dieksekusi oleh test case.'],
                    'answer' => 'e', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Boundary Value Analysis (BVA) dianggap efektif karena....',
                    'options' => ['a' => 'nilai ekstrem tidak pernah menyebabkan cacat pada sistem.', 'b' => 'cacat pemrograman paling sering muncul pada batas logika (operator <, <=, >, >=).', 'c' => 'BVA mengelompokkan input menjadi kelas valid dan invalid saja.', 'd' => 'BVA menggantikan kebutuhan pengujian unit oleh developer.', 'e' => 'BVA menjamin seluruh jalur kode telah dieksekusi.'],
                    'answer' => 'b', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Pernyataan yang benar mengenai white-box testing adalah....',
                    'options' => ['a' => 'Berpatokan hanya pada dokumen SRS dan user story.', 'b' => 'Dilakukan dari sudut pandang pengguna akhir tanpa akses kode.', 'c' => 'Mampu menemukan fitur yang hilang dari spesifikasi.', 'd' => 'Memerlukan akses source code dan dominan diterapkan pada unit dan integration testing.', 'e' => 'Tidak memerlukan kemampuan pemrograman sama sekali.'],
                    'answer' => 'd', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Pada Equivalence Partitioning untuk input usia yang valid pada rentang 18–65 tahun, kelas invalid yang harus diwakili oleh data uji adalah....',
                    'options' => ['a' => 'Hanya usia 18 dan usia 65.', 'b' => 'Hanya usia 35.', 'c' => 'Usia 1 sampai 17 saja.', 'd' => 'Usia di bawah 18 dan usia di atas 65.', 'e' => 'Usia tepat 17 dan tepat 66 saja.'],
                    'answer' => 'd', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Kolom nilai ujian menerima angka 0–100. Dengan teknik BVA 3-value, nilai uji untuk batas atas adalah....',
                    'options' => ['a' => '98, 99, 100', 'b' => '99, 100, 101', 'c' => '100, 101, 102', 'd' => '90, 100, 110', 'e' => '50, 99, 101'],
                    'answer' => 'b', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah fungsi memiliki 6 predicate node. Berdasarkan rumus McCabe V(G) = Predicate Nodes + 1, nilai Cyclomatic Complexity fungsi tersebut adalah....',
                    'options' => ['a' => '7', 'b' => '6', 'c' => '5', 'd' => '8', 'e' => '12'],
                    'answer' => 'a', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah fungsi memiliki 20 pernyataan. Kumpulan test case hanya mengeksekusi 15 pernyataan. Statement Coverage yang dicapai adalah....',
                    'options' => ['a' => '25%', 'b' => '50%', 'c' => '60%', 'd' => '70%', 'e' => '75%'],
                    'answer' => 'e', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Decision table kelayakan diskon hanya memuat tiga aturan dari dua kondisi Boolean: Aturan 1 (Member = True, Belanja > Rp500rb = True), Aturan 2 (True, False), dan Aturan 3 (False, True). Analisis yang paling tepat mengenai kelengkapan tabel tersebut adalah....',
                    'options' => ['a' => 'Tabel sudah lengkap karena setiap aturan menghasilkan aksi yang berbeda.', 'b' => 'Kombinasi (True, True) belum tercakup dan harus ditambahkan sebagai aturan keempat.', 'c' => 'Kombinasi (False, False) belum tercakup; aturan keempat perlu ditambahkan agar seluruh 2 × 2 = 4 kombinasi teruji.', 'd' => 'Kombinasi (True, False) belum tercakup karena Aturan 2 tidak menghasilkan diskon.', 'e' => 'Tabel tidak perlu dilengkapi karena kondisi Boolean hanya diuji pada nilai True.'],
                    'answer' => 'c', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Kartu ATM otomatis terblokir setelah tiga kali berturut-turut salah memasukkan PIN. Penguji ingin memverifikasi bahwa kartu masih aktif pada 2 kali salah, terblokir pada kesalahan ketiga, dan hitungan kesalahan kembali nol setelah PIN benar. Teknik black-box yang paling tepat adalah....',
                    'options' => ['a' => 'Equivalence Partitioning', 'b' => 'State Transition Testing', 'c' => 'Statement Coverage', 'd' => 'Boundary Value Analysis', 'e' => 'Path Coverage'],
                    'answer' => 'b', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Seorang penguji menekan tombol "Bayar" berkali-kali dengan cepat untuk melihat apakah saldo terpotong dua kali. Teknik pengujian yang digunakan adalah....',
                    'options' => ['a' => 'Equivalence Partitioning', 'b' => 'Boundary Value Analysis', 'c' => 'Decision Table', 'd' => 'Statement Coverage', 'e' => 'Error Guessing'],
                    'answer' => 'e', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Perhatikan potongan kode: if (A && B) { ... } else { ... }. Jumlah test case minimum untuk mencapai 100% Branch Coverage adalah....',
                    'options' => ['a' => '2', 'b' => '3', 'c' => '4', 'd' => '5', 'e' => '8'],
                    'answer' => 'a', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Modul perhitungan bunga pinjaman memiliki algoritma internal yang rumit dan banyak percabangan. Strategi pengujian yang paling tepat adalah....',
                    'options' => ['a' => 'Black-box dengan error guessing saja.', 'b' => 'Acceptance testing oleh pengguna saja.', 'c' => 'White-box dengan Branch dan Path Coverage.', 'd' => 'Black-box dengan equivalence partitioning saja.', 'e' => 'Pengujian tampilan antarmuka saja.'],
                    'answer' => 'c', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    // Soal dengan potongan kode yang di-format agar preserve newline dan indentasi
                    'text' => "Perhatikan kode berikut:\n\nif (a > 0) {\n    if (b > 0) { r = 1; } else { r = 2; }\n} else { r = 3; }\nwhile (c > 0) { c--; }\nif (r == 1) { log(); }\n\nNilai Cyclomatic Complexity V(G) dan jumlah jalur independen minimal fungsi tersebut adalah....",
                    'options' => ['a' => '2', 'b' => '3', 'c' => '4', 'd' => '5', 'e' => '6'],
                    'answer' => 'd', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    // Soal dengan potongan kode yang di-format menggunakan preserve newline
                    'text' => "Perhatikan fungsi calculateDiscount() berikut (nomor menunjukkan node CFG):\n\nif (totalAmount <= 0) {              // N1\n    return 0.0;                      // N2\n}\nif (isMember) {                      // N3\n    if (points > 100) {              // N4\n        return totalAmount * 0.20;   // N5\n    } else {\n        return totalAmount * 0.10;   // N6\n    }\n} else {\n    if (totalAmount >= 500000) {     // N7\n        return totalAmount * 0.05;   // N8\n    }\n}\nreturn 0.0;                          // N9, lalu N10 (Exit)\n\nTiga test case dijalankan: TC1 (totalAmount=0), TC2 (600000, member, points=150), TC3 (600000, bukan member). Statement Coverage (10 node) dan Branch Coverage (8 cabang) yang dicapai berturut-turut adalah....",
                    'options' => ['a' => '100% dan 100%', 'b' => '80% dan 80%', 'c' => '80% dan 75%', 'd' => '75% dan 80%', 'e' => '60% dan 50%'],
                    'answer' => 'c', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Form transfer menerima nominal minimal Rp10.000 dan maksimal Rp25.000.000. Kumpulan nilai uji BVA 3-value yang benar adalah....',
                    'options' => ['a' => '9.999; 10.000; 10.001; 24.999.999; 25.000.000; 25.000.001', 'b' => '10.000 dan 25.000.000 saja', 'c' => '0; 10.000; 25.000.000; 30.000.000', 'd' => '9.000; 10.000; 11.000; 24.000.000; 25.000.000; 26.000.000', 'e' => '10.001 dan 24.999.999 saja'],
                    'answer' => 'a', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    // Soal dengan potongan kode
                    'text' => "Fungsi berikut diuji dengan satu test case nilai = 70 dan mencapai Statement Coverage 100%, tetapi bug tetap lolos:\n\nString status = \"\";\nif (nilai >= 60) { status = \"LULUS\"; }\nreturn status;\n\nPenjelasan yang paling tepat adalah....",
                    'options' => ['a' => 'Statement Coverage 100% selalu menjamin seluruh cabang dan jalur teruji.', 'b' => 'Bug berasal dari tipe data String, bukan dari logika percabangan.', 'c' => 'Test case sudah cukup karena nilai 70 mewakili semua kemungkinan input.', 'd' => 'Statement Coverage tidak dapat dihitung pada kode yang memiliki if tanpa else.', 'e' => 'Cabang FALSE (nilai < 60) belum teruji; diperlukan Branch Coverage dengan nilai di bawah 60.'],
                    'answer' => 'e', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Seorang tester menyatakan, "Branch Coverage kami sudah 100%, jadi semua jalur kode pasti telah teruji." Evaluasi yang paling tepat terhadap pernyataan tersebut adalah....',
                    'options' => ['a' => 'Benar, karena Branch Coverage selalu setara dengan Path Coverage.', 'b' => 'Keliru, karena Branch Coverage mencakup Statement Coverage tetapi tidak menjamin semua kombinasi jalur teruji.', 'c' => 'Keliru, karena Branch Coverage lebih rendah tingkatannya dari Statement Coverage.', 'd' => 'Benar, karena setiap cabang TRUE dan FALSE hanya menghasilkan satu jalur.', 'e' => 'Keliru, karena Branch Coverage hanya berlaku untuk perulangan while.'],
                    'answer' => 'b', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Aplikasi persetujuan kredit menyetujui pengajuan jika usia 21–55 tahun dan penghasilan minimal Rp5.000.000. Rancangan test case yang paling efisien sekaligus komprehensif adalah....',
                    'options' => ['a' => 'Menguji seluruh usia 21–55 dikalikan seluruh nominal penghasilan yang mungkin.', 'b' => 'Menguji satu data valid untuk usia dan penghasilan, lalu menyimpulkan semua aturan benar.', 'c' => 'Membaca kode program dan menguji setiap pernyataan tanpa memperhatikan spesifikasi.', 'd' => 'BVA usia (20, 21, 55, 56) dan penghasilan (4.999.999 dan 5.000.000), lalu decision table untuk empat kombinasi kondisi usia dan penghasilan.', 'e' => 'Menebak kemungkinan cacat berdasarkan pengalaman tanpa teknik formal.'],
                    'answer' => 'd', 'difficulty' => 'sangat sulit', 'delta' => 2.0,
                ],
            ],

            // --- Bagian III: Pengujian Otomatis / Automation Testing (Soal 31 - 50) ---
            'automation_testing' => [
                [
                    'text' => 'Automation testing adalah....',
                    'options' => ['a' => 'eksekusi skenario uji menggunakan skrip atau tool otomatis untuk efisiensi dan akurasi.', 'b' => 'pengujian yang seluruhnya dilakukan secara manual oleh penguji.', 'c' => 'proses menulis kode produksi secara otomatis.', 'd' => 'pengujian yang hanya dilakukan oleh pengguna akhir.', 'e' => 'dokumentasi spesifikasi kebutuhan perangkat lunak.'],
                    'answer' => 'a', 'difficulty' => 'sangat mudah', 'delta' => -2.0,
                ],
                [
                    'text' => 'Pada alur kerja automation testing, tahap Assertion bertugas....',
                    'options' => ['a' => 'menemukan lokasi tombol atau kolom pada layar.', 'b' => 'mengetik data dan mengeklik tombol seperti pengguna asli.', 'c' => 'membandingkan hasil nyata dengan hasil yang diharapkan untuk menentukan PASS atau FAIL.', 'd' => 'memasang tool pengujian pada komputer penguji.', 'e' => 'menulis laporan akhir untuk manajemen.'],
                    'answer' => 'c', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Tool yang termasuk kategori pengujian performa/beban adalah....',
                    'options' => ['a' => 'Selenium', 'b' => 'Appium', 'c' => 'Cypress', 'd' => 'Apache JMeter', 'e' => 'Katalon Studio'],
                    'answer' => 'd', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Kasus yang paling tepat untuk diotomasi adalah....',
                    'options' => ['a' => 'Fitur yang tampilannya masih sering berubah setiap hari.', 'b' => 'Alur Login yang diulang pada setiap rilis dengan banyak variasi data.', 'c' => 'Penilaian estetika dan kenyamanan desain antarmuka.', 'd' => 'Pengujian eksploratori yang hanya dilakukan satu kali.', 'e' => 'Fitur yang belum memiliki spesifikasi kebutuhan.'],
                    'answer' => 'b', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Fitur Codegen pada Playwright berfungsi untuk....',
                    'options' => ['a' => 'mengukur beban server dengan ribuan virtual user.', 'b' => 'menjalankan unit test pada kode Java.', 'c' => 'memeriksa kompleksitas siklomatis kode.', 'd' => 'menguji aplikasi desktop berbasis Windows.', 'e' => 'merekam interaksi pengguna dan menghasilkan kode skrip secara otomatis.'],
                    'answer' => 'e', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Selenium WebDriver adalah tool open-source untuk mengotomasi pengujian pada....',
                    'options' => ['a' => 'beban server dengan ribuan virtual user.', 'b' => 'aplikasi mobile native saja.', 'c' => 'endpoint REST API tanpa antarmuka.', 'd' => 'performa jaringan dan latensi basis data.', 'e' => 'aplikasi web di berbagai browser melalui W3C WebDriver Protocol.'],
                    'answer' => 'e', 'difficulty' => 'mudah', 'delta' => -1.0,
                ],
                [
                    'text' => 'Tim membandingkan Cypress dengan Selenium untuk menguji aplikasi web. Perbedaan arsitektur yang paling menjelaskan mengapa Cypress umumnya lebih cepat dan lebih jarang flaky adalah....',
                    'options' => ['a' => 'Cypress berjalan langsung di dalam browser tanpa remote driver dan memiliki auto-wait, sedangkan Selenium berkomunikasi lewat WebDriver dan memerlukan explicit wait.', 'b' => 'Cypress berjalan di luar browser melalui remote driver, sedangkan Selenium berjalan langsung di dalam browser.', 'c' => 'Cypress hanya mengotomasi aplikasi mobile native, sedangkan Selenium hanya mengotomasi aplikasi web.', 'd' => 'Cypress menggunakan YAML deklaratif tanpa kode, sedangkan Selenium wajib memakai bahasa Groovy.', 'e' => 'Cypress mengharuskan sleep manual pada setiap langkah, sedangkan Selenium menunggu elemen secara otomatis.'],
                    'answer' => 'a', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Pada Postman, skrip pm.test("Status 200", () => pm.response.to.have.status(200)); berfungsi untuk....',
                    'options' => ['a' => 'mengirim ulang request sebanyak 200 kali.', 'b' => 'menyimpan token JWT ke variabel environment.', 'c' => 'memverifikasi bahwa status code respons API adalah 200.', 'd' => 'menunggu respons selama 200 milidetik.', 'e' => 'mengekspor collection ke file berformat JSON.'],
                    'answer' => 'c', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Pada JMeter, pengaturan 500 Thread dengan Ramp-Up 50 detik berarti....',
                    'options' => ['a' => 'server menerima tepat 500 request pada detik pertama.', 'b' => 'pengujian berhenti otomatis setelah 50 detik.', 'c' => '50 virtual user dijalankan sebanyak 500 kali.', 'd' => '500 virtual user dimulai secara bertahap selama 50 detik (sekitar 10 user per detik).', 'e' => '500 detik adalah batas waktu respons maksimum server.'],
                    'answer' => 'd', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Perintah untuk menjalankan collection Postman melalui CLI dengan file environment adalah....',
                    'options' => ['a' => 'npx cypress run --spec collection.json', 'b' => 'newman run collection.json -e env.json', 'c' => 'maestro test collection.json', 'd' => 'jmeter -n -t collection.json', 'e' => 'robot collection.json --env env.json'],
                    'answer' => 'b', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Tim backend Java ingin mengotomasi pengujian endpoint REST API dalam siklus build Maven menggunakan JUnit/TestNG dengan sintaks given().when().get().then(). Tool yang paling sesuai adalah....',
                    'options' => ['a' => 'Maestro', 'b' => 'Appium', 'c' => 'REST Assured', 'd' => 'Playwright Codegen', 'e' => 'Apache JMeter'],
                    'answer' => 'c', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Skrip uji web sering lulus pada sebagian besar eksekusi tetapi gagal secara acak karena elemen belum muncul saat skrip mengeklik (flaky test). Analisis penyebab dan solusi yang paling tepat adalah....',
                    'options' => ['a' => 'Terjadi race condition antara skrip dan proses render elemen; gunakan dynamic/explicit wait hingga elemen siap.', 'b' => 'Terjadi karena skrip terlalu pendek; tambahkan sleep tetap 60 detik pada setiap langkah.', 'c' => 'Terjadi karena assertion terlalu ketat; hapus seluruh assertion agar skrip tidak gagal.', 'd' => 'Terjadi karena skrip dijalankan berulang; jalankan sekali saja tanpa memverifikasi hasil.', 'e' => 'Terjadi karena mode headless; nonaktifkan mode headless pada seluruh eksekusi.'],
                    'answer' => 'a', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Seorang pengembang ingin menguji modul modal dialog secara terisolasi tanpa merender seluruh halaman aplikasi, sekaligus dapat menelusuri snapshot tiap langkah saat terjadi kegagalan. Evaluasi pendekatan yang paling tepat adalah....',
                    'options' => ['a' => 'Menggunakan JMeter dengan Thread Group agar komponen diuji beban.', 'b' => 'Menggunakan Postman karena modal dialog adalah endpoint API.', 'c' => 'Menggunakan E2E testing penuh karena komponen tidak dapat diuji secara terpisah.', 'd' => 'Menggunakan Appium Inspector untuk memeriksa ID elemen mobile.', 'e' => 'Menggunakan Cypress Component Testing, dengan fitur Time Travel untuk menelusuri snapshot langkah pengujian.'],
                    'answer' => 'e', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Pada struktur Given-When-Then di REST Assured, bagian When berisi....',
                    'options' => ['a' => 'konfigurasi header dan body request.', 'b' => 'assertion terhadap status code dan body respons.', 'c' => 'dependensi Maven yang dibutuhkan proyek.', 'd' => 'HTTP method yang dikirim, seperti GET atau POST.', 'e' => 'pemetaan JSON respons ke objek POJO.'],
                    'answer' => 'd', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Tim frontend mengembangkan SPA React. Skrip sering flaky karena elemen dirender secara asinkron, tim menguasai JavaScript, dan mereka membutuhkan debugging visual saat tes gagal. Tool yang paling sesuai adalah....',
                    'options' => ['a' => 'REST Assured', 'b' => 'Cypress', 'c' => 'Apache JMeter', 'd' => 'Appium', 'e' => 'Maestro'],
                    'answer' => 'b', 'difficulty' => 'sedang', 'delta' => 0.0,
                ],
                [
                    'text' => 'Hasil uji beban JMeter dengan 1.000 virtual user: Error Rate 3%, rata-rata Response Time 1,2 detik, 90th percentile 3,4 detik. Target lolos: Error Rate < 1% dan 90th percentile < 2 detik. Kesimpulan yang paling tepat adalah....',
                    'options' => ['a' => 'Lolos, karena rata-rata Response Time di bawah 2 detik.', 'b' => 'Lolos, karena 1.000 virtual user berhasil dijalankan.', 'c' => 'Lolos, karena Error Rate hanya 3% sehingga dapat ditoleransi.', 'd' => 'Tidak lolos, karena jumlah virtual user terlalu sedikit.', 'e' => 'Tidak lolos, karena Error Rate melebihi 1% dan 90th percentile melebihi 2 detik.'],
                    'answer' => 'e', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Pada Postman, request /login mengembalikan 200, tetapi request /profile selalu 401 Unauthorized. Diagnosis yang paling mungkin adalah....',
                    'options' => ['a' => 'token JWT dari /login tidak disimpan ke variabel dan tidak dipakai pada header Authorization di /profile (API chaining belum benar).', 'b' => 'Postman tidak mendukung pengujian endpoint yang membutuhkan autentikasi.', 'c' => 'status 401 menandakan server kehabisan memori.', 'd' => 'Newman wajib diinstal sebelum request dapat dikirim.', 'e' => 'Method request /profile harus diganti menjadi DELETE.'],
                    'answer' => 'a', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Urutan langkah evaluasi tool automation testing yang benar adalah....',
                    'options' => ['a' => 'Pilih tool, buat matriks skor, uji durasi setup, jalankan skrip dasar.', 'b' => 'Jalankan skrip dasar, pilih tool, buat matriks skor, uji durasi setup.', 'c' => 'Uji durasi setup, pilih tool, jalankan skrip dasar, buat matriks skor.', 'd' => 'Buat matriks skor kriteria, uji durasi setup, jalankan skrip dasar, pilih tool dengan skor tertinggi.', 'e' => 'Jalankan skrip dasar, buat matriks skor, pilih tool, uji durasi setup.'],
                    'answer' => 'd', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah tim berencana mengotomasi 100% pengujian, termasuk penilaian kenyamanan desain (UX) dan fitur yang tampilannya masih sering berubah. Evaluasi yang paling tepat terhadap rencana tersebut adalah....',
                    'options' => ['a' => 'Tepat, karena otomasi selalu lebih baik daripada pengujian manual.', 'b' => 'Tepat, karena robot dapat menilai estetika lebih objektif dari manusia.', 'c' => 'Kurang tepat; otomasi cocok untuk fitur stabil dan berulang, sedangkan UX dan UI yang labil sebaiknya diuji manual atau eksploratori.', 'd' => 'Kurang tepat karena otomasi hanya boleh dipakai pada pengujian performa.', 'e' => 'Tepat, selama seluruh skrip dibuat dengan fitur record and playback.'],
                    'answer' => 'c', 'difficulty' => 'sulit', 'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah aplikasi toko online memiliki web, aplikasi Android, REST API backend, dan akan menghadapi lonjakan trafik promosi. Strategi automation testing yang paling komprehensif dan beralasan adalah....',
                    'options' => ['a' => 'Hanya Selenium untuk semua lapisan, termasuk simulasi beban ribuan pengguna.', 'b' => 'Postman/Newman atau REST Assured untuk API di CI/CD setiap git push (shift-left), Cypress/Playwright untuk E2E web, Appium/Maestro untuk mobile, dan JMeter untuk uji beban, dengan pelaporan otomatis.', 'c' => 'Hanya JMeter untuk seluruh pengujian fungsional web dan mobile.', 'd' => 'Seluruh pengujian dilakukan manual agar tidak ada biaya perawatan skrip.', 'e' => 'Record and playback pada semua lapisan tanpa assertion agar skrip cepat dibuat.'],
                    'answer' => 'b', 'difficulty' => 'sangat sulit', 'delta' => 2.0,
                ],
            ],
        ];

        // === 8. Eksekusi Penyimpanan Soal ke Database ===
        foreach ($questions as $topicKey => $topicQuestions) {
            $topicId = $createdTopics[$topicKey];

            foreach ($topicQuestions as $q) {
                Question::create([
                    'id_topic'   => $topicId,
                    'type'       => 'MultipleChoice',
                    // json_encode ini akan mempertahankan \n dari struktur string.
                    'question'   => json_encode(['text' => $q['text'], 'URL' => null]),
                    'MC_option'  => json_encode([
                        ['a' => ['teks' => $q['options']['a'], 'url' => null]],
                        ['b' => ['teks' => $q['options']['b'], 'url' => null]],
                        ['c' => ['teks' => $q['options']['c'], 'url' => null]],
                        ['d' => ['teks' => $q['options']['d'], 'url' => null]],
                        ['e' => ['teks' => $q['options']['e'], 'url' => null]],
                    ]),
                    'MC_answer'  => $q['answer'],
                    'difficulty' => $q['difficulty'],
                    'delta'      => $q['delta'],
                    'created_by' => $guru->id,
                ]);
            }
        }
    }
}