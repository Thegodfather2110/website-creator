// NodeTree.js - Client-side Document Model Management
export class NodeTree {
    constructor(initialData) {
        this.data = initialData;
    }

    findNode(id) {
        // Recursive search for a node by ID
        return this._search(this.data.pages, id);
    }

    _search(nodes, id) {
        for (const node of nodes) {
            if (node.id === id) return node;
            if (node.children) {
                const found = this._search(node.children, id);
                if (found) return found;
            }
        }
        return null;
    }

    updateNode(id, props) {
        const node = this.findNode(id);
        if (node) {
            Object.assign(node, props);
            return true;
        }
        return false;
    }
}
