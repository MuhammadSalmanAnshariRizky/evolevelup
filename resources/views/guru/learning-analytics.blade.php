@extends('layouts.main')
@section('title', 'Learning Analytics')
@section('learningAnalytics', 'active')
@section('content')
    <style>
        :root {
            --la-ink: #343a40;
            --la-soft: #5a5c69;
            --la-faint: #858796;
            --la-paper: #f8f9fc;
            --la-panel: #fff;
            --la-line: #dddfeb;
            --la-line-soft: #eaecf4;
            --la-blue: #4e73df;
            --la-blue-soft: #e8eefc;
            --la-gold: #f6c23e;
            --la-gold-soft: #fff5d8;
            --la-green: #1cc88a;
            --la-green-soft: #e3f8f0;
            --la-amber: #f6c23e;
            --la-amber-soft: #fff5d8;
            --la-rust: #e74a3b;
            --la-rust-soft: #fbe8e6
        }

        .la-dashboard {
            min-height: calc(100vh - 100px);
            background: var(--la-paper);
            color: var(--la-ink);
            font-family: 'Nunito', sans-serif
        }

        .la-topbar {
            background: var(--la-blue);
            color: #fff;
            padding: 22px 32px 20px
        }

        .la-topbar-inner,
        .la-filterbar,
        .la-main {
            max-width: 1180px;
            margin: auto
        }

        .la-topbar-inner {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap
        }

        .la-eyebrow {
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: .14em;
            font-size: 11px;
            font-weight: 700;
            color: #dbe3ff
        }

        .la-topbar h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 600
        }

        .la-sub {
            margin: 6px 0 0;
            max-width: 680px;
            color: #e8eaf1;
            font-size: 13.5px;
            line-height: 1.5
        }

        .la-filterbar {
            padding: 18px 32px 0
        }

        .la-filter-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: flex-end;
            padding: 16px 18px;
            background: #fff;
            border: 1px solid var(--la-line);
            border-radius: 14px
        }

        .la-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1 1 150px;
            min-width: 150px
        }

        .la-field.la-student {
            flex: 1.3 1 190px
        }

        .la-field label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
            color: var(--la-faint)
        }

        .la-field select,
        .la-field input {
            width: 100%;
            height: 40px;
            border: 1px solid var(--la-line);
            border-radius: 9px;
            background: var(--la-paper);
            color: var(--la-ink);
            padding: 0 12px;
            font:11px 'Nunito',sans-serif;
            outline: 0
        }

        .la-field select:focus,
        .la-field input:focus {
            border-color: var(--la-blue);
            box-shadow: 0 0 0 3px var(--la-blue-soft)
        }

        .la-filter-actions {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .la-btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 9px;
            font: 700 12px 'Nunito',sans-serif;;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent
        }

        .la-btn-primary {
            background: var(--la-blue);
            color: #fff
        }

        .la-btn-primary:hover {
            background: #3f63c4;
            color: #fff
        }

        .la-btn-reset {
            background: #fff;
            border-color: var(--la-line);
            color: var(--la-soft)
        }

        .la-btn-reset:hover {
            background: var(--la-paper);
            color: var(--la-ink)
        }

        .la-main {
            padding: 18px 32px 50px
        }

        .la-legend-note {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 11px 14px;
            background: #fff;
            border: 1px solid var(--la-line);
            border-radius: 11px;
            font-size: 11px;
            color: var(--la-soft)
        }

        .la-legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px
        }

        .la-subject-block {
            margin-bottom: 25px
        }

        .la-subject-heading {
            display: flex;
            align-items: baseline;
            gap: 8px;
            padding-bottom: 9px;
            border-bottom: 1px solid var(--la-line);
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 700
        }

        .la-subject-heading .count {
            font-size: 10px;
            font-weight: 500;
            color: var(--la-faint)
        }

        .la-topic-card {
            overflow: hidden;
            margin-bottom: 14px;
            background: #fff;
            border: 1px solid var(--la-line);
            border-radius: 14px
        }

        .la-topic-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 17px 20px;
            border-bottom: 1px solid var(--la-line-soft)
        }

        .la-topic-head h2 {
            margin: 0;
            font-size: 17px;
            font-weight: 700
        }

        .la-topic-meta {
            margin-top: 4px;
            color: var(--la-faint);
            font-size: 10.5px
        }

        .la-mastery-pill {
            display: inline-block;
            flex-shrink: 0;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap
        }

        .la-pill-mahir {
            background: var(--la-blue-soft);
            color: var(--la-blue)
        }

        .la-pill-menguasai {
            background: var(--la-green-soft);
            color: var(--la-green)
        }

        .la-pill-cukup {
            background: var(--la-amber-soft);
            color: var(--la-amber)
        }

        .la-pill-belum {
            background: var(--la-rust-soft);
            color: var(--la-rust)
        }

        .la-indicator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.4fr 1.15fr
        }

        .la-indicator {
            padding: 15px 16px;
            min-height: 145px;
            border-right: 1px solid var(--la-line-soft)
        }

        .la-indicator:last-child {
            border-right: 0
        }

        .la-indicator-label {
            margin-bottom: 7px;
            color: var(--la-faint);
            text-transform: uppercase;
            letter-spacing: .07em;
            font-size: 9px;
            font-weight: 700
        }

        .la-perf-score {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 3px
        }

        .la-perf-score .num {
            font-size: 28px;
            font-weight: 700
        }

        .la-perf-score .max,
        .la-perf-delta,
        .la-perf-compare {
            font-size: 10px;
            color: var(--la-faint)
        }

        .la-perf-compare {
            margin-top: 4px
        }

        .la-mastery-row {
            display: flex;
            align-items: baseline;
            gap: 4px
        }

        .la-mastery-num {
            font-size: 27px;
            font-weight: 700
        }

        .la-mastery-unit {
            font-size: 10px;
            color: var(--la-faint)
        }

        .la-segbar {
            display: flex;
            gap: 4px;
            margin-top: 8px
        }

        .la-segbar i {
            height: 5px;
            flex: 1;
            border-radius: 3px;
            background: var(--la-line)
        }

        .la-segbar i.filled {
            background: currentColor
        }

        .la-difficulty-bars {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            align-items: end;
            height: 90px
        }

        .la-difficulty-item {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            height: 100%
        }

        .la-difficulty-value {
            text-align: center;
            margin-bottom: 3px;
            font-size: 9px;
            font-weight: 700
        }

        .la-difficulty-bar {
            width: 100%;
            min-height: 6px;
            border-radius: 5px 5px 2px 2px
        }

        .la-difficulty-label,
        .la-difficulty-count {
            text-align: center;
            font-size: 9px;
            color: var(--la-faint)
        }

        .la-difficulty-label {
            margin-top: 5px
        }

        .la-difficulty-count {
            margin-top: 2px;
            font-size: 8px
        }

        .la-topic-recommendation {
            min-height: 90px;
            padding: 12px 14px;
            background: var(--la-paper);
            border-left: 3px solid var(--la-blue);
            border-radius: 7px
        }

        .la-topic-recommendation-title {
            margin-bottom: 5px;
            color: var(--la-blue);
            text-transform: uppercase;
            letter-spacing: .07em;
            font-size: 9px;
            font-weight: 700
        }

        .la-topic-recommendation-text {
            margin: 0;
            color: var(--la-soft);
            font-size: 10.5px;
            line-height: 1.5
        }

        .la-student-section{
            padding:15px 20px 18px;
        }

        .la-student-section-title{
            display:flex;
            align-items:center;
            gap:5px;
            color:var(--la-blue);
            font-size:11px;
            font-weight:700;
            cursor:pointer;
            user-select:none;
            margin-bottom:0;
        }

        .la-student-section-title .arrow{
            display:inline-block;
            transition:transform .2s ease;
        }

        .la-student-section-title.open{
            margin-bottom:10px;
        }

        .la-student-section-title.open .arrow{
            transform:rotate(180deg);
        }

        .la-student-table-wrap{
            max-height:0;
            overflow:hidden;
            opacity:0;
            margin-top:0;
            transition:max-height .25s ease,opacity .2s ease,margin-top .2s ease;
        }

        .la-student-table-wrap.open{
            max-height:2000px;
            opacity:1;
            margin-top:10px;
            overflow-x:auto;
        }

        .la-student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px
        }

        .la-student-table th {
            padding: 8px 9px;
            text-align: left;
            color: var(--la-faint);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 8px;
            font-weight: 700;
            border-bottom: 1px solid var(--la-line);
            white-space: nowrap
        }

        .la-student-table td {
            padding: 10px 9px;
            border-bottom: 1px solid var(--la-line-soft);
            vertical-align: middle
        }

        .la-student-table tr:last-child td {
            border-bottom: 0
        }

        .la-student-row {
            cursor: pointer;
            transition: background .15s ease
        }

        .la-student-row:hover {
            background: var(--la-paper)
        }

        .la-student-row:focus {
            outline: 2px solid var(--la-blue);
            outline-offset: -2px
        }

        .la-stu-name,
        .la-stu-score {
            font-weight: 700
        }

        .la-student-difficulty-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
            min-width: 350px
        }

        .la-student-diff-item {
            min-width: 0
        }

        .la-student-diff-label {
            margin-bottom: 1px;
            color: var(--la-faint);
            font-size: 8px
        }

        .la-student-diff-value {
            font-size: 10px;
            font-weight: 700
        }

        .la-student-diff-count {
            margin-left: 2px;
            color: var(--la-faint);
            font-size: 8px;
            font-weight: 400
        }

        .la-student-diff-track {
            height: 4px;
            margin-top: 4px;
            background: var(--la-line);
            border-radius: 4px;
            overflow: hidden
        }

        .la-student-diff-fill {
            height: 100%;
            border-radius: inherit
        }

        .easy {
            background: var(--la-green)
        }

        .medium {
            background: var(--la-amber)
        }

        .hard {
            background: var(--la-rust)
        }

        .la-reco-button {
            border: 0;
            padding: 6px 9px;
            border-radius: 999px;
            font: 700 9px inherit;
            cursor: pointer;
            transition: transform .15s, opacity .15s
        }

        .la-reco-button:hover {
            transform: translateY(-1px);
            opacity: .9
        }

        .la-reco-rust {
            background: var(--la-rust-soft);
            color: var(--la-rust)
        }

        .la-reco-amber {
            background: var(--la-amber-soft);
            color: var(--la-amber)
        }

        .la-reco-teal {
            background: var(--la-blue-soft);
            color: var(--la-blue)
        }

        .la-reco-green {
            background: var(--la-green-soft);
            color: var(--la-green)
        }

        .la-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(20, 30, 50, .45)
        }

        .la-modal.open {
            display: flex
        }

        .la-modal-card {
            width: min(700px, 100%);
            max-height: calc(100vh - 40px);
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--la-line);
            border-top: 4px solid var(--la-blue);
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .18);
            display: flex;
            flex-direction: column
        }

        .la-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 20px;
            border-bottom: 1px solid var(--la-line-soft)
        }

        .la-modal-header h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 700
        }

        .la-modal-meta {
            margin-top: 4px;
            color: var(--la-faint);
            font-size: 10.5px
        }

        .la-modal-close {
            width: 30px;
            height: 30px;
            border: 1px solid var(--la-line);
            border-radius: 8px;
            background: #fff;
            color: var(--la-soft);
            font-size: 18px;
            line-height: 1;
            cursor: pointer
        }

        .la-modal-body {
            overflow-y: auto;
            padding: 18px 20px 20px
        }

        .la-modal-section-label {
            margin: 4px 0 8px;
            color: var(--la-faint);
            text-transform: uppercase;
            letter-spacing: .09em;
            font-size: 9px;
            font-weight: 700
        }

        .la-modal-recommendation {
            padding: 13px 15px;
            margin-bottom: 17px;
            border-radius: 10px
        }

        .la-modal-recommendation.rust {
            background: var(--la-rust-soft)
        }

        .la-modal-recommendation.amber {
            background: var(--la-amber-soft)
        }

        .la-modal-recommendation.teal {
            background: var(--la-blue-soft)
        }

        .la-modal-recommendation.green {
            background: var(--la-green-soft)
        }

        .la-modal-category {
            display: inline-flex;
            padding: 4px 8px;
            margin-bottom: 7px;
            border-radius: 999px;
            background: #fff;
            font-size: 9px;
            font-weight: 700
        }

        .la-modal-category.rust {
            color: var(--la-rust)
        }

        .la-modal-category.amber {
            color: var(--la-amber)
        }

        .la-modal-category.teal {
            color: var(--la-blue)
        }

        .la-modal-category.green {
            color: var(--la-green)
        }

        .la-modal-text {
            margin: 0;
            color: var(--la-soft);
            font-size: 11px;
            line-height: 1.55
        }

        .la-modal-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 18px
        }

        .la-modal-stat {
            padding: 10px;
            background: var(--la-paper);
            border: 1px solid var(--la-line-soft);
            border-radius: 10px
        }

        .la-modal-stat-label {
            margin-bottom: 4px;
            color: var(--la-faint);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 8px;
            font-weight: 700
        }

        .la-modal-stat-value {
            font-size: 15px;
            font-weight: 700
        }

        .la-modal-stat-sub {
            display: block;
            margin-top: 2px;
            color: var(--la-faint);
            font-size: 8px;
            line-height: 1.4
        }

        .la-modal-activity-list {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .la-modal-activity-item {
            padding: 11px 12px;
            background: var(--la-paper);
            border: 1px solid var(--la-line-soft);
            border-radius: 11px
        }

        .la-modal-activity-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px
        }

        .la-modal-activity-info {
            min-width: 0;
            flex: 1
        }

        .la-modal-activity-name {
            color: var(--la-ink);
            font-size: 11.5px;
            font-weight: 700;
            line-height: 1.4
        }

        .la-modal-activity-type {
            margin-top: 2px;
            color: var(--la-faint);
            font-size: 9px
        }

        .la-modal-activity-score {
            flex-shrink: 0;
            font-size: 14px;
            font-weight: 700
        }

        .la-modal-activity-track {
            width: 100%;
            height: 5px;
            margin-top: 8px;
            background: var(--la-line);
            border-radius: 5px;
            overflow: hidden
        }

        .la-modal-activity-fill {
            height: 100%;
            border-radius: inherit
        }

        .la-modal-activity-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px;
            color: var(--la-faint);
            font-size: 9px
        }

        .la-modal-activity-correct {
            color: var(--la-green)
        }

        .la-modal-activity-incorrect {
            color: var(--la-rust)
        }

        .la-modal-empty,
        .la-empty-state {
            padding: 15px;
            text-align: center;
            color: var(--la-faint);
            font-size: 10px;
            background: var(--la-paper);
            border: 1px solid var(--la-line-soft);
            border-radius: 10px
        }

        .la-empty-state {
            padding: 55px 20px;
            background: #fff;
            border-color: var(--la-line);
            border-radius: 14px;
            font-size: 12px
        }

        @media(max-width:900px) {
            .la-indicator-grid {
                grid-template-columns: 1fr 1fr
            }

            .la-indicator {
                border-right: 0;
                border-bottom: 1px solid var(--la-line-soft)
            }

            .la-indicator:nth-child(odd) {
                border-right: 1px solid var(--la-line-soft)
            }

            .la-modal-stats-grid {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media(max-width:650px) {
            .la-topbar {
                padding: 20px
            }

            .la-filterbar {
                padding: 12px 15px 0
            }

            .la-main {
                padding: 15px 15px 35px
            }

            .la-topbar h1 {
                font-size: 25px
            }

            .la-topic-head {
                align-items: flex-start;
                flex-direction: column
            }

            .la-student-table {
                min-width: 850px
            }

            .la-modal-stats-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:480px) {
            .la-indicator-grid {
                grid-template-columns: 1fr
            }

            .la-indicator:nth-child(odd) {
                border-right: 0
            }
        }
    </style>

    <div class="la-dashboard">
        <div class="la-topbar">
            <div class="la-topbar-inner">
                <div>
                    <p class="la-eyebrow">Learning Analytics</p>
                    <h1>Penguasaan Topik</h1>
                    <p class="la-sub">Performa, penguasaan materi, sebaran tingkat kesulitan, dan rekomendasi — dirangkum
                        per topik dari aktivitas yang telah dikerjakan siswa.</p>
                </div>
            </div>
        </div>

        <div class="la-filterbar">
            <form method="GET" action="{{ route('guru.learningAnalytics') }}" class="la-filter-row">
                <div class="la-field"><label>Kelas</label><select name="class_id">
                        <option value="">Semua Kelas</option>@foreach($analyticsClasses as $class)
                            <option value="{{ $class->id }}" {{ (string) $filterClassId === (string) $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                        </option>@endforeach
                    </select></div>
                <div class="la-field"><label>Mata Pelajaran</label><select name="subject_id">
                        <option value="">Semua Mata Pelajaran</option>@foreach($analyticsSubjects as $subject)
                        <option value="{{ $subject->id }}" {{ (string) $filterSubjectId === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach
                    </select></div>
                <div class="la-field"><label>Topik</label><select name="topic_id">
                        <option value="">Semua Topik</option>@foreach($analyticsTopics as $topic)
                            <option value="{{ $topic->id }}" {{ (string) $filterTopicId === (string) $topic->id ? 'selected' : '' }}>
                                {{ $topic->title }}
                        </option>@endforeach
                    </select></div>
                <div class="la-field"><label>Aktivitas</label><select name="activity_id">
                        <option value="">Semua Aktivitas</option>@foreach($analyticsActivities as $activity)
                            <option value="{{ $activity->id }}" {{ (string) $filterActivityId === (string) $activity->id ? 'selected' : '' }}>{{ $activity->title }}
                        </option>@endforeach
                    </select></div>
                <div class="la-field la-student"><label>Siswa</label><input type="text" name="student"
                        value="{{ $studentSearch }}" placeholder="Cari nama siswa..." list="analyticsStudents"><datalist
                        id="analyticsStudents">@foreach($analyticsStudents as $student)
                        <option value="{{ $student->name }}">@endforeach
                    </datalist></div>
                <div class="la-filter-actions"><button class="la-btn la-btn-primary" type="submit">Terapkan</button><a
                        class="la-btn la-btn-reset" href="{{ route('guru.learningAnalytics') }}">Reset</a></div>
            </form>
        </div>

        <main class="la-main">
            <div class="la-legend-note">
                <span><span class="la-legend-dot" style="background:var(--la-blue)"></span><b>Performa</b></span>
                <span><span class="la-legend-dot" style="background:var(--la-gold)"></span><b>Penguasaan</b></span>
                <span><span class="la-legend-dot" style="background:var(--la-green)"></span>Mudah <span
                        class="la-legend-dot" style="background:var(--la-amber);margin-left:8px"></span>Sedang <span
                        class="la-legend-dot" style="background:var(--la-rust);margin-left:8px"></span>Sulit</span>
            </div>

            @php $groupedTopics = $topicMastery->groupBy(fn($item) => $item['subject_id'] ?? 'all'); @endphp
            @if($topicMastery->count())
                @foreach($groupedTopics as $subjectId => $topics)
                    @php $firstTopic = $topics->first();
                    $subjectName = $firstTopic['subject_name'] ?? 'Mata Pelajaran'; @endphp
                    <div class="la-subject-block">
                        <div class="la-subject-heading">{{ $subjectName }} <span class="count">{{ $topics->count() }} topik</span>
                        </div>
                        @foreach($topics as $topic)
                            @php
                                $topicId = (int) ($topic['topic_id'] ?? 0);
                                $mastery = (float) ($topic['mastery'] ?? 0);
                                $accuracy = (float) ($topic['accuracy'] ?? 0);
                                if ($mastery >= 85) {
                                    $tierKey = 'mahir';
                                    $tierLabel = 'Mahir';
                                } elseif ($mastery >= 70) {
                                    $tierKey = 'menguasai';
                                    $tierLabel = 'Menguasai';
                                } elseif ($mastery >= 50) {
                                    $tierKey = 'cukup';
                                    $tierLabel = 'Cukup';
                                } else {
                                    $tierKey = 'belum';
                                    $tierLabel = 'Belum Menguasai';
                                }

                                /* Ambil agregat difficulty. Fallback ke data per siswa jika diperlukan. */
                                $topicDifficultyRows = collect($topicDifficulty ?? [])->filter(fn($item) => (int) data_get($item, 'topic_id') === $topicId)->values();
                                $difficultySummary = ['mudah' => ['accuracy' => 0, 'total_answers' => 0], 'sedang' => ['accuracy' => 0, 'total_answers' => 0], 'sulit' => ['accuracy' => 0, 'total_answers' => 0]];
                                foreach ($topicDifficultyRows as $row) {
                                    $d = strtolower(trim((string) data_get($row, 'difficulty', '')));
                                    $d = match ($d) { 'easy', '1' => 'mudah', 'medium', '2' => 'sedang', 'hard', '3' => 'sulit', default => $d};
                                    if (!isset($difficultySummary[$d]))
                                        continue;
                                    $difficultySummary[$d] = ['accuracy' => (float) data_get($row, 'accuracy', 0), 'total_answers' => (int) data_get($row, 'total_answers', 0)];
                                }
                                if (!$topicDifficultyRows->count()) {
                                    $fallback = collect($studentTopicDifficulty ?? [])->filter(fn($item) => (int) data_get($item, 'topic_id') === $topicId)->groupBy(function ($item) {
                                        $d = strtolower(trim((string) data_get($item, 'difficulty', '')));
                                        return match ($d) { 'easy', '1' => 'mudah', 'medium', '2' => 'sedang', 'hard', '3' => 'sulit', default => $d};
                                    });
                                    foreach ($fallback as $d => $rows) {
                                        $total = $rows->sum(fn($r) => (int) data_get($r, 'total_answers', 0));
                                        $correct = $rows->sum(fn($r) => (int) data_get($r, 'correct_answers', 0));
                                        $difficultySummary[$d] = ['accuracy' => $total ? round($correct / $total * 100, 2) : 0, 'total_answers' => $total];
                                    }
                                }
                                $easyAccuracy = $difficultySummary['mudah']['accuracy'];
                                $mediumAccuracy = $difficultySummary['sedang']['accuracy'];
                                $hardAccuracy = $difficultySummary['sulit']['accuracy'];
                                $easyTotal = $difficultySummary['mudah']['total_answers'];
                                $mediumTotal = $difficultySummary['sedang']['total_answers'];
                                $hardTotal = $difficultySummary['sulit']['total_answers'];
                                if (!$topicDifficultyRows->count() && ($easyTotal + $mediumTotal + $hardTotal) == 0) {
                                    $easyAccuracy = $mediumAccuracy = $hardAccuracy = 0;
                                }
                                if ($hardAccuracy < 50) {
                                    $topicRecoColor = 'rust';
                                    $topicRecoTitle = 'PERLU PERHATIAN';
                                    $topicRecoText = 'Performa pada soal tingkat sulit masih rendah. Fokuskan latihan pada pemahaman konsep dan soal tingkat sulit.';
                                } elseif ($mediumAccuracy < 60) {
                                    $topicRecoColor = 'amber';
                                    $topicRecoTitle = 'PERKUAT PEMAHAMAN';
                                    $topicRecoText = 'Performa pada soal tingkat sedang masih perlu diperkuat sebelum meningkatkan latihan pada soal yang lebih sulit.';
                                } elseif ($mastery >= 85) {
                                    $topicRecoColor = 'green';
                                    $topicRecoTitle = 'SIAP PENGAYAAN';
                                    $topicRecoText = 'Penguasaan topik sangat baik. Siswa dapat diberikan latihan pengayaan atau soal tingkat sulit.';
                                } else {
                                    $topicRecoColor = 'teal';
                                    $topicRecoTitle = 'PERTAHANKAN';
                                    $topicRecoText = 'Penguasaan topik sudah baik. Pertahankan pemahaman melalui latihan yang konsisten.';
                                }
                                $topicStudents = $studentTopicMastery->filter(fn($s) => (int) data_get($s, 'topic_id') === $topicId)->values();
                            @endphp

                            <div class="la-topic-card">
                                <div class="la-topic-head">
                                    <div>
                                        <h2>{{ $topic['topic_name'] }}</h2>
                                        <div class="la-topic-meta">{{ $subjectName }} · {{ $topic['total_answers'] ?? 0 }} jawaban</div>
                                    </div><span class="la-mastery-pill la-pill-{{ $tierKey }}">Penguasaan:
                                        {{ number_format($mastery, 0) }}% · {{ $tierLabel }}</span>
                                </div>
                                <div class="la-indicator-grid">
                                    <div class="la-indicator">
                                        <div class="la-indicator-label">Performa</div>
                                        <div class="la-perf-score"><span class="num">{{ number_format($accuracy, 0) }}</span><span
                                                class="max">/100</span></div>
                                        <div class="la-perf-delta">Akurasi jawaban topik</div>
                                        <div class="la-perf-compare">Benar: <b>{{ $topic['correct_answers'] ?? 0 }}</b> /
                                            {{ $topic['total_answers'] ?? 0 }}
                                        </div>
                                    </div>
                                    <div class="la-indicator">
                                        <div class="la-indicator-label">Penguasaan Materi</div>
                                        <div class="la-mastery-row"><span class="la-mastery-num"
                                                style="color:{{ $tierKey === 'mahir' ? 'var(--la-blue)' : ($tierKey === 'menguasai' ? 'var(--la-green)' : ($tierKey === 'cukup' ? 'var(--la-amber)' : 'var(--la-rust)')) }}">{{ number_format($mastery, 0) }}%</span><span
                                                class="la-mastery-unit">/100</span></div>
                                        <div class="la-segbar"
                                            style="color:{{ $tierKey === 'mahir' ? 'var(--la-blue)' : ($tierKey === 'menguasai' ? 'var(--la-green)' : ($tierKey === 'cukup' ? 'var(--la-amber)' : 'var(--la-rust)')) }}">
                                            @for($i = 1; $i <= 4; $i++)<i class="{{ $mastery >= $i * 25 ? 'filled' : '' }}"></i>@endfor
                                        </div>
                                        <div class="la-perf-delta">Kategori: <b>{{ $tierLabel }}</b></div>
                                    </div>
                                    <div class="la-indicator">
                                        <div class="la-indicator-label">Sebaran Tingkat Kesulitan</div>
                                        <div class="la-difficulty-bars">
                                            @foreach([['key' => 'mudah', 'label' => 'Mudah', 'color' => 'var(--la-green)', 'total' => $easyTotal, 'value' => $easyAccuracy], ['key' => 'sedang', 'label' => 'Sedang', 'color' => 'var(--la-amber)', 'total' => $mediumTotal, 'value' => $mediumAccuracy], ['key' => 'sulit', 'label' => 'Sulit', 'color' => 'var(--la-rust)', 'total' => $hardTotal, 'value' => $hardAccuracy]] as $diff)
                                                <div class="la-difficulty-item">
                                                    <div class="la-difficulty-value" style="color:{{ $diff['color'] }}">
                                                        {{ number_format($diff['value'], 0) }}%
                                                    </div>
                                                    <div class="la-difficulty-bar"
                                                        style="height:{{ max(8, $diff['value'] * .55) }}px;background:{{ $diff['color'] }}">
                                                    </div>
                                                    <div class="la-difficulty-label">{{ $diff['label'] }}</div>
                                                    <div class="la-difficulty-count">{{ $diff['total'] }} jawaban</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="la-indicator">
                                        <div class="la-topic-recommendation" style="border-left-color:var(--la-{{ $topicRecoColor }})">
                                            <div class="la-topic-recommendation-title" style="color:var(--la-{{ $topicRecoColor }})">
                                                {{ $topicRecoTitle }}
                                            </div>
                                            <p class="la-topic-recommendation-text">{{ $topicRecoText }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="la-student-section">
                                    <div class="la-student-section-title" data-toggle-students="student-table-{{ $topicId }}"><span
                                            class="arrow">▾</span> Rekap Per Siswa</div>
                                    <div class="la-student-table-wrap" id="student-table-{{ $topicId }}">
                                        <div style="overflow-x:auto">
                                            <table class="la-student-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Siswa</th>
                                                        <th>Performa</th>
                                                        <th>Sebaran Kesulitan</th>
                                                        <th>Penguasaan</th>
                                                        <th>Rekomendasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($topicStudents as $student)
                                                        @php
                                                            $studentId = (int) data_get($student, 'student_id');
                                                            $studentMastery = (float) data_get($student, 'mastery', 0);
                                                            if ($studentMastery >= 85) {
                                                                $studentTier = 'mahir';
                                                                $studentTierLabel = 'Mahir';
                                                            } elseif ($studentMastery >= 70) {
                                                                $studentTier = 'menguasai';
                                                                $studentTierLabel = 'Menguasai';
                                                            } elseif ($studentMastery >= 50) {
                                                                $studentTier = 'cukup';
                                                                $studentTierLabel = 'Cukup';
                                                            } else {
                                                                $studentTier = 'belum';
                                                                $studentTierLabel = 'Belum Menguasai';
                                                            }
                                                            $sdRows = collect($studentTopicDifficulty ?? [])->filter(fn($r) => (int) data_get($r, 'student_id') === $studentId && (int) data_get($r, 'topic_id') === $topicId);
                                                            $sd = [];
                                                            foreach (['mudah', 'sedang', 'sulit'] as $d) {
                                                                $sd[$d] = $sdRows->first(fn($r) => strtolower(trim((string) data_get($r, 'difficulty', ''))) === $d);
                                                            }
                                                            $studentEasyAccuracy = (float) data_get($sd['mudah'], 'accuracy', 0);
                                                            $studentMediumAccuracy = (float) data_get($sd['sedang'], 'accuracy', 0);
                                                            $studentHardAccuracy = (float) data_get($sd['sulit'], 'accuracy', 0);
                                                            $studentEasyAnswers = (int) data_get($sd['mudah'], 'total_answers', 0);
                                                            $studentMediumAnswers = (int) data_get($sd['sedang'], 'total_answers', 0);
                                                            $studentHardAnswers = (int) data_get($sd['sulit'], 'total_answers', 0);
                                                            $studentRecommendation = $recommendations->first(fn($r) => (int) data_get($r, 'student_id') === $studentId && (int) data_get($r, 'topic_id') === $topicId);
                                                            $recommendationCategory = data_get($studentRecommendation, 'category');
                                                            $recommendationText = data_get($studentRecommendation, 'recommendation', 'Belum terdapat rekomendasi pembelajaran.');
                                                            $recommendationLabel = match ($recommendationCategory) { 'penguatan' => 'Penguatan', 'mulai_ditingkatkan' => 'Mulai Ditingkatkan', 'perlu_ditingkatkan' => 'Perlu Ditingkatkan', 'pengayaan' => 'Pengayaan', 'pengayaan_lanjutan' => 'Pengayaan Lanjutan', default => 'Rekomendasi'};
                                                            $recommendationColor = match ($recommendationCategory) { 'penguatan' => 'rust', 'mulai_ditingkatkan', 'perlu_ditingkatkan' => 'amber', 'pengayaan' => 'teal', 'pengayaan_lanjutan' => 'green', default => 'teal'};
                                                            $modalId = 'student-detail-modal-' . $topicId . '-' . $studentId;
                                                        @endphp
                                                        <tr class="la-student-row" data-modal="{{ $modalId }}" tabindex="0" role="button"
                                                            aria-haspopup="dialog">
                                                            <td class="la-stu-name">{{ data_get($student, 'student_name', '-') }}</td>
                                                            <td><span
                                                                    class="la-stu-score">{{ number_format((float) data_get($student, 'accuracy', 0), 0) }}</span>/100
                                                            </td>
                                                            <td>
                                                                <div class="la-student-difficulty-grid">
                                                                    @foreach([['label' => 'Mudah', 'value' => $studentEasyAccuracy, 'answers' => $studentEasyAnswers, 'class' => 'easy'], ['label' => 'Sedang', 'value' => $studentMediumAccuracy, 'answers' => $studentMediumAnswers, 'class' => 'medium'], ['label' => 'Sulit', 'value' => $studentHardAccuracy, 'answers' => $studentHardAnswers, 'class' => 'hard']] as $d)
                                                                        <div class="la-student-diff-item">
                                                                            <div class="la-student-diff-label">{{ $d['label'] }}</div>
                                                                            <div class="la-student-diff-value"
                                                                                style="color:{{ $d['class'] === 'easy' ? 'var(--la-green)' : ($d['class'] === 'medium' ? 'var(--la-amber)' : 'var(--la-rust)') }}">
                                                                                {{ number_format($d['value'], 0) }}% <span
                                                                                    class="la-student-diff-count">{{ $d['answers'] }}
                                                                                    jawaban</span>
                                                                            </div>
                                                                            <div class="la-student-diff-track">
                                                                                <div class="la-student-diff-fill {{ $d['class'] }}"
                                                                                    style="width:{{ min(100, max(0, $d['value'])) }}%"></div>
                                                                            </div>
                                                                    </div>@endforeach
                                                                </div>
                                                            </td>
                                                            <td><span
                                                                    class="la-mastery-pill la-pill-{{ $studentTier }}">{{ number_format($studentMastery, 0) }}%
                                                                    · {{ $studentTierLabel }}</span></td>
                                                            <td><button type="button"
                                                                    class="la-reco-button la-reco-{{ $recommendationColor }}"
                                                                    data-modal="{{ $modalId }}">{{ $recommendationLabel }}</button></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" style="text-align:center;color:var(--la-faint);padding:20px">
                                                                Belum ada data siswa untuk topik ini.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL DITARUH DI LUAR TABLE AGAR HTML VALID DAN EVENT ROW TIDAK RUSAK. --}}
                            @foreach($topicStudents as $student)
                                @php
                                    $studentId = (int) data_get($student, 'student_id');
                                    $studentMastery = (float) data_get($student, 'mastery', 0);
                                    $studentTierLabel = $studentMastery >= 85 ? 'Mahir' : ($studentMastery >= 70 ? 'Menguasai' : ($studentMastery >= 50 ? 'Cukup' : 'Belum Menguasai'));
                                    $studentRecommendation = $recommendations->first(fn($r) => (int) data_get($r, 'student_id') === $studentId && (int) data_get($r, 'topic_id') === $topicId);
                                    $recommendationCategory = data_get($studentRecommendation, 'category');
                                    $recommendationText = data_get($studentRecommendation, 'recommendation', 'Belum terdapat rekomendasi pembelajaran.');
                                    $recommendationLabel = match ($recommendationCategory) { 'penguatan' => 'Penguatan', 'mulai_ditingkatkan' => 'Mulai Ditingkatkan', 'perlu_ditingkatkan' => 'Perlu Ditingkatkan', 'pengayaan' => 'Pengayaan', 'pengayaan_lanjutan' => 'Pengayaan Lanjutan', default => 'Rekomendasi'};
                                    $recommendationColor = match ($recommendationCategory) { 'penguatan' => 'rust', 'mulai_ditingkatkan', 'perlu_ditingkatkan' => 'amber', 'pengayaan' => 'teal', 'pengayaan_lanjutan' => 'green', default => 'teal'};
                                    $modalId = 'student-detail-modal-' . $topicId . '-' . $studentId;
                                    $sdRows = collect($studentTopicDifficulty ?? [])->filter(fn($r) => (int) data_get($r, 'student_id') === $studentId && (int) data_get($r, 'topic_id') === $topicId);
                                    $studentEasy = $sdRows->first(fn($r) => strtolower(trim((string) data_get($r, 'difficulty', ''))) === 'mudah');
                                    $studentMedium = $sdRows->first(fn($r) => strtolower(trim((string) data_get($r, 'difficulty', ''))) === 'sedang');
                                    $studentHard = $sdRows->first(fn($r) => strtolower(trim((string) data_get($r, 'difficulty', ''))) === 'sulit');
                                    $studentEasyAccuracy = (float) data_get($studentEasy, 'accuracy', 0);
                                    $studentMediumAccuracy = (float) data_get($studentMedium, 'accuracy', 0);
                                    $studentHardAccuracy = (float) data_get($studentHard, 'accuracy', 0);
                                    $studentEasyAnswers = (int) data_get($studentEasy, 'total_answers', 0);
                                    $studentMediumAnswers = (int) data_get($studentMedium, 'total_answers', 0);
                                    $studentHardAnswers = (int) data_get($studentHard, 'total_answers', 0);
                                    $studentActivities = collect($studentActivityPerformance ?? [])->filter(fn($a) => (int) data_get($a, 'student_id') === $studentId && (int) data_get($a, 'topic_id') === $topicId)->values();
                                @endphp
                                <div class="la-modal" id="{{ $modalId }}" aria-hidden="true">
                                    <div class="la-modal-card" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title"
                                        style="border-top-color:var(--la-{{ $recommendationColor }})">
                                        <div class="la-modal-header">
                                            <div>
                                                <h3 id="{{ $modalId }}-title">Detail Performa Siswa</h3>
                                                <div class="la-modal-meta"><b>{{ data_get($student, 'student_name', '-') }}</b> ·
                                                    {{ $topic['topic_name'] }} · {{ $subjectName }}
                                                </div>
                                            </div><button type="button" class="la-modal-close" data-close-modal
                                                aria-label="Tutup">×</button>
                                        </div>
                                        <div class="la-modal-body">
                                            <div class="la-modal-section-label">Rekomendasi Pembelajaran</div>
                                            <div class="la-modal-recommendation {{ $recommendationColor }}"><span
                                                    class="la-modal-category {{ $recommendationColor }}">{{ $recommendationLabel }}</span>
                                                <p class="la-modal-text">{{ $recommendationText }}</p>
                                            </div>
                                            <div class="la-modal-section-label">Ringkasan Capaian</div>
                                            <div class="la-modal-stats-grid">
                                                <div class="la-modal-stat">
                                                    <div class="la-modal-stat-label">Performa</div>
                                                    <div class="la-modal-stat-value">
                                                        {{ number_format((float) data_get($student, 'accuracy', 0), 0) }}%
                                                    </div><span class="la-modal-stat-sub">{{ data_get($student, 'correct_answers', 0) }}
                                                        benar dari
                                                        {{ data_get($student, 'total_answers', 0) }} jawaban</span>
                                                </div>
                                                <div class="la-modal-stat">
                                                    <div class="la-modal-stat-label">Penguasaan</div>
                                                    <div class="la-modal-stat-value">{{ number_format($studentMastery, 0) }}%</div><span
                                                        class="la-modal-stat-sub">{{ $studentTierLabel }}</span>
                                                </div>
                                                <div class="la-modal-stat">
                                                    <div class="la-modal-stat-label">Mudah</div>
                                                    <div class="la-modal-stat-value">{{ number_format($studentEasyAccuracy, 0) }}%</div>
                                                    <span class="la-modal-stat-sub">{{ $studentEasyAnswers }} jawaban</span>
                                                </div>
                                                <div class="la-modal-stat">
                                                    <div class="la-modal-stat-label">Sedang</div>
                                                    <div class="la-modal-stat-value">{{ number_format($studentMediumAccuracy, 0) }}%</div>
                                                    <span class="la-modal-stat-sub">{{ $studentMediumAnswers }} jawaban</span>
                                                </div>
                                                <div class="la-modal-stat">
                                                    <div class="la-modal-stat-label">Sulit</div>
                                                    <div class="la-modal-stat-value">{{ number_format($studentHardAccuracy, 0) }}%</div>
                                                    <span class="la-modal-stat-sub">{{ $studentHardAnswers }} jawaban</span>
                                                </div>
                                            </div>
                                            <div class="la-modal-section-label">Performa Per Aktivitas</div>
                                            @if($studentActivities->isNotEmpty())
                                                <div class="la-modal-activity-list">
                                                    @foreach($studentActivities as $activity)@php $activityAccuracy = (float) data_get($activity, 'accuracy', 0);
                                                            $activityCorrect = (int) data_get($activity, 'correct_answers', 0);
                                                            $activityIncorrect = (int) data_get($activity, 'incorrect_answers', 0);
                                                            $activityTotal = (int) data_get($activity, 'total_answers', 0);
                                                            $activityStatus = strtolower((string) data_get($activity, 'activity_status', ''));
                                                            $activityType = match ($activityStatus) { 'basic' => 'Aktivitas Dasar', 'additional' => 'Aktivitas Tambahan', 'remedial' => 'Remedial', default => 'Aktivitas'};
                                                        $activityColor = $activityAccuracy >= 85 ? 'var(--la-blue)' : ($activityAccuracy >= 70 ? 'var(--la-green)' : ($activityAccuracy >= 50 ? 'var(--la-amber)' : 'var(--la-rust)'));@endphp
                                                        <div class="la-modal-activity-item">
                                                            <div class="la-modal-activity-head">
                                                                <div class="la-modal-activity-info">
                                                                    <div class="la-modal-activity-name">
                                                                        {{ data_get($activity, 'activity_name', 'Aktivitas') }}
                                                                    </div>
                                                                    <div class="la-modal-activity-type">{{ $activityType }}</div>
                                                                </div>
                                                                <div class="la-modal-activity-score" style="color:{{ $activityColor }}">
                                                                    {{ number_format($activityAccuracy, 0) }}%
                                                                </div>
                                                            </div>
                                                            <div class="la-modal-activity-track">
                                                                <div class="la-modal-activity-fill"
                                                                    style="width:{{ min(100, max(0, $activityAccuracy)) }}%;background:{{ $activityColor }}">
                                                                </div>
                                                            </div>
                                                            <div class="la-modal-activity-meta"><span
                                                                    class="la-modal-activity-correct"><strong>{{ $activityCorrect }}</strong>
                                                                    benar</span><span>/</span><span
                                                                    class="la-modal-activity-incorrect"><strong>{{ $activityIncorrect }}</strong>
                                                                    salah</span><span>·</span><span>{{ $activityTotal }} jawaban</span></div>
                                                    </div>@endforeach
                                            </div>@else<div class="la-modal-empty">Belum terdapat data performa aktivitas untuk siswa pada
                                            topik ini.</div>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="la-empty-state">Belum terdapat data Learning Analytics berdasarkan filter yang dipilih.</div>
            @endif
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openModal = id => {
                const m = document.getElementById(id);
                if (!m) return;
                m.classList.add('open');
                m.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = m => {
                if (!m) return;
                m.classList.remove('open');
                m.setAttribute('aria-hidden', 'true');
                if (!document.querySelector('.la-modal.open')) {
                    document.body.style.overflow = '';
                }
            };

            document.querySelectorAll('.la-student-section-title[data-toggle-students]').forEach(title => {
                title.addEventListener('click', () => {
                    const table = document.getElementById(title.dataset.toggleStudents);
                    if (!table) return;

                    const isOpen = table.classList.toggle('open');
                    title.classList.toggle('open', isOpen);
                });
            });

            document.querySelectorAll('.la-student-row').forEach(row => {
                row.addEventListener('click', () => openModal(row.dataset.modal));

                row.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openModal(row.dataset.modal);
                    }
                });
            });

            document.querySelectorAll('.la-reco-button').forEach(button => {
                button.addEventListener('click', e => {
                    e.stopPropagation();
                    openModal(button.dataset.modal);
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach(button => {
                button.addEventListener('click', () => {
                    closeModal(button.closest('.la-modal'));
                });
            });

            document.querySelectorAll('.la-modal').forEach(modal => {
                modal.addEventListener('click', e => {
                    if (e.target === modal) {
                        closeModal(modal);
                    }
                });
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.la-modal.open').forEach(closeModal);
                }
            });
        });
    </script>
@endsection