# Website Creator --- Stability, Routing, Auth & UX Remediation Plan

**Repository reviewed:** `website creator/`\
**Primary entry point reviewed:** `index.php`\
**Stack:** PHP + MySQL + HTML + CSS + JavaScript\
**Review focus:** routing, redirects, login/register, buttons/actions,
editor flow, API boundaries, UI/UX smoothness, and architectural
consistency.

------------------------------------------------------------------------

## 1. Executive Diagnosis

The project already has a strong **product architecture direction**:
document model, editor, history, responsive styling, services, APIs,
templates, assets, publishing, comments, forms and AI.

The main problem is that the **UI layer and application layer are not
yet connected as one coherent product**.

The current application behaves like a collection of prototype pages:

``` text
Landing Page
    ↓
Hard-coded links
    ↓
Auth
    ↓
Hard-coded dashboard
    ↓
Hard-coded project IDs
    ↓
Editor
    ↓
Partially implemented APIs
```

It needs to become:

``` text
Application Router
        ↓
Authentication State
        ↓
Project Context
        ↓
Dashboard
        ↓
Project
   ├── Pages
   ├── Editor
   ├── Assets
   ├── Templates
   ├── Settings
   └── Publish
        ↓
API + Database
```

### Most important conclusion

**Do not patch individual buttons one by one.**

The routing, authentication, project ownership and API response handling
need to be standardized first. Once that foundation exists, the UI
becomes much easier to make smooth.

------------------------------------------------------------------------

# 2. Critical Issues Found

## P0 --- Application bootstrap is being used incorrectly

### Problem

`public/index.php`, `public/auth.php`, `public/dashboard.php`,
`public/page-manager.php`, `public/templates.php` and other HTML pages
include:

``` php
require_once __DIR__ . '/../api/init.php';
```

But `api/init.php` immediately sends:

``` php
header('Content-Type: application/json');
```

That file is intended for API requests, not HTML pages.

### Why this is wrong

A browser page should return:

``` http
Content-Type: text/html
```

while an API should return:

``` http
Content-Type: application/json
```

The current architecture mixes the two responsibilities.

### Fix

Create a shared application bootstrap:

``` text
app/bootstrap.php
```

It should load:

-   configuration
-   database
-   authentication
-   autoloading
-   shared helpers

It should **not** automatically send JSON headers.

Then:

``` php
// HTML page
require_once __DIR__ . '/../app/bootstrap.php';
```

and:

``` php
// API endpoint
require_once __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json');
```

------------------------------------------------------------------------

# 3. P0 --- Registration does not log the user in

## Current behavior

`api/auth/register.php` calls:

``` php
Auth::register($email, $password)
```

and returns success.

The frontend then redirects to:

``` text
/public/dashboard.php
```

But registration never creates a session.

The dashboard checks:

``` php
if (!Auth::isLoggedIn()) {
    header('Location: /public/auth.php');
    exit;
}
```

Therefore the actual flow becomes:

``` text
Register
  ↓
Account inserted
  ↓
Frontend says "success"
  ↓
Redirect dashboard
  ↓
No session
  ↓
Redirect auth
```

This is one of the main reasons the authentication flow feels broken.

## Fix

After successful registration:

``` php
if (Auth::register($email, $password)) {
    Auth::login($email, $password);

    ApiResponse::success([
        'message' => 'Account created successfully',
        'redirect' => '/dashboard'
    ], 201);
}
```

Better:

``` php
$user = Auth::register($email, $password);

if ($user) {
    Auth::loginById($user['id']);

    ApiResponse::success([
        'redirect' => '/dashboard'
    ], 201);
}
```

The backend should own the authentication state.

------------------------------------------------------------------------

# 4. P0 --- API error response and frontend error handling are inconsistent

The API returns:

``` json
{
    "error": "Invalid credentials"
}
```

but successful responses return:

``` json
{
    "success": true,
    "data": {}
}
```

The frontend currently does:

``` javascript
statusEl.textContent = data.error || 'Something went wrong.';
```

This works for the current error response, but the overall API contract
is inconsistent.

## Recommended universal contract

Success:

``` json
{
    "success": true,
    "data": {},
    "message": "Operation successful"
}
```

Error:

``` json
{
    "success": false,
    "error": {
        "code": "INVALID_CREDENTIALS",
        "message": "Email or password is incorrect."
    }
}
```

Frontend:

``` javascript
if (!result.success) {
    throw new Error(result.error?.message || result.message);
}
```

This should be used everywhere.

------------------------------------------------------------------------

# 5. P0 --- Root `index.php` routing is fragile

Current:

``` php
header('Location: /public/index.php', true, 302);
exit;
```

This assumes the application is installed at the domain root.

If the project is running locally as:

``` text
http://localhost/website-creator/
```

then:

``` text
/public/index.php
```

points to:

``` text
http://localhost/public/index.php
```

instead of:

``` text
http://localhost/website-creator/public/index.php
```

## Recommended solution

Do not hard-code the deployment root.

Create:

``` text
config/app.php
```

with:

``` php
return [
    'base_url' => '/website-creator',
];
```

Better still, configure this through an environment variable.

Then create:

``` text
app/helpers/url.php
```

with:

``` php
function url(string $path = ''): string
{
    global $config;

    return rtrim($config['base_url'], '/') . '/' . ltrim($path, '/');
}
```

Use:

``` php
<a href="<?= url('/dashboard') ?>">
```

instead of:

``` php
<a href="/public/dashboard.php">
```

------------------------------------------------------------------------

# 6. P0 --- There is no real application router

The application currently exposes internal files directly:

``` text
/public/index.php
/public/auth.php
/public/dashboard.php
/public/editor.php
/public/page-manager.php
/public/templates.php
```

This makes the product feel like a collection of PHP files rather than
one application.

## Recommended routes

Use clean URLs:

``` text
/
 /login
 /register
 /dashboard
 /projects
 /projects/{id}
 /projects/{id}/editor
 /projects/{id}/pages
 /projects/{id}/assets
 /templates
 /settings
```

APIs:

``` text
/api/auth/login
/api/auth/register
/api/auth/logout

/api/projects
/api/projects/{id}

/api/pages
/api/pages/{id}

/api/assets
/api/templates
/api/publish
```

Internally, PHP files can remain modular, but users should not see
implementation filenames.

------------------------------------------------------------------------

# 7. P0 --- Editor has no authentication guard

`public/editor.php` currently does not check:

``` php
Auth::isLoggedIn()
```

A user can directly access:

``` text
/public/editor.php?id=1
```

without being authenticated.

Worse, the editor itself does not verify that the page belongs to the
current user.

## Fix

At the top:

``` php
require_once __DIR__ . '/../app/bootstrap.php';

if (!Auth::isLoggedIn()) {
    header('Location: ' . url('/login?redirect=' . urlencode($_SERVER['REQUEST_URI'])));
    exit;
}
```

Then validate:

``` text
page → project → current user
```

before loading anything.

------------------------------------------------------------------------

# 8. P0 --- Page loading has an authorization vulnerability

Current:

``` php
SELECT * FROM pages WHERE id = ?
```

There is no check that the page belongs to the logged-in user.

That means knowing another page ID may allow access to its document.

The same problem exists conceptually for saving:

``` php
UPDATE pages SET document_json = ? WHERE id = ?
```

There is no ownership condition.

## Required query

Use:

``` sql
SELECT p.*
FROM pages p
JOIN projects pr ON pr.id = p.project_id
WHERE p.id = ?
AND pr.user_id = ?
```

For saving:

``` sql
UPDATE pages p
JOIN projects pr ON pr.id = p.project_id
SET p.document_json = ?
WHERE p.id = ?
AND pr.user_id = ?
```

Every project-owned resource must follow this rule.

------------------------------------------------------------------------

# 9. P0 --- Hard-coded project and page IDs

The dashboard repeatedly uses:

``` text
editor.php?id=1
editor.php?id=2
editor.php?id=3
```

This is prototype data.

It should become:

``` text
/projects/{projectId}/editor
```

and the database should determine the actual project.

The dashboard should load:

``` sql
SELECT * FROM projects
WHERE user_id = ?
ORDER BY updated_at DESC
```

instead of displaying:

``` text
Launch Page
Portfolio
Agency Site
```

as hard-coded HTML.

------------------------------------------------------------------------

# 10. P0 --- "Create new project" does not actually create a project

Buttons labelled:

-   New project
-   Create new
-   Start creating
-   Continue project
-   Open editor

mostly route to:

``` text
editor.php?id=1
```

That is not a creation flow.

## Correct flow

``` text
Create new
   ↓
POST /api/projects
   ↓
Create project
   ↓
Create default Home page
   ↓
Return project ID
   ↓
Redirect /projects/{id}/editor
```

Example response:

``` json
{
    "success": true,
    "data": {
        "project_id": 27,
        "page_id": 51
    }
}
```

Then:

``` javascript
window.location.href = `/projects/27/editor`;
```

------------------------------------------------------------------------

# 11. P1 --- Many buttons are visually interactive but functionally dead

Examples found in the dashboard:

``` text
Share
Preview
Duplicate
Use template
```

These are currently plain:

``` html
<button type="button">
```

with no behavior.

This creates a major UX problem: the interface promises functionality
that does not exist.

## Fix

Every button should be one of:

1.  Real navigation.
2.  Real action.
3.  Disabled with an explicit reason.
4.  Removed until implemented.

Do not leave fake interactive controls in production UI.

------------------------------------------------------------------------

# 12. P1 --- Template buttons are not connected

`public/templates.php` contains:

``` html
<button>Use template</button>
<button>Preview</button>
```

but no action is attached.

Recommended:

``` text
Preview
   ↓
/templates/{templateId}/preview

Use template
   ↓
POST /api/projects
template_id = X
   ↓
New project created
   ↓
Editor opens
```

------------------------------------------------------------------------

# 13. P1 --- Editor Publish button does nothing

The editor toolbar contains:

``` html
<button data-action="publish">Publish</button>
```

but `editor.js` only handles:

``` text
undo
redo
save
```

There is no publish implementation.

## Fix

Implement:

``` text
Publish
 ↓
Save
 ↓
Validate document
 ↓
Create version
 ↓
Compile
 ↓
Generate build
 ↓
Activate build
 ↓
Return public URL
```

Then show a publish confirmation/toast.

------------------------------------------------------------------------

# 14. P1 --- Editor does not properly load a project

The editor uses:

``` javascript
const currentPageId =
    new URLSearchParams(window.location.search).get('id') || '1';
```

This means the URL parameter is interpreted as a page ID.

But the surrounding UI calls it a project.

The system needs a clear distinction:

``` text
projectId
pageId
```

Never overload one `id` parameter for both.

Use:

``` text
/projects/12/editor?page=44
```

or:

``` text
/projects/12/editor/44
```

------------------------------------------------------------------------

# 15. P1 --- Canvas always renders the first page

`Canvas.js` does:

``` javascript
const rootNodes = documentModel.pages[0].root || [];
```

This ignores the active page.

If the project contains:

``` text
Home
Services
About
```

the renderer still uses:

``` text
pages[0]
```

## Fix

Maintain:

``` javascript
activePageId
```

Then:

``` javascript
const page = documentModel.pages.find(
    p => p.id === activePageId
);

const rootNodes = page?.root || [];
```

------------------------------------------------------------------------

# 16. P1 --- Page Manager is incomplete

`PageManager.js` displays:

``` text
Add Page
Home
Services
About
```

but:

-   Add Page has no handler.
-   Page selection is only partially wired.
-   There is no page creation API.
-   There is no delete/rename/duplicate flow.
-   The editor does not appear to connect PageManager to Canvas
    rendering.

The page system needs to become a real application subsystem.

------------------------------------------------------------------------

# 17. P1 --- Asset Manager references a missing endpoint

`editor/panels/AssetManager.js` calls:

``` text
/api/assets/list.php
```

but the repository list does not contain:

``` text
api/assets/list.php
```

The upload endpoint exists, but listing does not.

## Fix

Implement:

``` text
GET /api/assets
POST /api/assets
DELETE /api/assets/{id}
```

and return project-owned assets only.

------------------------------------------------------------------------

# 18. P1 --- Editor UI architecture is too primitive for the product vision

The current editor has:

``` text
Components
Pages
Canvas
Inspector
```

but the actual interactions are still minimal.

For the product you described, the editor should evolve toward:

``` text
┌──────────────────────────────────────────────────────────────┐
│ Logo | Project | Undo | Redo | Preview | Save | Publish     │
├────────────┬─────────────────────────────────┬───────────────┤
│ Components │                                 │ Inspector     │
│             │                                 │               │
│ Layout      │             CANVAS              │ Content       │
│ Typography  │                                 │ Layout        │
│ Media       │                                 │ Typography    │
│ Forms       │                                 │ Appearance    │
│ Sections    │                                 │ Responsive    │
│ Advanced    │                                 │ Interactions  │
├────────────┴─────────────────────────────────┴───────────────┤
│ Layers | Pages | Assets | Desktop | Tablet | Mobile | Zoom  │
└──────────────────────────────────────────────────────────────┘
```

------------------------------------------------------------------------

# 19. P1 --- UI styling is repeated across PHP files

Large portions of CSS are embedded directly in:

``` text
public/index.php
public/auth.php
public/dashboard.php
public/templates.php
public/page-manager.php
```

This makes global design changes difficult.

## Fix

Create:

``` text
public/assets/css/
    tokens.css
    base.css
    components.css
    landing.css
    auth.css
    dashboard.css
    templates.css
    editor.css
```

Then use global design tokens:

``` css
:root {
    --color-bg: #07111f;
    --color-surface: #101c2c;
    --color-text: #edf5ff;
    --color-muted: #a4b7d3;
    --color-primary: #67e8f9;
    --color-accent: #8b5cf6;

    --radius-sm: 10px;
    --radius-md: 14px;
    --radius-lg: 24px;

    --ease: cubic-bezier(.22,1,.36,1);
}
```

------------------------------------------------------------------------

# 20. P1 --- UI needs a proper interaction system

The visual design is already directionally strong, but smoothness needs
to be systemic.

Implement:

### Button states

``` text
default
hover
active
focus
loading
success
error
disabled
```

### Page transitions

Use subtle:

``` css
opacity
transform
filter
```

rather than aggressive animations.

### Feedback

Every asynchronous action should provide:

``` text
Saving...
Saved ✓
Publishing...
Published ✓
Error — Retry
```

### Toast system

Create one reusable:

``` javascript
toast.success('Project saved');
toast.error('Unable to publish');
toast.info('Uploading image...');
```

Do not use random `alert()` calls.

------------------------------------------------------------------------

# 21. P1 --- Auth UI should feel like a real application

Current auth is visually good but functionally basic.

Add:

``` text
Login
Register
Forgot password
Password visibility
Password strength
Email validation
Loading state
Inline errors
Success state
Redirect preservation
Session-aware navigation
```

Recommended login flow:

``` text
User opens /login
       ↓
Enters credentials
       ↓
Button becomes "Signing in..."
       ↓
API request
       ↓
Success
       ↓
Small success animation
       ↓
Dashboard
```

If a user originally tried to open:

``` text
/projects/12/editor
```

while logged out:

``` text
/login?redirect=%2Fprojects%2F12%2Feditor
```

After login:

``` text
→ /projects/12/editor
```

------------------------------------------------------------------------

# 22. P1 --- Registration needs proper validation

At minimum:

``` text
Valid email
Password minimum length
Password confirmation
Duplicate email handling
Rate limiting
Password strength indicator
```

The database already has a unique email constraint, but the user should
receive a clean application message rather than a raw SQL exception.

Example:

``` text
This email is already registered.
Try logging in instead.
```

------------------------------------------------------------------------

# 23. P1 --- Session handling should be centralized

Create:

``` text
app/Core/Session.php
```

Handle:

``` text
session_start()
session cookie settings
login state
logout
redirect state
session regeneration
```

On login:

``` php
session_regenerate_id(true);
```

On logout:

``` php
$_SESSION = [];
session_destroy();
```

------------------------------------------------------------------------

# 24. P1 --- Add logout

There is currently no proper logout route in the main navigation.

Create:

``` text
/api/auth/logout
```

and:

``` text
POST /api/auth/logout
```

Then redirect to:

``` text
/login
```

------------------------------------------------------------------------

# 25. P1 --- Landing page CTA logic is wrong

The landing page currently shows:

``` text
Open Studio
Continue project
Demo editor
Start creating
Launch editor
Open studio
```

and these all point to the editor.

The CTA should depend on authentication:

### Logged out

``` text
Start creating
→ /register
```

Secondary:

``` text
Already have an account?
→ /login
```

### Logged in

``` text
Open workspace
→ /dashboard
```

or:

``` text
Continue editing
→ last opened project
```

This makes the landing page feel intelligent.

------------------------------------------------------------------------

# 26. P1 --- Dashboard is static instead of user-driven

The dashboard currently displays fake statistics:

``` text
14 Projects
8 Templates
3 Drafts
```

and fake activity:

``` text
Updated landing page 2 hours ago
3 collaborators
1 build ready
```

These should eventually come from the database.

For example:

``` sql
COUNT(projects)
COUNT(status = 'draft')
COUNT(templates)
MAX(updated_at)
```

The user should see their actual workspace.

------------------------------------------------------------------------

# 27. P1 --- Missing project lifecycle

A project needs:

``` text
Create
Open
Rename
Duplicate
Archive
Delete
Restore
Publish
Unpublish
Preview
Share
Export
Settings
```

The UI should be built around that lifecycle.

------------------------------------------------------------------------

# 28. P1 --- Save system needs autosave

The current save is manual.

Recommended:

``` text
User changes something
        ↓
Mark document dirty
        ↓
Wait 800–1200 ms
        ↓
Autosave
        ↓
Saved ✓
```

Use:

``` javascript
let saveTimer;

function scheduleSave() {
    clearTimeout(saveTimer);

    saveTimer = setTimeout(() => {
        savePage();
    }, 1000);
}
```

Manual save remains available.

------------------------------------------------------------------------

# 29. P1 --- Save status should be visible

Top toolbar:

``` text
● Unsaved
Saving...
✓ Saved 2 sec ago
```

This dramatically improves user confidence.

------------------------------------------------------------------------

# 30. P1 --- Editor needs a real command architecture

The project already has:

``` text
Command
AddNodeCommand
DeleteNodeCommand
UpdateNodeCommand
HistoryManager
```

This is the correct direction.

Extend it to:

``` text
MoveNodeCommand
DuplicateNodeCommand
AddPageCommand
DeletePageCommand
UpdatePageCommand
UpdateStyleCommand
AddAssetCommand
DeleteAssetCommand
SetThemeTokenCommand
```

Every editor mutation should pass through the command system.

------------------------------------------------------------------------

# 31. P1 --- Selection system needs to be richer

Current selection is essentially:

``` javascript
event.target.dataset.id
```

Add:

``` text
hover outline
selected outline
parent breadcrumb
multi-select
keyboard navigation
delete
duplicate
copy
paste
arrow movement
```

Example:

``` text
Home
 > Hero
   > Container
     > Heading  ← selected
```

This breadcrumb should appear in the inspector.

------------------------------------------------------------------------

# 32. P1 --- Inspector has an HTML injection risk

`Inspector.js` builds HTML using:

``` javascript
value="${node.content || ''}"
```

If content contains quotes or markup, it can break the generated
inspector HTML.

Never inject raw document content directly into `innerHTML`.

Use:

``` javascript
element.value = node.content || '';
```

or properly escape all values.

------------------------------------------------------------------------

# 33. P1 --- Renderer needs semantic component rendering

Current CanvasRenderer does:

``` javascript
document.createElement(node.tagName || 'div')
```

This is a useful prototype, but the final architecture should use:

``` text
node.type
   ↓
component registry
   ↓
component renderer
```

Example:

``` javascript
ComponentRegistry.register('hero', HeroRenderer);
ComponentRegistry.register('button', ButtonRenderer);
ComponentRegistry.register('image', ImageRenderer);
```

This is the foundation for the real Website Creator Engine.

------------------------------------------------------------------------

# 34. P1 --- Renderer ignores important component behavior

Current renderer mostly applies:

``` text
tagName
content
styles
children
```

It needs future support for:

``` text
attributes
href
src
alt
events
classes
tokens
responsive styles
component props
semantic tags
visibility
interactions
```

------------------------------------------------------------------------

# 35. P1 --- Page data model needs active-page state

The document model currently contains pages, but the editor does not
maintain a proper active-page controller.

Add:

``` javascript
editorState = {
    projectId,
    activePageId,
    selectedNodeId,
    activeBreakpoint,
    dirty,
    previewMode
};
```

Keep this separate from the actual website document.

------------------------------------------------------------------------

# 36. P2 --- URL structure should be project-aware

Recommended:

``` text
/
 /login
 /register
 /dashboard

 /projects
 /projects/12
 /projects/12/editor
 /projects/12/pages
 /projects/12/assets
 /projects/12/settings

 /templates
 /templates/12

 /published/project-slug
```

This gives the entire product a predictable mental model.

------------------------------------------------------------------------

# 37. P2 --- Introduce a single URL helper

Do not repeat:

``` text
/public/editor.php
/public/dashboard.php
/api/pages/save.php
```

throughout the project.

Create:

``` php
url('/dashboard')
url('/projects/' . $projectId . '/editor')
apiUrl('/pages/save')
assetUrl($path)
```

This will eliminate a large class of routing bugs.

------------------------------------------------------------------------

# 38. P2 --- Use one application shell

The pages currently have slightly different headers, buttons and visual
conventions.

Create reusable UI components:

``` text
AppShell
Topbar
Sidebar
Button
IconButton
Card
Modal
Drawer
Toast
Tabs
Dropdown
CommandPalette
Breadcrumb
EmptyState
LoadingState
```

The application should feel like one product.

------------------------------------------------------------------------

# 39. Recommended UI/UX Direction

The existing dark glass aesthetic is a good starting direction, but the
final product should become more **editorial, calm and professional**.

Use:

``` text
Background:
#070B12

Surface:
#0E1622

Elevated:
#121D2B

Primary:
#67E8F9

Accent:
#8B5CF6

Success:
#4ADE80

Text:
#EDF5FF

Muted:
#8FA3BE
```

Do not overuse gradients.

Gradients should communicate hierarchy rather than decorate every card.

------------------------------------------------------------------------

# 40. Editor Smoothness Rules

The editor should feel like a professional desktop application.

### Use

``` text
120–180ms
```

for micro interactions.

``` text
180–280ms
```

for panels and overlays.

Use:

``` css
cubic-bezier(.22, 1, .36, 1)
```

for natural movement.

Avoid:

``` text
large bounce animations
constant glowing
excessive blur
long transitions
```

The editor should feel fast before it feels flashy.

------------------------------------------------------------------------

# 41. Loading System

Create a global loading state:

``` javascript
loading.start('Saving project...');
loading.stop();
```

Use skeletons for:

``` text
Dashboard projects
Templates
Assets
Pages
```

Use spinners only for short actions.

------------------------------------------------------------------------

# 42. Error System

Create a unified error handler:

``` javascript
async function apiRequest(url, options = {}) {
    const response = await fetch(url, options);

    let result;

    try {
        result = await response.json();
    } catch {
        throw new Error('Server returned an invalid response.');
    }

    if (!response.ok || !result.success) {
        throw new Error(
            result.error?.message ||
            result.message ||
            'Something went wrong.'
        );
    }

    return result;
}
```

Then all frontend code uses:

``` javascript
await apiRequest('/api/pages/save', {
    method: 'POST',
    ...
});
```

------------------------------------------------------------------------

# 43. API Security Rules

Every API endpoint must verify:

``` text
Authentication
+
Authorization
+
Input validation
+
Resource ownership
```

Especially:

``` text
projects
pages
assets
versions
comments
publishing
```

Do not rely on the frontend to enforce ownership.

------------------------------------------------------------------------

# 44. Database Improvements

The existing schema is a good foundation, but add indexes.

For example:

``` sql
INDEX idx_projects_user_id (user_id)
INDEX idx_pages_project_id (project_id)
INDEX idx_assets_project_id (project_id)
INDEX idx_versions_project_id (project_id)
```

For projects:

``` sql
UNIQUE KEY unique_user_project_slug (user_id, slug)
```

This allows two different users to have the same project slug while
preventing conflicts inside one user's workspace.

------------------------------------------------------------------------

# 45. File Naming Issue

The repository contains:

``` text
shcema.sql
```

Rename to:

``` text
schema.sql
```

This is minor, but consistency matters.

------------------------------------------------------------------------

# 46. Development/Test Files

The following should not be part of the normal production navigation:

``` text
public/page-save-test.php
.vscode/router.php
```

Move test utilities into:

``` text
/tests
/dev
```

or remove them from production deployment.

------------------------------------------------------------------------

# 47. Root Architecture Recommendation

Move toward:

``` text
website-creator/
│
├── app/
│   ├── Core/
│   ├── Models/
│   ├── Services/
│   ├── Validators/
│   ├── Renderers/
│   └── Helpers/
│
├── config/
│
├── database/
│
├── public/
│   ├── index.php
│   ├── assets/
│   └── published/
│
├── routes/
│
├── api/
│
├── editor/
│
├── components/
│
├── templates/
│
├── storage/
│
└── tests/
```

If possible, configure the web server's document root directly to:

``` text
/public
```

This is preferable because:

``` text
app/
config/
database/
storage/
```

are then not directly web-accessible.

------------------------------------------------------------------------

# 48. Recommended Authentication Architecture

``` text
                   AUTH
                    │
          ┌─────────┴─────────┐
          ▼                   ▼
       Login               Register
          │                   │
          ▼                   ▼
       Verify              Validate
          │                   │
          ▼                   ▼
   Regenerate Session     Create User
          │                   │
          └─────────┬─────────┘
                    ▼
               Create Session
                    │
                    ▼
                 Dashboard
```

Logout:

``` text
Dashboard
   ↓
Logout
   ↓
Destroy session
   ↓
Login
```

------------------------------------------------------------------------

# 49. Recommended Project Creation Architecture

``` text
Create Project
      ↓
Name
      ↓
Choose:
  Blank
  Template
  AI Generate
      ↓
POST /api/projects
      ↓
Create project
      ↓
Create Home page
      ↓
Return project/page IDs
      ↓
Open editor
```

This becomes the backbone of the product.

------------------------------------------------------------------------

# 50. Recommended Editor Architecture

``` text
EditorState
    │
    ├── ProjectController
    ├── PageController
    ├── SelectionController
    ├── HistoryManager
    ├── CanvasRenderer
    ├── ComponentRegistry
    ├── Inspector
    ├── AssetManager
    ├── ResponsiveController
    ├── AutosaveManager
    └── PublishManager
```

This is much cleaner than putting all behavior inside `editor.js`.

------------------------------------------------------------------------

# 51. Recommended Frontend State

``` javascript
const editorState = {
    project: null,
    document: null,

    activePageId: null,
    selectedNodeId: null,

    breakpoint: 'desktop',

    dirty: false,
    saving: false,
    publishing: false,

    previewMode: false
};
```

Then modules subscribe to state changes.

------------------------------------------------------------------------

# 52. Recommended Button Behavior

Every action should have a predictable lifecycle.

Example:

``` text
CLICK
  ↓
Validate
  ↓
Set loading
  ↓
API
  ↓
Success / Error
  ↓
Update UI
  ↓
Clear loading
```

Never allow:

``` text
button → nothing
```

------------------------------------------------------------------------

# 53. Recommended Navigation Rules

### Public

``` text
/
 /login
 /register
 /templates
 /templates/{id}/preview
```

### Authenticated

``` text
/dashboard
/projects
/projects/{id}
/projects/{id}/editor
/projects/{id}/pages
/projects/{id}/assets
/projects/{id}/settings
```

### API

``` text
/api/auth/*
/api/projects/*
/api/pages/*
/api/assets/*
/api/templates/*
/api/publish/*
```

------------------------------------------------------------------------

# 54. Redirect Rules

Centralize them.

``` text
Guest → protected page
      → /login?redirect=...

Authenticated → /login
      → /dashboard

Successful login
      → redirect target OR /dashboard

Successful register
      → redirect target OR /dashboard

Logout
      → /login

Unknown page
      → /404

Unauthorized resource
      → /403

Missing resource
      → /404
```

Do not scatter redirect strings across files.

------------------------------------------------------------------------

# 55. Exact Fix Order

Do the work in this order.

## Phase 1 --- Foundation

1.  Create `app/bootstrap.php`.
2.  Remove JSON headers from page bootstrap.
3.  Create URL/base-path helper.
4.  Standardize API response format.
5.  Centralize sessions.
6.  Rename `shcema.sql` → `schema.sql`.

## Phase 2 --- Authentication

7.  Fix registration to create a session.
8.  Add session regeneration.
9.  Add logout.
10. Add login redirect preservation.
11. Add registration validation.
12. Add duplicate-email handling.
13. Add editor authentication guard.

## Phase 3 --- Authorization

14. Add project ownership checks.
15. Add page ownership checks.
16. Add asset ownership checks.
17. Add version ownership checks.
18. Add template/project authorization.

## Phase 4 --- Routing

19. Create application routes.
20. Replace hard-coded `/public/...` URLs.
21. Replace hard-coded `/api/...` URLs in frontend.
22. Stop exposing implementation filenames.
23. Add 404/403 pages.

## Phase 5 --- Project Lifecycle

24. Implement project creation.
25. Implement project listing.
26. Implement project opening.
27. Implement duplicate.
28. Implement rename.
29. Implement archive/delete.
30. Create default Home page automatically.

## Phase 6 --- Editor

31. Separate `projectId` and `pageId`.
32. Add active page state.
33. Connect PageManager to Canvas.
34. Add real component insertion.
35. Add node selection.
36. Add move/duplicate/delete.
37. Add autosave.
38. Add save indicator.
39. Add publish action.
40. Add proper preview.

## Phase 7 --- UI/UX

41. Create shared design system.
42. Create reusable button/card/modal/toast components.
43. Add loading states.
44. Add error states.
45. Add empty states.
46. Add smooth transitions.
47. Add keyboard shortcuts.
48. Add command palette.

## Phase 8 --- Templates & Assets

49. Connect template API.
50. Implement Use Template.
51. Implement Preview.
52. Implement Asset List.
53. Implement Asset Delete.
54. Implement upload progress.

## Phase 9 --- Production

55. Secure uploads.
56. Add CSRF protection.
57. Add rate limiting.
58. Add database indexes.
59. Configure `/public` as web root.
60. Add logging.
61. Add backups.
62. Add automated tests.

------------------------------------------------------------------------

# 56. Definition of "Fixed"

The system should not be considered finished until this flow works:

``` text
OPEN WEBSITE
      ↓
Landing page
      ↓
Start Creating
      ↓
Register
      ↓
Session created
      ↓
Dashboard
      ↓
Create Project
      ↓
Choose Blank / Template / AI
      ↓
Project created
      ↓
Editor
      ↓
Add components
      ↓
Edit styles
      ↓
Switch Desktop / Tablet / Mobile
      ↓
Autosave
      ↓
Preview
      ↓
Publish
      ↓
Public Website
```

And:

``` text
Logout
 ↓
Login
 ↓
Return to previous project
```

must also work.

------------------------------------------------------------------------

# 57. The Bigger Product Direction

The current code should not be treated as a collection of pages that
need individual repairs.

The correct mental model is:

> **Website Creator is a visual compiler for the web.**

The user manipulates a structured website document.

``` text
Visual UI
    ↓
Document Model
    ↓
Validation
    ↓
Component Registry
    ↓
Renderer
    ↓
Compiler
    ↓
HTML / CSS / JS
```

That means the product can later support:

``` text
Visual Website Builder
        +
AI Website Generator
        +
Visual CMS
        +
Template Marketplace
        +
Component Marketplace
        +
Custom Domains
        +
Analytics
        +
Forms
        +
E-commerce
        +
Collaboration
        +
Visual App Builder
```

without replacing the core architecture.

------------------------------------------------------------------------

# 58. Final Priority Matrix

  Area                       Priority Current State    Action
  ------------------------ ---------- ---------------- ----------------------------------
  Bootstrap separation             P0 Incorrect        Fix immediately
  Registration session             P0 Broken flow      Fix immediately
  URL/base path                    P0 Fragile          Centralize
  Editor auth                      P0 Missing          Add
  Resource authorization           P0 Missing          Add
  Project creation                 P0 Hard-coded       Implement
  API contract                     P0 Inconsistent     Standardize
  Routing                          P0 File-based       Introduce routes
  Dashboard                        P1 Mostly static    Connect DB
  Templates                        P1 UI-only          Connect API
  Publish                          P1 Button only      Implement
  Page manager                     P1 Partial          Complete
  Asset manager                    P1 Partial          Complete
  Autosave                         P1 Missing          Implement
  Editor state                     P1 Minimal          Centralize
  UI components                    P1 Repeated CSS     Create system
  Toast/loading                    P1 Missing          Add
  Responsive editor                P1 Partial          Improve
  Security                         P1 Partial          Harden
  SEO                              P2 Future           Build after core
  Collaboration                    P2 Service exists   Integrate later
  AI                               P2 Service exists   Integrate after editor stability

------------------------------------------------------------------------

# 59. Final Recommendation

**Do not continue adding features to the current prototype until Phases
1--4 are fixed.**

The biggest improvement will not come from adding more visual effects or
more components.

It will come from making these five things extremely reliable:

``` text
1. ROUTING
2. AUTHENTICATION
3. AUTHORIZATION
4. PROJECT/PAGE STATE
5. EDITOR STATE
```

Once those are solid, the existing visual direction can be polished into
a very strong product.

The current codebase already contains several useful foundations ---
history commands, responsive handling, services, document rendering
concepts, templates, assets, AI and publishing abstractions. The next
step is **integration and architectural cleanup**, not starting over.

------------------------------------------------------------------------

## Immediate First Build Target

The first engineering milestone should be:

``` text
ROOT
 ↓
LANDING
 ↓
REGISTER / LOGIN
 ↓
SESSION
 ↓
DASHBOARD
 ↓
CREATE PROJECT
 ↓
PROJECT ID
 ↓
PAGE ID
 ↓
EDITOR
 ↓
LOAD DOCUMENT
 ↓
EDIT
 ↓
AUTOSAVE
 ↓
PUBLISH
```

Once this complete vertical slice works end-to-end, build the remaining
features on top of it.

**The objective is not merely to make every button work. The objective
is to make the application behave like one coherent product.**
