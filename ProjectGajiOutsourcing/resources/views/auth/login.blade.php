<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semen Padang Diklat - Login</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #eef2f6;
            /* Light gray background like screenshot */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            flex-direction: column;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: none;
        }

        .login-title {
            text-align: center;
            font-weight: 700;
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .login-subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #444;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #c0392b;
            /* Red focus border */
            background-color: white;
        }

        .btn-login {
            background-color: #c0392b;
            /* Semen Padang Red */
            border-color: #c0392b;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
        }

        .btn-login:hover {
            background-color: #a93226;
            border-color: #a93226;
        }

        .copyright {
            margin-top: 30px;
            color: #999;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h1 class="login-title">Outsourching PT Semen Padang</h1>
        <p class="login-subtitle">Silakan login untuk masuk ke sistem</p>

        @if ($errors->any())
            <div class="alert alert-danger"
                style="font-size: 14px; padding: 12px 15px; border-radius: 8px; border-left: 4px solid #dc3545; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div style="display: flex; align-items: flex-start;">
                        <span style="margin-right: 8px;">⚠️</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email"
                    value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-login">Masuk Dashboard</button>
        </form>
    </div>

</body>

</html>