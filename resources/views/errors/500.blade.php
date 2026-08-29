<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <title>500 - Kesalahan Server | CMMS</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <link href="/assets/metronic/plugins/global/plugins.bundle.css" rel="stylesheet"/>
    <link href="/assets/metronic/css/style.bundle.css" rel="stylesheet"/>
    <style>
        body { background: linear-gradient(135deg, #f8fafc 0%, #fce4ec 100%); min-height: 100vh; font-family: 'Inter', sans-serif; }
        .error-card { max-width: 600px; }
        .error-icon-wrap { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #f44336, #b71c1c); display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 60px rgba(244,67,54,.3); animation: shake 0.6s ease-in-out 0s 3; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 20%,60%{transform:translateX(-8px)} 40%,80%{transform:translateX(8px)} }
        .error-code { font-size: 8rem; font-weight: 900; background: linear-gradient(135deg,#f44336,#b71c1c); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1; letter-spacing:-4px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="error-card text-center px-4">
        <div class="error-icon-wrap mx-auto mb-6">
            <i class="ki-outline ki-warning-2" style="font-size:3.5rem;color:#fff"></i>
        </div>
        <div class="error-code mb-2">500</div>
        <h1 class="fs-2x fw-bolder text-gray-900 mb-3">Kesalahan Server</h1>
        <p class="text-muted fs-6 mb-8 mx-auto" style="max-width:440px">
            Terjadi kesalahan internal pada server. Tim teknis kami telah diberitahu dan sedang menangani masalah ini.
            Silakan coba beberapa saat lagi.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="javascript:location.reload()" class="btn btn-light-danger fw-semibold">
                <i class="ki-outline ki-arrows-circle fs-4 me-1"></i> Coba Lagi
            </a>
            <a href="/plt/dashboard" class="btn btn-danger fw-bold">
                <i class="ki-outline ki-home fs-4 me-1"></i> Dashboard
            </a>
        </div>
        <div class="mt-10 text-muted fs-9">Error Code: <code class="text-danger">500 Internal Server Error</code></div>
    </div>
</body>
</html>
