# Website Creator Editor Development Plan

This plan outlines the implementation of the requested features for the editor page.

## Phase 1: Editor Foundation
- **Goal:** Get the editor to load the project document and render it in the canvas.
- **Tasks:**
  - Initialize `editor.js` with project ID from query params.
  - Fix `CanvasRenderer.js` to build the DOM from the JSON structure loaded by `PageService`.
  - Establish state management between `NodeTree` and the canvas.

## Phase 2: Section Library ("Choose Sections")
- **Goal:** Allow users to drag and drop pre-defined sections into the document.
- **Tasks:**
  - Create `editor/panels/SectionManager.js`.
  - Define section structure (Hero, About, etc.) with default JSON for each.
  - Implement drag-and-drop from the Section Panel to the `CanvasRenderer`.
  - Update `NodeTree` when a new section is added.

## Phase 3: Customization ("Customize")
- **Goal:** Edit node properties (styles, content, etc.).
- **Tasks:**
  - Enhance `editor/inspector/Inspector.js`.
  - Add form input types (color pickers, font selects, sliders).
  - Create commands in `editor/history/` to enable Undo/Redo support for all visual changes.

## Phase 4: Connectivity & Responsive ("Connect" & "Preview")
- **Goal:** Handle links, forms, and breakpoint switching.
- **Tasks:**
  - Add property editing for links/forms in `Inspector.js`.
  - Complete `editor/js/Responsive.js` for breakpoint switching.
  - Implement mobile/tablet style overrides in `CanvasRenderer.js` based on current breakpoint.

## Phase 5: Publishing Engine ("Publish")
- **Goal:** Export functionality for website deployment.
- **Tasks:**
  - Define the static HTML/CSS generator based on the document model.
  - Implement `api/publish/` endpoint to create build versions.

---

## Next Immediate Step (Phase 1)
Upon approval, I will work on **Phase 1: Editor Foundation** to ensure the canvas loads and renders correctly before we add library components.
