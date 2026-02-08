@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="mt-4">Laporan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ringkasan & Analisis Laporan</li>
    </ol>

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
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Karyawan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalKaryawan) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Paket -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Paket</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalPaket) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
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
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Paket dengan Jumlah Karyawan Terbanyak</h6>
                            <div style="height: 300px;">
                                <canvas id="topQuotaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Paket dengan % Kuota Kosong Tertinggi</h6>
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
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Paket dengan Total Nilai Kontrak Tertinggi
                            </h6>
                            <div style="height: 300px;">
                                <canvas id="costChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Tren Total Nilai Kontrak per Tahun</h6>
                            <div style="height: 300px;">
                                <canvas id="contractTrendChart"></canvas>
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
                    <div class="card shadow-sm">
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
                                        <i class="fas fa-venus text-info d-block mb-2"></i>
                                        <div class="h4 mb-0 font-weight-bold text-info">
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
                                        <i class="fas fa-user-check text-success d-block mb-2"></i>
                                        <div class="h4 mb-0 font-weight-bold text-success">
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
                                    <i class="fas fa-user-times text-warning d-block mb-2"></i>
                                    <div class="h4 mb-0 font-weight-bold text-warning">
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
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Pertumbuhan Populasi Karyawan (Akumulasi)</h6>
                            <div style="height: 300px;">
                                <canvas id="populationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Department Distribution (New) -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
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
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Sebaran Usia</h6>
                            <div style="height: 300px;">
                                <canvas id="ageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
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
                    <div class="card shadow-sm h-100">
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
                    <div class="card shadow-sm h-100">
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
                                    backgroundColor: options.backgroundColor || 'rgba(54, 162, 235, 0.7)',
                                    borderColor: options.borderColor || 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
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
                        makeBarChart('tenureChart', {!! json_encode(array_keys($masaKerjaCount)) !!}, {!! json_encode(array_values($masaKerjaCount)) !!}, 'Jumlah');
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
                                            borderColor: 'rgb(75, 192, 192)',
                                            tension: 0.1
                                        },
                                        {
                                            label: 'Keluar',
                                            data: {!! json_encode($employeeDynamics->pluck('keluar') ?? []) !!},
                                            borderColor: 'rgb(255, 99, 132)',
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
                                        borderColor: 'rgb(153, 102, 255)',
                                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: { responsive: true, maintainAspectRatio: false }
                            });
                        }


                        makeBarChart('shiftChart', {!! json_encode(array_keys($shiftCount)) !!}, {!! json_encode(array_values($shiftCount)) !!}, 'Jumlah Karyawan', { indexAxis: 'y' });


                        // Added Jabatan Chart
                        makeBarChart('jabatanChart', {!! json_encode(array_keys($jabatanCount)) !!}, {!! json_encode(array_values($jabatanCount)) !!}, 'Jumlah Karyawan', { indexAxis: 'y' });

                        // Added Departemen Chart (Vertical Bar for better readability with many depts)
                        makeBarChart('departemenChart', {!! json_encode(array_keys($departemenCount)) !!}, {!! json_encode(array_values($departemenCount)) !!}, 'Jumlah Karyawan', {
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)'
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
                        const emptyCtx = document.getElementById('emptyQuotaChart');
                        if (emptyCtx) {
                            if (chartInstances['emptyQuotaChart']) chartInstances['emptyQuotaChart'].destroy();

                            // Prepare Data
                            const paketNames = {!! json_encode($topPaketKosong->pluck('nama_paket')) !!};
                            const paketTerisi = {!! json_encode($topPaketKosong->pluck('terisi')) !!};
                            const paketKosong = {!! json_encode($topPaketKosong->pluck('kosong')) !!};
                            const paketTotal = {!! json_encode($topPaketKosong->pluck('kuota')) !!};
                            const paketPersen = {!! json_encode($topPaketKosong->pluck('persen_kosong')) !!};


                            // Revert to Percentage Chart based on User Request
                            chartInstances['emptyQuotaChart'] = new Chart(emptyCtx.getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: paketNames,
                                    datasets: [{
                                        label: '% Kekosongan',
                                        data: paketPersen,
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { position: 'top' },
                                        tooltip: {
                                            callbacks: {
                                                label: function (tooltipItem) {
                                                    return tooltipItem.formattedValue + '%';
                                                },
                                                footer: function (tooltipItems) {
                                                    let index = tooltipItems[0].dataIndex;
                                                    let total = paketTotal[index];
                                                    let terisi = paketTerisi[index];
                                                    let kosong = paketKosong[index];
                                                    return `Total Kuota: ${total}\nTerisi: ${terisi}\nKosong: ${kosong}`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            stacked: false,
                                            ticks: { callback: value => value + '%' }
                                        },
                                        y: { stacked: false }
                                    }
                                }
                            });
                        }

                        // 3. Cost Analysis
                        // 3. Cost Analysis (Estimation per Paket)
                        makeBarChart('costChart',
                            {!! json_encode(array_keys($unitKerjaCost->toArray())) !!},
                            {!! json_encode(array_values($unitKerjaCost->toArray())) !!},
                            'Total Nilai (Rp)',
                            {
                                indexAxis: 'y',
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
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
                                
                                chartInstances['contractTrendChart'] = new Chart(trendCtx.getContext('2d'), {
                                    type: chartType,
                                    data: {
                                        labels: trendLabels,
                                        datasets: [{
                                            label: 'Total Nilai Kontrak',
                                            data: trendData,
                                            borderColor: 'rgb(54, 162, 235)',
                                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                            fill: true,
                                            tension: 0.1,
                                            barPercentage: 0.5 // For single bar width control
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: { 
                                            y: { 
                                                title: { display: true, text: 'Total Nilai (Rp)' },
                                                ticks: { 
                                                    callback: function(value) {
                                                        if (value >= 1000000000) {
                                                            return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                                                        } else {
                                                            return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                                                        }
                                                    }
                                                } 
                                            },
                                            x: {
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
            });
        </script>
@endsection
    ```