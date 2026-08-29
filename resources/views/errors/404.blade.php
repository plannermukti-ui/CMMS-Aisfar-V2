<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <title>404 - Halaman Tidak Ditemukan | CMMS</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <link href="/assets/metronic/plugins/global/plugins.bundle.css" rel="stylesheet"/>
    <link href="/assets/metronic/css/style.bundle.css" rel="stylesheet"/>
    <style>
        body { background: linear-gradient(135deg, #f8fafc 0%, #e8f0fe 100%); min-height: 100vh; font-family: 'Inter', sans-serif; }
        .error-card { max-width: 600px; }
        .error-icon-wrap { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #1a73e8, #0d47a1); display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 60px rgba(26,115,232,.3); animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .error-code { font-size: 8rem; font-weight: 900; background: linear-gradient(135deg,#1a73e8,#0d47a1); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1; letter-spacing:-4px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="error-card text-center px-4">
        <div class="error-icon-wrap mx-auto mb-6">
            <i class="ki-outline ki-search-list" style="font-size:3.5rem;color:#fff"></i>
        </div>
        <div class="error-code mb-2">404</div>
        <h1 class="fs-2x fw-bolder text-gray-900 mb-3">Halaman Tidak Ditemukan</h1>
        <p class="text-muted fs-6 mb-8 mx-auto" style="max-width:440px">
            Halaman yang Anda cari tidak ada, mungkin sudah dipindahkan atau URL yang Anda masukkan salah.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="javascript:history.back()" class="btn btn-light fw-semibold">
                <i class="ki-outline ki-arrow-left fs-4 me-1"></i> Kembali
            </a>
            <a href="/plt/dashboard" class="btn btn-primary fw-bold">
                <i class="ki-outline ki-home fs-4 me-1"></i> Dashboard
            </a>
        </div>
        <div class="mt-10 text-muted fs-9">Error Code: <code class="text-primary">404 Not Found</code></div>
    </div>
</body>
</html>
