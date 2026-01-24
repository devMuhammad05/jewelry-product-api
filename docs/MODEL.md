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

# 9. Cart (Shopping Session)

**Purpose**  
Manages temporary shopping sessions for both authenticated and guest users via API.

**Rules**

- Must support guest (token-based) and authenticated (user-based) carts
- Cart items reference variants, not products
- Carts must not store computed totals (derive on read)
- Guest carts must be mergeable with user carts on login
- Expired guest carts must be purgeable
- Price is snapshotted at add-time but recalculated at checkout

---

## 9.1 Cart

**Purpose**  
Represents a shopping session.

**Fields**

- `id`
- `user_id` (nullable, null for guests)
- `guest_token` (nullable, required for guests, UUID)
- `status` (Enum: Active, Abandoned, Converted, Merged)
- `expires_at` (nullable, for guest carts)
- `created_at`
- `updated_at`

**Relationships**

- belongsTo → User (nullable)
- hasMany → CartItems

**Constraints**

- `(user_id)` must be unique where `status = 'Active'` and `user_id IS NOT NULL`
- `(guest_token)` must be unique where `status = 'Active'` and `guest_token IS NOT NULL`
- Guest carts require `guest_token`
- User carts require `user_id`

**Invalidation Rules**

- Guest carts expire after 7-30 days of inactivity
- Abandoned carts marked after 24 hours of inactivity
- Merged carts retain history but become read-only

---

## 9.2 CartItem

**Purpose**  
Represents a single line item in a cart.

**Fields**

- `id`
- `cart_id`
- `variant_id`
- `quantity`
- `price_snapshot` (price at time of add)
- `currency_snapshot`
- `created_at`
- `updated_at`

**Relationships**

- belongsTo → Cart
- belongsTo → Variant

**Constraints**

- `(cart_id, variant_id)` must be unique
- `quantity` must be > 0
- `quantity` must not exceed `variant.inventory_quantity`

**Rules**

- Price is snapshotted for analytics but recalculated at checkout
- Variant availability must be validated before checkout
- Out-of-stock variants must trigger API error response

---

## 9.3 Cart Business Logic

### Guest Cart Flow (API)

1. **Initial Request** → API generates `guest_token` (UUID), returns in response
2. **Subsequent Requests** → Client sends `guest_token` in header or body
3. Cart persists in database keyed by `guest_token`
4. Cart expires after inactivity period
5. **On Login** → Merge guest cart with user cart via `/cart/merge` endpoint

### User Cart Flow (API)

1. **Authenticated Request** → Extract `user_id` from JWT/Sanctum token
2. Cart persists indefinitely tied to `user_id`
3. **On Logout** → Cart remains attached to user (no session dependency)

### API Authentication Strategy

**Guest Carts:**

```
Header: X-Guest-Token: {uuid}
OR
Body: { "guest_token": "{uuid}" }
```

**User Carts:**

```
Header: Authorization: Bearer {token}
```

### Merge Strategy (Guest → User on Login)

```
POST /api/cart/merge
Body: { "guest_token": "{uuid}" }
Headers: Authorization: Bearer {user_token}

IF user has active cart AND guest_token cart exists:
  FOR EACH guest cart item:
    IF item exists in user cart:
      user_cart_item.quantity += guest_cart_item.quantity
    ELSE:
      INSERT guest_cart_item INTO user_cart
  guest_cart.status = 'Merged'

Response: merged user cart
```

---

## 9.4 API Endpoints

### Guest Cart Endpoints

```
POST   /api/cart/init              → Create guest cart, return guest_token
GET    /api/cart                   → Fetch cart (guest_token required)
POST   /api/cart/items             → Add item to cart
PATCH  /api/cart/items/{id}        → Update item quantity
DELETE /api/cart/items/{id}        → Remove item from cart
DELETE /api/cart                   → Clear cart
```

### Authenticated Cart Endpoints

```
GET    /api/cart                   → Fetch user cart (auth required)
POST   /api/cart/items             → Add item to cart
PATCH  /api/cart/items/{id}        → Update item quantity
DELETE /api/cart/items/{id}        → Remove item from cart
DELETE /api/cart                   → Clear cart
POST   /api/cart/merge             → Merge guest cart into user cart
```

---

# 10. Wishlist (Saved Items)

**Purpose**  
Allows users to save products for future consideration via API.

**Rules**

- Wishlists reference products, not variants (user chooses variant later)
- Must support guest (token-based) and authenticated (user-based) wishlists
- Guest wishlists must be mergeable on login
- Users may have multiple named wishlists (e.g., "Birthday Ideas", "Anniversary")
- Products can appear in multiple wishlists

---

## 10.1 Wishlist

**Purpose**  
Represents a collection of saved products.

**Fields**

- `id`
- `user_id` (nullable, null for guests)
- `guest_token` (nullable, required for guests, UUID)
- `name` (default: "My Wishlist")
- `is_default` (boolean, one default per user)
- `visibility` (Enum: Private, Shared)
- `share_token` (nullable, UUID for shared wishlists)
- `expires_at` (nullable, for guest wishlists)
- `created_at`
- `updated_at`

**Relationships**

- belongsTo → User (nullable)
- hasMany → WishlistItems

**Constraints**

- `(user_id, is_default)` must enforce only one `is_default = true` per user
- Guest wishlists require `guest_token`
- User wishlists require `user_id`
- `share_token` must be unique when not null

**Rules**

- Each user has one default wishlist
- Users may create additional named wishlists
- Guest wishlists expire after 30-90 days
- Shared wishlists are read-only for non-owners

---

## 10.2 WishlistItem

**Purpose**  
Represents a product saved to a wishlist.

**Fields**

- `id`
- `wishlist_id`
- `product_id`
- `note` (nullable, user-specific note)
- `priority` (Enum: Low, Medium, High, nullable)
- `created_at`
- `updated_at`

**Relationships**

- belongsTo → Wishlist
- belongsTo → Product

**Constraints**

- `(wishlist_id, product_id)` must be unique

**Rules**

- Items reference products (design level)
- User selects variant when moving to cart
- Archived products remain visible in wishlist with "No longer available" flag

---

## 10.3 Wishlist Business Logic

### Guest Wishlist Flow (API)

1. **Initial Request** → API generates `guest_token` (UUID), returns in response
2. **Subsequent Requests** → Client sends `guest_token` in header or body
3. Wishlist persists in database keyed by `guest_token`
4. **On Login** → Merge guest wishlist with user's default wishlist via `/wishlist/merge`

### User Wishlist Flow (API)

1. **Authenticated Request** → Extract `user_id` from JWT/Sanctum token
2. User may create additional named wishlists
3. User may share wishlist via `share_token`

### API Authentication Strategy

**Guest Wishlists:**

```
Header: X-Guest-Token: {uuid}
OR
Body: { "guest_token": "{uuid}" }
```

**User Wishlists:**

```
Header: Authorization: Bearer {token}
```

### Merge Strategy (Guest → User on Login)

```
POST /api/wishlist/merge
Body: { "guest_token": "{uuid}" }
Headers: Authorization: Bearer {user_token}

IF user has default wishlist AND guest_token wishlist exists:
  FOR EACH guest wishlist item:
    IF product NOT IN user default wishlist:
      INSERT guest_wishlist_item INTO user_default_wishlist
  guest_wishlist.expires_at = NOW()

Response: merged user default wishlist
```

### Move to Cart

```
POST /api/wishlist/{wishlist_id}/items/{item_id}/move-to-cart
Body: { "variant_id": "{uuid}", "quantity": 1 }

1. Validate variant belongs to product
2. Add variant to cart
3. Optionally remove from wishlist (query param: remove_from_wishlist=true)
```

---

## 10.4 Wishlist Sharing

**Purpose**  
Allows users to share wishlists publicly via API.

**Rules**

- Only owner can edit shared wishlist
- Shared wishlists display product information and availability
- Viewers can add items from shared wishlist to their own cart
- Share tokens must be revocable

**API Endpoint**

```
GET /api/wishlist/shared/{share_token}  → Fetch shared wishlist (no auth)
```

---

## 10.5 API Endpoints

### Guest Wishlist Endpoints

```
POST   /api/wishlist/init                          → Create guest wishlist, return guest_token
GET    /api/wishlist                               → Fetch default wishlist (guest_token required)
POST   /api/wishlist/items                         → Add product to wishlist
DELETE /api/wishlist/items/{id}                    → Remove product from wishlist
```

### Authenticated Wishlist Endpoints

```
GET    /api/wishlist                               → Fetch default wishlist (auth required)
GET    /api/wishlist/all                           → Fetch all user wishlists
POST   /api/wishlist                               → Create new named wishlist
GET    /api/wishlist/{id}                          → Fetch specific wishlist
PATCH  /api/wishlist/{id}                          → Update wishlist (name, visibility)
DELETE /api/wishlist/{id}                          → Delete wishlist
POST   /api/wishlist/{id}/items                    → Add product to wishlist
PATCH  /api/wishlist/{id}/items/{item_id}          → Update wishlist item (note, priority)
DELETE /api/wishlist/{id}/items/{item_id}          → Remove product from wishlist
POST   /api/wishlist/{id}/share                    → Generate share_token
DELETE /api/wishlist/{id}/share                    → Revoke share_token
POST   /api/wishlist/merge                         → Merge guest wishlist into user wishlist
POST   /api/wishlist/{id}/items/{item_id}/move-to-cart → Move item to cart
```

### Public Wishlist Endpoints

```
GET    /api/wishlist/shared/{share_token}          → View shared wishlist (no auth)
```

---

## Cart vs Wishlist Summary

| Aspect         | Cart                           | Wishlist                  |
| -------------- | ------------------------------ | ------------------------- |
| References     | Variant (sellable unit)        | Product (design)          |
| Intent         | Immediate purchase             | Future consideration      |
| Guest Support  | Token-based, 7-30 days         | Token-based, 30-90 days   |
| Multiplicity   | One active cart per user/guest | Multiple named wishlists  |
| Sharing        | Not shareable                  | Shareable via share_token |
| Price Snapshot | Yes (for analytics)            | No                        |
| Merge on Login | Additive (quantities sum)      | Deduplicates by product   |
| Guest Token    | X-Guest-Token header           | X-Guest-Token header      |
| Auth Token     | Authorization: Bearer          | Authorization: Bearer     |

# 11. Coupon (Discounts)

**Purpose**  
Encourages purchases via percentage or fixed-amount discounts. Can be scoped to specific entities or applied to the entire cart.

**Rules**

- Must have a unique code.
- Can be percentage-based or fixed-amount.
- Validity is date-driven (`starts_at`, `ends_at`).
- Supports usage limits (total and per-user).
- Scoping is handled via pivot tables for targeted discounts.
- Deleting a coupon must not delete linked entities (Products, Categories, etc.).

**Fields**

- `id`
- `code` (Unique uppercase string)
- `description` (nullable)
- `type` (Enum: CouponType)
- `value` (integer, stored in cents for fixed or as basis points for percentage)
- `currency` (string, 3 chars, nullable)
- `min_cart_total` (integer, cents, nullable)
- `max_discount_amount` (integer, cents, nullable, for percentage coupons)
- `usage_limit` (integer, nullable)
- `usage_count` (integer, default 0)
- `starts_at` (datetime, nullable)
- `ends_at` (datetime, nullable)
- `status` (Enum: CouponStatus)
- `created_at`
- `updated_at`

**Relationships**

- belongsToMany → Products
- belongsToMany → Variants
- belongsToMany → Categories
- belongsToMany → Collections

---

## 11.1 coupon_product

**Purpose**  
Scopes a coupon to specific products.

**Fields**

- `coupon_id`
- `product_id`

---

## 11.2 coupon_variant

**Purpose**  
Scopes a coupon to specific variants.

**Fields**

- `coupon_id`
- `variant_id`

---

## 11.3 coupon_category

**Purpose**  
Scopes a coupon to all products within specific categories.

**Fields**

- `coupon_id`
- `category_id`

---

## 11.4 coupon_collection

**Purpose**  
Scopes a coupon to all products within specific collections.

**Fields**

- `coupon_id`
- `collection_id`

---

## 11.5 coupon_user

**Purpose**  
Tracks coupon usage by specific users to enforce per-user limits.

**Fields**

- `coupon_id`
- `user_id`
- `used_at`

---

# 12. Address

**Purpose**  
Stores shipping and billing information for users.

**Rules**

- Each user can have multiple addresses.
- Only one address can be marked as `default` per user.
- Addresses are required for checkout and shipping calculations.

**Fields**

- `id`
- `user_id`
- `label` (e.g., Home, Work, Office)
- `first_name`
- `last_name`
- `phone`
- `address_line_1`
- `address_line_2` (nullable)
- `country`
- `city`
- `state`
- `postal_code` (nullable)
- `is_default` (boolean)
- `created_at`
- `updated_at`

**Relationships**

- belongsTo → User

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
coupon_product → targeted discount (Product)
coupon_variant → targeted discount (Variant)
coupon_category → targeted discount (Category)
coupon_collection → targeted discount (Collection)
coupon_user → track user usage
address → shipping & billing
