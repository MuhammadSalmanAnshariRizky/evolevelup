@extends('layouts.main')

@section('content')
    <div class="container-fluid py-4">
        <h3 class="mb-4">Ringkasan Jawaban Siswa Per Aktivitas</h3>
        {{-- Tambahkan kode ini untuk melihat error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal Export!</strong> {{ session('error') }}
                <p>selectedActivityId: {{ $selectedActivityId }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row align-items-end mb-4">
            {{-- Form Filter Kelas dan Aktivitas --}}
            <div class="col-md-8">
                <form method="GET" action="{{ route('guru.jawaban_siswa') }}" class="row g-3">
                    <div class="col-md-6">
                        <label for="class_id" class="form-label font-weight-bold">Pilih Kelas</label>
                        <select name="class_id" id="class_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="activity_id" class="form-label font-weight-bold">Pilih Aktivitas</label>
                        <select name="activity_id" id="activity_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Aktivitas --</option>
                            @foreach ($activities as $activity)
                                <option value="{{ $activity->id }}" {{ $selectedActivityId == $activity->id ? 'selected' : '' }}>
                                    {{ $activity->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Tombol Export Excel Terpisah (Di Luar Form) --}}
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                @if ($selectedActivityId && $studentSummaries->isNotEmpty())
                    <a href="{{ route('guru.jawaban_siswa.export', $selectedActivityId) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xlsx)
                    </a>
                @else
                    <button class="btn btn-secondary" disabled title="Pilih aktivitas dan pastikan ada data untuk diexport">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xlsx)
                    </button>
                @endif
            </div>
        </div>

        {{-- Tabel Ringkasan Jawaban Siswa --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Jawaban Benar</th>
                                <th class="text-center">Jawaban Salah</th>
                                <th class="text-center">Progres Pengerjaan</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentSummaries as $index => $summary)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $summary['user_name'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $summary['correct_count'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $summary['wrong_count'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">
                                            {{ $summary['total_answered'] }} / {{ $selectedActivity->jumlah_soal ?? '-' }} Soal
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('guru.jawaban_siswa.detail', ['activityId' => $selectedActivityId, 'userId' => $summary['user_id']]) }}"
                                            class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada siswa yang menjawab aktivitas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection