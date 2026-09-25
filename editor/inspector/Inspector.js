// Inspector.js - Inspector panel with Component Registry and Schema integration
import { ComponentRegistry } from './ComponentRegistry.js';

export class Inspector {
    constructor(container, tree, onNodeUpdateCallback) {
        this.container = container;
        this.tree = tree;
        this.selectedId = null;
        this.onNodeUpdateCallback = onNodeUpdateCallback;
        this.activeBreakpoint = 'desktop';
    }

    selectNode(id) {
        this.selectedId = id;
        this.render();
    }

    setBreakpoint(bp) {
        this.activeBreakpoint = bp;
        this.render();
    }

    _rgbToHex(rgb) {
        if (!rgb) return '#000000';
        if (rgb.startsWith('#')) return rgb;
        return '#000000'; // Basic fallback
    }

    render() {
        if (!this.selectedId) {
            this.container.innerHTML = '<p>Select a node to inspect</p>';
            return;
        }

        const node = this.tree.findNode(this.selectedId);
        if (!node) {
            this.container.innerHTML = '<p>Error: Node not found</p>';
            return;
        }

        const componentDef = ComponentRegistry.get(node.type || 'container');
        node.styles = node.styles || { desktop: {}, tablet: {}, mobile: {} };
        const styles = node.styles[this.activeBreakpoint] || {};

        let controlsHtml = `
            <h3>${node.type || 'Container'}</h3>
            <label>Breakpoint:</label>
            <select id="bp-select">
                <option value="desktop" ${this.activeBreakpoint === 'desktop' ? 'selected' : ''}>Desktop</option>
                <option value="tablet" ${this.activeBreakpoint === 'tablet' ? 'selected' : ''}>Tablet</option>
                <option value="mobile" ${this.activeBreakpoint === 'mobile' ? 'selected' : ''}>Mobile</option>
            </select>
        `;

        // Generate controls based on schema
        Object.entries(componentDef.schema).forEach(([key, field]) => {
            const value = node[key] || styles[key] || '';
            controlsHtml += `<label>${field.label}:</label>`;
            if (field.type === 'color') {
                controlsHtml += `<input type="color" value="${this._rgbToHex(value)}" id="field-${key}">`;
            } else if (field.type === 'number') {
                controlsHtml += `<input type="number" value="${value || '0'}" id="field-${key}">`;
            } else {
                controlsHtml += `<input type="text" value="${value || ''}" id="field-${key}">`;
            }
        });

        this.container.innerHTML = controlsHtml + `<button id="save-node">Save</button>`;

        document.getElementById('bp-select').addEventListener('change', (e) => this.setBreakpoint(e.target.value));

        document.getElementById('save-node').addEventListener('click', () => {
            const newProps = { styles: { ...node.styles } };
            const newStyles = { ...newProps.styles[this.activeBreakpoint] };

            Object.entries(componentDef.schema).forEach(([key, field]) => {
                const val = document.getElementById(`field-${key}`).value;
                if (['content', 'url', 'src', 'alt'].includes(key)) {
                    newProps[key] = val;
                } else {
                    newStyles[key] = val;
                }
            });

            newProps.styles[this.activeBreakpoint] = newStyles;
            if (this.onNodeUpdateCallback) this.onNodeUpdateCallback(this.selectedId, newProps);
        });
    }
}
