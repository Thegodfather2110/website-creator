// Command.js - Base Class for Mutations
export class Command {
    execute() { throw new Error("Execute not implemented"); }
    undo() { throw new Error("Undo not implemented"); }
}
