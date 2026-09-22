
<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Auth;

$isLoggedIn = Auth::isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Website Creator Engine</title>
	<meta name="description" content="Visual website builder for modern product teams.">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<style>
		:root {
			--bg: #07111f;
			--bg-2: #0d1a2d;
			--line: rgba(148, 163, 184, 0.2);
			--text: #edf5ff;
			--muted: #a4b7d3;
			--primary: #67e8f9;
			--primary-2: #8b5cf6;
			--success: #4ade80;
			--shadow: 0 30px 60px rgba(4, 10, 18, 0.55);
			--radius: 24px;
		}
		* { box-sizing: border-box; }
		html { scroll-behavior: smooth; }
		body {
			margin: 0;
			font-family: 'Outfit', sans-serif;
			background: radial-gradient(circle at top left, rgba(103, 232, 249, 0.18), transparent 30%),
				radial-gradient(circle at top right, rgba(139, 92, 246, 0.18), transparent 35%),
				linear-gradient(180deg, var(--bg), var(--bg-2));
			color: var(--text);
		}
		a { color: inherit; text-decoration: none; }
		.container { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
		.site-header { position: sticky; top: 0; z-index: 20; backdrop-filter: blur(16px); background: rgba(7, 17, 31, 0.75); border-bottom: 1px solid var(--line); }
		.topbar { display: flex; align-items: center; justify-content: space-between; min-height: 82px; gap: 20px; }
		.brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 700; }
		.brand-mark { width: 36px; height: 36px; border-radius: 12px; display: grid; place-items: center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #05121e; font-weight: 800; }
		.nav { display: flex; align-items: center; gap: 26px; color: var(--muted); font-size: 0.96rem; }
		.nav a:hover { color: var(--text); }
		.header-actions { display: flex; align-items: center; gap: 12px; }
		.button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 12px; padding: 12px 20px; border: 1px solid rgba(255,255,255,0.08); font-weight: 600; transition: transform 0.2s ease, border-color 0.2s ease; }
		.button:hover { transform: translateY(-1px); border-color: rgba(103, 232, 249, 0.65); }
		.button.primary { background: linear-gradient(135deg, var(--primary), #9ae6ff); color: #05121e; border: none; box-shadow: 0 20px 35px rgba(103, 232, 249, 0.28); }
		.button.secondary { background: rgba(255,255,255,0.02); color: var(--text); }
		.hero { padding: 72px 0 40px; }
		.hero-grid { display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 28px; align-items: center; }
		.eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 14px; border-radius: 999px; background: rgba(103, 232, 249, 0.08); border: 1px solid rgba(103, 232, 249, 0.18); color: var(--primary); font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; }
		.hero h1 { margin: 22px 0 16px; font-size: clamp(2.8rem, 5vw, 5rem); line-height: 0.94; letter-spacing: -0.06em; }
		.hero p { margin: 0; max-width: 620px; color: var(--muted); font-size: 1.08rem; line-height: 1.7; }
		.hero-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 28px; }
		.mini-trust { display: flex; flex-wrap: wrap; gap: 24px; margin-top: 30px; color: var(--muted); font-size: 0.9rem; }
		.mini-trust span { display: inline-flex; align-items: center; gap: 8px; }
		.dot { width: 8px; height: 8px; border-radius: 50%; background: var(--success); box-shadow: 0 0 18px rgba(74, 222, 128, 0.8); }
		.product-preview { position: relative; padding: 24px; border-radius: 30px; background: linear-gradient(180deg, rgba(17, 27, 42, 0.9), rgba(8, 16, 25, 0.9)); border: 1px solid var(--line); box-shadow: var(--shadow); }
		.mock-window { background: rgba(5, 10, 17, 0.78); border: 1px solid rgba(255,255,255,0.08); border-radius: 22px; overflow: hidden; }
		.window-bar { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.06); }
		.window-dots { display: flex; gap: 8px; }
		.window-dots span { width: 11px; height: 11px; border-radius: 50%; display: inline-block; }
		.dot-red { background: #ff5f57; }
		.dot-yellow { background: #fbbf24; }
		.dot-green { background: #4ade80; }
		.window-title { color: var(--muted); font-size: 0.82rem; letter-spacing: 0.04em; text-transform: uppercase; }
		.window-body { display: grid; grid-template-columns: 220px 1fr 210px; min-height: 500px; }
		.aside, .inspector { background: rgba(13, 20, 30, 0.9); border-right: 1px solid rgba(255,255,255,0.06); padding: 18px; }
		.inspector { border-right: none; border-left: 1px solid rgba(255,255,255,0.06); }
		.section-chip { display: inline-block; margin-bottom: 18px; padding: 8px 10px; border-radius: 10px; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; background: rgba(103, 232, 249, 0.08); color: var(--primary); }
		.stack { display: grid; gap: 12px; }
		.stack-item { padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02); color: var(--muted); font-size: 0.88rem; }
		.stack-item.active { background: rgba(103, 232, 249, 0.08); border-color: rgba(103, 232, 249, 0.2); color: var(--text); }
		.canvas-surface { padding: 22px; background: rgba(10, 17, 26, 0.68); }
		.page-card { background: linear-gradient(180deg, rgba(14, 23, 36, 0.8), rgba(11, 19, 28, 0.8)); border: 1px solid rgba(255,255,255,0.06); border-radius: 18px; padding: 18px; }
		.page-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
		.page-pill { display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--muted); }
		.page-pill strong { color: var(--text); }
		.page-hero { padding: 34px 22px; border-radius: 16px; background: linear-gradient(135deg, rgba(103,232,249,0.18), rgba(139,92,246,0.12)); border: 1px solid rgba(103,232,249,0.14); }
		.page-hero h3 { margin: 0 0 10px; font-size: clamp(1.5rem, 2vw, 2.1rem); letter-spacing: -0.05em; }
		.page-hero p { margin: 0; color: var(--muted); line-height: 1.6; }
		.stat-blocks { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-top: 24px; }
		.stat-box { padding: 14px; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); }
		.stat-box strong { display: block; margin-bottom: 4px; font-size: 1.15rem; }
		.section { padding: 92px 0 0; }
		.section-head { display: flex; justify-content: space-between; align-items: end; gap: 24px; margin-bottom: 28px; }
		.section-head h2 { margin: 0; font-size: clamp(2rem, 3vw, 3rem); letter-spacing: -0.05em; }
		.section-head p { margin: 0; max-width: 620px; color: var(--muted); line-height: 1.7; }
		.stats-grid, .feature-grid, .template-grid, .workflow-grid { display: grid; gap: 20px; }
		.stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
		.stats-card, .feature-card, .template-card, .workflow-card { background: rgba(15, 23, 34, 0.78); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); }
		.stats-card { padding: 30px 24px; }
		.stats-card strong { display: block; font-size: clamp(1.7rem, 3vw, 2.6rem); letter-spacing: -0.05em; }
		.stats-card span { color: var(--muted); display: block; margin-top: 8px; }
		.feature-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
		.feature-card { padding: 30px; }
		.feature-icon { width: 52px; height: 52px; display: grid; place-items: center; border-radius: 16px; background: linear-gradient(135deg, rgba(103,232,249,0.18), rgba(139,92,246,0.2)); color: var(--primary); font-size: 1.5rem; margin-bottom: 22px; }
		.feature-card h3, .template-card h3, .workflow-card h3 { margin: 0 0 10px; font-size: 1.35rem; letter-spacing: -0.03em; }
		.feature-card p, .template-card p, .workflow-card p { margin: 0; line-height: 1.7; color: var(--muted); }
		.workflow-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
		.workflow-card { padding: 28px 22px; position: relative; overflow: hidden; }
		.workflow-card .step { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 12px; background: rgba(103,232,249,0.1); color: var(--primary); font-weight: 700; margin-bottom: 18px; }
		.template-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
		.template-card { overflow: hidden; }
		.template-art { height: 220px; border-bottom: 1px solid rgba(255,255,255,0.06); background: linear-gradient(135deg, rgba(103,232,249,0.15), rgba(139,92,246,0.18)); position: relative; }
		.template-art::before, .template-art::after { content: ""; position: absolute; border-radius: 14px; background: rgba(255,255,255,0.12); }
		.template-art::before { width: 68%; height: 56%; left: 18px; top: 38px; }
		.template-art::after { width: 40%; height: 30%; right: 18px; bottom: 26px; background: rgba(103,232,249,0.2); }
		.template-meta { padding: 24px 22px 26px; }
		.badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 7px 11px; font-size: 0.75rem; letter-spacing: 0.04em; text-transform: uppercase; background: rgba(74, 222, 128, 0.12); border: 1px solid rgba(74, 222, 128, 0.18); color: #bef7c7; margin-bottom: 12px; }
		.cta-panel { padding: 92px 0 100px; }
		.cta-box { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: clamp(28px, 4vw, 52px); border-radius: 30px; background: linear-gradient(135deg, rgba(103,232,249,0.12), rgba(139,92,246,0.12)); border: 1px solid var(--line); box-shadow: var(--shadow); }
		.cta-box h3 { margin: 0 0 8px; font-size: clamp(1.8rem, 3vw, 2.8rem); letter-spacing: -0.05em; }
		.cta-box p { margin: 0; color: var(--muted); line-height: 1.7; }
		.site-footer { border-top: 1px solid var(--line); padding: 28px 0 40px; color: var(--muted); }
		.footer-row { display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
		.footer-links { display: flex; gap: 18px; flex-wrap: wrap; }
		@media (max-width: 980px) { .hero-grid, .window-body, .cta-box, .section-head { grid-template-columns: 1fr; display: grid; } .nav { display: none; } .stats-grid, .feature-grid, .template-grid, .workflow-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .window-body { grid-template-columns: 1fr; } .aside, .inspector { border: none; border-bottom: 1px solid rgba(255,255,255,0.06); } }
		@media (max-width: 640px) { .header-actions { width: 100%; justify-content: flex-end; } .hero { padding-top: 48px; } .stats-grid, .feature-grid, .template-grid, .workflow-grid { grid-template-columns: 1fr; } .button { width: 100%; } .hero-actions { display: grid; grid-template-columns: 1fr; } }
	</style>
</head>
<body>
	<header class="site-header">
		<div class="container topbar">
			<a href="#top" class="brand" aria-label="Website Creator home">
				<span class="brand-mark">WC</span>
				<span>Website Creator</span>
			</a>

			<nav class="nav" aria-label="Main navigation">
				<a href="#features">Features</a>
				<a href="#workflow">Workflow</a>
				<a href="#templates">Templates</a>
				<a href="#about">About</a>
			</nav>

			<div class="header-actions">
				<?php if ($isLoggedIn): ?>
					<a class="button secondary" href="/public/dashboard.php">Dashboard</a>
					<a class="button primary" href="/public/editor.php?id=1">Continue project</a>
				<?php else: ?>
					<a class="button secondary" href="/public/auth.php">Login</a>
					<a class="button primary" href="/public/auth.php">Start creating</a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="top">
		<section class="hero">
			<div class="container hero-grid">
				<div>
					<span class="eyebrow">Visual web builder</span>
					<h1>Design, structure, and ship websites visually.</h1>
					<p>Build production-grade pages from a structured document model, edit with a live canvas, and publish clean output without losing control over layout, responsiveness, and content.</p>
					<div class="hero-actions">
						<a class="button primary" href="/public/editor.php?id=1">Launch editor</a>
						<a class="button secondary" href="#features">Explore features</a>
					</div>
					<div class="mini-trust" aria-label="Project highlights">
						<span><i class="dot"></i> Structured document model</span>
						<span><i class="dot"></i> Responsive-first editing</span>
						<span><i class="dot"></i> Publishing workflow</span>
					</div>
				</div>

				<div class="product-preview" aria-label="Website Creator editor preview">
					<div class="mock-window">
						<div class="window-bar">
							<div class="window-dots"><span class="dot-red"></span><span class="dot-yellow"></span><span class="dot-green"></span></div>
							<div class="window-title">studio / landing-page</div>
						</div>

						<div class="window-body">
							<aside class="aside">
								<span class="section-chip">Blocks</span>
								<div class="stack">
									<div class="stack-item active">Hero section</div>
									<div class="stack-item">Features</div>
									<div class="stack-item">Testimonials</div>
									<div class="stack-item">Pricing</div>
									<div class="stack-item">Contact</div>
								</div>
							</aside>

							<div class="canvas-surface">
								<div class="page-card">
									<div class="page-bar">
										<div class="page-pill"><strong>Home</strong> / Desktop</div>
										<span class="page-pill">Live preview</span>
									</div>
									<div class="page-hero">
										<h3>Build better experiences.</h3>
										<p>Create a modern web presence with visuals, content blocks, layout precision, and fast iteration.</p>
									</div>
									<div class="stat-blocks">
										<div class="stat-box"><strong>42%</strong><span>faster launch</span></div>
										<div class="stat-box"><strong>5x</strong><span>more reusable sections</span></div>
										<div class="stat-box"><strong>0</strong><span>hand-coded bottlenecks</span></div>
									</div>
								</div>
							</div>

							<aside class="inspector">
								<span class="section-chip">Inspector</span>
								<div class="stack">
									<div class="stack-item active">Typography</div>
									<div class="stack-item">Spacing</div>
									<div class="stack-item">Colors</div>
									<div class="stack-item">Responsive</div>
								</div>
							</aside>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section" id="about">
			<div class="container">
				<div class="section-head">
					<h2>Built for clarity, speed, and control.</h2>
					<p>This platform treats the website as an editable structured system instead of a fragile visual snapshot, giving teams a cleaner path from concept to production.</p>
				</div>
				<div class="stats-grid">
					<div class="stats-card"><strong>4x</strong><span>faster prototyping</span></div>
					<div class="stats-card"><strong>100%</strong><span>structured content</span></div>
					<div class="stats-card"><strong>3</strong><span>responsive modes</span></div>
					<div class="stats-card"><strong>∞</strong><span>design iteration</span></div>
				</div>
			</div>
		</section>

		<section class="section" id="features">
			<div class="container">
				<div class="section-head">
					<h2>Everything a modern site builder needs.</h2>
					<p>From composition and responsiveness to templates and publishing, every layer is designed around a clear, modular, data-first experience.</p>
				</div>
				<div class="feature-grid">
					<article class="feature-card"><div class="feature-icon">✦</div><h3>Visual editor</h3><p>Drag, select, style, and refine content directly on the canvas without losing the underlying structure.</p></article>
					<article class="feature-card"><div class="feature-icon">▣</div><h3>Structured model</h3><p>Each page is backed by a machine-readable document that stays portable, reusable, and easy to reason about.</p></article>
					<article class="feature-card"><div class="feature-icon">◌</div><h3>Responsive design</h3><p>Preview and tune layouts across desktop, tablet, and mobile breakpoints from one unified editing surface.</p></article>
					<article class="feature-card"><div class="feature-icon">⚡</div><h3>Publishing workflow</h3><p>Prepare a project for output, deployment, or static export using a clean build and publishing stack.</p></article>
					<article class="feature-card"><div class="feature-icon">◎</div><h3>Templates</h3><p>Use reusable patterns and page layouts to accelerate the creation of landing pages, portfolios, and agencies.</p></article>
					<article class="feature-card"><div class="feature-icon">✧</div><h3>AI-powered flow</h3><p>Extend the platform with prompt-based generation and intelligent design assistance without breaking the core model.</p></article>
				</div>
			</div>
		</section>

		<section class="section" id="workflow">
			<div class="container">
				<div class="section-head">
					<h2>From idea to published website in four steps.</h2>
					<p>A clean system that turns user intent into structured pages and deployable output.</p>
				</div>
				<div class="workflow-grid">
					<article class="workflow-card"><div class="step">1</div><h3>Plan</h3><p>Choose a project goal, page, template, or section to begin the build.</p></article>
					<article class="workflow-card"><div class="step">2</div><h3>Design</h3><p>Arrange blocks, tune spacing, adjust typography, and update content visually.</p></article>
					<article class="workflow-card"><div class="step">3</div><h3>Refine</h3><p>Inspect properties, validate responsiveness, and keep every node consistent.</p></article>
					<article class="workflow-card"><div class="step">4</div><h3>Publish</h3><p>Generate the final site output and ship a clean, performant website.</p></article>
				</div>
			</div>
		</section>

		<section class="section" id="templates">
			<div class="container">
				<div class="section-head">
					<h2>Start from proven patterns.</h2>
					<p>Templates help teams move quickly while keeping the editor flexible enough for custom builds.</p>
				</div>
				<div class="template-grid">
					<article class="template-card"><div class="template-art"></div><div class="template-meta"><span class="badge">Marketing</span><h3>Launch page</h3><p>Perfect for product launches, announcements, and conversion-focused landing pages.</p></div></article>
					<article class="template-card"><div class="template-art" style="background: linear-gradient(135deg, rgba(74, 222, 128, 0.16), rgba(103,232,249,0.18));"></div><div class="template-meta"><span class="badge">Portfolio</span><h3>Creative portfolio</h3><p>Showcase work, process, and case studies with elegant editorial layout systems.</p></div></article>
					<article class="template-card"><div class="template-art" style="background: linear-gradient(135deg, rgba(251, 191, 36, 0.12), rgba(139,92,246,0.18));"></div><div class="template-meta"><span class="badge">Agency</span><h3>Business site</h3><p>Present services, pricing, and social proof for agencies, studios, and consultants.</p></div></article>
				</div>
			</div>
		</section>

		<section class="cta-panel">
			<div class="container">
				<div class="cta-box">
					<div>
						<h3>Ready to build your next website?</h3>
						<p>Open the editor and turn your ideas into responsive, structured, publishable pages.</p>
					</div>
					<a class="button primary" href="/public/editor.php?id=1">Open studio</a>
				</div>
			</div>
		</section>
	</main>

	<footer class="site-footer">
		<div class="container footer-row">
			<div class="brand">
				<span class="brand-mark">WC</span>
				<span>Website Creator</span>
			</div>
			<div class="footer-links">
				<a href="#features">Features</a>
				<a href="#workflow">Workflow</a>
				<a href="#templates">Templates</a>
				<a href="/public/editor.php?id=1">Editor</a>
			</div>
		</div>
	</footer>
</body>
</html>
