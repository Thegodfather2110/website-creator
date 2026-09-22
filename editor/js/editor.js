import { CanvasRenderer } from './CanvasRenderer.js';
import { NodeTree } from './NodeTree.js';
import { Inspector } from '../inspector/Inspector.js';
import { getResponsiveStyle } from './Responsive.js';

document.addEventListener('DOMContentLoaded', () => {
    const canvasElement = document.getElementById('canvas');
    const inspectorElement = document.getElementById('sidebar-right');

    const initialData = {
        pages: [{
            id: "page_home",
            root: [{
                id: "node_1",
                tagName: "div",
                content: "Website Creator Engine - Canvas",
                styles: {
                    desktop: { "padding": "20px", "border": "1px solid blue", "display": "block", "backgroundColor": "#ffffff" },
                    tablet: { "padding": "10px" },
                    mobile: { "padding": "5px" }
                },
                children: [
                    {
                        id: "node_2",
                        tagName: "h1",
                        content: "Hello from the Canvas Engine",
                        styles: { desktop: { "color": "darkblue" } }
                    }
                ]
            }]
        }]
    };

    const tree = new NodeTree(initialData);
    const renderer = new CanvasRenderer(canvasElement);

    // We update the inspector to trigger render with current breakpoint
    const onUpdate = () => {
        const bp = inspector.activeBreakpoint;
        renderer.render(tree.data, bp);
    };

    const inspector = new Inspector(inspectorElement, tree, onUpdate);

    // Initial render
    renderer.render(tree.data, 'desktop');

    canvasElement.addEventListener('click', (e) => {
        const nodeId = e.target.dataset.id;
        if (nodeId) {
            inspector.selectNode(nodeId);
        }
    });

    console.log("Editor initialized");
});
