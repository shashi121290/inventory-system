# Inventory System

## Prerequisites:
- PHP 8.2 FPM
- Laravel 10.*
- MySQL
- PHP Unit
- Composer

## Test Coverage:
![Test Coverage](https://github.com/shashi121290/inventory-system/assets/153260639/7e0f105d-d3cc-4957-b77d-d156e79bbd75)

## API Endpoints

### Categories

- **GET /categories:** Retrieve all categories.
- **GET /categories/{id}:** Retrieve a specific category by ID.
- **POST /categories:** Create a new category.
- **PUT /categories/{id}:** Update a specific category by ID.
- **DELETE /categories/{id}:** Delete a specific category by ID.

#### Example Requests and Responses

##### Create a Category

```http
POST http://127.0.0.1:8000/api/categories/
{
    "name": "Car",
    "description": "Car with 4 wheels",
    "updated_at": "2023-12-11T07:14:07.000000Z",
    "created_at": "2023-12-11T07:14:07.000000Z",
    "id": 10
}

Get Categories:
GET http://127.0.0.1:8000/api/categories/
[
    {
        "id": 1,
        "name": "Category Name",
        "description": "Category Description",
        "created_at": "2023-12-11T05:07:00.000000Z",
        "updated_at": "2023-12-11T05:07:00.000000Z"
    },
    // Other category objects
]

Delete a Category:
DELETE http://127.0.0.1:8000/api/categories/1
{
    "message": "Category deleted successfully"
}

Update a Category:
PUT http://127.0.0.1:8000/api/categories/2
{
    "id": 2,
    "name": "Cloths",
    "description": "Kids Cloths",
    "created_at": "2023-12-11T05:10:57.000000Z",
    "updated_at": "2023-12-11T07:19:00.000000Z"
}

**Items**

GET /items: Retrieve all items.
GET /items/{id}: Retrieve a specific item by ID.
POST /items: Create a new item.
PUT /items/{id}: Update a specific item by ID.
DELETE /items/{id}: Delete a specific item by ID.

Create an Item:
POST http://127.0.0.1:8000/api/items/
{
    "name": "Item Name",
    "description": "Item Description",
    "category_ids": [1, 2]
}

Get Items:
GET http://127.0.0.1:8000/api/items/
[
    {
        "id": 2,
        "name": "Item2",
        "description": "Item2 Description1",
        "price": "6.00",
        "quantity": 1,
        "created_at": "2023-12-11T06:22:42.000000Z",
        "updated_at": "2023-12-11T07:23:30.000000Z",
        "categories": [
            // Category object
        ]
    },
    // Other item objects
]

Update an Item:
PUT http://127.0.0.1:8000/api/items/1
{
    "name": "Item2",
    "description": "Item2 Description1",
    "price": 6,
    "quantity": 1,
    "category_ids": [3]
}

Delete an Item:
DELETE http://127.0.0.1:8000/api/items/1
{
    "message": "Item deleted successfully"
}
