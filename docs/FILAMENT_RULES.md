# Filament Development Rules

## Form Design

- **Full-Width Managed Layouts**: Forms should prioritize full-width segments using `Section` and `columns()`. Avoid squeezing forms into small side-bars unless specifically requested.
- **Stacked Sections**: Use stacked `Section` components with `columnSpanFull()` and internal column logic (e.g., `->columns(2)`) for a premium, organized feel.

## Attribute Handling

- **No Manual Slugs**: Never include a `slug` field in a Filament form. Slugs must be generated automatically in the background.
    - **Implementation**: Slugs should be handled via the `HasSlug` trait in the Model.
    - **Logic**: The trait listens to model events (`creating` / `updating`) to set the `slug` attribute from the `name` attribute using `Str::slug()`.
