// CanvasRenderer.js - Visual rendering with Registry-based constraints
import { getResponsiveStyle } from '../js/Responsive.js';
import { ComponentRegistry } from '../js/components/ComponentRegistry.js';

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
        // Use Registry to get component definition
        const componentDef = ComponentRegistry.get(node.type || 'container');
        const el = document.createElement(node.tagName || componentDef.tagName || 'div');
        el.dataset.id = node.id;
        el.dataset.type = node.type || 'container';

        // Apply styles based on active breakpoint + inheritance
        const styles = node.styles ? getResponsiveStyle(node.styles, activeBreakpoint) : {};
        if (styles) {
            Object.assign(el.style, styles);
        }

        // Handle component-specific attributes
        if (node.tagName === 'img' && node.src) el.src = node.src;
        if (node.content) el.textContent = node.content;

        // Recursive render children if allowed
        if (node.children) {
            node.children.forEach(child => {
                // Constraint Check: can the current node accept this child type?
                if (ComponentRegistry.canAcceptChild(node.type || 'container', child.type || 'container')) {
                    el.appendChild(this._createNodeElement(child, activeBreakpoint));
                }
            });
        }
        return el;
    }
}
