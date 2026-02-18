@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line me-2 text-primary"></i> Dashboard</h1>
                <p class="text-muted small mb-0 mt-1">Ringkasan & Analisis Laporan</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation (Pills Style with Container) -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <ul class="nav nav-pills" id="reportTabs" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active" id="paket-tab" data-bs-toggle="pill" data-bs-target="#paket" type="button"
                    role="tab">
                    <i class="fas fa-box me-1"></i> Analisis Paket
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link" id="karyawan-tab" data-bs-toggle="pill" data-bs-target="#karyawan" type="button"
                    role="tab">
                    <i class="fas fa-users me-1"></i> Analisis Karyawan
                </button>
            </li>

        </ul>
    </div>

    <!-- Strategic Global KPIs -->
    <div class="row mb-4">
        <!-- Total Karyawan -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-gradient-blue h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-4 card-icon-bg-white"
                        style="width: 60px; height: 60px; min-width: 60px;">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase fw-bold small mb-1"
                            style="color: var(--text-secondary); letter-spacing: 1px;">TOTAL
                            KARYAWAN</div>
                        <div class="display-6 fw-bold mb-0" style="color: var(--text-primary);">
                            {{ number_format($totalKaryawan) }}
                        </div>
                        <div class="text-muted small mt-2">
                            Karyawan Terdaftar
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Total Paket -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-gradient-blue h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-4 card-icon-bg-white"
                        style="width: 60px; height: 60px; min-width: 60px;">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase fw-bold small mb-1" translate="no"
                            style="color: var(--text-secondary); letter-spacing: 1px;">TOTAL PAKET
                        </div>
                        <div class="display-6 fw-bold mb-0" style="color: var(--text-primary);">
                            {{ number_format($totalPaket) }}
                        </div>
                        <div class="text-muted small mt-2">
                            Paket Terdaftar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content" id="reportTabsContent">

        <!-- 1. Analisis Paket Tab -->
        <div class="tab-pane fade show active" id="paket" role="tabpanel">

            <!-- Top Row: Quota Analysis -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">10 Paket dengan Jumlah Karyawan Terbanyak</h6>
                            <div style="height: 300px;">
                                <canvas id="topQuotaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">10 Paket % Kuota Kosong Tertinggi</h6>
                            <div style="height: 300px;">
                                <canvas id="emptyQuotaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Cost & Trend -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">10 Paket dengan Total Nilai Kontrak Tertinggi
                            </h6>
                            <div style="height: 300px;">
                                <canvas id="costChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Tren Total Nilai Kontrak per Tahun</h6>
                            <div style="height: 300px;">
                                <canvas id="contractTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Row: Vendor Distribution -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Distribusi Karyawan per Vendor (Perusahaan)</h6>
                            <div style="height: 350px;">
                                <canvas id="vendorChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Analisis Karyawan Tab (Consolidated) -->
        <div class="tab-pane fade" id="karyawan" role="tabpanel">
            <!-- Ringkasan Demografis -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-lg-3 col-md-3 col-6 mb-2">
                                    <div class="border-end">
                                        <i class="fas fa-mars text-primary d-block mb-2"></i>
                                        <div class="h4 mb-0 font-weight-bold text-primary">
                                            {{ number_format($genderCount['Laki-laki'] ?? 0) }}
                                            @php
                                                $total = array_sum($genderCount);
                                                $pct = $total > 0 ? round((($genderCount['Laki-laki'] ?? 0) / $total) * 100, 1) : 0;
                                            @endphp
                                            <small class="text-muted">({{ $pct }}%)</small>
                                        </div>
                                        <small class="text-muted">Laki-laki</small>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6 mb-2">
                                    <div class="border-end">
                                        <i class="fas fa-venus d-block mb-2" style="color: #6ea8fe;"></i>
                                        <div class="h4 mb-0 font-weight-bold" style="color: #6ea8fe;">
                                            {{ number_format($genderCount['Perempuan'] ?? 0) }}
                                            @php
                                                $pct2 = $total > 0 ? round((($genderCount['Perempuan'] ?? 0) / $total) * 100, 1) : 0;
                                            @endphp
                                            <small class="text-muted">({{ $pct2 }}%)</small>
                                        </div>
                                        <small class="text-muted">Perempuan</small>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6 mb-2">
                                    <div class="border-end">
                                        <i class="fas fa-user-check text-primary d-block mb-2"></i>
                                        <div class="h4 mb-0 font-weight-bold text-primary">
                                            {{ number_format($statusAktifCount['Aktif'] ?? 0) }}
                                            @php
                                                $totalStatus = array_sum($statusAktifCount);
                                                $pct3 = $totalStatus > 0 ? round((($statusAktifCount['Aktif'] ?? 0) / $totalStatus) * 100, 1) : 0;
                                            @endphp
                                            <small class="text-muted">({{ $pct3 }}%)</small>
                                        </div>
                                        <small class="text-muted">Aktif</small>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6 mb-2">
                                    <i class="fas fa-user-times text-secondary d-block mb-2"></i>
                                    <div class="h4 mb-0 font-weight-bold text-secondary">
                                        {{ number_format($statusAktifCount['Tidak Aktif'] ?? 0) }}
                                        @php
                                            $pct4 = $totalStatus > 0 ? round((($statusAktifCount['Tidak Aktif'] ?? 0) / $totalStatus) * 100, 1) : 0;
                                        @endphp
                                        <small class="text-muted">({{ $pct4 }}%)</small>
                                    </div>
                                    <small class="text-muted">Tidak Aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Pertumbuhan Populasi (Full Width) -->
            <div class="row mb-4">
                <div class="col-lg-12 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Tren Jumlah Karyawan Aktif per Tahun</h6>
                            <div style="height: 300px;">
                                <canvas id="populationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Department Distribution (New) -->
                <div class="col-lg-12 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Sebaran per Departemen</h6>
                            <div style="height: 300px;">
                                <canvas id="departemenChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Demographics Row -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Sebaran Usia</h6>
                            <div style="height: 300px;">
                                <canvas id="ageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Masa Kerja</h6>
                            <div style="height: 300px;">
                                <canvas id="tenureChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operational & Risk -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Distribusi Jenis Shift</h6>
                            <div style="height: 300px;">
                                <canvas id="shiftChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Job Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Distribusi Jabatan</h6>
                            <div style="height: 300px;">
                                <canvas id="jabatanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>

        <!-- Load Chart.js dan Plugin Label -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Safety check for Chart.js
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js not loaded');
                    return;
                }

                const chartInstances = {};

                function makeBarChart(canvasId, labels, data, label, options = {}) {
                    try {
                        const canvas = document.getElementById(canvasId);
                        if (!canvas) return;

                        if (chartInstances[canvasId]) {
                            chartInstances[canvasId].destroy();
                            delete chartInstances[canvasId];
                        }

                        const ctx = canvas.getContext('2d');
                        const defaultConfig = {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        };
                        const finalOptions = { ...defaultConfig, ...options };

                        chartInstances[canvasId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels || [],
                                datasets: [{
                                    label: label,
                                    data: data || [],
                                    // Dark Mode Gradient for Bar
                                    backgroundColor: function (context) {
                                        const chart = context.chart;
                                        const { ctx, chartArea } = chart;
                                        if (!chartArea) return null;
                                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.8)');
                                        return gradient;
                                    },
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 0,
                                    borderRadius: 4,
                                    fill: true,
                                    tension: 0.4, // Smooth Curves (Spline)
                                    categoryPercentage: 0.8
                                }]
                            },
                            options: finalOptions
                        });
                    } catch (e) {
                        console.error('Error rendering chart ' + canvasId, e);
                    }
                }

                function makePieChart(canvasId, labels, data, bgColors) {
                    try {
                        const canvas = document.getElementById(canvasId);
                        if (!canvas) return;

                        if (chartInstances[canvasId]) {
                            chartInstances[canvasId].destroy();
                            delete chartInstances[canvasId];
                        }

                        const ctx = canvas.getContext('2d');
                        chartInstances[canvasId] = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labels || [],
                                datasets: [{
                                    data: data || [],
                                    backgroundColor: bgColors,
                                    hoverOffset: 4
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });
                    } catch (e) {
                        console.error('Error rendering pie ' + canvasId, e);
                    }
                }

                // renderPacketCharts() function removed - charts were deleted during cleanup


                function renderKaryawanCharts() {
                    try {
                        // Demographics (Gender & Status moved to summary card - charts removed)
                        makeBarChart('ageChart', {!! json_encode(array_keys($usiaCount)) !!}, {!! json_encode(array_values($usiaCount)) !!}, 'Jumlah');
                        makeBarChart('tenureChart', {!! json_encode(array_keys($masaKerjaCount)) !!}, {!! json_encode(array_values($masaKerjaCount)) !!}, 'Jumlah', {
                            backgroundColor: 'rgba(100, 116, 139, 0.7)',
                            borderColor: 'rgba(100, 116, 139, 1)'
                        });
                        // originChart removed - no decision-making value

                        // Dynamics
                        const dynCtx = document.getElementById('dynamicsChart');
                        if (dynCtx) {
                            if (chartInstances['dynamicsChart']) chartInstances['dynamicsChart'].destroy();
                            chartInstances['dynamicsChart'] = new Chart(dynCtx.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: {!! json_encode($employeeDynamics->pluck('tahun') ?? []) !!},
                                    datasets: [
                                        {
                                            label: 'Masuk',
                                            data: {!! json_encode($employeeDynamics->pluck('masuk') ?? []) !!},
                                            borderColor: 'rgb(14, 165, 233)',
                                            tension: 0.1
                                        },
                                        {
                                            label: 'Keluar',
                                            data: {!! json_encode($employeeDynamics->pluck('keluar') ?? []) !!},
                                            borderColor: 'rgb(239, 68, 68)',
                                            tension: 0.1
                                        }
                                    ]
                                },
                                options: { responsive: true, maintainAspectRatio: false }
                            });
                        }

                        // Population
                        const popCtx = document.getElementById('populationChart');
                        if (popCtx) {
                            if (chartInstances['populationChart']) chartInstances['populationChart'].destroy();
                            chartInstances['populationChart'] = new Chart(popCtx.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: {!! json_encode($employeeDynamics->pluck('tahun') ?? []) !!},
                                    datasets: [{
                                        label: 'Total Populasi',
                                        data: {!! json_encode($employeeDynamics->pluck('populasi') ?? []) !!},
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: { responsive: true, maintainAspectRatio: false }
                            });
                        }


                        makeBarChart('shiftChart', {!! json_encode(array_keys($shiftCount)) !!}, {!! json_encode(array_values($shiftCount)) !!}, 'Jumlah Karyawan', {
                            indexAxis: 'y',
                            backgroundColor: 'rgba(14, 165, 233, 0.7)',
                            borderColor: 'rgba(14, 165, 233, 1)'
                        });


                        // Added Jabatan Chart
                        makeBarChart('jabatanChart', {!! json_encode(array_keys($jabatanCount)) !!}, {!! json_encode(array_values($jabatanCount)) !!}, 'Jumlah Karyawan', {
                            indexAxis: 'y',
                            backgroundColor: 'rgba(100, 116, 139, 0.7)',
                            borderColor: 'rgba(100, 116, 139, 1)'
                        });

                        // Added Departemen Chart (Vertical Bar for better readability with many depts)
                        makeBarChart('departemenChart', {!! json_encode(array_keys($departemenCount)) !!}, {!! json_encode(array_values($departemenCount)) !!}, 'Jumlah Karyawan', {
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)'
                        });

                    } catch (e) { console.error('Error rendering karyawan charts', e); }
                }





                // Event Listeners
                const triggerTabList = [].slice.call(document.querySelectorAll('#reportTabs button'));
                triggerTabList.forEach(function (triggerEl) {
                    triggerEl.addEventListener('shown.bs.tab', function (event) {
                        const targetId = event.target.getAttribute('data-bs-target');
                        if (targetId === '#paket') renderPacketCharts();
                        if (targetId === '#karyawan') renderKaryawanCharts();

                    });
                });

                // Initial Render for Active Tab
                if (document.querySelector('#paket.active')) {
                    renderPacketCharts();
                }

                // Packet Charts Rendering
                function renderPacketCharts() {
                    try {
                        // 1. Top Quota (Jumlah Karyawan Terbanyak)
                        makeBarChart('topQuotaChart',
                            {!! json_encode($topPaketKuota->pluck('nama_paket')) !!},
                            {!! json_encode($topPaketKuota->pluck('terisi')) !!},
                            'Jumlah Karyawan',
                            { indexAxis: 'y' }
                        );

                        // 2. Empty Quota (Stacked: Terisi vs Kosong)
                        // Use makeBarChart to ensure Gradient Consistency
                        makeBarChart('emptyQuotaChart',
                            {!! json_encode($topPaketKosong->pluck('nama_paket')) !!},
                            {!! json_encode($topPaketKosong->pluck('persen_kosong')) !!},
                            '% Kekosongan',
                            {
                                indexAxis: 'y',
                                scales: { x: { ticks: { callback: value => value + '%' } } },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function (tooltipItem) {
                                                return tooltipItem.formattedValue + '%';
                                            },
                                            footer: function (tooltipItems) {
                                                const index = tooltipItems[0].dataIndex;
                                                const paketTotal = {!! json_encode($topPaketKosong->pluck('kuota')) !!};
                                                const paketTerisi = {!! json_encode($topPaketKosong->pluck('terisi')) !!};
                                                const paketKosong = {!! json_encode($topPaketKosong->pluck('kosong')) !!};
                                                return `Total: ${paketTotal[index]}\nTerisi: ${paketTerisi[index]}\nKosong: ${paketKosong[index]}`;
                                            }
                                        }
                                    }
                                }
                            }
                        );


                        // 3. Cost Analysis
                        // 3. Cost Analysis (Estimation per Paket)
                        makeBarChart('costChart',
                            {!! json_encode(array_keys($unitKerjaCost->toArray())) !!},
                            {!! json_encode(array_values($unitKerjaCost->toArray())) !!},
                            'Total Nilai (Rp)',
                            {
                                indexAxis: 'y',
                                // Note: makeBarChart enforces Blue Gradient by default, overriding manual colors
                                scales: { x: { ticks: { callback: value => 'Rp ' + (value / 1000000).toFixed(0) + ' Jt' } } }
                            }
                        );

                        // 4. Contract Trend
                        // 4. Contract Trend (Adjusted for Clarity)
                        const trendCtx = document.getElementById('contractTrendChart');
                        if (trendCtx) {
                            if (chartInstances['contractTrendChart']) chartInstances['contractTrendChart'].destroy();
                            const trendLabels = {!! json_encode($contractTrend->pluck('tahun')) !!};
                            const trendData = {!! json_encode($contractTrend->pluck('total_nilai')) !!};

                            // Use Bar chart if less than 2 data points for better visibility
                            const chartType = trendLabels.length < 2 ? 'bar' : 'line';

                            // Define Gradient for Line Chart Fill
                            const trendGradient = trendCtx.getContext('2d').createLinearGradient(0, 400, 0, 0);
                            trendGradient.addColorStop(0, 'rgba(59, 130, 246, 0.0)');
                            trendGradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

                            chartInstances['contractTrendChart'] = new Chart(trendCtx.getContext('2d'), {
                                type: chartType,
                                data: {
                                    labels: trendLabels,
                                    datasets: [{
                                        label: 'Total Nilai Kontrak',
                                        data: trendData,
                                        borderColor: 'rgb(59, 130, 246)', // Blue 500
                                        backgroundColor: trendGradient,   // Matching Line Gradient
                                        fill: true,
                                        tension: 0.4, // Smooth Spline
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: 'rgb(59, 130, 246)',
                                        pointBorderWidth: 2,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        barPercentage: 0.5
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: { legend: { display: false } }, // Hide Legend for consistency with bars
                                    scales: {
                                        y: {
                                            title: { display: true, text: 'Total Nilai (Rp)' },
                                            beginAtZero: true,
                                            ticks: {
                                                color: '#64748b',
                                                callback: function (value) {
                                                    if (value >= 1000000000) {
                                                        return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                                                    } else {
                                                        return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                                                    }
                                                }
                                            },
                                            grid: { color: '#e2e8f0' }
                                        },
                                        x: {
                                            ticks: { color: '#64748b' },
                                            grid: { display: false },
                                            title: { display: true, text: 'Tahun' }
                                        }
                                    }
                                }
                            });
                        }

                    } catch (e) { console.error('Error rendering packet charts', e); }
                }

                // Initial Render
                renderPacketCharts();

                // 5. Vendor Distribution
                makeBarChart('vendorChart',
                    {!! json_encode(array_keys($vendorCount)) !!},
                    {!! json_encode(array_values($vendorCount)) !!},
                    'Jumlah Karyawan',
                    {
                        indexAxis: 'x',
                        scales: { y: { beginAtZero: true } },
                        backgroundColor: '#1cc88a', // Green
                        borderColor: '#1cc88a'
                    }
                );
            });
        </script>
@endsection
    ```