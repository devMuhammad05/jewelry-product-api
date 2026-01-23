# Faceted Filtering - URL Examples

## Base URLs

- **Categories:** `http://127.0.0.1:8000/api/v1/categories/{slug}`
- **Collections:** `http://127.0.0.1:8000/api/v1/collections/{slug}`
- **Products:** `http://127.0.0.1:8000/api/v1/products/{slug}`

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

### 5. Product Details

```
http://127.0.0.1:8000/api/v1/products/classic-love-ring?include=variants,attributeValues,categories,collections
```

Returns a single product by slug with all selected relationships loaded.

---

### 6. With Pagination

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&page=2&per_page=12
```

Returns page 2 with 12 products per page.

---

### 7. With Sorting

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

### 8. With Related Data (Includes)

```
http://127.0.0.1:8000/api/v1/collections/love-collection?filter[metal]=platinum&include=variants,attributeValues
```

Eagerly loads variants and attribute values with each product.

---

## Important Notes

1. **Use `filter` (singular)** - Not `filters`
2. **Attribute values should be lowercase slugs** - Use `platinum` not `Platinum`
3. **Multiple values use commas** - `filter[metal]=gold,platinum`
4. **Different attributes use `&`** - `filter[metal]=gold&filter[stone_shape]=round`
5. **Descending sort uses minus** - `sort=-base_price`

---

## Expected Response Structure (Category/Collection)

```json
{
    "status": "success",
    "message": "Category details retrieved successfully.",
    "data": {
        "category": {
            "id": 1,
            "name": "Rings",
            "slug": "rings",
            "description": "...",
            "image_url": "...",
            "children": []
        },
        "products": [
            {
                "id": 1,
                "name": "Platinum Love Ring",
                "slug": "platinum-love-ring",
                "base_price": "3500.00"
            }
        ],
        "collections": [
            {
                "id": 1,
                "name": "Love Collection",
                "slug": "love-collection"
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
                        "product_count": 15
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
