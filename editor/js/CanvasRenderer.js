// CanvasRenderer.js - Visual rendering
export class CanvasRenderer {
    constructor(canvasElement) {
        this.container = canvasElement;
    }

    render(documentModel) {
        this.container.innerHTML = '';
        if (!documentModel.pages || documentModel.pages.length === 0) return;

        const rootNodes = documentModel.pages[0].root || [];
        rootNodes.forEach(node => {
            this.container.appendChild(this._createNodeElement(node));
        });
    }

    _createNodeElement(node) {
        const el = document.createElement(node.tagName || 'div');
        el.dataset.id = node.id;

        // Apply styles directly
        if (node.styles) {
            Object.assign(el.style, node.styles);
        }

        if (node.content) {
            el.textContent = node.content;
        }

        if (node.children) {
            node.children.forEach(child => {
                el.appendChild(this._createNodeElement(child));
            });
        }
        return el;
    }
}
