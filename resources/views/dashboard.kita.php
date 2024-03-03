<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Ulems</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.css" integrity="sha256-WAgYcAck1C1/zEl5sBl5cfyhxtLgKGdpI3oKyJffVRI=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha256-PI8n5gCcz9cQqQXm3PEtDuPG8qx9oFsFctPg0S5zb8g=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" integrity="sha256-wiz7ZSCn/btzhjKDQBms9Hx4sSeUYsDrTLg7roPstac=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap">
    <style>
        html {
            scroll-behavior: smooth !important;
        }

        body {
            font-family: 'Josefin Sans', sans-serif !important;
            padding: 0 !important;
        }

        .text-gelap {
            color: var(--bs-dark);
            background-color: var(--bs-gray-100);
        }

        .text-terang {
            color: var(--bs-light);
            background-color: var(--bs-gray-800);
        }

        .text-terang:hover {
            color: var(--bs-light);
            background-color: var(--bs-gray-600);
        }

        .text-gelap:hover {
            color: var(--bs-dark);
            background-color: var(--bs-gray-300);
        }

        .text-gelap:focus {
            color: var(--bs-dark);
        }

        .text-terang:focus {
            color: var(--bs-light);
        }

        .logout:hover {
            color: var(--bs-light) !important;
            background-color: var(--bs-danger) !important;
        }

        .loading-page {
            position: fixed;
            inset: 0 !important;
            width: 100%;
            height: 100%;
            z-index: 1056 !important;
        }
    </style>
    <script>
        const THEME_DARK = 'dark';
        const THEME_LIGHT = 'light';
        const THEME_TABLE = 'theme';
        const THEME_BS_DATA = 'data-bs-theme';

        if (localStorage.getItem(THEME_TABLE) === THEME_DARK) {
            document.documentElement.setAttribute(THEME_BS_DATA, THEME_DARK);
        } else {
            localStorage.setItem(THEME_TABLE, THEME_LIGHT);
        }
    </script>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark navbar-expand fixed-bottom rounded-top-3 border-top border-2 border-light d-md-none d-lg-none d-xl-none m-0 p-0">
        <ul class="navbar-nav nav-justified w-100 align-items-center m-0 p-0">
            <li class="nav-item">
                <button class="nav-link pb-2 w-100 text-center active" onclick="((btn) => {
                    bootstrap.Tab.getOrCreateInstance(document.getElementById('button-home')).show();
                    btn.classList.add('active');
                    document.getElementById('button-mobile-setting').classList.remove('active');
                })(this)" id="button-mobile-home">
                    <i class="fas fa-home"></i>
                    <span class="d-block" style="font-size: 0.8rem;">Home</span>
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link pb-2 w-100 text-center" onclick="((btn) => {
                    bootstrap.Tab.getOrCreateInstance(document.getElementById('button-setting')).show();
                    btn.classList.add('active');
                    document.getElementById('button-mobile-home').classList.remove('active');
                })(this)" id="button-mobile-setting">
                    <i class="fa-solid fa-gear"></i>
                    <span class="d-block" style="font-size: 0.8rem;">Setting</span>
                </button>
            </li>
        </ul>
    </nav>

    <main class="container mt-3 mb-5">

        <div class="d-flex d-none d-sm-flex justify-content-between align-items-center mt-4 mb-5">
            <h3 class="d-none d-sm-block fw-bold m-0 p-0">Undangan <i class="fa-solid fa-fire text-danger"></i></h3>
            <h4 class="m-0 p-0 text-truncate">Mimin <i class="fa-solid fa-hands text-warning"></i></h4>
        </div>

        <div class="row">

            <div class="col-md-3 d-none d-md-block">
                <div class="pe-3">
                    <ul class="nav flex-column w-100 nav-pills" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="text-gelap nav-link w-100 text-start fw-semibold mb-1 rounded-3 active" data-bs-toggle="pill" data-bs-target="#pills-home" id="button-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                                <i class="fas fa-home ms-3 me-2"></i>Home
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="text-gelap nav-link w-100 text-start fw-semibold my-1 rounded-3" data-bs-toggle="pill" data-bs-target="#pills-setting" id="button-setting" type="button" role="tab" aria-controls="pills-setting" aria-selected="false">
                                <i class="fas fa-gear ms-3 me-2"></i>Setting
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <hr class="my-2">
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="text-gelap logout nav-link w-100 text-start fw-semibold mt-1 rounded-3" data-bs-toggle="modal" data-bs-target="#logoutModal" id="button-logout">
                                <i class="fas fa-sign-out-alt ms-3 me-2"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" tabindex="0">

                        <div class="card-body rounded-3 p-2 mb-4" style="background-color: var(--bs-gray-200)">
                            <p class="fw-semibold text-dark m-1"><i class="fa-solid fa-home mx-2"></i>Home</p>
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

                        <hr>

                        <h4>Content</h4>

                    </div>
                    <div class="tab-pane fade" id="pills-setting" role="tabpanel" tabindex="0">

                        <div class="card-body rounded-3 p-2 mb-4" style="background-color: var(--bs-gray-200)">
                            <p class="fw-semibold text-dark m-1"><i class="fa-solid fa-gear mx-2"></i>Setting</p>
                        </div>

                        <p>Email : dewanakretarta29@gmail.com</p>
                        <p>Api Key : ************************</p>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="darkMode" onchange="(() => {
                                if (localStorage.getItem(THEME_TABLE) === THEME_DARK) {
                                    document.documentElement.setAttribute(THEME_BS_DATA, THEME_LIGHT);
                                    localStorage.setItem(THEME_TABLE, THEME_LIGHT);
                                    document.getElementById('button-home').classList.remove('text-terang');
                                    document.getElementById('button-setting').classList.remove('text-terang');
                                    document.getElementById('button-logout').classList.remove('text-terang');
                                    document.getElementById('button-home').classList.add('text-gelap');
                                    document.getElementById('button-setting').classList.add('text-gelap');
                                    document.getElementById('button-logout').classList.add('text-gelap');
                                } else {
                                    document.documentElement.setAttribute(THEME_BS_DATA, THEME_DARK);
                                    localStorage.setItem(THEME_TABLE, THEME_DARK);
                                    document.getElementById('button-home').classList.remove('text-gelap');
                                    document.getElementById('button-setting').classList.remove('text-gelap');
                                    document.getElementById('button-logout').classList.remove('text-gelap');
                                    document.getElementById('button-home').classList.add('text-terang');
                                    document.getElementById('button-setting').classList.add('text-terang');
                                    document.getElementById('button-logout').classList.add('text-terang');
                                }
                            })()">
                            <label class="form-check-label" for="darkMode">Dark Mode</label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="filterBadWord">
                            <label class="form-check-label" for="filterBadWord">Filter Bad Word</label>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="loading-page bg-white" id="loading" style="opacity: 1;">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh !important;">
            <div class="w-50">

                <div class="mb-3">
                    <label for="inputEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="inputEmail">
                </div>

                <div class="mb-3">
                    <label for="InputPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="InputPassword">
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-primary" onclick="opacity('loading')" onclick="(() => {
                        localStorage.getItem('token');
                    })()">Login</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout ?</h5>
                </div>
                <div class="modal-body">
                    <h5>Apakah anda ingin Logout ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="button-logout-batal"><i class="fas fa-times me-1"></i>Batal</button>
                    <button type="button" class="btn btn-danger" id="button-logout"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha256-CDOy6cOibCWEdsRiZuaHf8dSGGJRYuBGC+mjoJimHGw=" crossorigin="anonymous"></script>
    <script>
        if (document.documentElement.getAttribute(THEME_BS_DATA) === THEME_DARK) {
            document.getElementById('darkMode').checked = true;
            document.getElementById('button-home').classList.remove('text-gelap');
            document.getElementById('button-setting').classList.remove('text-gelap');
            document.getElementById('button-logout').classList.remove('text-gelap');
            document.getElementById('button-home').classList.add('text-terang');
            document.getElementById('button-setting').classList.add('text-terang');
            document.getElementById('button-logout').classList.add('text-terang');
            document.getElementById('loading').classList.remove('bg-white');
            document.getElementById('loading').classList.add('bg-dark');
        }

        const opacity = (nama) => {
            let nm = document.getElementById(nama);
            let op = parseInt(nm.style.opacity);
            let clear = null;

            clear = setInterval(() => {
                if (op >= 0) {
                    nm.style.opacity = op.toString();
                    op -= 0.025;
                } else {
                    clearInterval(clear);
                    clear = null;
                    nm.remove();
                    return;
                }
            }, 10);
        };
    </script>
</body>

</html>
