<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('assets/img/sp-white2.png') }}" alt="Logo" style="height: 30px; margin-right: 10px;">
        Outsourcing SP
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <!-- <li><a class="dropdown-item" href="#">Activity Log</a></li> -->
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); confirmLogout();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <script>
                        function confirmLogout() {
                            Swal.fire({
                                title: 'Konfirmasi Logout',
                                text: "Apakah Anda yakin ingin keluar?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: 'rgba(11, 203, 50, 1)',
                                confirmButtonText: 'Ya, Keluar',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('logout-form').submit();
                                }
                            })
                        }
                    </script>
                </li>
            </ul>
        </li>
    </ul>
</nav>