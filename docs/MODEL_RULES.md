# Model Rules

## General Rules

## Attribute Handling

- **Automatic Slugs**: If a model has a `slug` attribute, it **must** use the `App\Traits\HasSlug` trait.
    - **Logic**: Slugs are generated automatically during model events (`creating` and `updating`).
    - **Source Identification**: Models using the `HasSlug` trait **must** define a `protected string $slugSource` property specifying the column used to generate the slug (e.g., `'name'` or `'value'`).
    - **Uniformity**: This ensures all slugs across the application are generated consistently and eliminates the need for manual slug fields in the UI.

## Relationship Naming

- **Descriptive Names**: Always use descriptive names for relationship methods (e.g., `activeCart()`, not `cart()`).
- **Return Types**: Always provide explicit return type hints for relationships (e.g., `public function variants(): HasMany`).
