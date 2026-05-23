<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Generator Kuitansi BOSP')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#0f766e',
                        fresh: '#14b8a6',
                        ink: '#172033'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --ink: #eef4ff;
            --muted: #94a3b8;
            --line: #334155;
            --soft: #111c2f;
            --panel: #1d2a3d;
            --brand: #6366f1;
            --brand2: #60a5fa;
            --good: #22c55e;
            --bad: #fb7185;
            --warn: #f59e0b;
            --shadow: 0 28px 70px rgba(2, 6, 23, .32);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: #0f172a;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 14px;
            padding: 8px 16px;
        }

        .side {
            background: #1d2a3d;
            color: #eef4ff;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 28px;
            position: sticky;
            top: 8px;
            height: calc(100vh - 16px);
            border: 1px solid #334155;
            border-radius: 34px;
            box-shadow: var(--shadow);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 22px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
        }

        .mark {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: #4f46e5;
            color: #fff;
            display: grid;
            place-items: center;
            box-shadow: 0 16px 32px rgba(79, 70, 229, .28);
        }

        .brand b {
            display: block;
            font-size: 21px;
            font-weight: 900;
            line-height: 1;
        }

        .brand span {
            display: block;
            margin-top: 5px;
            color: #a5b4fc;
            font-size: 11px;
            font-weight: 800;
        }

        .nav {
            display: grid;
            gap: 14px;
        }

        .nav a {
            color: #94a3b8;
            border: 1px solid transparent;
            border-radius: 16px;
            padding: 15px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 900;
            font-size: 15px;
        }

        .nav a:hover,
        .nav a.active {
            background: #111827;
            border-color: #4f46e5;
            color: #a5b4fc;
            box-shadow: inset 0 0 0 1px rgba(99, 102, 241, .18);
        }

        .nav a i {
            font-size: 20px;
        }

        .note {
            margin-top: auto;
            border-top: 1px solid rgba(148, 163, 184, .16);
            padding: 20px 14px 0;
            color: #fb7185;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.45;
        }

        .main {
            min-width: 0;
            background: #1d2a3d;
            border: 1px solid #334155;
            border-radius: 34px;
            box-shadow: var(--shadow);
            overflow: auto;
            height: calc(100vh - 16px);
        }

        .top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 26px 34px 22px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
            margin-bottom: 0;
        }

        .page-content {
            padding: 24px 34px 34px;
        }

        h1 {
            margin: 0;
            font-size: 25px;
            letter-spacing: -.02em;
            font-weight: 900;
            color: #fff;
        }

        .hint {
            margin: 5px 0 0;
            color: #60a5fa;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 38px;
            border: 1px solid #334155;
            background: #182338;
            color: #e2e8f0;
            border-radius: 13px;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(2, 6, 23, .16);
            transition: .15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            border-color: #64748b;
        }

        .btn.primary {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .btn.primary:hover {
            background: #3b82f6;
        }

        .btn.good {
            background: #16a34a;
            color: #fff;
            border-color: #16a34a;
        }

        .btn.danger {
            color: var(--bad);
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        .grid.two {
            grid-template-columns: minmax(0, 1fr) minmax(320px, .7fr);
            align-items: start;
        }

        .grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel {
            background: #1d2a3d;
            border: 1px solid #334155;
            border-radius: 24px;
            box-shadow: none;
            overflow: hidden;
        }

        .panel-head {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .panel-head h2 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
        }

        .panel-body {
            padding: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .field {
            display: grid;
            gap: 6px;
        }

        .field.full {
            grid-column: 1/-1;
        }

        label {
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 800;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 10px 12px;
            background: #111827;
            color: #f8fafc;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 78px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(20, 108, 148, .14);
        }

        .stat {
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 18px;
            background: #172238;
        }

        .stat b {
            display: block;
            font-size: 21px;
        }

        .stat span {
            color: var(--muted);
            font-size: 12px;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid #334155;
            border-radius: 20px;
            background: #172238;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid #334155;
            padding: 10px;
            font-size: 12px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #111827;
            color: #cbd5e1;
            position: sticky;
            top: 0;
            z-index: 1;
            font-weight: 900;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 900;
            background: #273449;
            color: #cbd5e1;
        }

        .badge.good {
            background: #e8f7ef;
            color: #067647;
        }

        .badge.warn {
            background: #fff4e5;
            color: #b25e09;
        }

        .empty {
            border: 1px dashed #475569;
            border-radius: 18px;
            padding: 22px;
            color: var(--muted);
            background: #172238;
            text-align: center;
            font-size: 13px;
        }

        .alert {
            margin: 24px 42px 0;
            border: 1px solid #22c55e55;
            background: #052e1a;
            color: #86efac;
            border-radius: 16px;
            padding: 13px 15px;
            font-size: 13px;
            font-weight: 800;
        }

        .drop {
            border: 2px dashed #99f6e4;
            background: #f0fdfa;
            border-radius: 16px;
            padding: 24px;
            display: grid;
            justify-items: center;
            text-align: center;
            gap: 10px;
            min-height: 190px;
            cursor: pointer;
        }

        .drop.drag {
            border-color: var(--brand);
            background: #ccfbf1;
        }

        .drop input {
            display: none;
        }

        .drop strong {
            font-size: 16px;
        }

        .drop span {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
            max-width: 560px;
        }

        .progress {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e7edf3;
            overflow: hidden;
        }

        .progress span {
            display: block;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #0f766e, #14b8a6);
            transition: width .2s;
        }

        .fa-fw {
            width: 1.25em;
        }

        .receipt-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
            display: grid;
            gap: 8px;
            background: #fff;
        }

        .receipt-card h3 {
            margin: 0;
            font-size: 14px;
        }

        .receipt-card p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }

        @media (max-width:1100px) {
            .app {
                grid-template-columns: 1fr;
                padding: 10px
            }

            .side {
                height: auto;
                position: relative;
                border-radius: 28px
            }

            .main {
                height: auto;
                border-radius: 28px
            }

            .nav {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px
            }

            .nav a {
                font-size: 13px;
                padding: 12px
            }

            .note {
                display: none
            }

            .top {
                flex-direction: column;
                padding: 24px
            }

            .page-content {
                padding: 22px
            }

            .grid.two,
            .grid.three,
            .form-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
    @stack('head')
</head>

<body>
    <div class="app">
        <aside class="side">
            <div class="brand">
                <div class="mark"><i class="fa-solid fa-receipt"></i></div>
                <div><b>SIBUK BOS</b><span>Ya Sibuk Banget</span></div>
            </div>
            <nav class="nav">
                <a @class(['active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}"><i
                        class="fa-solid fa-house fa-fw"></i>Beranda Utama</a>
                <a @class(['active' => request()->routeIs('imports.*')]) href="{{ route('imports.create') }}"><i
                        class="fa-solid fa-upload fa-fw"></i>Upload BKU</a>
                <a @class(['active' => request()->routeIs('batches.*')]) href="{{ route('dashboard') }}"><i
                        class="fa-solid fa-print fa-fw"></i>Pusat Cetak</a>
                <a @class(['active' => request()->routeIs('settings.*')]) href="{{ route('settings.edit') }}"><i
                        class="fa-solid fa-gear fa-fw"></i>Setelan Akun</a>
            </nav>
            <div class="note"><i class="fa-solid fa-right-from-bracket"></i> Akhiri Sesi</div>
        </aside>
        <main class="main">
            <div class="top">
                <div>
                    <h1>@yield('page_title')</h1>
                    <p class="hint">@yield('page_hint')</p>
                </div>
                <div class="actions">@yield('actions')</div>
            </div>
            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif
            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
