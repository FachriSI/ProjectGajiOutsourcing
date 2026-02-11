<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Dashboard (Standalone) -->
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Laporan
                </a>

                <!-- SDM & Vendor (Dropdown) -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSdm"
                    aria-expanded="false" aria-controls="collapseSdm">
                    <i class="fas fa-users"></i> SDM & Vendor
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('karyawan*') || request()->is('perusahaan*') ? 'show' : '' }}"
                    id="collapseSdm" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('karyawan*') ? 'active' : '' }}"
                            href="{{ url('/karyawan') }}">
                            <i class="fas fa-user"></i> Karyawan
                        </a>
                        <a class="nav-link {{ request()->is('perusahaan*') ? 'active' : '' }}"
                            href="{{ url('/perusahaan') }}">
                            <i class="fas fa-building"></i> Perusahaan
                        </a>
                    </nav>
                </div>

                <!-- Paket & Penempatan (Dropdown) -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePaket"
                    aria-expanded="false" aria-controls="collapsePaket">
                    <i class="fas fa-box"></i> Paket & Penempatan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('paket') || request()->is('penempatan*') || request()->is('lokasi*') ? 'show' : '' }}"
                    id="collapsePaket" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('paket') ? 'active' : '' }}" href="{{ url('/paket') }}">
                            <i class="fas fa-box"></i> Paket
                        </a>
                        <a class="nav-link {{ request()->is('penempatan*') ? 'active' : '' }}"
                            href="{{ url('/penempatan') }}">
                            <i class="fas fa-map-marker-alt"></i> Penempatan
                        </a>

                        <a class="nav-link {{ request()->is('lokasi*') ? 'active' : '' }}" href="{{ url('/lokasi') }}">
                            <i class="fas fa-user-tie"></i> Lokasi
                        </a>
                    </nav>
                </div>

                <!-- Organisasi (Dropdown) -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOrganisasi"
                    aria-expanded="false" aria-controls="collapseOrganisasi">
                    <i class="fas fa-sitemap"></i> Organisasi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('departemen*') || request()->is('fungsi*') || request()->is('unit-kerja*') ? 'show' : '' }}"
                    id="collapseOrganisasi" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('departemen*') ? 'active' : '' }}"
                            href="{{ url('/departemen') }}">
                            <i class="fas fa-building"></i> Departemen
                        </a>
                        <a class="nav-link {{ request()->is('fungsi*') ? 'active' : '' }}" href="{{ url('/fungsi') }}">
                            <i class="fas fa-cogs"></i> Fungsi
                        </a>

                        <a class="nav-link {{ request()->is('unit-kerja*') ? 'active' : '' }}"
                            href="{{ url('/unit-kerja') }}">
                            <i class="fas fa-briefcase"></i> Unit Kerja
                        </a>
                    </nav>
                </div>



                <!-- Tunjangan (Dropdown) -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTunjangan"
                    aria-expanded="false" aria-controls="collapseTunjangan">
                    <i class="fas fa-hand-holding-usd"></i> Tunjangan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('jabatan*') || request()->is('resiko*') || request()->is('harianshift*') || request()->is('kuotajam*') || request()->is('masakerja*') || request()->is('pakaian*') || request()->is('penyesuaian*') || request()->is('medical-checkup*') ? 'show' : '' }}"
                    id="collapseTunjangan" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('jabatan*') ? 'active' : '' }}"
                            href="{{ url('/jabatan') }}">
                            <i class="fas fa-id-badge"></i> Tunjangan Jabatan
                        </a>
                        <a class="nav-link {{ request()->is('resiko*') ? 'active' : '' }}" href="{{ url('/resiko') }}">
                            <i class="fas fa-exclamation-triangle"></i> Tunjangan Risiko
                        </a>
                        <a class="nav-link {{ request()->is('harianshift*') ? 'active' : '' }}"
                            href="{{ url('/harianshift') }}">
                            <i class="fas fa-clock"></i> Harian/Shift
                        </a>
                        <a class="nav-link {{ request()->is('kuotajam*') ? 'active' : '' }}"
                            href="{{ url('/kuotajam') }}">
                            <i class="fas fa-hourglass-half"></i> Kuota Jam
                        </a>
                        <a class="nav-link {{ request()->is('masakerja*') ? 'active' : '' }}"
                            href="{{ url('/masakerja') }}">
                            <i class="fas fa-calendar-alt"></i> Masa Kerja
                        </a>
                        <a class="nav-link {{ request()->is('pakaian*') ? 'active' : '' }}"
                            href="{{ url('/pakaian') }}">
                            <i class="fas fa-tshirt"></i> Pakaian
                        </a>
                        <a class="nav-link {{ request()->is('penyesuaian*') ? 'active' : '' }}"
                            href="{{ url('/penyesuaian') }}">
                            <i class="fas fa-sliders-h"></i> Penyesuaian
                        </a>
                        <a class="nav-link {{ request()->is('medical-checkup*') ? 'active' : '' }}"
                            href="{{ url('/medical-checkup') }}">
                            <i class="fas fa-notes-medical"></i> Medical Checkup
                        </a>
                        <a class="nav-link {{ request()->is('lebaran*') ? 'active' : '' }}"
                            href="{{ url('/lebaran') }}">
                            <i class="fas fa-calendar-alt"></i> Data Lebaran
                        </a>
                    </nav>
                </div>

                <!-- UMP (Standalone) -->
                <a class="nav-link {{ request()->is('ump*') ? 'active' : '' }}" href="{{ url('/ump') }}">
                    <i class="fas fa-coins"></i> UMP
                </a>

                <!-- Kontrak (Standalone) -->
                <a class="nav-link {{ request()->is('kalkulator-kontrak*') ? 'active' : '' }}"
                    href="{{ url('/kalkulator-kontrak') }}">
                    <i class="fas fa-file-contract"></i> Kontrak
                </a>

            </div>
        </div>
    </nav>
</div>