<?php
require_once __DIR__ . "/../app/bootstrap.php";
use App\Core\Auth;

if (!Auth::isLoggedIn()) {
    header('Location: /public/auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Creator | Page Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#07111f; --bg-2:#0d1a2d; --panel:rgba(15,22,32,.92); --line:rgba(148,163,184,.18); --text:#edf5ff; --muted:#a4b7d3; --primary:#67e8f9; --primary-2:#8b5cf6; --success:#4ade80; --shadow:0 30px 60px rgba(4,10,18,.45);
        }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; font-family:'Outfit',sans-serif; background:linear-gradient(180deg,var(--bg),var(--bg-2)); color:var(--text); }
        a { color:inherit; text-decoration:none; }
        button, input { font:inherit; }
        .container { width:min(1180px, calc(100% - 32px)); margin:0 auto; }
        .topbar { position:sticky; top:0; background:rgba(7,17,31,.8); border-bottom:1px solid var(--line); backdrop-filter:blur(16px); }
        .topbar-inner { min-height:78px; display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .brand { display:inline-flex; align-items:center; gap:12px; font-weight:700; }
        .brand-mark { width:34px;height:34px;border-radius:12px; display:grid; place-items:center; background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; font-weight:800; }
        .toolbar { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .button { display:inline-flex; align-items:center; justify-content:center; padding:11px 18px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.02); color:var(--text); }
        .button.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; border:none; font-weight:700; }
        .shell { padding:34px 0 80px; }
        .page-header { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:22px; }
        .page-header h1 { margin:0; font-size:clamp(2.1rem,3vw,3.1rem); letter-spacing:-0.05em; }
        .page-header p { margin:10px 0 0; color:var(--muted); }
        .layout { display:grid; grid-template-columns: 340px minmax(0,1fr); gap:24px; }
        .panel { background:rgba(15,22,32,.82); border:1px solid var(--line); border-radius:24px; box-shadow:var(--shadow); }
        .sidebar { padding:22px; }
        .sidebar h3 { margin:0 0 18px; }
        .page-list { display:grid; gap:12px; }
        .page-item { padding:14px 12px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.02); color:var(--muted); }
        .page-item.active { background:rgba(103,232,249,.08); border-color:rgba(103,232,249,.2); color:var(--text); }
        .main-panel { padding:22px; }
        .page-meta { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:18px; }
        .badge { display:inline-flex; border-radius:999px; padding:6px 10px; font-size:.72rem; letter-spacing:.05em; text-transform:uppercase; background:rgba(103,232,249,.08); border:1px solid rgba(103,232,249,.2); color:var(--primary); }
        .settings-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
        .field { display:grid; gap:8px; }
        .field label { color:var(--muted); font-size:.8rem; letter-spacing:.06em; text-transform:uppercase; }
        .field input, .field select { width:100%; padding:12px 14px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.02); color:var(--text); }
        .summary-box { padding:18px; border:1px solid var(--line); border-radius:16px; background:rgba(255,255,255,.02); margin-top:20px; }
        .summary-box h4 { margin:0 0 12px; }
        .summary-box ul { margin:0; padding-left:18px; color:var(--muted); line-height:1.8; }
        .actions-row { display:flex; gap:10px; margin-top:22px; }
        .small-btn { flex:1; padding:12px 14px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.02); color:var(--text); }
        .small-btn.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; border:none; font-weight:700; }
        @media (max-width: 900px) {
            .layout, .settings-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div class="brand">
                <span class="brand-mark">WC</span>
                <span>Page Manager</span>
            </div>
            <div class="toolbar">
                <a class="button" href="/public/dashboard.php">Dashboard</a>
                <a class="button primary" href="/public/editor.php?id=1">New page</a>
            </div>
        </div>
    </header>

    <main class="container shell">
        <div class="page-header">
            <div>
                <h1>Pages and layout structure</h1>
                <p>Organize documents, page settings, and page-level publishing choices from one place.</p>
            </div>
        </div>

        <div class="layout">
            <aside class="panel sidebar">
                <h3>Pages</h3>
                <div class="page-list">
                    <div class="page-item active">Home</div>
                    <div class="page-item">Services</div>
                    <div class="page-item">About</div>
                    <div class="page-item">Contact</div>
                </div>
            </aside>

            <section class="panel main-panel">
                <div class="page-meta">
                    <h2>Home</h2>
                    <span class="badge">Draft</span>
                </div>

                <div class="settings-grid">
                    <div class="field">
                        <label>Page title</label>
                        <input type="text" value="Home" />
                    </div>
                    <div class="field">
                        <label>Slug</label>
                        <input type="text" value="/" />
                    </div>
                    <div class="field">
                        <label>Template</label>
                        <select>
                            <option>Launch page</option>
                            <option>Portfolio</option>
                            <option>Business site</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select>
                            <option>Draft</option>
                            <option>Published</option>
                            <option>Scheduled</option>
                        </select>
                    </div>
                </div>

                <div class="summary-box">
                    <h4>Page summary</h4>
                    <ul>
                        <li>Structured content model in sync with editor</li>
                        <li>Responsive sections ready for desktop and mobile</li>
                        <li>SEO fields and publishing metadata available</li>
                    </ul>
                </div>

                <div class="actions-row">
                    <button class="small-btn primary" type="button">Save changes</button>
                    <button class="small-btn" type="button">Open in editor</button>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
