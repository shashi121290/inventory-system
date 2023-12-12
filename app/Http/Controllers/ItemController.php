<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\NewItemNotification;
use App\Notifications\UpdatedItemNotification;
use App\Notifications\DeletedItemNotification;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('categories')->get();
        return response()->json($items);
    }

    public function show($id)
    {
        $item = Item::with('categories')->findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id', // Validate that each category ID exists in the 'categories' table
        ]);

        $item = Item::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
        ]);

        // Attach categories to the item
        $item->categories()->attach($request->input('category_ids'));
        $user = User::where('id', 1)->first();
        //sending email notification on create item
        $user->notify(new NewItemNotification($item));
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'quantity' => 'integer|min:0',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id', // Validate that each category ID exists in the 'categories' table
        ]);

        $item = Item::findOrFail($id);
        $item->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
        ]);

        // Sync categories for the item
        if ($request->has('category_ids')) {
            $item->categories()->sync($request->input('category_ids'));
        }

        //sending email notification on update item
        $user = User::where('id', 1)->first(); 
        $user->notify(new UpdatedItemNotification($item));
        return response()->json($item, 200);
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->categories()->detach(); // Detach categories before deleting the item
        $item->delete();
        $user = User::where('id', 1)->first();

        //sending email notification on update item
        $user->notify(new DeletedItemNotification($item));
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
