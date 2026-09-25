# Website Creator Engine — Master Project Documentation

**Type:** Complete Overview + Development Plan + Audit Report
**Stack:** PHP 8 · MySQL/MariaDB · Vanilla JavaScript (ES Modules) · HTML5/CSS3
**Audit basis:** `Website_Creator_Engine_Documentation.docx` (product spec) cross-checked line-by-line against the actual `website creator/` codebase.
**Status at time of writing:** Early-stage prototype. Backend auth/data layer is partially solid; the visual editor (drag-drop, node selection, save, publish) is largely non-functional due to a small number of root-cause bugs.

---

## Table of Contents

1. Project Overview
2. System Architecture
3. Technology Stack
4. Complete Folder & File Structure
5. Page-by-Page Reference (every page/route)
6. API Endpoint Reference
7. Database Schema Reference
8. Feature Reference (spec vs. reality)
9. Master Development Plan
10. Full Bug & Error List
11. Missing Files, Endpoints & Tables
12. Security Checklist
13. Recommended Fix Priority

---

## 1. Project Overview

Website Creator Engine is a **visual, browser-based website builder**. The product idea (per the spec doc) is not "edit raw HTML" — it's a structured **Website Document Model** (JSON) that the visual editor reads and writes. The canvas is a *view* of that JSON; published HTML/CSS/JS is *generated from* that JSON, not hand-edited.

```
USER ACTION → EDITOR ENGINE → WEBSITE DOCUMENT MODEL → RENDERER → PUBLISHED WEBSITE
```

### What the product is meant to do
- Let a non-developer build a multi-page, responsive website by dragging sections/components onto a canvas.
- Let them edit content, typography, colors, spacing, and CSS through an Inspector panel — per breakpoint (desktop/tablet/mobile).
- Manage pages, assets (images/files), and reusable templates.
- Save work automatically/on-demand, support undo/redo, and eventually publish a static (or PHP-backed) build.
- Optionally generate sections/copy via an AI layer, always producing validated document-model JSON — never raw trusted code.

### Current reality in one sentence
The **account system, database schema, and JSON document model are solid and mostly match the spec** — but the **editor itself cannot currently add, select, edit, or save a node** due to a handful of concrete bugs, and **drag-and-drop, publishing, asset management, and page management are either unwired or unimplemented**, despite scaffolding existing for most of them.

---

## 2. System Architecture

### 2.1 Intended architecture (per spec)

```
┌───────────────────────────────────────────────────────────────┐
│                        WEBSITE CREATOR                        │
├───────────────────────────────────────────────────────────────┤
│  Project Manager   │   Visual Editor   │   Preview / Publish  │
├───────────────────────────────────────────────────────────────┤
│                 EDITOR APPLICATION LAYER                      │
│ Selection • Drag/Drop • Layers • Inspector • History          │
├───────────────────────────────────────────────────────────────┤
│                 WEBSITE DOCUMENT MODEL                        │
│ Pages • Nodes • Styles • Assets • Components • Breakpoints    │
├──────────────────────────────┬────────────────────────────────┤
│        RENDER ENGINE         │        VALIDATION ENGINE       │
├──────────────────────────────┴────────────────────────────────┤
│                         PHP API LAYER                          │
│ Auth • Projects • Pages • Assets • Versions • Publishing       │
├───────────────────────────────────────────────────────────────┤
│                       MySQL / MariaDB                          │
└───────────────────────────────────────────────────────────────┘
```

### 2.2 Actual architecture as implemented

```
Browser (static HTML pages, each with inline <style>, no shared layout)
    │
    ├── public/*.php  (landing, auth, dashboard, page-manager, templates — mostly static markup)
    ├── public/editor.php (the actual visual editor shell)
    │        └── editor/js/editor.js → NodeTree, CanvasRenderer, Inspector, HistoryManager
    │
    ▼
api/*.php  (JSON endpoints, each requires api/init.php → app/bootstrap.php)
    │
    ▼
app/Core (Auth, Database, Session, Sanitizer, ApiResponse)
app/Services (PageService, ProjectService, AssetService, TemplateService,
              CommentService, CollaborationService, FormService, AiService,
              PublishingService)
app/Models (Page)
    │
    ▼
MySQL (schema.sql + 4 separate, manually-run migration files)
```

**Key architectural deviation:** the spec (Section 15) asks for a clean separation — SQL access in models/services, business rules in services, HTTP handling in API endpoints. In practice, some endpoints (`pages/save.php`, `pages/load.php`) skip the service/model layer entirely and run raw SQL inline, while others (`pages/list.php`) do it correctly. This inconsistency is one of several signs the codebase was built in disconnected passes rather than end-to-end.

### 2.3 Request flow that *should* happen vs. what *actually* happens

| Step | Spec / intended | Actual |
|---|---|---|
| User lands on `/` | Sees marketing page, logs in | ✅ Works |
| Clicks "Launch editor" | Opens *their* most recent project/page | ❌ Always opens hardcoded `?id=1`, regardless of who's logged in |
| Registers a new account | Auto-logged in, a starter project+page created, redirected into it | ⚠️ Backend does this correctly (`register.php` returns a real `redirect` URL) — but the frontend JS in `auth.php` **ignores** that value and always redirects to `/public/dashboard.php` |
| Dashboard shows their projects | Real project list from DB | ❌ Fully hardcoded mock data (fake counts, 3 fake project cards) |
| Drags "Hero" onto canvas | A hero section node is added to the page | ❌ No drag/drop code exists anywhere; clicking the sidebar item also does nothing |
| Clicks a canvas element to edit it | Inspector shows its properties | ❌ Always shows "Error: Node not found" (data-shape bug, see §10) |
| Clicks "Save" | Page JSON persists to DB | ❌ Save button has no click handler wired |
| Clicks "Publish" | Static build generated, versioned, activated | ❌ Shows `alert('under development')`; no backend path is wired |

---

## 3. Technology Stack

| Layer | Technology | Notes |
|---|---|---|
| Backend language | PHP 8 (namespaced, PSR-4-style autoloading) | Custom autoloader in `app/bootstrap.php`, no Composer |
| Database | MySQL / MariaDB via PDO | Prepared statements used consistently (good) |
| Frontend | Vanilla JavaScript, ES Modules (`type="module"`) | No framework, no build step, no bundler |
| Styling | Hand-written CSS, inline `<style>` blocks per page | No shared design system CSS file across pages — each `public/*.php` duplicates its own CSS variables and rules |
| Fonts | Google Fonts (`Outfit`) loaded via `<link>` | Consistent across pages |
| Session/Auth | PHP native sessions, `password_hash`/`password_verify` | No CSRF tokens, no rate limiting |
| No JS framework, no CSS framework, no ORM, no test suite, no CI config | — | Confirmed absent anywhere in the repo |

---

## 4. Complete Folder & File Structure

```
website creator/
├── index.php                          Landing page (marketing + login state)
├── info.php                           ⚠️ phpinfo() left exposed — DELETE before deploy
├── schema.sql                         Base DB schema (users, projects, pages, assets, versions, published_builds)
├── EDITOR_PLAN.md                     Internal planning notes
├── PLANNING.md                        Internal planning notes
├── WEBSITE_CREATOR_REMEDIATION_PLAN.md  Prior audit/remediation notes (partially actioned)
├── Website_Creator_Engine_Documentation.md   Markdown copy of the product spec
│
├── app/
│   ├── bootstrap.php                  Master bootstrap: config + autoloader + url() helper
│   ├── Core/
│   │   ├── ApiResponse.php            JSON response helper (send/success/error) — solid
│   │   ├── Auth.php                   register/login/logout/isLoggedIn — solid
│   │   ├── Database.php               PDO singleton — solid
│   │   ├── Sanitizer.php              ⚠️ weak: strip_tags only, no attribute sanitization
│   │   └── Session.php                ⚠️ regenerate() called before start() in Auth flows
│   ├── Models/
│   │   └── Page.php                   getById / updateDocument / listByProject — only partly used
│   ├── Renderers/
│   │   └── DocumentRendererInterface.php   Interface with **zero implementers**
│   ├── Services/
│   │   ├── AiService.php              ⚠️ placeholder — does not call any real AI model
│   │   ├── AssetService.php           ⚠️ uploads with no type/MIME validation
│   │   ├── CollaborationService.php   No API endpoint calls it — orphaned
│   │   ├── CommentService.php         Used by api/comments/index.php
│   │   ├── FormService.php            No API endpoint calls it — orphaned
│   │   ├── PageService.php            Used by api/pages/list.php
│   │   ├── ProjectService.php         Used by register.php — solid
│   │   ├── PublishingService.php      ⚠️ Never called by anything; XSS + style bugs inside it
│   │   └── TemplateService.php        Used by api/templates/list.php
│   └── Validators/
│       └── AiValidator.php            ⚠️ 0 bytes — empty stub
│
├── api/
│   ├── bootstrap.php                  ⚠️ dead duplicate of app/bootstrap.php — unused, unreferenced
│   ├── init.php                       Real shared init used by every endpoint (loads app/bootstrap.php)
│   ├── ai/generate.php                POST — returns AiService placeholder output
│   ├── assets/upload.php              POST — uploads a file, no validation
│   ├── auth/login.php                 POST — login
│   ├── auth/register.php              POST — register + auto-login + default project
│   ├── comments/index.php             GET/POST — list/add comments (GET has no auth check)
│   ├── pages/list.php                 GET — list pages for a project (uses PageService, has auth check)
│   ├── pages/load.php                 GET — ⚠️ NO auth check at all (anyone can read any page)
│   ├── pages/save.php                 POST — ⚠️ auth check but NO ownership check (IDOR)
│   └── templates/list.php             GET — list templates (needs `templates` table from a migration)
│
├── public/
│   ├── index.php                      Just redirects to `/`
│   ├── auth.php                       Real login/register UI, POSTs to api/auth/*
│   ├── dashboard.php                  ⚠️ Fully static/mock project list
│   ├── editor.php                     The actual visual editor shell — ⚠️ no auth check
│   ├── page-manager.php               ⚠️ Fully static mock UI, no JS at all
│   ├── page-save-test.php             Dev-only raw JSON save/load tester — should not ship
│   └── templates.php                  ⚠️ Fully static mock UI, no JS at all
│
├── editor/
│   ├── canvas/Canvas.js               CanvasRenderer — renders `page.root` recursively (correct)
│   ├── css/editor.css                 Editor shell styling
│   ├── history/
│   │   ├── Command.js                 Base class — fine
│   │   ├── HistoryManager.js          Undo/redo stack — fine in isolation
│   │   └── commands/
│   │       ├── AddNodeCommand.js      ⚠️ Never instantiated anywhere (no add-node UI path)
│   │       ├── DeleteNodeCommand.js   ⚠️ Calls tree.removeNode() which doesn't exist — will throw
│   │       └── UpdateNodeCommand.js   Used by Inspector — fine, but only reachable if selection works
│   ├── inspector/Inspector.js         Renders property form — only reachable if NodeTree.findNode works (it doesn't)
│   ├── js/
│   │   ├── CanvasRenderer.js          1-line re-export of Canvas.js
│   │   ├── editor.js                  Main editor entry point — Save button unwired, no drag/drop
│   │   ├── NodeTree.js                ⚠️ Root cause bug: searches `.children` instead of `.root`
│   │   ├── Responsive.js              getResponsiveStyle() breakpoint cascade — correct
│   │   └── Tokens.js                  Hardcoded design tokens object — not connected to any UI
│   └── panels/
│       ├── AssetManager.js            Written, but never imported by editor.js; also calls a missing endpoint
│       └── PageManager.js             Written, but never imported by editor.js
│
├── views/
│   └── header.php                     ⚠️ Orphaned — never `require`'d anywhere; links to a logout.php that doesn't exist
│
├── database/
│   ├── migrate_add_username.sql       Adds `username` to `users` (needed — see §7)
│   ├── migrate_collaboration.sql      Adds `project_members`, `comments`
│   ├── migrate_forms.sql              Adds `form_submissions`
│   └── migrate_templates.sql          Adds `templates`
│
├── config/
│   └── config.php                     DB config from env vars, sane defaults
│
└── plans/
    └── editor_roadmap.md              Internal roadmap notes
```

---

## 5. Page-by-Page Reference

Each entry: **Route → Purpose → Auth required? → Current state**

| Route | Purpose | Auth? | Current state |
|---|---|---|---|
| `GET /` (`index.php`) | Marketing landing page; shows Login/Register or Dashboard/Continue links depending on session | No | Renders correctly. All "editor" links hardcode `?id=1` instead of the logged-in user's actual page |
| `GET /public/auth.php` | Combined login + register UI (tab-switching form) | Redirects to dashboard if already logged in | Fully functional — posts JSON to `api/auth/login.php` / `register.php`. **Bug:** ignores the `redirect` URL the register API returns, always sends the user to `/public/dashboard.php` |
| `GET /public/dashboard.php` | Should show the user's real projects, template gallery, recent activity | Yes (redirects to auth.php if not logged in) | Static mock only — hardcoded numbers and 3 fake project cards. "New project" / template "Use template" buttons have no click handlers |
| `GET /public/editor.php?id=` | The visual editor — canvas, component sidebar, inspector, toolbar | **None** — no login check at all | Loads and renders the default/loaded document, but: no drag/drop, Save button unwired, node selection broken (see §10), Publish is a stub alert |
| `GET /public/page-manager.php` | Should list/manage a project's pages, slugs, SEO, status | Yes | Fully static mock — one hardcoded "Home" page, no fetch calls, "Save changes"/"Open in editor" buttons do nothing |
| `GET /public/templates.php` | Should show template gallery pulled from `templates` table | Yes | Fully static mock — 3 hardcoded template cards, "Use template"/"Preview" buttons do nothing |
| `GET /public/page-save-test.php` | Developer-only raw JSON tester for the save/load API | Yes | Works (talks to the real API), but is a debug tool that should not ship to production, and inherits the auth/ownership bugs of `pages/save.php`/`load.php` |
| `GET /public/index.php` | Legacy redirect stub | — | Just 302-redirects to `/` — effectively dead, safe to delete |
| `views/header.php` | Was meant to be a shared header partial | — | **Not included anywhere** in the codebase. Also references `/api/auth/logout.php`, which doesn't exist |
| `GET /info.php` | Leftover debug file | **None** | Calls `phpinfo()` — publicly exposes server configuration. **Remove immediately** |

---

## 6. API Endpoint Reference

| Method & Path | Purpose | Auth check | Ownership check | Notes |
|---|---|---|---|---|
| `POST /api/auth/register.php` | Create account, auto-login, create default project+page | N/A | N/A | ✅ Solid: validates email/username/password length, hashes password, returns real `redirect` URL (unused by frontend) |
| `POST /api/auth/login.php` | Log in by email or username | N/A | N/A | ✅ Solid |
| — *(missing)* `api/auth/logout.php` | Log out | — | — | ❌ **Does not exist.** `Auth::logout()` exists in code but nothing calls it |
| `GET /api/pages/list.php?project_id=` | List pages for a project | ✅ Checked | ❌ Doesn't confirm the project belongs to the caller | Uses `PageService` correctly |
| `GET /api/pages/load.php?id=` | Load one page's document JSON | ❌ **No auth check at all** | ❌ None | Anyone can read any page by ID |
| `POST /api/pages/save.php` | Save a page's document JSON | ✅ Checked | ❌ **No ownership check** | Any logged-in user can overwrite any page by ID (IDOR) |
| — *(missing)* `api/pages/create.php` | Create a new page in a project | — | — | `PageService::createPage()` exists but no endpoint calls it |
| — *(missing)* `api/pages/delete.php` | Delete a page | — | — | `PageService::deletePage()` exists but no endpoint calls it |
| `POST /api/assets/upload.php` | Upload a file to a project | ✅ Checked | ❌ None | ⚠️ No file type/extension/MIME validation — arbitrary file upload |
| — *(missing)* `api/assets/list.php` | List a project's assets | — | — | `AssetService::listAssets()` exists; `editor/panels/AssetManager.js` calls this exact URL and gets a 404 |
| — *(missing)* `api/assets/delete.php` | Delete an asset | — | — | No service method or endpoint exists |
| `GET /api/templates/list.php` | List available templates | None (public) | N/A | Requires the `templates` table, which is **not** in `schema.sql` — only in `database/migrate_templates.sql` |
| `GET/POST /api/comments/index.php` | List/add comments on a node | GET: none / POST: checked | N/A | GET is fully public; POST uses `$_SESSION['user_id']` directly instead of the `Auth`/`Session` abstraction |
| — *(missing)* `api/collaboration/*` | Manage project members/roles | — | — | `CollaborationService` exists, fully orphaned — no endpoint anywhere |
| — *(missing)* `api/forms/submit.php` | Handle a published site's form submission | — | — | `FormService::handleSubmission()` exists, fully orphaned |
| `POST /api/ai/generate.php` | AI section/content generation | ✅ Checked | N/A | ⚠️ `AiService` is a hardcoded placeholder, not a real model call; validation step is a comment, not code (`AiValidator.php` is empty) |
| — *(missing)* `api/publish/*` and `api/projects/*` | Publish/unpublish, project CRUD (create/rename/duplicate/archive/delete), versions | — | — | None of Section 16's documented `/api/projects/*` or `/api/projects/{id}/publish` endpoints exist. `api/publish/` is an empty folder |

---

## 7. Database Schema Reference

### 7.1 Tables defined in `schema.sql` (base install)
- `users` (id, username\*, email, password_hash, role, created_at)
- `projects` (id, user_id, name, slug, status, created_at, updated_at)
- `pages` (id, project_id, name, slug, document_json, seo_json, created_at, updated_at)
- `assets` (id, project_id, path, mime_type, metadata_json, created_at, updated_at)
- `versions` (id, project_id, version_number, document_json, created_by, created_at)
- `published_builds` (id, project_id, version_id, output_path, created_at)

\* `username` is actually added by `migrate_add_username.sql`, not present in the base `CREATE TABLE users` — **run that migration or registration will fail** on a fresh install using only `schema.sql`, because `Auth::register()` inserts into a `username` column.

### 7.2 Tables that only exist in separate migration files (must be run manually — no migration runner exists)
| Migration file | Table(s) added | Used by |
|---|---|---|
| `migrate_add_username.sql` | `users.username` column | `Auth::register()`, `Auth::login()` — **required for the app to function at all** |
| `migrate_templates.sql` | `templates` | `api/templates/list.php`, `public/templates.php` (once wired) |
| `migrate_collaboration.sql` | `project_members`, `comments` | `CollaborationService`, `CommentService` |
| `migrate_forms.sql` | `form_submissions` | `FormService` |

### 7.3 Gap vs. spec
`versions` and `published_builds` tables exist in the schema, but **nothing in the codebase ever writes to them** — there is no version-snapshot-on-save logic and no publish pipeline that creates a build row.

---

## 8. Feature Reference — Spec vs. Reality

| Feature (from product spec) | Spec section | Status | Detail |
|---|---|---|---|
| Registration / Login / Logout | §5, §25 | 🟡 Partial | Register + login work well; **logout has no endpoint or UI link** |
| Dashboard: real project listing | §5 | 🔴 Not implemented | Static mock data only |
| Project Manager (create/rename/duplicate/archive/delete) | §5, §16 | 🔴 Not implemented | No `/api/projects/*` endpoints exist at all |
| Canvas rendering of a document | §8 | 🟢 Working | `Canvas.js` correctly renders `page.root` recursively with responsive styles |
| Drag-and-drop components onto canvas | §8.3 | 🔴 Not implemented | Zero drag/drop code anywhere (no `draggable`, no drag event listeners) |
| Click-to-select a node | §8.2 | 🔴 Broken | Selection fires, but `NodeTree.findNode()` searches the wrong field (`children` instead of `root`) and always fails |
| Inspector: edit content/layout/typography/colors | §9 | 🔴 Broken | Only reachable after selection succeeds, which it doesn't (see above) |
| Responsive breakpoint editing (desktop/tablet/mobile) | §10 | 🟡 Partial | The cascade logic (`Responsive.js`) is correct and unit-correct; can't be exercised through the UI due to the selection bug; **would also break on publish** (see below) |
| Design tokens / global theme | §11 | 🔴 Not implemented | `Tokens.js` defines a hardcoded token object not connected to any UI, save, or render logic |
| Multi-page site structure, slugs, SEO fields | §12 | 🔴 Not implemented | `page-manager.php` is static; `pages` table has `seo_json` column but nothing reads/writes it |
| Asset upload/manager | §13 | 🔴 Broken | Upload endpoint works but has no validation; `AssetManager.js` UI exists but isn't wired into the editor and calls a missing `list.php` endpoint |
| Save (autosave or manual) | §26 | 🔴 Broken | `savePage()` exists in `editor.js` but the Save button's click handler never calls it |
| Undo / Redo | §20 | 🟡 Partial | `HistoryManager` logic is correct; buttons are wired; but since nodes can't currently be added/edited through the UI, there's nothing meaningful to undo yet. Delete command is bugged (calls a non-existent method) |
| Publishing engine (build, activate, static output) | §18 | 🔴 Not implemented | Publish button is a stub alert; `PublishingService::compile()` exists but is never called; if it were called, it would emit broken CSS (flattens the per-breakpoint style object incorrectly) and unescaped content (XSS) |
| Export (project JSON / HTML-CSS-JS ZIP) | §21 | 🔴 Not implemented | No export code anywhere |
| Custom HTML/CSS/JS blocks | §19 | 🔴 Not implemented | No UI or backend support found |
| Forms & submissions | §13/§5 | 🔴 Not implemented | `FormService` exists; no endpoint, no form-node type, no submission UI |
| Comments on nodes | §24 | 🟡 Partial | Backend (`CommentService` + `api/comments/index.php`) works; **no UI in the editor** consumes it |
| Collaboration (project members/roles) | §24 | 🔴 Not implemented | `CollaborationService` exists; zero endpoints, zero UI |
| AI generation | §22 | 🔴 Placeholder only | `AiService` returns a hardcoded template string, doesn't call any model |
| AI output validation boundary | §23 | 🔴 Not implemented | `AiValidator.php` is a 0-byte file |
| Password hashing | §25 | 🟢 Working | `password_hash`/`password_verify`, correct |
| CSRF protection | §25 | 🔴 Not implemented | No CSRF tokens anywhere |
| Prepared SQL statements | §25 | 🟢 Working | Used consistently throughout |
| Upload validation (MIME/extension/SVG sanitization) | §25 | 🔴 Not implemented | `AssetService::uploadAsset()` accepts any file type |
| Rate limiting | §25 | 🔴 Not implemented | None anywhere |
| Authorization on every resource | §25 | 🔴 Broken | `pages/load.php` has no auth at all; `pages/save.php` has no ownership check |
| Output escaping / XSS prevention | §25 | 🔴 Broken | `PublishingService` interpolates raw content into HTML; `Sanitizer::sanitizeHtml()` doesn't strip dangerous attributes |
| Secure session cookies / session handling | §25 | 🟡 Partial | `session_regenerate_id()` is called before the session has been started in the register/login flow, so fixation protection likely doesn't actually apply |

**Legend:** 🟢 Working as specified · 🟡 Partially working / inconsistent · 🔴 Not implemented or broken

---

## 9. Master Development Plan

This reframes the spec's Phase 0–11 roadmap against what's actually done, so it can be used as a real backlog.

### Phase 0 — Foundation *(≈70% done)*
- [x] Project structure, autoloading, config
- [x] Database schema (base tables)
- [x] Authentication (register/login)
- [ ] Logout endpoint + UI link
- [ ] Consolidate `api/bootstrap.php` into `app/bootstrap.php` (remove the dead duplicate)
- [ ] Fold the 4 separate `database/*.sql` migrations into `schema.sql`, or add a real migration runner

### Phase 1 — Core Editor *(≈25% done — the priority phase)*
- [ ] **Fix `NodeTree` to search `page.root`, not `page.children`** — this single fix unblocks selection, editing, add, delete, and undo/redo simultaneously
- [ ] Wire the Save button (`data-action="save"`) to the existing `savePage()` function
- [ ] Implement drag-and-drop: `draggable="true"` + `dragstart` on sidebar `.component-item`s, `dragover`/`drop` on `#canvas`, insertion position calculation, commit via `AddNodeCommand`
- [ ] Fix `DeleteNodeCommand` to call `tree.deleteNode()` (the method that actually exists) instead of `tree.removeNode()`
- [ ] Add a delete UI path (keyboard `Delete`/`Backspace` or a toolbar button) — currently nothing triggers `DeleteNodeCommand` at all
- [ ] Load the *correct* page ID everywhere (stop hardcoding `?id=1`; use the real project/page IDs returned by the backend)

### Phase 2 — Layout Engine *(not started)*
- [ ] Container/Section/Flex/Grid component types with real property schemas (spec §7 lists component types; none currently have declared editable properties, accepted children, or defaults)

### Phase 3 — Styling Engine *(≈40% done, disconnected)*
- [x] Responsive style cascade logic (`Responsive.js`) is correct
- [x] Basic color/padding/display controls exist in `Inspector.js`
- [ ] Connect `Tokens.js` design tokens to the Inspector and to the document model instead of leaving them hardcoded and unused
- [ ] Expand Inspector fields to match spec §9 (typography, spacing, borders, shadows, effects, accessibility)

### Phase 4 — Responsive Engine *(≈50% done)*
- [x] Desktop → tablet → mobile override cascade works correctly at the render layer
- [ ] Fix `PublishingService::compileNodes()` so responsive styles compile into real CSS/media queries instead of misreading the breakpoint object as flat CSS properties

### Phase 5 — Pages & Assets *(≈15% done)*
- [ ] Wire `public/page-manager.php` to real data (`PageService`) instead of static mock
- [ ] Add `api/pages/create.php` and `api/pages/delete.php` (service methods already exist)
- [ ] Fix asset upload validation (extension/MIME allowlist, size limits, storage outside webroot or with execution disabled)
- [ ] Add `api/assets/list.php` and `api/assets/delete.php`
- [ ] Import `AssetManager.js`/`PageManager.js` into `editor.js` so the panels actually render

### Phase 6 — History *(≈70% done)*
- [x] Undo/redo stack logic correct
- [ ] Fix `DeleteNodeCommand` bug (see Phase 1)
- [ ] Add snapshot/version persistence (writes to the existing but unused `versions` table)

### Phase 7 — Publishing *(≈5% done)*
- [ ] Build real `/api/projects/{id}/publish` and `/unpublish` endpoints
- [ ] Fix `PublishingService` to escape output (XSS) and correctly compile per-breakpoint styles
- [ ] Write build output somewhere and populate `published_builds`
- [ ] Make the publish operation atomic (build first, then switch active version) per spec §18
- [ ] ZIP export

### Phase 8 — Templates *(≈10% done)*
- [x] `templates` table + `TemplateService::listTemplates()` + `api/templates/list.php` work
- [ ] Wire `public/templates.php` to that endpoint instead of static cards
- [ ] "Use template" action: clone a template's `document_json` into a new page

### Phase 9 — Advanced Web Features *(not started)*
- [ ] Forms: add a Form node type, wire `FormService` to a real `api/forms/submit.php`
- [ ] SEO: read/write the existing `seo_json` column; sitemap/robots.txt generation
- [ ] Custom code blocks with sanitization/isolation

### Phase 10 — AI *(≈5% done)*
- [ ] Replace `AiService`'s hardcoded placeholder with a real model call
- [ ] Implement `AiValidator.php` (currently empty) to actually validate AI output against the document schema before it reaches the editor, per spec §23

### Phase 11 — Collaboration *(≈15% done, orphaned)*
- [x] `CollaborationService` + `comments`/`project_members` tables exist
- [ ] Build `api/collaboration/*` endpoints
- [ ] Surface comments and members in the editor UI (currently invisible even though the backend partially works)

---

## 10. Full Bug & Error List

### 🔴 Critical (security)
1. **`api/pages/load.php` — no authentication check.** Any page's full document JSON is readable by anyone via `GET /api/pages/load.php?id=N`.
2. **`api/pages/save.php` — no ownership check.** Any logged-in user can overwrite any other user's page (IDOR); only "is *someone* logged in" is checked, not "does this page belong to them."
3. **`app/Services/AssetService.php::uploadAsset()` — no file-type/extension/MIME validation.** Combined with `mkdir(..., 0777, true)`, a malicious file (e.g. a `.php` shell) can be uploaded into a project's storage folder.
4. **`info.php` — exposes `phpinfo()` publicly**, with no auth guard. Leaks server paths, versions, and configuration.
5. **`app/Services/PublishingService.php::compileNodes()` — unescaped output.** Node `content` is concatenated directly into the generated HTML with no `htmlspecialchars()`, allowing stored XSS in any published output (once publishing is wired up, this must be fixed first).
6. **`app/Core/Sanitizer.php::sanitizeHtml()` — incomplete sanitization.** `strip_tags()` with an allowlist doesn't strip dangerous *attributes* on allowed tags (e.g., `<img onerror=...>` passes through untouched).
7. **No CSRF protection anywhere** in the codebase, despite being an explicit spec requirement (§25).
8. **No rate limiting anywhere**, including on `login.php`/`register.php` — open to brute-force.
9. **`app/Validators/AiValidator.php` is a 0-byte empty file.** AI output is never actually schema-validated, contradicting spec §23's explicit "invalid output rejected" boundary.

### 🟠 Functional bugs (breaks core editing flow)
10. **`editor/js/NodeTree.js` — wrong field name.** `_search()`/`findNode()`/`findNodeWithParent()` traverse `node.children`, but the document model stores page content under `page.root`. Result: `findNode()` can never find a real node — clicking anything on the canvas shows "Error: Node not found" in the Inspector, every time.
11. **`editor/js/editor.js` — Save button unwired.** `savePage()` is defined but the toolbar's click handler only branches on `'undo'`, `'redo'`, `'publish'` — `'save'` is never checked, so the Save button silently does nothing.
12. **Drag-and-drop does not exist.** No `draggable` attributes, no `dragstart`/`dragover`/`drop` listeners anywhere in `editor/`. There is currently no way to add a component to the canvas through the UI at all.
13. **`editor/history/commands/DeleteNodeCommand.js` calls `this.tree.removeNode(...)`**, but `NodeTree` only defines `deleteNode()`. This throws a `TypeError` the moment delete is triggered (currently unreachable since nothing calls it yet, but it will break as soon as a delete UI is added).
14. **`editor/panels/AssetManager.js::loadAssets()` fetches `/api/assets/list.php`**, which does not exist anywhere in the codebase (only `upload.php` exists) — this call will always 404.
15. **`AssetManager.js` and `PageManager.js` are never imported by `editor.js`.** Both panels are fully written but never appear in the actual editor UI.
16. **`PublishingService::compileNodes()` misreads the style object.** It treats `node.styles` as a flat `{property: value}` map and iterates it directly, but styles are actually stored per-breakpoint (`{desktop:{}, tablet:{}, mobile:{}}`). If this method is ever called, it will emit invalid CSS like `desktop: [object Object];` instead of real styles.
17. **`PublishingService::compile()` is never called by anything** — `api/publish/` is an empty folder; the Publish button is just `alert('Publishing functionality is under development.')`.

### 🟡 Missing wiring / static mock pages
18. **`public/dashboard.php`** — entirely hardcoded mock project/stat data; "New project," "Use template," "Share," "Duplicate," "Preview" buttons have no handlers.
19. **`public/page-manager.php`** — entirely static, no JavaScript, no fetch calls; "Save changes"/"Open in editor" buttons do nothing.
20. **`public/templates.php`** — entirely static, no JavaScript; "Use template"/"Preview" buttons do nothing.
21. **Every "editor" link across the app hardcodes `?id=1`, `?id=2`, or `?id=3`** instead of the actual logged-in user's project/page — including the landing page, dashboard, page manager, and template gallery.
22. **`public/auth.php` ignores the `redirect` field** that `api/auth/register.php` correctly returns (a real, working URL into the new user's actual page); it always hardcodes a redirect to `/public/dashboard.php` instead.
23. **`public/editor.php` has no authentication check at all.** Anyone, logged in or not, can open the editor shell directly.
24. **`views/header.php` is never `require`'d anywhere** — dead/orphaned file. It also links to `/api/auth/logout.php`, which doesn't exist.

### 🟡 Architecture / consistency issues
25. **`api/bootstrap.php` duplicates `app/bootstrap.php`'s autoloader** and is no longer referenced by anything — dead code.
26. **`api/pages/save.php` and `load.php` bypass `App\Models\Page`/`PageService`**, using inline raw SQL instead, while `api/pages/list.php` correctly uses the service layer — inconsistent with the spec's own stated architecture (§15).
27. **The `templates` table only exists in `database/migrate_templates.sql`**, not in the base `schema.sql`. A fresh install using only `schema.sql` will make `api/templates/list.php` fail with a missing-table error. The same is true for `project_members`, `comments`, `form_submissions` (migration-only), and — critically — the `users.username` column, without which registration itself fails.
28. **`app/Core/Session::regenerate()` is called before `Session::start()`** in both `Auth::register()` and `Auth::login()` (since `start()` only happens inside `set()`, which runs *after* `regenerate()`). `session_regenerate_id()` on a session that hasn't started typically fails silently — session-fixation protection likely isn't actually taking effect.
29. **`app/Services/AiService.php` doesn't call any real AI model** — it returns a hardcoded placeholder string built from the prompt, per its own inline comment ("In a real scenario, this would call the Claude API").
30. **`app/Renderers/DocumentRendererInterface.php` has zero implementers** anywhere in the codebase — an aspirational interface with nothing behind it.
31. **`api/comments/index.php` GET has no auth check** (anyone can read a page's comments); its POST handler reads `$_SESSION['user_id']` directly instead of going through the `Session`/`Auth` classes, breaking the abstraction consistently used elsewhere.

---

## 11. Missing Files, Endpoints & Tables — Consolidated Checklist

### Missing PHP endpoints (referenced by frontend JS or documented, but absent)
- [ ] `api/auth/logout.php`
- [ ] `api/assets/list.php` *(called by `AssetManager.js`, 404s today)*
- [ ] `api/assets/delete.php`
- [ ] `api/pages/create.php`
- [ ] `api/pages/delete.php`
- [ ] `api/projects/*` — full CRUD (`list`, `create`, `get`, `update`, `delete`, `duplicate`, `archive`) per spec §16
- [ ] `api/projects/{id}/publish.php`, `api/projects/{id}/unpublish.php`
- [ ] `api/projects/{id}/versions` (list/create)
- [ ] `api/collaboration/*` (members list/add/remove/role)
- [ ] `api/forms/submit.php`

### Missing/incomplete database objects (relative to a fresh `schema.sql`-only install)
- [ ] `users.username` column (in a separate migration; registration fails without it)
- [ ] `templates` table (separate migration)
- [ ] `project_members`, `comments` tables (separate migration)
- [ ] `form_submissions` table (separate migration)
- [ ] No migrations tracking table/runner — all 4 extra `.sql` files must be applied manually, in order, by hand

### Missing/empty implementation files
- [ ] `app/Validators/AiValidator.php` — 0 bytes, needs real schema-validation logic
- [ ] A real implementer of `DocumentRendererInterface.php`
- [ ] Any drag-and-drop module (e.g. an `editor/canvas/DragDrop.js` was never created)
- [ ] Any project-management JS/UI (project creation, rename, duplicate, archive, delete all missing on the frontend)
- [ ] Export functionality (project JSON export, HTML/CSS/JS ZIP export) — no code at all

### Files that exist but are effectively dead code (safe to delete or must be reconnected)
- [ ] `api/bootstrap.php` (superseded by `app/bootstrap.php`)
- [ ] `views/header.php` (never included)
- [ ] `public/index.php` (redundant redirect to `/`)
- [ ] `info.php` (debug leftover — security risk, delete)
- [ ] `public/page-save-test.php` (dev tool — gate behind an environment check or remove before production)

---

## 12. Security Checklist (per spec §25, current status)

| Requirement | Status |
|---|---|
| Password hashing with a modern algorithm | ✅ Done (`password_hash`/`password_verify`) |
| CSRF protection for session-authenticated mutations | ❌ Missing entirely |
| Prepared SQL statements | ✅ Done consistently |
| Strict upload validation (MIME/extension checks) | ❌ Missing entirely |
| SVG sanitization | ❌ Missing (no SVG-specific handling found) |
| Rate limiting on auth and public APIs | ❌ Missing entirely |
| Authorization checks on every project resource | ❌ Broken — `load.php` has none, `save.php` has no ownership check |
| Secure session cookies | 🟡 Default PHP session handling; regenerate-before-start bug undermines fixation protection |
| Output escaping to prevent XSS | ❌ Broken in `PublishingService`; `Sanitizer` incomplete |
| Isolation of custom code | N/A — custom code feature not built yet |
| Backups and recovery procedures | ❌ Not present anywhere in the repo |

---

## 13. Recommended Fix Priority

**Week 1 — Make the editor actually usable**
1. Fix `NodeTree` (`root` vs `children`) — unblocks selection/edit/delete/undo/redo in one change
2. Wire the Save button to `savePage()`
3. Implement basic drag-and-drop (sidebar → canvas)
4. Fix `DeleteNodeCommand.removeNode()` → `deleteNode()`
5. Add a logout endpoint + link

**Week 2 — Close the security gaps**
6. Add auth check to `pages/load.php`
7. Add ownership checks to `pages/save.php` and `pages/list.php`
8. Add file-type/MIME/size validation to asset upload; move uploads outside the web-executable path
9. Delete `info.php`; gate or delete `page-save-test.php`
10. Add CSRF tokens to all session-mutating POST endpoints

**Week 3 — Connect the disconnected pieces**
11. Wire `dashboard.php`, `page-manager.php`, `templates.php` to real API data
12. Fix every hardcoded `?id=1` link to use the real, logged-in user's project/page
13. Import `AssetManager.js` / `PageManager.js` into the editor
14. Add the missing `assets/list.php`, `assets/delete.php`, `pages/create.php`, `pages/delete.php` endpoints

**Week 4+ — Build out the remaining phases**
15. Publishing pipeline (fix `PublishingService`, wire a real `/publish` endpoint, write to `published_builds`)
16. Project CRUD API + UI
17. Forms, SEO fields, templates "use template" flow, AI validation, collaboration UI

---

*End of document. This reflects a direct audit of the uploaded `website_creator.zip` codebase against `Website_Creator_Engine_Documentation.docx`, performed by reading and cross-referencing the actual source files rather than assuming the spec was implemented.*
