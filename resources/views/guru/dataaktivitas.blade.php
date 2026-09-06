@extends('layouts.main')
@section('dataAktivitas', request()->is('dataaktivitas') ? 'active' : '')
@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center gap-2 mb-4">
            <h3 class="fw-bold mb-0">Data Evaluasi Berdasarkan Topik</h3>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px" data-bs-toggle="modal" data-bs-target="#modalInfoAktivitas">
                    <i class="bi bi-info-lg"></i>
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success text-center shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Aktivitas
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.aktivitas.simpan') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            {{-- KIRI: judul / deadline / topik (stacked) --}}
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Judul Aktivitas</label>
                                    <input type="text" name="title" class="form-control shadow-sm @error('title') is-invalid @enderror"
                                        placeholder="Masukkan judul aktivitas..." value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Deadline</label>
                                    <input type="datetime-local" name="deadline" class="form-control shadow-sm @error('deadline') is-invalid @enderror" 
                                        value="{{ old('deadline') }}" required>
                                    @error('deadline')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Input Tipe Aktivitas -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipe Aktivitas</label>
                                    <select name="type" class="form-select shadow-sm select-type @error('type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="evaluation" {{ old('type') == 'evaluation' ? 'selected' : '' }}>Evaluation (Bisa Pilih Banyak Topik)</option>
                                        <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Latihan / Lainnya (Hanya Satu Topik)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Input Topik -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Topik</label>

                                    <!-- Select biasa untuk Latihan / Lainnya (Single) -->
                                    <select name="id_topic" class="form-select shadow-sm single-topic-select @error('id_topic') is-invalid @enderror" required>
                                        <option value="">Pilih Topik</option>
                                        @foreach(\App\Models\Topic::with('subject')->where('created_by', Auth::id())->get() as $topicOption)
                                            <option value="{{ $topicOption->id }}" {{ old('id_topic') == $topicOption->id ? 'selected' : '' }}>
                                                {{ $topicOption->title }} ({{ $topicOption->subject->name ?? 'Tanpa Subject' }})
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Wadah Checkbox untuk Evaluation (Multiple) -->
                                    <div class="multiple-topic-container d-none border rounded p-3 bg-light shadow-sm"
                                        style="max-height: 200px; overflow-y: auto;">
                                        <p class="text-muted small mb-2">Pilih satu atau lebih topik untuk evaluasi:</p>
                                        <div class="row g-2">
                                            @foreach(\App\Models\Topic::with('subject')->where('created_by', Auth::id())->get() as $topicOption)
                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input topic-checkbox" type="checkbox"
                                                            name="id_topic[]" value="{{ $topicOption->id }}"
                                                            id="topic_{{ $topicOption->id }}"
                                                            {{ is_array(old('id_topic')) && in_array($topicOption->id, old('id_topic')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="topic_{{ $topicOption->id }}">
                                                            {{ $topicOption->title }} <span
                                                                class="text-muted small">({{ $topicOption->subject->name ?? 'Tanpa Subject' }})</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('id_topic')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    @error('id_topic.*')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- KANAN: grid 2 kolom x 3 baris --}}
                            <div class="col-lg-4">
                                <div class="row gx-2 gy-2">

                                    {{-- Row 1: Durasi (kiri) & Adaptive (kanan) --}}
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Durasi (menit)</label>
                                        <input type="number" name="durasi_pengerjaan" class="form-control shadow-sm @error('durasi_pengerjaan') is-invalid @enderror" min="1"
                                            placeholder="Masukkan durasi" value="{{ old('durasi_pengerjaan') }}" required>
                                        @error('durasi_pengerjaan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-6 d-flex flex-column">
                                        <label class="form-label fw-semibold">Adaptif</label>
                                        <div class="form-check mt-1">
                                            <input type="hidden" name="addaptive" value="no">
                                            <input class="form-check-input" type="checkbox" name="addaptive" value="yes"
                                                id="adaptiveToggle" {{ old('addaptive') == 'yes' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="adaptiveToggle">Aktifkan</label>
                                        </div>
                                        @error('addaptive')
                                            <div class="text-danger small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- KKM -->
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">KKM</label>
                                        <input type="number" name="kkm" class="form-control shadow-sm @error('kkm') is-invalid @enderror" min="0" max="100"
                                            placeholder="Masukkan KKM" value="{{ old('kkm') }}" required>
                                        @error('kkm')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-6"></div>
                                    <div class="col-6"></div>

                                    {{-- Row 3: tombol Simpan --}}
                                    <div class="col-12 d-grid">
                                        <button class="btn btn-success shadow-sm py-2">
                                            Simpan
                                        </button>
                                    </div>

                                </div><!-- /.row (kanan) -->
                            </div><!-- /.col-kanan -->

                        </div><!-- /.row utama -->
                    </form>
                </div>
            </div>

            {{-- DataTables --}}
            <div class="d-none d-md-block">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="activitiesTable" class="table table-striped table-bordered " style="width:100%">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th style="width:60px">No</th>
                                        <th>Judul</th>
                                        <th>Deadline</th>
                                        <th>adaptif</th>
                                        <th>Topik</th>
                                        <th>Mapel</th>
                                        <th>Kelas</th>
                                        <th>Semester</th>
                                        <th style="width:260px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $r)
                                        <tr>
                                            <td class="text-center align-middle"></td>
                                            <td class="align-middle">{{ $r->title }}</td>
                                            <td class="align-middle">
                                                {{ $r->deadline ? date('Y-m-d H:i', strtotime($r->deadline)) : '-' }}
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($r->addaptive === 'yes')
                                                    <span class="badge bg-success">Ya</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="align-middle col-title">
                                                <div class="cell-inner" title="{{ $r->topic_title }}">{{ $r->topic_title }}</div>
                                            </td>
                                            <td class="align-middle col-subject hide-sm">
                                                <div class="cell-inner" title="{{ $r->subject_name ?? '-' }}">
                                                    {{ $r->subject_name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="align-middle col-class hide-sm">
                                                <div class="cell-inner" title="{{ $r->class_name ?? '-' }}">
                                                    {{ $r->class_name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($r->semester === 'odd')
                                                    <span class="badge bg-info text-dark">Ganjil</span>
                                                @elseif($r->semester === 'even')
                                                    <span class="badge bg-secondary">Genap</span>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="action-group" role="group" aria-label="Aksi aktivitas">
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#modalEdit{{ $r->id }}" aria-label="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <a href="{{ route('guru.aktivitas.aturSoal', $r->id) }}"
                                                        class="btn btn-warning btn-sm" aria-label="Atur Soal">
                                                        <i class="bi bi-gear"></i> Soal
                                                    </a>

                                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                                        data-bs-target="#lihatSoal{{ $r->id }}"
                                                        aria-label="Lihat Soal">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    <form action="{{ route('guru.aktivitas.hapus', $r->id) }}" method="POST"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                            aria-label="Hapus">
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
                    </div>
                </div>
            </div>

            <div class="d-block d-md-none">
                @foreach($rows as $r)
                    <div class="card shadow-sm mb-3 border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $r->title }}</h6>
                            <div class="small text-muted mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-bookmark text-secondary"></i>
                                    <span><strong>Topik:</strong> {{ $r->topic_title ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-journal-bookmark text-secondary"></i>
                                    <span><strong>Mapel:</strong> {{ $r->subject_name ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building text-secondary"></i>
                                    <span><strong>Kelas:</strong> {{ $r->class_name ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock text-secondary"></i>
                                    <span>
                                        <strong>Deadline:</strong>
                                        {{ $r->deadline ? date('d M Y H:i', strtotime($r->deadline)) : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                @if($r->addaptive === 'yes')
                                    <span class="badge bg-success">Adaptif</span>
                                @else
                                    <span class="badge bg-secondary">Non-adaptif</span>
                                @endif

                                @if($r->semester === 'odd')
                                    <span class="badge bg-info text-dark">Ganjil</span>
                                @elseif($r->semester === 'even')
                                    <span class="badge bg-secondary">Genap</span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $r->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <a href="{{ url('/guru/aktivitas/' . $r->id . '/atur-soal?topic=' . $r->topic_id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="bi bi-gear"></i> Soal
                                </a>

                                <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                    data-bs-target="#lihatSoal{{ $r->id }}">
                                    <i class="bi bi-eye"></i> Lihat
                                </button>

                                <form action="{{ route('guru.aktivitas.hapus', $r->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm btn-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- GLOBAL MODALS --}}
            @foreach($rows as $r)
                {{-- MODAL EDIT --}}
                <div class="modal fade" id="modalEdit{{ $r->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('guru.aktivitas.ubah', $r->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Aktivitas — {{ $r->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Judul</label>
                                        <input type="text" name="title" class="form-control" value="{{ $r->title }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Deadline</label>
                                        <input type="datetime-local" name="deadline" class="form-control"
                                            value="{{ $r->deadline ? date('Y-m-d\TH:i', strtotime($r->deadline)) : '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Durasi (menit)</label>
                                        <input type="number" name="durasi_pengerjaan" class="form-control"
                                            value="{{ $r->durasi_pengerjaan ?? '' }}" min="1" placeholder="Masukkan durasi">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">KKM</label>
                                        <input type="number" name="kkm" class="form-control" value="{{ $r->kkm }}" min="0" max="100"
                                            placeholder="Masukkan KKM" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Topik</label>
                                        <select name="id_topic" class="form-select edit-single-topic" {{ $r->type !== 'evaluation' ? 'required' : '' }} style="{{ $r->type === 'evaluation' ? 'display: none;' : '' }}">
                                            @foreach(\App\Models\Topic::with('subject')->where('created_by', Auth::id())->get() as $topicOpt)
                                                <option value="{{ $topicOpt->id }}" {{ $topicOpt->id === $r->topic_id ? 'selected' : '' }}>
                                                    {{ $topicOpt->title }} ({{ $topicOpt->subject->name ?? 'Tanpa Subject' }})
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="edit-multiple-topic border rounded p-3 bg-light shadow-sm {{ $r->type === 'evaluation' ? '' : 'd-none' }}"
                                            style="max-height: 180px; overflow-y: auto;">
                                            <p class="text-muted small mb-2">Pilih satu atau lebih topik untuk evaluasi:</p>
                                            <div class="row g-2">
                                                @php
                                                    $selectedTopicIds = is_array($r->topic_ids ?? null) ? $r->topic_ids : [$r->topic_ids];
                                                @endphp
                                                @foreach(\App\Models\Topic::with('subject')->where('created_by', Auth::id())->get() as $topicOpt)
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input edit-topic-checkbox" type="checkbox"
                                                                name="id_topic[]" value="{{ $topicOpt->id }}"
                                                                id="edit_topic_{{ $r->id }}_{{ $topicOpt->id }}" {{ in_array($topicOpt->id, $selectedTopicIds) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="edit_topic_{{ $r->id }}_{{ $topicOpt->id }}">
                                                                {{ $topicOpt->title }} <span
                                                                    class="text-muted small">({{ $topicOpt->subject->name ?? 'Tanpa Subject' }})</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tipe Aktivitas</label>
                                        <select name="type" class="form-select edit-select-type" required>
                                            <option value="evaluation" {{ $r->type === 'evaluation' ? 'selected' : '' }}>Evaluation (Bisa Pilih Banyak Topik)</option>
                                            <option value="quiz" {{ $r->type !== 'evaluation' ? 'selected' : '' }}>Latihan / Lainnya (Hanya Satu Topik)</option>
                                        </select>
                                    </div>

                                    <div class="form-check mb-1 mt-3">
                                        <input type="hidden" name="addaptive" value="no">
                                        <input class="form-check-input" type="checkbox" name="addaptive" value="yes" {{ $r->addaptive === 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label">Adaptif</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Lihat Soal --}}
                <div class="modal fade" id="lihatSoal{{ $r->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-list-check me-2"></i> Daftar Soal – {{ $r->title }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                @php
                                    $selectedQuestions = $questionsMap[$r->id] ?? collect();
                                @endphp

                                @if($selectedQuestions->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-inboxes fs-1 d-block mb-2"></i>
                                        Belum ada soal untuk aktivitas ini.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered align-middle">
                                            <thead class="table-secondary text-center">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="12%">Tipe</th>
                                                    <th width="12%">Kesulitan</th>
                                                    <th>Pertanyaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedQuestions as $s)
                                                    @php $sData = json_decode($s->question); @endphp
                                                    <tr>
                                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                        <td class="text-center"><span class="badge bg-primary">{{ $s->type }}</span></td>
                                                        <td class="text-center">
                                                            @if(in_array($s->difficulty, ['easy', 'mudah']))
                                                                <span class="badge bg-success">Mudah</span>
                                                            @elseif(in_array($s->difficulty, ['medium', 'sedang']))
                                                                <span class="badge bg-warning text-dark">Sedang</span>
                                                            @else
                                                                <span class="badge bg-danger">Sulit</span>
                                                            @endif
                                                        </td>
                                                        <td>{!! nl2br(e($sData->text ?? '-')) !!}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- MODAL INFO AKTIVITAS --}}
            <div class="modal fade" id="modalInfoAktivitas" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content shadow rounded-4 border-0">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle"></i> Informasi Data Evaluasi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-4">
                                Halaman <strong>Data Evaluasi Berdasarkan Topik</strong> digunakan untuk membuat, mengelola, dan mendistribusikan aktivitas evaluasi (<em>kuis / tes</em>) kepada siswa berdasarkan topik pembelajaran.
                            </p>
                            <hr>
                            <section class="mb-4">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Aktivitas
                                </h6>
                                <ul class="ps-3 mb-0">
                                    <li>Membuat evaluasi baru.</li>
                                    <li>Guru wajib mengisi:</li>
                                    <ul class="ps-3 text-muted">
                                        <li>Judul aktivitas</li>
                                        <li>Topik</li>
                                        <li>Deadline <span class="text-muted">(opsional)</span></li>
                                        <li>Durasi pengerjaan</li>
                                    </ul>
                                </ul>
                            </section>
                            <hr>
                            <section class="mb-4">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="bi bi-shuffle me-2"></i>Mode Adaptif (Soal Menyesuaikan Siswa)
                                </h6>
                                <p>
                                    Pada <strong>Mode Adaptif</strong>, setiap siswa akan mendapatkan <strong>alur soal yang berbeda</strong> sesuai dengan kemampuan masing-masing.
                                </p>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .text-ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .text-wrap { white-space: normal; word-wrap: break-word; }
        td.col-title { max-width: 220px; }
        td.col-topic { max-width: 180px; }
        td.col-subject { max-width: 140px; }
        td.col-class { max-width: 120px; }
        td.col-title>.cell-inner, td.col-topic>.cell-inner, td.col-subject>.cell-inner, td.col-class>.cell-inner {
            display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .action-group { display: flex; gap: .35rem; align-items: center; white-space: nowrap; overflow-x: auto; padding: .15rem 0; }
        .action-group .btn { flex: 0 0 auto; }
        @media (max-width: 768px) { .hide-sm { display: none !important; } }
        .dt-scroll-wrapper { overflow-x: auto; }
        .multiple-topic-container { background-color: #f8f9fa; border: 1px solid #dee2e6; transition: all 0.3s ease-in-out; }
        .form-check-input:checked { background-color: #198754; border-color: #198754; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SWEETALERT UNTUK ERROR VALIDASI --}}
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
                    title: 'Gagal Menyimpan Aktivitas!',
                    html: errorMessages,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Tutup'
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.select-type').forEach(function (selectType) {
                selectType.addEventListener('change', function () {
                    let form = this.closest('form');
                    let singleSelect = form.querySelector('.single-topic-select');
                    let checkboxContainer = form.querySelector('.multiple-topic-container');
                    let checkboxes = checkboxContainer.querySelectorAll('.topic-checkbox');

                    if (this.value === 'evaluation') {
                        singleSelect.classList.add('d-none');
                        singleSelect.removeAttribute('required');
                        singleSelect.value = '';
                        checkboxContainer.classList.remove('d-none');
                    } else {
                        singleSelect.classList.remove('d-none');
                        singleSelect.setAttribute('required', 'required');
                        checkboxContainer.classList.add('d-none');
                        checkboxes.forEach(cb => cb.checked = false);
                    }
                });
            });

            document.querySelectorAll('.edit-select-type').forEach(function (selectType) {
                selectType.addEventListener('change', function () {
                    let modalBody = this.closest('.modal-body');
                    let singleSelect = modalBody.querySelector('.edit-single-topic');
                    let checkboxContainer = modalBody.querySelector('.edit-multiple-topic');
                    let checkboxes = checkboxContainer.querySelectorAll('.edit-topic-checkbox');

                    if (this.value === 'evaluation') {
                        singleSelect.style.display = 'none';
                        singleSelect.removeAttribute('required');
                        singleSelect.value = '';
                        checkboxContainer.classList.remove('d-none');
                    } else {
                        singleSelect.style.display = 'block';
                        singleSelect.setAttribute('required', 'required');
                        checkboxContainer.classList.add('d-none');
                        checkboxes.forEach(cb => cb.checked = false);
                    }
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    let form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin hapus aktivitas ini?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            var table = $('#activitiesTable').DataTable({
                responsive: { details: { type: 'column', target: -1 } },
                scrollX: true,
                autoWidth: false,
                lengthChange: true,
                pageLength: 10,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0, 8] },
                    { searchable: false, targets: 0 },
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 2, targets: 7 }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari aktivitas, topik, subject, atau kelas...",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    paginate: { previous: "Sebelumnya", next: "Selanjutnya" }
                },
                drawCallback: function () {
                    var tlist = [].slice.call(document.querySelectorAll('[title]'));
                    tlist.map(function (el) { return new bootstrap.Tooltip(el); });
                }
            });

            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        });
    </script>
@endpush