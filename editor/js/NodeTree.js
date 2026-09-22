// NodeTree.js - Client-side Document Model Management
export class NodeTree {
    constructor(initialData) {
        this.data = initialData;
    }

    findNode(id) {
        return this._search(this.data.pages, id);
    }

    findNodeWithParent(id, nodes = this.data.pages, parentId = null, index = -1) {
        for (let i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) {
                return { node: nodes[i], parentId, index: i };
            }
            if (nodes[i].children) {
                const found = this.findNodeWithParent(id, nodes[i].children, nodes[i].id, i);
                if (found) return found;
            }
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
        this._deleteRecursive(this.data.pages, id);
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
