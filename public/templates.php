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
    <title>Website Creator Templates</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#07111f; --bg-2:#0d1a2d; --panel:rgba(15,22,32,.92); --line:rgba(148,163,184,.18); --text:#edf5ff; --muted:#a4b7d3; --primary:#67e8f9; --primary-2:#8b5cf6; --success:#4ade80; --warning:#fbbf24; --shadow:0 30px 60px rgba(4,10,18,.45);
        }
        * { box-sizing:border-box; }
        body { margin:0; font-family:'Outfit',sans-serif; background:linear-gradient(180deg,var(--bg),var(--bg-2)); color:var(--text); }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; }
        .container { width:min(1200px, calc(100% - 32px)); margin:0 auto; }
        .topbar { position:sticky; top:0; z-index:5; background:rgba(7,17,31,.8); border-bottom:1px solid var(--line); backdrop-filter:blur(16px); }
        .topbar-inner { display:flex; justify-content:space-between; align-items:center; min-height:78px; }
        .brand { display:inline-flex; align-items:center; gap:12px; font-weight:700; }
        .brand-mark { width:34px; height:34px; display:grid; place-items:center; border-radius:12px; background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; font-weight:800; }
        .toolbar { display:flex; align-items:center; gap:12px; }
        .button { display:inline-flex; align-items:center; justify-content:center; padding:11px 18px; border-radius:12px; background:rgba(255,255,255,.02); border:1px solid var(--line); color:var(--text); }
        .button.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; border:none; font-weight:700; }
        .shell { padding:38px 0 80px; }
        .page-header { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:22px; }
        .page-header h1 { margin:0; font-size:clamp(2rem,3vw,3.4rem); letter-spacing:-0.06em; }
        .page-header p { margin:10px 0 0; color:var(--muted); }
        .templates-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; }
        .template-card { overflow:hidden; background:rgba(15,22,32,.82); border:1px solid var(--line); border-radius:24px; box-shadow:var(--shadow); }
        .template-visual { height:220px; border-bottom:1px solid var(--line); }
        .template-info { padding:22px; }
        .template-tag { display:inline-flex; padding:6px 9px; border-radius:999px; letter-spacing:.06em; text-transform:uppercase; font-size:.7rem; background:rgba(74,222,128,.12); border:1px solid rgba(74,222,128,.18); color:#bef7c7; }
        .template-info h3 { margin:14px 0 8px; font-size:1.4rem; }
        .template-info p { margin:0; color:var(--muted); line-height:1.7; }
        .template-actions { display:flex; gap:10px; margin-top:20px; }
        .small-btn { flex:1; border-radius:10px; padding:10px 12px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text); }
        .small-btn.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; border:none; font-weight:700; }
        @media (max-width: 900px) {
            .templates-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div class="brand">
                <span class="brand-mark">WC</span>
                <span>Template Library</span>
            </div>
            <div class="toolbar">
                <a class="button" href="/public/dashboard.php">Dashboard</a>
                <a class="button primary" href="/public/editor.php?id=1">Create from scratch</a>
            </div>
        </div>
    </header>

    <main class="container shell">
        <div class="page-header">
            <div>
                <h1>Choose a starting point</h1>
                <p>Pick a template and customize it in the visual editor to suit your product or brand.</p>
            </div>
        </div>

        <div class="templates-grid">
            <article class="template-card">
                <div class="template-visual" style="background:linear-gradient(135deg, rgba(103,232,249,.18), rgba(139,92,246,.18));"></div>
                <div class="template-info">
                    <span class="template-tag">Marketing</span>
                    <h3>Launch page</h3>
                    <p>High-converting hero, features, and CTA flow for product launches and campaigns.</p>
                    <div class="template-actions">
                        <button class="small-btn primary" type="button">Use template</button>
                        <button class="small-btn" type="button">Preview</button>
                    </div>
                </div>
            </article>

            <article class="template-card">
                <div class="template-visual" style="background:linear-gradient(135deg, rgba(74, 222, 128, 0.18), rgba(103,232,249,0.18));"></div>
                <div class="template-info">
                    <span class="template-tag">Portfolio</span>
                    <h3>Creative portfolio</h3>
                    <p>Editorial layout for work, process, and client showcase with a premium look.</p>
                    <div class="template-actions">
                        <button class="small-btn primary" type="button">Use template</button>
                        <button class="small-btn" type="button">Preview</button>
                    </div>
                </div>
            </article>

            <article class="template-card">
                <div class="template-visual" style="background:linear-gradient(135deg, rgba(251,191,36,.16), rgba(139,92,246,.18));"></div>
                <div class="template-info">
                    <span class="template-tag">Agency</span>
                    <h3>Business site</h3>
                    <p>Presentation-ready template for agencies, consultants, and service businesses.</p>
                    <div class="template-actions">
                        <button class="small-btn primary" type="button">Use template</button>
                        <button class="small-btn" type="button">Preview</button>
                    </div>
                </div>
            </article>
        </div>
    </main>
</body>
</html>
