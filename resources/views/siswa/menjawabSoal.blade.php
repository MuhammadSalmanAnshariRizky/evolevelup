<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }} - Kuis Adaptif</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* Card Informasi Awal */
        .info-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        /* Panel Soal Utama */
        #soal-test {
            background: #ffffff;
            border-radius: 1rem;
            padding: 25px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.4s ease;
        }

        /* Meta soal */
        .soal-meta {
            padding: 15px 20px;
            border-radius: 0.75rem;
            background: #f8f9fc;
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
        }

        .soal-meta strong {
            color: var(--primary-color);
        }

        /* Timer */
        #timer {
            font-size: 1.3rem;
            font-weight: 700;
            background: #e74a3b;
            color: white;
            padding: 6px 16px;
            border-radius: 0.5rem;
            text-align: center;
            box-shadow: 0 2px 6px rgba(231, 74, 59, 0.3);
            letter-spacing: 1px;
        }

        /* Teks Soal & Kotak Soal */
        .question-box {
            font-size: 1.125rem;
            line-height: 1.7;
            color: #2e384d;
        }

        /* Opsi jawaban */
        .option-item {
            padding: 14px 18px;
            border-radius: 0.75rem;
            border: 2px solid #e3e6f0;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background: #fdfdfd;
            font-size: 1rem;
        }

        .option-item:hover {
            border-color: var(--primary-color);
            background: #f8f9fc;
        }

        .form-check-input:checked~.form-check-label {
            font-weight: 600;
        }

        /* Tombol Next */
        .btn-next {
            padding: 10px 24px;
            font-size: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--success-color), #13855c);
            border: none;
            box-shadow: 0 4px 10px rgba(28, 200, 138, 0.3);
            transition: all 0.2s;
        }

        .btn-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(28, 200, 138, 0.4);
        }

        /* Radio Button Custom */
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            cursor: pointer;
            border: 2px solid #b7b9cc !important;
            accent-color: var(--success-color);
        }

        .form-check-label {
            margin-left: 8px;
            cursor: pointer;
            width: 100%;
        }

        /* Animasi */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dashboard Metrik Adaptif */
        .adaptive-dashboard {
            border-radius: 0.75rem;
            border: 2px solid var(--primary-color);
            overflow: hidden;
            margin-bottom: 1.5rem;
            background: #fff;
        }

        .adaptive-dashboard-header {
            background-color: #f1f3f9;
            padding: 12px 20px;
            border-bottom: 1px solid #e3e6f0;
        }

        .progress-adaptive {
            height: 26px;
            border-radius: 0;
            background-color: #eaecf4;
            position: relative;
        }

        .kkm-marker {
            position: absolute;
            left: 72.16%;
            /* (1.33 + 3.0) / 6.0 * 100 */
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: var(--danger-color);
            z-index: 10;
        }

        .kkm-label {
            position: absolute;
            left: 72.16%;
            top: 28px;
            transform: translateX(-50%);
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--danger-color);
        }

        /* Floating Widgets */
        #comboMeter {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--warning-color);
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.9);
            padding: 6px 14px;
            border-radius: 50rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @keyframes firePulse {
            0% {
                transform: scale(1);
                text-shadow: 0 0 10px orange;
            }

            50% {
                transform: scale(1.15);
                text-shadow: 0 0 20px red;
            }

            100% {
                transform: scale(1);
                text-shadow: 0 0 10px orange;
            }
        }

        #onFire.active {
            animation: firePulse 1s infinite;
        }

        #onFire {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 1.8rem;
            font-weight: 800;
            color: #e74a3b;
            display: none;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 16px;
            border-radius: 50rem;
            box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
        }
    </style>
</head>

<body class="py-5">

    <div class="container" style="max-width: 800px;">

        <h3 class="text-center fw-bold mb-4 text-dark">
            <i class="bi bi-journal-code text-primary me-2"></i>{{ $judul }}
            <span class="text-muted fs-5 fw-normal">({{ ucfirst($topik) }})</span>
        </h3>

        <div id="info-test" class="text-center">
            <div class="card info-card mx-auto shadow-sm p-4">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-cpu fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Keterangan Aktivitas Ujian</h4>
                    </div>

                    <div class="row justify-content-center mb-4 text-start bg-light p-3 rounded-3 mx-1">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <span class="text-muted small d-block">Maksimal Soal</span>
                            <span id="infoJumlahSoal" class="text-success fw-bold fs-5">
                                {{ isset($jumlah_soal) ? $jumlah_soal . ' Soal' : '—' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Durasi Pengerjaan</span>
                            <span id="infoDurasi" class="text-primary fw-bold fs-5">
                                {{ isset($durasi) ? $durasi . ' Menit' : '—' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-muted small mb-4 px-2">
                        <i class="bi bi-info-circle-fill text-info me-1"></i> <b>Mode Ujian Adaptif:</b> Sistem secara
                        dinamis menyesuaikan tingkat kesulitan soal berdasarkan kemampuan Anda. Ujian dapat diselesaikan
                        lebih awal jika tingkat presisi psikometrik telah tercapai.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm" onclick="mulai()">
                            <i class="bi bi-play-fill me-1"></i> Mulai Ujian
                        </button>
                        <a href="{{ route('siswa.aktivitas') }}"
                            class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="soal-test" hidden>

            <div class="soal-meta d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <div class="mb-1"><strong>Kelas:</strong> {{ $kelas }}</div>
                    <div class="mb-1"><strong>Mata Pelajaran:</strong> {{ $mapel }}</div>
                    <div><strong>Topik:</strong> {{ $topik }}</div>
                </div>

                <div id="timer" class="shadow-sm">
                    <i class="bi bi-clock-history me-1"></i>{{ str_pad($durasi, 2, '0', STR_PAD_LEFT) }}:00
                </div>
            </div>

            <div class="adaptive-dashboard shadow-sm">
                <div
                    class="adaptive-dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="fw-bold text-primary mb-1 d-inline-block">
                            🧠 Kemampuan (<span style="font-style: italic;">&theta;</span>):
                            <span id="currentThetaDisplay" class="badge bg-primary fs-6 ms-1">0.00</span>
                        </span>
                        <span class="fw-bold text-secondary mb-1 d-inline-block ms-md-3">
                            🎯 Error (SE):
                            <span id="currentSeDisplay" class="badge bg-secondary fs-6 ms-1">1.00</span>
                            <span class="text-muted fw-normal" style="font-size: 0.8rem;">/ Target: <span
                                    id="targetSeDisplay">0.30</span></span>
                        </span>
                    </div>
                    <div>
                        <span class="text-muted fw-semibold">Soal ke-<span id="questionCounter"
                                class="text-dark fw-bold">1</span></span>
                    </div>
                </div>
                <div style="position: relative; margin-bottom: 20px;">
                    <div class="progress progress-adaptive">
                        <div class="kkm-marker" title="Target KKM: Theta +1.33"></div>
                        <div id="adaptiveProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                            style="width: 50%; font-weight:bold; font-size: 0.85rem;">
                            START
                        </div>
                    </div>
                    <div class="kkm-label">TARGET KKM (+1.33)</div>
                </div>
            </div>

            <div class="question-panel">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Pertanyaan No. <span id="soalNumHeader">1</span></h5>
                    <div class="d-flex align-items-center gap-2">
                        <span id="badgeDifficulty" class="badge bg-secondary px-3 py-2 shadow-sm">Tingkat: —</span>
                        <span id="badgeDelta" class="badge bg-dark px-3 py-2 shadow-sm font-monospace"
                            title="Parameter Kesulitan Soal (Delta)">δ: —</span>
                    </div>
                </div>

                <div id="questionText" class="question-box mb-4"></div>

                <div id="optionsContainer" class="mb-4"></div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button id="nextBtn" class="btn btn-success btn-next" onclick="checkAnswer()">
                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div id="comboMeter"></div>
    <div id="onFire"><i class="bi bi-fire text-danger me-1"></i>ON FIRE!</div>

    <script>
        let currentIndex = 0;
        let totalQuestions = 0;
        let answers = [];
        let currentQuestionID = null;
        let timeLeft = 30 * 60;
        let timerInterval;
        let totalBenar = 0;
        let totalSalah = 0;
        let currentStreak = 0;
        let currentTheta = 0.00;
        let currentSE = 1.00;
        let targetSE = 0.30;

        function startTimer() {
            timerInterval = setInterval(() => {
                timeLeft--;

                let m = Math.floor(timeLeft / 60);
                let s = timeLeft % 60;

                document.getElementById("timer").innerHTML =
                    `<i class="bi bi-clock-history me-1"></i>${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire("Waktu Habis!", "Ujian akan otomatis diselesaikan.", "info");
                    showResult();
                }
            }, 1000);
        }

        function mulai() {
            fetch(`/activity/{{ $id_activity }}/start`)
                .then(async r => {
                    const data = await r.json();

                    if (!r.ok) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Bisa Dimulai',
                            text: data.message ?? 'Aktivitas belum siap',
                            confirmButtonText: 'Mengerti',
                        });
                        throw new Error(data.message);
                    }
                    return data;
                })
                .then(data => {
                    totalQuestions = data.totalQuestions;
                    answers = Array(totalQuestions).fill(null);

                    currentTheta = data.theta_initial !== undefined ? parseFloat(data.theta_initial) : 0.00;

                    // --- BARIS BARU UNTUK SE ---
                    targetSE = data.target_se !== undefined ? parseFloat(data.target_se) : 0.30;
                    document.getElementById("targetSeDisplay").innerText = targetSE.toFixed(2);
                    document.getElementById("currentSeDisplay").innerText = "1.00";
                    document.getElementById("currentSeDisplay").className = "badge bg-secondary fs-6 ms-1";
                    // ---------------------------

                    document.getElementById("info-test").hidden = true;
                    document.getElementById("soal-test").hidden = false;

                    const durasiMenit = Number.isInteger(data.durasi_pengerjaan)
                        ? data.durasi_pengerjaan
                        : 30;

                    timeLeft = durasiMenit * 60;

                    loadQuestion();
                    updateAdaptiveProgress(currentTheta);
                    startTimer();
                })
                .catch(err => console.warn('Start dibatalkan:', err.message));
        }

        function loadQuestion() {
            document.getElementById("questionCounter").innerText = (currentIndex + 1);
            document.getElementById("soalNumHeader").innerText = (currentIndex + 1);

            fetch(`/activity/{{ $id_activity }}/question?index=${currentIndex}`)
                .then(r => r.json())
                .then(q => {
                    if (q.end) {
                        showResult();
                        return;
                    }

                    currentQuestionID = q.question_id;

                    // 1. Tampilkan Teks Soal
                    document.getElementById('questionText').innerHTML = q.question.text;

                    // 2. Olah & Tampilkan Badge Difficulty & Delta Soal
                    let diffText = q.difficulty ? q.difficulty.toLowerCase() : 'sedang';
                    let badgeColor = 'bg-secondary';
                    let badgeLabel = diffText.toUpperCase();

                    if (diffText === 'mudah') {
                        badgeColor = 'bg-success';
                    } else if (diffText === 'sedang') {
                        badgeColor = 'bg-warning text-dark';
                    } else if (diffText === 'sulit') {
                        badgeColor = 'bg-danger';
                    }

                    document.getElementById('badgeDifficulty').className = `badge ${badgeColor} px-3 py-2 shadow-sm`;
                    document.getElementById('badgeDifficulty').innerText = `Tingkat: ${badgeLabel}`;

                    // Menampilkan Nilai Delta Soal (jika ada dari backend)
                    let deltaVal = q.delta !== undefined && q.delta !== null ? parseFloat(q.delta).toFixed(2) : '0.00';
                    document.getElementById('badgeDelta').innerText = `δ: ${deltaVal > 0 ? '+' + deltaVal : deltaVal}`;

                    // 3. Render Pilihan Jawaban
                    let html = "";
                    if (q.type === "MultipleChoice") {
                        q.options.forEach(o => {
                            let key = Object.keys(o)[0];
                            let val = o[key].teks;

                            html += `
                            <div class="form-check option-item d-flex align-items-center">
                                <input type="radio" name="answer" value="${key}" id="opt_${key}" class="form-check-input"
                                    ${answers[currentIndex] === key ? "checked" : ""}>
                                <label class="form-check-label" for="opt_${key}">
                                    <strong class="me-1">${key.toUpperCase()}.</strong> ${val}
                                </label>
                            </div>
                            `;
                        });
                    } else if (q.type === "ShortAnswer") {
                        html = `
                            <input type="text" name="answer" class="form-control form-control-lg rounded-3"
                                placeholder="Ketik jawaban Anda di sini..."
                                value="${answers[currentIndex] ?? ''}">
                        `;
                    }

                    document.getElementById("optionsContainer").innerHTML = html;

                    const btn = document.getElementById("nextBtn");
                    if (currentIndex >= totalQuestions - 1) {
                        btn.innerHTML = `Selesai Ujian <i class="bi bi-check-circle-fill ms-1"></i>`;
                        btn.classList.replace("btn-success", "btn-primary");
                    } else {
                        btn.innerHTML = `Selanjutnya <i class="bi bi-arrow-right ms-1"></i>`;
                        btn.classList.replace("btn-primary", "btn-success");
                    }
                });
        }

        function checkAnswer() {
            let selectedRadio = document.querySelector('input[name="answer"]:checked');
            let textAnswer = document.querySelector('input[name="answer"]:not([type=radio])');
            let finalAnswer = null;

            if (selectedRadio) {
                finalAnswer = selectedRadio.value;
            } else if (textAnswer) {
                finalAnswer = textAnswer.value.trim();
                if (finalAnswer === "") {
                    return Swal.fire("Oops", "Isi jawaban terlebih dahulu!", "warning");
                }
            } else {
                return Swal.fire("Oops", "Pilih atau isi jawaban terlebih dahulu!", "warning");
            }

            document.getElementById("nextBtn").disabled = true;
            answers[currentIndex] = finalAnswer;

            fetch(`/activity/{{ $id_activity }}/submit`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    question_id: currentQuestionID,
                    user_answer: finalAnswer
                })
            })
                .then(r => r.json())
                .then(res => {
                    if (res.correct === true) {
                        totalBenar++;
                        currentStreak++;
                    } else {
                        totalSalah++;
                        currentStreak = 0;
                    }

                    if (res.current_theta !== undefined) {
                        currentTheta = parseFloat(res.current_theta);
                        updateAdaptiveProgress(currentTheta);
                    }

                    // --- BARIS BARU UNTUK UPDATE UI SE ---
                    if (res.current_se !== undefined) {
                        currentSE = parseFloat(res.current_se);
                        let seBadge = document.getElementById("currentSeDisplay");
                        seBadge.innerText = currentSE.toFixed(2);

                        // Beri warna hijau jika SE sudah mencapai target/stabil
                        if (currentSE <= targetSE) {
                            seBadge.className = "badge bg-success fs-6 ms-1";
                        } else {
                            seBadge.className = "badge bg-secondary fs-6 ms-1";
                        }
                    }
                    // -------------------------------------

                    showAnswerFeedback(res);
                    updateComboUI(currentStreak);

                    setTimeout(() => {
                        document.getElementById("nextBtn").disabled = false;

                        if (res.should_stop || currentIndex >= totalQuestions - 1) {
                            showResult();
                        } else {
                            currentIndex++;
                            loadQuestion();
                        }
                    }, 1300);
                })
                .catch(err => {
                    document.getElementById("nextBtn").disabled = false;
                    Swal.fire("Error", "Gagal menyimpan jawaban. Cek koneksi Anda.", "error");
                });
        }

        function updateAdaptiveProgress(theta) {
            let percent = ((theta + 3.0) / 6.0) * 100;
            percent = Math.max(0, Math.min(100, percent));

            const bar = document.getElementById("adaptiveProgressBar");
            const thetaDisplay = document.getElementById("currentThetaDisplay");

            bar.style.width = percent + "%";
            thetaDisplay.innerText = theta > 0 ? "+" + theta.toFixed(2) : theta.toFixed(2);

            if (theta >= 1.33) {
                bar.className = "progress-bar progress-bar-striped progress-bar-animated bg-success";
                bar.innerText = "Target KKM Tercapai!";
                thetaDisplay.className = "badge bg-success fs-6 ms-1";
            } else if (theta >= 0.0) {
                bar.className = "progress-bar progress-bar-striped progress-bar-animated bg-info";
                bar.innerText = "Kemampuan Meningkat";
                thetaDisplay.className = "badge bg-info fs-6 ms-1 text-dark";
            } else {
                bar.className = "progress-bar progress-bar-striped progress-bar-animated bg-warning text-dark";
                bar.innerText = "Butuh Perbaikan";
                thetaDisplay.className = "badge bg-warning fs-6 ms-1 text-dark";
            }
        }

        function showAnswerFeedback(res) {
            const isCorrect = res.correct === true;
            Swal.fire({
                icon: isCorrect ? 'success' : 'error',
                title: isCorrect ? 'Jawaban Benar 🎉' : 'Jawaban Salah ❌',
                html: `
                    <div style="text-align:center">
                        ${isCorrect
                        ? `<p class="mb-0 text-success fw-bold">Estimasi Kemampuan Kamu Naik!</p>`
                        : `<p class="mb-0 text-danger fw-bold">Sistem menurunkan tingkat kesulitan soal berikutnya.</p>`
                    }
                    </div>
                `,
                timer: 1200,
                showConfirmButton: false,
                timerProgressBar: true,
                allowOutsideClick: false,
                backdrop: `rgba(0,0,0,0.4)`
            });
        }

        function updateComboUI(streak) {
            const combo = document.getElementById("comboMeter");
            const fire = document.getElementById("onFire");

            if (streak >= 2) {
                combo.style.display = "block";
                combo.innerText = `COMBO x${streak}`;
            } else {
                combo.style.display = "none";
            }

            if (streak >= 3) {
                fire.style.display = "block";
                fire.classList.add("active");
            } else {
                fire.style.display = "none";
                fire.classList.remove("active");
            }
        }

        function showResult() {
            clearInterval(timerInterval);

            Swal.fire({
                title: 'Mengkalkulasi Nilai...',
                html: 'Sistem sedang menganalisis kestabilan psikometrik Anda.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            fetch(`/activity/{{ $id_activity }}/finish`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
                .then(r => r.json())
                .then(res => {
                    const db = res.result_db ?? null;

                    const sec = res.duration_seconds ?? (db ? db.waktu_mengerjakan : 0);
                    const m = Math.floor(sec / 60);
                    const s = sec % 60;

                    const jumlahSoalDik = res.jumlah_soal ?? '-';
                    const totalBnr = res.total_correct ?? 0;

                    const thetaAkhir = db ? (db.theta_akhir ?? null) : null;
                    const nilaiAkhir = db ? (db.nilai_akhir ?? null) : null;
                    const statusText = db ? (db.result_status ?? '-') : '-';

                    const fmt = v => (v === null || v === undefined) ? '-' : (typeof v === 'number' && v % 1 !== 0 ? v.toFixed(2) : v);
                    const isLulus = statusText === 'Pass';

                    const html = `
                    <div style="text-align:left; font-size: 1.02rem;">
                        <p class="mb-2"><strong>Waktu Pengerjaan:</strong> ${m} menit ${s} detik</p>
                        <p class="mb-2"><strong>Soal Diselesaikan:</strong> ${jumlahSoalDik} dari Maksimal ${totalQuestions}</p>
                        <p class="mb-3"><strong>Benar:</strong> <span class="text-success fw-bold">${totalBnr}</span> | <strong>Salah:</strong> <span class="text-danger fw-bold">${jumlahSoalDik !== '-' ? (jumlahSoalDik - totalBnr) : '-'}</span></p>
                        <hr class="border-2 border-secondary">
                        <div class="text-center bg-light p-3 rounded-3 shadow-sm border">
                            <p class="mb-1 text-muted small">Estimasi Kemampuan Akhir (&theta; Logit)</p>
                            <h4 class="text-primary mb-3">${thetaAkhir > 0 ? '+' + fmt(thetaAkhir) : fmt(thetaAkhir)}</h4>
                            <p class="mb-1 text-muted small">Nilai Skala (0-100)</p>
                            <h2 class="mb-2 fw-bolder ${isLulus ? 'text-success' : 'text-danger'}">${fmt(nilaiAkhir)}</h2>
                            <span class="badge ${isLulus ? 'bg-success' : 'bg-danger'} fs-6 px-4 py-2 mt-1">${isLulus ? 'LULUS (PASS)' : 'REMEDIAL'}</span>
                        </div>
                    </div>
                `;

                    Swal.fire({
                        title: "Ujian Adaptif Selesai!",
                        html: html,
                        icon: "success",
                        confirmButtonText: "Kembali ke Beranda",
                        confirmButtonColor: '#4e73df',
                        allowOutsideClick: false,
                        width: '600px'
                    }).then(result => {
                        if (result.isConfirmed) {
                            location.href = "{{ route('siswa.aktivitas') }}";
                        }
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "Gagal menyimpan sesi ujian. Coba muat ulang halaman.", "error")
                        .then(() => location.href = "{{ route('siswa.aktivitas') }}");
                });
        }
    </script>

</body>

</html>