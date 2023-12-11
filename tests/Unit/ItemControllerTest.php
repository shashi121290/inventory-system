<?php
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class ItemControllerTest extends BaseTestCase
{
    use CreatesApplication;

    public function testIndex()
    {
        $response = $this->get('/api/items');
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'quantity',
                    'categories' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                        ],
                    ],
                ],
            ]);
    }

    public function testShow()
    {
        $newItem = Item::create([
            'name' => 'Test Item',
            'description' => 'Test Description',
            'price' => 10.99,
            'quantity' => 5,
        ]);
        $response = $this->getJson("/api/items/{$newItem->id}");
        $response->assertStatus(200)
        ->assertJson([
            'id' => $newItem->id,
            'name' => $newItem->name,
            'description' => $newItem->description,
            'price' => $newItem->price,
            'quantity' => $newItem->quantity,
        ]);
    }

    public function testStore()
    {
        $categories = Category::factory()->count(2)->create();
        $data = [
            'name' => 'Test Item',
            'description' => 'Test Description',
            'price' => 10.99,
            'quantity' => 5,
            'category_ids' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->post('/api/items', $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'price',
                'quantity',
                'created_at',
                'updated_at',
                'categories' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at',
                        'pivot'=>[
                            'item_id',
                            'category_id'
                        ]
                    ],
                ],
            ]);
    }
}
