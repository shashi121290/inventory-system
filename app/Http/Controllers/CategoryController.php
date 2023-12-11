<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);
        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        // Detach associated items from the category
        $category->items()->detach();
        // Delete the category
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
