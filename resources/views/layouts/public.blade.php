<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Verifikasi Dokumen')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 700px;
            width: 100%;
        }
        
        .verification-header {
            border-radius: 15px 15px 0 0;
            padding: 30px;
            text-align: center;
        }
        
        .verification-body {
            padding: 30px;
        }
        
        .info-table {
            margin-bottom: 0;
        }
        
        .info-table th {
            width: 40%;
            padding: 12px;
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .info-table td {
            padding: 12px;
        }
        
        .download-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-top: 25px;
        }
        
        .btn-download {
            padding: 12px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
        }
        
        .verification-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .footer-text {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @yield('content')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
