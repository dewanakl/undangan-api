<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Ulems</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-light bg-light navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none m-0 p-0">
        <ul class="navbar-nav nav-justified w-100 align-items-center m-0 p-0">
            <li class="nav-item">
                <a class="nav-link pb-2 active text-center" href="https://dikit.my.id/dashboard">
                    <i class="fas fa-home"></i>
                    <span class="d-block" style="font-size: 0.7rem;">Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link pb-2  text-center" href="https://dikit.my.id/profile">
                    <i class="fa-solid fa-address-card"></i>
                    <span class="d-block" style="font-size: 0.7rem;">Profil</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="container mt-3 mb-5">
        <div class="d-flex d-none d-sm-flex justify-content-between align-items-center mt-4 mb-5">
            <h3 class="d-none d-sm-block fw-bold m-0 p-0">Dikit<i class="fa-solid fa-link mx-2"></i>Link</h3>
            <h4 class="m-0 p-0 text-truncate">Haii, Dewana Kretarta L</h4>
        </div>
        <div class="row">
            <div class="col-md-3 d-none d-md-block">
                <div class="pe-4">
                    <ul class="list-group">
                        <a class="list-group-item list-menu active disabled dropdown-item fw-semibold my-1 rounded-3 border-0" href="https://dikit.my.id/dashboard">
                            <i class="fas fa-home mx-2"></i>Home
                        </a>
                        <a class="list-group-item list-menu  dropdown-item fw-semibold my-1 rounded-3 border-0" href="https://dikit.my.id/list">
                            <i class="fas fa-list mx-2"></i>List
                        </a>
                        <hr class="my-2">
                        <a class="list-group-item list-menu danger dropdown-item fw-semibold my-1 rounded-3 border-0" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="fas fa-sign-out-alt mx-2"></i>Logout
                        </a>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-block d-sm-none card-body rounded-3 p-2 mb-3" style="background-color: var(--bs-gray-200)">
                    <p class="fw-semibold text-dark m-1"><i class="fa-solid fa-home mx-2"></i>Halaman utama</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="card-body rounded-3 shadow p-3 border-0" style="background: #8D9EFF;">
                            <div class="row align-items-center text-light">
                                <div class="col">
                                    <h6 class="fw-bold">Link saat ini</h6>
                                    <div class="h5 mb-0 fw-bold">13</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-link fa-2x me-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card-body rounded-3 shadow p-3 border-0" style="background: #8D72E1;">
                            <div class="row align-items-center text-light">
                                <div class="col">
                                    <h6 class="fw-bold">Pengunjung unik</h6>
                                    <div class="h5 mb-0 fw-bold">8058</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-fingerprint fa-2x me-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card-body rounded-3 shadow p-3 border-0" style="background: #6C4AB6;">
                            <div class="row align-items-center text-light">
                                <div class="col">
                                    <h6 class="fw-bold">Klik semua link</h6>
                                    <div class="h5 mb-0 fw-bold">11163</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-chart-simple fa-2x me-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
