# Phase 3: Styling Engine Implementation Plan

Implementation of the Styling Engine for the Website Creator Engine.

## 1. Objectives
Extend the Inspector and Renderer to support:
- Typography: font family, size, weight, line-height.
- Appearance: colors (background, text), borders (radius, width, color).
- Advanced: shadows, opacity.

## 2. Approach
- **Global Theme Tokens**: Start by defining a simple token structure in the `editor/js/editor.js` initially, then expand.
- **Inspector Updates**: Update `editor/js/components/Inspector.js` to create input fields for these new properties.
- **Renderer Updates**: Update `editor/js/CanvasRenderer.js` to map these JSON properties into CSS styles applied to elements.

## 3. Implementation Steps:
1.  **Update Document Model structure**: Ensure `styles` in the node definition can store these new properties.
2.  **Update `CanvasRenderer.js`**: Expand the `_createNodeElement` method (or helper) to map JSON style keys to CSS style properties.
3.  **Update `Inspector.js`**: Add UI controls (color pickers, text inputs) for typography and appearance.
4.  **Integration**: Ensure updates in the Inspector correctly mutate the `styles` object in the NodeTree, triggering the re-render.
