// DeleteNodeCommand.js - Command to delete a node
import { Command } from '../Command.js';

export class DeleteNodeCommand extends Command {
    constructor(tree, nodeId) {
        super();
        this.tree = tree;
        this.nodeId = nodeId;
        this.deletedNode = null;
        this.parentId = null;
        this.index = -1;
    }

    execute() {
        // Find the node and its parent before deleting
        const result = this.tree.findNodeWithParent(this.nodeId);
        if (result) {
            this.deletedNode = result.node;
            this.parentId = result.parentId;
            this.index = result.index;
            this.tree.removeNode(this.nodeId);
        }
    }

    undo() {
        if (this.deletedNode && this.parentId !== null) {
            const parent = this.tree.findNode(this.parentId);
            if (parent && parent.children) {
                parent.children.splice(this.index, 0, this.deletedNode);
            }
        }
    }
}
