@extends('layouts.main')
@section('dataSoal', request()->is('soal/edit/*') ? 'active' : '')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">

                <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h3 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square"></i>
                                Edit Soal
                            </h3>

                            <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoEditSoal"
                                title="Informasi Edit Soal">
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

                <form id="editSoalForm" action="{{ route('updateSoal', $data->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    @php
                        $question = json_decode($data->question);
                        $mcOption = $data->MC_option ? json_decode($data->MC_option, true) : [];
                        $saAnswer = $data->SA_answer ? json_decode($data->SA_answer, true) : [];
                        $labels = ['a', 'b', 'c', 'd', 'e'];
                    @endphp

                    <!-- BARIS 1 -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipe Soal</label>
                            <select name="type" class="form-select" id="tipeSoal" disabled>
                                <option value="MultipleChoice" {{ $data->type == 'MultipleChoice' ? 'selected' : '' }}>Pilihan
                                    Ganda</option>
                                <option value="ShortAnswer" {{ $data->type == 'ShortAnswer' ? 'selected' : '' }}>Isian Singkat
                                </option>
                            </select>
                            <small class="text-muted">Jenis soal tidak dapat diubah.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tingkat Kesulitan</label>
                            <select name="difficulty" class="form-select" id="difficulty" required>
                                <option value="sangat mudah" {{ $data->difficulty == 'sangat mudah' ? 'selected' : '' }}>
                                    Sangat Mudah</option>
                                <option value="mudah" {{ $data->difficulty == 'mudah' ? 'selected' : '' }}>Mudah</option>
                                <option value="sedang" {{ $data->difficulty == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="sulit" {{ $data->difficulty == 'sulit' ? 'selected' : '' }}>Sulit</option>
                                <option value="sangat sulit" {{ $data->difficulty == 'sangat sulit' ? 'selected' : '' }}>
                                    Sangat Sulit</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Topik</label>
                            <select name="id_topic" class="form-select" id="id_topic">
                                <option value="" data-level="default">-- Pilih Topik --</option>
                                @foreach($topics as $t)
                                    <option value="{{ $t->id }}" data-level="{{ $t->level }}" {{ (isset($data->id_topic) && $data->id_topic == $t->id) ? 'selected' : '' }}>
                                        {{ $t->title }} ({{ $t->level }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Topik yang tampil hanya untuk mata pelajaran/kls yang Anda ampu.</div>
                        </div>

                        <div class="col-md-3">
                            @php
                                $tags = $data->tags ? json_decode($data->tags, true) : [];
                            @endphp

                            <label class="form-label fw-semibold">Tags</label>

                            <input type="text" name="tags" id="tags" class="form-control"
                                value="{{ is_array($tags) ? implode(', ', $tags) : '' }}"
                                placeholder="Contoh: vlan, switch, jaringan">

                            <div class="form-text">
                                Pisahkan beberapa tag dengan koma.
                            </div>
                        </div>
                    </div>

                    {{-- PETUNJUK / HINT (Hanya jika tipe soal adalah Isian Singkat) --}}
                    @if($data->type == 'ShortAnswer')
                        <div class="row mt-3" id="petunjukContainer">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Petunjuk / Hint (opsional)</label>

                                <textarea name="hint" id="hint" class="form-control" rows="2"
                                    placeholder="Tuliskan petunjuk atau bantuan pengerjaan soal di sini...">{{ $data->hint ?? '' }}</textarea>

                                <div class="form-text">
                                    Petunjuk dapat membantu siswa ketika mengalami kesulitan saat mengerjakan soal isian
                                    singkat.
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- BARIS 2 -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teks Pertanyaan</label>
                            <textarea name="question_text" id="question_text" class="form-control" rows="4"
                                required>{{ $question->text ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Soal (opsional)</label>

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
                                value="{{ $question->URL ?? '' }}" placeholder="Atau masukkan URL gambar">

                            <div id="previewQuestionImage" class="mt-2 text-center">
                                @if(!empty($question->URL))
                                    <img src="{{ $question->URL }}" class="img-fluid rounded shadow-sm"
                                        style="max-height: 180px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- MULTIPLE CHOICE --}}
                    @if($data->type == 'MultipleChoice')
                        <hr class="mt-4">
                        <h5 class="fw-bold text-secondary mb-3">
                            <i class="bi bi-list-check me-2"></i> Pilihan Jawaban
                        </h5>

                        <div class="row">
                            @foreach($labels as $i => $label)
                                @php
                                    $teks = $mcOption[$i][$label]['teks'] ?? '';
                                    $url = $mcOption[$i][$label]['url'] ?? '';
                                @endphp

                                <div class="col-md-4 mb-3 option-card-col" id="col-option-{{ $label }}" data-opt="{{ $label }}">
                                    <div class="card shadow-sm border-0 h-100 option-card">
                                        <div class="card-body">
                                            <label class="fw-semibold mb-2">Opsi {{ strtoupper($label) }}</label>
                                            <input type="text" name="option_text[]" class="form-control option-text mb-2"
                                                value="{{ $teks }}" placeholder="Teks opsi {{ strtoupper($label) }}">

                                            <div class="option-image-wrapper">
                                                <div class="input-group input-group-sm mb-2">
                                                    <button class="btn btn-outline-secondary btn-trigger-opt-file" type="button">
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
                                                    class="form-control form-control-sm opt-url-input mb-2" value="{{ $url }}"
                                                    placeholder="URL gambar (opsional)">
                                                <div class="opt-preview text-center">
                                                    @if(!empty($url))
                                                        <img src="{{ $url }}" class="img-fluid rounded shadow-sm mt-1"
                                                            style="max-height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Jawaban Benar --}}
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">Jawaban Benar</label>
                                        <select name="mc_answer" id="mc_answer" class="form-select">
                                            <option value="">-- Pilih Jawaban --</option>
                                            @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                                <option value="{{ $opt }}" id="mc-ans-opt-{{ $opt }}" {{ $data->MC_answer == $opt ? 'selected' : '' }}>
                                                    {{ strtoupper($opt) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SHORT ANSWER --}}
                    @if($data->type == 'ShortAnswer')
                        <hr>
                        <h5 class="fw-bold text-secondary mb-3">
                            <i class="bi bi-pencil-square me-2"></i> Jawaban Benar
                        </h5>

                        <div id="opsiIsianSingkat">
                            @if(count($saAnswer))
                                @foreach($saAnswer as $ans)
                                    <input type="text" name="sa_answer[]" class="form-control sa-answer mb-2" value="{{ $ans }}"
                                        placeholder="Masukkan jawaban singkat">
                                @endforeach
                            @else
                                <input type="text" name="sa_answer[]" class="form-control sa-answer mb-2"
                                    placeholder="Masukkan jawaban singkat">
                            @endif

                            <button type="button" id="tambahJawaban" class="btn btn-outline-secondary btn-sm mt-2">
                                <i class="bi bi-plus-circle"></i> Tambah Jawaban
                            </button>
                        </div>
                    @endif

                    {{-- BUTTONS --}}
                    <div class="text-end mt-4">
                        <button type="submit" id="submitBtn" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('tampilanSoal') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembali
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- MODAL INFO EDIT SOAL --}}
    <div class="modal fade" id="modalInfoEditSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-4">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Informasi Edit Soal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <section class="mb-4">
                        <h6 class="fw-bold text-primary">Tujuan Halaman</h6>
                        <p class="text-muted mb-0">
                            Halaman <strong>Edit Soal</strong> digunakan untuk memperbarui isi soal yang sudah dibuat,
                            seperti teks pertanyaan, tingkat kesulitan, topik, petunjuk, serta jawaban.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h6 class="fw-bold text-primary">Jumlah Opsi Pilihan Ganda</h6>
                        <ul class="text-muted mb-0">
                            <li><b>SD / MI</b>: 3 Opsi Jawaban (A – C)</li>
                            <li><b>SMP / MTs</b>: 4 Opsi Jawaban (A – D)</li>
                            <li><b>SMA / SMK / MA / PT</b>: 5 Opsi Jawaban (A – E)</li>
                        </ul>
                    </section>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const idTopicSelect = document.getElementById('id_topic');
            const mcAnswerSelect = document.getElementById('mc_answer');
            const form = document.getElementById('editSoalForm');
            const submitBtn = document.getElementById('submitBtn');

            // 🔹 PENYESUAIAN JUMLAH OPSI JAWABAN SESUAI LEVEL TOPIK
            function adjustOptionCount() {
                if (!idTopicSelect) return;
                const selectedOption = idTopicSelect.options[idTopicSelect.selectedIndex];
                const level = selectedOption ? selectedOption.getAttribute('data-level') : 'default';

                let maxOptions = 5; // Default (SMA/SMK/MA/PT)

                if (['SD', 'MI'].includes(level)) {
                    maxOptions = 3;
                } else if (['SMP', 'MTs'].includes(level)) {
                    maxOptions = 4;
                } else if (['SMA', 'SMK', 'MA', 'PT'].includes(level)) {
                    maxOptions = 5;
                }

                const labels = ['a', 'b', 'c', 'd', 'e'];

                labels.forEach((opt, index) => {
                    const colEl = document.getElementById(`col-option-${opt}`);
                    const ansOptEl = document.getElementById(`mc-ans-opt-${opt}`);

                    if (index < maxOptions) {
                        if (colEl) colEl.style.display = 'block';
                        if (ansOptEl) ansOptEl.style.display = 'block';
                    } else {
                        if (colEl) {
                            colEl.style.display = 'none';
                            const input = colEl.querySelector('.option-text');
                            if (input) input.value = '';
                        }
                        if (ansOptEl) {
                            ansOptEl.style.display = 'none';
                            if (mcAnswerSelect && mcAnswerSelect.value === opt) {
                                mcAnswerSelect.value = '';
                            }
                        }
                    }
                });
            }

            idTopicSelect?.addEventListener('change', adjustOptionCount);
            adjustOptionCount(); // Jalankan sekali saat halaman dimuat

            // GAMBAR SOAL UTAMA
            const btnTriggerQuestionImage = document.getElementById('btnTriggerQuestionImage');
            const questionImageInput = document.getElementById('questionImageInput');
            const questionFileName = document.getElementById('questionFileName');
            const questionUrlInput = document.getElementById('question_url');
            const previewQuestionImage = document.getElementById('previewQuestionImage');
            const btnClearQuestionImage = document.getElementById('btnClearQuestionImage');

            btnTriggerQuestionImage?.addEventListener('click', () => questionImageInput.click());

            questionImageInput?.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    questionFileName.value = file.name;
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewQuestionImage.innerHTML = `<img src="${event.target.result}" class="img-fluid rounded shadow-sm mt-2" style="max-height: 180px;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    questionFileName.value = '';
                    renderQuestionUrlPreview();
                }
            });

            questionUrlInput?.addEventListener('input', function () {
                if (!questionImageInput.files.length) {
                    renderQuestionUrlPreview();
                }
            });

            function renderQuestionUrlPreview() {
                const url = questionUrlInput.value.trim();
                if (url) {
                    previewQuestionImage.innerHTML = `<img src="${url}" class="img-fluid rounded shadow-sm mt-2" style="max-height: 180px;" onerror="this.remove();">`;
                } else {
                    previewQuestionImage.innerHTML = '';
                }
            }

            btnClearQuestionImage?.addEventListener('click', function () {
                questionImageInput.value = '';
                questionFileName.value = '';
                questionUrlInput.value = '';
                previewQuestionImage.innerHTML = '';
            });

            // GAMBAR OPSI JAWABAN (A-E)
            document.querySelectorAll('.option-card').forEach(card => {
                const triggerBtn = card.querySelector('.btn-trigger-opt-file');
                const fileInput = card.querySelector('.opt-file-input');
                const fileNameInput = card.querySelector('.opt-file-name');
                const urlInput = card.querySelector('.opt-url-input');
                const previewEl = card.querySelector('.opt-preview');
                const clearBtn = card.querySelector('.btn-clear-opt-image');

                triggerBtn?.addEventListener('click', () => fileInput.click());

                fileInput?.addEventListener('change', function (e) {
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

                urlInput?.addEventListener('input', function () {
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

                clearBtn?.addEventListener('click', function () {
                    fileInput.value = '';
                    fileNameInput.value = '';
                    urlInput.value = '';
                    previewEl.innerHTML = '';
                });
            });

            // Tambah field isian singkat
            document.getElementById('tambahJawaban')?.addEventListener('click', function () {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'sa_answer[]';
                input.classList.add('form-control', 'sa-answer', 'mb-2');
                input.placeholder = 'Masukkan jawaban singkat';
                this.parentElement.insertBefore(input, this);
                input.focus();
            });

            // SweetAlert flash success
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: '#3b82f6',
                    allowOutsideClick: false
                });
            @endif

            // Validasi Form
            form?.addEventListener('submit', function (e) {
                submitBtn.disabled = true;

                function fail(msg, el) {
                    e.preventDefault();
                    submitBtn.disabled = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Form Belum Lengkap',
                        text: msg,
                        confirmButtonColor: '#f87171'
                    }).then(() => {
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            el.focus();
                        }
                    });
                }

                const tipe = "{{ $data->type }}";
                const questionText = (document.getElementById('question_text')?.value || '').trim();

                if (!questionText) {
                    return fail('Teks pertanyaan harus diisi.', document.getElementById('question_text'));
                }

                if (tipe === 'MultipleChoice') {
                    const visibleCols = Array.from(document.querySelectorAll('.option-card-col')).filter(col => col.style.display !== 'none');

                    for (let i = 0; i < visibleCols.length; i++) {
                        const optText = visibleCols[i].querySelector('.option-text').value.trim();
                        const optLabel = visibleCols[i].getAttribute('data-opt').toUpperCase();
                        if (!optText) {
                            return fail(`Opsi ${optLabel} belum diisi!`, visibleCols[i].querySelector('.option-text'));
                        }
                    }

                    const mcAnswer = (document.getElementById('mc_answer')?.value || '');
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