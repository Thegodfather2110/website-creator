<?php
// Main entry point for the Visual Editor
$pageId = $_GET['id'] ?? 'demo';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Creator Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/editor/css/editor.css">
</head>
<body class="studio-shell">
    <header class="studio-header">
        <div class="studio-brand">
            <span class="brand-mark">WC</span>
            <div>
                <span class="brand-name">Website Creator</span>
                <small>Studio</small>
            </div>
        </div>

        <div class="workspace-meta">
            <span class="chip neutral">Project</span>
            <span class="project-name">Landing page concept</span>
        </div>

        <div class="workspace-actions" id="toolbar-actions">
            <button class="tool-btn" type="button" data-action="undo">Undo</button>
            <button class="tool-btn" type="button" data-action="redo">Redo</button>
            <button class="tool-btn" type="button" data-action="save">Save</button>
            <button class="tool-btn primary" type="button" data-action="publish">Publish</button>
        </div>
    </header>

    <div class="editor-layout">
        <aside class="sidebar left-panel">
            <div class="panel-header">
                <h3>Components</h3>
                <span class="panel-badge">Library</span>
            </div>

            <div class="component-list">
                <div class="component-item active">Hero</div>
                <div class="component-item">Features</div>
                <div class="component-item">Gallery</div>
                <div class="component-item">Testimonials</div>
                <div class="component-item">Pricing</div>
                <div class="component-item">Contact</div>
            </div>

            <div class="panel-block">
                <h4>Pages</h4>
                <ul>
                    <li class="page-item active">Home</li>
                    <li class="page-item">Services</li>
                    <li class="page-item">About</li>
                </ul>
            </div>
        </aside>

        <main class="studio-stage">
            <div class="stage-toolbar">
                <div class="stage-tabs">
                    <button class="tab active" type="button">Desktop</button>
                    <button class="tab" type="button">Tablet</button>
                    <button class="tab" type="button">Mobile</button>
                </div>
                <div class="stage-tools">
                    <span class="status-pill">Live preview</span>
                    <span class="status-pill muted">Page #<?php echo htmlspecialchars($pageId); ?></span>
                </div>
            </div>

            <div id="canvas" class="canvas-surface" aria-label="Canvas area"></div>
        </main>

        <aside class="sidebar right-panel">
            <div class="panel-header">
                <h3>Inspector</h3>
                <span class="panel-badge">Selection</span>
            </div>
            <div id="sidebar-right" class="inspector-panel">
                <p>Select a node to inspect it.</p>
            </div>
        </aside>
    </div>

    <script type="module" src="/editor/js/editor.js"></script>
</body>
</html>
