# Phase 1: Foundation Remediation Plan - Remediation according to WEBSITE_CREATOR_REMEDIATION_PLAN.md

## 1. Objectives
- Separate HTML page bootstrapping from API bootstrapping.
- Simplify autoloader usage and centralize application setup.
- Rename/Fix SQL schema file name.
- Centralize session management.

## 2. Approach
- **Bootstrap Consolidation**: Extract autoloader, session init, and database setup to `app/bootstrap.php`.
- **API Initialization**: Update `api/init.php` to use `app/bootstrap.php` and only add JSON headers when serving API requests.
- **Session Service**: Implement `App\Core\Session` to encapsulate session operations.

## 3. Implementation Steps:
1.  **Refactor `api/init.php`**: Remove hardcoded requirements, require `app/bootstrap.php`.
2.  **Rename `shcema.sql`**: Execute file rename via bash.
3.  **Implement `App\Core\Session`**: Standardize `start()`, `regenerate()`, `destroy()`, `login()`, `logout()`.
4.  **Cleanup**: Verify all pages (`public/*.php`) now include the correct bootstrap.
