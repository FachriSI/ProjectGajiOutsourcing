@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="mt-4">Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Report Overview & Analytics</li>
    </ol>

    <!-- Tabs Navigation (Pills Style with Container) -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <ul class="nav nav-pills" id="reportTabs" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active" id="paket-tab" data-bs-toggle="pill" data-bs-target="#paket" type="button" role="tab">
                    <i class="fas fa-box me-1"></i> Analisis Paket
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link" id="karyawan-tab" data-bs-toggle="pill" data-bs-target="#karyawan" type="button" role="tab">
                    <i class="fas fa-users me-1"></i> Analisis Karyawan
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link" id="organisasi-tab" data-bs-toggle="pill" data-bs-target="#organisasi" type="button" role="tab">
                    <i class="fas fa-sitemap me-1"></i> Analisis Organisasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ump-tab" data-bs-toggle="pill" data-bs-target="#ump" type="button" role="tab">
                    <i class="fas fa-coins me-1"></i> UMP
                </button>
            </li>
        </ul>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content" id="reportTabsContent">
        
        <!-- 1. Analisis Paket Tab -->
        <div class="tab-pane fade show active" id="paket" role="tabpanel">
            <div class="row">
                 <!-- Top 10 Paket (Kuota) -->
                 <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Paket (Kuota Terbesar)</h6>
                            <div style="height: 300px;">
                                <canvas id="topPacketChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Realisasi Kuota (New) -->
                <div class="col-lg-6 mb-4">
                     <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Realisasi Kuota (Terisi vs Kosong)</h6>
                            <div style="height: 300px;">
                                <canvas id="quotaRealizationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Analisis Biaya (Cost) -->
                <div class="col-lg-6 mb-4">
                     <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Unit Kerja (Nilai Kontrak)</h6>
                            <div style="height: 300px;">
                                <canvas id="unitCostChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- NEW: Tren Nilai Kontrak -->
                 <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                       <div class="card-body">
                           <h6 class="text-center font-weight-bold mb-3">Tren Total Nilai Kontrak (Tahunan)</h6>
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
             <!-- NEW ROW: Trend Dynamics -->
             <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Dinamika Karyawan (Masuk vs Keluar)</h6>
                            <div style="height: 300px;">
                                <canvas id="dynamicsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Pertumbuhan Populasi Karyawan (Akumulasi)</h6>
                            <div style="height: 300px;">
                                <canvas id="populationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Demographics Row -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Gender</h6>
                            <div style="height: 200px;">
                                <canvas id="genderChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Status Aktif</h6>
                            <div style="height: 200px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Sebaran Usia</h6>
                            <div style="height: 200px;">
                                <canvas id="ageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Masa Kerja</h6>
                            <div style="height: 200px;">
                                <canvas id="tenureChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asal Daerah Full Width -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Asal Daerah (Kecamatan)</h6>
                            <div style="height: 300px;">
                                <canvas id="originChart"></canvas>
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
                 <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Distribusi Tingkat Resiko</h6>
                            <div style="height: 300px;">
                                <canvas id="resikoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                 <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Alasan Berhenti</h6>
                            <div style="height: 300px;">
                                <canvas id="reasonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Analisis Organisasi Tab -->
        <div class="tab-pane fade" id="organisasi" role="tabpanel">
             <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Top 10 Jabatan (Populasi Terbanyak)</h6>
                            <div style="height: 350px;">
                                <canvas id="jabatanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-3">Sebaran Karyawan per Departemen</h6>
                            <div style="height: 350px;">
                                <canvas id="departemenChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. UMP Tab -->
        <div class="tab-pane fade" id="ump" role="tabpanel">
            <div class="row">
                 <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-4">Tren UMP Sumbar per Tahun</h6>
                            <div style="height: 350px;">
                                <canvas id="umpChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- NEW: UMP Growth % -->
                 <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-4">Kenaikan UMP (%) per Tahun</h6>
                            <div style="height: 350px;">
                                <canvas id="umpGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-4">UMP per Lokasi (2024)</h6>
                            <div style="height: 350px;">
                                <canvas id="umpLocationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- NEW: Detailed UMP Pivot Table -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-center font-weight-bold mb-4">Detail UMP per Daerah dan Tahun</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Lokasi</th>
                                            @foreach($umpYears as $year)
                                                <th class="text-center">{{ $year }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($umpMatrix as $lokasi => $years)
                                            <tr>
                                                <td>{{ $lokasi }}</td>
                                                @foreach($umpYears as $year)
                                                    <td class="text-end">
                                                        @if(isset($years[$year]))
                                                            Rp {{ number_format($years[$year], 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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

            // === Render Functions Wrapped in Try-Catch ===
            function renderPacketCharts() {
                try {
                    // Top Packet
                    makeBarChart('topPacketChart', {!! json_encode($topPaket->pluck('nama_paket') ?? []) !!}, {!! json_encode($topPaket->pluck('kuota') ?? []) !!}, 'Kuota', { indexAxis: 'y' });

                    // Cost
                    const unitCostCtx = document.getElementById('unitCostChart');
                    if (unitCostCtx) {
                        if (chartInstances['unitCostChart']) chartInstances['unitCostChart'].destroy();
                        chartInstances['unitCostChart'] = new Chart(unitCostCtx.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($unitKerjaCost->keys() ?? []) !!},
                                datasets: [{
                                    label: 'Total Nilai Kontrak',
                                    data: {!! json_encode($unitKerjaCost->values() ?? []) !!},
                                    backgroundColor: 'rgba(255, 159, 64, 0.7)'
                                }]
                            },
                            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: {display:false} } }
                        });
                    }

                    // Contract Trend
                    const trendCtx = document.getElementById('contractTrendChart');
                    if (trendCtx) {
                         if (chartInstances['contractTrendChart']) chartInstances['contractTrendChart'].destroy();
                         chartInstances['contractTrendChart'] = new Chart(trendCtx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: {!! json_encode($contractTrend->pluck('tahun') ?? []) !!},
                                datasets: [{
                                    label: 'Total Nilai Kontrak (Rp)',
                                    data: {!! json_encode($contractTrend->pluck('total_nilai') ?? []) !!},
                                    borderColor: 'rgb(54, 162, 235)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                                scales: { y: { ticks: { callback: value => 'Rp ' + (value/1000000000).toFixed(1) + 'M' } } }
                            }
                         });
                    }
                    
                    // Realisasi Kuota
                    const quotaCtx = document.getElementById('quotaRealizationChart');
                    if (quotaCtx) {
                         const labels = {!! json_encode($topPaket->pluck('nama_paket') ?? []) !!};
                         const terisi = {!! json_encode($topPaket->pluck('terisi') ?? []) !!};
                         const kuota = {!! json_encode($topPaket->pluck('kuota') ?? []) !!};
                         const kosong = kuota.map((k, i) => k - (terisi[i] || 0));

                         if (chartInstances['quotaRealizationChart']) chartInstances['quotaRealizationChart'].destroy();
                         chartInstances['quotaRealizationChart'] = new Chart(quotaCtx.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    { label: 'Terisi', data: terisi, backgroundColor: 'rgba(75, 192, 192, 0.7)' },
                                    { label: 'Kosong', data: kosong, backgroundColor: 'rgba(255, 99, 132, 0.7)' }
                                ]
                            },
                             options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true } } }
                         });
                    }
                } catch (e) { console.error('Error rendering packet charts', e); }
            }

            function renderKaryawanCharts() {
                try {
                    // Demographics
                    makePieChart('genderChart', {!! json_encode(array_keys($genderCount)) !!}, {!! json_encode(array_values($genderCount)) !!}, ['#36A2EB', '#FF6384']);
                    makePieChart('statusChart', {!! json_encode(array_keys($statusAktifCount)) !!}, {!! json_encode(array_values($statusAktifCount)) !!}, ['#4BC0C0', '#FFCD56']);
                    makeBarChart('ageChart', {!! json_encode(array_keys($usiaCount)) !!}, {!! json_encode(array_values($usiaCount)) !!}, 'Jumlah');
                    makeBarChart('tenureChart', {!! json_encode(array_keys($masaKerjaCount)) !!}, {!! json_encode(array_values($masaKerjaCount)) !!}, 'Jumlah');
                    makeBarChart('originChart', {!! json_encode(array_keys($asalKecamatanCount)) !!}, {!! json_encode(array_values($asalKecamatanCount)) !!}, 'Jumlah', { indexAxis: 'y' });

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
                    makeBarChart('resikoChart', {!! json_encode(array_keys($resikoCount)) !!}, {!! json_encode(array_values($resikoCount)) !!}, 'Jumlah Karyawan', { indexAxis: 'y', backgroundColor: 'rgba(255, 99, 132, 0.7)' });
                    makeBarChart('reasonChart', {!! json_encode($exitReasons->keys()) !!}, {!! json_encode($exitReasons->values()) !!}, 'Jumlah', { indexAxis: 'y' });

                } catch (e) { console.error('Error rendering karyawan charts', e); }
            }

            function renderUmpCharts() {
                try {
                    const umpCtx = document.getElementById('umpChart');
                    if (umpCtx) {
                        if (chartInstances['umpChart']) chartInstances['umpChart'].destroy();
                        chartInstances['umpChart'] = new Chart(umpCtx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: {!! json_encode($umpTrend->pluck('tahun') ?? []) !!},
                                datasets: [{
                                    label: 'Nilai UMP (Rp)',
                                    data: {!! json_encode($umpTrend->pluck('ump') ?? []) !!},
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    fill: true
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                        });
                    }

                    const growthCtx = document.getElementById('umpGrowthChart');
                    if (growthCtx) {
                        if (chartInstances['umpGrowthChart']) chartInstances['umpGrowthChart'].destroy();
                        chartInstances['umpGrowthChart'] = new Chart(growthCtx.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($umpGrowth->pluck('tahun') ?? []) !!},
                                datasets: [{
                                    label: 'Kenaikan (%)',
                                    data: {!! json_encode($umpGrowth->pluck('growth') ?? []) !!},
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                }]
                            },
                             options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { y: { ticks: { callback: value => value + '%' } } }
                            }
                        });
                    }

                   makeBarChart('umpLocationChart', {!! json_encode(array_keys($umpPerLokasi->toArray())) !!}, {!! json_encode(array_values($umpPerLokasi->toArray())) !!}, 'Nilai UMP', {
                        indexAxis: 'y',
                        scales: { x: { ticks: { callback: value => 'Rp ' + (value/1000).toFixed(0) + 'k' } } }
                     });
                } catch(e) { console.error('Error rendering UMP charts', e); }
            }

            function renderOrganisasiCharts() {
                try {
                     makeBarChart('jabatanChart', {!! json_encode(array_keys($jabatanCount)) !!}, {!! json_encode(array_values($jabatanCount)) !!}, 'Jumlah Karyawan', { indexAxis: 'y' });
                     
                     // Pie Chart for Department
                     const deptCtx = document.getElementById('departemenChart');
                     if (deptCtx) {
                         // Generate random colors for departments
                         const deptLabels = {!! json_encode(array_keys($departemenCount)) !!};
                         const deptData = {!! json_encode(array_values($departemenCount)) !!};
                         const bgColors = deptLabels.map(() => `hsla(${Math.random() * 360}, 70%, 50%, 0.7)`);

                         if (chartInstances['departemenChart']) chartInstances['departemenChart'].destroy();
                         chartInstances['departemenChart'] = new Chart(deptCtx.getContext('2d'), {
                             type: 'doughnut',
                             data: {
                                 labels: deptLabels,
                                 datasets: [{ data: deptData, backgroundColor: bgColors }]
                             },
                             options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                         });
                    }
                } catch(e) { console.error('Error rendering organization charts', e); }
            }

            // Event Listeners
            const triggerTabList = [].slice.call(document.querySelectorAll('#reportTabs button'));
            triggerTabList.forEach(function (triggerEl) {
                triggerEl.addEventListener('shown.bs.tab', function (event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    if (targetId === '#paket') renderPacketCharts();
                    if (targetId === '#karyawan') renderKaryawanCharts();
                    if (targetId === '#organisasi') renderOrganisasiCharts();
                    if (targetId === '#ump') renderUmpCharts();
                });
            });

            // Initial Render
            renderPacketCharts();
        });
    </script>
@endsection
```