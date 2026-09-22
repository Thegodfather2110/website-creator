// UpdateNodeCommand.js - Command to handle node updates
import { Command } from '../Command.js';

export class UpdateNodeCommand extends Command {
    constructor(tree, nodeId, newProps) {
        super();
        this.tree = tree;
        this.nodeId = nodeId;
        this.newProps = newProps;
        // Capture old state for undo
        this.oldProps = {};
        const node = this.tree.findNode(nodeId);
        if (node) {
            Object.keys(newProps).forEach(key => {
                this.oldProps[key] = node[key];
            });
        }
    }

    execute() {
        this.tree.updateNode(this.nodeId, this.newProps);
    }

    undo() {
        this.tree.updateNode(this.nodeId, this.oldProps);
    }
}
