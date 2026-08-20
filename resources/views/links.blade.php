<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K'enzza Professional | Links Oficiais</title>
    <link rel="icon" type="image/png" href="logo-kenzza-bco.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <style>
        :root {
            --primary-bg: #0a0a0a;
            --accent-gold: #c5a059; /* Tom dourado sutil para autoridade */
            --white: #ffffff;
            --gray: #1a1a1a;
        }

        body {
            background-color: var(--primary-bg);
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('../bg_header.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--white);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .profile-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-k {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            margin-bottom: 15px;
            border: 2px solid var(--accent-gold);
            padding: 5px;
            background: #000;
        }

        h1 {
            font-size: 1.2rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        p {
            font-size: 0.9rem;
            color: #aaa;
            margin-bottom: 20px;
        }

        .links-wrapper {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .link-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .link-card:hover {
            background: var(--white);
            color: #000;
            transform: translateY(-3px);
        }

        .highlight {
            border: 1px solid var(--accent-gold);
            background: rgba(197, 160, 89, 0.1);
        }

        .footer-text {
            margin-top: auto;
            padding-top: 40px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <img src="{{ asset('logo-k-bco.png') }}" alt="K'enzza" class="logo-k">
        <h1>K'enzza Professional</h1>
        <p>Alta Performance para Salões e Home Care</p>
    </div>

    <div class="links-wrapper">
        <a href="/seja-um-distribuidor"
           class="link-card highlight" target="_blank">
            🤝 SEJA UM DISTRIBUIDOR (Fale Conosco)
        </a>

        <a href="https://wa.me/5511937525151?text=Quero%20comprar%20produtos%20Kenzza"
           class="link-card" target="_blank">
            🛍️ COMPRE ONLINE (WhatsApp Vendas)
        </a>

        <!--<a href="#" class="link-card" target="_blank">
            📖 CATÁLOGO TÉCNICO 2026
        </a>-->

        <a href="https://instagram.com/kenzzabr" class="link-card" target="_blank">
            📸 INSTAGRAM OFICIAL
        </a>
    </div>

    <div class="footer-text">
        KENZZA HAIR © 2026
    </div>

</body>
</html>
