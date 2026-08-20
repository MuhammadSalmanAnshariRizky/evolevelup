@extends('layouts.main')
@section('dataAktivitas', request()->is('guru/aktivitas/*/atur-soal') ? 'active' : '')

@section('content')
    <div class="container mt-4">

        <div class="d-flex align-items-center gap-2 mb-3">
            <h3 class="fw-bold mb-0 d-flex align-items-center gap-2 flex-wrap">
                Atur Soal untuk: {{ $aktivitas->title }}

                @if($aktivitas->addaptive === 'yes')
                    <span class="badge bg-success">
                        <i class="bi bi-cpu me-1"></i> Adaptif
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="bi bi-slash-circle me-1"></i> Non-Adaptif
                    </span>
                @endif
            </h3>

            <button type="button"
                class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoAturSoal"
                title="Informasi Pengaturan Soal">
                <i class="bi bi-info-lg"></i>
            </button>
        </div>


        <a href="{{ url('/dataaktivitas') }}" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        {{-- PETUNJUK --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="small text-muted">Petunjuk</div>
                <p class="mb-0">
                    Soal terpilih akan ditampilkan di bawah. Gunakan tombol <strong>Lihat Soal</strong> untuk memilih soal
                    dari daftar (manual atau otomatis). Setelah memilih di modal, tekan <strong>Terapkan ke
                        Aktivitas</strong>
                    untuk memindahkan hasil ke halaman ini. Gunakan tombol <strong>Simpan Pilihan</strong> untuk menyimpan
                    ke database.
                </p>
            </div>
        </div>

        {{-- SOAL TERPILIH --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary text-white fw-semibold">Soal Terpilih</div>

            <div class="card-body" id="selectedArea" style="min-height:240px; max-height:420px; overflow-y:auto;">
                @if($selectedQuestions->isEmpty())
                    <div id="noSelectedPlaceholder" class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-x" style="font-size:2rem"></i>
                        <div class="mt-2">Belum ada soal.</div>
                    </div>
                @else
                    @foreach($selectedQuestions as $s)
                        @php $sData = json_decode($s->question); @endphp
                        <div class="p-2 border rounded mb-2 bg-light d-flex justify-content-between align-items-start"
                            id="selectedItem-{{ $s->id }}">
                            <div>
                                <small class="text-muted">{{ $s->difficulty }} — {{ $s->type }}</small>
                                <div class="mt-1">{{ Str::limit($sData->text ?? '-', 240) }}</div>
                            </div>

                            <button class="btn btn-sm btn-danger" onclick="hapusDariTerpilih({{ $s->id }})">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-outline-primary flex-fill" data-bs-toggle="modal" data-bs-target="#soalModal">
                    <i class="bi bi-list-ul me-1"></i> Lihat Soal
                </button>

                <button class="btn btn-danger" onclick="clearAll()">
                    <i class="bi bi-trash me-1"></i> Hapus Semua Pilihan
                </button>

                <button class="btn btn-success" onclick="simpanPilihan()">
                    <i class="bi bi-save me-1"></i> Simpan Pilihan
                </button>
            </div>
        </div>

        {{-- INFORMASI COUNT --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-column flex-md-row justify-content-start align-items-md-center gap-3">
                    {{-- Total Soal --}}
                    <div class="px-3">
                        <div class="small text-muted">Total Soal Terpilih</div>
                        <div class="fw-bold text-primary fs-4" id="currentTotal">
                            {{ $selectedQuestions->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL: Daftar Soal --}}
    <div class="modal fade" id="soalModal" tabindex="-1" aria-labelledby="soalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Soal — {{ $aktivitas->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="card border-0 mb-0">
                        <div class="card-body p-3">

                            {{-- Kontrol atas --}}
                            <div class="d-flex flex-column flex-md-row gap-3 align-items-start mb-3">
                                <div class="w-100">
                                    <h6 class="mb-2 text-muted">Pilih atau Masukkan Jumlah Soal Minimum</h6>

                                    @php $savedJumlah = $aktivitas->jumlah_soal ?? null; @endphp

                                    {{-- Pilihan Button Group Radio --}}
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="jumlah soal">
                                            @foreach ([5, 10, 15, 20, 25, 30] as $opt)
                                                <label
                                                    class="btn btn-outline-primary {{ $savedJumlah == $opt ? 'active' : '' }}">
                                                    <input type="radio" name="modalJumlahRadio" value="{{ $opt }}" class="me-1"
                                                        {{ $savedJumlah == $opt ? 'checked' : '' }}>
                                                    {{ $opt }}
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- Input Manual / Custom Angka --}}
                                        <div class="input-group input-group-sm" style="width: 180px;">
                                            <span class="input-group-text">Custom</span>
                                            <input type="number" id="customJumlahInput" class="form-control"
                                                placeholder="Cth: 12" min="1"
                                                value="{{ !in_array($savedJumlah, [5, 10, 15, 20, 25, 30]) ? $savedJumlah : '' }}">
                                        </div>
                                    </div>


                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-primary btn-sm" id="btnAmbilModal">Ambil Soal Acak</button>

                                        <button class="btn btn-outline-primary btn-sm" id="btnSelectAllModal"
                                            title="Pilih semua soal pada daftar">
                                            <i class="bi bi-check2-all me-1"></i> Ambil Semua
                                        </button>

                                        <button class="btn btn-outline-secondary btn-sm"
                                            id="btnUnselectAllModal">Bersihkan</button>
                                    </div>
                                </div>


                            </div>

                            <hr class="my-2">

                            {{-- Header daftar soal --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-muted">Daftar Semua Soal</h6>
                                <div class="small text-muted">Total: <span id="modalTotalCount">{{ $questions->count() }}
                                        Soal</span></div>
                            </div>

                            {{-- tabel scrollable --}}
                            <div style="max-height:540px; overflow:auto;">
                                <table class="table table-sm table-bordered mb-0 align-middle">
                                    <thead class="table-light text-center sticky-top" style="top:0; z-index:1;">
                                        <tr>
                                            <th style="width:84px">Aksi</th>
                                            <th style="width:56px">No</th>
                                            <th style="min-width:120px">Tipe</th>
                                            <th style="min-width:100px">Kesulitan</th>
                                            <th>Pertanyaan</th>
                                        </tr>
                                    </thead>

                                    <tbody id="modalQuestionList">
                                        @foreach ($questions as $q)
                                            @php $qData = json_decode($q->question); @endphp
                                            <tr data-qid="{{ $q->id }}" id="modalRow-{{ $q->id }}">
                                                <td class="text-center">
                                                    <button
                                                        class="btn btn-sm {{ in_array($q->id, $selectedIds) ? 'btn-danger' : 'btn-success' }}"
                                                        onclick="modalToggleSelect({{ $q->id }})"
                                                        aria-label="{{ in_array($q->id, $selectedIds) ? 'Unselect' : 'Select' }}">
                                                        <i
                                                            class="bi {{ in_array($q->id, $selectedIds) ? 'bi-x-circle' : 'bi-plus-circle' }}"></i>
                                                    </button>
                                                </td>

                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $q->type }}</td>
                                                <td>{{ $q->difficulty }}</td>
                                                <td style="white-space:normal;">{{ Str::limit($qData->text ?? '-', 300) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="me-auto text-muted small">Pilih soal lalu tekan <strong>Terapkan ke Aktivitas</strong></div>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" id="btnApplyToActivity">Terapkan ke Aktivitas</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INFO ATUR SOAL --}}
    <div class="modal fade" id="modalInfoAturSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Panduan Mengatur Soal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted">
                        Halaman <strong>Atur Soal</strong> digunakan untuk menentukan
                        soal-soal yang akan digunakan pada aktivitas
                        <strong>{{ $aktivitas->title }}</strong>.
                    </p>
                    <hr>

                    <h6 class="fw-bold text-primary">
                        <i class="bi bi-1-circle me-1"></i>
                        Langkah 1 – Menentukan Jumlah Soal
                    </h6>
                    <ul>
                        <li>Langkah pertama yang <strong>wajib dilakukan</strong> adalah memilih atau mengetik
                            <strong>jumlah soal minimum</strong> (menggunakan pilihan tombol atau input custom).
                        </li>
                        <li>Jumlah soal ini menjadi acuan untuk validasi dan penentuan batas saat mengambil soal secara
                            otomatis.
                        </li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-success">
                        <i class="bi bi-2-circle me-1"></i>
                        Langkah 2 – Memilih Soal
                    </h6>
                    <ul>
                        <li>Klik tombol <strong>Lihat Soal</strong> untuk membuka daftar soal.</li>
                        <li>Guru dapat memilih soal dengan cara:
                            <ul>
                                <li><strong>Manual</strong>: klik tombol tambah (+) pada soal</li>
                                <li><strong>Acak Otomatis</strong>: klik tombol <strong>Ambil Soal Acak</strong></li>
                                <li><strong>Ambil Semua</strong>: memilih seluruh soal di daftar</li>
                            </ul>
                        </li>
                        <li>Perubahan di dalam modal belum tersimpan sampai diterapkan.</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-info">
                        <i class="bi bi-3-circle me-1"></i>
                        Langkah 3 – Terapkan & Simpan
                    </h6>
                    <ul>
                        <li>Klik tombol <strong>Terapkan ke Aktivitas</strong> di modal.</li>
                        <li>Soal yang dipilih akan muncul di bagian <strong>Soal Terpilih</strong>.</li>
                        <li>Guru masih dapat menghapus atau menyesuaikan soal.</li>
                        <li>Klik <strong>Simpan Pilihan</strong> untuk mengunci bank soal ini ke dalam database.</li>
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


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // GLOBALS
        const ACTIVITAS_ID = {{ $aktivitas->id }};
        const CSRF = "{{ csrf_token() }}";

        // Ambil total soal maksimal yang ada di database/daftar saat ini
        const MAX_QUESTIONS_AVAILABLE = {{ $questions->count() }};

        // modalSelected: array id soal yang dipilih di modal (temp)
        let modalSelected = @json($selectedIds);
        // window.lastPicked tetap menyimpan selection yang sudah disimpan/applied di halaman
        window.lastPicked = @json($selectedIds);

        // helper escape
        function escapeHtml(s) {
            if (!s) return '';
            return s.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
        }

        // --- helper: fetch single question detail (caches results) ---
        const _questionCache = {};
        async function fetchQuestionById(id) {
            if (_questionCache[id]) return _questionCache[id];
            try {
                const res = await fetch(`/get-question/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Network');
                const j = await res.json();
                _questionCache[id] = j;
                return j;
            } catch (e) {
                console.error('fetchQuestionById error', id, e);
                return null;
            }
        }

        // --- render selected area on page (soal terpilih)
        async function renderSelectedArea(ids, questionsMap = null) {
            const area = document.getElementById('selectedArea');
            if (!area) return;

            if (!ids || ids.length === 0) {
                area.innerHTML = `<div id="noSelectedPlaceholder" class="text-center text-muted py-4">
                                            <i class="bi bi-clipboard-x" style="font-size:2rem"></i>
                                            <div class="mt-2">Belum ada soal.</div>
                                         </div>`;
                document.getElementById('currentTotal') && (document.getElementById('currentTotal').innerText = 0);
                return;
            }

            let html = '';
            ids.forEach(id => {
                const q = questionsMap && questionsMap[id] ? questionsMap[id] : null;
                const smallText = q ? (q.difficulty + ' — ' + q.type) : '';
                const bodyText = q ? escapeHtml(q.text) : `Memuat soal #${id}...`;
                html += `<div class="p-2 border rounded mb-2 bg-light d-flex justify-content-between align-items-start" id="selectedItem-${id}">
                                <div>
                                    <small class="text-muted">${smallText}</small>
                                    <div class="mt-1" id="selectedText-${id}">${bodyText}</div>
                                </div>
                                <button class="btn btn-sm btn-danger" onclick="hapusDariTerpilih(${id})">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>`;
            });
            area.innerHTML = html;
            document.getElementById('currentTotal') && (document.getElementById('currentTotal').innerText = ids.length);

            const toFetch = ids.filter(id => {
                const q = questionsMap && questionsMap[id] ? questionsMap[id] : null;
                return !(q && q.text);
            });

            if (toFetch.length === 0) return;

            await Promise.all(toFetch.map(async id => {
                const q = await fetchQuestionById(id);
                if (q && q.text) {
                    const el = document.getElementById(`selectedText-${id}`);
                    if (el) el.innerHTML = escapeHtml(q.text);
                    const smallEl = document.querySelector(`#selectedItem-${id} small.text-muted`);
                    if (smallEl && q.difficulty && q.type) smallEl.innerText = `${q.difficulty} — ${q.type}`;
                } else {
                    const el = document.getElementById(`selectedText-${id}`);
                    if (el) el.innerHTML = `Soal #${id}`;
                }
            }));
        }

        // ketika modal dibuka: sinkronkan tombol aksi di modal dengan modalSelected
        const soalModalEl = document.getElementById('soalModal');
        if (soalModalEl) {
            soalModalEl.addEventListener('show.bs.modal', function () {
                syncModalRowButtons();
            });
        }

        // toggle select di modal (klik tombol + / x)
        function modalToggleSelect(id) {
            id = parseInt(id);
            const row = document.getElementById('modalRow-' + id);
            if (!row) return;
            const btn = row.querySelector('button');

            if (modalSelected.includes(id)) {
                modalSelected = modalSelected.filter(x => x !== id);
                if (btn) {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-success');
                    btn.innerHTML = `<i class="bi bi-plus-circle"></i>`;
                }
            } else {
                modalSelected.push(id);
                if (btn) {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-danger');
                    btn.innerHTML = `<i class="bi bi-x-circle"></i>`;
                }
            }
        }

        // helper: update modal row buttons based on modalSelected
        function syncModalRowButtons() {
            document.querySelectorAll('#modalQuestionList tr').forEach(tr => {
                const id = parseInt(tr.dataset.qid);
                const btn = tr.querySelector('button');
                if (!btn) return;
                if (modalSelected.includes(id)) {
                    btn.classList.remove('btn-success'); btn.classList.add('btn-danger');
                    btn.innerHTML = `<i class="bi bi-x-circle"></i>`;
                } else {
                    btn.classList.remove('btn-danger'); btn.classList.add('btn-success');
                    btn.innerHTML = `<i class="bi bi-plus-circle"></i>`;
                }
            });
        }

        // BERSIHKAN semua pilihan di modal
        const btnUnselect = document.getElementById('btnUnselectAllModal');
        if (btnUnselect) {
            btnUnselect.addEventListener('click', function () {
                modalSelected = [];
                syncModalRowButtons();
            });
        }

        // Terapkan pilihan di modal -> ke halaman
        const btnApply = document.getElementById('btnApplyToActivity');
        if (btnApply) {
            btnApply.addEventListener('click', function () {

                // ✅ VALIDASI JUMLAH SOAL & CEK MAKSIMAL DATABASE
                const jumlahSoal = readCheckedN();
                if (!jumlahSoal || jumlahSoal <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah soal belum ditentukan',
                        text: 'Silakan pilih atau ketik jumlah soal minimum terlebih dahulu.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (jumlahSoal > MAX_QUESTIONS_AVAILABLE) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Melebihi Batas Soal',
                        html: `Jumlah minimal soal yang diminta (<b>${jumlahSoal}</b>) melebihi total soal yang tersedia di database/daftar (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
                    });
                    return;
                }

                if (!modalSelected || modalSelected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Soal belum dipilih',
                        text: 'Silakan pilih soal terlebih dahulu.',
                    });
                    return;
                }

                fetch("{{ url('/guru/simpan-atur-soal/' . $aktivitas->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF
                    },
                    body: JSON.stringify({
                        id_question: modalSelected,
                        jumlah_soal: jumlahSoal
                    })
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            let questionsMap = {};
                            document.querySelectorAll('#modalQuestionList tr').forEach(tr => {
                                const id = parseInt(tr.dataset.qid);
                                const tds = tr.querySelectorAll('td');
                                questionsMap[id] = {
                                    id,
                                    type: tds[2]?.innerText.trim() || '',
                                    difficulty: tds[3]?.innerText.trim() || '',
                                    text: tds[4]?.innerText.trim() || ''
                                };
                            });

                            renderSelectedArea(modalSelected, questionsMap);
                            window.lastPicked = modalSelected.slice();

                            const modal = document.getElementById('soalModal');
                            if (modal) {
                                const inst = bootstrap.Modal.getInstance(modal);
                                if (inst) inst.hide();
                            }
                            Swal.fire('Berhasil', res.message || 'Soal diterapkan ke aktivitas.', 'success');
                        } else {
                            Swal.fire('Gagal', res.message || 'Tidak dapat menyimpan pilihan.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan pilihan.', 'error');
                    });
            });
        }


        // HAPUS dari halaman
        function hapusDariTerpilih(id) {
            fetch("{{ url('/guru/hapus-soal-manual/' . $aktivitas->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": CSRF
                },
                body: JSON.stringify({ id_question: id })
            })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        window.lastPicked = window.lastPicked.filter(x => x !== id);
                        modalSelected = modalSelected.filter(x => x !== id);
                        const el = document.getElementById('selectedItem-' + id);
                        if (el) el.remove();
                        syncModalRowButtons();

                        if ((window.lastPicked || []).length === 0) {
                            renderSelectedArea([]);
                        }
                        document.getElementById('currentTotal') && (document.getElementById('currentTotal').innerText = (window.lastPicked || []).length);
                    } else {
                        Swal.fire('Gagal', res.message || 'Tidak dapat menghapus soal.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                });
        }

        // CLEAR ALL
        function clearAll() {
            Swal.fire({
                title: "Hapus Semua?",
                text: "Semua soal terpilih akan dihapus.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch("{{ url('/guru/clear-all/' . $aktivitas->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF
                    }
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            modalSelected = [];
                            window.lastPicked = [];
                            renderSelectedArea([]);
                            syncModalRowButtons();
                            document.getElementById('currentTotal') && (document.getElementById('currentTotal').innerText = 0);
                            Swal.fire("Berhasil!", "Semua soal terpilih telah dihapus.", "success");
                        } else {
                            Swal.fire('Gagal', res.message || 'Tidak dapat menghapus semua.', 'error');
                        }
                    });
            });
        }

        let selectedN = @json($aktivitas->jumlah_soal ?? null);

        // Fungsi pembaca jumlah soal yang mengakomodasi Radio maupun Input Custom
        function readCheckedN() {
            const customInput = document.getElementById('customJumlahInput');
            if (customInput && customInput.value.trim() !== '') {
                return parseInt(customInput.value, 10);
            }
            const modalRadio = document.querySelector('input[name="modalJumlahRadio"]:checked');
            if (modalRadio) return parseInt(modalRadio.value, 10);
            return selectedN || null;
        }

        function clearRadioActiveVisuals() {
            document.querySelectorAll('input[name="modalJumlahRadio"]').forEach(r => {
                const lbl = r.closest('label');
                if (lbl) lbl.classList.remove('active');
                if (lbl) lbl.setAttribute('aria-pressed', 'false');
            });
        }

        function attachRadioHandlers() {
            const customInput = document.getElementById('customJumlahInput');

            document.querySelectorAll('input[name="modalJumlahRadio"]').forEach(r => {
                r.addEventListener('change', function () {
                    selectedN = parseInt(this.value, 10);
                    clearRadioActiveVisuals();
                    const lbl = this.closest('label');
                    if (lbl) {
                        lbl.classList.add('active');
                        lbl.setAttribute('aria-pressed', 'true');
                    }
                    if (customInput) customInput.value = '';
                });

                const lbl = r.closest('label');
                if (lbl) {
                    lbl.addEventListener('click', function () {
                        setTimeout(() => {
                            if (r.checked) {
                                clearRadioActiveVisuals();
                                lbl.classList.add('active');
                                lbl.setAttribute('aria-pressed', 'true');
                                selectedN = parseInt(r.value, 10);
                                if (customInput) customInput.value = '';
                            }
                        }, 1);
                    });
                }
            });

            if (customInput) {
                customInput.addEventListener('input', function () {
                    if (this.value.trim() !== '') {
                        clearRadioActiveVisuals();
                        document.querySelectorAll('input[name="modalJumlahRadio"]').forEach(r => {
                            r.checked = false;
                        });
                        selectedN = parseInt(this.value, 10);
                    }
                });
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (selectedN) {
                const savedRadio = document.querySelector(
                    `input[name="modalJumlahRadio"][value="${selectedN}"]`
                );
                const customInput = document.getElementById('customJumlahInput');

                if (savedRadio) {
                    savedRadio.checked = true;
                    clearRadioActiveVisuals();
                    const lbl = savedRadio.closest("label");
                    if (lbl) {
                        lbl.classList.add("active");
                        lbl.setAttribute('aria-pressed', 'true');
                    }
                } else if (customInput && selectedN) {
                    customInput.value = selectedN;
                }
            }
            attachRadioHandlers();
        });


        // AMBIL SOAL OTOMATIS
        const btnAmbil = document.getElementById('btnAmbilModal');
        if (btnAmbil) {
            btnAmbil.addEventListener('click', function () {
                const jumlah = readCheckedN();
                if (!jumlah || jumlah <= 0) {
                    Swal.fire('Pilih atau masukkan jumlah soal terlebih dahulu', '', 'warning');
                    return;
                }

                // Validasi jika jumlah soal melebihi total soal di database
                if (jumlah > MAX_QUESTIONS_AVAILABLE) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Melebihi Batas Soal',
                        html: `Jumlah minimal soal yang diminta (<b>${jumlah}</b>) melebihi total soal yang tersedia di database/daftar (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
                    });
                    return;
                }

                selectedN = jumlah;
                let payload = { jumlah: jumlah };

                fetch("{{ url('/guru/ambil-soal/' . $aktivitas->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF
                    },
                    body: JSON.stringify(payload)
                })
                    .then(r => r.json())
                    .then(res => {
                        if (!res || !res.data) {
                            Swal.fire('Gagal mengambil soal', '', 'error');
                            return;
                        }

                        modalSelected = res.data.map(q => q.id);
                        syncModalRowButtons();
                        Swal.fire('Sukses', 'Soal acak telah dipilih.', 'success');
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan saat mengambil soal.', 'error');
                    });
            });
        }

        // Ambil Semua handler 
        const btnSelectAll = document.getElementById('btnSelectAllModal');
        if (btnSelectAll) {
            btnSelectAll.addEventListener('click', function () {
                const allIds = Array.from(document.querySelectorAll('#modalQuestionList tr'))
                    .map(tr => parseInt(tr.dataset.qid))
                    .filter(Boolean);

                if (!allIds.length) {
                    Swal.fire('Kosong', 'Tidak ada soal pada daftar untuk dipilih.', 'info');
                    return;
                }

                modalSelected = allIds.slice();
                syncModalRowButtons();
                Swal.fire({ icon: 'success', title: 'Semua soal dipilih' });
            });
        }

        // SIMPAN PILIHAN
        function simpanPilihan() {
            let n = readCheckedN();
            if (!n) n = (window.lastPicked || []).length || null;

            if (!n || n <= 0) {
                Swal.fire('Jumlah soal belum ditentukan', 'Silakan pilih atau masukkan jumlah soal minimum terlebih dahulu.', 'warning');
                return;
            }

            // Validasi input melebihi batas database saat klik Simpan Pilihan
            if (n > MAX_QUESTIONS_AVAILABLE) {
                Swal.fire({
                    icon: 'error',
                    title: 'Melebihi Batas Soal',
                    html: `Jumlah minimal soal yang ditentukan (<b>${n}</b>) melebihi total soal yang tersedia di database (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
                });
                return;
            }

            const totalDipilih = (window.lastPicked || []).length;

            if (totalDipilih < n) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah soal belum mencukupi',
                    html: `
                        Jumlah soal yang dipilih: <b>${totalDipilih}</b><br>
                        Jumlah soal minimal yang ditentukan: <b>${n}</b><br><br>
                        Silakan tambah <b>${n - totalDipilih}</b> soal lagi ke dalam aktivitas ini.`
                });
                return;
            }

            fetch("{{ url('/guru/simpan-atur-soal/' . $aktivitas->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": CSRF
                },
                body: JSON.stringify({ id_question: window.lastPicked, jumlah: n })
            })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil Disimpan' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: res.message || '' });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
                });
        }

        // Inisialisasi render awal
        document.addEventListener('DOMContentLoaded', function () {
            renderSelectedArea(window.lastPicked);
            attachRadioHandlers();
            syncModalRowButtons();
        });
    </script>
@endsection