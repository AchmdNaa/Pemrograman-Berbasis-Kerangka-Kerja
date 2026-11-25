<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'edit'       => $request->query('edit') ? Category::find($request->query('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('categories.index')->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori {$category->name} masih memiliki produk.");
        }
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori dihapus.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:50', Rule::unique('categories', 'name')->ignore($category?->id)],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ], [], ['name' => 'nama kategori', 'color' => 'warna']);
    }
}
