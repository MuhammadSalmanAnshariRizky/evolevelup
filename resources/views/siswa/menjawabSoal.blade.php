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

        .info-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        #soal-test {
            background: #ffffff;
            border-radius: 1rem;
            padding: 25px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.4s ease;
        }

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

        .question-box {
            font-size: 1.125rem;
            line-height: 1.7;
            color: #2e384d;
        }

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

    <div class="container" style="max-width: 900px;">

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
                        dinamis menyesuaikan alur soal berdasarkan kemampuan Anda. Ujian dapat diselesaikan
                        lebih awal jika kompetensi telah terpenuhi.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm" onclick="mulai()">
                            <i class="bi bi-play-fill me-1"></i> Mulai / Lanjutkan Ujian
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

            <div class="soal-meta shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="mb-1"><strong>Kelas:</strong> {{ $kelas }}</div>
                        <div class="mb-1"><strong>Mata Pelajaran:</strong> {{ $mapel }}</div>
                        <div><strong>Topik:</strong> {{ $topik }}</div>
                    </div>

                    <div id="timer" class="shadow-sm">
                        <i class="bi bi-clock-history me-1"></i>{{ str_pad($durasi, 2, '0', STR_PAD_LEFT) }}:00
                    </div>
                </div>

                <div class="bg-white p-3 rounded border shadow-sm mt-3" style="font-size: 0.85rem;">
                    <div class="fw-bold text-primary border-bottom pb-1 mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calculator me-1"></i> Perhitungan Real-Time IRT Rasch Model (1PL)</span>
                        <span class="badge bg-primary-subtle text-primary border">Target SE &le; <span id="targetSEDisplay">0.50</span></span>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 border-end pe-2">
                            <div><span class="text-muted">Ability (&Theta;):</span> <strong id="liveTheta" class="text-primary">0.0000</strong> Logit</div>
                            <div>
                                <span class="text-muted">Difficulty (&delta;):</span>
                                <strong id="liveDelta" class="text-dark">0.0000</strong> Logit
                                <span id="liveDifficulty" class="badge bg-secondary ms-1">-</span>
                            </div>
                            <div><span class="text-muted">Peluang Benar (P):</span> <strong id="liveP" class="text-success">0.5000</strong></div>
                        </div>

                        <div class="col-md-4 border-end px-2">
                            <div><span class="text-muted">Info Soal I = P(1-P):</span> <strong id="liveItemInfo" class="text-info">0.2500</strong></div>
                            <div><span class="text-muted">Total Info (&sum;I):</span> <strong id="liveSumInfo" class="text-secondary">0.0000</strong></div>
                            <div><span class="text-muted">Standard Error (SE):</span> <strong id="liveSE" class="text-danger">1.0000</strong></div>
                        </div>

                        <div class="col-md-4 ps-2">
                            <div><span class="text-muted">Residual (&sum;(u - P)):</span> <strong id="liveNumerator" class="text-dark">0.0000</strong></div>
                            <div><span class="text-muted">Penyesuaian (&Delta;&Theta;):</span> <strong id="liveDeltaTheta" class="text-warning-emphasis">0.0000</strong></div>
                            <div><span class="text-muted">Formula Update:</span> <code>&Theta;<sub>baru</sub> = &Theta; + &Delta;&Theta;</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="question-panel">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Pertanyaan No. <span id="soalNumHeader">1</span></h5>
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

        document.addEventListener("DOMContentLoaded", function () {
            checkExistingSession();
        });

        function checkExistingSession() {
            fetch(`/activity/{{ $id_activity }}/start`)
                .then(r => r.json())
                .then(data => {
                    if (!data) return;

                    totalQuestions = data.totalQuestions;
                    totalBenar = data.total_correct ?? 0;
                    currentIndex = data.current_index ?? 0;
                    answers = Array(totalQuestions).fill(null);

                    if (data.theta_initial !== undefined) {
                        document.getElementById("liveTheta").innerText = Number(data.theta_initial).toFixed(4);
                    }
                    if (data.se_initial !== undefined) {
                        document.getElementById("liveSE").innerText = Number(data.se_initial).toFixed(4);
                    }

                    const durasiMenit = Number.isInteger(data.durasi_pengerjaan) ? data.durasi_pengerjaan : 30;
                    timeLeft = durasiMenit * 60;

                    if (currentIndex > 0) {
                        document.getElementById("info-test").hidden = true;
                        document.getElementById("soal-test").hidden = false;
                        loadQuestion();
                        startTimer();
                    }
                })
                .catch(err => console.warn('Gagal sinkronisasi sesi:', err));
        }

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);

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
                    currentIndex = data.current_index ?? 0;
                    totalBenar = data.total_correct ?? 0;
                    answers = Array(totalQuestions).fill(null);

                    document.getElementById("info-test").hidden = true;
                    document.getElementById("soal-test").hidden = false;

                    const durasiMenit = Number.isInteger(data.durasi_pengerjaan)
                        ? data.durasi_pengerjaan
                        : 30;

                    timeLeft = durasiMenit * 60;

                    loadQuestion();
                    startTimer();
                })
                .catch(err => console.warn('Start dibatalkan:', err.message));
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function getDifficultyBadgeClass(difficulty) {
            switch (difficulty) {
                case 'Sangat Mudah': return 'bg-success';
                case 'Mudah': return 'bg-info text-dark';
                case 'Sedang': return 'bg-warning text-dark';
                case 'Sulit': return 'bg-danger';
                case 'Sangat Sulit': return 'bg-dark text-white';
                default: return 'bg-secondary';
            }
        }

        function loadQuestion() {
            fetch(`/activity/{{ $id_activity }}/question`)
                .then(r => r.json())
                .then(q => {
                    if (q.end) {
                        showResult();
                        return;
                    }

                    if (q.current_index !== undefined) {
                        currentIndex = q.current_index;
                    }

                    document.getElementById("soalNumHeader").innerText = (currentIndex + 1);
                    currentQuestionID = q.question_id;

                    if (q.theta !== undefined) document.getElementById("liveTheta").innerText = Number(q.theta).toFixed(4);
                    if (q.delta !== undefined) document.getElementById("liveDelta").innerText = Number(q.delta).toFixed(4);
                    if (q.difficulty !== undefined) {
                        const badgeEl = document.getElementById("liveDifficulty");
                        badgeEl.innerText = q.difficulty;
                        badgeEl.className = `badge ms-1 ${getDifficultyBadgeClass(q.difficulty)}`;
                    }
                    if (q.p_value !== undefined) document.getElementById("liveP").innerText = Number(q.p_value).toFixed(4);
                    if (q.item_info !== undefined) document.getElementById("liveItemInfo").innerText = Number(q.item_info).toFixed(4);
                    if (q.sum_info !== undefined) document.getElementById("liveSumInfo").innerText = Number(q.sum_info).toFixed(4);
                    if (q.current_se !== undefined) document.getElementById("liveSE").innerText = Number(q.current_se).toFixed(4);

                    document.getElementById('questionText').textContent = q.question.text;

                    let html = "";
                    if (q.type === "MultipleChoice") {
                        q.options.forEach(o => {
                            let key = Object.keys(o)[0];
                            let val = escapeHtml(o[key].teks);

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
                        const userAns = escapeHtml(answers[currentIndex] ?? '');
                        html = `
                            <input type="text" name="answer" class="form-control form-control-lg rounded-3 mb-3"
                                placeholder="Ketik jawaban Anda di sini..."
                                value="${userAns}">
                        `;

                        if (q.hint && q.hint.trim() !== "") {
                            html += `
                            <div class="alert alert-warning border-0 bg-warning-subtle text-dark p-3 rounded-3 d-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-lightbulb-fill text-warning fs-4 me-2"></i>
                                <div>
                                    <strong>Petunjuk:</strong> ${escapeHtml(q.hint)}
                                </div>
                            </div>
                            `;
                        }
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
                    if (res.current_theta !== undefined) document.getElementById("liveTheta").innerText = Number(res.current_theta).toFixed(4);
                    if (res.current_p !== undefined) document.getElementById("liveP").innerText = Number(res.current_p).toFixed(4);
                    if (res.current_se !== undefined) document.getElementById("liveSE").innerText = Number(res.current_se).toFixed(4);
                    if (res.sum_numerator !== undefined) document.getElementById("liveNumerator").innerText = Number(res.sum_numerator).toFixed(4);
                    if (res.sum_info !== undefined) document.getElementById("liveSumInfo").innerText = Number(res.sum_info).toFixed(4);
                    if (res.delta_theta !== undefined) document.getElementById("liveDeltaTheta").innerText = Number(res.delta_theta).toFixed(4);

                    if (res.correct === true) {
                        totalBenar++;
                        currentStreak++;
                    } else {
                        totalSalah++;
                        currentStreak = 0;
                    }

                    showAnswerFeedback(res);
                    updateComboUI(currentStreak);

                    setTimeout(() => {
                        document.getElementById("nextBtn").disabled = false;

                        if (res.should_stop || currentIndex >= totalQuestions - 1) {
                            showResult();
                        } else {
                            loadQuestion();
                        }
                    }, 1300);
                })
                .catch(err => {
                    document.getElementById("nextBtn").disabled = false;
                    Swal.fire("Error", "Gagal menyimpan jawaban. Cek koneksi Anda.", "error");
                });
        }

        function showAnswerFeedback(res) {
            const isCorrect = res.correct === true;
            Swal.fire({
                icon: isCorrect ? 'success' : 'error',
                title: isCorrect ? 'Jawaban Benar 🎉' : 'Jawaban Salah ❌',
                html: `
                    <div style="text-align:center">
                        ${isCorrect
                        ? `<p class="mb-0 text-success fw-bold">Bagus! Jawaban kamu tepat.</p>`
                        : `<p class="mb-0 text-danger fw-bold">Tetap semangat! Coba lebih baik di soal berikutnya.</p>`
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
                title: 'Menyimpan Hasil...',
                html: 'Sistem sedang memproses nilai Anda.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
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
                    const debug = res.debug_info ?? {};

                    const sec = res.duration_seconds ?? (db ? db.waktu_mengerjakan : 0);
                    const m = Math.floor(sec / 60);
                    const s = sec % 60;

                    const totalDik = debug.total_dikerjakan ?? res.jumlah_soal ?? 0;
                    const totalBnr = debug.benar ?? res.total_correct ?? 0;
                    const totalSlh = debug.salah ?? res.total_incorrect ?? 0;
                    const nilaiAkhir = db ? (db.nilai_akhir ?? 0) : 0;
                    const statusText = db ? (db.result_status ?? '-') : '-';
                    const isLulus = statusText === 'Pass';

                    const html = `
            <div style="text-align:left; font-size: 0.95rem;">
                <p class="mb-1"><strong>Waktu Pengerjaan:</strong> ${m} menit ${s} detik</p>
                <p class="mb-1"><strong>Soal Diselesaikan:</strong> ${totalDik} dari Maksimal ${totalQuestions}</p>
                <p class="mb-3">
                    <strong>Benar:</strong> <span class="text-success fw-bold">${totalBnr}</span> | 
                    <strong>Salah:</strong> <span class="text-danger fw-bold">${totalSlh}</span>
                </p>
                
                <div class="text-center bg-light p-3 rounded-3 shadow-sm border mb-3">
                    <p class="mb-1 text-muted small">Nilai Akhir</p>
                    <h2 class="mb-1 fw-bolder ${isLulus ? 'text-success' : 'text-danger'}">${nilaiAkhir}</h2>
                    <span class="badge ${isLulus ? 'bg-success' : 'bg-danger'} fs-6 px-3 py-1">${isLulus ? 'LULUS' : 'REMEDIAL'}</span>
                </div>

                <div class="card border-warning bg-warning-subtle p-2 rounded-3 text-dark" style="font-size: 0.85rem;">
                    <div class="fw-bold text-warning-emphasis mb-1">
                        <i class="bi bi-bug-fill me-1"></i> Panel Debugging Sistem:
                    </div>
                    <ul class="mb-0 ps-3">
                        <li><b>Perhitungan Nilai:</b> <code>${debug.rumus ?? '-'}</code> = <b>${debug.nilai_hitung ?? 0}</b></li>
                        <li><b>Sifat Ujian:</b> ${debug.mode_adaptif ? 'Adaptif (IRT 1PL)' : 'Non-Adaptif'}</li>
                        <li><b>Skor Kemampuan (&Theta; / Theta):</b> <code>${debug.theta_akhir ?? 0}</code> (Logit)</li>
                        <li><b>Standard Error (SE):</b> <code>${debug.se_akhir ?? 0}</code> (Target SE &le; ${debug.target_se})</li>
                        <li><b>Rata-rata Nilai P (Peluang):</b> <code>${debug.p_value_akhir ?? debug.p_value ?? '-'}</code></li>
                    </ul>
                </div>
            </div>
        `;

                    Swal.fire({
                        title: "Ujian Selesai!",
                        html: html,
                        icon: "success",
                        confirmButtonText: "Kembali ke Daftar Aktivitas",
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