<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Notifications\NewItemNotification;
use App\Notifications\UpdatedItemNotification;
use App\Notifications\DeletedItemNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index()
    {
        try {
            $items = Item::with('categories')->get();
            return response()->json($items);
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in ItemController@index: ' . $e->getMessage());
            // Return a custom error response
            return response()->json(['error' => 'An error occurred while fetching items.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $item = Item::with('categories')->findOrFail($id);
            return response()->json($item);
        } catch (ModelNotFoundException $e) {
            Log::error("Item with ID $id not found.");
            return response()->json(['error' => 'Item not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error while fetching item with ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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
            
            // Sending email notification on create item
            $user->notify(new NewItemNotification($item));
            return response()->json($item, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
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

            // Sending email notification on update item
            $user = User::where('id', 1)->first();
            $user->notify(new UpdatedItemNotification($item));
            return response()->json($item, 200);
        } catch (ModelNotFoundException $e) {
            Log::error("Item with ID $id not found.");
            return response()->json(['error' => 'Item not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the item.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->categories()->detach(); // Detach categories before deleting the item
            $item->delete();

            $user = User::findOrFail(1);
            //sending email notification on delete item
            $user->notify(new DeletedItemNotification($item));
            return response()->json(['message' => 'Item deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete item'], 500);
        }
    }
}
