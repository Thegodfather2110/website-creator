# Website Creator Editor Roadmap

## Phase 1: Foundation (Loading & Canvas)
*   **Goal:** Initialize editor with a loadable project document.
*   **Tasks:**
    *   Update `public/editor.php` to fetch project JSON via API.
    *   Initialize `NodeTree` with the loaded JSON.
    *   Initialize `CanvasRenderer` to display the initial document state.
    *   Setup `HistoryManager` to track mutations.

## Phase 2: Choose Sections (Block Library)
*   **Goal:** Add a panel to drag-and-drop website blocks.
*   **Tasks:**
    *   Create `editor/panels/SectionManager.js`.
    *   Define section templates (Hero, About, etc. as JSON structures).
    *   Implement drag-and-drop from the Section Panel to the `Canvas`.
    *   Hook into `AddNodeCommand` to insert sections into the `NodeTree`.

## Phase 3: Customize (Inspector Integration)
*   **Goal:** Edit properties (styles, content) of selected sections/nodes.
*   **Tasks:**
    *   Implement selection state in `editor/js/editor.js`.
    *   Sync selected node with `Inspector.js`.
    *   Create UI for Colors, Typography, Spacing.
    *   Map inspector inputs to `UpdateNodeCommand` for real-time updates.

## Phase 4: Preview (Responsive Engine)
*   **Goal:** Preview on Desktop, Tablet, Mobile.
*   **Tasks:**
    *   Use existing `editor/js/Responsive.js` to manage breakpoints.
    *   Update `CanvasRenderer` to apply breakpoint-specific styles.
    *   Add responsive toolbar to the editor UI.

## Phase 5: Connect & Publish
*   **Goal:** Interact with links/forms and deploy the site.
*   **Tasks:**
    *   Add interaction settings to `Inspector` (link input, form action).
    *   Develop API to save document state.
    *   Implement `PublishingService` to generate static HTML/CSS from the document JSON.
