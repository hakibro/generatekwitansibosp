<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Generator Kuitansi BOSP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --ink: #eef4ff;
            --panel: #1d2a3d;
            --line: #334155;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: #0f172a;
            display: grid;
            place-items: center;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 28px 70px rgba(2, 6, 23, .32);
        }

        .mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #4f46e5;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 22px;
            margin: 0 auto 20px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #0f172a;
            color: var(--ink);
            outline: none;
            transition: border-color .2s;
        }

        input:focus {
            border-color: #6366f1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #94a3b8;
        }

        .field {
            margin-bottom: 18px;
        }

        button {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            background: #4f46e5;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }

        button:hover {
            background: #4338ca;
        }

        .error {
            background: rgba(251, 113, 133, .12);
            border: 1px solid rgba(251, 113, 133, .3);
            color: #fb7185;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #94a3b8;
        }

        .check input {
            width: auto;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="mark">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <h2 style="text-align:center;margin:0 0 28px;font-size:20px;font-weight:700;">Login</h2>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="field">
                <label class="check">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>
            </div>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>

</html>
