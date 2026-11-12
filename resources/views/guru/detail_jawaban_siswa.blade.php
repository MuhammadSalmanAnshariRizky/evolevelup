@extends('layouts.main')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Detail Jawaban Siswa</h3>
            <p class="text-muted mb-0">
                <strong>Siswa:</strong> {{ $student->name }} | 
                <strong>Aktivitas:</strong> {{ $activity->title }}
            </p>
        </div>
        <a href="{{ route('guru.jawaban_siswa', ['activity_id' => $activity->id]) }}" class="btn btn-secondary">
            &larr; Kembali
        </a>
    </div>

    {{-- Tabel Detail Jawaban Per Soal --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Pertanyaan / Soal</th>
                            <th class="text-center" style="width: 160px;">Tingkat Kesulitan</th>
                            <th class="text-center" style="width: 150px;">Jawaban Siswa</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($answers as $index => $answer)
                            @php
                                // Decode teks soal jika berbentuk JSON
                                $questionRaw = $answer->question->question ?? 'Soal tidak ditemukan';
                                $questionJson = json_decode($questionRaw, true);
                                $questionText = is_array($questionJson) && isset($questionJson['text']) 
                                    ? $questionJson['text'] 
                                    : $questionRaw;
                                
                                $difficulty = strtolower($answer->question->difficulty ?? '-');

                                // Penentuan warna badge berdasarkan difficulty
                                $badgeColor = match ($difficulty) {
                                    'sangat_mudah', 'sangat mudah' => 'bg-success',
                                    'mudah'                        => 'bg-info text-dark',
                                    'sedang'                       => 'bg-warning text-dark',
                                    'sulit'                        => 'bg-danger',
                                    'sangat_sulit', 'sangat sulit' => 'bg-dark text-white',
                                    default                        => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{-- Menggunakan {{ }} agar tag HTML di-escape / tidak dieksekusi sebagai HTML --}}
                                <td>{{ $questionText }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $difficulty)) }}
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ strtoupper($answer->user_answer) }}
                                </td>
                                <td class="text-center">
                                    @if ($answer->is_correct)
                                        <span class="badge bg-success">Benar</span>
                                    @else
                                        <span class="badge bg-danger">Salah</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Siswa ini belum menjawab soal apapun.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection