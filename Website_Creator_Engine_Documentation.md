**WEBSITE CREATOR ENGINE**

_Product & Technical Architecture Documentation • v1.0_

A visual website-building platform that turns website creation into a browser-based visual programming environment. The platform is designed around a structured Website Document Model rather than directly editing generated HTML.

Core stack: HTML5 • CSS3 • JavaScript • PHP • MySQL/MariaDB

Document status: Foundational architecture / product specification

# 1\. Executive Vision

The product is not merely a drag-and-drop website builder. It is a visual programming environment for the web: users manipulate structured components, styles, layouts, interactions, pages and assets through a visual editor, while the system maintains a machine-readable website document underneath.

The central principle is simple:

```
USER ACTION → EDITOR ENGINE → WEBSITE DOCUMENT MODEL → RENDERER → PUBLISHED WEBSITE
```

The Website Document Model is the source of truth. The visual canvas is a view of that model, and published HTML/CSS/JS is an output generated from it.

# 2\. Product Goals

- Allow a non-developer to create production-quality websites visually.
- Give developers precise control over HTML, CSS and JavaScript when required.
- Keep websites portable and exportable instead of locking projects into a proprietary visual format.
- Support responsive design as a first-class concept.
- Separate editing, rendering and publishing so the system can evolve independently.
- Make templates, reusable components and design systems data-driven.
- Enable future AI website generation by generating the same Website Document Model used by the visual editor.
- Support self-hosted deployment using conventional PHP hosting or a VPS.
- Provide a path from simple landing pages to complex multi-page web applications.

# 3\. Product Philosophy

## 3.1 Visual first, code when needed

Users should be able to work visually without seeing code. Advanced users should be able to open controlled code surfaces such as custom CSS, JavaScript and HTML blocks.

## 3.2 Structured data first

Do not treat the canvas DOM as the permanent database. The application should store a structured document and reconstruct the canvas from it.

## 3.3 Components over arbitrary markup

The system should provide reliable components with known properties, constraints and rendering behavior. Raw/custom code is an escape hatch, not the foundation.

## 3.4 Responsive by design

Every visual property should have a clear inheritance and breakpoint strategy.

## 3.5 Portable output

A project should be capable of being published as static HTML/CSS/JS where possible, with PHP-backed features only where server-side behavior is actually required.

# 4\. High-Level System Architecture

```
┌───────────────────────────────────────────────────────────────┐
│                        WEBSITE CREATOR                        │
├───────────────────────────────────────────────────────────────┤
│  Project Manager   │   Visual Editor   │   Preview / Publish │
├───────────────────────────────────────────────────────────────┤
│                 EDITOR APPLICATION LAYER                      │
│ Selection • Drag/Drop • Layers • Inspector • History          │
├───────────────────────────────────────────────────────────────┤
│                 WEBSITE DOCUMENT MODEL                        │
│ Pages • Nodes • Styles • Assets • Components • Breakpoints     │
├──────────────────────────────┬────────────────────────────────┤
│        RENDER ENGINE         │        VALIDATION ENGINE       │
│ Canvas renderer              │ Schema validation              │
│ Preview renderer             │ Constraints                    │
├──────────────────────────────┴────────────────────────────────┤
│                         PHP API LAYER                          │
│ Auth • Projects • Pages • Assets • Versions • Publishing       │
├───────────────────────────────────────────────────────────────┤
│                       MySQL / MariaDB                          │
└───────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    Published Website
                    HTML + CSS + JS
                    + optional PHP
```

# 5\. Core Modules

## Authentication & Accounts

Registration, login, sessions, roles, permissions and account security.

## Dashboard

Project listing, recent projects, templates, activity and publishing status.

## Project Manager

Create, rename, duplicate, archive, export and delete websites.

## Visual Editor

Canvas, selection, drag/drop, resize, layers, keyboard commands and editing.

## Component Library

Reusable sections, layout primitives, content components and advanced blocks.

## Inspector

Contextual controls for content, layout, typography, colors, borders, effects and responsive behavior.

## Page Manager

Create pages, change slugs, navigation, SEO metadata and page settings.

## Asset Manager

Upload, search, organize and reuse images, SVGs, videos, fonts and files.

## Theme / Design System

Global colors, typography, spacing, radii, shadows, containers and reusable tokens.

## History Engine

Undo/redo, snapshots, version history and recovery.

## Renderer

Convert the document model into a visual DOM representation.

## Preview Engine

Desktop/tablet/mobile preview and isolated preview mode.

## Publishing Engine

Generate deployable output, publish, unpublish and maintain build versions.

## Custom Code

Controlled HTML/CSS/JS blocks for advanced users.

## Forms & Integrations

Form submission, email/webhook integration and future third-party connectors.

## AI Layer

Prompt-to-site, section generation, copy generation, style generation and AI-assisted editing.

# 6\. Website Document Model

The Website Document Model is the most important technical decision in the system. It should be versioned and validated.

```
{
  "schemaVersion": "1.0",
  "site": {
    "name": "Example Site",
    "theme": {},
    "settings": {}
  },
  "pages": [
    {
      "id": "page_home",
      "name": "Home",
      "slug": "/",
      "seo": {},
      "root": []
    }
  ],
  "assets": [],
  "globalStyles": {},
  "navigation": {}
}
```

Every visual object should have a stable ID. IDs allow the editor to update one object without rebuilding the entire document conceptually.

```
{
  "id": "node_hero_title",
  "type": "text",
  "content": "Build something extraordinary.",
  "styles": {
    "desktop": {
      "fontSize": "64px",
      "fontWeight": 700
    },
    "mobile": {
      "fontSize": "38px"
    }
  },
  "children": []
}
```

# 7\. Node / Component System

Every element on a page is a node. Nodes can be containers, content elements, layout elements or application-specific components.

- Container: owns children and establishes layout context.
- Section: full-width page region.
- Text: editable rich or plain text.
- Heading: semantic heading with level control.
- Image: responsive image with alt text and focal positioning.
- Button: link/action component.
- Columns/Grid: structured layout components.
- Form: fields, validation and submission behavior.
- Navigation: menu structure and responsive behavior.
- Custom HTML/CSS/JS: controlled advanced escape hatch.

Components should declare their editable properties, accepted children, defaults, semantic HTML, responsive behavior and rendering rules.

# 8\. Visual Editor

## 8.1 Editor layout

```
┌──────────────┬──────────────────────────────────┬──────────────┐
│ Components   │                                  │ Inspector    │
│              │                                  │              │
│ Layout       │            Canvas               │ Content      │
│ Typography   │                                  │ Layout       │
│ Media        │                                  │ Typography   │
│ Forms        │                                  │ Appearance   │
│ Advanced     │                                  │ Responsive   │
├──────────────┴──────────────────────────────────┴──────────────┤
│ Pages │ Layers │ Assets │ Desktop │ Tablet │ Mobile │ Zoom     │
└────────────────────────────────────────────────────────────────┘
```

## 8.2 Selection model

- Click selects a node.
- Double-click enters text editing where appropriate.
- Escape moves selection to the parent.
- Shift-click supports multi-selection.
- Selection state is editor state, not website data.
- A selected node must display its box, constraints and relevant controls.

## 8.3 Drag and drop

- Components are dragged from the library to valid drop targets.
- The system calculates insertion position before committing the change.
- Invalid parent-child relationships are blocked.
- Drop operations should be atomic so they can be undone as one action.

# 9\. Inspector & Styling System

The inspector should expose properties based on the selected component rather than showing one enormous generic form.

- Content: text, links, images, labels and component-specific data.
- Layout: display, width, height, position, flex, grid, alignment and overflow.
- Spacing: margin, padding and gap.
- Typography: font family, size, weight, line height, letter spacing and alignment.
- Appearance: colors, gradients, borders, radius, opacity and shadows.
- Effects: transforms, filters and transitions.
- Responsive: breakpoint-specific overrides.
- Accessibility: semantic element, labels, alt text, keyboard behavior.

# 10\. Responsive Architecture

Responsive behavior should use cascading overrides rather than duplicating an entire website for every device.

```
Base / Desktop
      ↓
Tablet overrides
      ↓
Mobile overrides
```

A property inherits from the nearest defined breakpoint. This keeps documents compact and makes responsive editing understandable.

# 11\. Design System

The platform should support global design tokens so users can change a site's visual language without editing hundreds of nodes.

```
{
  "tokens": {
    "colors": {
      "primary": "#00FF9D",
      "background": "#0B0D10",
      "text": "#FFFFFF"
    },
    "spacing": {
      "xs": "4px",
      "sm": "8px",
      "md": "16px",
      "lg": "32px",
      "xl": "64px"
    },
    "radius": {
      "sm": "6px",
      "md": "12px",
      "lg": "20px"
    }
  }
}
```

Components should be able to reference tokens instead of storing every visual value as a hard-coded value.

# 12\. Pages & Site Structure

- Multiple pages per website.
- Nested navigation where required.
- Custom slugs and redirects.
- Per-page SEO title and description.
- Open Graph metadata.
- Canonical URL support.
- 404 page.
- Header/footer shared regions.
- Reusable symbols/components across pages.

# 13\. Asset Management

Assets should be stored independently from page documents.

- Image upload and optimization.
- SVG support with sanitization.
- File metadata and dimensions.
- Asset search and filtering.
- Folders/tags.
- Reusable asset references.
- Future image transformation/CDN layer.
- Font upload with licensing responsibility left to the user.

# 14\. Database Architecture

```
users
 ├── id
 ├── email
 ├── password_hash
 ├── role
 └── created_at

projects
 ├── id
 ├── user_id
 ├── name
 ├── slug
 ├── status
 └── timestamps

pages
 ├── id
 ├── project_id
 ├── name
 ├── slug
 ├── document_json
 ├── seo_json
 └── timestamps

assets
 ├── id
 ├── project_id
 ├── path
 ├── mime_type
 ├── metadata_json
 └── timestamps

versions
 ├── id
 ├── project_id
 ├── version_number
 ├── document_json
 ├── created_by
 └── created_at

published_builds
 ├── id
 ├── project_id
 ├── version_id
 ├── output_path
 └── created_at
```

# 15\. PHP Backend Architecture

```
/core
  Database.php
  Auth.php
  Project.php
  Page.php
  Asset.php
  Renderer.php
  Publisher.php
  Validator.php
  VersionManager.php

/api
  auth/
  projects/
  pages/
  assets/
  publish/
  versions/
  ai/
```

Use a small service-oriented PHP architecture. Keep SQL access, business rules, rendering and HTTP response handling separated. The editor should communicate with PHP through JSON APIs.

# 16\. API Design

```
GET    /api/projects
POST   /api/projects
GET    /api/projects/{id}
PATCH  /api/projects/{id}

GET    /api/projects/{id}/pages
POST   /api/projects/{id}/pages
PATCH  /api/pages/{id}
DELETE /api/pages/{id}

POST   /api/assets
DELETE /api/assets/{id}

POST   /api/projects/{id}/versions
GET    /api/projects/{id}/versions

POST   /api/projects/{id}/publish
POST   /api/projects/{id}/unpublish
```

Requests and responses should use JSON. Authentication should use secure server-side sessions or appropriately designed tokens depending on deployment architecture.

# 17\. Renderer

The renderer has two related responsibilities: canvas rendering for the editor and production rendering for published output.

```
Website JSON
    ↓
Node resolver
    ↓
Component renderer
    ↓
Semantic HTML
    ↓
Style compiler
    ↓
Interaction compiler
    ↓
Final output
```

The renderer should not blindly trust user-generated HTML. Custom code requires sanitization, isolation or explicit trusted-project permissions.

# 18\. Publishing Engine

Publishing converts a saved website document into a deployable build.

```
Project
  ↓
Load document
  ↓
Validate schema
  ↓
Resolve components
  ↓
Compile CSS
  ↓
Compile JS
  ↓
Optimize assets
  ↓
Generate HTML
  ↓
Write build
  ↓
Activate build
```

- Every publish creates a build/version.
- A failed build must not destroy the currently published version.
- Publishing should be atomic: build first, switch active version second.
- Static pages should be preferred where possible.
- Dynamic PHP features should be explicitly declared.

# 19\. Custom Code Architecture

Advanced users need control, but unrestricted server-side code execution is dangerous in a multi-user SaaS environment.

- Custom HTML: allowed after sanitization or in trusted mode.
- Custom CSS: scoped to project/page where possible.
- Custom JavaScript: sandboxed or clearly marked as trusted code.
- PHP: do not execute arbitrary user PHP in a shared application process.
- For server-side extensions, use approved modules, webhooks or isolated execution environments.

# 20\. History & Versioning

Undo/redo should operate on document operations rather than storing a full database copy for every keystroke.

```
Action
  ↓
Command
  ↓
Document mutation
  ↓
History stack

Undo → inverse command
Redo → replay command
```

Examples include add node, delete node, move node, update style, edit content and change breakpoint.

# 21\. Export

A key product principle is that users should own the result.

- Export project JSON.
- Export HTML/CSS/JS ZIP.
- Export assets.
- Export production build.
- Optional platform-specific configuration export.

# 22\. AI Architecture

AI should operate on the Website Document Model rather than directly manipulating arbitrary files.

```
User prompt
     ↓
AI planner
     ↓
Structured site specification
     ↓
Component resolver
     ↓
Website Document Model
     ↓
Validator
     ↓
Visual editor
     ↓
Publish
```

- Generate a complete site from a business description.
- Generate individual sections.
- Rewrite copy.
- Generate SEO metadata.
- Suggest layout improvements.
- Convert a screenshot into a component structure.
- Generate responsive variants.
- Create themes and token sets.
- Explain and repair invalid component configurations.

# 23\. AI Safety / Validation Boundary

AI output must never be treated as trusted application code. The AI layer should produce structured data that passes schema validation before entering the editor.

```
AI → JSON → Schema Validator → Sanitizer → Editor
                 ✕
          Invalid output rejected
```

# 24\. Collaboration — Future

- Project sharing.
- Comments on nodes.
- Live multi-user editing.
- Presence indicators.
- Role-based permissions.
- Design review mode.
- Client approval workflow.

Real-time collaboration should be added only after the document model and operation history are stable.

# 25\. Security Requirements

- Password hashing with a modern password hashing algorithm.
- CSRF protection for session-authenticated mutations.
- Prepared SQL statements.
- Strict upload validation.
- MIME/type and extension checks.
- SVG sanitization.
- Rate limiting on authentication and public APIs.
- Authorization checks on every project resource.
- Secure session cookies.
- Output escaping to prevent XSS.
- Isolation of custom code.
- Backups and recovery procedures.

# 26\. Performance Strategy

- Debounce autosave rather than saving every keystroke.
- Keep the editor document in client memory.
- Use incremental updates.
- Lazy-load large component libraries.
- Virtualize long layer trees.
- Optimize image assets.
- Cache published static builds.
- Avoid regenerating the entire document for every small visual change.

# 27\. Accessibility

- Semantic HTML by default.
- Keyboard navigation inside the editor.
- Accessible controls and labels.
- Alt text enforcement for meaningful images.
- Color contrast guidance.
- Heading hierarchy validation.
- Form label and input association.
- Published websites should not inherit editor-only accessibility defects.

# 28\. SEO

- Semantic HTML.
- Title and description controls.
- Canonical URLs.
- Open Graph and social metadata.
- XML sitemap generation.
- Robots.txt support.
- Clean slugs.
- Structured data blocks for advanced users.
- Performance-focused output.

# 29\. Suggested Project Folder

```
website-creator/
├── public/
│   ├── index.php
│   ├── assets/
│   └── published/
├── app/
│   ├── Core/
│   ├── Models/
│   ├── Services/
│   ├── Renderers/
│   └── Validators/
├── api/
├── editor/
│   ├── canvas/
│   ├── components/
│   ├── inspector/
│   ├── panels/
│   ├── history/
│   └── editor.js
├── components/
├── templates/
├── storage/
│   ├── uploads/
│   ├── builds/
│   └── backups/
├── database/
│   └── migrations/
└── config/
```

# 30\. Development Roadmap

## Phase 0 — Foundation

Project structure, database, authentication, API conventions, document schema and renderer contracts.

## Phase 1 — Core Editor

Canvas, node tree, selection, add/delete/duplicate, text editing, basic drag/drop and save/load.

## Phase 2 — Layout Engine

Container, section, flex, grid, spacing, sizing, positioning and alignment.

## Phase 3 — Styling Engine

Typography, colors, backgrounds, borders, shadows, transforms and design tokens.

## Phase 4 — Responsive Engine

Desktop/tablet/mobile breakpoints, inheritance and responsive inspector.

## Phase 5 — Pages & Assets

Page manager, navigation, media library, uploads and reusable site regions.

## Phase 6 — History

Undo/redo, commands, snapshots and recovery.

## Phase 7 — Publishing

Build pipeline, static output, preview, atomic publish and export ZIP.

## Phase 8 — Templates

Template schema, template marketplace/library and reusable components.

## Phase 9 — Advanced Web Features

Forms, analytics, SEO, integrations, custom code and dynamic modules.

## Phase 10 — AI

Prompt-to-site, section generation, AI editing and screenshot-to-layout.

## Phase 11 — Collaboration

Sharing, comments, roles and real-time editing.

# 31\. MVP Definition

The first usable release should not attempt to compete with every feature of mature website builders. The MVP is successful if a user can create, edit, save, preview and publish a responsive multi-section website without manually writing code.

- Authentication
- Project creation
- Single-page visual editor
- 10 core components
- Text/image/button editing
- Flex/column layout
- Basic responsive controls
- Save/load
- Undo/redo
- Preview
- Static HTML/CSS/JS publishing
- ZIP export

# 32\. Example User Journey

1. User creates a project named 'Acme Logistics'.
2. User selects a starter theme.
3. The editor opens with a blank or template page.
4. User drags a Hero section onto the canvas.
5. User edits heading, paragraph and CTA.
6. User changes colors through global theme tokens.
7. User switches to mobile view and adjusts mobile typography.
8. User creates About and Contact pages.
9. User uploads a logo and images.
10. User previews the complete site.
11. The publishing engine validates and generates a build.
12. The platform activates the build and provides the published URL.
13. User can later reopen the project and continue editing without touching generated files.

# 33\. Key Technical Principles

- The JSON document is the source of truth.
- The DOM is a rendering surface, not the database.
- Every node has a stable ID.
- All mutations are commands that can be undone.
- Components define their own property schemas.
- Responsive values inherit and override.
- Publishing is a build process.
- Generated output is disposable and reproducible.
- Custom code is isolated from the trusted application core.
- AI writes structured data, never uncontrolled application internals.

# 34\. Future Expansion

- Multi-tenant SaaS architecture.
- Custom domains and SSL automation.
- Client portals.
- White-label website builder.
- Component marketplace.
- Template marketplace.
- Team workspaces.
- Website analytics.
- A/B testing.
- E-commerce components.
- Membership/authentication components.
- Database-backed dynamic collections.
- Visual CMS.
- Visual email builder using the same component philosophy.
- Visual app/page builder beyond traditional websites.

# 35\. Final Architecture Statement

The long-term product should be treated as a compiler-driven visual web platform. Users create a structured website document through a visual programming interface. The same document can be rendered in the editor, previewed responsively, validated, versioned, transformed by AI and compiled into deployable web output.

This architecture deliberately avoids coupling the product to generated HTML. HTML/CSS/JS are outputs of the system. The real product is the document model, component system, editor, renderer and compiler pipeline.

```
VISUAL EDITOR + DOCUMENT MODEL + COMPONENT SYSTEM + RENDERER + COMPILER = WEBSITE CREATOR PLATFORM
```

That foundation leaves room to grow from a simple PHP website builder into a complete visual development platform without throwing away the original architecture.