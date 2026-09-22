// PageManager.js - UI for Page Management
export class PageManager {
    constructor(container, projectId, onPageSelect) {
        this.container = container;
        this.projectId = projectId;
        this.onPageSelect = onPageSelect;
    }

    async render() {
        const response = await fetch(`/api/pages/list.php?project_id=${this.projectId}`);
        const result = await response.json();
        const pages = result.data || [];

        this.container.innerHTML = `
            <h3>Pages</h3>
            <button id="add-page">Add Page</button>
            <ul>
                ${pages.map(p => `<li data-id="${p.id}">${p.name}</li>`).join('')}
            </ul>
        `;

        this.container.querySelector('ul').addEventListener('click', (e) => {
            const pageId = e.target.dataset.id;
            if (pageId) this.onPageSelect(pageId);
        });
    }
}
