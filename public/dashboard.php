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
    <title>Website Creator Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#07111f; --bg-2:#0d1a2d; --panel:rgba(15,22,32,0.92); --line:rgba(148,163,184,.18); --text:#edf5ff; --muted:#a4b7d3; --primary:#67e8f9; --primary-2:#8b5cf6; --success:#4ade80; --warning:#fbbf24; --shadow:0 30px 60px rgba(4,10,18,.45);
        }
        * { box-sizing:border-box; }
        body {
            margin:0; min-height:100vh; font-family:'Outfit',sans-serif; background:linear-gradient(180deg,var(--bg),var(--bg-2)); color:var(--text);
        }
        a { text-decoration:none; color:inherit; }
        button { font:inherit; }
        .container { width:min(1200px, calc(100% - 32px)); margin:0 auto; }
        .topbar {
            position:sticky; top:0; z-index:5; background:rgba(7,17,31,.8); backdrop-filter:blur(16px); border-bottom:1px solid var(--line);
        }
        .topbar-inner { display:flex; justify-content:space-between; align-items:center; gap:16px; min-height:78px; }
        .brand { display:inline-flex; align-items:center; gap:12px; font-weight:700; }
        .brand-mark { width:34px; height:34px; display:grid; place-items:center; border-radius:12px; background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; font-weight:800; }
        .nav { display:flex; gap:22px; color:var(--muted); }
        .nav a:hover { color:var(--text); }
        .toolbar { display:flex; align-items:center; gap:12px; }
        .button { display:inline-flex; align-items:center; justify-content:center; padding:11px 18px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.02); color:var(--text); }
        .button.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; border:none; font-weight:700; }
        .shell { padding:38px 0 80px; }
        .hero {
            display:grid; grid-template-columns:1.3fr .7fr; gap:24px; margin-bottom:24px;
        }
        .panel { background:rgba(15,22,32,.8); border:1px solid var(--line); border-radius:24px; box-shadow:var(--shadow); }
        .hero-copy { padding:30px; }
        .eyebrow { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:rgba(103,232,249,.08); color:var(--primary); border:1px solid rgba(103,232,249,.2); font-size:.72rem; letter-spacing:0.08em; text-transform:uppercase; }
        .hero-copy h1 { margin:18px 0 12px; font-size:clamp(2.2rem,4vw,3.5rem); letter-spacing:-0.06em; }
        .hero-copy p { margin:0 0 18px; color:var(--muted); line-height:1.7; }
        .hero-actions { display:flex; gap:12px; flex-wrap:wrap; }
        .stats-inline { display:flex; gap:18px; flex-wrap:wrap; margin-top:20px; }
        .stat-pill { display:inline-flex; flex-direction:column; gap:4px; background:rgba(255,255,255,.02); border:1px solid var(--line); border-radius:14px; padding:12px 14px; }
        .stat-pill strong { font-size:1.2rem; }
        .stat-pill span { color:var(--muted); font-size:.82rem; }
        .quick-card { padding:28px; }
        .quick-grid { display:grid; gap:14px; }
        .mini-box { padding:16px; border:1px solid var(--line); border-radius:16px; background:rgba(255,255,255,.02); }
        .mini-box strong { display:block; margin-bottom:6px; }
        .mini-box span { color:var(--muted); }
        .section { margin-top:26px; }
        .section-head { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:16px; }
        .section-head h2 { margin:0; font-size:clamp(1.7rem,3vw,2.5rem); letter-spacing:-0.05em; }
        .project-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .project-card { padding:20px; }
        .project-thumbnail { height:150px; border-radius:18px; margin-bottom:18px; background:linear-gradient(135deg, rgba(103,232,249,.15), rgba(139,92,246,.18)); border:1px solid rgba(255,255,255,.06); }
        .project-meta { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
        .project-title { font-size:1.2rem; font-weight:600; }
        .status { padding:6px 10px; border-radius:999px; font-size:.72rem; letter-spacing:.04em; text-transform:uppercase; }
        .status.draft { background:rgba(251,191,36,.12); color:#fcd34d; border:1px solid rgba(251,191,36,.2); }
        .status.published { background:rgba(74,222,128,.12); color:#bef7c7; border:1px solid rgba(74,222,128,.2); }
        .project-card p { margin:0; color:var(--muted); line-height:1.7; }
        .card-actions { display:flex; gap:10px; margin-top:18px; }
        .small-btn { flex:1; border:none; border-radius:10px; padding:10px 12px; background:rgba(255,255,255,.03); color:var(--text); border:1px solid var(--line); }
        .small-btn.primary { background:linear-gradient(135deg,var(--primary),#9ae6ff); color:#06141e; font-weight:700; border:none; }
        .templates-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; }
        .template-card { overflow:hidden; }
        .template-visual { height:160px; background:linear-gradient(135deg, rgba(103,232,249,.15), rgba(139,92,246,.18)); border-bottom:1px solid var(--line); }
        .template-info { padding:18px; }
        .template-tag { display:inline-flex; padding:6px 8px; border-radius:999px; background:rgba(74,222,128,.12); border:1px solid rgba(74,222,128,.18); color:#bef7c7; font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; }
        .template-info h3 { margin:12px 0 8px; font-size:1.15rem; }
        .template-info p { margin:0; color:var(--muted); line-height:1.6; }
        .template-info .small-btn { margin-top:16px; }
        @media (max-width: 900px) {
            .hero, .project-grid, .templates-grid { grid-template-columns:1fr; }
            .nav { display:none; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div class="brand">
                <span class="brand-mark">WC</span>
                <span>Website Creator</span>
            </div>
            <nav class="nav" aria-label="Main navigation">
                <a href="#projects">Projects</a>
                <a href="#templates">Templates</a>
                <a href="#insights">Insights</a>
            </nav>
            <div class="toolbar">
                <a class="button" href="/">Home</a>
                <a class="button" href="/public/page-manager.php">Pages</a>
                <a class="button primary" href="/public/editor.php?id=1">New project</a>
            </div>
        </div>
    </header>

    <main class="container shell">
        <section class="hero">
            <div class="panel hero-copy">
                <span class="eyebrow">Workspace</span>
                <h1>Build your next website faster.</h1>
                <p>Create polished pages, reuse layouts, refine styles, and publish projects from a single, streamlined workspace.</p>
                <div class="hero-actions">
                    <a class="button primary" href="/public/editor.php?id=1">Open editor</a>
                    <a class="button" href="/public/page-manager.php">Manage pages</a>
                </div>
                <div class="stats-inline">
                    <div class="stat-pill"><strong>14</strong><span>Projects</span></div>
                    <div class="stat-pill"><strong>8</strong><span>Templates</span></div>
                    <div class="stat-pill"><strong>3</strong><span>Drafts</span></div>
                </div>
            </div>

            <aside class="panel quick-card">
                <div class="quick-grid">
                    <div class="mini-box">
                        <strong>Recent activity</strong>
                        <span>Updated landing page 2 hours ago</span>
                    </div>
                    <div class="mini-box">
                        <strong>Team</strong>
                        <span>3 collaborators</span>
                    </div>
                    <div class="mini-box">
                        <strong>Publishing</strong>
                        <span>1 build ready</span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="section" id="projects">
            <div class="section-head">
                <h2>Your projects</h2>
                <a class="button primary" href="/public/editor.php?id=1">Create new</a>
            </div>

            <div class="project-grid">
                <article class="panel project-card">
                    <div class="project-thumbnail"></div>
                    <div class="project-meta">
                        <span class="project-title">Launch Page</span>
                        <span class="status draft">Draft</span>
                    </div>
                    <p>Product launch site with pricing, testimonials, and high-conversion CTAs.</p>
                    <div class="card-actions">
                        <a class="small-btn primary" href="/public/editor.php?id=1">Open</a>
                        <button class="small-btn" type="button">Share</button>
                    </div>
                </article>

                <article class="panel project-card">
                    <div class="project-thumbnail" style="background:linear-gradient(135deg, rgba(74, 222, 128, 0.14), rgba(103,232,249,0.2));"></div>
                    <div class="project-meta">
                        <span class="project-title">Portfolio</span>
                        <span class="status published">Live</span>
                    </div>
                    <p>Personal portfolio with case studies, client work, and editorial storytelling.</p>
                    <div class="card-actions">
                        <a class="small-btn primary" href="/public/editor.php?id=2">Open</a>
                        <button class="small-btn" type="button">Preview</button>
                    </div>
                </article>

                <article class="panel project-card">
                    <div class="project-thumbnail" style="background:linear-gradient(135deg, rgba(251,191,36,0.16), rgba(139,92,246,0.16));"></div>
                    <div class="project-meta">
                        <span class="project-title">Agency Site</span>
                        <span class="status draft">Draft</span>
                    </div>
                    <p>Marketing-focused agency website featuring services, process, and contact funnel.</p>
                    <div class="card-actions">
                        <a class="small-btn primary" href="/public/editor.php?id=3">Open</a>
                        <button class="small-btn" type="button">Duplicate</button>
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="templates">
            <div class="section-head">
                <h2>Template gallery</h2>
                <a class="button" href="/public/templates.php">View all</a>
                <a class="button primary" href="/public/page-manager.php">Page manager</a>
            </div>

            <div class="templates-grid">
                <article class="panel template-card">
                    <div class="template-visual"></div>
                    <div class="template-info">
                        <span class="template-tag">Marketing</span>
                        <h3>Launch page</h3>
                        <p>High-converting hero, proof, and pricing layout.</p>
                        <button class="small-btn primary" type="button">Use template</button>
                    </div>
                </article>

                <article class="panel template-card">
                    <div class="template-visual" style="background:linear-gradient(135deg, rgba(74, 222, 128, 0.14), rgba(103,232,249,0.2));"></div>
                    <div class="template-info">
                        <span class="template-tag">Portfolio</span>
                        <h3>Portfolio</h3>
                        <p>Clean editorial grid for work and case studies.</p>
                        <button class="small-btn primary" type="button">Use template</button>
                    </div>
                </article>

                <article class="panel template-card">
                    <div class="template-visual" style="background:linear-gradient(135deg, rgba(251,191,36,0.12), rgba(139,92,246,0.16));"></div>
                    <div class="template-info">
                        <span class="template-tag">Business</span>
                        <h3>Agency</h3>
                        <p>Services-first presentation with conversion blocks.</p>
                        <button class="small-btn primary" type="button">Use template</button>
                    </div>
                </article>

                <article class="panel template-card">
                    <div class="template-visual" style="background:linear-gradient(135deg, rgba(103,232,249,0.14), rgba(74, 222, 128, 0.12));"></div>
                    <div class="template-info">
                        <span class="template-tag">Startup</span>
                        <h3>SaaS</h3>
                        <p>Conversion flow for software products and demos.</p>
                        <button class="small-btn primary" type="button">Use template</button>
                    </div>
                </article>
            </div>
        </section>
    </main>
</body>
</html>
