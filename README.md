# Blog API

This project is a RESTful API for a simple blog application built with Laravel. It allows users to register, create blog posts, leave comments, and perform various CRUD operations while adhering to REST principles. The API is secured using Laravel Sanctum for user authentication.

## Features

-   User Registration & Login
-   CRUD operations for blog posts
-   CRUD operations for comments
-   Search posts by title and filter posts by status (published or draft)
-   Pagination for listing published posts
-   Role-based access control for admin (optional)
-   Validation for all input fields
-   API Documentation using Postman or Swagger (optional)

## Requirements

-   PHP >= 8.1
-   Composer
-   Laravel 10.x
-   MySQL or any other supported database
-   Postman (optional, for testing and documentation)

## Installation

### 1. Clone the repository:

```bash
https://github.com/nimeshdkumarawansha/blog-api.git
cd blog-api
```

### 2. Install dependencies:

```bash
composer install
```

### 3. Set up environment variables:

Rename .env.example to .env and configure your database and other environment settings:

```bash
cp .env.example .env
```

Update your .env file:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 4. Generate the application key:

```bash
php artisan key:generate
```

### 5. Run database migrations:

```bash
php artisan migrate
```

### 6. Install Laravel Sanctum:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 7. Run the development server:

```bash
php artisan serve
```

# API Endpoints

## Authentication

-   POST /api/register - Register a new user
-   POST /api/login - Log in a user and get an access token

## Blog Posts

-   GET /api/posts - Get a list of published posts with pagination
-   POST /api/posts - Create a new post (Authenticated)
-   PUT/PATCH /api/posts/{id} - Update a post (Authenticated, only author)
-   DELETE /api/posts/{id} - Delete a post (Authenticated, only author)

## Comments

-   POST /api/posts/{post_id}/comments - Add a comment to a post (Authenticated)
-   PUT/PATCH /api/posts/{post_id}/comments/{id} - Update a comment (Authenticated, only author)
-   DELETE /api/posts/{post_id}/comments/{id} - Delete a comment (Authenticated, only author)

## Search and Filtering

You can search and filter blog posts using query parameters:

-   GET /api/posts?search=keyword - Search posts by title
-   GET /api/posts?status=published - Filter posts by their status (published or draft)

## Testing

To run the tests, use the following command:

```bash
php artisan test
```

## API Documentation

You can use Postman or Swagger to document and test the API endpoints. Postman is recommended for manual testing, while Swagger can be used to generate documentation dynamically.
