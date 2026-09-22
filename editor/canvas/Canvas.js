// Canvas.js - Visual rendering with Responsive support
import { getResponsiveStyle } from '../js/Responsive.js';

export class CanvasRenderer {
    constructor(canvasElement) {
        this.container = canvasElement;
    }

    render(documentModel, activeBreakpoint) {
        this.container.innerHTML = '';
        if (!documentModel.pages || documentModel.pages.length === 0) return;

        const rootNodes = documentModel.pages[0].root || [];
        rootNodes.forEach(node => {
            this.container.appendChild(this._createNodeElement(node, activeBreakpoint));
        });
    }

    _createNodeElement(node, activeBreakpoint) {
        const el = document.createElement(node.tagName || 'div');
        el.dataset.id = node.id;

        // Apply styles based on active breakpoint + inheritance
        const styles = node.styles ? getResponsiveStyle(node.styles, activeBreakpoint) : {};
        if (styles) {
            Object.assign(el.style, styles);
        }

        if (node.content) {
            el.textContent = node.content;
        }

        if (node.children) {
            node.children.forEach(child => {
                el.appendChild(this._createNodeElement(child, activeBreakpoint));
            });
        }
        return el;
    }
}
