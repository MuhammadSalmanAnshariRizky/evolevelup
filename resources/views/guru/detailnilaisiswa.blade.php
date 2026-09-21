@extends('layouts.main')
@section('dataNilai', 'active')

@section('head')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .meta-key {
            font-weight: 600;
            color: #495057;
        }

        .meta-value {
            color: #212529;
        }

        .card-activity {
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }

        .badge-nilai {
            font-size: .85rem;
            padding: .35rem .6rem;
            border-radius: .35rem;
        }

        .no-data {
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <a href="{{ route('data.nilai') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>

        {{-- Header / Card Info Aktivitas --}}
        <div class="card card-activity mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <h4 class="mb-1">{{ $activity->title ?? 'Aktivitas' }}</h4>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoDetailNilai"
                                title="Informasi Detail Nilai">
                                <i class="bi bi-info-lg"></i>
                            </button>
                        </div>

                        <div class="text-muted mb-2">
                            <span class="me-3"><span class="meta-key">Mata Pelajaran:</span>
                                <span class="meta-value">{{ optional(optional($activity->topic)->subject)->name ?? '-' }}</span>
                            </span>
                            <span class="me-3"><span class="meta-key">Topik:</span>
                                <span class="meta-value">{{ optional($activity->topic)->title ?? '-' }}</span>
                            </span>
                            <span class="me-3"><span class="meta-key">Kelas:</span>
                                <span class="meta-value">
                                    {{ optional(optional($activity->topic)->subject)->id_class
                                        ? (optional(optional($activity->topic)->subject)->classes->name ?? 'Kelas ' . optional(optional($activity->topic)->subject)->id_class)
                                        : '-' }}
                                </span>
                            </span>
                        </div>

                        <div class="small text-muted">
                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i>
                                Dibuat: {{ optional($activity->created_at)->format('d M Y H:i') ?? '-' }}
                            </span>
                            <span><i class="far fa-clock me-1"></i>
                                Deadline: {{ optional($activity->deadline)->format('d M Y H:i') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        {{-- Ringkasan Angka --}}
                        @php
                            $countStudents = isset($students) ? count($students) : 0;
                            $countWithNilai = 0;
                            $sumNilai = 0;
                            if ($countStudents) {
                                foreach ($students as $st) {
                                    $raw = $st['nilai'] ?? null;
                                    if ($raw !== null && $raw !== '' && $raw !== '-') {
                                        if (is_numeric($raw)) {
                                            $num = (float) $raw;
                                        } else {
                                            preg_match('/[0-9]+(\.[0-9]+)?/', (string)$raw, $matches);
                                            $num = isset($matches[0]) ? (float)$matches[0] : null;
                                        }

                                        if ($num !== null) {
                                            $countWithNilai++;
                                            $sumNilai += $num;
                                        }
                                    }
                                }
                            }
                            $avg = $countWithNilai ? round($sumNilai / $countWithNilai, 2) : null;
                        @endphp

                        <div class="d-inline-block text-start">
                            <div class="small text-muted">Siswa</div>
                            <div class="h5 mb-0">{{ $countStudents }}</div>
                        </div>

                        <div class="d-inline-block text-start ms-3">
                            <div class="small text-muted">Tercatat Nilai</div>
                            <div class="h5 mb-0">{{ $countWithNilai }}</div>
                        </div>

                        <div class="d-inline-block text-start ms-3">
                            <div class="small text-muted">Rata-rata</div>
                            <div class="h5 mb-0">{{ $avg !== null ? $avg : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Nilai DataTables --}}
        <div class="card">
            <div class="card-body">
                @if(empty($students) || count($students) === 0)
                    <div class="alert alert-info mb-0">Tidak ada siswa di kelas ini.</div>
                @else
                    <div class="table-responsive">
                        <table id="nilaiTable" class="table table-striped table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">No</th>
                                    <th>Nama Siswa</th>
                                    <th style="width:160px">Nilai Akhir</th>
                                    <th style="width:140px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $i => $s)
                                    @php
                                        $rawNilai = $s['nilai'] ?? null;
                                        $numericVal = null;

                                        // Parsing nilai angka murni
                                        if ($rawNilai !== null && $rawNilai !== '' && $rawNilai !== '-') {
                                            if (is_numeric($rawNilai)) {
                                                $numericVal = (float) $rawNilai;
                                            } else {
                                                preg_match('/[0-9]+(\.[0-9]+)?/', (string)$rawNilai, $matches);
                                                $numericVal = isset($matches[0]) ? (float)$matches[0] : null;
                                            }
                                        }

                                        // Penentuan status berdasarkan ambang batas >= 60
                                        if ($numericVal !== null) {
                                            $status = ($numericVal >= 60) ? 'Lulus' : 'Tidak Lulus';
                                        } else {
                                            $status = 'Belum Mengerjakan';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $s['name'] ?? ('Siswa ' . ($s['id'] ?? '')) }}</td>
                                        <td data-order="{{ $numericVal ?? -1 }}">
                                            @if($numericVal === null)
                                                <span class="no-data">-</span>
                                            @else
                                                <span class="text-dark fw-bold">{{ $numericVal }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($status === 'Lulus')
                                                <span class="badge bg-success badge-nilai">Lulus</span>
                                            @elseif($status === 'Tidak Lulus')
                                                <span class="badge bg-warning text-dark badge-nilai">Tidak Lulus</span>
                                            @else
                                                <span class="badge bg-secondary badge-nilai">Belum Mengerjakan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Tombol Export --}}
                    <div class="mt-3">
                        <a href="{{ route('detail.nilai', $activity->id) }}?export=xlsx" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel me-1"></i> Export Excel (XLSX)
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL INFO DETAIL NILAI --}}
    <div class="modal fade" id="modalInfoDetailNilai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Detail Nilai Aktivitas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-3">
                        Halaman ini menampilkan <strong>hasil nilai siswa</strong> untuk satu
                        <strong>aktivitas evaluasi</strong> tertentu. Data digunakan untuk
                        memantau pencapaian siswa dan melakukan tindak lanjut pembelajaran.
                    </p>

                    <hr>

                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-layout-text-sidebar me-1"></i>
                        Informasi Aktivitas
                    </h6>
                    <ul>
                        <li><strong>Mata Pelajaran</strong> → mapel tempat aktivitas dibuat.</li>
                        <li><strong>Topik</strong> → topik pembelajaran aktivitas.</li>
                        <li><strong>Kelas</strong> → kelas yang mengerjakan aktivitas.</li>
                        <li><strong>Deadline</strong> → batas waktu pengerjaan siswa.</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-success mb-2">
                        <i class="bi bi-people me-1"></i>
                        Ringkasan Nilai
                    </h6>
                    <ul>
                        <li><strong>Siswa</strong> → jumlah total siswa di kelas.</li>
                        <li><strong>Tercatat Nilai</strong> → siswa yang sudah mengerjakan.</li>
                        <li><strong>Rata-rata</strong> → nilai rata-rata siswa yang mengerjakan.</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-warning mb-2">
                        <i class="bi bi-table me-1"></i>
                        Tabel Nilai
                    </h6>
                    <ul>
                        <li><strong>Nilai Akhir</strong> → skor akhir siswa.</li>
                        <li>
                            <strong>Status</strong>:
                            <ul>
                                <li><span class="badge bg-success">Lulus</span> → nilai $\ge 60$.</li>
                                <li><span class="badge bg-warning text-dark">Tidak Lulus</span> → nilai $< 60$.</li>
                                <li><span class="badge bg-secondary">Belum Mengerjakan</span> → belum submit.</li>
                            </ul>
                        </li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold text-info mb-2">
                        <i class="bi bi-file-earmark-excel me-1"></i>
                        Export Data
                    </h6>
                    <ul>
                        <li>Gunakan tombol <strong>Export Excel (XLSX)</strong> untuk mengunduh nilai siswa.</li>
                        <li>File dapat digunakan untuk laporan, arsip, atau pengolahan lanjutan.</li>
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('#nilaiTable').length) {
            $('#nilaiTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[0, "asc"]]
            });
        }
    });
</script>
@endsection