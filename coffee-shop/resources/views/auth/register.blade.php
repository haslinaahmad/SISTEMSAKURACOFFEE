<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sakura Coffee POS</title>
    
    {{-- Font & Icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            
            /* Background Coffee Shop Estetik (Sama dengan Login) */
            background: 
                linear-gradient(rgba(40, 25, 20, 0.7), rgba(40, 25, 20, 0.8)), 
                url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=2671&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Agar background diam saat scroll */
            padding: 20px;
        }

        .login-card {
            /* Warna Creamy Latte Glassmorphism */
            background: rgba(255, 250, 240, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 480px; /* Sedikit lebih lebar dari login agar muat nama panjang */
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .brand-icon {
            width: 70px;
            height: 70px;
            /* Gradasi Coklat */
            background: linear-gradient(135deg, #6F4E37, #4A3321);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFF8E1; 
            font-size: 28px;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(74, 51, 33, 0.3);
        }

        h2 {
            color: #3E2723; 
            margin-bottom: 5px;
            font-weight: 700;
        }

        p.subtitle {
            color: #795548; 
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-group { margin-bottom: 20px; text-align: left; }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #4E342E;
            font-size: 0.9rem;
        }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8D6E63;
        }

        .form-control {
            background: #FFFFFF;
            border: 1px solid #D7CCC8;
            padding: 12px 12px 12px 45px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s;
            font-size: 1rem;
            color: #3E2723;
        }

        .form-control:focus {
            border-color: #6F4E37;
            box-shadow: 0 0 0 4px rgba(111, 78, 55, 0.15);
            outline: none;
        }

        /* Tombol Gradasi Coklat */
        .btn-login {
            background: linear-gradient(135deg, #8D6E63 0%, #5D4037 100%);
            color: #FFF8E1;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(93, 64, 55, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(93, 64, 55, 0.4);
            filter: brightness(1.1);
        }

        .invalid-feedback { 
            color: #D32F2F; 
            font-size: 0.85rem; 
            margin-top: 5px; 
            display: block; 
        }
        
        /* Link Login */
        .auth-link { 
            color: #6F4E37; 
            text-decoration: none; 
            font-weight: 700; 
        }
        .auth-link:hover { text-decoration: underline; color: #3E2723; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Bergabung dengan tim Sakura Coffee</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            {{-- Nama Lengkap --}}
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="form-control" placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
                </div>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}" required>
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                Daftar Sekarang <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #D7CCC8;">
            <p style="color: #5D4037; font-size: 0.95rem;">
                Sudah punya akun? <a href="{{ route('login') }}" class="auth-link">Login disini</a>
            </p>
        </div>
    </div>

</body>
</html>