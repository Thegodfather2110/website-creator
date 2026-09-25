// Inspector.js - Inspector panel with Breakpoint support and Command Integration
adimport { DesignTokens } from '../js/Tokens.js';

export class Inspector {
    constructor(container, tree, onNodeUpdateCallback) {
        this.container = container;
        this.tree = tree;
        this.selectedId = null;
        this.onNodeUpdateCallback = onNodeUpdateCallback; // Now expects (nodeId, newProps)
        this.activeBreakpoint = 'desktop'; // Default breakpoint
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
        // Ensure styles has a breakpoint object
        node.styles = node.styles || { desktop: {}, tablet: {}, mobile: {} };
        const styles = node.styles[this.activeBreakpoint] || {};

        this.container.innerHTML = `
            <h3>Inspector</h3>
            <p>Node ID: ${node.id}</p>
            <label>Breakpoint:</label>
            <select id="bp-select">
                <option value="desktop" ${this.activeBreakpoint === 'desktop' ? 'selected' : ''}>Desktop</option>
                <option value="tablet" ${this.activeBreakpoint === 'tablet' ? 'selected' : ''}>Tablet</option>
                <option value="mobile" ${this.activeBreakpoint === 'mobile' ? 'selected' : ''}>Mobile</option>
            </select>

            <label>Content:</label>
            <input type="text" value="${node.content || ''}" id="content-input">

            <h4>Layout (${this.activeBreakpoint})</h4>
            <label>Display:</label>
            <select id="display-input">
                <option value="block" ${styles.display === 'block' ? 'selected' : ''}>Block</option>
                <option value="flex" ${styles.display === 'flex' ? 'selected' : ''}>Flex</option>
            </select>
            <label>Padding:</label>
            <input type="text" value="${styles.padding || ''}" id="padding-input">

            <h4>Appearance (${this.activeBreakpoint})</h4>
            <label>Text Color:</label>
            <input type="color" value="${styles.color || '#000000'}" id="color-input">
            <label>Background:</label>
            <input type="color" value="${styles.backgroundColor || '#ffffff'}" id="bg-input">

            <button id="save-node">Save</button>
        `;

        document.getElementById('bp-select').addEventListener('change', (e) => {
            this.setBreakpoint(e.target.value);
        });

        document.getElementById('save-node').addEventListener('click', () => {
            const newContent = document.getElementById('content-input').value;

            const newStyles = { ...node.styles };
            newStyles[this.activeBreakpoint] = {
                display: document.getElementById('display-input').value,
                padding: document.getElementById('padding-input').value,
                color: document.getElementById('color-input').value,
                backgroundColor: document.getElementById('bg-input').value
            };

            // Call callback with nodeId and ALL new properties (content + updated styles)
            if (this.onNodeUpdateCallback) {
                this.onNodeUpdateCallback(this.selectedId, {
                    content: newContent,
                    styles: newStyles
                });
            }
        });
    }
}
