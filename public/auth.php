<?php
require_once __DIR__ . '/../api/init.php';
use App\Core\Auth;

if (Auth::isLoggedIn()) {
    header('Location: /public/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Creator | Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #07111f;
            --bg-2: #0d1a2d;
            --panel: rgba(16, 25, 38, 0.9);
            --panel-strong: #101c2c;
            --line: rgba(148, 163, 184, 0.18);
            --text: #edf5ff;
            --muted: #a4b7d3;
            --primary: #67e8f9;
            --primary-2: #8b5cf6;
            --success: #4ade80;
            --shadow: 0 30px 60px rgba(4, 10, 18, 0.5);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(103, 232, 249, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(139, 92, 246, 0.18), transparent 34%),
                linear-gradient(180deg, var(--bg), var(--bg-2));
        }

        a { color: inherit; text-decoration: none; }
        button, input { font: inherit; }

        .page-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }

        .auth-card {
            width: min(100%, 1040px);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: rgba(11, 18, 28, 0.8);
            box-shadow: var(--shadow);
        }

        .welcome-panel {
            padding: clamp(28px, 4vw, 52px);
            background: linear-gradient(135deg, rgba(103,232,249,0.10), rgba(139,92,246,0.12));
            border-right: 1px solid var(--line);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            font-weight: 700;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            color: #06141e;
            font-weight: 800;
        }

        .welcome-panel h1 {
            margin: 0 0 14px;
            font-size: clamp(2.2rem, 4vw, 3.6rem);
            letter-spacing: -0.06em;
            line-height: 0.96;
        }

        .welcome-panel p {
            margin: 0;
            color: var(--muted);
            font-size: 1.06rem;
            line-height: 1.7;
            max-width: 450px;
        }

        .feature-list {
            display: grid;
            gap: 14px;
            margin-top: 34px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text);
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            padding: 12px 14px;
        }

        .feature-item .bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 20px rgba(74, 222, 128, 0.7);
        }

        .auth-panel {
            padding: clamp(24px, 4vw, 42px);
            background: rgba(15, 22, 32, 0.8);
        }

        .auth-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 8px;
        }

        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--muted);
            cursor: pointer;
            border: none;
            background: transparent;
            font-weight: 600;
        }

        .auth-tab.active {
            background: rgba(103,232,249,0.08);
            color: var(--text);
            border: 1px solid rgba(103,232,249,0.18);
        }

        .auth-form {
            display: none;
            gap: 16px;
        }

        .auth-form.active {
            display: grid;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            color: var(--muted);
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .field input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.02);
            color: var(--text);
            outline: none;
        }

        .field input:focus {
            border-color: rgba(103,232,249,0.45);
            box-shadow: 0 0 0 3px rgba(103,232,249,0.12);
        }

        .submit-btn {
            width: 100%;
            padding: 15px 18px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #9ae6ff);
            color: #06141e;
            font-weight: 800;
            cursor: pointer;
            margin-top: 6px;
        }

        .secondary-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: var(--muted);
        }

        .auth-status {
            min-height: 20px;
            color: var(--primary);
            font-size: 0.92rem;
            margin-top: 8px;
        }

        @media (max-width: 820px) {
            .auth-card {
                grid-template-columns: 1fr;
            }

            .welcome-panel {
                border-right: none;
                border-bottom: 1px solid var(--line);
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="auth-card">
            <div class="welcome-panel">
                <div class="brand">
                    <span class="brand-mark">WC</span>
                    <span>Website Creator</span>
                </div>

                <h1>Design faster. Ship smarter.</h1>
                <p>Create responsive websites visually with a structured editor, live layout controls, and a publishing workflow built for real product teams.</p>

                <div class="feature-list">
                    <div class="feature-item"><span class="bullet"></span>Visual page builder and block library</div>
                    <div class="feature-item"><span class="bullet"></span>Responsive preview across breakpoints</div>
                    <div class="feature-item"><span class="bullet"></span>Fast project dashboard and publishing flow</div>
                </div>
            </div>

            <div class="auth-panel">
                <div class="auth-tabs">
                    <button class="auth-tab active" type="button" data-mode="login">Login</button>
                    <button class="auth-tab" type="button" data-mode="register">Register</button>
                </div>

                <form id="login-form" class="auth-form active" method="post">
                    <div class="field">
                        <label for="login-email">Email</label>
                        <input id="login-email" name="email" type="email" required placeholder="you@example.com">
                    </div>
                    <div class="field">
                        <label for="login-password">Password</label>
                        <input id="login-password" name="password" type="password" required placeholder="••••••••">
                    </div>
                    <button class="submit-btn" type="submit">Log in</button>
                </form>

                <form id="register-form" class="auth-form" method="post">
                    <div class="field">
                        <label for="register-email">Email</label>
                        <input id="register-email" name="email" type="email" required placeholder="you@example.com">
                    </div>
                    <div class="field">
                        <label for="register-password">Password</label>
                        <input id="register-password" name="password" type="password" required placeholder="Create a strong password">
                    </div>
                    <button class="submit-btn" type="submit">Create account</button>
                </form>

                <div id="auth-status" class="auth-status" aria-live="polite"></div>
                <a class="secondary-link" href="/public/index.php">Back to home</a>
            </div>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.auth-tab');
        const forms = {
            login: document.getElementById('login-form'),
            register: document.getElementById('register-form')
        };
        const statusEl = document.getElementById('auth-status');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const mode = tab.dataset.mode;
                tabs.forEach(item => item.classList.toggle('active', item === tab));
                Object.entries(forms).forEach(([key, form]) => form.classList.toggle('active', key === mode));
                statusEl.textContent = '';
            });
        });

        async function submitForm(mode, payload) {
            statusEl.textContent = 'Please wait...';

            const response = await fetch(`/api/auth/${mode}.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (response.ok && data.success) {
                statusEl.textContent = mode === 'login' ? 'Login successful. Redirecting...' : 'Account created successfully. Redirecting...';
                setTimeout(() => {
                    window.location.href = '/public/dashboard.php';
                }, 700);
                return;
            }

            statusEl.textContent = data.error || 'Something went wrong.';
        }

        forms.login.addEventListener('submit', async (event) => {
            event.preventDefault();
            await submitForm('login', {
                email: document.getElementById('login-email').value.trim(),
                password: document.getElementById('login-password').value
            });
        });

        forms.register.addEventListener('submit', async (event) => {
            event.preventDefault();
            await submitForm('register', {
                email: document.getElementById('register-email').value.trim(),
                password: document.getElementById('register-password').value
            });
        });
    </script>
</body>
</html>
