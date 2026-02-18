<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semen Padang - Login</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border: 1px solid #e2e8f0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-img {
            height: 60px;
            margin-bottom: 1rem;
        }

        .app-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .app-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        .form-floating > .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .form-floating > .form-control:focus {
            border-color: #0b5ed7;
            box-shadow: 0 0 0 4px rgba(11, 94, 215, 0.1);
        }

        .btn-login {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background-color: #0a58ca;
            border-color: #0a58ca;
            transform: translateY(-1px);
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .copyright {
            text-align: center;
            margin-top: 2rem;
            color: #94a3b8;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('assets/img/sp-logo.png') }}" alt="Semen Padang Logo" class="logo-img">
            <h1 class="app-title">Sistem Outsourcing</h1>
            <p class="app-subtitle">Silakan login untuk melanjutkan</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                <label for="email">Email</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                <label class="form-check-label text-secondary small" for="rememberMe">
                    Ingat Saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-login">
                Masuk
            </button>
        </form>

        <div class="copyright">
            &copy; {{ date('Y') }} PT Semen Padang. All rights reserved.
        </div>
    </div>

</body>

</html>