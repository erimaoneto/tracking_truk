<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PT. Erickman Sarana Abadi</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="brand">
                <div class="brand-icon">E</div>
                <h1>PT. Erickman</h1>
                <p>Sistem Pelacakan Truk Gas Terintegrasi</p>
            </div>

            @if($errors->any())
                <div class="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@erickman.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>
        </div>
    </div>

</body>
</html>
