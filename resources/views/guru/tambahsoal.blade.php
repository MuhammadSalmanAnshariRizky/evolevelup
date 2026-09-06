@extends('layouts.main')
@section('dataSoal', request()->is('soal/tambah') ? 'active' : '')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap w-100">
                        <div>
                            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
                                <h3 class="fw-bold text-primary mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-plus-circle"></i>
                                    Tambah Soal
                                </h3>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:32px;height:32px" data-bs-toggle="modal"
                                    data-bs-target="#modalInfoTambahSoal" title="Informasi Tambah Soal">
                                    <i class="bi bi-info-lg"></i>
                                </button>
                            </div>

                            {{-- Info kelas --}}
                            @if($kelasGuru->count())
                                <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                    @foreach($kelasGuru as $k)
                                        <span class="text-muted"> Nama Kelas : {{ $k->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-danger small mt-1">
                                    Anda belum tergabung pada kelas manapun.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- FORM START --}}
                <form id="soalForm" action="{{ route('simpanSoal') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- baris 1 -->
                    <div class="row mt-3">

                        {{-- Tipe Soal --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe Soal</label>
                            <select name="type" class="form-select" id="tipeSoal" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="MultipleChoice">Pilihan Ganda</option>
                                <option value="ShortAnswer">Isian Singkat</option>
                            </select>
                        </div>

                        {{-- Kesulitan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tingkat Kesulitan</label>
                            <select name="difficulty" class="form-select" id="difficulty" required>
                                <option value="">-- Pilih Kesulitan --</option>
                                <option value="mudah">Mudah</option>
                                <option value="sedang">Sedang</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>

                        {{-- Topik --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Topik (opsional)</label>
                            <select name="id_topic" class="form-select" id="id_topic">
                                <option value="">-- Pilih Topik --</option>
                                @if(isset($topics) && $topics->count())
                                    @foreach($topics as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->title }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <div class="form-text">
                                Topik muncul berdasarkan mata pelajaran/kelas yang Anda ajar.
                            </div>
                        </div>

                    </div>

                    {{-- TAGS --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tags</label>

                            <input type="text" name="tags" id="tags" class="form-control"
                                placeholder="Contoh: vlan, switch, jaringan">

                            <div class="form-text">
                                Masukkan beberapa tag dan pisahkan dengan koma.
                                Contoh: <strong>vlan, switch, jaringan</strong>
                            </div>
                        </div>
                    </div>

                    {{-- PETUNJUK / HINT (Hanya muncul jika memilih Isian Singkat) --}}
                    <div class="row mt-3" id="petunjukContainer" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Petunjuk / Hint (opsional)</label>

                            <textarea name="hint" id="hint" class="form-control" rows="2"
                                placeholder="Tuliskan petunjuk atau bantuan pengerjaan soal di sini..."></textarea>

                            <div class="form-text">
                                Petunjuk dapat membantu siswa ketika mengalami kesulitan saat mengerjakan soal isian
                                singkat.
                            </div>
                        </div>
                    </div>

                    <!-- baris 2 -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teks Pertanyaan</label>
                            <textarea name="question_text" id="question_text" class="form-control" rows="4"
                                placeholder="Tulis teks soal di sini..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Soal (opsional)</label>

                            {{-- Custom Input File + Tombol Hapus --}}
                            <div class="input-group mb-2">
                                <button class="btn btn-outline-secondary" type="button" id="btnTriggerQuestionImage">
                                    <i class="bi bi-image me-1"></i> Pilih File
                                </button>
                                <input type="text" class="form-control" id="questionFileName"
                                    placeholder="Belum ada file dipilih" readonly>
                                <input type="file" name="question_image" class="d-none" accept="image/*"
                                    id="questionImageInput">

                                <button class="btn btn-outline-danger" type="button" id="btnClearQuestionImage"
                                    title="Hapus Gambar Soal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <input type="text" name="question_url" id="question_url" class="form-control mb-2"
                                placeholder="Atau masukkan URL gambar">

                            <div id="previewQuestionImage" class="mt-2 text-center"></div>
                        </div>
                    </div>

                    <!-- baris 3 (Pilihan Ganda area) -->
                    <div class="row mt-3">
                        <div id="opsiPilihanGanda" style="display:none; width:100%;">
                            <hr>
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-list-check me-2"></i> Pilihan Jawaban
                            </h5>

                            <div class="row">
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $i => $opt)
                                    <div class="col-md-4 mb-3">
                                        <div class="card shadow-sm border-0 h-100 option-card">
                                            <div class="card-body">
                                                <label class="fw-semibold mb-2">Opsi {{ strtoupper($opt) }}</label>
                                                <input type="text" name="option_text[]" class="form-control option-text mb-2"
                                                    placeholder="Teks opsi {{ strtoupper($opt) }}">

                                                {{-- Input Gambar Opsi --}}
                                                <div class="option-image-wrapper">
                                                    <div class="input-group input-group-sm mb-2">
                                                        <button class="btn btn-outline-secondary btn-trigger-opt-file"
                                                            type="button">
                                                            Pilih File
                                                        </button>
                                                        <input type="text" class="form-control opt-file-name"
                                                            placeholder="Belum ada file" readonly>
                                                        <input type="file" name="option_image[]" class="d-none opt-file-input"
                                                            accept="image/*">

                                                        <button class="btn btn-outline-danger btn-clear-opt-image" type="button"
                                                            title="Hapus Gambar/URL Opsi">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>

                                                    <input type="text" name="option_url[]"
                                                        class="form-control form-control-sm opt-url-input mb-2"
                                                        placeholder="URL gambar (opsional)">

                                                    <div class="opt-preview text-center"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Jawaban Benar -->
                                <div class="col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <label class="form-label fw-semibold">Jawaban Benar</label>
                                            <select name="mc_answer" id="mc_answer" class="form-select">
                                                <option value="">-- Pilih Jawaban --</option>
                                                @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                                    <option value="{{ $opt }}">{{ strtoupper($opt) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Isian Singkat -->
                    <div id="opsiIsianSingkat" style="display:none; margin-top:1rem;">
                        <hr>
                        <h5 class="fw-bold text-secondary mb-3">
                            <i class="bi bi-pencil-square me-2"></i> Jawaban Benar (Isian Singkat)
                        </h5>
                        <div id="jawabanContainer">
                            <input type="text" name="sa_answer[]" class="form-control sa-answer mb-2"
                                placeholder="Masukkan jawaban singkat">
                        </div>
                        <button type="button" id="tambahJawaban" class="btn btn-outline-secondary btn-sm mt-2">
                            <i class="bi bi-plus-circle"></i> Tambah Jawaban
                        </button>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" id="submitBtn" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Simpan Soal
                        </button>
                        <a href="{{ route('tampilanSoal') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembali
                        </a>
                    </div>
                </form>
                {{-- FORM END --}}

            </div>
        </div>
    </div>

    {{-- MODAL INFO TAMBAH SOAL --}}
    <div class="modal fade" id="modalInfoTambahSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Panduan Menambah Soal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Halaman <strong>Tambah Soal</strong> digunakan untuk membuat soal baru
                        yang akan disimpan ke bank soal dan dapat digunakan dalam berbagai aktivitas.
                    </p>
                    <hr>
                    <h6 class="fw-bold text-primary">
                        <i class="bi bi-ui-checks me-1"></i>
                        Tipe & Kesulitan Soal
                    </h6>
                    <ul>
                        <li><strong>Tipe Soal</strong> menentukan bentuk soal:
                            <ul>
                                <li><b>Pilihan Ganda</b>: memiliki opsi A–E dan satu jawaban benar</li>
                                <li><b>Isian Singkat</b>: memiliki satu atau lebih jawaban benar serta petunjuk/hint</li>
                            </ul>
                        </li>
                        <li><strong>Tingkat Kesulitan</strong> digunakan untuk pengelompokan dan sistem adaptive.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-bold text-secondary">
                        <i class="bi bi-tags me-1"></i>
                        Topik Soal
                    </h6>
                    <ul>
                        <li>Topik bersifat <b>opsional</b>.</li>
                        <li>Topik yang muncul hanya berasal dari mata pelajaran dan kelas yang Anda ajar.</li>
                        <li>Topik memudahkan pengelompokan soal dan pemilihan otomatis.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-bold text-success">
                        <i class="bi bi-question-circle me-1"></i>
                        Teks & Gambar Pertanyaan
                    </h6>
                    <ul>
                        <li>Teks pertanyaan wajib diisi.</li>
                        <li>Gambar soal bersifat opsional dan dapat diisi dengan upload file atau URL gambar.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-bold text-warning">
                        <i class="bi bi-list-check me-1"></i>
                        Pilihan Jawaban (Pilihan Ganda)
                    </h6>
                    <ul>
                        <li>Semua opsi A–E harus diisi.</li>
                        <li>Setiap opsi dapat memiliki teks jawaban dan gambar (opsional).</li>
                        <li>Jawaban benar wajib dipilih.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-bold text-info">
                        <i class="bi bi-pencil-square me-1"></i>
                        Jawaban Isian Singkat
                    </h6>
                    <ul>
                        <li>Minimal satu jawaban harus diisi.</li>
                        <li>Gunakan tombol <b>Tambah Jawaban</b> untuk menambahkan variasi jawaban benar.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-bold text-danger">
                        <i class="bi bi-shield-check me-1"></i>
                        Validasi Form
                    </h6>
                    <ul>
                        <li>Sistem akan memeriksa kelengkapan data sebelum soal disimpan.</li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tipeSoal = document.getElementById('tipeSoal');
            const opsiPG = document.getElementById('opsiPilihanGanda');
            const opsiSA = document.getElementById('opsiIsianSingkat');
            const petunjukContainer = document.getElementById('petunjukContainer');
            const hintInput = document.getElementById('hint');
            const tambahJawaban = document.getElementById('tambahJawaban');
            const jawabanContainer = document.getElementById('jawabanContainer');

            const btnTriggerQuestionImage = document.getElementById('btnTriggerQuestionImage');
            const questionImageInput = document.getElementById('questionImageInput');
            const questionFileName = document.getElementById('questionFileName');
            const questionUrlInput = document.getElementById('question_url');
            const previewQuestionImage = document.getElementById('previewQuestionImage');
            const btnClearQuestionImage = document.getElementById('btnClearQuestionImage');

            const form = document.getElementById('soalForm');
            const submitBtn = document.getElementById('submitBtn');

            // Toggle area sesuai tipe soal (Opsi PG, Opsi SA, dan Petunjuk Container)
            tipeSoal.addEventListener('change', function () {
                const isShortAnswer = this.value === 'ShortAnswer';
                const isMultipleChoice = this.value === 'MultipleChoice';

                opsiPG.style.display = isMultipleChoice ? 'block' : 'none';
                opsiSA.style.display = isShortAnswer ? 'block' : 'none';
                petunjukContainer.style.display = isShortAnswer ? 'block' : 'none';

                // Bersihkan isi hint jika user berpindah dari Isian Singkat ke Pilihan Ganda
                if (!isShortAnswer) {
                    hintInput.value = '';
                }
            });

            tambahJawaban.addEventListener('click', function () {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'sa_answer[]';
                input.classList.add('form-control', 'sa-answer', 'mb-2');
                input.placeholder = 'Masukkan jawaban singkat';
                jawabanContainer.appendChild(input);
                input.focus();
            });

            btnTriggerQuestionImage.addEventListener('click', () => questionImageInput.click());

            questionImageInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    questionFileName.value = file.name;
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewQuestionImage.innerHTML = `<img src="${event.target.result}" alt="Preview Gambar Soal" class="img-fluid rounded shadow-sm mt-2" style="max-height: 180px;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    questionFileName.value = '';
                    renderQuestionUrlPreview();
                }
            });

            questionUrlInput.addEventListener('input', function () {
                if (!questionImageInput.files.length) {
                    renderQuestionUrlPreview();
                }
            });

            function renderQuestionUrlPreview() {
                const url = questionUrlInput.value.trim();
                if (url) {
                    previewQuestionImage.innerHTML = `<img src="${url}" alt="Preview URL Gambar" class="img-fluid rounded shadow-sm mt-2" style="max-height: 180px;" onerror="this.remove();">`;
                } else {
                    previewQuestionImage.innerHTML = '';
                }
            }

            btnClearQuestionImage.addEventListener('click', function () {
                questionImageInput.value = '';
                questionFileName.value = '';
                questionUrlInput.value = '';
                previewQuestionImage.innerHTML = '';
            });

            document.querySelectorAll('.option-card').forEach(card => {
                const triggerBtn = card.querySelector('.btn-trigger-opt-file');
                const fileInput = card.querySelector('.opt-file-input');
                const fileNameInput = card.querySelector('.opt-file-name');
                const urlInput = card.querySelector('.opt-url-input');
                const previewEl = card.querySelector('.opt-preview');
                const clearBtn = card.querySelector('.btn-clear-opt-image');

                triggerBtn.addEventListener('click', () => fileInput.click());

                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        fileNameInput.value = file.name;
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            previewEl.innerHTML = `<img src="${event.target.result}" class="img-fluid rounded shadow-sm mt-1" style="max-height: 100px;">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fileNameInput.value = '';
                        renderOptUrlPreview();
                    }
                });

                urlInput.addEventListener('input', function () {
                    if (!fileInput.files.length) {
                        renderOptUrlPreview();
                    }
                });

                function renderOptUrlPreview() {
                    const url = urlInput.value.trim();
                    if (url) {
                        previewEl.innerHTML = `<img src="${url}" class="img-fluid rounded shadow-sm mt-1" style="max-height: 100px;" onerror="this.remove();">`;
                    } else {
                        previewEl.innerHTML = '';
                    }
                }

                clearBtn.addEventListener('click', function () {
                    fileInput.value = '';
                    fileNameInput.value = '';
                    urlInput.value = '';
                    previewEl.innerHTML = '';
                });
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: '#3b82f6',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = '{{ route('tampilanSoal') }}';
                });
            @endif

            form.addEventListener('submit', function (e) {
                submitBtn.disabled = true;

                function fail(msg, focusEl) {
                    e.preventDefault();
                    submitBtn.disabled = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Form Belum Lengkap',
                        text: msg,
                        confirmButtonColor: '#f87171'
                    }).then(() => {
                        if (focusEl) {
                            focusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            focusEl.focus();
                        }
                    });
                }

                const tipe = tipeSoal.value;
                const difficulty = document.getElementById('difficulty').value;
                const questionText = document.getElementById('question_text').value.trim();

                if (!tipe) {
                    return fail('Pilih tipe soal terlebih dahulu.', tipeSoal);
                }

                if (!difficulty) {
                    return fail('Pilih tingkat kesulitan soal terlebih dahulu.', document.getElementById('difficulty'));
                }

                if (!questionText) {
                    return fail('Teks pertanyaan harus diisi.', document.getElementById('question_text'));
                }

                if (tipe === 'MultipleChoice') {
                    const optionInputs = Array.from(document.querySelectorAll('.option-text'));
                    const labels = ['A', 'B', 'C', 'D', 'E'];

                    for (let i = 0; i < optionInputs.length; i++) {
                        if ((optionInputs[i].value || '').trim() === '') {
                            return fail(`Opsi ${labels[i]} belum diisi!`, optionInputs[i]);
                        }
                    }

                    const mcAnswer = document.getElementById('mc_answer').value;
                    if (!mcAnswer) {
                        return fail('Silakan pilih jawaban benar untuk soal pilihan ganda.', document.getElementById('mc_answer'));
                    }
                } else if (tipe === 'ShortAnswer') {
                    const saInputs = Array.from(document.querySelectorAll('.sa-answer'));
                    const anyFilled = saInputs.some(i => (i.value || '').trim() !== '');
                    if (!anyFilled) {
                        return fail('Masukkan minimal satu jawaban untuk isian singkat.', saInputs[0] || document.getElementById('question_text'));
                    }
                }

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';
            });
        });
    </script>
@endsection