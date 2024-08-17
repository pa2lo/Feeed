<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
		// Post::withCount('comments')->get();
        return inertia('Categories/Index', [
			'items' => Category::all()
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
			'name' => 'required|min:3'
		]);

		$newCategory = Category::create([
			'name' => $request->name
		]);

		return back()->with(['success' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category) {
        $request->validate([
			'name' => 'required|min:3'
		]);

		$category->update([
			'name' => $request->name
		]);

		return back()->with(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category) {
        $category->delete();
		return back()->with(['success' => true]);
    }
}
