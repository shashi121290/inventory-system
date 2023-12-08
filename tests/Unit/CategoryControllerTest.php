<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        // Arrange
        $categories = Category::factory(3)->create();

        // Act
        $response = $this->getJson('/api/categories');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_can_show_category()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->getJson("/api/categories/{$category->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['data' => $category->toArray()]);
    }

    public function test_can_create_category()
    {
        // Arrange
        $categoryData = [
            'name' => 'Test Category',
            'description' => 'This is a test category.',
        ];

        // Act
        $response = $this->postJson('/api/categories', $categoryData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_can_update_category()
    {
        // Arrange
        $category = Category::factory()->create();
        $updatedData = [
            'name' => 'Updated Category',
            'description' => 'This category has been updated.',
        ];

        // Act
        $response = $this->putJson("/api/categories/{$category->id}", $updatedData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', $updatedData);
    }

    public function test_can_delete_category()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->deleteJson("/api/categories/{$category->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
