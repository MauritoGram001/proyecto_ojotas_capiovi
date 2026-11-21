<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar — Ojotas Capioví</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 420px; }
    </style>
</head>
<body>
    <main class="container">
        <div class="card login-card mx-auto border-0 shadow-sm p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-box-seam-fill text-warning fs-1"></i>
                <h1 class="h4 fw-bold mb-1">Ojotas Capioví</h1>
                <p class="text-muted small mb-0">Acceso interno de gestión</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success py-2" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="username"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                    <input id="password" name="password" type="password"
                        autocomplete="current-password" class="form-control" required>
                </div>

                <div class="form-check mb-3">
                    <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
                    <label for="remember" class="form-check-label small">Mantener la sesión iniciada</label>
                </div>

                <button type="submit" class="btn btn-primary btn-touch w-100 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                </button>
            </form>

            <p class="text-muted small text-center mt-3 mb-0">
                Las credenciales son administradas por el dueño del negocio.
            </p>
        </div>
    </main>
</body>
</html>
