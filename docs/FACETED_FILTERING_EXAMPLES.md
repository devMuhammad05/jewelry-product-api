# Faceted Filtering - URL Examples

## Base URLs

- **Categories:** `http://127.0.0.1:8000/api/v1/categories/{slug}`
- **Collections:** `http://127.0.0.1:8000/api/v1/collections/{slug}`

---

## URL Examples

### 1. No Filters (Get All Products)

```
http://127.0.0.1:8000/api/v1/collections/love-collection
```

Returns all products in the "love-collection" with available facets.

---

### 2. Filter by Single Attribute Value

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum
```

**Note:** Use `filter` (singular), not `filters` (plural)

Returns only products with Platinum metal. The attribute value slug should be lowercase (`platinum`, not `Platinum`).

---

### 3. Filter by Multiple Values (Same Attribute - OR Logic)

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=gold,platinum
```

Returns products with EITHER Gold OR Platinum metal.

---

### 4. Filter by Multiple Attributes (AND Logic)

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&filter[stone_shape]=round
```

Returns products that are BOTH Platinum AND have Round stone shape.

---

### 5. With Pagination

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&page=2&per_page=12
```

Returns page 2 with 12 products per page.

---

### 6. With Sorting

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&sort=-base_price
```

**Sort Options:**

- `sort=name` - Name A-Z
- `sort=-name` - Name Z-A
- `sort=base_price` - Price Low to High
- `sort=-base_price` - Price High to Low (use minus sign for descending)
- `sort=created_at` - Oldest First
- `sort=-created_at` - Newest First

---

### 7. With Related Data (Includes)

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&include=variants,attributeValues,images
```

Eagerly loads variants, attribute values, and images with each product.

---

### 8. Complete Example (All Parameters)

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=gold,platinum&filter[stone_shape]=round&filter[stone_type]=diamond&sort=-base_price&page=1&per_page=24&include=variants,images
```

This query:

- Filters by Gold OR Platinum metal
- AND Round stone shape
- AND Diamond stone type
- Sorts by price high to low
- Shows page 1 with 24 items
- Includes variants and images

---

## Category Examples

Same syntax works for categories:

```
http://127.0.0.1:8000/api/v1/categories/rings?filter[metal]=platinum
```

```
http://127.0.0.1:8000/api/v1/categories/rings?filter[metal]=gold,platinum&filter[stone_shape]=round&sort=base_price
```

---

## Important Notes

1. **Use `filter` (singular)** - Not `filters`
2. **Attribute values should be lowercase slugs** - Use `platinum` not `Platinum`
3. **Multiple values use commas** - `filter[metal]=gold,platinum`
4. **Different attributes use `&`** - `filter[metal]=gold&filter[stone_shape]=round`
5. **Descending sort uses minus** - `sort=-base_price`

---

## Testing in Browser/Postman

You can copy any of these URLs directly into your browser or Postman to test:

### Test 1: Get all products in a collection

```
http://127.0.0.1:8000/api/v1/collections/love-collection
```

### Test 2: Filter by Platinum

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum
```

### Test 3: Filter by multiple metals

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=gold,platinum,silver
```

### Test 4: Complex filtering

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&filter[stone_shape]=round&sort=-base_price&per_page=10
```

---

## Expected Response Structure

```json
{
    "status": "success",
    "message": "Collection details retrieved successfully.",
    "data": {
        "collection": {
            "id": 1,
            "name": "Love Collection",
            "slug": "love-collection"
        },
        "products": [
            {
                "id": 1,
                "name": "Platinum Love Ring",
                "base_price": "3500.00"
            }
        ],
        "facets": [
            {
                "id": 1,
                "name": "Metal",
                "slug": "metal",
                "values": [
                    {
                        "id": 2,
                        "value": "Platinum",
                        "slug": "platinum",
                        "products_count": 15
                    }
                ]
            }
        ],
        "meta": {
            "current_page": 1,
            "last_page": 2,
            "per_page": 24,
            "total": 45
        }
    }
}
```
