<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        $token = 'test-static-token';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->get('/api/categories');
        $response->assertStatus(200)
            ->assertJsonStructure([
                        '*' => [
                            'id',
                            'name',
                            'description',
                        ],
            ]);
    }

    public function test_can_show_category()
    {
        $category = Category::factory()->create();
        $token = 'test-static-token';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson("/api/categories/{$category->id}");
        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'created_at' => $category->created_at->toISOString(),
                'updated_at' => $category->updated_at->toISOString(),
            ]);
    }

    public function test_can_create_category()
    {
        $categoryData = [
            'name' => 'Test Category',
            'description' => 'This is a test category.',
        ];
        $token = 'test-static-token';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/categories', $categoryData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_can_update_category()
    {
        $category = Category::factory()->create();
        $updatedData = [
            'name' => 'Updated Category',
            'description' => 'This category has been updated.',
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updatedData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', $updatedData);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson("/api/categories/{$category->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
