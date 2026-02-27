# Indicia Institute Development Doctrine

**Status**: Active  
**Version**: 1.0.1  
**Last Updated**: 2026-02-16  
**Applies To**: Indicia MVC only (NOT Chaos CMS)

## I. Architectural Principle

The Indicia Institute application is a strictly controlled MVC system.

 - It is not a CMS.
 - It is not a plugin host.
 - It is not dynamically extensible.

All components are intentional and finite.

## II. Core Structural Rule

Every module must contain exactly:

**Controller**
`/app/controllers/{module}.php`

**Model**
`/app/models/{module}_model.php`

**Public Views**
`/app/views/public/{module}/`

**Admin Views**
`/app/views/admin/`

 - No dynamic module registration.
 - No auto-discovery.
 - No runtime loading of arbitrary modules.

Modules are added manually and deliberately.

## III. Separation of Responsibility
**Controllers**
 - Route requests.
 - Coordinate models.
 - Select views.
 - Contain no raw SQL.

**Models**
 - Own all database interaction.
 - Contain no presentation logic.
 - Contain no routing logic.
 - May not load views.

**Views**
 - Present data.
 - Contain no business logic.
 - Contain no SQL.
 - Contain no direct DB access.

## IV. Anti-Bloat Rule

The system shall not:
 - Implement plugin systems.
 - Implement hook systems.
 - Implement event dispatchers.
 - Implement module registries.
 - Implement runtime dependency injection.
 - Implement reflection-based auto loading of modules.
 - Implement unnecessary abstraction layers.

**Less is best**.

## V. Data Governance
 - No HTML stored in the database.
 - Rendering is governed by Indicia Rendering Grammar (v1.0.8+).
 - All content validation occurs at save-time.
 - Nothing is deleted; data may only be archived or deactivated.

## VI. Visibility Control
 - Public visibility is controlled via state flags (e.g., is_active).
 - Inactive entities are hidden from public routes.
 - Inactive entities remain structurally present.

## VII. Expansion Policy

**New modules must**:
 - Serve a clear institutional purpose.
 - Avoid cross-module bleed.
 - Avoid introducing systemic abstraction.

If a feature requires structural expansion beyond this doctrine, it must be justified and reviewed before implementation.

## VIII. Philosophical Constraint
The Institute system prioritizes:
 - Stability over extensibility.
 - Clarity over cleverness.
 - Structure over dynamism.
 - Intentional growth over reactive expansion.
