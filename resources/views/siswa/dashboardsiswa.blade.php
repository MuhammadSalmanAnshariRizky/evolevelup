@extends('layouts.main')

@section('dashboard')
    @if(request()->is('*dashboard*')) active @endif
@endsection

@section('content')
    <style>
        .card {
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #4e73df;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* ========
                    PROFIL SISWA — layout
                    ======== */

        .profile-hero {
            padding-bottom: 0.25rem;
        }

        .profile-name {
            font-size: 1.05rem;
        }

        .profile-actions .btn {
            white-space: nowrap;
        }

        .profile-stat {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            height: 100%;
            padding: 0.65rem 0.8rem;
            border-radius: 0.6rem;
            background: #f8f9fc;
            border-left-width: 4px !important;
        }

        .profile-stat-icon {
            flex: 0 0 auto;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .bg-soft-primary {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }

        .profile-badge-section {
            background: #f8f9fc;
            border-radius: 0.8rem;
            padding: 0.9rem 1rem;
        }

        .min-w-0 {
            min-width: 0;
        }

        th {
            background-color: #4e73df !important;
            color: white;
            text-align: center;
            vertical-align: middle !important;
        }

        td {
            vertical-align: middle !important;
        }

        .nilai-box {
            border-radius: 0.4rem;
            background-color: #f8f9fc;
            padding: 0.3rem 0.5rem;
        }

        .nilai-title {
            font-weight: 600;
            color: #4e73df;
            font-size: 0.8rem;
        }

        .nilai-score {
            font-size: 0.9rem;
            font-weight: bold;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.4em 0.7em;
        }

        .status-card {
            border-left-width: 5px !important;
        }

        .border-start-primary {
            border-left-color: #4e73df !important;
        }

        .border-start-success {
            border-left-color: #1cc88a !important;
        }

        .border-start-danger {
            border-left-color: #e74a3b !important;
        }

        .bg-soft-danger {
            background-color: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }

        .bg-soft-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        /* ========
                    LEADERBOARD — tombol & modal
                    ======== */

        .btn-leaderboard {
            position: relative;
            background: #fff;
            border: 1px solid rgba(246, 194, 62, .55);
            color: #4e73df;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .btn-leaderboard i {
            color: #f6c23e;
        }

        .btn-leaderboard:hover,
        .btn-leaderboard:focus {
            transform: translateY(-1px);
            border-color: #f6c23e;
            box-shadow: 0 6px 16px rgba(246, 194, 62, .3);
            color: #4e73df;
        }

        #leaderboardModal .modal-content {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }

        #leaderboardModal .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #3b5fce 100%);
            color: #fff;
            border-bottom: none;
            padding: 1.1rem 1.4rem;
        }

        #leaderboardModal .modal-title {
            font-weight: 700;
            letter-spacing: .01em;
        }

        #leaderboardModal .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        #leaderboardModal .modal-body {
            background: #f8f9fc;
            padding: 1.25rem 1.4rem 1.4rem;
        }

        #leaderboardModal #kelasSelector {
            border-radius: .6rem;
        }

        .lb-podium {
            display: flex;
            align-items: flex-end;
            gap: .6rem;
            margin-bottom: 1.1rem;
            flex-wrap: wrap;
        }

        .lb-podium-item {
            flex: 1 1 0;
            min-width: 150px;
            display: flex;
            align-items: center;
            gap: .6rem;
            background: #fff;
            border-radius: .7rem;
            padding: .5rem .65rem;
            text-align: left;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            border: 1.5px solid transparent;
            border-top: 3px solid #dee2e6;
        }

        .lb-podium-item.rank-1 {
            order: 2;
            padding: .75rem .7rem;
            border-top-color: #f6c23e;
        }

        .lb-podium-item.rank-2 {
            order: 1;
        }

        .lb-podium-item.rank-3 {
            order: 3;
        }

        .lb-podium-item.lb-me {
            border: 1.5px solid #4e73df;
            border-top: 1.5px solid #4e73df;
            background: rgba(78, 115, 223, .04);
            box-shadow: 0 4px 10px rgba(78, 115, 223, .18);
        }

        .lb-podium-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .lb-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-weight: 700;
            font-size: .82rem;
            background: rgba(78, 115, 223, .1);
            color: #4e73df;
        }

        .lb-podium-item.rank-1 .lb-avatar {
            width: 44px;
            height: 44px;
            font-size: .9rem;
            background: rgba(246, 194, 62, .2);
        }

        .lb-medal-badge {
            position: absolute;
            bottom: -3px;
            right: -3px;
            font-size: .8rem;
            line-height: 1;
            background: #fff;
            border-radius: 50%;
            padding: 1px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .18);
        }

        .lb-podium-info {
            min-width: 0;
            flex: 1;
        }

        .lb-podium-name {
            font-weight: 600;
            font-size: .78rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lb-podium-score {
            font-size: .82rem;
            font-weight: 700;
            color: #4e73df;
        }

        .lb-podium-score .lb-podium-score-unit {
            font-weight: 500;
            font-size: .68rem;
            color: #8a93a6;
        }

        .lb-list-title {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #8a93a6;
            font-weight: 700;
            margin: 0 0 .5rem .1rem;
        }

        .lb-list .list-group-item {
            border: none;
            border-radius: .6rem;
            margin-bottom: .4rem;
            padding: .55rem .8rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .lb-list .list-group-item.lb-me {
            background: rgba(78, 115, 223, .06);
            border-left: 3px solid #4e73df;
        }

        .lb-rank-chip {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
            background: #eef1f8;
            color: #5a6474;
            flex-shrink: 0;
        }

        /* Navigasi pagination tabel Performa (LA) ditengahkan */
        #topicAnalyticsTable_wrapper .dataTables_paginate {
            float: none;
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        #topicAnalyticsTable_wrapper .dataTables_info {
            float: none;
            margin-top: .5rem;
        }

        .table th,
        .table td {
            font-size: 0.9rem;
        }

        .table>thead>tr>th {
            color: white !important
        }

        /* Tambahan / pengganti styling untuk tampilan modal badge */
        .badge-card {
            border-radius: 14px;
            padding: 14px;
            min-height: 150px;
        }

        .badge-card .card-body {
            padding: 0;
        }

        .badge-card .badge-icon {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .badge-card .badge-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .badge-card .badge-desc {
            color: #6c757d;
            font-size: .9rem;
            margin-top: 4px;
        }

        /* badge matches: row layout (2 kolom) */
        .badge-matches-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .badge-matches-list .list-group-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* left / right */
            padding: 0.45rem 0.6rem;
            border-radius: 8px;
            border: 1px solid #eef2f6;
            background: #fff;
            gap: 12px;
            min-height: 44px;
        }

        /* kiri: nama kelas, ambil sisa ruang */
        .badge-matches-list .match-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            /* penting supaya text-overflow bekerja */
            flex: 1 1 auto;
            /* ambil sisa ruang */
        }

        /* class name: potong kalau terlalu panjang */
        .badge-matches-list .class-name {
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* kanan: tombol / pill, tidak mengecil */
        .badge-matches-list .match-right {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* tombol ukuran kecil */
        .btn-claim-class {
            min-width: 120px;
            padding: 0.36rem 0.6rem;
            font-size: 0.86rem;
        }

        /* pill terklaim */
        .claimed-pill {
            background: linear-gradient(180deg, #1cc88a, #17a673);
            color: #fff;
            font-size: 0.82rem;
            padding: 0.32rem 0.6rem;
            border-radius: 999px;
            display: inline-block;
        }

        /* override cepat: pastikan pane tab & kartu badge tetap putih/transparent */
        .tab-content .tab-pane {
            background: transparent !important;
            color: inherit !important;
            padding: 0.5rem 0;
            /* beri jarak bila ingin */
        }

        /* pastikan kartu internal (badge) tidak menerima background global biru */
        .profile-badges-row .card,
        .badge-card,
        .badge-card .card-body,
        #badgeListModal .badge-card {
            background: transparent !important;
            box-shadow: none !important;
            /* optional, jika shadow ikut berpengaruh */
        }

        /* set card internal content tetap putih (jika kamu mau kotak putih di atas latar) */
        .profile-badges-row .card>.d-flex,
        .profile-badges-row .card .card-body {
            background: transparent !important;
        }

        /* jika nav-pills aktif mengubah warna tab (tombol) itu hanya tombol, bukan pane.
                                                                                       namun kalau tombol membungkus pane (struktur salah), pisahkan struktur HTML. */
        .nav-pills .nav-link.active {
            background: #0d6efd;
            /* tetap tombol biru — tidak akan mempengaruhi content */
        }

        /* kalau masih biru, coba override .bg-primary pada parent yang tidak seharusnya */
        .pt-2.border-top>.tab-content,
        .pt-2.border-top>.tab-content .tab-pane {
            background: transparent !important;
        }

        /* ========
                    LEARNING ANALYTICS
                    ======== */

        .analytics-card {
            border: 0 !important;
            border-radius: 1rem !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08) !important;
            overflow: hidden;
        }

        .analytics-card>.card-body {
            padding: 1.25rem;
        }

        /* Header Learning Analytics */
        .analytics-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .analytics-header-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(78, 115, 223, 0.1);
            color: #4e73df;
            font-size: 1.1rem;
        }

        .analytics-header h5 {
            margin: 0;
            font-weight: 700;
            color: #4e73df;
        }

        /* Filter */
        .analytics-filter {
            border: 1px solid #e9ecef !important;
            border-radius: 0.8rem !important;
            box-shadow: none !important;
            background: #fff;
        }

        .analytics-filter .card-body {
            padding: 1rem;
        }

        /* Topic card */
        .topic-card {
            border: 1px solid #e9ecef !important;
            border-radius: 0.9rem !important;
            box-shadow: 0 0.1rem 0.7rem rgba(0, 0, 0, 0.04) !important;
            transition: all 0.2s ease;
        }

        .topic-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.07) !important;
        }

        .topic-card>.card-body {
            padding: 1.15rem;
        }

        /* Topic header */
        .topic-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(78, 115, 223, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4e73df;
        }

        /* Mastery / category cards */
        .analytics-box {
            border: 1px solid #e9ecef !important;
            border-radius: 0.7rem !important;
            background: #fff;
            padding: 1rem;
        }

        /* Difficulty */
        .difficulty-box {
            border: 1px solid #e9ecef;
            border-radius: 0.7rem;
            padding: 0.9rem;
            height: 100%;
            background: #fff;
        }

        /* Recommendation */
        .recommendation-box {
            border: 1px solid #e9ecef;
            border-radius: 0.7rem;
            padding: 0.9rem 1rem;
            background: #f8f9fc;
        }

        /* Konsistensi badge */
        .analytics-card .badge,
        .topic-card .badge {
            border-radius: 0.45rem;
            font-weight: 600;
        }

        /* Progress */
        .analytics-card .progress,
        .topic-card .progress {
            background-color: #eaecf4;
            border-radius: 999px;
            overflow: hidden;
        }

        .analytics-card .progress-bar,
        .topic-card .progress-bar {
            border-radius: 999px;
        }
    
        .analytics-summary-box{display:flex;align-items:center;gap:.75rem;height:100%;padding:.85rem 1rem;border:1px solid #e9ecef;border-radius:.7rem;background:#fff}
        .analytics-summary-icon{width:38px;height:38px;flex:0 0 38px;display:flex;align-items:center;justify-content:center;border-radius:10px;font-size:1rem}
        .analytics-filter .form-select{font-size:.82rem}
</style>

    <div class="container mt-3">

        <!-- 🔹 Profile + Statistik -->
        <div class="row g-3 mb-4">

            <!-- COMBINED: Profile + Stats + Badge (gabungan jadi 1 card) -->
            <div class="col-12">
                <div class="card shadow-sm h-100 status-card border-start-primary">
                    <div class="card-body">
                        <div
                            class="profile-hero d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">

                            {{-- Profile Image + Name --}}
                            <div class="text-center flex-shrink-0">
                                <img src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png"
                                    alt="Foto Profile" class="rounded-circle profile-img mb-2">
                                <h6 class="fw-bold mb-0 text-primary profile-name">{{ $user->name }}</h6>
                                <small class="text-muted d-block">{{ $user->email }}</small>
                            </div>

                            {{-- Right: stats, aksi, badge --}}
                            <div class="flex-fill w-100">

                                {{-- Statistik --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-6 col-lg-4">
                                        <div class="profile-stat border-start-success">
                                            <div class="profile-stat-icon bg-soft-success">
                                                <i class="bi bi-list-check"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="small text-muted">Jumlah Aktivitas</div>
                                                <div class="fw-bold text-success">{{ $jumlahAktivitas }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-lg-4">
                                        <div class="profile-stat border-start-danger">
                                            <div class="profile-stat-icon bg-soft-danger">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="small text-muted">Jumlah Remedial</div>
                                                <div class="fw-bold text-danger">{{ $jumlahRemedial }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-4">
                                        <div class="profile-stat border-start-primary">
                                            <div class="profile-stat-icon bg-soft-primary">
                                                <i class="bi bi-mortarboard"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="small text-muted">Kelas</div>
                                                <div class="fw-bold text-truncate" style="font-size:0.9rem;">
                                                    @if($kelasList->isNotEmpty())
                                                        {{ $kelasList->pluck('name')->implode(', ') }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="d-flex flex-wrap gap-2 mb-3 profile-actions">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalGabungKelas">
                                        <i class="bi bi-box-arrow-in-right"></i> Gabung Kelas
                                    </button>
                                    <button class="btn btn-sm btn-leaderboard" data-bs-toggle="modal"
                                        data-bs-target="#leaderboardModal">
                                        <i class="bi bi-trophy-fill"></i> Leaderboard
                                    </button>
                                </div>

                                <!-- badge siswa -->
                                <div class="pt-2 border-top profile-badge-section">
                                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                        <div class="small text-muted fw-semibold mb-0">Informasi Badge</div>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#badgeListModal">
                                            <i class="bi bi-award"></i> Dapatkan Badge
                                        </button>
                                    </div>

                                    {{-- Tabs: Umum + per-kelas --}}
                                    <ul class="nav nav-pills mb-2" id="badgeTabs" role="tablist">
                                        @foreach($kelasList as $k)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="badge-tab-{{ $k->id }}" data-bs-toggle="pill"
                                                    data-bs-target="#badge-pane-{{ $k->id }}" type="button" role="tab"
                                                    aria-controls="badge-pane-{{ $k->id }}"
                                                    aria-selected="false">{{ $k->name }}</button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="d-none justify-content-end mb-1" id="closeBadgePaneWrap">
                                        <button type="button" id="closeBadgePane"
                                            class="btn btn-sm btn-light border text-muted py-0 px-2"
                                            style="font-size:.75rem;">
                                            <i class="bi bi-x-lg"></i> Tutup
                                        </button>
                                    </div>

                                    {{-- Panes --}}
                                    <div class="tab-content" id="badgeTabContent">
                                        {{-- Per-kelas panes --}}
                                        @foreach($kelasList as $k)
                                            @php $key = 'class_' . $k->id; @endphp
                                            <div class="tab-pane fade" id="badge-pane-{{ $k->id }}" role="tabpanel"
                                                aria-labelledby="badge-tab-{{ $k->id }}">
                                                <div class="row g-2 mt-2 profile-badges-row" id="profile-badges-{{ $k->id }}">
                                                    @if(!empty($badgesByClass[$key]))
                                                        @foreach($badgesByClass[$key] as $ub)
                                                            @php $icon = $ub->path_icon ? asset($ub->path_icon) : asset('img/default.png'); @endphp
                                                            <div class="col-12 col-sm-6 col-md-4" id="profile-badge-{{ $ub->id }}">
                                                                <div class="card h-100 border-0 bg-transparent p-0">
                                                                    <div class="d-flex flex-column align-items-center text-center p-2">
                                                                        <img src="{{ $icon }}" alt="{{ $ub->name }}" width="64"
                                                                            height="64"
                                                                            style="object-fit:contain; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.08);">
                                                                        <div class="mt-2 fw-semibold" style="font-size:0.92rem;">
                                                                            {{ $ub->name }}
                                                                        </div>
                                                                        @if(!empty($ub->description))
                                                                            <div class="small text-muted">{{ $ub->description }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="col-12">
                                                            <div class="mt-1 text-muted">Belum ada badge untuk kelas ini.</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEARNING ANALYTICS SISWA -->
<div class="card analytics-card status-card border-start-primary mb-4">
    <div class="card-body">
        <div class="analytics-header">
            <div class="analytics-header-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <h5>Performa Saya</h5>
                <small class="text-muted">Performa penguasaan pembelajaran berdasarkan topik, jawaban soal, tingkat kesulitan, dan aktivitas.</small>
            </div>
        </div>

        @php
            $studentMasteryCollection = collect($studentMastery ?? []);
            $studentDifficultyCollection = collect($studentDifficulty ?? []);
            $recommendationsCollection = collect($recommendations ?? []);

            $recommendationByTopic = $recommendationsCollection->keyBy(
                fn($item) => (string) data_get($item, 'topic_id')
            );

            $masteryValues = $studentMasteryCollection
                ->map(fn($item) => (float) data_get($item, 'mastery', 0))
                ->filter(fn($value) => is_finite($value));

            $studentActivityPerformanceCollection = collect($studentActivityPerformance ?? []);

            $averageMastery = $masteryValues->isNotEmpty() ? $masteryValues->avg() : 0;
            $masteredTopics = $masteryValues->filter(fn($value) => $value >= 70)->count();
            $needsImprovement = $masteryValues->filter(fn($value) => $value < 70)->count();

            $totalActivityAnswers = $studentActivityPerformanceCollection->sum(
                fn($item) => (int) data_get($item, 'total_answers', 0)
            );
            $totalActivityCorrect = $studentActivityPerformanceCollection->sum(
                fn($item) => (int) data_get($item, 'correct_answers', 0)
            );

            $averageAccuracy = $totalActivityAnswers > 0
                ? round(($totalActivityCorrect / $totalActivityAnswers) * 100, 2)
                : (float) data_get($performanceSummary ?? [], 'average_accuracy', 0);

            $totalActivities = $studentActivityPerformanceCollection
                ->pluck('activity_id')
                ->filter()
                ->unique()
                ->count();

            if ($totalActivities === 0) {
                $totalActivities = (int) data_get($performanceSummary ?? [], 'total_results', 0);
            }
        @endphp

        <!-- RINGKASAN -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analytics-summary-box">
                    <div class="analytics-summary-icon bg-soft-primary"><i class="bi bi-speedometer2"></i></div>
                    <div>
                        <small class="text-muted d-block">Rata-rata Performa</small>
                        <strong class="text-primary fs-5">{{ number_format($averageAccuracy, 1) }}%</strong>
                        <small class="text-muted d-block">{{ $totalActivities }} aktivitas</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analytics-summary-box">
                    <div class="analytics-summary-icon bg-soft-success"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <small class="text-muted d-block">Rata-rata Penguasaan</small>
                        <strong class="text-success fs-5">{{ number_format($averageMastery, 1) }}%</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analytics-summary-box">
                    <div class="analytics-summary-icon bg-soft-success"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <small class="text-muted d-block">Topik Dikuasai</small>
                        <strong class="text-success fs-5">{{ $masteredTopics }}</strong>
                        <small class="text-muted">topik</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="analytics-summary-box">
                    <div class="analytics-summary-icon bg-soft-danger"><i class="bi bi-lightbulb"></i></div>
                    <div>
                        <small class="text-muted d-block">Perlu Diperkuat</small>
                        <strong class="text-danger fs-5">{{ $needsImprovement }}</strong>
                        <small class="text-muted">topik</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card analytics-filter mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-filter text-primary me-2"></i>
                    <div>
                        <h6 class="fw-bold mb-0">Filter</h6>
                        <small class="text-muted">Pilih konteks pembelajaran yang ingin ditampilkan.</small>
                    </div>
                </div>
                <form method="GET" action="{{ route('dashboard.siswa') }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold">Kelas</label>
                            <select name="class_id" id="analyticsClass" class="form-select form-select-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($analyticsClasses as $kelas)
                                    <option value="{{ $kelas->id }}" {{ (string) $filterClassId === (string) $kelas->id ? 'selected' : '' }}>{{ $kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold">Mata Pelajaran</label>
                            <select name="subject_id" id="analyticsSubject" class="form-select form-select-sm">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($analyticsSubjects as $subject)
                                    <option value="{{ $subject->id }}" data-class="{{ $subject->id_class }}" {{ (string) $filterSubjectId === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold">Topik</label>
                            <select name="topic_id" id="analyticsTopic" class="form-select form-select-sm">
                                <option value="">Semua Topik</option>
                                @foreach($analyticsTopics as $topic)
                                    <option value="{{ $topic->id }}" data-subject="{{ $topic->id_subject }}" {{ (string) $filterTopicId === (string) $topic->id ? 'selected' : '' }}>{{ $topic->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold">Aktivitas</label>
                            <select name="activity_id" id="analyticsActivity" class="form-select form-select-sm">
                                <option value="">Semua Aktivitas</option>
                                @foreach($analyticsActivities as $activity)
                                    <option value="{{ $activity->id }}" data-topic="{{ $activity->id_topic }}" {{ (string) $filterActivityId === (string) $activity->id ? 'selected' : '' }}>{{ $activity->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Terapkan Filter</button>
                        <a href="{{ route('dashboard.siswa') }}" class="btn btn-light border btn-sm"><i class="fas fa-undo me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- REKAP TOPIK -->
        @if($studentMasteryCollection->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-bar-chart fs-1 d-block mb-3"></i>
                Belum terdapat data Learning Analytics untuk konteks pembelajaran yang dipilih.
            </div>
        @else
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Rekap Penguasaan Topik</h6>
                    <small class="text-muted">Klik satu baris untuk melihat detail performa.</small>
                </div>
                <span class="badge bg-light text-primary border">{{ $studentMasteryCollection->count() }} topik</span>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table id="topicAnalyticsTable" class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:48px;">No</th>
                            <th class="text-start">Topik</th>
                            <th style="min-width:210px;">Penguasaan</th>
                            <th>Kategori</th>
                            <th>Rekomendasi</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentMasteryCollection as $index => $mastery)
                            @php
                                $topicId = data_get($mastery, 'topic_id');
                                $masteryValue = min(100, max(0, (float) data_get($mastery, 'mastery', 0)));

                                if ($masteryValue >= 85) {
                                    $categoryLabel = 'Mahir';
                                    $categoryColor = 'success';
                                    $recommendationLabel = 'Sangat Baik';
                                    $recommendationColor = 'success';
                                } elseif ($masteryValue >= 70) {
                                    $categoryLabel = 'Menguasai';
                                    $categoryColor = 'primary';
                                    $recommendationLabel = 'Baik';
                                    $recommendationColor = 'info';
                                } elseif ($masteryValue >= 50) {
                                    $categoryLabel = 'Cukup';
                                    $categoryColor = 'info';
                                    $recommendationLabel = 'Perlu Ditingkatkan';
                                    $recommendationColor = 'warning';
                                } else {
                                    $categoryLabel = 'Belum Menguasai';
                                    $categoryColor = 'danger';
                                    $recommendationLabel = 'Penguatan';
                                    $recommendationColor = 'danger';
                                }
                            @endphp
                            <tr role="button" data-bs-toggle="modal" data-bs-target="#topicDetailModal-{{ $topicId }}" style="cursor:pointer;">
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start fw-semibold">{{ data_get($mastery, 'topic_name', 'Topik') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height:8px;">
                                            <div class="progress-bar bg-primary" style="width:{{ $masteryValue }}%;"></div>
                                        </div>
                                        <small class="text-muted" style="white-space:nowrap;">{{ number_format($masteryValue, 1) }}%</small>
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-{{ $categoryColor }}{{ in_array($categoryColor, ['warning', 'info']) ? ' text-dark' : '' }}">{{ $categoryLabel }}</span></td>
                                <td class="text-center"><span class="badge bg-{{ $recommendationColor }}{{ in_array($recommendationColor, ['warning', 'info']) ? ' text-dark' : '' }}">{{ $recommendationLabel }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#topicDetailModal-{{ $topicId }}" aria-label="Detail {{ data_get($mastery, 'topic_name', 'Topik') }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- MOBILE -->
            <div class="d-block d-md-none">
                @foreach($studentMasteryCollection as $mastery)
                    @php
                        $topicId = data_get($mastery, 'topic_id');
                        $masteryValue = min(100, max(0, (float) data_get($mastery, 'mastery', 0)));

                        if ($masteryValue >= 85) {
                            $categoryLabel = 'Mahir';
                            $categoryColor = 'success';
                            $recommendationLabel = 'Sangat Baik';
                            $recommendationColor = 'success';
                        } elseif ($masteryValue >= 70) {
                            $categoryLabel = 'Menguasai';
                            $categoryColor = 'primary';
                            $recommendationLabel = 'Baik';
                            $recommendationColor = 'info';
                        } elseif ($masteryValue >= 50) {
                            $categoryLabel = 'Cukup';
                            $categoryColor = 'info';
                            $recommendationLabel = 'Perlu Ditingkatkan';
                            $recommendationColor = 'warning';
                        } else {
                            $categoryLabel = 'Belum Menguasai';
                            $categoryColor = 'danger';
                            $recommendationLabel = 'Penguatan';
                            $recommendationColor = 'danger';
                        }
                    @endphp
                    <div class="card topic-card mb-3" role="button" data-bs-toggle="modal" data-bs-target="#topicDetailModal-{{ $topicId }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="topic-icon"><i class="bi bi-book"></i></div>
                                    <div class="fw-bold">{{ data_get($mastery, 'topic_name', 'Topik') }}</div>
                                </div>
                                <span class="badge bg-{{ $categoryColor }}{{ in_array($categoryColor, ['warning', 'info']) ? ' text-dark' : '' }}">{{ $categoryLabel }}</span>
                            </div>
                            <div class="progress mb-1" style="height:8px;"><div class="progress-bar bg-primary" style="width:{{ $masteryValue }}%;"></div></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ number_format($masteryValue, 1) }}%</small>
                                <span class="badge bg-{{ $recommendationColor }}{{ in_array($recommendationColor, ['warning', 'info']) ? ' text-dark' : '' }}">{{ $recommendationLabel }}</span>
                            </div>
                            <div class="text-end mt-1"><small class="text-primary fw-semibold">Lihat detail <i class="bi bi-chevron-right"></i></small></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- 🔹 Daftar Nilai (dengan filter kelas) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-bar-chart-line me-1"></i> Daftar Nilai</h5>

            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <div>
                    <label class="small text-muted mb-1 d-block">Filter Kelas</label>
                    <select id="filterKelas" class="form-select form-select-sm" style="min-width:200px;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ e($k->name) }}">{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="ms-auto">
                    <small class="text-muted">Jumlah: <strong id="countVisible">{{ $nilaiList->count() }}</strong></small>
                </div>
            </div>

            <div class="table-responsive">

                {{-- Jika data kosong, tampilkan pesan saja --}}
                @if($nilaiList->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inboxes fs-1 d-block mb-2"></i>
                        Belum ada data nilai.
                    </div>

                @else
                    {{-- Jika ada data, tampilkan tabel --}}
                    {{-- DESKTOP: DataTable --}}
                    <div class="d-none d-md-block">
                        <table id="nilaiTable" class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Topik</th>
                                    <th>Nama Aktivitas</th>
                                    <th>Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nilaiList as $index => $n)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($n->result_created_at)->format('d M Y H:i') }}</td>
                                        <td>{{ $n->kelas ?? '-' }}</td>
                                        <td>{{ $n->mapel ?? '-' }}</td>
                                        <td>{{ $n->topik ?? $n->aktivitas ?? '-' }}</td>
                                        <td>{{ $n->aktivitas ?? '-' }}</td>
                                        <td>
                                            {{ is_null($n->nilai_akhir) || $n->nilai_akhir === '-' ? 'Belum Mengerjakan' : $n->nilai_akhir }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- MOBILE: Card List --}}
                    <div class="d-block d-md-none">

                        @forelse($nilaiList as $n)
                            <div class="card shadow-sm mb-3 border-0">
                                <div class="card-body">

                                    <div class="fw-bold mb-1">
                                        {{ $n->aktivitas ?? '-' }}
                                    </div>

                                    <div class="small text-muted mb-2">
                                        {{ \Carbon\Carbon::parse($n->result_created_at)->format('d M Y H:i') }}
                                    </div>

                                    <div class="mb-2">
                                        <div><strong>Kelas:</strong> {{ $n->kelas ?? '-' }}</div>
                                        <div><strong>Mapel:</strong> {{ $n->mapel ?? '-' }}</div>
                                        <div><strong>Topik:</strong> {{ $n->topik ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <span class="badge {{ is_numeric($n->nilai_akhir) ? 'bg-success' : 'bg-secondary' }}">
                                            {{ is_numeric($n->nilai_akhir) ? 'Nilai: ' . $n->nilai_akhir : 'Belum Mengerjakan' }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                Belum ada data nilai.
                            </div>
                        @endforelse

                    </div>

                @endif
            </div>

        </div>
    </div>

    <!-- MODAL DETAIL LEARNING ANALYTICS PER TOPIK -->
@foreach($studentMasteryCollection as $mastery)
    @php
        $topicId = data_get($mastery, 'topic_id');
        $masteryValue = min(100, max(0, (float) data_get($mastery, 'mastery', 0)));
        $recommendation = $recommendationByTopic->get((string) $topicId);

        if ($masteryValue >= 85) {
            $categoryLabel = 'Mahir';
            $categoryColor = 'success';
            $recommendationLabel = 'Sangat Baik';
            $recommendationColor = 'success';
        } elseif ($masteryValue >= 70) {
            $categoryLabel = 'Menguasai';
            $categoryColor = 'primary';
            $recommendationLabel = 'Baik';
            $recommendationColor = 'info';
        } elseif ($masteryValue >= 50) {
            $categoryLabel = 'Cukup';
            $categoryColor = 'info';
            $recommendationLabel = 'Perlu Ditingkatkan';
            $recommendationColor = 'warning';
        } else {
            $categoryLabel = 'Belum Menguasai';
            $categoryColor = 'danger';
            $recommendationLabel = 'Penguatan';
            $recommendationColor = 'danger';
        }

        $topicDifficulty = $studentDifficultyCollection
            ->filter(fn($item) => (int) data_get($item, 'topic_id') === (int) $topicId)
            ->values();

        $topicActivities = $studentActivityPerformanceCollection
            ->filter(fn($item) => (int) data_get($item, 'topic_id') === (int) $topicId)
            ->values();

        $topicAccuracy = (float) data_get($mastery, 'accuracy', 0);
        $topicTotalAnswers = (int) data_get($mastery, 'total_answers', 0);
        $topicCorrectAnswers = (int) data_get($mastery, 'correct_answers', 0);

        $recommendationText = data_get($recommendation, 'recommendation');
        if (!$recommendationText) {
            $recommendationText = match ($recommendationLabel) {
                'Penguatan' => 'Pelajari kembali materi dan perbanyak latihan untuk memperkuat pemahaman topik.',
                'Perlu Ditingkatkan' => 'Perkuat pemahaman melalui latihan yang lebih konsisten sebelum melanjutkan ke materi berikutnya.',
                'Baik' => 'Pertahankan penguasaan topik dan lanjutkan latihan dengan tingkat kesulitan yang lebih tinggi.',
                'Sangat Baik' => 'Pertahankan penguasaan melalui latihan lanjutan dan soal tingkat sulit.',
            };
        }
    @endphp

    <div class="modal fade" id="topicDetailModal-{{ $topicId }}" tabindex="-1" aria-labelledby="topicDetailModalLabel-{{ $topicId }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="topicDetailModalLabel-{{ $topicId }}">
                            <i class="bi bi-book text-primary me-2"></i>{{ data_get($mastery, 'topic_name', 'Topik') }}
                        </h5>
                        <small class="text-muted">{{ data_get($mastery, 'subject_name', '') }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <!-- RINGKASAN -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="analytics-box h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Penguasaan Topik</span>
                                    <strong class="text-primary">{{ number_format($masteryValue, 1) }}%</strong>
                                </div>
                                <div class="progress" style="height:9px;">
                                    <div class="progress-bar bg-primary" style="width:{{ $masteryValue }}%;"></div>
                                </div>
                                <div class="small text-muted mt-2">Kategori: <strong>{{ $categoryLabel }}</strong></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="analytics-box h-100">
                                <div class="small text-muted mb-1">Performa / Akurasi</div>
                                <strong class="text-primary fs-4">{{ number_format($topicAccuracy, 1) }}%</strong>
                                <div class="small text-muted">{{ $topicCorrectAnswers }} benar dari {{ $topicTotalAnswers }} jawaban</div>
                            </div>
                        </div>
                    </div>

                    <!-- SEBARAN KESULITAN -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-bar-chart text-primary me-2"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Sebaran Tingkat Kesulitan</h6>
                                <small class="text-muted">Akurasi jawaban pada setiap tingkat kesulitan.</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach(['mudah' => 'Mudah', 'sedang' => 'Sedang', 'sulit' => 'Sulit'] as $difficultyKey => $difficultyLabel)
                                @php
                                    $difficulty = $topicDifficulty->firstWhere('difficulty', $difficultyKey);
                                    $accuracy = (float) data_get($difficulty, 'accuracy', 0);
                                    $total = (int) data_get($difficulty, 'total_answers', 0);
                                    $difficultyColor = $difficultyKey === 'mudah' ? 'success' : ($difficultyKey === 'sedang' ? 'warning' : 'danger');
                                @endphp
                                <div class="col-12 col-md-4">
                                    <div class="difficulty-box h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold">{{ $difficultyLabel }}</span>
                                            <strong class="text-{{ $difficultyColor }}">{{ number_format($accuracy, 1) }}%</strong>
                                        </div>
                                        <div class="progress mb-1" style="height:8px;">
                                            <div class="progress-bar bg-{{ $difficultyColor }}" style="width:{{ min(100, max(0, $accuracy)) }}%;"></div>
                                        </div>
                                        <small class="text-muted">{{ $total }} jawaban</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- PERFORMA PER AKTIVITAS -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-activity text-primary me-2"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Performa Per Aktivitas</h6>
                                <small class="text-muted">Hasil performa pada setiap aktivitas pembelajaran.</small>
                            </div>
                        </div>

                        @if($topicActivities->isEmpty())
                            <div class="analytics-box text-center text-muted py-3">
                                Belum terdapat data performa aktivitas untuk topik ini.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Aktivitas</th>
                                            <th class="text-center">Benar</th>
                                            <th class="text-center">Jawaban</th>
                                            <th class="text-center">Akurasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topicActivities as $activity)
                                            @php
                                                $accuracy = (float) data_get($activity, 'accuracy', 0);
                                                $correctAnswers = (int) data_get($activity, 'correct_answers', 0);
                                                $totalAnswers = (int) data_get($activity, 'total_answers', 0);
                                                $incorrectAnswers = (int) data_get($activity, 'incorrect_answers', max(0, $totalAnswers - $correctAnswers));
                                            @endphp
                                            <tr>
                                                <td>{{ data_get($activity, 'activity_name', 'Aktivitas') }}</td>
                                                <td class="text-center text-success">{{ $correctAnswers }}</td>
                                                <td class="text-center">{{ $totalAnswers }}</td>
                                                <td class="text-center fw-semibold">{{ number_format($accuracy, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- REKOMENDASI -->
                    <div class="recommendation-box">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-lightbulb-fill text-warning fs-4 me-3"></i>
                            <div>
                                <div class="small text-muted mb-2">Rekomendasi Belajar</div>
                                <span class="badge bg-{{ $recommendationColor }}{{ in_array($recommendationColor, ['warning', 'info']) ? ' text-dark' : '' }} mb-2">{{ $recommendationLabel }}</span>
                                <div class="small">{{ $recommendationText }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Leaderboard -->
    <div class="modal fade" id="leaderboardModal" tabindex="-1" aria-labelledby="leaderboardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaderboardModalLabel">
                        <i class="bi bi-trophy-fill me-1"></i> Leaderboard
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($kelasList->count() > 1)
                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Kelas</label>
                            <select id="kelasSelector" class="form-select form-select-sm">
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <small class="text-muted d-block mb-3">
                            Kelas: {{ $kelasList->first()->name ?? '-' }}
                        </small>
                    @endif

                    <div id="leaderboardArea" style="max-height:420px; overflow-y:auto; padding-right:6px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Daftar Semua Badge  -->
    <div class="modal fade" id="badgeListModal" tabindex="-1" aria-labelledby="badgeListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="badgeListModalLabel">Badge Tersedia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @if(!isset($allBadges) || $allBadges->isEmpty())
                        <div class="text-center text-muted py-4">Belum ada data badge di sistem.</div>
                    @else
                        <div class="row g-3">
                            @foreach($allBadges as $b)
                                @php
                                    $icon = $b->path_icon ? asset($b->path_icon) : asset('img/default.png');
                                    $isClaimed = in_array($b->id, $claimedBadgeIds ?? []);
                                @endphp

                                <div class="col-12 col-sm-6 col-md-4" id="badge-card-{{ $b->id }}">
                                    <div class="card h-100 shadow-sm badge-card">
                                        <div class="card-body d-flex gap-3">
                                            <img src="{{ $icon }}" alt="{{ $b->name }}" class="badge-icon ">
                                            <div class=" min-w-0">
                                                <div class="badge-title mb-1">{{ $b->name }}</div>
                                                <div class="badge-desc small text-muted mb-2">{{ $b->description }}</div>

                                                <!-- JS akan memasukkan daftar kelas eligible di sini -->
                                                <div class="badge-matches-wrapper"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal gabung kelas -->
    <div class="modal fade" id="modalGabungKelas" tabindex="-1" aria-labelledby="modalGabungKelasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('student.gabungKelas') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGabungKelasLabel">Gabung Kelas dengan Token</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small">Masukkan Token Kelas</label>
                            <input type="text" name="token" class="form-control form-control-sm" placeholder="Token kelas"
                                required>
                        </div>
                        <div class="small text-muted">Token biasanya diberikan oleh guru. Pastikan memasukkan token dengan
                            benar.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Gabung</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTables --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function () {
                // inisialisasi DataTable dan simpan instance
                var table = $('#nilaiTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ baris",
                        info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                        paginate: { previous: "← Sebelumnya", next: "Berikutnya →" },
                        zeroRecords: "Tidak ditemukan data yang sesuai."
                    },
                    // disable automatic order pada kolom No agar numbering manual
                    order: [],
                    columnDefs: [
                        { orderable: false, targets: 0 } // kolom No tidak bisa di-sort
                    ]
                });

                // fungsi escape regex untuk nilai kelas (hindari karakter regex bermasalah)
                function escapeRegex(str) {
                    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                // filter by kelas (kolom index 2)
                $('#filterKelas').on('change', function () {
                    var val = $(this).val();
                    if (!val) {
                        // kosong => tampilkan semua
                        table.column(2).search('').draw();
                    } else {
                        // exact match menggunakan regex anchors ^...$
                        var regex = '^' + escapeRegex(val) + '$';
                        table.column(2).search(regex, true, false).draw();
                    }
                });

                // update numbering (kolom No) setelah setiap draw (filter/pagination/sort)
                table.on('draw.dt', function () {
                    var info = table.page.info();
                    // loop rows yang sedang tampil dan set nomor berdasar index di display (1..n)
                    table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
                        // nomor relatif ke halaman: i + 1 + (page * length)
                        var pageInfo = table.page.info();
                        var num = pageInfo.start + i + 1;
                        cell.innerHTML = num;
                    });

                    // update count visible
                    $('#countVisible').text(table.rows({ search: 'applied' }).count());
                });

                // trigger pertama supaya count & numbering benar saat load
                table.draw();
            });
        </script>
        <script>
            $(document).ready(function () {
                // DataTable untuk rekap Learning Analytics per topik (tabel Performa)
                if ($('#topicAnalyticsTable').length) {
                    var topicTable = $('#topicAnalyticsTable').DataTable({
                        responsive: true,
                        pageLength: 5,
                        lengthChange: false,
                        searching: false,
                        dom: 'tip',
                        language: {
                            info: "Menampilkan _START_–_END_ dari _TOTAL_ topik",
                            infoEmpty: "Tidak ada topik untuk ditampilkan",
                            paginate: { previous: "← Sebelumnya", next: "Berikutnya →" },
                            zeroRecords: "Tidak ditemukan data yang sesuai."
                        },
                        // disable automatic order pada kolom No & Aksi
                        order: [],
                        columnDefs: [
                            { orderable: false, targets: [0, 5] }
                        ]
                    });

                    // update numbering (kolom No) setelah setiap draw (pagination)
                    topicTable.on('draw.dt', function () {
                        var pageInfo = topicTable.page.info();
                        topicTable.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
                            cell.innerHTML = pageInfo.start + i + 1;
                        });
                    });

                    topicTable.draw();
                }
            });
        </script>
        <script>
            // ambil data dari backend (array of {class_id, class_name, students})
            const leaderboardsPerClass = @json($leaderboardsPerClass);
            const myUserId = {{ $user->id }};

            function lbInitials(name) {
                if (!name) return '?';
                const parts = name.trim().split(/\s+/);
                const first = parts[0]?.[0] || '';
                const last = parts.length > 1 ? parts[parts.length - 1][0] : '';
                return (first + last).toUpperCase();
            }

            const LB_MEDALS = { 1: '🥇', 2: '🥈', 3: '🥉' };

            function lbPodiumCardHTML(row, rank) {
                const isMe = (row.id == myUserId);
                return `
                            <div class="lb-podium-item rank-${rank} ${isMe ? 'lb-me' : ''}">
                                <div class="lb-podium-avatar-wrap">
                                    <div class="lb-avatar">${lbInitials(row.name)}</div>
                                    <span class="lb-medal-badge">${LB_MEDALS[rank]}</span>
                                </div>
                                <div class="lb-podium-info">
                                    <div class="lb-podium-name" title="${row.name}">${row.name}${isMe ? ' (Anda)' : ''}</div>
                                    <div class="lb-podium-score">${Number(row.total_score).toLocaleString()} <span class="lb-podium-score-unit">poin</span></div>
                                </div>
                            </div>
                        `;
            }

            function lbListRowHTML(row, rank) {
                const isMe = (row.id == myUserId);
                return `
                            <li class="list-group-item d-flex justify-content-between align-items-center ${isMe ? 'lb-me' : ''}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="lb-rank-chip">${rank}</div>
                                    <div class="lb-avatar" style="width:34px;height:34px;font-size:.78rem;margin:0;">${lbInitials(row.name)}</div>
                                    <div class="fw-semibold">${row.name}${isMe ? ' <span class="badge bg-primary bg-opacity-10 text-primary ms-1">Anda</span>' : ''}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">${Number(row.total_score).toLocaleString()}</div>
                                    <small class="text-muted">poin</small>
                                </div>
                            </li>
                        `;
            }

            // helper render
            function renderLeaderboardForClass(classId) {
                const block = leaderboardsPerClass.find(c => c.class_id == classId);
                const area = document.getElementById('leaderboardArea');

                if (!block || !block.students || block.students.length === 0) {
                    area.innerHTML = `
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-trophy fs-1 d-block mb-2 opacity-50"></i>
                                    Belum ada data leaderboard untuk kelas ini.
                                </div>
                            `;
                    return;
                }

                const top3 = block.students.slice(0, 3);
                const rest = block.students.slice(3);

                let html = '';

                if (top3.length > 0) {
                    html += '<div class="lb-podium">';
                    top3.forEach((row, idx) => { html += lbPodiumCardHTML(row, idx + 1); });
                    html += '</div>';
                }

                if (rest.length > 0) {
                    html += '<div class="lb-list-title">Peringkat Lainnya</div>';
                    html += '<ul class="list-group list-group-flush lb-list">';
                    rest.forEach((row, idx) => { html += lbListRowHTML(row, idx + 4); });
                    html += '</ul>';
                }

                area.innerHTML = html;
            }

            // inisialisasi: gunakan kelas pertama jika ada
            if (leaderboardsPerClass.length > 0) {
                renderLeaderboardForClass(leaderboardsPerClass[0].class_id);
            }

            // swap handler
            const sel = document.getElementById('kelasSelector');
            if (sel) {
                sel.addEventListener('change', function () {
                    renderLeaderboardForClass(this.value);
                });
            }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Informasi Badge: tampilkan tombol Tutup saat sebuah kelas dibuka,
                // dan tutup kembali panel badge saat tombol tersebut diklik.
                const badgeTabs = document.getElementById('badgeTabs');
                const closeBadgePaneWrap = document.getElementById('closeBadgePaneWrap');
                const closeBadgePaneBtn = document.getElementById('closeBadgePane');

                if (badgeTabs && closeBadgePaneWrap && closeBadgePaneBtn) {
                    badgeTabs.querySelectorAll('button[data-bs-toggle="pill"]').forEach(function (tabBtn) {
                        tabBtn.addEventListener('shown.bs.tab', function () {
                            closeBadgePaneWrap.classList.remove('d-none');
                            closeBadgePaneWrap.classList.add('d-flex');
                        });
                    });

                    closeBadgePaneBtn.addEventListener('click', function () {
                        badgeTabs.querySelectorAll('.nav-link').forEach(function (link) {
                            link.classList.remove('active');
                            link.setAttribute('aria-selected', 'false');
                        });
                        document.querySelectorAll('#badgeTabContent .tab-pane').forEach(function (pane) {
                            pane.classList.remove('show', 'active');
                        });
                        closeBadgePaneWrap.classList.add('d-none');
                        closeBadgePaneWrap.classList.remove('d-flex');
                    });
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const badgeModal = document.getElementById('badgeListModal');

                async function checkEligibilityFor(badgeId) {
                    try {
                        const res = await fetch("{{ url('/badges') }}/" + badgeId + "/eligibility", {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        return await res.json();
                    } catch (err) {
                        console.error('eligibility fetch error', err);
                        return { eligible: false, reason: 'Gagal mengecek syarat (network).' };
                    }
                }

                async function refreshBadgeEligibility() {
                    // kosongkan semua wrapper dulu (hindari duplikat)
                    document.querySelectorAll('.badge-matches-wrapper').forEach(w => w.innerHTML = '');

                    const cards = Array.from(document.querySelectorAll('[id^="badge-card-"]'));
                    for (const card of cards) {
                        const badgeId = card.id.replace('badge-card-', '').trim();
                        const wrapper = card.querySelector('.badge-matches-wrapper');

                        if (!wrapper) continue;

                        // tampilkan loading sederhana
                        wrapper.innerHTML = '<div class="small text-muted">Memeriksa syarat…</div>';

                        const json = await checkEligibilityFor(badgeId);

                        // clear
                        wrapper.innerHTML = '';

                        // setelah menerima `json` dan sudah clear wrapper:
                        if (json.eligible && Array.isArray(json.matches) && json.matches.length) {
                            // jika semua sudah claimed -> cukup tandai footer sebagai terklaim
                            const allClaimed = json.matches.every(m => !!m.already_claimed);
                            const cardFooter = card.querySelector('.mt-3.text-end');

                            if (allClaimed) {
                                // tampilkan pesan di wrapper dan tandai footer
                                wrapper.innerHTML = '<div class="small text-muted">Sudah diklaim di semua kelas.</div>';
                                if (cardFooter) cardFooter.innerHTML = '<span class="claimed-pill">Terklaim</span>';
                                continue; // lanjut ke card berikutnya
                            }

                            // tidak semua claimed -> render daftar, tapi tetap tunjukkan pill per baris jika sudah claimed
                            const list = document.createElement('div');
                            list.className = 'badge-matches-list';

                            json.matches.forEach(m => {
                                const item = document.createElement('div');
                                item.className = 'list-group-item';

                                const left = document.createElement('div');
                                left.className = 'match-left';
                                left.innerHTML = `<div class="class-name">${escapeHtml(m.class_name)}</div>`;

                                const right = document.createElement('div');

                                if (m.already_claimed) {
                                    right.innerHTML = '<span class="claimed-pill">Terklaim</span>';
                                } else {
                                    const btn = document.createElement('button');
                                    btn.className = 'btn btn-sm btn-primary btn-claim-class';
                                    btn.dataset.badgeId = badgeId;
                                    btn.dataset.classId = m.class_id;
                                    btn.type = 'button';
                                    btn.textContent = 'Klaim';
                                    right.appendChild(btn);
                                }

                                item.appendChild(left);
                                item.appendChild(right);
                                list.appendChild(item);
                            });

                            wrapper.appendChild(list);
                        } else {
                            // tidak eligible: tampilkan alasan
                            const reason = json.reason || 'Belum memenuhi syarat.';
                            wrapper.innerHTML = `<div class="small text-muted">${escapeHtml(reason)}</div>`;
                        }

                    }
                }

                // delegated click handler untuk klaim per kelas
                document.addEventListener('click', function (e) {
                    const t = e.target;
                    if (!t) return;
                    if (t.classList.contains('btn-claim-class')) {
                        const badgeId = t.dataset.badgeId;
                        const classId = t.dataset.classId;
                        const originalText = t.innerText;
                        t.disabled = true;
                        t.innerText = 'Memproses...';

                        fetch("{{ route('badges.claim') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ badge_id: badgeId, class_id: classId })
                        })
                            .then(r => r.json())
                            .then(res => {
                                if (res && res.success) {
                                    if (typeof Swal !== 'undefined') Swal.fire('Sukses', res.message || 'Badge diklaim', 'success');

                                    // update baris yang diklaim
                                    const listItem = t.closest('.list-group-item');
                                    if (listItem) {
                                        const right = t.parentElement;
                                        right.innerHTML = '<span class="claimed-pill">Terklaim</span>';
                                    }

                                } else {
                                    const msg = (res && (res.message || res.reason)) || 'Gagal klaim';
                                    if (typeof Swal !== 'undefined') Swal.fire('Gagal', msg, 'error');
                                    t.disabled = false;
                                    t.innerText = originalText;
                                }
                            })
                            .catch(err => {
                                console.error('claim error', err);
                                if (typeof Swal !== 'undefined') Swal.fire('Error', 'Gagal menghubungi server', 'error');
                                t.disabled = false;
                                t.innerText = originalText;
                            });
                    }
                });

                if (badgeModal) badgeModal.addEventListener('show.bs.modal', refreshBadgeEligibility);

                function escapeHtml(str) {
                    if (typeof str !== 'string') return str || '';
                    return str.replace(/[&<>"'`=\/]/g, function (s) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s];
                    });
                }
            });

        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const classSelect = document.getElementById('analyticsClass');
                const subjectSelect = document.getElementById('analyticsSubject');
                const topicSelect = document.getElementById('analyticsTopic');
                const activitySelect = document.getElementById('analyticsActivity');

                const selectedSubject = "{{ $filterSubjectId ?? '' }}";
                const selectedTopic = "{{ $filterTopicId ?? '' }}";
                const selectedActivity = "{{ $filterActivityId ?? '' }}";

                function filterSubjects() {

                    const classId = classSelect.value;

                    Array.from(subjectSelect.options).forEach(option => {

                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        const optionClass = option.dataset.class;

                        option.hidden =
                            classId !== '' &&
                            optionClass !== classId;

                    });

                    if (
                        subjectSelect.value &&
                        subjectSelect.selectedOptions[0]?.hidden
                    ) {
                        subjectSelect.value = '';
                    }
                }

                function filterTopics() {

                    const subjectId = subjectSelect.value;

                    Array.from(topicSelect.options).forEach(option => {

                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        const optionSubject = option.dataset.subject;

                        option.hidden =
                            subjectId !== '' &&
                            optionSubject !== subjectId;

                    });

                    if (
                        topicSelect.value &&
                        topicSelect.selectedOptions[0]?.hidden
                    ) {
                        topicSelect.value = '';
                    }
                }

                function filterActivities() {

                    const topicId = topicSelect.value;

                    Array.from(activitySelect.options).forEach(option => {

                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        const optionTopic = option.dataset.topic;

                        option.hidden =
                            topicId !== '' &&
                            optionTopic !== topicId;

                    });

                    if (
                        activitySelect.value &&
                        activitySelect.selectedOptions[0]?.hidden
                    ) {
                        activitySelect.value = '';
                    }
                }

                classSelect.addEventListener('change', function () {

                    // Jika kelas berubah, mapel/topik/aktivitas harus mengikuti kelas baru.
                    filterSubjects();

                    subjectSelect.value = '';
                    topicSelect.value = '';
                    activitySelect.value = '';

                    filterTopics();
                    filterActivities();
                });

                subjectSelect.addEventListener('change', function () {

                    filterTopics();

                    topicSelect.value = '';
                    activitySelect.value = '';

                    filterActivities();
                });

                topicSelect.addEventListener('change', function () {

                    filterActivities();

                    activitySelect.value = '';
                });

                // Inisialisasi awal
                filterSubjects();
                filterTopics();
                filterActivities();

            });
        </script>

        <script>
            $(function () {
                // ----- helper: build HTML badge untuk profil -----
                function buildProfileBadgeHtml(badge) {
                    var icon = badge.path_icon || '{{ asset("img/default.png") }}';
                    var safeName = $('<div/>').text(badge.name || '').html();
                    var safeDesc = $('<div/>').text(badge.description || '').html();

                    return `
                                                                                                                                                                                                        <div class="col-12 col-sm-6 col-md-4" id="profile-badge-${badge.id}">
                                                                                                                                                                                                            <div class="card h-100 border-0 bg-transparent p-0">
                                                                                                                                                                                                                <div class="d-flex flex-column align-items-center text-center p-2">
                                                                                                                                                                                                                    <img src="${icon}" alt="${safeName}" width="64" height="64"
                                                                                                                                                                                                                        style="object-fit:contain; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.08);">
                                                                                                                                                                                                                    <div class="mt-2 fw-semibold" style="font-size:0.92rem;">${safeName}</div>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>`;
                }

                // ----- helper: cari / buat container badge profil -----
                function ensureProfileBadgeContainerJQ() {
                    // cari container yang sudah ada (sesuai blade: .row.g-2.mt-2 di bawah Informasi Badge)
                    var $infoSection = null;
                    $('.pt-2.border-top').each(function () {
                        if ($(this).find('.small.text-muted').first().text().trim().indexOf('Informasi Badge') !== -1) {
                            $infoSection = $(this);
                            return false;
                        }
                    });

                    if (!$infoSection || !$infoSection.length) {
                        // fallback cari berdasarkan tombol modal
                        $infoSection = $('button[data-bs-target="#badgeListModal"]').closest('.pt-2.border-top');
                        if (!$infoSection || !$infoSection.length) $infoSection = $('.col-12.col-md-6 .card-body').first();
                    }

                    // coba dapatkan row container yang ada
                    var $container = $infoSection.find('.profile-badges-row').first();
                    if (!$container || !$container.length) {
                        // jika blade sudah ada row.g-2.mt-2 gunakan itu; kalau tidak buat baru
                        var $existingRow = $infoSection.find('.row.g-2.mt-2').first();
                        if ($existingRow && $existingRow.length) {
                            $existingRow.addClass('profile-badges-row');
                            return $existingRow;
                        }
                        // buat row baru
                        $container = $('<div class="row g-2 mt-2 profile-badges-row"></div>');
                        // jika ada teks placeholder "Belum ada badge" hapus
                        $infoSection.find('.mt-1.text-muted:contains("Belum ada badge")').remove();
                        $infoSection.append($container);
                    }
                    return $container;
                }

                // ----- helper: tambahkan badge ke profil (tanpa duplikasi) -----
                function addBadgeToProfile(badge) {
                    if (!badge || !badge.id) return;
                    var $container = ensureProfileBadgeContainerJQ();
                    if ($('#profile-badge-' + badge.id).length) return; // cegah duplikat
                    var html = buildProfileBadgeHtml(badge);
                    $container.append(html);
                }

                // ----- small escape helper for fallback reads -----
                function escapeHtml(str) {
                    if (typeof str !== 'string') return str || '';
                    return str.replace(/[&<>"'`=\/]/g, function (s) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s];
                    });
                }

                // ----- Intercept legacy form claim (global claim button) -----
                $(document).on('submit', '.badge-claim-form', function (e) {
                    e.preventDefault();
                    var $form = $(this);
                    var badgeId = $form.data('badge-id');
                    var $btn = $form.find('.claim-btn');
                    if (!$btn.length || $btn.prop('disabled')) return;

                    $btn.prop('disabled', true).text('Memproses…');
                    var token = $('meta[name="csrf-token"]').attr('content') || $form.find('input[name="_token"]').val();

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: JSON.stringify({ badge_id: badgeId }),
                        contentType: 'application/json',
                        headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                        success: function (res) {
                            if (res && res.success) {
                                if (typeof Swal !== 'undefined') Swal.fire('Berhasil', res.message || 'Badge diklaim', 'success');

                                // update modal footer/card
                                var $card = $('#badge-card-' + badgeId);
                                if ($card.length) $card.find('.mt-3.text-end').html('<span class="claimed-pill">Terklaim</span>');

                                // ambil badge object dari response atau fallback membaca dari DOM card
                                var badge = res.badge || {};
                                if (!badge.path_icon && $card.length) badge.path_icon = $card.find('img').first().attr('src') || '{{ asset("img/default.png") }}';
                                if (!badge.name && $card.length) badge.name = $card.find('.badge-title').first().text().trim() || 'Badge';
                                if (!badge.description && $card.length) badge.description = $card.find('.badge-desc').first().text().trim() || '';
                                badge.id = badge.id || badgeId;

                                // langsung tambahkan ke profil tanpa refresh
                                addBadgeToProfile(badge);

                                // disable duplicate buttons if ada
                                $('.claim-btn[data-badge-id="' + badgeId + '"]').prop('disabled', true).text('Terklaim');

                            } else {
                                var msg = (res && (res.message || res.reason)) || 'Gagal klaim badge.';
                                if (typeof Swal !== 'undefined') Swal.fire('Gagal', msg, 'error');
                                $btn.prop('disabled', false).text('Klaim');
                            }
                        },
                        error: function (xhr) {
                            var json = xhr.responseJSON || {};
                            var msg = json.message || json.reason || 'Terjadi kesalahan jaringan/server.';
                            if (typeof Swal !== 'undefined') Swal.fire('Error', msg, 'error');
                            $btn.prop('disabled', false).text('Klaim');
                        }
                    });
                });

                // ----- Delegated handler untuk tombol 'Klaim' per-kelas -----
                document.addEventListener('click', function (e) {
                    const t = e.target;
                    if (!t) return;
                    if (t.classList && t.classList.contains('btn-claim-class')) {
                        const badgeId = t.dataset.badgeId;
                        const classId = t.dataset.classId;
                        const originalText = t.innerText;
                        t.disabled = true;
                        t.innerText = 'Memproses...';

                        fetch("{{ route('badges.claim') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ badge_id: badgeId, class_id: classId })
                        })
                            .then(r => r.json().catch(() => ({ success: false, message: 'Invalid JSON' })))
                            .then(res => {
                                if (res && res.success) {
                                    if (typeof Swal !== 'undefined') Swal.fire('Sukses', res.message || 'Badge diklaim', 'success');

                                    // ubah tombol menjadi terklaim
                                    const listItem = t.closest('.list-group-item');
                                    if (listItem) {
                                        const right = t.parentElement;
                                        right.innerHTML = '<span class="claimed-pill">Terklaim</span>';
                                    }

                                    // update footer badge card
                                    const card = document.getElementById('badge-card-' + badgeId);
                                    if (card) {
                                        const footer = card.querySelector('.mt-3.text-end');
                                        if (footer) footer.innerHTML = '<span class="claimed-pill">Terklaim</span>';
                                    }

                                    // ambil badge data dari response atau fallback ke DOM card
                                    var badge = res.badge || {};
                                    if (!badge.path_icon && card) badge.path_icon = card.querySelector('img')?.getAttribute('src') || '{{ asset("img/default.png") }}';
                                    if (!badge.name && card) badge.name = card.querySelector('.badge-title')?.textContent.trim() || 'Badge';
                                    if (!badge.description && card) badge.description = card.querySelector('.badge-desc')?.textContent.trim() || '';
                                    badge.id = badge.id || badgeId;

                                    // tambahkan ke profil langsung
                                    addBadgeToProfile(badge);

                                } else {
                                    const msg = (res && (res.message || res.reason)) || 'Gagal klaim';
                                    if (typeof Swal !== 'undefined') Swal.fire('Gagal', msg, 'error');
                                    t.disabled = false;
                                    t.innerText = originalText;
                                }
                            })
                            .catch(err => {
                                console.error('claim error', err);
                                if (typeof Swal !== 'undefined') Swal.fire('Error', 'Gagal menghubungi server', 'error');
                                t.disabled = false;
                                t.innerText = originalText;
                            });
                    }
                });

            }); 
        </script>
    @endpush
    {{-- SWEETALERT FLASH MESSAGE --}}
    @if(session('swal_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('swal_error')),
                    confirmButtonColor: '#e74a3b'
                });
            });
        </script>
    @endif

    @if(session('swal_warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: @json(session('swal_warning')),
                    confirmButtonColor: '#f6c23e'
                });
            });
        </script>
    @endif

    @if(session('swal_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('swal_success')),
                    confirmButtonColor: '#1cc88a'
                });
            });
        </script>
    @endif

@endsection