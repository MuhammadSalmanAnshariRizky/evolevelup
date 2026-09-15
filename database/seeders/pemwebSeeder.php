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

class pemwebSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Akun Pengajar / Dosen ===
        $guru = User::create([
            'id_other'      => '199311102020121008',
            'type_id_other' => 'NIP',
            'name'          => 'Novan Alkaf Bahraini Saputra, S.Kom., M.T.',
            'email'         => 'novan.saputra@ulm.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'teacher',
        ]);

        // === 2. Data Kelas ===
        $kelasA1 = Classes::create([
            'name'        => 'Pemrograman Web A1 2026',
            'description' => 'Kelas Pemrograman Web A1 Tahun 2026',
            'level'       => 'PT',
            'grade'       => null,
            'semester'    => 'odd',
            'token'       => strtoupper(Str::random(8)),
            'created_by'  => $guru->id,
        ]);

        $kelasA2 = Classes::create([
            'name'        => 'Pemrograman Web A2 2026',
            'description' => 'Kelas Pemrograman Web A2 Tahun 2026',
            'level'       => 'PT',
            'grade'       => null,
            'semester'    => 'odd',
            'token'       => strtoupper(Str::random(8)),
            'created_by'  => $guru->id,
        ]);

        // === 3. Relasi Pengajar & Kelas (TeacherClasses) ===
        TeacherClasses::create([
            'id_teacher' => $guru->id,
            'id_class'   => $kelasA1->id,
        ]);

        TeacherClasses::create([
            'id_teacher' => $guru->id,
            'id_class'   => $kelasA2->id,
        ]);

        // === 4. Data Mahasiswa Kelas A1 ===
        $studentsA1 = [
            ['name' => 'MUHAMMAD KHALIQA', 'nim' => '2510131110003'],
            ['name' => 'ADINDA PASHA ABDUL AZIZ', 'nim' => '2510131110007'],
            ['name' => 'MUHAMMAD HELMY NADHIF', 'nim' => '2510131110011'],
            ['name' => 'RUBY MAULIDA', 'nim' => '2510131120001'],
            ['name' => 'SYIFA NURAINI', 'nim' => '2510131120005'],
            ['name' => 'GINA PUTRI RAMADHANI', 'nim' => '2510131120009'],
            ['name' => 'MUHAMMAD RIFKI MAULANA', 'nim' => '2510131210001'],
            ['name' => 'MUHAMMAD NABIL MAULANA', 'nim' => '2510131210009'],
            ['name' => 'RAFI NUR PRATAMA', 'nim' => '2510131210015'],
            ['name' => "A'AN FAHRAWIE", 'nim' => '2510131210017'],
            ['name' => 'MUHAMMAD AZRIEL YAHYA', 'nim' => '2510131210023'],
            ['name' => 'MUHAMMAD RAMADHANI', 'nim' => '2510131210025'],
            ['name' => 'KRISTOFORUS THEO GARARDO', 'nim' => '2510131210027'],
            ['name' => 'MUHAMMAD FADILLAH', 'nim' => '2510131210029'],
            ['name' => 'MUHAMMAD SUHAIMI', 'nim' => '2510131210033'],
            ['name' => 'ARRAHMAN HUDALLINNAS', 'nim' => '2510131210035'],
            ['name' => 'JECONIAH MARVA PELASULA', 'nim' => '2510131210037'],
            ['name' => 'RAHMA ANZALINA', 'nim' => '2510131220005'],
            ['name' => 'ARLYN FARAH DHILLA', 'nim' => '2510131220007'],
            ['name' => 'NOOR FADHILAH', 'nim' => '2510131220013'],
            ['name' => 'KHOLISHOTUL ILMIA', 'nim' => '2510131220019'],
            ['name' => 'NUR RAHMANIDZA AZZAHRA', 'nim' => '2510131220021'],
            ['name' => 'IRVA ROSYADA', 'nim' => '2510131220031'],
            ['name' => 'ERIK BASTIAN', 'nim' => '2510131310003'],
            ['name' => 'MUHAMMAD GHIFARI AZHARI', 'nim' => '2510131310005'],
            ['name' => 'MUHAMMAD ADITIYA MURSALIN', 'nim' => '2510131310007'],
            ['name' => 'SAID ALWI', 'nim' => '2510131310011'],
            ['name' => 'ALZIKRI RAMADAN', 'nim' => '2510131310015'],
            ['name' => 'MUHAMMAD AZKIA', 'nim' => '2510131310017'],
            ['name' => 'PUTRI SYIFA NABILAH', 'nim' => '2510131320001'],
            ['name' => 'NADYA ALIKA RISMAYA', 'nim' => '2510131320009'],
            ['name' => 'HALIMAH', 'nim' => '2510131320013'],
        ];

        foreach ($studentsA1 as $student) {
            $siswa = User::create([
                'id_other'      => $student['nim'],
                'type_id_other' => 'NIM',
                'name'          => $student['name'],
                'email'         => $student['nim'] . '@mhs.ulm.ac.id',
                'password'      => Hash::make($student['nim']),
                'role'          => 'student',
            ]);

            StudentClasses::create([
                'id_student' => $siswa->id,
                'id_class'   => $kelasA1->id,
            ]);
        }

        // === 5. Data Mahasiswa Kelas A2 ===
        $studentsA2 = [
            ['name' => 'MUHAMMAD FADHIL RAHMATILLAH', 'nim' => '2510131110002'],
            ['name' => 'MUHAMMAD PRISQI ADERIANA', 'nim' => '2510131110006'],
            ['name' => 'MUHAMMAD KHOIRIYANTO', 'nim' => '2510131110008'],
            ['name' => 'HERY AKMAL MUNTAZHAR', 'nim' => '2510131110010'],
            ['name' => 'MUHAMMAD ZULFAKHNUR', 'nim' => '2510131110012'],
            ['name' => 'WINDA RAFA', 'nim' => '2510131120004'],
            ['name' => 'NURUL MUAWWANAH KARATLAU', 'nim' => '2510131120014'],
            ['name' => 'MUHAMMAD MAULANA YUSUF', 'nim' => '2510131210010'],
            ['name' => 'NUR RIZQI HABIBI', 'nim' => '2510131210012'],
            ['name' => 'MUHAMMAD ABRAR RAMADHANI', 'nim' => '2510131210016'],
            ['name' => 'MUHAMMAD FAKHRUDIN FASIH', 'nim' => '2510131210018'],
            ['name' => 'RAICHA AZKA SANUBARI', 'nim' => '2510131210022'],
            ['name' => 'MUHAMMAD FIQRI FAHREZA', 'nim' => '2510131210024'],
            ['name' => 'MUHAMMAD NABIL RAMADHAN', 'nim' => '2510131210030'],
            ['name' => 'MUHAMMAD SYAFIQ MUBARAK', 'nim' => '2510131210034'],
            ['name' => 'MUHAMMAD DHAIFI HUDAIN', 'nim' => '2510131210036'],
            ['name' => 'ZAHRATUN NISA', 'nim' => '2510131220006'],
            ['name' => 'ADZRA DWI ANYQAH', 'nim' => '2510131220008'],
            ['name' => 'HAYATUN SHAUBAH', 'nim' => '2510131220014'],
            ['name' => 'MAULIDA', 'nim' => '2510131220020'],
            ['name' => 'AINUN ASTRID HANIFAH', 'nim' => '2510131220026'],
            ['name' => 'IRMAYA ELFA AINUNNISA', 'nim' => '2510131220028'],
            ['name' => 'DANIELA PUTRI DE LO VINA', 'nim' => '2510131220032'],
            ['name' => 'ANDI DWI SASTRO', 'nim' => '2510131310002'],
            ['name' => 'MUHAMMAD NOVAL REZALDY', 'nim' => '2510131310004'],
            ['name' => 'M. HABIBI MUTTAQIN', 'nim' => '2510131310006'],
            ['name' => 'MUHAMMAD FADILLAH', 'nim' => '2510131310010'],
            ['name' => 'AHMAD AFDHALI ZIKRI', 'nim' => '2510131310014'],
            ['name' => 'AKHMAD ABIZAR GHIFARI', 'nim' => '2510131310016'],
        ];

        foreach ($studentsA2 as $student) {
            $siswa = User::create([
                'id_other'      => $student['nim'],
                'type_id_other' => 'NIM',
                'name'          => $student['name'],
                'email'         => $student['nim'] . '@mhs.ulm.ac.id',
                'password'      => Hash::make($student['nim']),
                'role'          => 'student',
            ]);

            StudentClasses::create([
                'id_student' => $siswa->id,
                'id_class'   => $kelasA2->id,
            ]);
        }

        // === 6. Data Mata Pelajaran (Subject) ===
        $subjectA1 = Subject::create([
            'name'       => 'Pemrograman Web',
            'id_class'   => $kelasA1->id,
            'created_by' => $guru->id,
        ]);

        $subjectA2 = Subject::create([
            'name'       => 'Pemrograman Web',
            'id_class'   => $kelasA2->id,
            'created_by' => $guru->id,
        ]);

        // === 7. Data Topik (Topic) ===
        $topicsData = [
            'pengantar_web' => [
                'title'       => 'Pengantar Web',
                'description' => 'Materi CPMK 1: Menganalisis Konsep Dasar Internet, Arsitektur Web, dan HTTP.',
            ],
            'html' => [
                'title'       => 'HTML',
                'description' => 'Materi CPMK 2: Menulis Dokumen Halaman Web Menggunakan Elemen Semantik HTML5.',
            ],
            'css' => [
                'title'       => 'CSS',
                'description' => 'Materi CPMK 3: Menerapkan Styling dan Tata Letak Halaman Web Menggunakan CSS3 Dasar dan Framework CSS.',
            ],
        ];

        $createdTopics = [];
        foreach ([$subjectA1, $subjectA2] as $subject) {
            foreach ($topicsData as $key => $topic) {
                $t = Topic::create([
                    'title'       => $topic['title'],
                    'description' => $topic['description'],
                    'id_subject'  => $subject->id,
                    'created_by'  => $guru->id,
                ]);
                $createdTopics[$subject->id][$key] = $t->id;
            }
        }

        // === 8. Data Soal (Question Seeder - 50 Butir Soal) ===
        $questions = [
            // --- CPMK 1: Pengantar Web (Soal 1 - 13) ---
            'pengantar_web' => [
                [
                    'text' => 'Manakah port default yang digunakan oleh protokol HTTPS untuk komunikasi data terenkripsi?',
                    'options' => ['a' => '80', 'b' => '21', 'c' => '443', 'd' => '8080', 'e' => '500'],
                    'answer' => 'c',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Layanan arsitektur jaringan web yang berfungsi mengonversi alamat nama domain yang mudah diingat manusia (misalnya google.com) menjadi alamat IP numerik mesin adalah....',
                    'options' => ['a' => 'HTTP (Hypertext Transfer Protocol)', 'b' => 'DNS (Domain Name System)', 'c' => 'FTP (File Transfer Protocol)', 'd' => 'DHCP (Dynamic Host Configuration Protocol)', 'e' => 'URL (Uniform Resource Locator)'],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Pengembang ingin membuat formulir pendaftaran akun yang mengirimkan kata sandi pengguna ke server. Manakah pernyataan yang paling tepat mengenai pemilihan metode HTTP GET atau POST dalam skenario ini?',
                    'options' => [
                        'a' => 'Menggunakan GET karena proses pengiriman data URL lebih cepat sampai ke server.',
                        'b' => 'Menggunakan GET dan POST secara bersamaan agar server dapat memvalidasi dua kali.',
                        'c' => 'Menggunakan GET karena data enkripsi otomatis disembunyikan di header URL.',
                        'd' => 'Menggunakan POST karena hanya metode POST yang diizinkan untuk memproses input teks.',
                        'e' => 'Menggunakan POST karena data disisipkan dalam HTTP request body sehingga tidak terpapar di URL.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Di antara aplikasi berikut, manakah yang bertindak sebagai web client yang berfungsi meminta data halaman web ke server lalu menampilkan hasilnya kepada pengguna?',
                    'options' => ['a' => 'Google Chrome', 'b' => 'MySQL Database', 'c' => 'PHP Runtime', 'd' => 'Apache Web Server', 'e' => 'Laravel Framework'],
                    'answer' => 'a',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => "Protokol HTTP dirancang bersifat 'stateless'. Pemahaman yang benar mengenai karakteristik 'stateless' pada komunikasi web ini adalah.....",
                    'options' => [
                        'a' => 'Server menyimpan seluruh riwayat interaksi dan aktivitas klien secara otomatis tanpa batas waktu.',
                        'b' => 'Koneksi socket TCP antara browser dan server akan terus terbuka secara permanen tanpa pernah terputus.',
                        'c' => 'Browser klien dilarang mengirimkan permintaan berulang ke server yang sama sebelum sesi berakhir.',
                        'd' => 'Setiap permintaan (request) dari klien diperlakukan sebagai transaksi independen yang berdiri sendiri tanpa menyimpan status sesi secara default.',
                        'e' => 'Server web secara otomatis menolak permintaan data jika klien tidak memiliki alamat IP statis.'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Seorang pengembang web perlu mengirimkan token kredensial autentikasi (misalnya 'Bearer token_xyz') dari klien ke API server pada setiap request. Header HTTP manakah yang secara standar ditujukan untuk kebutuhan tersebut?",
                    'options' => ['a' => 'Content-Type', 'b' => 'User-Agent', 'c' => 'Accept-Language', 'd' => 'Authorization', 'e' => 'Cache-Control'],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah situs web mengganti alamat halamannya. Ketika pengguna mengakses alamat lama, server mengirimkan kode status HTTP 301. Apakah arti utama dari kode status HTTP 301 tersebut?',
                    'options' => [
                        'a' => 'Halaman telah dipindahkan ke alamat baru secara permanen (Moved Permanently).',
                        'b' => 'Halaman tidak ditemukan di server (Not Found).',
                        'c' => 'Halaman web mengalami kesalahan internal (Server Error).',
                        'd' => 'Pengguna dilarang mengakses halaman tersebut (Forbidden).',
                        'e' => 'Permintaan pengguna sedang diproses oleh server (Processing).'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Dalam sebuah implementasi autentikasi web berbasis Session, informasi apakah yang disimpan di dalam Cookie pada browser klien?',
                    'options' => [
                        'a' => 'Seluruh data profil pengguna termasuk kata sandi dalam format teks asli.',
                        'b' => 'Sertifikat TLS/SSL publik milik server web.',
                        'c' => 'Struktur skema tabel dan koneksi basis data server.',
                        'd' => 'Seluruh kode sumber skrip backend yang menjalankan fungsi autentikasi.',
                        'e' => 'String pengenal unik berupa Session ID yang merujuk pada data sesi di server.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Tim Berners-Lee menciptakan World Wide Web (WWW) pada tahun 1989 untuk mempermudah berbagi informasi. Manakah pernyataan yang paling tepat mengenai perbedaan konseptual antara istilah Internet dan World Wide Web (WWW)?',
                    'options' => [
                        'a' => 'Internet adalah tampilan visual grafis di browser, sedangkan WWW adalah kabel jaringan fisik yang menghubungkan antar komputer.',
                        'b' => 'WWW merupakan jaringan fisik lokal, sedangkan Internet adalah aplikasi perangkat lunak pembaca halaman web.',
                        'c' => 'Internet hanya digunakan untuk mengirim surat elektronik (email), sedangkan WWW khusus digunakan untuk mengunggah file basis data.',
                        'd' => 'Internet dan WWW adalah dua istilah yang memiliki makna teknis yang persis sama tanpa perbedaan fungsi.',
                        'e' => 'Internet adalah infrastruktur jaringan komputer global yang saling terhubung, sedangkan WWW adalah salah satu layanan informasi berbasis dokumen hypertext yang berjalan di atas jaringan internet.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pengguna mencoba mengakses halaman profil pada aplikasi web tanpa melakukan login terlebih dahulu. Server menolak permintaan tersebut dan mengembalikan HTTP Status Code yang menandakan bahwa pengguna belum memiliki hak akses/autentikasi untuk melihat halaman tersebut. Kode status HTTP manakah yang dikembalikan oleh server?',
                    'options' => ['a' => '200 OK', 'b' => '301 Moved Permanently', 'c' => '500 Internal Server Error', 'd' => '404 Not Found', 'e' => '401 Unauthorized'],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Saat aplikasi web mengambil data dari domain lain yang berbeda, browser terkadang memblokir akses tersebut karena aturan keamanan CORS. HTTP header apakah yang harus dikirimkan oleh server tujuan agar browser mengizinkan akses dari domain luar?',
                    'options' => ['a' => 'Content-Type', 'b' => 'Access-Control-Allow-Origin', 'c' => 'User-Agent', 'd' => 'Host', 'e' => 'Set-Cookie'],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Saat mengakses halaman HTTPS, browser menampilkan peringatan keamanan "SSL Certificate Mismatch". Apakah penyebab utama dari peringatan tersebut?',
                    'options' => [
                        'a' => 'Komputer pengguna tidak terhubung ke jaringan internet.',
                        'b' => 'Nama domain yang diakses tidak cocok dengan nama domain yang terdaftar pada sertifikat SSL.',
                        'c' => 'Server web belum memasang sistem basis data.',
                        'd' => 'Browser pengguna tidak mendukung bahasa pemrograman JavaScript.',
                        'e' => 'Port 80 pada server diblokir oleh ISP.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Tim pengembang web ingin mengoptimalkan performa pemuatan web berita yang memiliki puluhan aset kecil (CSS, JS, ikon) dengan bermigrasi dari HTTP/1.1 ke HTTP/2. Pada HTTP/1.1, terjadi masalah 'head-of-line blocking' karena browser harus membuka banyak koneksi TCP terpisah atau menunggu antrean response. Berdasarkan kasus ini, evaluasilah bagaimana arsitektur HTTP/2 mengatasi keterbatasan tersebut melalui fitur Multiplexing!",
                    'options' => [
                        'a' => 'HTTP/2 secara otomatis mengompresi seluruh file gambar raster menjadi format biner tanpa kehilangan kualitas.',
                        'b' => 'HTTP/2 menghilangkan kebutuhan proses autentikasi dan enkripsi TLS sehingga lalu lintas data menjadi lebih ringan.',
                        'c' => 'HTTP/2 mengubah seluruh sintaks dokumen HTML dan skrip JS menjadi bahasa rakitan di tingkat server.',
                        'd' => 'HTTP/2 mengizinkan pengiriman banyak permintaan dan tanggapan secara bersamaan berupa frame biner melalui satu koneksi TCP tunggal yang terjalin.',
                        'e' => 'HTTP/2 membagi server menjadi 10 server virtual terpisah untuk menangani masing-masing aset secara paralel.'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],

            // --- CPMK 2: HTML (Soal 14 - 31) ---
            'html' => [
                [
                    'text' => 'Tag semantik HTML5 yang khusus dirancang untuk mengelompokkan sekumpulan tautan atau menu navigasi utama situs web adalah.....',
                    'options' => ['a' => '<section>', 'b' => '<dir>', 'c' => '<header>', 'd' => '<aside>', 'e' => '<nav>'],
                    'answer' => 'e',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Dalam struktur halaman web HTML5, tag semantik yang digunakan untuk menandai bagian kaki dokumen (yang biasanya berisi hak cipta, peta situs, atau informasi kontak) adalah....',
                    'options' => ['a' => '<footer>', 'b' => '<bottom>', 'c' => '<section>', 'd' => '<end>', 'e' => '<aside>'],
                    'answer' => 'a',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => "Atribut 'type' yang paling tepat pada elemen <input> HTML5 untuk memunculkan antarmuka kalender interaktif (date picker) secara bawaan browser adalah....",
                    'options' => ['a' => 'type="text"', 'b' => 'type="calendar"', 'c' => 'type="date"', 'd' => 'type="time"', 'e' => 'type="datetime-local"'],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => "Fungsi utama dari penerapan atribut 'alt' pada tag elemen gambar <img> di HTML5 adalah....",
                    'options' => [
                        'a' => 'Menyediakan deskripsi teks alternatif jika file gambar gagal dimuat atau dibaca oleh peranti pembaca layar.',
                        'b' => 'Menampilkan teks judul petunjuk (tooltip) saat kursor mouse melayang di atas gambar.',
                        'c' => 'Mengatur resolusi tinggi asli dan tingkat kecerahan gambar secara otomatis.',
                        'd' => 'Mengubah format berkas gambar dari JPEG menjadi WebP secara dinamis.',
                        'e' => 'Memberikan bingkai border dekoratif di sekeliling elemen gambar.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Tag semantik HTML5 yang paling tepat digunakan untuk menandai bagian judul utama, logo, atau grup navigasi teratas dalam sebuah dokumen atau bagian halaman web adalah....',
                    'options' => ['a' => '<head>', 'b' => '<top>', 'c' => '<h1>', 'd' => '<header>', 'e' => '<section>'],
                    'answer' => 'd',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Seorang pengembang ingin membungkus sebuah konten artikel berita atau postingan blog independen yang dapat didistribusikan ulang secara mandiri. Elemen semantik HTML5 yang paling tepat untuk konteks ini adalah......',
                    'options' => ['a' => '<section>', 'b' => '<main>', 'c' => '<div>', 'd' => '<article>', 'e' => '<fieldset>'],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Perbedaan konseptual mendasar antara penggunaan elemen <div> dan elemen <section> dalam penulisan HTML5 yang benar adalah.....',
                    'options' => [
                        'a' => '<div> memiliki makna semantik tinggi, sedangkan <section> adalah pembungkus generik.',
                        'b' => '<div> digunakan khusus untuk gambar, sedangkan <section> khusus untuk teks.',
                        'c' => '<section> hanya boleh ditempatkan di dalam elemen <footer>, sedangkan <div> bebas.',
                        'd' => '<div> secara otomatis tebal (bold), sedangkan <section> miring (italic).',
                        'e' => '<div> merupakan pembungkus generik tanpa makna semantik, sedangkan <section> mewakili kelompok konten berdasar tema tertentu.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Tag HTML5 yang benar untuk menyematkan video dengan beberapa pilihan format file pendukung (fallback) di dalamnya adalah....',
                    'options' => [
                        'a' => '<video src="video.mp4" fallback="video.webm"></video>',
                        'b' => '<video> <source src="video.mp4" type="video/mp4"> <source src="video.webm" type="video/webm"></video>',
                        'c' => '<video file="video.mp4" type="mp4"> <alt file="video.webm"> </video>',
                        'd' => '<media type="video"> <file src="video.mp4"> <file src="video.webm"> </media>',
                        'e' => '<object video="video.mp4" alt="video.webm"> </object>'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Tag semantik <aside> paling tepat dan ideal digunakan dalam penulisan dokumen HTML5 untuk membungkus konten berupa....',
                    'options' => [
                        'a' => 'Konten pelengkap yang berkaitan tidak langsung dengan konten utama (seperti sidebar, iklan, atau daftar link terkait).',
                        'b' => 'Judul utama dan logo dari seluruh situs web.',
                        'c' => 'Tombol eksekusi utama pengiriman data formulir.',
                        'd' => 'Tabel data laporan keuangan utama perusahaan.',
                        'e' => 'Hak cipta dan informasi lisensi di bagian paling bawah halaman.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Atribut HTML5 yang paling tepat digunakan untuk memvalidasi kolom input agar wajib diisi dengan format pola tertentu (misalnya tepat 10 digit angka) tanpa skrip JavaScript adalah.....',
                    'options' => ['a' => 'required="numeric"', 'b' => 'validate="[0-9]{10}"', 'c' => 'maxlength="10"', 'd' => 'type="number"', 'e' => 'pattern="[0-9]{10}"'],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Atribut manakah yang digunakan pada elemen <label> untuk menghubungkannya secara langsung dengan atribut id milik elemen <input>?',
                    'options' => [
                        'a' => '<label>Nama Lengkap</label><input type="text" id="nama" name="nama" required>',
                        'b' => '<div class="label">Nama Lengkap</div><input type="text" id="nama" name="nama">',
                        'c' => '<label for="nama">Nama Lengkap</label><input type="text" id="nama" name="nama">',
                        'd' => '<label>Nama Lengkap</label><input type="text" name="label" required>',
                        'e' => '<label aria-label="nama">Nama Lengkap</label><input type="text" id="nama" name="nama">'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Perhatikan dua potongan kode HTML berikut:\nKode 1: <div class=\"berita\">...</div>\nKode 2: <section class=\"berita\">...</section>\nManakah penjelasan yang paling tepat mengenai perbedaan penggunaan elemen <div> dan <section> di atas?",
                    'options' => [
                        'a' => '<div> dan <section> memiliki makna yang sama persis dan tidak ada bedanya.',
                        'b' => '<div> adalah pembungkus umum tanpa makna semantik, sedangkan <section> menandai kelompok konten yang memiliki kesamaan tema.',
                        'c' => '<div> khusus untuk membungkus gambar, sedangkan <section> khusus untuk membungkus teks.',
                        'd' => '<div> otomatis membuat teks tebal, sedangkan <section> membuat teks miring.',
                        'e' => '<section> hanya boleh digunakan satu kali dalam satu dokumen HTML.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah tim pengembang web ingin meningkatkan kualitas SEO (Search Engine Optimization) dan aksesibilitas bagi pengguna penyandang disabilitas. Mereka sedang meninjau ulang penulisan struktur penjenjangan judul (heading) pada halaman artikel berita. Praktik hirarki penulisan heading yang paling tepat dan memenuhi standar kriteria web semantik adalah....',
                    'options' => [
                        'a' => 'Menggunakan tag <h1> secara berulang di setiap kalimat paragraf agar terindeks lebih cepat oleh mesin pencari.',
                        'b' => 'Menggunakan tag <h1> sebagai judul utama tunggal halaman, diikuti <h2> untuk sub-bagian, dan <h3> untuk anak sub-bagian tanpa melompati level hirarki.',
                        'c' => 'Menggunakan <h3> paling atas untuk judul utama jika ingin tampilan huruf berukuran kecil secara otomatis.',
                        'd' => 'Mengganti seluruh tag heading dengan <span class="title"> yang diatur gayanya menggunakan CSS.',
                        'e' => 'Menggunakan tag <h6> untuk judul halaman utama agar muat dalam satu baris tampilan mobile.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Seorang web developer senior sedang melakukan refactoring pada kode HTML legacy buatan tahun 2008 berikut:\n<div class=\"header\">\n  <div class=\"menu\">....</div>\n</div>\nManakah bentuk refactoring terbaik menggunakan elemen semantik murni HTML5 yang mempertahankan struktur hirarki tersebut?",
                    'options' => [
                        'a' => '<header><nav>....</nav></header>',
                        'b' => '<top><menu>....</menu></top>',
                        'c' => '<section class="header"><section class="menu">....</section></section>',
                        'd' => '<div type="header"><div type="nav">....</div></div>',
                        'e' => '<main><aside>....</aside></main>'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Elemen HTML5 yang digunakan untuk menampilkan gambar secara responsif (memilih gambar berbeda sesuai ukuran layar pengguna) adalah....',
                    'options' => [
                        'a' => '<img src="large.jpg" responsive="small.jpg">',
                        'b' => 'Elemen <picture> yang memuat beberapa tag <source media="..."> untuk kondisi layar dan satu tag fallback <img>.',
                        'c' => 'Tag <iframe> yang memanggil file HTML terpisah untuk masing-masing ukuran gambar.',
                        'd' => 'Tag <figure> dengan atribut media-query bawaan di dalamnya.',
                        'e' => 'Tag <canvas> yang menggambar ulang piksel gambar menggunakan script JavaScript.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Perhatikan kode formulir HTML5 berikut:\n<form action=\"/daftar\">\n  <label for=\"usia\">Usia:</label>\n  <input type=\"number\" id=\"usia\" name=\"usia\" min=\"17\" max=\"60\">\n  <button type=\"submit\">Daftar</button>\n</form>\nSaat pengguna membiarkan kolom usia kosong lalu menekan tombol \"Daftar\", formulir tetap berhasil terkirim ke server tanpa ada peringatan error. Analisislah penyebab utama masalah ini beserta solusinya!",
                    'options' => [
                        'a' => 'Nilai pada atribut min="17" terlalu kecil; mengubah nilainya menjadi min="18".',
                        'b' => 'Atribut min dan max tidak berfungsi pada type="number"; mengganti type="number" menjadi type="text".',
                        'c' => 'Atribut min dan max hanya memvalidasi angka jika kolom diisi; menambahkan atribut required pada elemen <input>.',
                        'd' => 'Elemen <button> tidak mendukung pengiriman formulir; menggantinya dengan tag <input type="button">.',
                        'e' => 'Atribut action="/daftar" salah; menghapus tanda garis miring (/).'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Perhatikan potongan kode formulir HTML5 berikut:\n<form action=\"/kirim\">\n  <label for=\"nama\">Nama:</label>\n  <input type=\"text\" id=\"nama\" name=\"nama\">\n  <input type=\"button\">Kirim Data</input>\n</form>\nSaat tombol \"Kirim Data\" diklik, formulir tidak mau mengirimkan data dan teks pada tombol tidak muncul dengan benar. Analisislah penyebab kesalahan kode di atas beserta solusinya!",
                    'options' => [
                        'a' => 'Elemen <label> harus diletakkan di bawah <input>; menukar posisi label dan input.',
                        'b' => 'Atribut action tidak boleh diawali dengan tanda garis miring /; menghapus tanda /',
                        'c' => 'Tag <input> adalah elemen tunggal (self-closing) yang tidak memiliki tag penutup </input>; menggunakan <input type="submit" value="Kirim Data">.',
                        'd' => 'Tipe input type="text" tidak mendukung pengiriman data formulir; mengganti type="text" menjadi type="email".',
                        'e' => 'Elemen <form> wajib menambahkan atribut id="form1"; menambahkan id pada tag <form>.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Pengembang web ingin membuat komponen kartu produk yang baru akan ditampilkan saat data berhasil dimuat. Pengembang memilih menggunakan elemen <template> dibanding menyembunyikannya menggunakan CSS (display: none). Evaluasilah mengapa penggunaan elemen <template> merupakan keputusan yang paling tepat untuk skenario tersebut!',
                    'options' => [
                        'a' => '<template> langsung menampilkan konten di layar namun transparansinya diubah menjadi transparan.',
                        'b' => '<template> dapat mengubah struktur data JSON menjadi bentuk tabel secara otomatis.',
                        'c' => '<template> menyimpan data formulir langsung ke server basis data saat halaman dimuat.',
                        'd' => '<template> secara otomatis menghapus file CSS dan mengeksekusi skrip di latar belakang.',
                        'e' => '<template> menyimpan konten pasif yang tidak di-render ke layar (DOM visual) sampai dipanggil oleh skrip.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],

            // --- CPMK 3: CSS (Soal 32 - 50) ---
            'css' => [
                [
                    'text' => 'Selector CSS standar yang digunakan khusus untuk memilih dan menerapkan gaya pada elemen HTML yang memiliki atribut id="utama" adalah.....',
                    'options' => ['a' => '#utama', 'b' => '.utama', 'c' => '*utama', 'd' => 'element(utama)', 'e' => 'id[utama]'],
                    'answer' => 'a',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Properti CSS yang digunakan untuk mengatur jarak atau area ruang antara konten dalam elemen dengan batas dalam (border) elemen itu sendiri adalah....',
                    'options' => ['a' => 'margin', 'b' => 'spacing', 'c' => 'padding', 'd' => 'outline', 'e' => 'gap'],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => "Sintaks penulisan elemen HTML yang benar untuk menghubungkan berkas stylesheet eksternal bernama 'style.css' ke dalam dokumen HTML adalah....",
                    'options' => [
                        'a' => '<script src="style.css">',
                        'b' => '<css href="style.css">',
                        'c' => '<style src="style.css">',
                        'd' => '<link rel="stylesheet" href="style.css">',
                        'e' => '<import style="style.css">'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => "Manakah efek visual dan tata letak (layout) yang terjadi pada elemen web saat diterapkan aturan CSS 'visibility: hidden;'?",
                    'options' => [
                        'a' => 'Elemen disembunyikan dari layar dan ruang fisik elemen dalam layout dihapus sepenuhnya.',
                        'b' => 'Elemen dipindahkan secara otomatis ke bagian paling bawah halaman web.',
                        'c' => 'Elemen dihapus secara permanen dari struktur DOM dokumen.',
                        'd' => 'Warna teks berubah menjadi transparan tetapi elemen tetap dapat diklik oleh kursor.',
                        'e' => 'Elemen disembunyikan dari visual tetapi ruang fisik tempat elemen berada tetap dipertahankan di layout.'
                    ],
                    'answer' => 'e',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Properti CSS3 yang digunakan untuk mengubah warna latar belakang (background) dari sebuah elemen HTML adalah....',
                    'options' => ['a' => 'color', 'b' => 'background-color', 'c' => 'border-color', 'd' => 'fill-color', 'e' => 'text-color'],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Urutan konseptual lapisan CSS Box Model dari urutan paling dalam (tempat konten berada) hingga ke urutan paling luar adalah.....',
                    'options' => [
                        'a' => 'Content -> Border -> Padding -> Margin',
                        'b' => 'Content -> Padding -> Border -> Margin',
                        'c' => 'Margin -> Border -> Padding -> Content',
                        'd' => 'Padding -> Content -> Margin -> Border',
                        'e' => 'Border -> Padding -> Content -> Margin'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Diberikan beberapa aturan CSS berikut yang menyasar elemen teks paragraf yang sama:\n1. p { color: red; }\n2. .teks { color: blue; }\n3. #judul { color: green; }\n4. div p { color: yellow; }\nManakah selector yang memiliki nilai spesifisitas (specificity) paling tinggi sehingga warnanya diprioritaskan oleh browser?",
                    'options' => [
                        'a' => 'p { color: red; }',
                        'b' => '.teks { color: blue; }',
                        'c' => '#judul { color: green; }',
                        'd' => 'div p { color: yellow; }',
                        'e' => 'Semua selector memiliki tingkat spesifisitas yang seimbang.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada modul CSS Flexbox, properti yang berfungsi untuk mengatur posisi perataan item-item anak (flex items) di sepanjang sumbu silang (cross-axis) kontainer adalah....',
                    'options' => ['a' => 'justify-content', 'b' => 'align-content', 'c' => 'flex-direction', 'd' => 'flex-wrap', 'e' => 'align-items'],
                    'answer' => 'e',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Aturan CSS Media Query yang paling tepat untuk menerapkan gaya desain khusus hanya pada perangkat yang memiliki lebar viewport maksimal 768px (seperti tampilan smartphone/tablet) adalah....',
                    'options' => [
                        'a' => '@media (min-width: 768px)',
                        'b' => '@media (screen-width: 768px)',
                        'c' => '@device (size: 768px)',
                        'd' => '@media screen and (max-width: 768px)',
                        'e' => '@responsive (mobile: 768px)'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Seorang pengembang web menerapkan gaya CSS berikut untuk sekelompok elemen tautan navigasi:\na {\n  color: blue;\n}\na:hover {\n  color: red;\n}\nFungsi dari pseudo-class :hover pada aturan CSS di atas adalah.....",
                    'options' => [
                        'a' => 'Mengubah warna tautan menjadi merah saat tautan tersebut telah pernah dikunjungi sebelumnya.',
                        'b' => 'Mengubah warna tautan menjadi merah saat kursor mouse diarahkan/melayang di atas elemen tautan.',
                        'c' => 'Mengubah warna tautan menjadi merah saat elemen tautan sedang diklik oleh pengguna.',
                        'd' => 'Mengubah warna tautan menjadi merah saat tautan mendapat fokus melalui tombol Tab keyboard.',
                        'e' => 'Mengubah warna tautan secara permanen setelah halaman selesai dimuat.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada modul tata letak CSS Grid, properti yang digunakan pada kontainer grid untuk menentukan jumlah kolom beserta ukuran masing-masing kolom adalah.....',
                    'options' => ['a' => 'grid-template-rows', 'b' => 'grid-gap', 'c' => 'grid-auto-flow', 'd' => 'grid-template-columns', 'e' => 'align-content'],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Seorang desainer ingin membuat bilah navigasi melayang (fixed navbar) di bagian atas layar. Namun saat halaman di-scroll, navbar ikut tergeser ke atas dan menghilang. Kode CSS yang ditulis adalah:\n.navbar {\n  position: absolute;\n  top: 0;\n  width: 100%;\n}\nBagaimanakah perbaikan properti CSS yang tepat agar navbar tetap menempel menetap di posisi atas jendela saat di-scroll?",
                    'options' => ['a' => 'position: relative;', 'b' => 'position: fixed;', 'c' => 'position: static;', 'd' => 'float: top;', 'e' => 'display: inline-block;'],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Seorang pengembang mencoba meratakan sebuah kotak card di tengah-tengah kontainer flex menggunakan kode CSS berikut:\n.container {\n  display: flex;\n  align-items: center; /* Sumbu silang */\n}\n/* Kotak card masih berada di sebelah kiri kontainer */\nProperti CSS apakah yang harus ditambahkan pada .container untuk meratakan card secara horizontal tepat di tengah sumbu utama (main axis)?",
                    'options' => ['a' => 'flex-direction: column;', 'b' => 'justify-content: center;', 'c' => 'align-content: space-between;', 'd' => 'margin-left: auto;', 'e' => 'text-align: center;'],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => "Seorang pengembang web menetapkan aturan CSS berikut pada sebuah elemen <div>:\nwidth: 200px;\npadding: 20px;\nborder: 5px solid black;\nbox-sizing: content-box;\nBerdasarkan perhitungan standar CSS Box Model, berapakah lebar total visual elemen tersebut saat di-render di layar browser?",
                    'options' => ['a' => '200px', 'b' => '240px', 'c' => '250px', 'd' => '225px', 'e' => '210px'],
                    'answer' => 'c',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Seorang pengembang web memiliki tiga aturan CSS berikut yang menyasar elemen tombol yang sama:\n/* Aturan 1 */\nbutton.btn-primary { color: white; }\n/* Aturan 2 */\n#submit-btn { color: yellow; }\n/* Aturan 3 */\nbutton { color: black; }\nJika elemen HTML ditulis sebagai <button id=\"submit-btn\" class=\"btn-primary\">Kirim</button>, warna teks manakah yang akan diterapkan oleh browser pada tombol tersebut dan mengapa?",
                    'options' => [
                        'a' => 'Warna black karena Aturan 3 ditulis paling akhir dalam dokumen CSS.',
                        'b' => 'Warna white karena kombinasi elemen dan class (button.btn-primary) memiliki spesifisitas paling tinggi.',
                        'c' => 'Warna yellow karena selector ID (#submit-btn) memiliki nilai spesifisitas lebih tinggi dibandingkan selector class atau elemen.',
                        'd' => 'Warna black karena tag button merupakan selector bawaan HTML yang diprioritaskan.',
                        'e' => 'Warna tidak berubah karena terjadi konflik nilai spesifisitas antar ketiga aturan.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Saat membangun antarmuka web yang kompleks, desainer sering kali menerapkan aturan CSS Reset secara global berikut:\n* { box-sizing: border-box; }\nAnalisis keunggulan teknis utama dari penerapan 'box-sizing: border-box' secara global dibandingkan nilai default 'content-box' adalah.....",
                    'options' => [
                        'a' => 'Padding dan border dihitung di dalam total ukuran width yang ditetapkan, sehingga lebar total elemen tidak melebihi spesifikasi.',
                        'b' => 'Elemen secara otomatis berubah transparan saat kursor melewatinya.',
                        'c' => 'Mempercepat kecepatan pengunduhan aset CSS dari server perantara.',
                        'd' => 'Menghapus seluruh efek animasi CSS yang berat secara otomatis.',
                        'e' => 'Mengubah seluruh elemen inline menjadi elemen bertipe block secara paksa.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Seorang pengembang menetapkan lebar kotak <div> sebesar 300px. Namun saat diberi padding: 20px dan border: 5px, total lebar fisik kotak di layar membengkak menjadi 350px sehingga merusak tata letak grid di sebelahnya. Kode CSS yang ditulis:\n.box {\n  width: 300px;\n  padding: 20px;\n  border: 5px solid black;\n  box-sizing: content-box;\n}\nBagaimanakah perbaikan aturan CSS agar total lebar fisik elemen tetap konsisten persis 300px tanpa mengurangi padding?",
                    'options' => ['a' => 'width: 250px;', 'b' => 'margin: -25px;', 'c' => 'display: inline-box;', 'd' => 'box-sizing: border-box;', 'e' => 'overflow: hidden;'],
                    'answer' => 'd',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Seorang pengembang web ingin menampilkan tiga kartu produk agar tersusun sejajar secara horizontal serta berada tepat di tengah kontainer, baik secara horizontal maupun vertikal. Namun, saat kode CSS berikut diterapkan, ketiga kartu produk tetap menumpuk rapat di sudut kiri atas kontainer dan sama sekali tidak berada di tengah:\n.container {\n  display: block;\n  justify-content: center;\n  align-items: center;\n}\nAnalisislah kesalahan pada kode CSS tersebut dan tentukan perbaikan yang paling tepat agar tata letak sesuai dengan kebutuhan!",
                    'options' => [
                        'a' => "Kesalahan terletak pada properti 'align-items'; perbaikannya adalah menggantinya dengan properti 'text-align: center'.",
                        'b' => "Kesalahan terletak pada properti 'justify-content'; perbaikannya adalah menghapus properti tersebut karena tidak diperlukan.",
                        'c' => "Kesalahan terletak pada urutan penulisan properti; perbaikannya adalah menuliskan 'align-items' sebelum 'justify-content'.",
                        'd' => "Kesalahan terletak pada nilai properti 'display: block', sebab properti 'justify-content' dan 'align-items' hanya berfungsi jika kontainer memiliki konteks pemformatan flex (atau grid); perbaikannya adalah mengubah nilai tersebut menjadi 'display: flex'.",
                        'e' => "Kesalahan terletak pada satuan ukuran kartu produk; perbaikannya adalah menambahkan 'width: 100%' pada setiap kartu produk."
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => "Sebuah komponen kartu menggunakan CSS Grid dengan deklarasi berikut:\n.grid-container {\n  display: grid;\n  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\n}\nNamun, pengembang secara keliru menuliskan sintaks minmax sebagai minmax(1fr, 200px), yang menyebabkan browser mengabaikan seluruh aturan grid (invalid property value). Bagaimanakah perbaikan kode fungsi minmax() yang benar agar layout grid responsif berfungsi kembali?",
                    'options' => [
                        'a' => 'grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));',
                        'b' => 'grid-template-columns: repeat(auto-fit, minmax(100%, 200px));',
                        'c' => 'grid-template-columns: flex(200px, 1fr);',
                        'd' => 'grid-template-columns: auto-fit(200px, 1fr);',
                        'e' => 'grid-template-columns: scale(200px, 1fr);'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],
        ];

        // Menyimpan data soal ke dalam database untuk masing-masing topik di setiap kelas
        foreach ($createdTopics as $subjectId => $topicMap) {
            foreach ($questions as $topicKey => $topicQuestions) {
                $topicId = $topicMap[$topicKey];

                foreach ($topicQuestions as $q) {
                    Question::create([
                        'id_topic'   => $topicId,
                        'type'       => 'MultipleChoice',
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
}