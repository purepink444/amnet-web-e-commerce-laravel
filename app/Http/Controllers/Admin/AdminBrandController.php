<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Brand::query();

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where('brand_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $brands = $query->latest()->paginate(15);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255|unique:brands,brand_name',
            'description' => 'nullable|string|max:1000',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle logo upload
        if ($request->hasFile('brand_logo')) {
            $logoName = time() . '_' . Str::slug($request->brand_name) . '.' . $request->brand_logo->extension();
            $validated['brand_logo'] = $request->brand_logo->storeAs('brands', $logoName, 'public');
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'เพิ่มแบรนด์สำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): View
    {
        $brand->load(['products' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255|unique:brands,brand_name,' . $brand->brand_id . ',brand_id',
            'description' => 'nullable|string|max:1000',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle logo upload
        if ($request->hasFile('brand_logo')) {
            // Delete old logo
            if ($brand->brand_logo && Storage::disk('public')->exists($brand->brand_logo)) {
                Storage::disk('public')->delete($brand->brand_logo);
            }

            $logoName = time() . '_' . Str::slug($request->brand_name) . '.' . $request->brand_logo->extension();
            $validated['brand_logo'] = $request->brand_logo->storeAs('brands', $logoName, 'public');
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'อัปเดทแบรนด์สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        // Check if brand has products
        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'ไม่สามารถลบแบรนด์ที่ยังมีสินค้าอยู่ได้');
        }

        // Delete logo
        if ($brand->brand_logo && Storage::disk('public')->exists($brand->brand_logo)) {
            Storage::disk('public')->delete($brand->brand_logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'ลบแบรนด์สำเร็จ');
    }

    /**
     * Bulk delete brands
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'กรุณาเลือกแบรนด์ที่ต้องการลบ');
        }

        $brands = Brand::whereIn('brand_id', $ids)->get();

        foreach ($brands as $brand) {
            // Check if brand has products
            if ($brand->products()->count() > 0) {
                return redirect()->route('admin.brands.index')
                    ->with('error', 'ไม่สามารถลบแบรนด์ "' . $brand->brand_name . '" ที่ยังมีสินค้าอยู่ได้');
            }

            // Delete logo
            if ($brand->brand_logo && Storage::disk('public')->exists($brand->brand_logo)) {
                Storage::disk('public')->delete($brand->brand_logo);
            }

            $brand->delete();
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'ลบแบรนด์ ' . count($brands) . ' รายการสำเร็จ');
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status');

        if (empty($ids) || !in_array($status, ['active', 'inactive'])) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'ข้อมูลไม่ถูกต้อง');
        }

        Brand::whereIn('brand_id', $ids)->update(['status' => $status]);

        return redirect()->route('admin.brands.index')
            ->with('success', 'อัปเดทสถานะ ' . count($ids) . ' รายการสำเร็จ');
    }
}
