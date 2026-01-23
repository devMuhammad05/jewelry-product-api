# Faceted Filtering - Query Examples

## Overview

The faceted filtering system allows dynamic filtering based on product attributes. The API returns available facets (filters) based on the current product set, and the frontend can then apply these filters to narrow down results.

---

## How It Works

### 1. Initial Request (No Filters)

**Request:**

```http
GET /api/v1/categories/rings
```

**Response:**

```json
{
  "status": "success",
  "message": "Category details retrieved successfully.",
  "data": {
    "category": {
      "id": 1,
      "name": "Rings",
      "slug": "rings",
      "description": "Beautiful rings collection",
      "image_url": "..."
    },
    "products": [
      {
        "id": 1,
        "name": "Classic Solitaire Diamond Ring",
        "slug": "classic-solitaire-diamond-ring",
        "base_price": "2500.00"
      }
    ],
    "collections": [...],
    "facets": [
      {
        "id": 1,
        "name": "Metal",
        "slug": "metal",
        "values": [
          {
            "id": 1,
            "value": "Gold",
            "slug": "gold",
            "product_count": 45
          }
        ]
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 24,
      "total": 80
    }
  }
}
```

---

### 2. Filtering by Single Attribute Value

**Request:**

```http
GET /api/v1/categories/rings?filter[metal]=gold
```

**What happens:**

- Returns only products with "Gold" metal
- Facets are recalculated to show only options available in gold rings
- For example, if no gold rings have "Emerald" stones, that option won't appear in the stone facet

---

### 3. Filtering by Multiple Values (Same Attribute)

**Request:**

```http
GET /api/v1/categories/rings?filter[metal]=gold,platinum
```

**What happens:**

- Returns products with EITHER "Gold" OR "Platinum" metal
- Uses comma-separated values for OR logic within the same attribute

---

### 4. Filtering by Multiple Attributes (AND Logic)

**Request:**

```http
GET /api/v1/categories/rings?filter[metal]=gold&filter[stone_shape]=round
```

**What happens:**

- Returns products that are BOTH "Gold" AND have "Round" stones
- Different attributes use AND logic

---

### 5. Complex Filtering with Pagination and Sorting

**Request:**

```http
GET /api/v1/categories/rings?filter[metal]=gold,platinum&filter[stone_shape]=round&sort=base_price&per_page=12&page=2
```

**Parameters:**

- `filter[metal]=gold,platinum` - Gold OR Platinum
- `filter[stone_shape]=round` - Round stones
- `sort=base_price` - Sort by price ascending
- `sort=-base_price` - Sort by price descending (use minus sign)
- `per_page=12` - Show 12 products per page
- `page=2` - Get page 2

---

### 6. Including Related Data

**Request:**

```http
GET /api/v1/categories/rings?filter[metal]=gold&include=variants,attributeValues,images
```

**What happens:**

- Returns products with their variants, attribute values, and images eagerly loaded
- Reduces N+1 query problems

---

## Frontend Implementation Examples

### JavaScript/Fetch Example

```javascript
class ProductFilter {
    constructor(categorySlug) {
        this.categorySlug = categorySlug;
        this.filters = {};
        this.page = 1;
        this.perPage = 24;
        this.sort = "name";
    }

    // Add or update a filter
    setFilter(attribute, values) {
        if (values.length === 0) {
            delete this.filters[attribute];
        } else {
            this.filters[attribute] = values;
        }
    }

    // Toggle a single value in a filter
    toggleFilterValue(attribute, value) {
        if (!this.filters[attribute]) {
            this.filters[attribute] = [];
        }

        const index = this.filters[attribute].indexOf(value);
        if (index > -1) {
            this.filters[attribute].splice(index, 1);
            if (this.filters[attribute].length === 0) {
                delete this.filters[attribute];
            }
        } else {
            this.filters[attribute].push(value);
        }
    }

    // Build query string
    buildQueryString() {
        const params = new URLSearchParams();

        // Add filters
        Object.entries(this.filters).forEach(([attribute, values]) => {
            params.append(`filter[${attribute}]`, values.join(","));
        });

        // Add pagination
        params.append("page", this.page);
        params.append("per_page", this.perPage);

        // Add sorting
        if (this.sort) {
            params.append("sort", this.sort);
        }

        // Add includes
        params.append("include", "variants,attributeValues,images");

        return params.toString();
    }

    // Fetch products
    async fetchProducts() {
        const queryString = this.buildQueryString();
        const response = await fetch(
            `/api/v1/categories/${this.categorySlug}?${queryString}`,
        );

        if (!response.ok) {
            throw new Error("Failed to fetch products");
        }

        return await response.json();
    }
}

// Usage
const filter = new ProductFilter("rings");

// User clicks "Gold" checkbox
filter.toggleFilterValue("metal", "gold");

// User clicks "Platinum" checkbox
filter.toggleFilterValue("metal", "platinum");

// User clicks "Round" stone shape
filter.toggleFilterValue("stone_shape", "round");

// User changes sort
filter.sort = "-base_price"; // Price high to low

// Fetch filtered products
const result = await filter.fetchProducts();
console.log(result.data.products);
console.log(result.data.facets); // Updated facets based on current filters
```

---

### React Example

```jsx
import { useState, useEffect } from "react";

function CategoryPage({ categorySlug }) {
    const [products, setProducts] = useState([]);
    const [facets, setFacets] = useState([]);
    const [filters, setFilters] = useState({});
    const [meta, setMeta] = useState({});
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        fetchProducts();
    }, [filters, categorySlug]);

    const fetchProducts = async (page = 1) => {
        setLoading(true);

        const params = new URLSearchParams({
            page,
            per_page: 24,
            include: "variants,attributeValues,images",
        });

        // Add filters
        Object.entries(filters).forEach(([attribute, values]) => {
            if (values.length > 0) {
                params.append(`filter[${attribute}]`, values.join(","));
            }
        });

        try {
            const response = await fetch(
                `/api/v1/categories/${categorySlug}?${params}`,
            );
            const result = await response.json();

            setProducts(result.data.products);
            setFacets(result.data.facets);
            setMeta(result.data.meta);
        } catch (error) {
            console.error("Error fetching products:", error);
        } finally {
            setLoading(false);
        }
    };

    const toggleFilter = (attributeSlug, valueSlug) => {
        setFilters((prev) => {
            const current = prev[attributeSlug] || [];
            const updated = current.includes(valueSlug)
                ? current.filter((v) => v !== valueSlug)
                : [...current, valueSlug];

            if (updated.length === 0) {
                const { [attributeSlug]: _, ...rest } = prev;
                return rest;
            }

            return { ...prev, [attributeSlug]: updated };
        });
    };

    return (
        <div className="category-page">
            {/* Filters Sidebar */}
            <aside className="filters">
                <h3>Filters</h3>
                {facets.map((facet) => (
                    <div key={facet.id} className="filter-group">
                        <h4>{facet.name}</h4>
                        {facet.values.map((value) => (
                            <label key={value.id}>
                                <input
                                    type="checkbox"
                                    checked={
                                        filters[facet.slug]?.includes(
                                            value.slug,
                                        ) || false
                                    }
                                    onChange={() =>
                                        toggleFilter(facet.slug, value.slug)
                                    }
                                />
                                {value.value} ({value.products_count})
                            </label>
                        ))}
                    </div>
                ))}
            </aside>

            {/* Products Grid */}
            <main>
                {loading ? (
                    <div>Loading...</div>
                ) : (
                    <>
                        <div className="products-grid">
                            {products.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                />
                            ))}
                        </div>

                        {/* Pagination */}
                        <Pagination meta={meta} onPageChange={fetchProducts} />
                    </>
                )}
            </main>
        </div>
    );
}
```

---

## Query Parameter Reference

| Parameter           | Format                | Example                       | Description                             |
| ------------------- | --------------------- | ----------------------------- | --------------------------------------- |
| `filter[attribute]` | `value1,value2`       | `filter[metal]=gold,platinum` | Filter by attribute values (OR logic)   |
| `sort`              | `field` or `-field`   | `sort=-base_price`            | Sort ascending or descending (- prefix) |
| `page`              | `integer`             | `page=2`                      | Page number for pagination              |
| `per_page`          | `integer`             | `per_page=12`                 | Items per page (default: 24)            |
| `include`           | `relation1,relation2` | `include=variants,images`     | Eager load relationships                |

---

## Available Sort Fields

- `name` - Product name (A-Z)
- `-name` - Product name (Z-A)
- `base_price` - Price low to high
- `-base_price` - Price high to low
- `created_at` - Oldest first
- `-created_at` - Newest first

---

## Tips

1. **Dynamic Facets**: Facets change based on current filters, showing only relevant options
2. **Product Counts**: Each facet value shows how many products have that attribute
3. **Multiple Values**: Use commas to filter by multiple values of the same attribute (OR logic)
4. **Multiple Attributes**: Different attributes use AND logic
5. **Performance**: Use `include` parameter wisely to avoid N+1 queries
