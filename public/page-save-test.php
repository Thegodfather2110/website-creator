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
    <title>Page API Tester</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111827; color: white; padding: 30px; }
        .box { max-width: 820px; margin: 0 auto; background: #1f2937; padding: 20px; border-radius: 16px; }
        textarea, input, button { width: 100%; padding: 12px; border-radius: 10px; margin: 10px 0; }
        button { background: #67e8f9; border: none; font-weight: bold; }
        pre { background: #0b1220; padding: 16px; border-radius: 12px; overflow: auto; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Page Save / Load API Tester</h2>
        <label>Page ID</label>
        <input id="pageId" value="1" />

        <label>Document JSON</label>
        <textarea id="documentJson" rows="12">{
  "pages": [{
    "id": "page_home",
    "root": [{
      "id": "node_1",
      "tagName": "section",
      "content": "",
      "styles": { "desktop": { "padding": "24px", "backgroundColor": "#0f172a", "color": "#fff" } },
      "children": [{ "id": "node_2", "tagName": "h1", "content": "Hello from API test", "styles": { "desktop": { "color": "#67e8f9" } } }]
    }]
  }]
}</textarea>

        <button id="saveBtn">Save Page</button>
        <button id="loadBtn">Load Page</button>

        <h3>Output</h3>
        <pre id="output">Waiting...</pre>
    </div>

    <script>
        const output = document.getElementById('output');
        document.getElementById('saveBtn').addEventListener('click', async () => {
            const payload = {
                id: Number(document.getElementById('pageId').value),
                document_json: JSON.parse(document.getElementById('documentJson').value)
            };

            const res = await fetch('/api/pages/save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            output.textContent = JSON.stringify(json, null, 2);
        });

        document.getElementById('loadBtn').addEventListener('click', async () => {
            const pageId = document.getElementById('pageId').value;
            const res = await fetch(`/api/pages/load.php?id=${pageId}`);
            const json = await res.json();
            output.textContent = JSON.stringify(json, null, 2);
        });
    </script>
</body>
</html>
