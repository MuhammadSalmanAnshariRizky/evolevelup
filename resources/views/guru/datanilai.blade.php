@extends('layouts.main')
@section('dataNilai', 'active')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .page-header {
            margin-bottom: 1.5rem;
        }

        .table-card {
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .muted-small {
            font-size: .85rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- HEADER PAGE --}}
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="h4 mb-0 fw-bold">Data Nilai</h1>
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:30px;height:30px" data-bs-toggle="modal" data-bs-target="#modalInfoDataNilai"
                        title="Informasi Data Nilai">
                        <i class="bi bi-info-lg"></i>
                    </button>
                </div>
                <div class="muted-small">
                    Kelola dan pantau seluruh hasil penilaian siswa lintas kelas, mata pelajaran, dan topik.
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('guru.datanilai.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Semua Kelas
                </a>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card table-card">
            <div class="card-body p-4">
                @if ($grouped->isEmpty())
                    <div class="alert alert-info mb-0">Belum ada kelas atau aktivitas untuk Anda.</div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Topik</th>
                                    <th>Nama Aktivitas</th>
                                    <th>Siswa Mengerjakan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($grouped as $classData)
                                    @foreach($classData['subjects'] as $subject)
                                        {{-- Diubah: Mengulang langsung ke activities, bukan topics --}}
                                        @foreach($subject['activities'] as $activity)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $classData['class_name'] }}</td>
                                                <td>
                                                    <span class="badge bg-info text-white">
                                                        {{ $subject['name'] }}
                                                    </span>
                                                </td>
                                                {{-- Topik gabungan/penunjang yang dibuat di Controller --}}
                                                <td>{{ $activity['topic_title'] }}</td>
                                                <td><strong>{{ $activity['title'] }}</strong></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $activity['results_count'] }} Nilai
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('detail.nilai', $activity['id']) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- MODAL INFO --}}
    <div class="modal fade" id="modalInfoDataNilai" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i> Informasi Data Nilai
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Halaman <strong>Data Nilai</strong> menampilkan seluruh rekap aktivitas pengerjaan kuis/ujian siswa
                        yang diurutkan secara terstruktur.
                    </p>
                    <hr class="my-3">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-search me-1"></i> Fitur Tabel</h6>
                    <ul class="mb-3">
                        <li><strong>Pencarian Cepat</strong>: Cari nama aktivitas, topik, mapel, atau kelas pada kolom
                            pencarian.</li>
                        <li><strong>Pengurutan (Sorting)</strong>: Klik header kolom untuk mengurutkan data.</li>
                        <li><strong>Status Nilai</strong>: Badge <span class="badge bg-success">Hijau</span> menandakan
                            sudah ada siswa yang mengerjakan, sedangkan <span class="badge bg-secondary">Abu-abu</span>
                            menandakan belum ada pengerjaan.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#nilaiTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[1, 'asc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari kelas, mapel, topik, atau aktivitas...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    },
                    emptyTable: "Tidak ada data nilai yang tersedia"
                }
            });
        });
    </script>

    @if (session('swal'))
        <script>
            Swal.fire({
                icon: "{{ session('swal.icon') }}",
                title: "{{ session('swal.title') }}",
                text: "{{ session('swal.text') }}",
                confirmButtonColor: '#4e73df'
            });
        </script>
    @endif
@endpush