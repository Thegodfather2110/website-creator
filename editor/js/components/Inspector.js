// Inspector.js - Inspector panel with Design Tokens
import { DesignTokens } from '../Tokens.js';

export class Inspector {
    constructor(container, tree, onUpdateCallback) {
        this.container = container;
        this.tree = tree;
        this.selectedId = null;
        this.onUpdateCallback = onUpdateCallback;
    }

    selectNode(id) {
        this.selectedId = id;
        this.render();
    }

    render() {
        if (!this.selectedId) {
            this.container.innerHTML = '<p>Select a node to inspect</p>';
            return;
        }

        const node = this.tree.findNode(this.selectedId);
        const styles = node.styles || {};

        this.container.innerHTML = `
            <h3>Inspector</h3>
            <p>Node ID: ${node.id}</p>
            <label>Content:</label>
            <input type="text" value="${node.content || ''}" id="content-input">

            <h4>Layout</h4>
            <label>Display:</label>
            <select id="display-input">
                <option value="block" ${styles.display === 'block' ? 'selected' : ''}>Block</option>
                <option value="flex" ${styles.display === 'flex' ? 'selected' : ''}>Flex</option>
            </select>
            <label>Padding (Token):</label>
            <select id="padding-input">
                ${Object.keys(DesignTokens.spacing).map(key =>
                    `<option value="${DesignTokens.spacing[key]}" ${styles.padding === DesignTokens.spacing[key] ? 'selected' : ''}>${key} (${DesignTokens.spacing[key]})</option>`
                ).join('')}
            </select>

            <h4>Appearance</h4>
            <label>Text Color:</label>
            <input type="color" value="${this._rgbToHex(styles.color) || '#000000'}" id="color-input">
            <label>Background:</label>
            <input type="color" value="${this._rgbToHex(styles.backgroundColor) || '#ffffff'}" id="bg-input">
            <label>Border Radius (Token):</label>
            <select id="radius-input">
                ${Object.keys(DesignTokens.radius).map(key =>
                    `<option value="${DesignTokens.radius[key]}" ${styles.borderRadius === DesignTokens.radius[key] ? 'selected' : ''}>${key} (${DesignTokens.radius[key]})</option>`
                ).join('')}
            </select>

            <button id="save-node">Save</button>
        `;

        document.getElementById('save-node').addEventListener('click', () => {
            const newContent = document.getElementById('content-input').value;
            const newDisplay = document.getElementById('display-input').value;
            const newPadding = document.getElementById('padding-input').value;
            const newColor = document.getElementById('color-input').value;
            const newBg = document.getElementById('bg-input').value;
            const newRadius = document.getElementById('radius-input').value;

            this.tree.updateNode(this.selectedId, {
                content: newContent,
                styles: {
                    ...styles,
                    display: newDisplay,
                    padding: newPadding,
                    color: newColor,
                    backgroundColor: newBg,
                    borderRadius: newRadius
                }
            });

            if (this.onUpdateCallback) this.onUpdateCallback();
        });
    }

    // Helper to convert rgb string to hex (basic)
    _rgbToHex(rgb) {
        if (!rgb) return null;
        if (rgb.startsWith('#')) return rgb;
        // Simple case: handling basic color names or already hex
        return rgb; // Needs expansion for real RGB
    }
}
