# Coding Standards & Architecture Rules

## 1. Controller Architecture

### Slim Controllers

Controllers must be kept "thin" or "slim". They should serve strictly as an entry point for the HTTP layer.

**Responsibilities:**

- Validating incoming requests (delegate to Form Requests).
- Unpacking request data.
- Delegating business logic to **Actions**.
- Transforming the result into a response (e.g., JSON Resource, View).

**Restrictions:**

- Controllers **must not** contain business logic.
- Controllers **must not** perform direct database queries (except for simple lookups if absolutely necessary, but prefer Actions/Repositories).

## 2. Action Classes

### Structure

- All business logic should be encapsulated within **Action** classes.
- Actions must use a single public method named `execute`.

### Reusability

- Actions must be reusable in any context (e.g., Controllers, Console Commands, Queued Jobs, Tests).
- Actions **must not** depend on the HTTP layer.

### Inputs (Parameters)

- The `execute` method **must not** accept the `Illuminate\Http\Request` object or use global `request()` helpers.
- Instead, accept an **explicit `array`** of data.

### Outputs (Return Types)

- The `execute` method must have an **explicit array return type** (e.g., `: array`).

## 3. Code Examples

### Correct Action Implementation

```php
namespace App\Actions;

use App\Models\Product;

class CreateProductAction
{
    /**
     * Execute the action.
     *
     * @param array $data Input data independent of the HTTP request
     * @return array Explicit array return type
     */
    public function execute(array $data): array
    {
        $product = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
        ]);

        // Perform other business logic...

        return $product->toArray();
    }
}
```

### Correct Controller Implementation

```php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Actions\CreateProductAction;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request, CreateProductAction $action): JsonResponse
    {
        // Controller delegates to the action, passing an array of validated data
        $result = $action->execute($request->validated());

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $result
        ], 201);
    }
}
```

### Incorrect Implementation (Avoid)

```php
// ❌ BAD: Controller contains logic and accepts Request in methods that handle logic
public function store(Request $request)
{
    // Logic polluting the controller
    $product = Product::create($request->all());
    return $product;
}

// ❌ BAD: Action accepts Request object
class CreateProductAction
{
    public function execute(Request $request) // Dependent on HTTP layer
    {
        // ...
    }
}
```
