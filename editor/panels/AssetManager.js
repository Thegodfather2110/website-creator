// AssetManager.js - UI for Asset Management
export class AssetManager {
    constructor(container, projectId) {
        this.container = container;
        this.projectId = projectId;
    }

    async render() {
        this.container.innerHTML = `
            <h3>Assets</h3>
            <input type="file" id="asset-upload">
            <button id="upload-btn">Upload</button>
            <ul id="assets-list"></ul>
        `;

        this.container.querySelector('#upload-btn').addEventListener('click', async () => {
            const formData = new FormData();
            formData.append('file', document.getElementById('asset-upload').files[0]);
            formData.append('project_id', this.projectId);

            await fetch('/api/assets/upload.php', { method: 'POST', body: formData });
            this.loadAssets();
        });

        this.loadAssets();
    }

    async loadAssets() {
        const res = await fetch(`/api/assets/list.php?project_id=${this.projectId}`);
        const result = await res.json();
        const list = document.getElementById('assets-list');
        list.innerHTML = result.data.map(asset => `<li>${asset.path}</li>`).join('');
    }
}
