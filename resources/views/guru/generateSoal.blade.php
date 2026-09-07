@extends('layouts.main')
@section('dataSoal', request()->is('generate-soal') ? 'active' : '')

@section('content')
    <div class="container py-4">
        {{-- HEADER HALAMAN --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-robot me-2"></i>Generator Soal dengan Bantuan AI
                </h3>
                <button type="button"
                    class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoGenerateSoal"
                    title="Informasi Generator Soal">
                    <i class="bi bi-info-lg"></i>
                </button>
            </div>

            <a href="{{ route('tampilanSoal') }}" class="btn btn-secondary px-3 shadow-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali
            </a>
        </div>

        {{-- MAIN LAYOUT 2 KOLOM --}}
        <div class="row g-4">
            {{-- KOLOM KIRI: FORM GENERATOR AI --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-primary text-white fw-bold py-3 px-4 rounded-top-4">
                        <i class="bi bi-sliders me-2"></i> Konfigurasi Kebutuhan
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <form id="formGenerateAI">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Topik Soal <span class="text-danger">*</span></label>
                                <select name="topic" id="topicSelect" class="form-select" required>
                                    <option value="">-- Pilih Topik --</option>
                                    @foreach($topics as $t)
                                        <option value="{{ $t->id }}" {{ isset($selectedTopic) && $selectedTopic == $t->id ? 'selected' : '' }}>
                                            {{ $t->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenjang Pendidikan</label>
                                <select name="jenjang" id="jenjangSelect" class="form-select">
                                    <option value="">-- Pilih Jenjang (Opsional) --</option>
                                    @foreach($jenjangList as $j)
                                        <option value="{{ $j }}" {{ (old('jenjang') ?? ($selectedJenjang ?? null)) == $j ? 'selected' : '' }}>
                                            {{ strtoupper($j) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Jumlah Soal / Level <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="jumlah" id="jumlahInput" class="form-control" min="1" max="10"
                                    value="{{ $jumlahInput ?? 3 }}" required placeholder="1-10 soal">
                                <div class="form-text small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>Jumlah soal yang dibuat untuk
                                    <strong>masing-masing tingkat kesulitan</strong> (mudah, sedang, sulit).
                                </div>
                            </div>

                            <button type="submit" id="btnGenerateAI" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-cpu-fill me-1"></i> Generate Soal dengan AI
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: OUTPUT KODE JSON & SIMPAN --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div
                        class="card-header bg-dark text-white fw-bold py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-code me-2"></i> Output Soal dalam bentuk JSON</span>
                        <button type="button" class="btn btn-sm btn-outline-light" id="btnCopyJson">
                            <i class="bi bi-clipboard me-1"></i> Salin JSON
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('importQuestionJson') }}" method="POST" id="formPasteJson">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hasil JSON Soal:</label>
                                <textarea name="json_text" id="json_text" class="form-control font-monospace p-3 bg-light"
                                    rows="14"
                                    placeholder='Hasil JSON buatan AI akan muncul di sini secara otomatis.'
                                    required></textarea>
                            </div>

                            <input type="hidden" name="upload_mode" value="paste">

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan JSON ke Database Bank Soal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INFO GENERATOR SOAL --}}
    <div class="modal fade" id="modalInfoGenerateSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle me-2"></i> Panduan Generator Soal Otomatis
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-muted">
                        Halaman <strong>Generator Soal Otomatis</strong> membantu guru membuat variasi soal secara instan
                        berbasis AI dan langsung menyimpannya ke bank soal database.
                    </p>
                    <hr>

                    <h6 class="fw-bold text-primary">
                        <i class="bi bi-sliders me-1"></i> Langkah 1: Konfigurasi AI (Sisi Kiri)
                    </h6>
                    <ul>
                        <li>Pilih <strong>Topik Soal</strong> yang ingin dibuatkan pertanyaan.</li>
                        <li>Tentukan <strong>Jenjang Pendidikan</strong> agar kompleksitas bahasa disesuaikan.</li>
                        <li>Masukkan <strong>Jumlah Soal</strong> per tingkat kesulitan (mudah, sedang, sulit).</li>
                        <li>Klik <strong>Generate Soal dengan AI</strong>.</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-success">
                        <i class="bi bi-file-earmark-code me-1"></i> Langkah 2: Pratinjau & Simpan (Sisi Kanan)
                    </h6>
                    <ul>
                        <li>Hasil JSON buatan AI akan otomatis terisi di kolom <strong>Hasil JSON Soal</strong> di sebelah
                            kanan.</li>
                        <li>Anda dapat menyunting atau memeriksa struktur teks soal secara manual jika diperlukan.</li>
                        <li>Klik <strong>Simpan JSON ke Database Bank Soal</strong> untuk menyimpan seluruh data pertanyaan.
                        </li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ===== GENERATE AI VIA AJAX =====
            const formGenerateAI = document.getElementById('formGenerateAI');
            const btnGenerateAI = document.getElementById('btnGenerateAI');
            const jsonTextarea = document.getElementById('json_text');

            if (formGenerateAI) {
                formGenerateAI.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const topicVal = document.getElementById('topicSelect').value;
                    const jenjangVal = document.getElementById('jenjangSelect').value;
                    const jumlahVal = document.getElementById('jumlahInput').value;

                    if (!topicVal) {
                        Swal.fire('Peringatan', 'Silakan pilih topik soal terlebih dahulu.', 'warning');
                        return;
                    }

                    // Tampilkan Loading Spinner
                    Swal.fire({
                        title: 'AI Sedang Menyusun Soal...',
                        html: 'Mohon tunggu beberapa saat. AI sedang membuat variasi soal pilihan ganda & isian singkat.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    btnGenerateAI.disabled = true;

                    try {
                        const response = await fetch("{{ route('generateSoal.post') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                topic: topicVal,
                                jenjang: jenjangVal,
                                jumlah: jumlahVal
                            })
                        });

                        const resData = await response.json();

                        if (resData.success) {
                            // Format & masukkan hasil ke textarea kanan
                            const formattedJson = JSON.stringify(resData.data, null, 2);
                            if (jsonTextarea) jsonTextarea.value = formattedJson;

                            Swal.fire({
                                icon: 'success',
                                title: 'Soal Berhasil Dibuat!',
                                text: 'Hasil JSON telah diisi di kolom sebelah kanan. Silakan periksa dan klik "Simpan JSON ke Database Bank Soal".',
                                confirmButtonText: 'Periksa Soal'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Membuat Soal',
                                text: resData.message || 'Terjadi kesalahan pada respon server AI.'
                            });
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Koneksi',
                            text: 'Gagal terhubung ke server. Periksa koneksi internet Anda.'
                        });
                    } finally {
                        btnGenerateAI.disabled = false;
                    }
                });
            }

            // ===== SALIN KODE JSON =====
            const btnCopyJson = document.getElementById('btnCopyJson');
            if (btnCopyJson && jsonTextarea) {
                btnCopyJson.addEventListener('click', function () {
                    if (!jsonTextarea.value.trim()) {
                        Swal.fire('Kosong', 'Tidak ada teks JSON untuk disalin.', 'info');
                        return;
                    }

                    navigator.clipboard.writeText(jsonTextarea.value)
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disalin',
                                text: 'Kode JSON berhasil disalin ke clipboard.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        })
                        .catch(() => {
                            Swal.fire('Gagal', 'Browser tidak mengizinkan akses clipboard.', 'error');
                        });
                });
            }

            // ===== SUBMIT HANDLER FORM IMPORT =====
            const formPasteJson = document.getElementById('formPasteJson');
            if (formPasteJson) {
                formPasteJson.addEventListener('submit', function () {
                    Swal.fire({
                        title: 'Memproses Simpan Data',
                        text: 'Mohon tunggu, soal sedang disimpan ke database bank soal.',
                        allowOutsideClick: false,
                        didOpen: function () {
                            Swal.showLoading();
                        }
                    });
                });
            }

            // ===== SWEETALERT DARI SESSION LARAVEL =====
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Menyimpan!',
                    html: `
                                <p>{{ session('success') }}</p>
                                @if(session('imported_count'))
                                    <p class="mb-0"><strong>Total soal disimpan: {{ session('imported_count') }} soal</strong></p>
                                @endif
                            `,
                    confirmButtonText: 'OK'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Periksa Format JSON'
                });
            @endif

            });
    </script>
@endsection