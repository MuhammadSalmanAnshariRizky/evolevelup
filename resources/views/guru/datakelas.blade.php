@extends('layouts.main')

@section('dataKelas', request()->is('datakelas') ? 'active' : '')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .table-card {
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border: none;
        }
        .token-code {
            font-family: monospace;
            font-size: 0.85rem;
        }
    </style>
@endpush

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

        {{-- PAGE HEADER --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold mb-0">Daftar Kelas Anda</h2>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:32px;height:32px"
                    data-bs-toggle="modal" data-bs-target="#modalInfoKelas" title="Informasi Pengelolaan Kelas">
                    <i class="bi bi-info-lg"></i>
                </button>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
                </button>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalGabung">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Gabung Kelas
                </button>
            </div>
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="card table-card">
            <div class="card-body p-4">
                @if($dataKelas->isEmpty())
                    <div class="alert alert-info mb-0">Anda belum mengajar kelas apa pun.</div>
                @else
                    <div class="table-responsive">
                        <table id="kelasTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Jenjang & Grade</th>
                                    <th>Token Kelas</th>
                                    <th>Ringkasan Cakupan</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataKelas as $data)
                                    <tr>
                                        <td class="text-center fw-semibold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $data->kelas->name }}</div>
                                            <small class="text-muted">
                                                Semester: <strong>{{ $data->kelas->semester == 'odd' ? 'Ganjil' : 'Genap' }}</strong>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border me-1">{{ $data->kelas->level }}</span>
                                            <span class="badge bg-light text-dark border">
                                                {{ $data->kelas->level === 'PT' || !$data->kelas->grade ? 'Non-Grade' : 'Grade ' . $data->kelas->grade }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="px-2 py-1 rounded bg-light text-secondary border token-code"
                                                    id="tokenText{{ $loop->index }}">{{ $data->kelas->token }}</code>
                                                <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                                    onclick="copyToken('tokenText{{ $loop->index }}')"
                                                    title="Salin token">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-info text-dark" title="Pengajar">
                                                    <i class="bi bi-person-fill me-1"></i>{{ $data->guru->count() }} Guru
                                                </span>
                                                <span class="badge bg-primary" title="Mata Pelajaran">
                                                    <i class="bi bi-journal-text me-1"></i>{{ $data->subjects->count() }} Mapel
                                                </span>
                                                <span class="badge bg-secondary" title="Topik">
                                                    <i class="bi bi-bookmark-fill me-1"></i>{{ $data->topics->count() }} Topik
                                                </span>
                                                <span class="badge bg-success" title="Aktivitas">
                                                    <i class="bi bi-check-circle-fill me-1"></i>{{ $data->activities->count() }} Aktivitas
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit{{ $loop->index }}" title="Edit Kelas">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('kelas.hapus', $data->kelas->id) }}" method="POST" class="d-inline form-hapus-kelas">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-hapus-kelas" title="Hapus Kelas">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ==================== MODAL EDIT (DILUAR TABEL) ==================== --}}
    @foreach($dataKelas as $data)
        <div class="modal fade" id="modalEdit{{ $loop->index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('kelas.update', $data->kelas->id) }}" method="POST" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Kelas: {{ $data->kelas->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $data->kelas->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Level (Jenjang)</label>
                            <select name="level" class="form-control form-select level-select-edit" data-target-grade="#gradeEdit{{ $loop->index }}" required>
                                <option value="">Pilih Jenjang</option>
                                @foreach(array_keys($grades) as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $data->kelas->level) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Grade (Kelas)</label>
                            <select name="grade" id="gradeEdit{{ $loop->index }}" class="form-control form-select" data-current-grade="{{ old('grade', $data->kelas->grade) }}">
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
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $data->kelas->description) }}</textarea>
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

    {{-- ==================== MODAL INFO KELAS ==================== --}}
    <div class="modal fade" id="modalInfoKelas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle me-2"></i> Panduan Pengelolaan Kelas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <section class="mb-3">
                        <h6 class="fw-bold text-primary"><i class="bi bi-plus-circle me-1"></i> 1. Tambah Kelas Baru</h6>
                        <p class="small text-muted mb-0">Klik tombol <strong>Tambah Kelas</strong> untuk membuat ruang kelas baru. Pilih jenjang dan grade yang sesuai agar materi dapat terorganisir dengan baik.</p>
                    </section>
                    <hr>
                    <section class="mb-3">
                        <h6 class="fw-bold text-success"><i class="bi bi-box-arrow-in-right me-1"></i> 2. Gabung Kelas</h6>
                        <p class="small text-muted mb-0">Gunakan tombol <strong>Gabung Kelas</strong> jika Anda ingin mengajar pada kelas yang telah dibuat oleh guru lain dengan menginputkan <strong>Token Kelas</strong>.</p>
                    </section>
                    <hr>
                    <section class="mb-3">
                        <h6 class="fw-bold text-warning text-dark"><i class="bi bi-key me-1"></i> 3. Salin Token Kelas</h6>
                        <p class="small text-muted mb-0">Setiap kelas memiliki token unik. Klik tombol salin <i class="bi bi-clipboard"></i> di samping kode token untuk membagikannya kepada guru pengajar lain.</p>
                    </section>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== MODAL TAMBAH ==================== --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('kelas.tambah') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    {{-- ==================== MODAL GABUNG ==================== --}}
    <div class="modal fade" id="modalGabung" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('kelas.gabung') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Gabung Kelas</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Token Kelas</label>
                    <input type="text" name="token" class="form-control" placeholder="Masukkan token kelas..." required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">Gabung</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inisialisasi DataTables
                if ($('#kelasTable').length) {
                    $('#kelasTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Cari kelas, semester, atau token...",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ kelas",
                            paginate: { previous: "Sebelumnya", next: "Selanjutnya" },
                            emptyTable: "Belum ada kelas yang tersedia"
                        }
                    });
                }

                // Data pemetaan grade dari Controller
                const gradesData = @json($grades);

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

                // Modal Tambah Grade Dynamic
                const addLevel = document.getElementById('addLevel');
                const addGrade = document.getElementById('addGrade');
                if (addLevel && addGrade) {
                    addLevel.addEventListener('change', function () {
                        updateGradeSelect(this.value, addGrade);
                    });
                }

                // Modal Edit Grade Dynamic
                document.querySelectorAll('.level-select-edit').forEach(function (levelSelect) {
                    const targetSelector = levelSelect.getAttribute('data-target-grade');
                    const gradeSelect = document.querySelector(targetSelector);

                    if (gradeSelect) {
                        const currentGrade = gradeSelect.getAttribute('data-current-grade');
                        updateGradeSelect(levelSelect.value, gradeSelect, currentGrade);
                        levelSelect.addEventListener('change', function () {
                            updateGradeSelect(this.value, gradeSelect);
                        });
                    }
                });

                // Hapus Kelas Confirmation
                $(document).on('click', '.btn-hapus-kelas', function () {
                    const form = $(this).closest('form');
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

            // Copy Token Function
            window.copyToken = function (elementId) {
                const el = document.getElementById(elementId);
                if (!el) return;
                navigator.clipboard.writeText(el.textContent.trim()).then(function () {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Token berhasil disalin',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            };
        </script>
    @endpush
@endsection