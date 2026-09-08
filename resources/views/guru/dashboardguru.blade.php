@extends('layouts.main')

@section('dashboardGuru', request()->is('dashboardguru') ? 'active' : '')

@section('content')
<style>
    .step-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .la-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
</style>

<div class="container py-4">

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
        <div class="card-body text-center py-4">
            <h3 class="fw-bold mb-2">
                Selamat Datang, {{ Auth::user()->name }} 
            </h3>
            <p class="mb-0 opacity-75">
                Platform Evaluasi Pembelajaran Adaptif <strong>(Computerized Adaptive Testing)</strong> & <strong>Learning Analytics</strong>.
            </p>
        </div>
    </div>

    {{-- LAYOUT UTAMA DUA KOLOM --}}
    <div class="row g-4 mb-4">

        {{-- KOLOM KIRI --}}
        <div class="col-lg-6">

            {{-- BAGIAN 1: PENGATURAN KESULTAN SOAL --}}
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-white fw-bold py-3 text-primary border-bottom">
                    <i class="fas fa-sliders-h me-2"></i> 1. Pengelolaan Tingkat Kesulitan Soal
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        Dosen menentukan tingkat kesulitan saat menambah soal. Sistem memprosesnya secara otomatis untuk ujian adaptif tanpa perlu kalibrasi manual.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success">Mudah</span>
                                <h6 class="fw-bold mb-0 text-dark">Tingkat Dasar</h6>
                            </div>
                            <p class="small text-muted mb-0">
                                Soal di bawah rerata kesulitan. Didesain untuk pemahaman dasar atau penyesuaian saat siswa mengalami kesulitan.
                            </p>
                        </div>

                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-warning text-dark">Sedang</span>
                                <h6 class="fw-bold mb-0 text-dark">Tingkat Standar</h6>
                            </div>
                            <p class="small text-muted mb-0">
                                Soal pada titik netral/rata-rata. Digunakan sebagai titik awal pengerjaan ujian untuk mengukur kemampuan awal siswa.
                            </p>
                        </div>

                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-danger">Sulit</span>
                                <h6 class="fw-bold mb-0 text-dark">Tingkat Lanjut</h6>
                            </div>
                            <p class="small text-muted mb-0">
                                Soal di atas rerata kesulitan. Ditujukan untuk siswa yang menunjukkan penguasaan materi tinggi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-6">

            {{-- BAGIAN 2: CARA KERJA UJIAN ADAPTIF --}}
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 text-dark border-bottom">
                    <i class="fas fa-sync-alt me-2 text-primary"></i> 2. Cara Kerja Ujian Adaptif Real-Time
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <p class="text-muted small mb-3">
                        Ujian adaptif bekerja secara interaktif dengan menyesuaikan tingkat kesulitan soal berikutnya berdasarkan jawaban siswa secara langsung (*real-time*).
                    </p>

                    <div class="d-flex flex-column gap-3 my-auto">
                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex gap-3 align-items-start">
                                <span class="step-badge">1</span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Mulai dari Soal Standar</h6>
                                    <p class="small text-muted mb-0">
                                        Seluruh siswa memulai ujian dari tingkat kesulitan <strong>Sedang</strong> untuk mengukur posisi awal kemampuan mereka.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex gap-3 align-items-start">
                                <span class="step-badge">2</span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Penyesuaian Otomatis</h6>
                                    <p class="small text-muted mb-0">
                                        Jika siswa menjawab <strong>Benar</strong>, sistem memberikan soal lebih <strong>Sulit</strong>. Jika <strong>Salah</strong>, sistem menurunkan ke soal lebih <strong>Mudah</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex gap-3 align-items-start">
                                <span class="step-badge">3</span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Pengukuran Akurat</h6>
                                    <p class="small text-muted mb-0">
                                        Setiap jawaban memperbarui estimasi kemampuan siswa hingga diperoleh gambaran kompetensi yang stabil.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex gap-3 align-items-start">
                                <span class="step-badge">4</span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Penyelesaian Pintar</h6>
                                    <p class="small text-muted mb-0">
                                        Ujian selesai otomatis saat kemampuan siswa terukur presisi atau batas maksimal soal terpenuhi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- BAGIAN 3: LEARNING ANALYTICS (ANALITIK PEMBELAJARAN) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3 text-dark border-bottom d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-chart-line me-2 text-primary"></i> 3. Analitik Pembelajaran (Learning Analytics)
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">Fitur Edukasi</span>
        </div>
        <div class="card-body p-4">
            
            {{-- RINGKASAN KONSEP UNTUK UMUM --}}
            <div class="p-3 bg-light rounded border mb-4">
                <p class="small text-muted mb-0">
                    <strong>Apa itu Learning Analytics?</strong> Sederhananya, sistem mengubah aktivitas belajar siswa (<strong>Input</strong>) menjadi gambaran pemahaman yang jelas (<strong>Proses</strong>), lalu memberikan petunjuk langkah belajar berikutnya bagi guru dan siswa (<strong>Output</strong>).
                </p>
            </div>

            {{-- 4 INDIKATOR ANALITIK --}}
            <div class="row g-3">
                
                {{-- INDIKATOR 1 --}}
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 border rounded h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="la-icon bg-primary-subtle text-primary">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Performa & Akurasi</h6>
                                <span class="badge bg-secondary-subtle text-secondary border small" style="font-size: 0.7rem;">Deskriptif</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">
                            Mengukur skor aktual siswa berdasarkan perbandingan jumlah jawaban benar terhadap seluruh soal yang dikerjakan.
                        </p>
                    </div>
                </div>

                {{-- INDIKATOR 2 --}}
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 border rounded h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="la-icon bg-success-subtle text-success">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Penguasaan Materi</h6>
                                <span class="badge bg-success-subtle text-success border small" style="font-size: 0.7rem;">Diagnostik</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">
                            Mendiagnosis tingkat pemahaman siswa pada suatu topik berdasarkan kombinasi jawaban dan tingkat kesulitan soal.
                        </p>
                    </div>
                </div>

                {{-- INDIKATOR 3 --}}
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 border rounded h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="la-icon bg-warning-subtle text-warning-emphasis">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Sebaran Kesulitan</h6>
                                <span class="badge bg-secondary-subtle text-secondary border small" style="font-size: 0.7rem;">Deskriptif</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">
                            Menampilkan pola akurasi siswa secara spesifik pada tingkatan soal mudah, sedang, maupun sulit.
                        </p>
                    </div>
                </div>

                {{-- INDIKATOR 4 --}}
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 border rounded h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="la-icon bg-info-subtle text-info-emphasis">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Rekomendasi Belajar</h6>
                                <span class="badge bg-info-subtle text-info-emphasis border small" style="font-size: 0.7rem;">Preskriptif</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">
                            Mengubah hasil analisis menjadi saran tindakan nyata untuk perbaikan atau pengayaan belajar siswa.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection