# Phase 3: Styling Engine Implementation Plan

Implementation of the complete Styling Engine, connecting Design Tokens and expanding Inspector functionality.

## 1. Objectives
- **Design Token Integration**: Connect `Tokens.js` to the Inspector and model, replacing hardcoded values with system defaults.
- **Spec §9 Expansion**: Complete the Inspector to support typography, spacing, borders, shadows, effects, and accessibility.

## 2. Approach
- **Tokens**: Expand `editor/js/Tokens.js` to full spec per Section 11.
- **Component Registry**: Update `ComponentRegistry.js` schemas to define which properties use tokens.
- **Inspector**: Update `Inspector.js` to dynamically generate dropdowns for token-based fields (spacing/colors/radius) and inputs for other visual properties.
- **Renderer**: The renderer already maps `node.styles` JSON directly to element style properties; this remains efficient and correct.

## 3. Implementation Steps:
1.  **Tokens**: Full expansion of `editor/js/Tokens.js`.
2.  **Registry**: Add `appearance` and `typography` categories to component schemas in `ComponentRegistry.js`.
3.  **UI/Interaction**:
    - Update `Inspector.js` renderer to handle `token` type inputs (dropdowns) vs `color`, `text`, `number` inputs.
    - Implement the specific property controls defined in Section 9 of the Documentation.
4.  **Verification**: Confirm inspector changes successfully update the JSON document model and that the canvas reactively updates using the new styles.
