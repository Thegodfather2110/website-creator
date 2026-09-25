// Inspector.js - Inspector panel with robust schema and token support
import { ComponentRegistry } from './ComponentRegistry.js';
import { DesignTokens } from '../js/Tokens.js';

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

    render() {
        if (!this.selectedId) {
            this.container.innerHTML = '<p>Select a node to inspect</p>';
            return;
        }

        const node = this.tree.findNode(this.selectedId);
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
                controlsHtml += `<input type="color" value="${value || '#000000'}" id="field-${key}">`;
            } else if (field.type === 'select') {
                // If it's a token type, use token values
                if (field.options && !Array.isArray(field.options)) {
                   controlsHtml += `<select id="field-${key}">` +
                       Object.entries(field.options).map(([k, v]) =>
                           `<option value="${v}" ${value === v ? 'selected' : ''}>${k}</option>`).join('') +
                       `</select>`;
                } else {
                   // Generic select
                   controlsHtml += `<select id="field-${key}">...</select>`;
                }
            } else {
                controlsHtml += `<input type="text" value="${value || ''}" id="field-${key}">`;
            }
        });

        this.container.innerHTML = controlsHtml + `<button id="save-node">Save</button>`;

        document.getElementById('save-node').addEventListener('click', () => {
            const newProps = { styles: { ...node.styles } };
            const newStyles = { ...newProps.styles[this.activeBreakpoint] };

            Object.entries(componentDef.schema).forEach(([key, field]) => {
                const el = document.getElementById(`field-${key}`);
                if (!el) return;

                // Logic to separate content vs styles
                if (['content', 'url', 'src', 'alt'].includes(key)) {
                    newProps[key] = el.value;
                } else {
                    newStyles[key] = el.value;
                }
            });

            newProps.styles[this.activeBreakpoint] = newStyles;
            if (this.onNodeUpdateCallback) this.onNodeUpdateCallback(this.selectedId, newProps);
        });
    }
}
