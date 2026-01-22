Core E-commerce Modeling Principles (Luxury / Collection-Driven)
1. Product is the Source of Truth

A Product represents a sellable design or concept.

Products must not contain navigation or marketing logic.

Products never belong to a single category or collection.

2. Variants Hold Commerce Data

Variants represent purchasable units.

All prices, inventory, size, material, and SKU live on variants.

A product without variants is invalid.

3. Categories Are Hierarchical Navigation

Categories exist only for site structure and browsing.

Categories are tree-based (parent → child).

Products relate to categories via many-to-many relations.

Categories do not affect business logic.

4. Collections Are Marketing Constructs

Collections group products for storytelling, campaigns, and UI sections.

A product may belong to multiple collections.

Collections control ordering and visibility, not data ownership.

Removing a collection must not affect product integrity.

5. No Hardcoded Product Types

Do not model product types as columns (ring_type, necklace_type).

Use categories + attributes instead.

Product logic must remain generic.

6. Attributes Are Extensible Metadata

Attributes describe products (metal, stone, gender).

Attributes must be dynamic and schema-agnostic.

Filtering must rely on attributes, not fixed columns.

7. Relationships Over Fields

Prefer pivot tables over nullable columns.

Many-to-many is the default unless proven otherwise.

Ordering is stored on the relationship, not the entity.

8. UI Sections Are Data-Driven

Homepage sections (Featured, Valentine’s Day, Icons) are powered by:

Collections

Campaigns

Flags + positions

UI must not infer meaning from product data.

9. Data Must Be Reusable

The same product must safely appear in:

Multiple categories

Multiple collections

Multiple campaigns

Duplication is forbidden.

10. Delete Rules

Deleting a category or collection:

Must not delete products.

Deleting a product:

Must cascade to variants, media, and pivots.

11. Scalability Rule

Any model decision must support:

New collections without migrations

New attributes without schema changes

New campaigns without product edits

12. Forbidden Anti-Patterns

❌ Single category_id on products
❌ collection_id column on products
❌ Price on products
❌ UI logic in the database
❌ One-to-many where many-to-many applies

Product = Design
Variant = Sellable Unit
Category = Navigation
Collection = Story
Attribute = Filter
Campaign = Time-bound Promotion
