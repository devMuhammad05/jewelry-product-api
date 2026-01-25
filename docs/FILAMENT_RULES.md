# Filament Development Rules

## Form Design

- **Full-Width Managed Layouts**: Forms should prioritize full-width segments using `Section` and `columns()`. Avoid squeezing forms into small side-bars unless specifically requested.
- **Stacked Sections**: Use stacked `Section` components with `columnSpanFull()` and internal column logic (e.g., `->columns(2)`) for a premium, organized feel.

## Attribute Handling

- **No Manual Slugs**: Never include a `slug` field in a Filament form. Slugs must be generated automatically in the background.
    - **Implementation**: Slugs should be handled via the `HasSlug` trait in the Model.
    - **Logic**: The trait listens to model events (`creating` / `updating`) to set the `slug` attribute from the `name` attribute using `Str::slug()`.

## Table Design

- **State Filters**: Always include table filters for record statuses (e.g., `is_active`, `status`, `draft`). Use `TernaryFilter` or `SelectFilter` to keep the list clean.
- **Column Customization**: Enable user-driven column management.
    - **Toggleable Columns**: Use `toggleable()` on non-essential columns.
    - **Timestamp Strategy**: `created_at` and `updated_at` columns must always be included but hidden by default using `toggleable(isToggledHiddenByDefault: true)`.
- **Layout Control**: For tables with many columns, ensure the table remains readable by prioritizing essential data and moving secondary data to the toggle manager.

## Navigation & UI

- **Meaningful Icons**: Always change the default `navigationIcon` from `Heroicon::OutlinedRectangleStack` to an icon that matches the resource's purpose.
    - **Implementation**: Refer to [Heroicons](https://heroicons.com/) for appropriate icons.
    - **Fallback**: If no specific matching icon is found that fits the resource context, you may result to a generic but appropriate default.


