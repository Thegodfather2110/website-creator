# Phase 2: Layout/Component Engine Implementation Plan

Implementation of the Layout Engine for the Website Creator Engine (Section 7 of doc, Phase 2 of Roadmap).

## 1. Objectives
- Establish a `ComponentRegistry` to manage node types (Container, Section, Text, Heading, Image, Button, Grid).
- Define property schemas for each component (editable fields, children constraints, defaults).
- Make the `Inspector` dynamic based on the selected component's schema.

## 2. Approach
- **Component Registry**: Create `editor/js/components/ComponentRegistry.js` to store definitions.
- **Component Definitions**: Each node type will define: `name`, `tagName`, `schema` (for Inspector controls), `defaultData`, and `allowedChildren`.
- **Dynamic Inspector**: Refactor `Inspector.js` to read the properties from the schema rather than having hardcoded `input` fields for everything.

## 3. Implementation Steps:
1.  **Component Definitions**: Define objects for each component (e.g., `Container`, `Button`, `Section`).
2.  **Registry Initialization**: Create `ComponentRegistry.js` that centralizes these and the mapping of Node `type` -> `Component`.
3.  **Refactor Inspector**: Update `Inspector.js` to clear its innerHTML and map over the `schema` of the selected node type to generate inputs.
4.  **Renderer Augmentation**: Update `CanvasRenderer.js` to use the `ComponentRegistry` to create elements, supporting `allowedChildren` validation.
