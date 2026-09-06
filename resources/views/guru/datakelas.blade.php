@extends('layouts.main')

@section('dataKelas', request()->is('datakelas') ? 'active' : '')

@section('content')
    <div class="container py-4">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold mb-0">Daftar Kelas Anda</h2>

                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:32px;height:32px"
                    data-bs-toggle="modal" data-bs-target="#modalInfoKelas" title="Informasi Pengelolaan Kelas">
                    <i class="bi bi-info-lg"></i>
                </button>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    Tambah Kelas
                </button>

                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalGabung">
                    Gabung Kelas
                </button>
            </div>
        </div>

        {{-- Jika tidak ada kelas --}}
        @if($dataKelas->isEmpty())
            <div class="alert alert-info">Anda belum mengajar kelas apa pun.</div>
        @else
            <div class="row g-4">
                @foreach($dataKelas as $data)
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="card h-100 shadow-sm border-0 rounded-4 kelas-card overflow-hidden">
                            <div class="card-header bg-gradient p-3 d-flex justify-content-between align-items-start">
                                <div class="text-primary">
                                    <h5 class="mb-1 fw-bold" style="letter-spacing: .2px;">{{ $data->kelas->name }}</h5>
                                    <div class="small opacity-85">
                                        <span class="me-2">Semester:
                                            <strong>{{ $data->kelas->semester == 'odd' ? 'Ganjil' : 'Genap' }}</strong></span>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light btn-icon rounded-circle" type="button"
                                        id="menuKelas{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false"
                                        title="Actions">
                                        ⋮
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuKelas{{ $loop->index }}">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $loop->index }}">
                                                Edit Kelas
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('kelas.hapus', $data->kelas->id) }}" method="POST"
                                                class="d-inline form-hapus-kelas">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger btn-hapus-kelas">
                                                    Hapus Kelas
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                {{-- Meta info vertical --}}
                                <dl class="row mb-3">
                                    <dt class="col-4 text-muted small">Jenjang</dt>
                                    <dd class="col-8 mb-1">
                                        <span class="badge bg-light text-dark border px-3 py-2">{{ $data->kelas->level }}</span>
                                    </dd>

                                    <dt class="col-4 text-muted small">Kelas</dt>
                                    <dd class="col-8 mb-1">
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            {{ $data->kelas->level === 'PT' || !$data->kelas->grade ? '-' : 'Grade ' . $data->kelas->grade }}
                                        </span>
                                    </dd>

                                    <dt class="col-4 text-muted small">Token</dt>
                                    <dd class="col-8 mb-0 d-flex align-items-center gap-2">
                                        <code class="px-2 py-1 rounded bg-white text-secondary border"
                                            id="tokenText{{ $loop->index }}">{{ $data->kelas->token }}</code>
                                        <button class="btn btn-sm btn-outline-secondary"
                                            onclick="copyToken('tokenText{{ $loop->index }}')" data-bs-toggle="tooltip"
                                            title="Salin token">
                                            Salin
                                        </button>
                                    </dd>
                                </dl>

                                <div class="mt-2">
                                    <button class="btn btn-outline-primary btn-sm btn-open-claim-modal"
                                        data-class-id="{{ $data->kelas->id }}" data-class-name="{{ $data->kelas->name }}">
                                        Klaim Paket
                                    </button>
                                </div>

                                {{-- Lists with collapse --}}
                                <div class="mb-3 mt-3">
                                    <h6 class="fw-semibold text-secondary mb-1">Guru Pengajar</h6>
                                    @if($data->guru->isNotEmpty())
                                        <div class="collapse show" id="guruList{{ $loop->index }}">
                                            <ol class="ps-3 mb-0 small max-list">
                                                @foreach($data->guru as $g)
                                                    <li>{{ $g }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @else
                                        <em class="text-muted small">Belum ada guru pengajar</em>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-semibold text-secondary mb-0">Mata Pelajaran</h6>
                                        @if($data->subjects->count() > 3)
                                            <a class="small" data-bs-toggle="collapse" href="#subjectList{{ $loop->index }}"
                                                role="button">Lihat semua</a>
                                        @endif
                                    </div>
                                    @if($data->subjects->isNotEmpty())
                                        <div class="collapse {{ $data->subjects->count() <= 3 ? 'show' : '' }}"
                                            id="subjectList{{ $loop->index }}">
                                            <ol class="ps-3 mb-0 small max-list">
                                                @foreach($data->subjects as $s)
                                                    <li>{{ $s }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @else
                                        <em class="text-muted small">Tidak ada Mata Pelajaran</em>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-semibold text-secondary mb-0">Topik</h6>
                                        @if($data->topics->count() > 3)
                                            <a class="small" data-bs-toggle="collapse" href="#topicList{{ $loop->index }}"
                                                role="button">Lihat semua</a>
                                        @endif
                                    </div>
                                    @if($data->topics->isNotEmpty())
                                        <div class="collapse {{ $data->topics->count() <= 3 ? 'show' : '' }}"
                                            id="topicList{{ $loop->index }}">
                                            <ol class="ps-3 mb-0 small max-list">
                                                @foreach($data->topics as $t)
                                                    <li>{{ $t }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @else
                                        <em class="text-muted small">Tidak ada topic</em>
                                    @endif
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-semibold text-secondary mb-0">Aktivitas</h6>
                                        @if($data->activities->count() > 3)
                                            <a class="small" data-bs-toggle="collapse" href="#activityList{{ $loop->index }}"
                                                role="button">Lihat semua</a>
                                        @endif
                                    </div>
                                    @if($data->activities->isNotEmpty())
                                        <div class="collapse {{ $data->activities->count() <= 3 ? 'show' : '' }}"
                                            id="activityList{{ $loop->index }}">
                                            <ol class="ps-3 mb-0 small max-list">
                                                @foreach($data->activities as $a)
                                                    <li>{{ $a }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @else
                                        <em class="text-muted small">Tidak ada activity</em>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>

                    {{-- Modal Edit per item --}}
                    <div class="modal fade" id="modalEdit{{ $loop->index }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('kelas.update', $data->kelas->id) }}" method="POST" class="modal-content">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Kelas: {{ $data->kelas->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kelas</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $data->kelas->name) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Level (Jenjang)</label>
                                        <select name="level" class="form-control form-select level-select-edit"
                                            data-target-grade="#gradeEdit{{ $loop->index }}" required>
                                            <option value="">Pilih Jenjang</option>
                                            @foreach(array_keys($grades) as $lvl)
                                                <option value="{{ $lvl }}" {{ old('level', $data->kelas->level) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Grade (Kelas)</label>
                                        <select name="grade" id="gradeEdit{{ $loop->index }}" class="form-control form-select"
                                            data-current-grade="{{ old('grade', $data->kelas->grade) }}">
                                            <option value="">Pilih Kelas</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Semester</label>
                                        <select name="semester" class="form-control form-select" required>
                                            <option value="odd" {{ old('semester', $data->kelas->semester) == 'odd' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="even" {{ old('semester', $data->kelas->semester) == 'even' ? 'selected' : '' }}>Genap</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi (Opsional)</label>
                                        <textarea name="description" class="form-control"
                                            rows="3">{{ old('description', $data->kelas->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('kelas.tambah') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kelas 7A / TI-1A" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level (Jenjang)</label>
                        <select name="level" id="addLevel" class="form-control form-select" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach(array_keys($grades) as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade (Kelas)</label>
                        <select name="grade" id="addGrade" class="form-control form-select" disabled>
                            <option value="">Pilih Level Terlebih Dahulu</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-control form-select" required>
                            <option value="odd">Ganjil</option>
                            <option value="even">Genap</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Gabung --}}
    <div class="modal fade" id="modalGabung" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('kelas.gabung') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Gabung Kelas</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Token Kelas</label>
                    <input type="text" name="token" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">Gabung</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #0d6efd 0%, #3b82f6 100%);
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .kelas-card {
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .kelas-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(13, 110, 253, .12);
        }

        .max-list {
            max-height: 6.5rem;
            overflow: auto;
        }

        .max-list::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .max-list::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 6px;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data pemetaan grade dari Controller
                const gradesData = @json($grades);

                // Helper function untuk update opsi grade
                function updateGradeSelect(levelVal, gradeSelect, selectedGrade = null) {
                    gradeSelect.innerHTML = '';

                    if (levelVal === 'PT') {
                        gradeSelect.disabled = true;
                        gradeSelect.removeAttribute('required');
                        gradeSelect.innerHTML = '<option value="">Tidak Perlu Grade (PT)</option>';
                    } else if (levelVal && gradesData[levelVal]) {
                        gradeSelect.disabled = false;
                        gradeSelect.setAttribute('required', 'required');
                        gradeSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                        gradesData[levelVal].forEach(function (g) {
                            const option = document.createElement('option');
                            option.value = g;
                            option.textContent = 'Kelas ' + g;
                            if (selectedGrade && String(selectedGrade) === String(g)) {
                                option.selected = true;
                            }
                            gradeSelect.appendChild(option);
                        });
                    } else {
                        gradeSelect.disabled = true;
                        gradeSelect.removeAttribute('required');
                        gradeSelect.innerHTML = '<option value="">Pilih Level Terlebih Dahulu</option>';
                    }
                }

                // Handler untuk Modal Tambah
                const addLevel = document.getElementById('addLevel');
                const addGrade = document.getElementById('addGrade');
                if (addLevel && addGrade) {
                    addLevel.addEventListener('change', function () {
                        updateGradeSelect(this.value, addGrade);
                    });
                }

                // Handler untuk Modal Edit (Looping tiap item)
                document.querySelectorAll('.level-select-edit').forEach(function (levelSelect) {
                    const targetSelector = levelSelect.getAttribute('data-target-grade');
                    const gradeSelect = document.querySelector(targetSelector);

                    if (gradeSelect) {
                        const currentGrade = gradeSelect.getAttribute('data-current-grade');
                        // Inisialisasi awal nilai saat modal dirender
                        updateGradeSelect(levelSelect.value, gradeSelect, currentGrade);

                        // Event listener jika level diubah pada modal edit
                        levelSelect.addEventListener('change', function () {
                            updateGradeSelect(this.value, gradeSelect);
                        });
                    }
                });

                // Hapus kelas confirmation
                document.querySelectorAll('.btn-hapus-kelas').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const form = this.closest('form');
                        Swal.fire({
                            title: 'Hapus kelas?',
                            text: 'Kelas dan seluruh data terkait akan dihapus.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });

            // Copy Token
            window.copyToken = function (elementId) {
                const el = document.getElementById(elementId);
                if (!el) return;
                navigator.clipboard.writeText(el.textContent.trim()).then(function () {
                    const btn = event?.target;
                    if (btn) {
                        btn.setAttribute('data-bs-original-title', 'Tersalin!');
                        var t = bootstrap.Tooltip.getInstance(btn);
                        if (t) { t.show(); setTimeout(() => t.hide(), 900); }
                    } else {
                        alert('Token disalin: ' + el.textContent.trim());
                    }
                });
            };
        </script>
    @endpush
@endsection