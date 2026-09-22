# Phase 11: Collaboration Implementation Plan

Implementation of Collaboration features for the Website Creator Engine (Section 24).

## 1. Objectives
- Support project sharing (inviting collaborators).
- Enable node-specific comments for design review.
- Implement basic role-based access control (RBAC).

## 2. Approach
- **Backend Service**: `app/Services/CollaborationService.php` for handling project membership, and `app/Services/CommentService.php` for threaded comments.
- **Database**: 
    - `project_members`: Define roles (Owner, Editor, Viewer).
    - `comments`: Annotate nodes (`page_id`, `node_id`, `user_id`, `content`).
- **Interaction**: UI panels for "Collaborators" management and "Comments/Review" mode.

## 3. Implementation Steps:
1.  **Database Migration**:
    - Add `project_members` and `comments` tables.
2.  **Backend Services**:
    - Build `app/Services/CollaborationService.php` (invite, list, remove members).
    - Build `app/Services/CommentService.php` (CRUD for comments linked to nodes).
3.  **API Layer**:
    - Create `api/projects/{id}/collaborators` and `api/pages/{id}/comments`.
4.  **UI/Tooling**:
    - Add UI to the Editor to view and add comments to specific canvas nodes.
5.  **Security**:
    - Ensure `Auth::isLoggedIn()` and Project ownership checks are enforced on all collaboration API endpoints.
