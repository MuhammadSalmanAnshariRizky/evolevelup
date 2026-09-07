@extends('layouts.main')
@section('dataAktivitas', request()->is('guru/aktivitas/*/atur-soal') ? 'active' : '')

@section('content')
    <div class="container mt-4">

        {{-- HEADER HALAMAN --}}
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="fw-bold mb-0 d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-folder-check text-primary"></i>
                    Atur Soal untuk: {{ $aktivitas->title }}

                    @if($aktivitas->addaptive === 'yes')
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                            <i class="bi bi-cpu-fill me-1"></i> Adaptif
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2 rounded-pill">
                            <i class="bi bi-slash-circle me-1"></i> Non-Adaptif
                        </span>
                    @endif
                </h3>

                <button type="button"
                    class="btn btn-sm btn-outline-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                    style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoAturSoal"
                    title="Panduan Pengaturan Soal">
                    <i class="bi bi-question-lg fs-6"></i>
                </button>
            </div>

            <a href="{{ url('/dataaktivitas') }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- PETUNJUK PENGENALAN UI --}}
        <div class="card shadow-sm border-0 rounded-3 mb-3 bg-light">
            <div class="card-body p-3">
                <div class="fw-semibold text-primary mb-1 d-flex align-items-center gap-1">
                    <i class="bi bi-info-circle-fill"></i> Petunjuk Pengaturan Soal
                </div>
                <p class="mb-0 text-muted small">
                    Soal terpilih akan ditampilkan pada kolom <strong>Soal Terpilih</strong> di bawah. Klik tombol
                    <span class="badge bg-primary px-2 py-1 me-1"><i class="bi bi-list-check me-1"></i> Lihat Soal</span>
                    pada <strong>bagian bawah kartu Soal Terpilih</strong> untuk memilih soal dari bank soal (manual maupun
                    acak).
                    Setelah selesai memilih, tekan tombol <strong>Terapkan ke Aktivitas</strong> di dalam modal, lalu tekan
                    tombol
                    <span class="badge bg-success px-2 py-1"><i class="bi bi-save me-1"></i> Simpan Pilihan</span> untuk
                    menyimpan data ke database.
                </p>
            </div>
        </div>

        {{-- SOAL TERPILIH CARD --}}
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div
                class="card-header bg-primary text-white fw-bold py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-card-checklist me-2"></i>Soal Terpilih</span>
                <span class="badge bg-white text-primary fw-semibold" id="headerCountBadge">
                    {{ $selectedQuestions->count() }} Soal
                </span>
            </div>

            <div class="card-body p-3" id="selectedArea" style="min-height:240px; max-height:420px; overflow-y:auto;">
                @if($selectedQuestions->isEmpty())
                    <div id="noSelectedPlaceholder" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard-x text-secondary" style="font-size:2.5rem"></i>
                        <div class="mt-2 fw-semibold">Belum ada soal terpilih.</div>
                        <div class="small">Klik tombol <strong>Lihat Soal</strong> di bawah untuk menambah soal.</div>
                    </div>
                @else
                    @foreach($selectedQuestions as $s)
                        @php $sData = json_decode($s->question); @endphp
                        <div class="p-3 border rounded-3 mb-2 bg-light d-flex justify-content-between align-items-start shadow-sm"
                            id="selectedItem-{{ $s->id }}">
                            <div>
                                <div class="mb-1">
                                    <span class="badge bg-secondary me-1">{{ ucfirst($s->type) }}</span>
                                    <span class="badge bg-info text-dark me-1">{{ ucfirst($s->difficulty) }}</span>
                                    @if($s->tags)
                                        <span class="badge bg-light text-dark border"><i
                                                class="bi bi-tag-fill me-1"></i>{{ $s->tags }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-dark fw-medium">{{ Str::limit($sData->text ?? '-', 240) }}</div>
                            </div>

                            <button class="btn btn-sm btn-outline-danger ms-2 flex-shrink-0"
                                onclick="hapusDariTerpilih({{ $s->id }})" title="Hapus Soal Ini">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- TOMBOL AKSI UTAMA --}}
            <div class="card-footer bg-white p-3 border-top d-flex flex-wrap gap-2">
                <button
                    class="btn btn-primary fw-bold shadow-sm flex-grow-1 py-2 d-flex align-items-center justify-content-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#soalModal" id="btnOpenSoalModal">
                    <i class="bi bi-list-check fs-5"></i> Lihat Soal
                </button>

                <button class="btn btn-outline-danger py-2 px-3 fw-semibold shadow-sm d-flex align-items-center gap-1"
                    onclick="clearAll()">
                    <i class="bi bi-trash3-fill me-1"></i> Hapus Semua Pilihan
                </button>

                <button class="btn btn-success py-2 px-4 fw-bold shadow-sm d-flex align-items-center gap-1"
                    onclick="simpanPilihan()">
                    <i class="bi bi-save me-1"></i> Simpan Pilihan
                </button>
            </div>
        </div>

        {{-- INFORMASI COUNT CARD --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                        <i class="bi bi-card-text fs-3"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-semibold">Total Soal Terpilih</div>
                        <div class="fw-bold text-primary fs-3 mb-0" id="currentTotal">
                            {{ $selectedQuestions->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL: DAFTAR SOAL --}}
    <div class="modal fade" id="soalModal" tabindex="-1" aria-labelledby="soalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="soalModalLabel">
                        <i class="bi bi-collection-fill me-2"></i>Daftar Bank Soal — {{ $aktivitas->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="card border-0 bg-light rounded-3 mb-3">
                        <div class="card-body p-3">

                            {{-- KONTROL ATAS --}}
                            <div class="d-flex flex-column flex-md-row gap-3 align-items-start mb-2">
                                <div class="w-100">
                                    <h6 class="fw-bold text-secondary mb-2">Pilih atau Masukkan Jumlah Soal Minimum</h6>

                                    @php $savedJumlah = $aktivitas->jumlah_soal ?? null; @endphp

                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
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

                                        <div class="input-group input-group-sm" style="width: 180px;">
                                            <span class="input-group-text">Custom</span>
                                            <input type="number" id="customJumlahInput" class="form-control"
                                                placeholder="Cth: 12" min="1"
                                                value="{{ !in_array($savedJumlah, [5, 10, 15, 20, 25, 30]) ? $savedJumlah : '' }}">
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-primary btn-sm fw-semibold" id="btnAmbilModal">
                                            <i class="bi bi-shuffle me-1"></i> Ambil Soal Acak
                                        </button>

                                        <button class="btn btn-outline-primary btn-sm fw-semibold" id="btnSelectAllModal"
                                            title="Pilih semua soal pada daftar">
                                            <i class="bi bi-check2-all me-1"></i> Ambil Semua
                                        </button>

                                        <button class="btn btn-outline-secondary btn-sm fw-semibold"
                                            id="btnUnselectAllModal">
                                            <i class="bi bi-eraser me-1"></i> Bersihkan
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- HEADER TABEL --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-table me-1"></i> Tabel Soal Tersedia</h6>
                        <div class="small text-muted fw-semibold">Total: <span
                                id="modalTotalCount">{{ $questions->count() }} Soal</span></div>
                    </div>

                    {{-- TABEL SCROLLABLE --}}
                    <div style="max-height:480px; overflow:auto;" class="border rounded-3">
                        <table class="table table-sm table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light text-center sticky-top" style="top:0; z-index:1;">
                                <tr>
                                    <th style="width:84px">Pilih</th>
                                    <th style="width:56px">No</th>
                                    <th style="min-width:110px">Tipe</th>
                                    <th style="min-width:100px">Kesulitan</th>
                                    <th style="min-width:100px">Tags</th>
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
                                                title="{{ in_array($q->id, $selectedIds) ? 'Batal Pilih' : 'Pilih Soal Ini' }}">
                                                <i
                                                    class="bi {{ in_array($q->id, $selectedIds) ? 'bi-trash3-fill' : 'bi-plus-lg' }}"></i>
                                            </button>
                                        </td>

                                        <td class="text-center fw-semibold">{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($q->type) }}</span></td>
                                        <td><span class="badge bg-info text-dark">{{ ucfirst($q->difficulty) }}</span></td>
                                        <td>
                                            @if($q->tags)
                                                <span class="badge bg-light text-dark border">{{ $q->tags }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="white-space:normal;">{{ Str::limit($qData->text ?? '-', 300) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div class="me-auto text-muted small">Pilih soal yang ingin digunakan lalu tekan <strong>Terapkan ke
                            Aktivitas</strong></div>
                    <button class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary px-4 fw-bold" id="btnApplyToActivity">
                        <i class="bi bi-check-circle-fill me-1"></i> Terapkan ke Aktivitas
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INFO ATUR SOAL --}}
    <div class="modal fade" id="modalInfoAturSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle-fill me-2"></i> Panduan Mengatur Soal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body fs-6 p-4">
                    <p class="text-muted">
                        Halaman <strong>Atur Soal</strong> digunakan untuk menentukan soal-soal yang akan dikerjakan siswa
                        pada aktivitas <strong>{{ $aktivitas->title }}</strong>.
                    </p>
                    <hr>

                    <h6 class="fw-bold text-primary">
                        <i class="bi bi-1-circle-fill me-1"></i> Langkah 1 – Menentukan Jumlah Soal
                    </h6>
                    <ul>
                        <li>Pilih atau ketik <strong>jumlah soal minimum</strong> (menggunakan pilihan tombol preset atau
                            input custom).</li>
                        <li>Jumlah soal ini menjadi acuan validasi kelengkapan soal.</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-success">
                        <i class="bi bi-2-circle-fill me-1"></i> Langkah 2 – Memilih Soal
                    </h6>
                    <ul>
                        <li>Klik tombol biru <span class="badge bg-primary"><i class="bi bi-list-check me-1"></i> Lihat
                                Soal</span> yang berada di bawah kartu Soal Terpilih.</li>
                        <li>Pilih soal dengan metode:
                            <ul>
                                <li><strong>Manual</strong>: klik tombol tambah (+) pada baris soal.</li>
                                <li><strong>Acak Otomatis</strong>: klik tombol <strong>Ambil Soal Acak</strong>.</li>
                                <li><strong>Ambil Semua</strong>: memilih seluruh soal yang tersedia.</li>
                            </ul>
                        </li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-info">
                        <i class="bi bi-3-circle-fill me-1"></i> Langkah 3 – Terapkan & Simpan
                    </h6>
                    <ul>
                        <li>Klik tombol <strong>Terapkan ke Aktivitas</strong> di bagian kanan bawah modal.</li>
                        <li>Soal akan berpindah ke daftar <strong>Soal Terpilih</strong>.</li>
                        <li>Klik tombol hijau <strong>Simpan Pilihan</strong> untuk mengunci daftar soal ke dalam database.
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
        // GLOBALS
        const ACTIVITAS_ID = {{ $aktivitas->id }};
        const CSRF = "{{ csrf_token() }}";
        const MAX_QUESTIONS_AVAILABLE = {{ $questions->count() }};

        let modalSelected = @json($selectedIds);
        window.lastPicked = @json($selectedIds);

        function escapeHtml(s) {
            if (!s) return '';
            return s.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
        }

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

        // Render area soal terpilih
        async function renderSelectedArea(ids, questionsMap = null) {
            const area = document.getElementById('selectedArea');
            if (!area) return;

            if (!ids || ids.length === 0) {
                area.innerHTML = `<div id="noSelectedPlaceholder" class="text-center text-muted py-5">
                                        <i class="bi bi-clipboard-x text-secondary" style="font-size:2.5rem"></i>
                                        <div class="mt-2 fw-semibold">Belum ada soal terpilih.</div>
                                        <div class="small">Klik tombol <strong>Lihat Soal</strong> di bawah untuk menambah soal.</div>
                                      </div>`;
                updateCountDisplays(0);
                return;
            }

            let html = '';
            ids.forEach(id => {
                const q = questionsMap && questionsMap[id] ? questionsMap[id] : null;
                let tagHtml = '';
                if (q && q.tags && q.tags !== '-') {
                    tagHtml = ` — <span class="badge bg-light text-dark border"><i class="bi bi-tag-fill me-1"></i>${q.tags}</span>`;
                }
                const smallText = q ? (`<span class="badge bg-secondary me-1">${q.type}</span><span class="badge bg-info text-dark">${q.difficulty}</span>${tagHtml}`) : '';
                const bodyText = q ? escapeHtml(q.text) : `Memuat soal #${id}...`;

                html += `<div class="p-3 border rounded-3 mb-2 bg-light d-flex justify-content-between align-items-start shadow-sm" id="selectedItem-${id}">
                                <div>
                                    <div class="mb-1">${smallText}</div>
                                    <div class="mt-1 text-dark fw-medium" id="selectedText-${id}">${bodyText}</div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger ms-2 flex-shrink-0" onclick="hapusDariTerpilih(${id})" title="Hapus Soal Ini">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>`;
            });
            area.innerHTML = html;
            updateCountDisplays(ids.length);

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
                }
            }));
        }

        function updateCountDisplays(total) {
            const currentTotalEl = document.getElementById('currentTotal');
            const headerBadgeEl = document.getElementById('headerCountBadge');
            if (currentTotalEl) currentTotalEl.innerText = total;
            if (headerBadgeEl) headerBadgeEl.innerText = total + ' Soal';
        }

        const soalModalEl = document.getElementById('soalModal');
        if (soalModalEl) {
            soalModalEl.addEventListener('show.bs.modal', function () {
                syncModalRowButtons();
            });
        }

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
                    btn.title = "Pilih Soal Ini";
                    btn.innerHTML = `<i class="bi bi-plus-lg"></i>`;
                }
            } else {
                modalSelected.push(id);
                if (btn) {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-danger');
                    btn.title = "Batal Pilih";
                    btn.innerHTML = `<i class="bi bi-trash3-fill"></i>`;
                }
            }
        }

        function syncModalRowButtons() {
            document.querySelectorAll('#modalQuestionList tr').forEach(tr => {
                const id = parseInt(tr.dataset.qid);
                const btn = tr.querySelector('button');
                if (!btn) return;
                if (modalSelected.includes(id)) {
                    btn.classList.remove('btn-success'); btn.classList.add('btn-danger');
                    btn.title = "Batal Pilih";
                    btn.innerHTML = `<i class="bi bi-trash3-fill"></i>`;
                } else {
                    btn.classList.remove('btn-danger'); btn.classList.add('btn-success');
                    btn.title = "Pilih Soal Ini";
                    btn.innerHTML = `<i class="bi bi-plus-lg"></i>`;
                }
            });
        }

        const btnUnselect = document.getElementById('btnUnselectAllModal');
        if (btnUnselect) {
            btnUnselect.addEventListener('click', function () {
                modalSelected = [];
                syncModalRowButtons();
            });
        }

        const btnApply = document.getElementById('btnApplyToActivity');
        if (btnApply) {
            btnApply.addEventListener('click', function () {
                const jumlahSoal = readCheckedN();
                if (!jumlahSoal || jumlahSoal <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah soal belum ditentukan',
                        text: 'Silakan pilih atau ketik jumlah soal minimum terlebih dahulu.',
                        confirmButtonColor: '#f87171'
                    });
                    return;
                }

                if (jumlahSoal > MAX_QUESTIONS_AVAILABLE) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Melebihi Batas Soal',
                        html: `Jumlah minimal soal yang diminta (<b>${jumlahSoal}</b>) melebihi total soal yang tersedia di database (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
                    });
                    return;
                }

                if (!modalSelected || modalSelected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Soal belum dipilih',
                        text: 'Silakan pilih soal dari daftar terlebih dahulu.',
                        confirmButtonColor: '#f87171'
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
                                    tags: tds[4]?.innerText.trim() || '',
                                    text: tds[5]?.innerText.trim() || ''
                                };
                            });
                            renderSelectedArea(modalSelected, questionsMap);
                            window.lastPicked = modalSelected.slice();

                            const modal = document.getElementById('soalModal');
                            if (modal) {
                                const inst = bootstrap.Modal.getInstance(modal);
                                if (inst) inst.hide();
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Soal berhasil diterapkan ke aktivitas.',
                                timer: 1500,
                                showConfirmButton: false
                            });
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

        // HAPUS SATU SOAL DENGAN KONFIRMASI SWEETALERT2
        function hapusDariTerpilih(id) {
            Swal.fire({
                title: 'Hapus Soal Ini?',
                text: 'Soal akan dikeluarkan dari daftar soal terpilih aktivitas ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

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
                            } else {
                                updateCountDisplays(window.lastPicked.length);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: 'Soal berhasil dihapus dari daftar.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Tidak dapat menghapus soal.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                    });
            });
        }

        // CLEAR ALL DENGAN VALIDASI DAFTAR KOSONG
        function clearAll() {
            if (!window.lastPicked || window.lastPicked.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Daftar Kosong',
                    text: 'Belum ada soal terpilih yang dapat dihapus.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            Swal.fire({
                title: "Hapus Semua Pilihan?",
                text: "Semua soal terpilih akan dikosongkan dari aktivitas ini.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Ya, Hapus Semua!",
                cancelButtonText: "Batal"
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
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Semua soal terpilih telah dikosongkan.",
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Tidak dapat menghapus semua.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                    });
            });
        }

        let selectedN = @json($aktivitas->jumlah_soal ?? null);

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
                if (lbl) {
                    lbl.classList.remove('active');
                    lbl.setAttribute('aria-pressed', 'false');
                }
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
                const savedRadio = document.querySelector(`input[name="modalJumlahRadio"][value="${selectedN}"]`);
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
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Soal Belum Ditentukan',
                        text: 'Pilih atau masukkan jumlah soal terlebih dahulu.',
                        confirmButtonColor: '#f87171'
                    });
                    return;
                }

                if (jumlah > MAX_QUESTIONS_AVAILABLE) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Melebihi Batas Soal',
                        html: `Jumlah minimal soal yang diminta (<b>${jumlah}</b>) melebihi total soal yang tersedia di database (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: 'Soal acak telah dipilih.',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan saat mengambil soal.', 'error');
                    });
            });
        }

        // AMBIL SEMUA SOAL (DENGAN VALIDASI JUMLAH SOAL MINIMUM)
        const btnSelectAll = document.getElementById('btnSelectAllModal');
        if (btnSelectAll) {
            btnSelectAll.addEventListener('click', function () {
                const jumlah = readCheckedN();
                if (!jumlah || jumlah <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Soal Belum Ditentukan',
                        text: 'Silakan pilih atau masukkan jumlah soal minimum terlebih dahulu.',
                        confirmButtonColor: '#f87171'
                    });
                    return;
                }

                if (jumlah > MAX_QUESTIONS_AVAILABLE) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Melebihi Batas Soal',
                        html: `Jumlah minimal soal yang diminta (<b>${jumlah}</b>) melebihi total soal yang tersedia di database (<b>${MAX_QUESTIONS_AVAILABLE} soal</b>).`
                    });
                    return;
                }

                const allIds = Array.from(document.querySelectorAll('#modalQuestionList tr'))
                    .map(tr => parseInt(tr.dataset.qid))
                    .filter(Boolean);

                if (!allIds.length) {
                    Swal.fire('Kosong', 'Tidak ada soal pada daftar untuk dipilih.', 'info');
                    return;
                }

                selectedN = jumlah;
                modalSelected = allIds.slice();
                syncModalRowButtons();
                Swal.fire({
                    icon: 'success',
                    title: 'Semua Soal Dipilih',
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        }

        // SIMPAN PILIHAN
        function simpanPilihan() {
            let n = readCheckedN();
            if (!n) n = (window.lastPicked || []).length || null;

            if (!n || n <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Soal Belum Ditentukan',
                    text: 'Silakan pilih atau masukkan jumlah soal minimum terlebih dahulu.',
                    confirmButtonColor: '#f87171'
                });
                return;
            }

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
                    title: 'Jumlah Soal Belum Mencukupi',
                    html: `Jumlah soal yang dipilih: <b>${totalDipilih}</b><br>
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan',
                            text: 'Pilihan soal telah berhasil diperbarui di database.',
                            confirmButtonColor: '#3b82f6'
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: res.message || '' });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
                });
        }

        // INIT RENDER
        document.addEventListener('DOMContentLoaded', function () {
            renderSelectedArea(window.lastPicked);
            attachRadioHandlers();
            syncModalRowButtons();
        });
    </script>
@endsection