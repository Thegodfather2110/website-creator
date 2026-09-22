// AddNodeCommand.js - Command to add a node to the tree
import { Command } from '../Command.js';

export class AddNodeCommand extends Command {
    constructor(tree, parentId, newNode) {
        super();
        this.tree = tree;
        this.parentId = parentId;
        this.newNode = newNode;
    }

    execute() {
        this.tree.addNode(this.parentId, this.newNode);
    }

    undo() {
        this.tree.deleteNode(this.newNode.id);
    }
}
