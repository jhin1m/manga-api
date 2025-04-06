# Manga CMS Backend Implementation Documentation

## Overview

This document provides a comprehensive overview of the Manga CMS Backend implementation using Laravel 11.x and Domain-Driven Design (DDD) principles. The backend is designed to support a feature-rich manga reading platform with advanced features like reading history tracking, bookmarking, ratings, and search capabilities.

## Architecture

The Manga CMS Backend follows a Domain-Driven Design architecture with clear separation of concerns:

### Layers

1. **Domain Layer**
   - Contains the core business logic and domain models
   - Independent of framework and infrastructure details
   - Includes entities, value objects, domain events, and repository interfaces

2. **Application Layer**
   - Orchestrates the use cases of the application
   - Contains actions (use cases) that coordinate domain objects
   - Handles application-specific logic

3. **Infrastructure Layer**
   - Provides implementations for interfaces defined in the domain layer
   - Handles technical concerns like database access, file storage, and caching
   - Adapts external services to the domain

4. **Presentation Layer**
   - Handles HTTP requests and responses
   - Contains controllers, requests, and resources
   - Transforms domain objects to API responses

### Key Components

#### Domain Models

Domain models represent the core business entities and encapsulate business rules:

- **Manga**: Represents a manga series with properties like title, description, status, etc.
- **Chapter**: Represents a chapter of a manga with properties like chapter number, pages, etc.
- **User**: Represents a user of the system with properties like username, email, role, etc.
- **ReadingHistory**: Tracks a user's reading progress for a specific manga chapter.
- **Bookmark**: Represents a user's bookmark for a manga.
- **Rating**: Represents a user's rating and review for a manga.

#### Data Transfer Objects (DTOs)

DTOs are used to transfer data between layers without exposing domain implementation details:

- **MangaData**: Contains data for creating or updating a manga.
- **ChapterData**: Contains data for creating or updating a chapter.
- **UserData**: Contains data for creating or updating a user.

#### Repository Interfaces

Repository interfaces define the contract for data access:

- **MangaRepositoryInterface**: Defines methods for manga data access.
- **ChapterRepositoryInterface**: Defines methods for chapter data access.
- **UserRepositoryInterface**: Defines methods for user data access.

#### Actions

Actions implement use cases and orchestrate domain objects:

- **CreateMangaAction**: Creates a new manga.
- **UpdateMangaAction**: Updates an existing manga.
- **DeleteMangaAction**: Deletes a manga.
- **CreateChapterAction**: Creates a new chapter.
- **UpdateChapterAction**: Updates an existing chapter.
- **DeleteChapterAction**: Deletes a chapter.
- **CreateUserAction**: Creates a new user.
- **UpdateUserAction**: Updates an existing user.
- **DeleteUserAction**: Deletes a user.

#### Events

Events are used to decouple components and implement event-driven architecture:

- **MangaCreated**: Triggered when a manga is created.
- **ChapterCreated**: Triggered when a chapter is created.
- **UserCreated**: Triggered when a user is created.

#### Services

Services provide specialized functionality:

- **MediaStorageService**: Handles file uploads, thumbnail generation, and file deletion.
- **SearchService**: Provides advanced search capabilities with multiple filters and sorting options.
- **CacheService**: Optimizes performance by caching frequently accessed data.

## Database Schema

The database schema consists of the following tables:

1. **mangas**
   - id (primary key)
   - title
   - slug (unique)
   - description
   - status (ongoing, completed, hiatus, cancelled)
   - cover_image
   - thumbnail
   - author_id (foreign key)
   - artist_id (foreign key)
   - release_year
   - is_featured
   - is_published
   - views
   - average_rating
   - created_at
   - updated_at
   - deleted_at (soft delete)

2. **chapters**
   - id (primary key)
   - manga_id (foreign key)
   - chapter_number
   - title
   - slug (unique)
   - description
   - release_date
   - views
   - is_published
   - created_at
   - updated_at
   - deleted_at (soft delete)

3. **pages**
   - id (primary key)
   - chapter_id (foreign key)
   - page_number
   - image
   - thumbnail
   - created_at
   - updated_at

4. **users**
   - id (primary key)
   - username (unique)
   - email (unique)
   - password
   - role (admin, mod, translator, user)
   - avatar
   - bio
   - remember_token
   - created_at
   - updated_at
   - deleted_at (soft delete)

5. **authors**
   - id (primary key)
   - name
   - slug (unique)
   - description
   - image
   - created_at
   - updated_at

6. **artists**
   - id (primary key)
   - name
   - slug (unique)
   - description
   - image
   - created_at
   - updated_at

7. **categories**
   - id (primary key)
   - name
   - slug (unique)
   - description
   - created_at
   - updated_at

8. **tags**
   - id (primary key)
   - name
   - slug (unique)
   - created_at
   - updated_at

9. **category_manga** (pivot)
   - category_id (foreign key)
   - manga_id (foreign key)

10. **manga_tag** (pivot)
    - manga_id (foreign key)
    - tag_id (foreign key)

11. **reading_histories**
    - id (primary key)
    - user_id (foreign key)
    - manga_id (foreign key)
    - chapter_id (foreign key)
    - chapter_number
    - page_number
    - last_read_at
    - created_at
    - updated_at

12. **bookmarks**
    - id (primary key)
    - user_id (foreign key)
    - manga_id (foreign key)
    - created_at
    - updated_at

13. **ratings**
    - id (primary key)
    - user_id (foreign key)
    - manga_id (foreign key)
    - score
    - comment
    - created_at
    - updated_at

14. **comments**
    - id (primary key)
    - user_id (foreign key)
    - commentable_id
    - commentable_type
    - parent_id (self-referencing foreign key)
    - content
    - created_at
    - updated_at
    - deleted_at (soft delete)

15. **notifications**
    - id (primary key)
    - user_id (foreign key)
    - type
    - notifiable_id
    - notifiable_type
    - data
    - read_at
    - created_at
    - updated_at

16. **translation_groups**
    - id (primary key)
    - name
    - slug (unique)
    - description
    - logo
    - created_at
    - updated_at

17. **group_user** (pivot)
    - group_id (foreign key)
    - user_id (foreign key)
    - role (leader, translator, editor, etc.)
    - created_at
    - updated_at

18. **manga_group** (pivot)
    - manga_id (foreign key)
    - group_id (foreign key)
    - created_at
    - updated_at

19. **media**
    - id (primary key)
    - model_id
    - model_type
    - collection_name
    - name
    - file_name
    - mime_type
    - size
    - created_at
    - updated_at

20. **settings**
    - id (primary key)
    - key (unique)
    - value
    - created_at
    - updated_at

## Authentication and Authorization

The Manga CMS Backend uses Laravel Sanctum for API authentication and implements a role-based access control system.

### Authentication

- **Token-based Authentication**: Laravel Sanctum provides token-based authentication for API access.
- **Registration**: Users can register with username, email, and password.
- **Login**: Users can login with email and password to receive an API token.
- **Logout**: Users can logout to invalidate their API token.
- **Token Refresh**: Users can refresh their API token for extended sessions.

### Authorization

- **Role-based Access Control**: Users have roles (admin, mod, translator, user) that determine their permissions.
- **Middleware**: The `CheckRole` middleware verifies user roles for protected routes.
- **Policies**: Policies define fine-grained permissions for specific resources:
  - **MangaPolicy**: Controls access to manga resources.
  - **ChapterPolicy**: Controls access to chapter resources.
  - **UserPolicy**: Controls access to user resources.

## Advanced Features

### Media Management

The `MediaStorageService` handles file uploads, thumbnail generation, and file deletion:

- **File Upload**: Stores uploaded files in the specified directory.
- **Thumbnail Generation**: Automatically creates thumbnails for image files.
- **File Deletion**: Removes files and their thumbnails.

### Reading History

The reading history feature tracks users' reading progress:

- **Track Progress**: Records which chapter and page a user last read.
- **Resume Reading**: Allows users to continue reading from where they left off.
- **Reading History List**: Shows users their reading history.

### Bookmarks

The bookmark feature allows users to save their favorite manga:

- **Add Bookmark**: Users can bookmark manga they like.
- **Remove Bookmark**: Users can remove bookmarks.
- **Bookmark List**: Shows users their bookmarked manga.

### Ratings and Reviews

The rating system allows users to rate and review manga:

- **Rate Manga**: Users can give a score (1-5) to manga.
- **Write Review**: Users can write a review along with their rating.
- **View Ratings**: Shows average rating and individual reviews for manga.

### Advanced Search

The `SearchService` provides powerful search capabilities:

- **Text Search**: Search manga by title or description.
- **Filters**: Filter by status, release year, author, artist, categories, and tags.
- **Sorting**: Sort results by various criteria like title, views, rating, etc.
- **Pagination**: Paginate search results for better performance.

### Caching

The `CacheService` optimizes performance by caching frequently accessed data:

- **Cache Management**: Methods for storing, retrieving, and invalidating cached data.
- **Key Generation**: Helper methods for generating consistent cache keys.
- **Error Handling**: Graceful fallback if caching fails.

## API Endpoints

The API endpoints are organized by resource and follow RESTful principles. See the [API Documentation](api_documentation.md) for detailed information on all available endpoints, request/response formats, and error handling.

## Performance Optimizations

Several optimizations have been implemented to ensure good performance:

1. **Caching**: Frequently accessed data is cached to reduce database queries.
2. **Eager Loading**: Related models are eager loaded to prevent N+1 query problems.
3. **Pagination**: Large result sets are paginated to reduce memory usage and response size.
4. **Indexing**: Database tables have appropriate indexes for frequently queried columns.
5. **Soft Deletes**: Important data uses soft deletes to prevent accidental data loss.
6. **Thumbnails**: Images have thumbnails to reduce bandwidth usage for previews.

## Security Considerations

The Manga CMS Backend implements several security measures:

1. **Authentication**: API endpoints are protected with token-based authentication.
2. **Authorization**: Role-based access control and policies restrict access to resources.
3. **Input Validation**: All user input is validated before processing.
4. **Password Hashing**: User passwords are hashed using bcrypt.
5. **CSRF Protection**: Cross-Site Request Forgery protection for web routes.
6. **Rate Limiting**: API requests are rate limited to prevent abuse.
7. **Secure File Uploads**: File uploads are validated and stored securely.

## Conclusion

The Manga CMS Backend provides a robust foundation for a feature-rich manga reading platform. By following Domain-Driven Design principles, the codebase is well-organized, maintainable, and extensible. The implementation includes all the core features required for a manga CMS as well as advanced features like reading history tracking, bookmarking, ratings, and search capabilities.
