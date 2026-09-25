export const ComponentRegistry = {
    components: {
        'container': {
            tagName: 'div',
            allowedChildren: ['container', 'text', 'heading', 'image', 'button'],
            schema: {
                padding: { type: 'text', label: 'Padding' },
                backgroundColor: { type: 'color', label: 'Background Color' },
                display: { type: 'select', label: 'Display' }
            }
        },
        'text': {
            tagName: 'p',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Text Content' },
                fontSize: { type: 'text', label: 'Font Size' }
            }
        },
        'heading': {
            tagName: 'h2',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Heading Text' },
                level: { type: 'select', label: 'Level' }
            }
        },
        'image': {
            tagName: 'img',
            allowedChildren: [],
            schema: {
                src: { type: 'text', label: 'Source URL' },
                alt: { type: 'text', label: 'Alt Text' }
            }
        },
        'button': {
            tagName: 'button',
            allowedChildren: [],
            schema: {
                content: { type: 'text', label: 'Button Text' },
                url: { type: 'text', label: 'URL' }
            }
        },
        'grid': {
            tagName: 'div',
            allowedChildren: ['container'],
            schema: {
                columns: { type: 'number', label: 'Columns' },
                gap: { type: 'text', label: 'Gap' }
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
