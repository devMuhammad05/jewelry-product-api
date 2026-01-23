# Jewelry Product Model (Core Domain)

This document defines the **core product model** for a collection-driven luxury jewelry e-commerce system.

It is designed to be **LLM-readable**, **framework-agnostic**, and **scalable**.

---

## Mental Model

Product = Design
Variant = Sellable Unit
Category = Navigation
Collection= Story
Attribute = Filter
Campaign = Time-bound Promotion

---

## 1. Product (Design)

**Purpose**  
Represents the jewelry design or concept.  
Not directly purchasable.

**Rules**

- Must be generic and reusable
- Must not store price or inventory
- Must not contain navigation or marketing logic
- Must support multiple variants

**Fields**

- `id`
- `name`
- `slug`
- `description`
- `status` (Enum: Draft, Active, Archived)
- `created_at`
- `updated_at`

**Relationships**

- hasMany → Variants
- belongsToMany → Categories
- belongsToMany → Collections
- belongsToMany → Campaigns
- belongsToMany → AttributeValues
- hasMany → Media

---

## 2. Variant (Sellable Unit)

**Purpose**  
Represents a purchasable configuration of a product.

**Rules**

- Holds all commerce-related data
- Each variant must have a unique SKU
- Inventory and pricing live only here

**Fields**

- `id`
- `product_id`
- `sku`
- `price`
- `currency`
- `inventory_quantity`
- `size`
- `metal_type`
- `weight`
- `created_at`

**Relationships**

- belongsTo → Product
- hasMany → Media
- hasMany → Prices (optional, region-based)

---

## 3. Category (Navigation)

**Examples**

- Jewelry
- Watches
- Fragrances

**Rules**

- Tree-structured (self-referencing)
- Purely navigational
- Deleting a category must not delete products

**Fields**

- `id`
- `parent_id` (nullable)
- `name`
- `slug`
- `description`
- `image_url`
- `position`
- `is_active` (boolean)
- `created_at`
- `updated_at`

**Relationships**

- belongsToMany → Products
- hasMany → Categories (children)

---

## 4. Collection (Story)

**Purpose**  
Groups products into branded or thematic stories.

**Examples**

- LOVE
- Trinity
- Panthère de Cartier

**Rules**

- Marketing-only construct
- Controls UI ordering and visibility
- Products may belong to multiple collections

**Fields**

- `id`
- `parent_id` (nullable)
- `name`
- `slug`
- `description`
- `hero_image`
- `is_featured` (boolean)
- `position`
- `created_at`
- `updated_at`

**Relationships**

- belongsToMany → Products (with position)

---

## 5. Attribute (Filter System)

### Attribute

**Purpose**  
Defines a filterable characteristic.

**Fields**

- `id`
- `name` (Metal, Stone, Gender)
- `slug`
- `type` (select | boolean | text)

### AttributeValue

**Purpose**  
Concrete values for an attribute.

**Fields**

- `id`
- `attribute_id`
- `value` (Gold, Diamond, Unisex)
- `slug`
- `hex_color` (optional)

**Relationships**

- belongsToMany → Products

---

## 6. Campaign (Time-bound Promotion)

**Purpose**  
Groups products for seasonal or time-limited visibility.

**Examples**

- Valentine’s Day
- Mother’s Day

**Rules**

- Time-bound
- Must not affect product data

**Fields**

- `id`
- `name`
- `slug`
- `start_date`
- `end_date`

**Relationships**

- belongsToMany → Products

---

## 7. Media

**Purpose**  
Stores images and videos for products and variants.

**Rules**

- Variant media overrides product media
- Ordered for display control

**Fields**

- `id`
- `product_id` (nullable)
- `variant_id` (nullable)
- `url`
- `type` (image | video)
- `position`

---

## 8. Pivot Tables (Relationship Contracts)

Pivot tables define **explicit relationship contracts** between core models.
They must contain **no business logic**, only linkage and ordering metadata.

---

### 8.1 category_product

**Purpose**  
Links products to navigational categories.

**Rules**

- A product may belong to multiple categories
- Categories must not own or mutate products

**Fields**

- `product_id`
- `category_id`

**Constraints**

- `(product_id, category_id)` must be unique
- Deleting a category must not delete products

---

### 8.2 collection_product

**Purpose**  
Links products to collections and controls UI ordering.

**Rules**

- A product may belong to multiple collections
- Ordering is defined at the relationship level

**Fields**

- `collection_id`
- `product_id`
- `position`

**Constraints**

- `(collection_id, product_id)` must be unique
- `position` controls carousel / grid order

---

### 8.3 campaign_product

**Purpose**  
Associates products with time-bound campaigns.

**Rules**

- Campaigns must not modify product data
- Campaign visibility is date-driven

**Fields**

- `campaign_id`
- `product_id`

**Constraints**

- `(campaign_id, product_id)` must be unique
- Campaign expiry must not delete relationships

---

### 8.4 product_attribute_value

**Purpose**  
Assigns filterable attributes to products.

**Rules**

- Attributes must be schema-agnostic
- Filtering relies entirely on this table

**Fields**

- `product_id`
- `attribute_value_id`

**Constraints**

- `(product_id, attribute_value_id)` must be unique
- Attribute values must belong to exactly one attribute

---

### 8.5 variant_media (Optional)

**Purpose**  
Explicit ordering of media for variants.

**Fields**

- `variant_id`
- `media_id`
- `position`

---

### 8.6 product_media (Optional)

**Purpose**  
Explicit ordering of media for products.

**Fields**

- `product_id`
- `media_id`
- `position`

---

## Pivot Table Invariants

- Pivot tables must never contain:
    - price
    - inventory
    - descriptive text
- Pivot tables exist only to express relationships
- Ordering always lives on the pivot, never the entity

---

## Relationship Priority Rules

1. Variant media overrides product media
2. Collection ordering overrides category ordering
3. Campaign visibility overrides collection visibility (time-based)

---

## Summary

category_product → navigation membership
collection_product → storytelling + ordering
campaign_product → promotional visibility
product_attribute_value → filtering
product_media → visual ordering
variant_media → variant visuals
