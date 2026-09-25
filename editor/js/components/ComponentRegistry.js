import { DesignTokens } from '../Tokens.js';

export const ComponentRegistry = {
    components: {
        'container': {
            tagName: 'div',
            allowedChildren: ['container', 'text', 'heading', 'image', 'button'],
            schema: {
                padding: { type: 'token', label: 'Padding', options: DesignTokens.spacing },
                backgroundColor: { type: 'color', label: 'Background Color' },
                display: { type: 'select', label: 'Display', options: {'block': 'block', 'flex': 'flex'} }
            }
        },
        'text': {
            tagName: 'p',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Text Content' },
                fontSize: { type: 'token', label: 'Font Size', options: DesignTokens.typography.fontSize },
                color: { type: 'color', label: 'Text Color' }
            }
        },
        'heading': {
            tagName: 'h2',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Heading Text' },
                fontSize: { type: 'token', label: 'Size', options: DesignTokens.typography.fontSize }
            }
        },
        'button': {
            tagName: 'button',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Button Text' },
                url: { type: 'text', label: 'URL' },
                borderRadius: { type: 'token', label: 'Border Radius', options: DesignTokens.radius }
            }
        }
    },
    get(type) {
        return this.components[type] || this.components['container'];
    },
    canAcceptChild(parentType, childType) {
        const parent = this.components[parentType];
        return parent && parent.allowedChildren.includes(childType);
    }
};
