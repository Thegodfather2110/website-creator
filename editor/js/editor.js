import { CanvasRenderer } from './CanvasRenderer.js';
import { NodeTree } from './NodeTree.js';
import { Inspector } from '../inspector/Inspector.js';
import { HistoryManager } from '../history/HistoryManager.js';
import { UpdateNodeCommand } from '../history/commands/UpdateNodeCommand.js';

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
    const history = new HistoryManager();

    // Inspector callback now uses HistoryManager
    const onNodeUpdate = (nodeId, newProps) => {
        const command = new UpdateNodeCommand(tree, nodeId, newProps);
        history.execute(command);
        renderer.render(tree.data, inspector.activeBreakpoint);
    };

    const inspector = new Inspector(inspectorElement, tree, onNodeUpdate);

    // Initial render
    renderer.render(tree.data, 'desktop');

    // Add Undo/Redo buttons
    const undoBtn = document.createElement('button');
    undoBtn.textContent = 'Undo';
    undoBtn.onclick = () => { history.undo(); renderer.render(tree.data, inspector.activeBreakpoint); };
    document.body.appendChild(undoBtn);

    canvasElement.addEventListener('click', (e) => {
        const nodeId = e.target.dataset.id;
        if (nodeId) {
            inspector.selectNode(nodeId);
        }
    });

    console.log("Editor initialized with History Engine");
});
