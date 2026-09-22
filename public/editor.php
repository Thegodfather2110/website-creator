<?php
// Main entry point for the Visual Editor
$pageId = $_GET['id'] ?? null;
if (!$pageId) {
    die("No page ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Website Creator Engine - Editor</title>
    <link rel="stylesheet" href="/editor/css/editor.css">
</head>
<body>
    <div id="editor-container">
        <aside id="sidebar-left">Components</aside>
        <main id="canvas">Canvas Area</main>
        <aside id="sidebar-right">Inspector</aside>
    </div>
    <script type="module" src="/editor/js/editor.js"></script>
</body>
</html>
