<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Category::query();

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where('category_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $categories = $query->latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description' => 'nullable|string|max:1000',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('category_image')) {
            $imageName = time() . '_' . Str::slug($request->category_name) . '.' . $request->category_image->extension();
            $validated['category_image'] = $request->category_image->storeAs('categories', $imageName, 'public');
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'เพิ่มหมวดหมู่สำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category->load(['products' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
            'description' => 'nullable|string|max:1000',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('category_image')) {
            // Delete old image
            if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
                Storage::disk('public')->delete($category->category_image);
            }

            $imageName = time() . '_' . Str::slug($request->category_name) . '.' . $request->category_image->extension();
            $validated['category_image'] = $request->category_image->storeAs('categories', $imageName, 'public');
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'อัปเดทหมวดหมู่สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่ที่ยังมีสินค้าอยู่ได้');
        }

        // Delete image
        if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
            Storage::disk('public')->delete($category->category_image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'ลบหมวดหมู่สำเร็จ');
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'กรุณาเลือกหมวดหมู่ที่ต้องการลบ');
        }

        $categories = Category::whereIn('category_id', $ids)->get();

        foreach ($categories as $category) {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'ไม่สามารถลบหมวดหมู่ "' . $category->category_name . '" ที่ยังมีสินค้าอยู่ได้');
            }

            // Delete image
            if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
                Storage::disk('public')->delete($category->category_image);
            }

            $category->delete();
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'ลบหมวดหมู่ ' . count($categories) . ' รายการสำเร็จ');
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status');

        if (empty($ids) || !in_array($status, ['active', 'inactive'])) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'ข้อมูลไม่ถูกต้อง');
        }

        Category::whereIn('category_id', $ids)->update(['status' => $status]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'อัปเดทสถานะ ' . count($ids) . ' รายการสำเร็จ');
    }
}
