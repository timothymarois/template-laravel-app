# Routing & Controllers Best Practices

## Use Implicit Route Model Binding

Let Laravel resolve models automatically from route parameters.

Incorrect:
```php
public function show(int $id)
{
    $post = Post::findOrFail($id);
}
```

Correct:
```php
public function show(Post $post)
{
    return view('posts.show', ['post' => $post]);
}
```

## Use Scoped Bindings for Nested Resources

Enforce parent-child relationships automatically.

```php
Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
    // $post is automatically scoped to $user
})->scopeBindings();
```

## Use Resource Controllers

Use `Route::resource()` or `apiResource()` for RESTful endpoints.

Use resource actions while the endpoint fits that contract; split unrelated responsibilities
instead of forcing custom verbs into one controller.

```php
Route::resource('posts', PostController::class);
// In routes/api.php — the /api prefix is applied automatically
Route::apiResource('posts', Api\PostController::class);
```

## Keep Controllers Thin

Keep transport code easy to scan. Extract business logic when reuse, coordinated steps, side
effects, or independent testing earns the boundary; line count alone is not a design rule.

Incorrect:
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    if ($request->hasFile('image')) {
        $request->file('image')->move(public_path('images'));
    }
    $post = Post::create($validated);
    $post->tags()->sync($validated['tags']);
    event(new PostCreated($post));
    return redirect()->route('posts.show', $post);
}
```

Correct:
```php
public function store(StorePostRequest $request, CreatePostAction $create)
{
    $post = $create->execute($request->validated());

    return redirect()->route('posts.show', $post);
}
```

## Type-Hint Form Requests

Type-hinting Form Requests triggers automatic validation and authorization before the method executes.

Incorrect:
```php
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'title' => ['required', 'max:255'],
        'body' => ['required'],
    ]);

    Post::create($validated);

    return redirect()->route('posts.index');
}
```

Correct:
```php
public function store(StorePostRequest $request): RedirectResponse
{
    Post::create($request->validated());

    return redirect()->route('posts.index');
}
```

## Keep Binding and Authorization Separate

Scoped binding proves that a child belongs to its parent. It does not prove that the current user
may access either model.

Incorrect:

```php
Route::get('/accounts/{account}/projects/{project}', [ProjectController::class, 'show'])
    ->scopeBindings();
```

Correct:

```php
public function show(Account $account, Project $project): ProjectResource
{
    Gate::authorize('view', $project);

    return new ProjectResource($project);
}
```

## Prevent Queries During Resource Serialization

Incorrect — serialization can lazy-load once per resource:

```php
'items' => ItemResource::collection($this->items),
```

Correct — eager load at the query site and serialize only the relation that was intentionally
loaded:

```php
$orders = Order::with('items')->paginate();

// OrderResource
'items' => ItemResource::collection($this->whenLoaded('items')),
```

## Test the Whole Redirected Session Flow

Flash data lasts for the next request. If a later workflow step needs the value after the redirect
target renders, store it until that step consumes it.

```php
$request->session()->put('workflow.result', $result);

// In the later request:
$result = $request->session()->pull('workflow.result');
```

Exercise the POST, redirect target, and later request in order. A test that stops after the POST can
miss the browser-visible expiry.

## Follow the Installed Application Skeleton

New Laravel 11+ applications configure middleware and exceptions through `bootstrap/app.php`, while
upgraded applications may intentionally retain `app/Http/Kernel.php` and
`app/Exceptions/Handler.php`. Extend the boundary the repository actually uses.
