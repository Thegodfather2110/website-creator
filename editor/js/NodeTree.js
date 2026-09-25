// NodeTree.js - Client-side Document Model Management
export class NodeTree {
    constructor(initialData) {
        this.data = initialData;
    }

    // Searches all pages to find a node by ID
    findNode(id) {
        if (!this.data.pages) return null;
        for (const page of this.data.pages) {
            if (page.id === id) return page; // Page itself might be selected
            const found = this._search(page.root || [], id);
            if (found) return found;
        }
        return null;
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

    findNodeWithParent(id) {
        if (!this.data.pages) return null;
        for (const page of this.data.pages) {
            const found = this._searchWithParent(page.root || [], id, null, -1);
            if (found) return found;
        }
        return null;
    }

    _searchWithParent(nodes, id, parentId, index) {
        for (let i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) {
                return { node: nodes[i], parentId, index: i };
            }
            if (nodes[i].children) {
                const found = this._searchWithParent(nodes[i].children, id, nodes[i].id, i);
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

    addNode(parentId, newNode) {
        const parent = this.findNode(parentId);
        if (parent) {
            if (!parent.children) parent.children = [];
            parent.children.push(newNode);
        }
    }

    deleteNode(id) {
        for (const page of this.data.pages) {
            if (this._deleteRecursive(page.root || [], id)) return true;
        }
        return false;
    }

    _deleteRecursive(nodes, id) {
        for (let i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) {
                nodes.splice(i, 1);
                return true;
            }
            if (nodes[i].children) {
                if (this._deleteRecursive(nodes[i].children, id)) return true;
            }
        }
        return false;
    }
}
