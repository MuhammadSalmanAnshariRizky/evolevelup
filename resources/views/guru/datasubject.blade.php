@extends('layouts.main')

@section('dataSubject', request()->is('datamatapelajaran') ? 'active' : '')
@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold mb-0">Data Mata Pelajaran Berdasarkan Kelas</h3>

            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:32px;height:32px"
                data-bs-toggle="modal" data-bs-target="#modalInfoSubject" title="Informasi Mata Pelajaran">
                <i class="bi bi-info-lg"></i>
            </button>
        </div>

        {{-- Pesan sukses --}}
        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        {{-- Form tambah subject --}}
        <div class="card mb-4 mt-3">
            <div class="card-header bg-primary text-white fw-semibold">Tambah Mata Pelajaran</div>
            <div class="card-body">
                <form action="{{ route('guru.subject.tambah') }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-md-5">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                            placeholder="Masukkan Nama Mata Pelajaran" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <select name="id_class" class="form-control @error('id_class') is-invalid @enderror" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($data as $item)
                                <option value="{{ $item->kelas->id }}" {{ old('id_class') == $item->kelas->id ? 'selected' : '' }}>
                                    {{ $item->kelas->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel DataTables DESKTOP --}}
        <div class="d-none d-md-block">
            <div class="card">
                <div class="card-body">
                    <table id="subjectsTable" class="table table-striped table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Semester</th>
                                <th>Mata Pelajaran</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                                @foreach($item->subjects as $subject)
                                    <tr>
                                        <td></td>
                                        <td>{{ $item->kelas->name }}</td>
                                        <td>{{ $item->kelas->semester_human }}</td>
                                        <td class="fw-semibold">
                                            <span class="subject-name-{{ $subject->id }}">{{ $subject->name }}</span>
                                        </td>
                                        <td>{{ $subject->creator_name ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-success btn-sm btn-edit-subject" data-id="{{ $subject->id }}"
                                                    data-name="{{ $subject->name }}" data-class="{{ $item->kelas->id }}">
                                                    Edit
                                                </button>

                                                <form action="{{ route('guru.subject.hapus', $subject->id) }}" method="POST"
                                                    class="form-delete-subject">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-subject">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MOBILE CARD VIEW --}}
        <div class="d-block d-md-none mt-3">
            @foreach($data as $item)
                @foreach($item->subjects as $subject)
                    <div class="card mb-3 shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">
                                <i class="bi bi-book-fill text-primary me-1"></i>
                                {{ $subject->name }}
                            </h6>

                            <div class="small text-muted mb-2">
                                <div><i class="bi bi-building me-1"></i>{{ $item->kelas->name }}</div>
                                <div><i class="bi bi-calendar3 me-1"></i>Semester {{ $item->kelas->semester_human }}</div>
                                <div><i class="bi bi-person-badge me-1"></i>{{ $subject->creator_name ?? '—' }}</div>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-success btn-sm btn-edit-subject" data-id="{{ $subject->id }}"
                                    data-name="{{ $subject->name }}" data-class="{{ $item->kelas->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>

                                <form action="{{ route('guru.subject.hapus', $subject->id) }}" method="POST" class="form-delete-subject">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-subject">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Modal Edit Subject --}}
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editSubjectForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSubjectLabel">Edit Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="subject_id" id="modalSubjectId">
                        <div class="mb-3">
                            <label for="modalSubjectName" class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="name" id="modalSubjectName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="modalSubjectClass" class="form-label">Kelas</label>
                            <select name="id_class" id="modalSubjectClass" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($data as $item)
                                    <option value="{{ $item->kelas->id }}">{{ $item->kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="modalSaveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Info Subject --}}
    <div class="modal fade" id="modalInfoSubject" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle me-1"></i> Panduan Data Mata Pelajaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <section class="mb-4">
                        <h6 class="fw-semibold text-primary"><i class="bi bi-plus-circle me-1"></i>Menambah Mata Pelajaran</h6>
                        <ol class="small text-muted">
                            <li>Isi <strong>Nama Mata Pelajaran</strong></li>
                            <li>Pilih <strong>Kelas</strong> yang akan menggunakan mata pelajaran tersebut</li>
                            <li>Klik tombol <strong>Tambah</strong></li>
                        </ol>
                    </section>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        table#subjectsTable .form-control-sm { padding: .25rem .5rem; font-size: .85rem; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    {{-- SweetAlert untuk penanganan error validasi duplikasi --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let errorMessages = `
                    <ul class="text-start mb-0" style="font-size: 14px; padding-left: 1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `;

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data!',
                    html: errorMessages,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Tutup'
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var table = $('#subjectsTable').DataTable({
                responsive: true,
                lengthChange: true,
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, 5] },
                    { searchable: false, targets: 0 }
                ],
                order: [[1, 'asc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari mata pelajaran atau kelas...",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    paginate: { previous: "Sebelumnya", next: "Selanjutnya" }
                },
                drawCallback: function () {
                    var api = this.api();
                    api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });

            var editModalEl = document.getElementById('editSubjectModal');
            var editModal = (typeof bootstrap !== 'undefined' && editModalEl) ? new bootstrap.Modal(editModalEl) : null;
            var updateUrlTemplate = "{{ route('guru.subject.update', ['id' => ':id']) }}";

            $(document).on('click', '.btn-edit-subject', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var cls = $(this).data('class');

                $('#modalSubjectId').val(id);
                $('#modalSubjectName').val(name);
                $('#modalSubjectClass').val(cls);

                var action = updateUrlTemplate.replace(':id', id);
                $('#editSubjectForm').attr('action', action);

                if (editModal) {
                    editModal.show();
                }
            });

            document.querySelectorAll('.btn-delete-subject').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const subjectName = this.closest('tr')?.querySelector('[class^="subject-name-"]')?.innerText ?? 'mata pelajaran ini';

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        html: `<div class="text-start"><p>Mata pelajaran <strong>${subjectName}</strong> akan dihapus.</p></div>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush