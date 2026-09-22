import { CanvasRenderer } from './CanvasRenderer.js';
import { NodeTree } from './NodeTree.js';
import { Inspector } from '../inspector/Inspector.js';
import { HistoryManager } from '../history/HistoryManager.js';
import { UpdateNodeCommand } from '../history/commands/UpdateNodeCommand.js';

document.addEventListener('DOMContentLoaded', () => {
    const canvasElement = document.getElementById('canvas');
    const inspectorElement = document.getElementById('sidebar-right');
    const toolbarActions = document.getElementById('toolbar-actions');
    const currentPageId = new URLSearchParams(window.location.search).get('id') || '1';

    const defaultData = {
        pages: [{
            id: "page_home",
            root: [{
                id: "node_1",
                tagName: "div",
                content: "",
                styles: {
                    desktop: {
                        padding: "32px",
                        background: "linear-gradient(135deg, rgba(103,232,249,0.10), rgba(139,92,246,0.14))",
                        border: "1px solid rgba(103,232,249,0.18)",
                        borderRadius: "22px",
                        boxShadow: "0 24px 48px rgba(4,10,18,0.35)",
                        color: "#edf5ff",
                        display: "block"
                    },
                    tablet: { padding: "20px" },
                    mobile: { padding: "14px" }
                },
                children: [
                    {
                        id: "node_2",
                        tagName: "h1",
                        content: "Build better digital experiences.",
                        styles: {
                            desktop: {
                                color: "#edf5ff",
                                fontSize: "42px",
                                margin: "0 0 12px",
                                letterSpacing: "-0.06em"
                            }
                        }
                    },
                    {
                        id: "node_3",
                        tagName: "p",
                        content: "Shape content, refine layout, and publish a clean website directly from a structured visual editor.",
                        styles: {
                            desktop: {
                                color: "#a4b7d3",
                                fontSize: "18px",
                                lineHeight: "1.7",
                                margin: "0"
                            }
                        }
                    }
                ]
            }]
        }]
    };

    const tree = new NodeTree(defaultData);
    const renderer = new CanvasRenderer(canvasElement);
    const history = new HistoryManager();

    const renderCurrent = () => {
        renderer.render(tree.data, inspector.activeBreakpoint);
    };

    const savePage = async () => {
        try {
            const response = await fetch('/api/pages/save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: Number(currentPageId),
                    document_json: tree.data
                })
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                console.error('Save failed:', result);
                return;
            }

            console.log('Page saved successfully', result);
        } catch (error) {
            console.error('Save error:', error);
        }
    };

    const loadPage = async () => {
        try {
            const response = await fetch(`/api/pages/load.php?id=${currentPageId}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                console.log('No saved page found, using default data');
                renderCurrent();
                return;
            }

            const pageData = result.data && result.data.document_json ? result.data.document_json : result.data;
            if (pageData && pageData.pages) {
                tree.data = pageData;
                renderCurrent();
            } else {
                renderCurrent();
            }
        } catch (error) {
            console.error('Load error:', error);
            renderCurrent();
        }
    };

    const onNodeUpdate = (nodeId, newProps) => {
        const command = new UpdateNodeCommand(tree, nodeId, newProps);
        history.execute(command);
        renderCurrent();
    };

    const inspector = new Inspector(inspectorElement, tree, onNodeUpdate);

    loadPage();

    toolbarActions?.addEventListener('click', async (event) => {
        const action = event.target.closest('[data-action]')?.dataset.action;
        if (!action) return;

        if (action === 'undo') {
            history.undo();
            renderCurrent();
        }

        if (action === 'redo') {
            history.redo();
            renderCurrent();
        }

        if (action === 'publish') {
            alert('Publishing functionality is under development.');
        }
    });

    canvasElement.addEventListener('click', (event) => {
        const nodeId = event.target.dataset.id;
        if (nodeId) {
            inspector.selectNode(nodeId);
        }
    });

    console.log('Editor initialized with History Engine');
});
