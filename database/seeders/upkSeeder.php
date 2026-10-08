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

class upkSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Akun Pengajar / Dosen ===
        $guru = User::create([
            'id_other'      => '199001012022012001',
            'type_id_other' => 'NIP',
            'name'          => 'YULIDA KHAIRUNNISA',
            'email'         => 'Yulida.036@gmail.com',
            'password'      => Hash::make('password'),
            'role'          => 'teacher',
        ]);

        // === 2. Data Kelas ===
        $kelasSO = Classes::create([
            'name'        => 'Sistem Operasi 01 2026',
            'description' => 'Kelas Sistem Operasi 01 Tahun 2026',
            'level'       => 'PT',
            'grade'       => null,
            'semester'    => 'odd',
            'token'       => strtoupper(Str::random(8)),
            'created_by'  => $guru->id,
        ]);

        // === 3. Relasi Pengajar & Kelas (TeacherClasses) ===
        TeacherClasses::create([
            'id_teacher' => $guru->id,
            'id_class'   => $kelasSO->id,
        ]);

        // === 4. Data Mahasiswa Kelas Sistem Operasi ===
        $students = [
            ['name' => 'M. ALDY', 'nim' => '1342521006'],
            ['name' => 'HADRANI', 'nim' => '1342521010'],
            ['name' => 'ADITYA HERY SYAHBANA', 'nim' => '1342521011'],
            ['name' => 'NUR FAN RUSLI', 'nim' => '1342521014'],
            ['name' => 'RIZKA RAMADANI', 'nim' => '1342521015'],
            ['name' => 'NAZWA AMYRA', 'nim' => '1342521016'],
            ['name' => 'MUHAMMAD UTSMAN', 'nim' => '1342521018'],
            ['name' => 'SITI NABILA HAMIDAH', 'nim' => '1342521019'],
            ['name' => 'YUNI PUJI LESTARI', 'nim' => '1342521020'],
            ['name' => 'PUTRI NORLAILA', 'nim' => '1342521021'],
            ['name' => 'AULIA HIDAYANTI SAPUTRI', 'nim' => '1342522001'],
            ['name' => 'NI MADE AYU CLAUDYA PUTRI', 'nim' => '1342522006'],
            ['name' => 'MUHAMMAD HUSIEN', 'nim' => '1342522010'],
            ['name' => 'MUHLISA HASANAH', 'nim' => '1342522012'],
            ['name' => 'ARUM ENDAH AWAN TIKA', 'nim' => '1342522014'],
            ['name' => 'MUHAMAD REZA SAPUTRA', 'nim' => '1342522016'],
            ['name' => 'AKHMED SYAHRUL ASSYADIQIE', 'nim' => '1342522019'],
            ['name' => 'MUHAMMAD DAVI', 'nim' => '1342523010'],
            ['name' => 'SITI ALISAH', 'nim' => '1342523011'],
            ['name' => 'DENTI SUDIARTI', 'nim' => '1342523012'],
            ['name' => 'PITNATA SARI', 'nim' => '1342523013'],
        ];

        foreach ($students as $student) {
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
                'id_class'   => $kelasSO->id,
            ]);
        }

        // === 5. Data Mata Pelajaran (Subject) ===
        $subjectSO = Subject::create([
            'name'       => 'Sistem Operasi',
            'id_class'   => $kelasSO->id,
            'created_by' => $guru->id,
        ]);

        // === 6. Data Topik (Topic) ===
        $topicsData = [
            'topik_1' => [
                'title'       => 'Pengantar Sistem Operasi & Konsep Dasar Perangkat Komputer',
                'description' => 'Materi mengenai konsep dasar sistem operasi dan arsitektur perangkat keras komputer.',
            ],
            'topik_2' => [
                'title'       => 'Komponen atau Layanan Sistem Operasi',
                'description' => 'Materi mengenai manajemen utama, layanan sistem operasi, dan System Call.',
            ],
            'topik_3' => [
                'title'       => 'Struktur Sistem Operasi',
                'description' => 'Materi mengenai perancangan struktur sistem operasi (Monolitik, Berlapis, Microkernel, Hybrid).',
            ],
        ];

        $createdTopics = [];
        foreach ($topicsData as $key => $topic) {
            $t = Topic::create([
                'title'       => $topic['title'],
                'description' => $topic['description'],
                'id_subject'  => $subjectSO->id,
                'created_by'  => $guru->id,
            ]);
            $createdTopics[$key] = $t->id;
        }

        // === 7. Data Soal (Question Seeder - 50 Butir Soal) ===
        $questions = [
            // --- TOPIK 1: Pengantar Sistem Operasi & Konsep Dasar Perangkat Komputer (Soal 1 - 16) ---
            'topik_1' => [
                [
                    'text' => 'Secara mendasar, apakah peran utama Sistem Operasi dalam konteks hierarki sistem komputer?',
                    'options' => [
                        'a' => 'Sebagai satu-satunya perangkat lunak aplikasi yang mengolah data pengguna secara langsung.',
                        'b' => 'Sebagai perangkat keras utama yang mengeksekusi perhitungan aritmatika dan logika.',
                        'c' => 'Sebagai perangkat lunak penghubung antara perangkat keras (hardware) dan perangkat lunak aplikasi.',
                        'd' => 'Sebagai media penyimpanan permanen untuk menggantikan fungsi dari harddisk dan SSD.',
                        'e' => 'Sebagai komponen fisik yang menyalurkan arus listrik ke seluruh komponen komputer.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Berdasarkan arsitektur komputer tradisional Von-Neumann, komponen utama sistem komputer terdiri dari:',
                    'options' => [
                        'a' => 'Monitor, Keyboard, Mouse, dan Printer.',
                        'b' => 'Prosesor, Memori Penyimpanan, Masukan (Input), dan Keluaran (Output).',
                        'c' => 'Compiler, Operating System, Browser, dan Text Editor.',
                        'd' => 'Kernel, Shell, User, dan Application.',
                        'e' => 'Cache, Register, Harddisk, dan Flash Drive.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Manakah pernyataan berikut yang paling tepat menggambarkan perbedaan karakteristik kapasitas memori komputer era dahulu (Mainframe) dibanding komputer modern?',
                    'options' => [
                        'a' => 'Komputer dahulu menggunakan ukuran Gigabytes, sedangkan komputer modern menggunakan Kbytes.',
                        'b' => 'Komputer dahulu menggunakan ukuran Terabytes, sedangkan komputer modern menggunakan Megabytes.',
                        'c' => 'Komputer dahulu menggunakan kapasitas beberapa Kbytes, sedangkan komputer modern mencapai beberapa Gbytes.',
                        'd' => 'Komputer dahulu tidak membutuhkan memori utama, sedangkan komputer modern sangat bergantung pada memori.',
                        'e' => 'Komputer dahulu dan modern memiliki kapasitas memori utama yang persis sama.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Komponen perangkat lunak dari sistem operasi yang berjalan terus-menerus di dalam memori sepanjang komputer aktif dikenal dengan istilah:',
                    'options' => [
                        'a' => 'Shell',
                        'b' => 'BIOS',
                        'c' => 'Kernel',
                        'd' => 'User Interface',
                        'e' => 'Device Driver'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Mengapa Random Access Memory (RAM) dikategori kan sebagai media penyimpanan yang bersifat volatile?',
                    'options' => [
                        'a' => 'Karena konten didalamnya diisi secara permanen oleh pabrik pembuatnya.',
                        'b' => 'Karena data yang tersimpan di dalamnya akan hilang apabila pasokan daya listrik terputus.',
                        'c' => 'Karena RAM memiliki daya tampung yang sangat besar dengan harga yang relatif murah.',
                        'd' => 'Karena RAM hanya dapat dibaca dan tidak dapat diubah isinya oleh CPU.',
                        'e' => 'Karena RAM digunakan sebagai media cadangan untuk pencadangan (backup) data.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Pada saat komputer desktop pertama kali dinyalakan, instruksi awal yang dieksekusi berasal dari ROM yang dikenal sebagai BIOS. Fungsi utama instruksi awal tersebut adalah:',
                    'options' => [
                        'a' => 'Menghapus seluruh data di harddisk untuk mengosongkan ruang memori.',
                        'b' => 'Menjalankan aplikasi web browser dan pengolah kata secara otomatis.',
                        'c' => 'Memeriksa komponen sistem, menampilkan pesan di layar, dan memuat sistem operasi.',
                        'd' => 'Menghubungkan komputer langsung ke jaringan internet global.',
                        'e' => 'Mengompres file sistem agar memori RAM tidak cepat penuh.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Dari sudut pandang sistem (system view), Sistem Operasi berfungsi sebagai Resource Allocator dan Control Program. Maksud dari fungsi tersebut adalah:',
                    'options' => [
                        'a' => 'Memudahkan pengguna awam membuat program aplikasi tanpa menulis kode.',
                        'b' => 'Menempatkan sumber daya secara efisien serta mengendalikan eksekusi program untuk mencegah kesalahan.',
                        'c' => 'Menyediakan tampilan antarmuka grafis yang menarik bagi pengguna.',
                        'd' => 'Mempercepat koneksi jaringan internet melalui pengoptimalan bandwidth.',
                        'e' => 'Mengharuskan pengguna mengantre secara manual saat ingin menggunakan komputasi.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Perbedaan utama antara sistem pemrosesan Batch (Batch System) dengan Multiprogrammed System pada sejarah sistem operasi terletak pada:',
                    'options' => [
                        'a' => 'Batch system menggunakan jaringan nirkabel, sedangkan multiprogrammed system menggunakan kabel.',
                        'b' => 'Batch system mengelompokkan pekerjaan sejenis tanpa ada interaksi simultaneous di memori, sedangkan multiprogrammed menyimpan beberapa job di memori utama sekaligus untuk mengefisienkan CPU.',
                        'c' => 'Batch system berukuran sangat kecil, sedangkan multiprogrammed system memerlukan ruangan berukuran raksasa.',
                        'd' => 'Batch system mendukung banyak pengguna interaktif secara berbarengan, sedangkan multiprogrammed tidak.',
                        'e' => 'Batch system tidak memerlukan CPU, sedangkan multiprogrammed system memerlukan banyak CPU.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah laboratorium komputasi menerapkan Time-Sharing System untuk melayani puluhan pengguna sekaligus. Mengapa pendekatan ini menghasilkan efek seolah-olah setiap pengguna memiliki komputer pribadi?',
                    'options' => [
                        'a' => 'Karena CPU digandakan sebanyak jumlah pengguna yang sedang aktif secara fisik.',
                        'b' => 'Karena perpindahan eksekusi CPU antar-pengguna terjadi begitu cepat sehingga response time menjadi sangat pendek.',
                        'c' => 'Karena sistem menghentikan semua program pengguna lain ketika satu pengguna mengetik.',
                        'd' => 'Karena data setiap pengguna disimpan di ROM sehingga tidak memerlukan proses pemuatan.',
                        'e' => 'Karena Time-Sharing System mengubah aplikasi teks menjadi aplikasi grafis resolusi tinggi secara otomatis.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada peranti Masukan/Keluaran berkecepatan tinggi seperti harddisk, penerapan Direct Memory Access (DMA) sangat krusial. Apa keuntungan utama penggunaan DMA bagi kinerja CPU?',
                    'options' => [
                        'a' => 'CPU dapat mengambil alih tugas pembacaan setiap byte data dari disk.',
                        'b' => 'CPU dibebaskan dari interupsi transfer data per byte, karena DMA mentransfer blok data langsung ke/dari memori utama.',
                        'c' => 'DMA mengubah penyimpanan volatile menjadi non-volatile secara otomatis.',
                        'd' => 'DMA mematikan fungsi Memory Controller saat proses transfer data berlangsung.',
                        'e' => 'CPU tidak perlu lagi menjalankan interupsi saat seluruh blok data selesai ditransfer.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Apabila terjadi interupsi perangkat keras, sistem dapat menentukan Interrupt Service Routine (ISR) melalui metode polling atau interrupt vector. Keunggulan utama interrupt vector dibandingkan polling adalah:',
                    'options' => [
                        'a' => 'Interrupt vector tidak memerlukan memori utama untuk menyimpan alamat ISR.',
                        'b' => 'Interrupt vector memeriksa setiap perangkat satu per satu secara sekuensial sehingga lebih detail.',
                        'c' => 'Interrupt vector langsung merujuk pada alamat ISR melalui array penunjuk berdasarkan sinyal interupsi, sehingga lebih cepat dibanding memeriksa perangkat satu per satu.',
                        'd' => 'Interrupt vector mematikan seluruh sinyal interupsi perangkat lunak (trap).',
                        'e' => 'Interrupt vector secara otomatis menghapus kesalahan (bug) pada program pengguna.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah program melakukan operasi pembacaan berkas dari disk secara Synchronous I/O. Dampak langsung dari eksekusi ini terhadap alur kerja CPU dan program pengguna adalah:',
                    'options' => [
                        'a' => 'Kendali langsung kembali ke program pengguna tanpa menunggu proses I/O selesai.',
                        'b' => 'CPU mengeksekusi instruksi wait loop dan berada pada kondisi idle (atau beralih jika ada multiprogramming) hingga interupsi I/O selesai.',
                        'c' => 'Sistem operasi akan menjalankan multiple operasi I/O dari program yang sama secara bersamaan.',
                        'd' => 'Program pengguna akan langsung diberhentikan secara permanen (abort).',
                        'e' => 'Memori utama akan langsung menghapus isi dari Device-status table.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Pada arsitektur bus modern, perangkat berkecepatan tinggi seperti CPU, RAM, dan GPU dihubungkan oleh Front Side Bus (FSB), sementara perangkat lambat dihubungkan via bus sekunder melalui Bridge dan Bus Master (chipset). Mengapa arsitektur hierarki bus bertingkat ini diterapkan?',
                    'options' => [
                        'a' => 'Untuk menyamakan kecepatan transfer seluruh perangkat keras komputer agar sesuai dengan perangkat terlanbat.',
                        'b' => 'Untuk mencegah terjadinya kemacetan (bottleneck) pada bus utama berkecepatan tinggi akibat lalu lintas data dari perangkat yang lambat.',
                        'c' => 'Karena arsitektur bus tunggal tidak memerlukan komponen Memory Controller.',
                        'd' => 'Agar perangkat lambat dapat mengirimkan data ke bus utama tanpa melalui proses verifikasi.',
                        'e' => 'Untuk menghilangkan kebutuhan sinyal clock pada Synchronous Bus.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Ketika terjadi interupsi di pertengahan eksekusi instruksi, arsitektur komputer modern menyimpan alamat instruksi yang terinterupsi beserta informasi state saat itu ke dalam Stack, sedangkan komputer lama menyimpannya di lokasi tetap tertentu. Dampak positif dari penggunaan Stack pada komputer modern adalah:',
                    'options' => [
                        'a' => 'Menghilangkan kebutuhan akan Interrupt Service Routine (ISR).',
                        'b' => 'Memungkinkan penanganan interupsi bersarang (nested interrupts) tanpa menimpa (overwrite) data alamat pemanggilan sebelumnya.',
                        'c' => 'Mempercepat kecepatan fisik rotasi harddisk.',
                        'd' => 'Mengubah interupsi perangkat lunak (trap) menjadi interupsi perangkat keras secara otomatis.',
                        'e' => 'Mencegah penggunaan memori RAM untuk proses eksekusi program.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Teknik caching memanfaatkan memori berkecepatan tinggi untuk menyimpan data yang sering diakses. Namun, keberadaan hirarki penyimpanan dengan cache menimbulkan permasalahan Data Consistency. Permasalahan tersebut muncul ketika:',
                    'options' => [
                        'a' => 'Data diubah pada salah satu tingkatan hierarki (misal cache), namun data pada tingkatan di bawahnya (misal memori utama) belum diperbarui.',
                        'b' => 'Ukuran cache jauh lebih besar daripada ukuran memori utama.',
                        'c' => 'Cache terhapus otomatis saat komputer terhubung ke jaringan LAN.',
                        'd' => 'Data yang berada di cache bersifat non-volatile.',
                        'e' => 'Waktu akses cache jauh lebih lambat dibanding waktu akses penyimpan sekunder.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Dalam sistem multiprosesing di mana CPU dan beberapa device controller beroperasi secara konkuren mengakses memori utama melalui bus bersama, terjadi potensi konflik akibat akses bersamaan. Mekanisme perangkat keras manakah yang secara spesifik bertindak sebagai pengatur lalu lintas data serta dampaknya jika komponen tersebut mengalami kerusakan?',
                    'options' => [
                        'a' => 'Device driver; jika rusak maka seluruh aplikasi grafis berhenti bekerja.',
                        'b' => 'Memory Controller / Bus Controller; jika rusak akan timbul tabrakan data (data corruption) atau kekacauan sinkronisasi akses memori antar-komponen.',
                        'c' => 'Command Interpreter; jika rusak maka CPU tidak dapat membaca file dari ROM.',
                        'd' => 'Interrupt Vector; jika rusak maka daya listrik komputer langsung terputus.',
                        'e' => 'Spooler; jika rusak maka kapasitas harddisk berkurang secara drastis.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],

            // --- TOPIK 2: Komponen atau Layanan Sistem Operasi (Soal 17 - 33) ---
            'topik_2' => [
                [
                    'text' => 'Di bawah ini yang bukan merupakan salah satu dari empat komponen manajemen utama dalam Sistem Operasi adalah:',
                    'options' => [
                        'a' => 'Manajemen Proses',
                        'b' => 'Manajemen Memori Utama',
                        'c' => 'Manajemen Sistem Berkas',
                        'd' => 'Manajemen Web Browser',
                        'e' => 'Manajemen Masukan/Keluaran'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Dalam terminologi sistem operasi, definisi dari istilah "Proses" adalah:',
                    'options' => [
                        'a' => 'Sebuah file eksekutabel yang tersimpan permanen di dalam disk.',
                        'b' => 'Sebuah program yang sedang dalam kondisi dieksekusi (program in execution).',
                        'c' => 'Perangkat keras penyedia sumber daya komputasi.',
                        'd' => 'Kumpulan perintah baris teks pada shell.',
                        'e' => 'Kumpulan data pasif yang berada di media penyimpanan sekunder.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Komponen sistem operasi yang berfungsi membaca dan mengartikan instruksi dari pengguna (control statements) sering disebut sebagai:',
                    'options' => [
                        'a' => 'Linkage Editor',
                        'b' => 'Command-Interpreter System (Shell)',
                        'c' => 'Device Driver',
                        'd' => 'Interrupt Handler',
                        'e' => 'Boot Loader'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Salah satu layanan inti sistem operasi adalah Error Detection (Deteksi Kesalahan). Tindakan ini dilakukan sistem operasi bertujuan untuk:',
                    'options' => [
                        'a' => 'Mencegah pengguna memasukkan password saat login.',
                        'b' => 'Mengubah kode program aplikasi secara otomatis agar tidak ada bug.',
                        'c' => 'Mengidentifikasi kesalahan pada CPU, memori, I/O, atau program pengguna serta mengambil langkah tepat untuk menjaga kelangsungan komputasi.',
                        'd' => 'Menghapus seluruh berkas pengguna saat terjadi kegagalan jaringan.',
                        'e' => 'Mematikan fungsi pendingin prosesor ketika suhu komputer meningkat.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Antarmuka pemanggilan berupa instruksi yang disediakan oleh sistem operasi agar program tingkat tinggi dapat meminta layanan dari kernel disebut:',
                    'options' => [
                        'a' => 'System Call',
                        'b' => 'Interrupt Vector',
                        'c' => 'Control Card',
                        'd' => 'Register File',
                        'e' => 'File Descriptor'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Dalam System Call komunikasi, terdapat dua model utama komunikasi antar-proses, yaitu:',
                    'options' => [
                        'a' => 'Synchronous dan Asynchronous',
                        'b' => 'Message-passing dan Shared-memory',
                        'c' => 'Polling dan Interrupt',
                        'd' => 'Monolithic dan Microkernel',
                        'e' => 'Direct Access dan Sequential Access'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Manakah dari aktivitas berikut yang bukan merupakan tanggung jawab sistem operasi dalam hal Manajemen Memori Utama?',
                    'options' => [
                        'a' => 'Menjaga rekam jejak (track) bagian memori mana yang sedang digunakan dan oleh siapa.',
                        'b' => 'Memilih program mana yang akan dimuat (load) ke dalam memori.',
                        'c' => 'Mengalokasikan dan mengalokasikan kembali (deallocate) ruang memori sesuai kebutuhan.',
                        'd' => 'Menyusun susunan sektor dan trek fisik secara mekanis pada piringan harddisk.',
                        'e' => 'Mengatur alokasi alamat memori untuk setiap variabel proses yang aktif.'
                    ],
                    'answer' => 'd',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada Manajemen Sistem Masukan/Keluaran, dikenal istilah Buffering dan Spooling. Perbedaan mendasar antara kedua konsep tersebut adalah:',
                    'options' => [
                        'a' => 'Buffering menampung data sementara untuk mengatasi perbedaan kecepatan transfer, sedangkan Spooling mengantrekan pekerjaan I/O agar penggunaan perangkat berurutan dan efisien.',
                        'b' => 'Buffering khusus untuk media disk, sedangkan Spooling khusus untuk memori RAM.',
                        'c' => 'Buffering bekerja di luar sistem operasi, sedangkan Spooling bekerja di dalam CPU.',
                        'd' => 'Buffering menghapus data secara permanen, sedangkan Spooling menyimpan data di ROM.',
                        'e' => 'Buffering digunakan untuk jaringan WAN, sedangkan Spooling digunakan untuk LAN.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Ketika sebuah program selesai dikompilasi, program tersebut belum bisa langsung dieksekusi sebelum di-load ke memori. System Program yang bertanggung jawab menggabungkan modul-modul kode dan menyesuaikan alamat memori sebelum dieksekusi adalah:',
                    'options' => [
                        'a' => 'Text Editor dan Web Browser',
                        'b' => 'Linkage Editors dan Relocatable Loaders',
                        'c' => 'Command Line Interpreter dan Shell',
                        'd' => 'File Compressor dan Defragmenter',
                        'e' => 'Device Driver dan Status Monitor'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Ada tiga metode umum untuk mengirimkan parameter dari program pengguna ke sistem operasi saat melakukan pemanggilan System Call. Ketiga metode tersebut adalah:',
                    'options' => [
                        'a' => 'CPU, RAM, dan Secondary Storage.',
                        'b' => 'Register, Block/Table di memori, dan Stack.',
                        'c' => 'Polling, Vector, dan Interupsi.',
                        'd' => 'Read, Write, dan Execute.',
                        'e' => 'CLI, GUI, dan Touch Interface.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada sistem operasi UNIX, pembuatan proses baru dilakukan menggunakan system call fork(). Pernyataan yang benar mengenai perilaku proses parent dan child setelah perintah fork() dieksekusi adalah:',
                    'options' => [
                        'a' => 'Proses child mengeksekusi instruksi dari ROM, sementara parent langsung mati.',
                        'b' => 'Proses child merupakan duplikasi dari parent, lalu parent memanggil waitpid() untuk menunggu child selesai mengeksekusi program baru via exec().',
                        'c' => 'Proses parent dan child membagi satu register CPU yang sama tanpa duplikasi memori.',
                        'd' => 'System call fork() menghapus file eksekutabel asli dari media penyimpan sekunder.',
                        'e' => 'fork() hanya dapat dipanggil oleh proses yang berjalan di User Mode tanpa memerlukan privilege kernel.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sistem operasi MS-DOS dikategorikan sebagai single-tasking system, sedangkan Berkeley UNIX adalah multi-tasking system. Perbedaan perlakuan alur eksekusi program pada MS-DOS dibanding UNIX adalah:',
                    'options' => [
                        'a' => 'MS-DOS tidak memiliki memori utama, sedangkan UNIX memiliki memori utama.',
                        'b' => 'MS-DOS menimpa sebagian besar sistem operasi di memori saat program berjalan dan tidak membuat proses baru, sedangkan UNIX mempertahankan Command Interpreter tetap berjalan saat membuat proses child.',
                        'c' => 'MS-DOS mengeksekusi puluhan program bersamaan, sedangkan UNIX hanya satu program.',
                        'd' => 'MS-DOS menggunakan message passing, sedangkan UNIX hanya menggunakan shared memory.',
                        'e' => 'MS-DOS melarang penggunaan peranti I/O secara langsung.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Dua buah proses dalam sistem operasi saling menunggu sumber daya yang dipegang oleh proses lainnya, sehingga tidak ada satu pun proses yang dapat melanjutkan eksekusi. Kejadian ini dikategorikan sebagai Deadlock. Aktivitas Manajemen Proses mana yang bertugas mengantisipasi kondisi tersebut?',
                    'options' => [
                        'a' => 'Penyediaan antarmuka Command-Interpreter.',
                        'b' => 'Penjadwalan alokasi memori volatile.',
                        'c' => 'Mekanisme penanganan deadlock (deadlock-handling mechanism).',
                        'd' => 'Pemetaan berkas ke secondary storage.',
                        'e' => 'Penjadwalan rotasi head pada disk.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah aplikasi terdistribusi yang berjalan pada beberapa komputer dalam jaringan membutuhkan komunikasi data secara berkala. Model System Call komunikasi manakah yang paling tepat digunakan dan apa alasannya?',
                    'options' => [
                        'a' => 'Shared-memory, karena semua komputer terhubung ke satu keping RAM fisik yang sama.',
                        'b' => 'Message-passing, karena prosesor pada sistem terdistribusi tidak berbagi memori (shared memory) atau clock, sehingga pesan harus dikirim via koneksi jaringan.',
                        'c' => 'Shared-memory, karena message-passing hanya berfungsi untuk komunikasi dalam satu komputer.',
                        'd' => 'File Manipulation Call, karena jaringan tidak mendukung pengiriman data langsung.',
                        'e' => 'Process Control Call, karena komunikasi jaringan tidak memerlukan mekanisme pertukaran data.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Seorang pengguna melakukan operasi penyalinan (copy) file A.txt menjadi B.txt melalui antarmuka perintah (command line). Urutan kategori System Call yang dieksekusi oleh sistem operasi secara sistematis adalah:',
                    'options' => [
                        'a' => 'Hanya System Call Komunikasi.',
                        'b' => 'Pemanggilan System Call Manajemen Berkas (buka A.txt, buat B.txt, baca/tulis isi), dilanjutkan Manajemen Peranti, serta Kontrol Proses jika eksekusi selesai.',
                        'c' => 'Pemanggilan System Call Informasi/Pemeliharaan tanpa melibatkan Manajemen Berkas.',
                        'd' => 'Langsung mengoperasikan perintah perangkat keras tanpa melalui System Call.',
                        'e' => 'Pemanggilan System Call Proteksi untuk mengunci seluruh harddisk.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Pada server Cloud Computing yang digunakan oleh ratusan perusahaan (multi-tenant), layanan sistem operasi berupa Accounting dan Resource Allocation menjadi sangat krusial. Mengapa kedua fungsi tersebut saling berkaitan erat dalam konteks bisnis cloud?',
                    'options' => [
                        'a' => 'Karena Accounting menghapus berkas pengguna, sementara Resource Allocation menghentikan CPU.',
                        'b' => 'Karena Resource Allocation membagi sumber daya (CPU, memori, I/O) secara efisien, sedangkan Accounting mencatat statistik penggunaan tersebut untuk keperluan audit, analisis konfigurasi, dan penagihan.',
                        'c' => 'Karena kedua layanan tersebut bertugas menggantikan fungsi dari Kernel.',
                        'd' => 'Karena Accounting berfungsi menangani interupsi hardware, sementara Resource Allocation berfungsi membaca BIOS.',
                        'e' => 'Karena layanan Accounting hanya berlaku untuk komputer berbasis MS-DOS.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah program pengguna mengalami kendala crash karena mencoba mengakses area memori milik proses lain secara ilegal, atau melakukan operasi division by zero. Apa respons teknis yang dilakukan Sistem Operasi melalui gabungan mekanisme interupsi dan System Call?',
                    'options' => [
                        'a' => 'Sistem operasi akan membiarkan program tersebut merusak data proses lain.',
                        'b' => 'Perangkat keras memicu interupsi khusus perangkat lunak (trap/exception), lalu kernel mengeksekusi System Call Kontrol Proses untuk menghentikan pengeksekusian program secara tidak normal (abort) serta mencatat status kesalahan.',
                        'c' => 'Sistem operasi otomatis melakukan reboot pada seluruh sistem komputer.',
                        'd' => 'Command Interpreter akan menghapus file program tersebut dari media penyimpan sekunder secara permanen.',
                        'e' => 'Sistem operasi akan mengubah kode bahasa assembly program secara realtime agar berjalan normal.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],

            // --- TOPIK 3: Struktur Sistem Operasi (Soal 34 - 50) ---
            'topik_3' => [
                [
                    'text' => 'Ciri khas dari pendekatan Struktur Sederhana (Monolitik Sederhana) pada sistem operasi adalah:',
                    'options' => [
                        'a' => 'Seluruh fungsi dan layanan sistem operasi berada dan berjalan dalam satu ruang memori (kernel space) tanpa partisi ketat.',
                        'b' => 'Setiap modul dijalankan pada komputer yang berbeda secara terdistribusi.',
                        'c' => 'Kernel hanya berisi penjadwalan CPU, sedangkan layanan file berada di ROM.',
                        'd' => 'Menggunakan sistem lapisan abstrak hingga 100 tingkatan.',
                        'e' => 'Tidak dapat mengeksekusi instruksi masukan/keluaran.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Pada Pendekatan Berlapis (Layered Approach) klasik seperti THE System oleh Dijkstra, Lapisan paling bawah (Layer 0) bertugas menangani:',
                    'options' => [
                        'a' => 'Antarmuka Pengguna (Shell/GUI).',
                        'b' => 'Sistem File dan Penyimpanan.',
                        'c' => 'Penanganan interupsi dan penjadwalan proses (fungsi paling dasar dekat hardware).',
                        'd' => 'Aplikasi Web Browser dan Editor.',
                        'e' => 'Manajemen jaringan terdistribusi.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sangat mudah',
                    'delta' => -2.0,
                ],
                [
                    'text' => 'Kelebihan utama dari sistem operasi berstruktur Monolitik Sederhana dibandingkan struktur lainnya adalah:',
                    'options' => [
                        'a' => 'Sangat stabil dan tidak pernah mengalami crash.',
                        'b' => 'Keamanan data pengguna sangat terjamin dari serangan virus.',
                        'c' => 'Performa dan kecepatan eksekusi sangat tinggi karena tidak ada overhead komunikasi antar-proses (context switching).',
                        'd' => 'Sangat mudah diubah kodenya tanpa perlu kompilasi ulang.',
                        'e' => 'Mengisolasi kesalahan driver dengan sempurna.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Komputer modern berbasis Linux memanfaatkan Loadable Kernel Modules (LKM). Fitur ini memungkinkan sistem operasi untuk:',
                    'options' => [
                        'a' => 'Menghapus kernel utama saat sistem beroperasi.',
                        'b' => 'Memuat atau melepas modul (seperti driver hardware/filesystem) ke dalam kernel saat sistem berjalan (run-time) tanpa perlu restart.',
                        'c' => 'Mengubah seluruh struktur OS menjadi Microkernel murni secara otomatis.',
                        'd' => 'Menjalankan aplikasi pengguna tanpa memerlukan RAM.',
                        'e' => 'Menolak semua jenis koneksi bus master.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Konsep utama dari arsitektur Kernel Mikro (Microkernel) adalah:',
                    'options' => [
                        'a' => 'Memindahkan sebanyak mungkin fungsi sistem operasi (seperti file system, driver) keluar dari kernel ke dalam User Space.',
                        'b' => 'Menggabungkan kode aplikasi pengguna ke dalam Kernel Space.',
                        'c' => 'Memperbesar ukuran kernel agar mencakup seluruh software di komputer.',
                        'd' => 'Menghilangkan penggunaan fasilitas Message Passing.',
                        'e' => 'Mengharuskan komputer memiliki micro-processor berukuran fisik kecil.'
                    ],
                    'answer' => 'a',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Sistem operasi modern seperti Windows NT dan macOS (XNU) mengadopsi pendekatan struktur Kernel Hibrida (Hybrid Kernel). Alasan utama digunakannya pendekatan ini adalah:',
                    'options' => [
                        'a' => 'Ingin kembali ke sistem pemrosesan Batch era kuno.',
                        'b' => 'Menyeimbangkan antara keandalan/isolasi struktur Mikrokernel dengan performa/kecepatan struktur Monolitik.',
                        'c' => 'Karena sistem komputer modern tidak mendukung penggunaan driver.',
                        'd' => 'Agar OS dapat dijalankan tanpa memerlukan memori sekunder.',
                        'e' => 'Karena harga lisensi struktur berlapis sangat mahal.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'mudah',
                    'delta' => -1.0,
                ],
                [
                    'text' => 'Pada sistem operasi berstruktur Monolitik Murni, apabila terjadi kegagalan fatal (bug/crash) pada modul driver printer, dampak buruk yang akan terjadi pada seluruh sistem komputer adalah:',
                    'options' => [
                        'a' => 'Hanya driver printer yang terhenti, komponen OS lain tetap berjalan aman.',
                        'b' => 'Seluruh kernel sistem operasi dapat mengalami crash atau hang karena driver berada di ruang memori kernel yang sama.',
                        'c' => 'Sistem secara otomatis beralih menjadi sistem berarsitektur Microkernel.',
                        'd' => 'Aplikasi pengolah kata akan langsung memperbaiki kode driver tersebut.',
                        'e' => 'Harddisk secara otomatis melakukan pemformatan ulang.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Aturan komunikasi pada Pendekatan Berlapis (Layered Approach) menetapkan bahwa suatu lapisan N hanya boleh memanggil layanan dari:',
                    'options' => [
                        'a' => 'Lapisan di atasnya (N+1) saja.',
                        'b' => 'Seluruh lapisan secara bebas tanpa hirarki.',
                        'c' => 'Lapisan tepat di bawahnya (N-1).',
                        'd' => 'Hanya dari perangkat keras secara langsung.',
                        'e' => 'Lapisan paling atas (User Interface) saja.'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Mengapa sistem operasi dengan arsitektur Kernel Mikro (Microkernel) memiliki tingkat kestabilan (fault tolerance) dan keamanan yang lebih tinggi dibanding Monolitik?',
                    'options' => [
                        'a' => 'Karena Microkernel menggunakan bahasa pemrograman yang tidak dapat salah.',
                        'b' => 'Karena layanan seperti file system dan driver berjalan sebagai proses terpisah di User Space, sehingga jika layanan tersebut crash, kernel utama tidak ikut runtuh.',
                        'c' => 'Karena Microkernel mengeksekusi semua perintah secara Synchronous.',
                        'd' => 'Karena Microkernel tidak membutuhkan komunikasi antar-proses (IPC).',
                        'e' => 'Karena Microkernel mematikan seluruh hak akses bagi pengguna (user).'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Pada awalnya Windows NT dirancang sangat mendekati arsitektur Microkernel. Namun pada versi selanjutnya, Microsoft memindahkan komponen subsistem grafik dan I/O kembali ke dalam ruang kernel (Kernel Space). Alasan teknis di balik keputusan desain tersebut adalah:',
                    'options' => [
                        'a' => 'Untuk memperlambat kinerja grafis agar hemat baterai.',
                        'b' => 'Karena komunikasi via message passing untuk grafik di user space menghasilkan overhead performa yang terlalu lambat.',
                        'c' => 'Karena sistem file tidak lagi membutuhkan memori RAM.',
                        'd' => 'Karena Microkernel tidak mendukung penggunaan monitor berwarna.',
                        'e' => 'Mencegah pengguna mengubah resolusi layar secara manual.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Sebuah perangkat keras USB modem baru dihubungkan ke laptop berbasis Linux. Sistem operasi langsung mengenali perangkat tersebut dan memuat modul driver tanpa melakukan restart/reboot. Pendekatan struktur OS manakah yang memungkinkan fleksibilitas ini?',
                    'options' => [
                        'a' => 'Monolitik Tradisional tanpa Modul',
                        'b' => 'Struktur Monolitik Modular (dengan Loadable Kernel Modules)',
                        'c' => 'Pendekatan Berlapis Kaku (THE System)',
                        'd' => 'Batch Processing Structure',
                        'e' => 'Firmware ROM Standalone'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Perhatikan perbandingan berikut: 1) Panggilan fungsi langsung di dalam memori kernel. 2) Pengiriman pesan (message passing) antar-proses terpisah melalui kernel. Manakah pernyataan yang paling tepat mengenai perbandingan overhead performa antara kedua cara komunikasi di atas?',
                    'options' => [
                        'a' => 'Cara (2) jauh lebih cepat daripada cara (1).',
                        'b' => 'Cara (1) tidak memerlukan memori RAM sama sekali.',
                        'c' => 'Cara (1) pada Monolitik jauh lebih cepat daripada cara (2) pada Microkernel karena tidak memerlukan context switching berulang.',
                        'd' => 'Kedua cara memiliki kecepatan yang persis sama.',
                        'e' => 'Cara (2) tidak mengalami overhead waktu tunda (latency).'
                    ],
                    'answer' => 'c',
                    'difficulty' => 'sedang',
                    'delta' => 0.0,
                ],
                [
                    'text' => 'Tim pengembang sedang merancang sistem operasi untuk sistem kendali Pesawat Komersial atau Kapsul Luar Angkasa yang mengutamakan keandalan mutlak (high reliability), di mana kegagalan satu komponen tidak boleh mematikan sistem navigasi utama. Struktur sistem operasi manakah yang paling direkomendasikan untuk sistem kritis ini?',
                    'options' => [
                        'a' => 'Monolitik Sederhana, karena mengejar kecepatan maksimal tanpa peduli crash.',
                        'b' => 'Kernel Mikro (Microkernel) seperti QNX, karena isolasi layanan di user space menjamin sistem inti tetap stabil saat subsistem lain mengalami kesalahan.',
                        'c' => 'Pendekatan Berlapis 100 tingkat, agar lalu lintas pesan sangat panjang.',
                        'd' => 'MS-DOS Single tasking, karena tidak memiliki memori sekunder.',
                        'e' => 'Struktur Monolitik tanpa modul, agar kode tidak dapat diperbarui.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Salah satu alasan utama mengapa Pendekatan Berlapis (Layered Approach) jarang digunakan secara murni dalam praktik pembuatan sistem operasi komersial modern adalah tantangan Circular Dependency. Permasalahan ini terjadi ketika:',
                    'options' => [
                        'a' => 'Lapisan antarmuka pengguna memanggil instruksi BIOS secara langsung.',
                        'b' => 'Dua komponen pada lapisan berbeda saling membutuhkan layanan satu sama lain (misal: Sistem File butuh Penyangga Memori, tetapi Manajemen Memori butuh penyimpanan Disk untuk backing store), sehingga sulit menentukan urutan lapisan hierarki secara murni.',
                        'c' => 'Seluruh lapisan digabungkan ke dalam satu file executable tunggal.',
                        'd' => 'Lapisan terbawah tidak dapat berkomunikasi dengan perangkat keras.',
                        'e' => 'Kecepatan eksekusi lapisan paling atas melebihi kecepatan prosesor.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Ketika sebuah aplikasi di arsitektur Microkernel ingin membaca data dari berkas di disk, aplikasi (User Space) mengirim pesan ke Kernel, Kernel meneruskan pesan ke File Server (User Space), File Server kirim pesan balik via Kernel ke Disk Driver (User Space). Mengapa runtutan ini menyebabkan penurunan performa dibanding sistem Monolitik?',
                    'options' => [
                        'a' => 'Karena disk driver pada Microkernel berputar lebih lambat secara mekanis.',
                        'b' => 'Karena terjadi siklus context switching dan pengiriman pesan (IPC) yang berulang kali antara User Mode dan Kernel Mode.',
                        'c' => 'Karena Microkernel tidak mendukung penggunaan RAM di User Space.',
                        'd' => 'Karena file server pada Microkernel menghapus isi file saat dibaca.',
                        'e' => 'Karena sistem Monolitik tidak menggunakan CPU untuk membaca disk.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Linux sering disebut sebagai sistem operasi Monolitik Modular. Pernyataan yang paling tepat untuk menjelaskan kombinasi konsep ini adalah:',
                    'options' => [
                        'a' => 'Seluruh fungsi OS dijalankan di User Space seperti Microkernel, tetapi kodenya ditulis dalam satu file.',
                        'b' => 'Inti sistem berjalan dalam satu ruang alamat memori kernel untuk performa tinggi, namun fungsionalitasnya dapat ditambah/dikurangi secara dinamis melalui modul (LKM) tanpa kompilasi ulang seluruh kernel.',
                        'c' => 'Linux menggunakan struktur berlapis kaku 5 tingkat tanpa dukungan memori virtual.',
                        'd' => 'Linux tidak dapat dijalankan jika tidak ada modul dinamis di dalam ROM.',
                        'e' => 'Setiap modul Linux berjalan pada prosesor fisik yang berbeda-beda.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sulit',
                    'delta' => 1.0,
                ],
                [
                    'text' => 'Sebuah perusahaan perangkat medis sedang mengembangkan alat Pacemaker (Pemicu Jantung Implan) yang harus hemat energi, berukuran kode sangat kecil, responsif secara real-time, dan tidak boleh crash sama sekali. Berdasarkan analisis kelebihan dan kekurangan struktur OS, arsitektur manakah yang paling rasional dipilih beserta alasannya?',
                    'options' => [
                        'a' => 'Monolitik Sederhana, karena kode sangat besar sehingga muat banyak fitur hiburan.',
                        'b' => 'Microkernel Ter-minimalisasi, karena mengisolasi fungsi pemicu jantung dari modul sekunder, sehingga jika modul diagnostik/komunikasi bermasalah, fungsi pemicu jantung utama tidak terganggu.',
                        'c' => 'Hybrid Kernel skala penuh, karena membutuhkan dukungan graphics driver 3D resolusi tinggi.',
                        'd' => 'Batch Processing System, karena data detak jantung hanya perlu diproses sehari sekali di malam hari.',
                        'e' => 'Layered Approach 20 Tingkat, agar instruksi pemicu jantung melewati rute terpanjang sebelum sampai ke hardware.'
                    ],
                    'answer' => 'b',
                    'difficulty' => 'sangat sulit',
                    'delta' => 2.0,
                ],
            ],
        ];

        // === 8. Menyimpan Data Soal ke Database ===
        foreach ($questions as $topicKey => $topicQuestions) {
            $topicId = $createdTopics[$topicKey];

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