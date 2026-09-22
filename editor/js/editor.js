import { CanvasRenderer } from './CanvasRenderer.js';
import { NodeTree } from './NodeTree.js';
import { Inspector } from './components/Inspector.js';

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
                styles: { "padding": "20px", "border": "1px solid blue" },
                children: [
                    {
                        id: "node_2",
                        tagName: "h1",
                        content: "Hello from the Canvas Engine",
                        styles: { "color": "darkblue" }
                    }
                ]
            }]
        }]
    };

    const tree = new NodeTree(initialData);
    const renderer = new CanvasRenderer(canvasElement);

    // Pass re-render callback to inspector
    const onUpdate = () => renderer.render(tree.data);
    const inspector = new Inspector(inspectorElement, tree, onUpdate);

    // Initial render
    renderer.render(tree.data);

    // Add click handler for selection
    canvasElement.addEventListener('click', (e) => {
        const nodeId = e.target.dataset.id;
        if (nodeId) {
            inspector.selectNode(nodeId);
        }
    });

    console.log("Editor initialized");
});
