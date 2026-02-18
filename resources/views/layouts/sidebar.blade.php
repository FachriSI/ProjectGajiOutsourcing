<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Dashboard -->
                <div class="sb-sidenav-menu-heading">Ringkasan</div>
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Laporan
                </a>

                <!-- SDM -->
                <div class="sb-sidenav-menu-heading">Manajemen SDM</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSdm"
                    aria-expanded="false" aria-controls="collapseSdm">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    SDM & Vendor
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('karyawan*') || request()->is('perusahaan*') ? 'show' : '' }}"
                    id="collapseSdm">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('karyawan*') ? 'active' : '' }}"
                            href="{{ url('/karyawan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Karyawan
                        </a>
                        <a class="nav-link {{ request()->is('perusahaan*') ? 'active' : '' }}"
                            href="{{ url('/perusahaan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Perusahaan
                        </a>
                    </nav>
                </div>

                <!-- Paket -->
                <div class="sb-sidenav-menu-heading">Operasional</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePaket"
                    aria-expanded="false" aria-controls="collapsePaket">
                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                    Paket & Penempatan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('paket') || request()->is('penempatan*') || request()->is('lokasi*') ? 'show' : '' }}"
                    id="collapsePaket">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('paket') ? 'active' : '' }}" href="{{ url('/paket') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Paket
                        </a>
                        <a class="nav-link {{ request()->is('penempatan*') ? 'active' : '' }}"
                            href="{{ url('/penempatan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-map-marker-alt"></i></div>
                            Penempatan
                        </a>

                        <a class="nav-link {{ request()->is('lokasi*') ? 'active' : '' }}" href="{{ url('/lokasi') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                            Lokasi
                        </a>
                    </nav>
                </div>

                <!-- Organisasi -->
                <div class="sb-sidenav-menu-heading">Organisasi</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOrganisasi"
                    aria-expanded="false" aria-controls="collapseOrganisasi">
                    <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                    Struktur
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('departemen*') || request()->is('fungsi*') || request()->is('unit-kerja*') ? 'show' : '' }}"
                    id="collapseOrganisasi">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('departemen*') ? 'active' : '' }}"
                            href="{{ url('/departemen') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Departemen
                        </a>
                        <a class="nav-link {{ request()->is('fungsi*') ? 'active' : '' }}" href="{{ url('/fungsi') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Fungsi
                        </a>

                        <a class="nav-link {{ request()->is('unit-kerja*') ? 'active' : '' }}"
                            href="{{ url('/unit-kerja') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Unit Kerja
                        </a>
                    </nav>
                </div>

                <!-- Tunjangan & Keuangan -->
                <div class="sb-sidenav-menu-heading">Remunerasi</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTunjangan"
                    aria-expanded="false" aria-controls="collapseTunjangan">
                    <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    Tunjangan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ request()->is('jabatan*') || request()->is('resiko*') || request()->is('harianshift*') || request()->is('kuotajam*') || request()->is('masakerja*') || request()->is('pakaian*') || request()->is('penyesuaian*') || request()->is('medical-checkup*') ? 'show' : '' }}"
                    id="collapseTunjangan">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->is('jabatan*') ? 'active' : '' }}"
                            href="{{ url('/jabatan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-id-badge"></i></div>
                            Jabatan
                        </a>
                        <a class="nav-link {{ request()->is('resiko*') ? 'active' : '' }}" href="{{ url('/resiko') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            Risiko
                        </a>
                        <a class="nav-link {{ request()->is('harianshift*') ? 'active' : '' }}"
                            href="{{ url('/harianshift') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-clock"></i></div>
                            Harian/Shift
                        </a>
                        <a class="nav-link {{ request()->is('kuotajam*') ? 'active' : '' }}"
                            href="{{ url('/kuotajam') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-hourglass-half"></i></div>
                            Kuota Jam
                        </a>
                        <a class="nav-link {{ request()->is('masakerja*') ? 'active' : '' }}"
                            href="{{ url('/masakerja') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                            Masa Kerja
                        </a>
                        <a class="nav-link {{ request()->is('pakaian*') ? 'active' : '' }}"
                            href="{{ url('/pakaian') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tshirt"></i></div>
                            Pakaian
                        </a>
                        <a class="nav-link {{ request()->is('penyesuaian*') ? 'active' : '' }}"
                            href="{{ url('/penyesuaian') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-sliders-h"></i></div>
                            Penyesuaian
                        </a>
                        <a class="nav-link {{ request()->is('medical-checkup*') ? 'active' : '' }}"
                            href="{{ url('/medical-checkup') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                            Medical Checkup
                        </a>
                        <a class="nav-link {{ request()->is('lebaran*') ? 'active' : '' }}"
                            href="{{ url('/lebaran') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                            Data Lebaran
                        </a>
                    </nav>
                </div>

                <!-- UMP & Kontrak -->
                <a class="nav-link {{ request()->is('ump*') ? 'active' : '' }}" href="{{ url('/ump') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                    UMP / UMK
                </a>

                <a class="nav-link {{ request()->is('kalkulator-kontrak*') ? 'active' : '' }}"
                    href="{{ url('/kalkulator-kontrak') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-contract"></i></div>
                    Hitung Kontrak
                </a>

            </div>
        </div>
    </nav>
</div>