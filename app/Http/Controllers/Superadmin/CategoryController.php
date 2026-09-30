<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('manageCategories', Catalog::class);

        $categories = Category::query()
            ->withCount('catalogs')
            ->withCount('subcategories')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('superadmin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->authorize('manageCategories', Catalog::class);

        return view('superadmin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manageCategories', Catalog::class);

        if (! $request->filled('slug') && is_string($request->input('name'))) {
            $request->merge(['slug' => Str::slug($request->input('name'))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:categories,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        Category::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('manageCategories', Catalog::class);

        return view('superadmin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('manageCategories', Catalog::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $category->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('superadmin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('manageCategories', Catalog::class);

        if ($category->catalogs()->exists()) {
            return back()->with('error', 'Kategori sedang digunakan oleh katalog. Nonaktifkan kategori jika tidak ingin digunakan lagi.');
        }

        $category->delete();

        return redirect()->route('superadmin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
