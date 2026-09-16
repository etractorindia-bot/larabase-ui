<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Kids Fashion Store' }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #fffaf5;
            --surface: #ffffff;
            --surface-alt: #fff1f2;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #7c3aed;
            --primary-soft: #ede9fe;
            --accent: #fb7185;
            --success: #15803d;
            --border: #f1d5db;
            --shadow: 0 12px 30px rgba(124, 58, 237, 0.08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #fff7ed 0%, var(--bg) 100%);
            color: var(--text);
        }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1200px, calc(100% - 2rem)); margin: 0 auto; }
        .header {
            position: sticky; top: 0; z-index: 20;
            backdrop-filter: blur(12px);
            background: rgba(255, 250, 245, 0.92);
            border-bottom: 1px solid rgba(241, 213, 219, 0.85);
        }
        .header-inner {
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            padding: 1rem 0;
        }
        .brand { font-weight: 800; font-size: 1.15rem; }
        .brand small { display: block; font-weight: 500; color: var(--muted); }
        .pill-row { display: flex; gap: .75rem; flex-wrap: wrap; }
        .pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .65rem .95rem; border-radius: 999px;
            background: var(--surface); border: 1px solid var(--border); box-shadow: var(--shadow);
            font-size: .92rem;
        }
        .content { padding: 2rem 0 3rem; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }
        .button, button {
            cursor: pointer;
            border: 0;
            border-radius: 999px;
            padding: .9rem 1.1rem;
            font-weight: 700;
            background: var(--primary);
            color: white;
        }
        .button.secondary, button.secondary {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }
        .button.accent, button.accent { background: var(--accent); }
        .button.full, button.full { width: 100%; }
        .muted { color: var(--muted); }
        .grid { display: grid; gap: 1.25rem; }
        .flash {
            margin-bottom: 1rem; padding: .95rem 1rem; border-radius: 16px;
            background: #ecfdf5; color: var(--success); border: 1px solid #bbf7d0;
        }
        .errors {
            margin-bottom: 1rem; padding: 1rem 1.1rem; border-radius: 16px;
            background: #fff1f2; color: #be123c; border: 1px solid #fecdd3;
        }
        .section-title { display: flex; justify-content: space-between; align-items: end; gap: 1rem; margin-bottom: 1rem; }
        .section-title h1, .section-title h2, .section-title h3 { margin: 0; }
        .price { font-weight: 800; font-size: 1.15rem; }
        .price del { color: var(--muted); font-size: .9rem; margin-left: .45rem; }
        .badge {
            display: inline-flex; align-items: center; padding: .35rem .65rem; border-radius: 999px;
            background: var(--primary-soft); color: var(--primary); font-size: .75rem; font-weight: 700;
        }
        .field { display: grid; gap: .4rem; }
        .field input, .field select, .field textarea {
            width: 100%; padding: .85rem .95rem; border-radius: 14px; border: 1px solid var(--border);
            background: #fff; font: inherit;
        }
        .summary-row { display: flex; justify-content: space-between; gap: 1rem; }
        @media (max-width: 900px) {
            .header-inner, .section-title, .summary-row { align-items: flex-start; flex-direction: column; }
        }
    </style>
    @stack('head')
</head>
<body>
<header class="header">
    <div class="container header-inner">
        <a href="{{ route('store.index') }}" class="brand">
            Larabase Kids Store
            <small>11 aisles · age 0–9 · Razorpay + COD checkout</small>
        </a>
        <div class="pill-row">
            <span class="pill">❤️ Wishlist: {{ $wishlistCount ?? 0 }}</span>
            <span class="pill">🛍️ Bag: {{ $bagCount ?? 0 }}</span>
            <span class="pill">🚚 Free delivery over ₹{{ number_format(config('storefront.free_delivery_threshold'), 0) }}</span>
        </div>
    </div>
</header>
<main class="content">
    <div class="container">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
@stack('scripts')
</body>
</html>
