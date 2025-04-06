# Cấu trúc dự án Manga CMS Backend với Laravel 11.x

Dựa trên thiết kế hướng nghiệp vụ (Domain Oriented Design) và tính năng mới của Laravel 11.x, dưới đây là cấu trúc dự án được đề xuất cho Manga CMS Backend:

```
MangaCMS/
├── app/                                # Thư mục ứng dụng Laravel
│   ├── Http/                           # Xử lý HTTP
│   │   ├── Controllers/                # Controllers
│   │   │   └── Api/                    # API Controllers
│   │   │       ├── MangaController.php
│   │   │       ├── ChapterController.php
│   │   │       ├── UserController.php
│   │   │       └── ...
│   │   ├── Requests/                   # Form Requests cho validation
│   │   │   ├── Manga/
│   │   │   ├── Chapter/
│   │   │   └── ...
│   │   ├── Resources/                  # API Resources cho response
│   │   │   ├── Manga/
│   │   │   ├── Chapter/
│   │   │   └── ...
│   │   └── Middleware/                 # Middleware tùy chỉnh
│   ├── Models/                         # Eloquent Models (Infrastructure)
│   │   ├── Manga.php
│   │   ├── Chapter.php
│   │   ├── Page.php
│   │   └── ...
│   └── Providers/                      # Service Providers
│       └── AppServiceProvider.php      # Service Provider chính
├── bootstrap/
│   └── app.php                         # File bootstrap mới của Laravel 11.x
├── config/                             # Cấu hình
├── database/                           # Migrations, seeds
│   ├── migrations/
│   └── seeders/
├── domain/                             # Domain Layer (Core Business Logic)
│   ├── Manga/                          # Domain Manga
│   │   ├── Actions/                    # Business Actions
│   │   │   ├── CreateMangaAction.php
│   │   │   ├── UpdateMangaAction.php
│   │   │   ├── DeleteMangaAction.php
│   │   │   └── ...
│   │   ├── DataTransferObjects/        # DTOs
│   │   │   ├── MangaData.php
│   │   │   └── ...
│   │   ├── Events/                     # Domain Events
│   │   │   ├── MangaCreated.php
│   │   │   └── ...
│   │   ├── Exceptions/                 # Domain Exceptions
│   │   │   └── MangaNotFoundException.php
│   │   ├── Models/                     # Domain Models
│   │   │   └── Manga.php
│   │   └── Repositories/               # Repository Interfaces
│   │       └── MangaRepositoryInterface.php
│   ├── Chapter/                        # Domain Chapter
│   │   ├── Actions/
│   │   ├── DataTransferObjects/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   ├── Models/
│   │   └── Repositories/
│   ├── User/                           # Domain User
│   │   ├── Actions/
│   │   ├── DataTransferObjects/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   ├── Models/
│   │   └── Repositories/
│   └── ...                             # Các Domain khác
├── infrastructure/                     # Infrastructure Layer
│   ├── Repositories/                   # Repository Implementations
│   │   ├── EloquentMangaRepository.php
│   │   ├── EloquentChapterRepository.php
│   │   └── ...
│   ├── Services/                       # External Services
│   │   ├── MediaStorage/
│   │   │   └── CloudStorageService.php
│   │   └── ...
│   └── QueryBuilders/                  # Custom Query Builders
│       ├── MangaQueryBuilder.php
│       └── ...
├── public/                             # Public assets
├── resources/                          # Views, language files
├── routes/                             # Routes
│   ├── api.php                         # API routes
│   ├── web.php                         # Web routes
│   └── console.php                     # Console commands
├── storage/                            # Storage
├── support/                            # Support classes
│   ├── Helpers/                        # Helper functions
│   ├── Traits/                         # Shared traits
│   └── ...
├── tests/                              # Tests
└── vendor/                             # Composer dependencies
```

## Giải thích cấu trúc

### 1. Thư mục `app/`

Chứa các thành phần của Laravel framework, bao gồm:

- **Controllers**: Xử lý request và response, gọi các Actions từ Domain layer
- **Requests**: Validation cho input data
- **Resources**: Định dạng response data
- **Models**: Eloquent models cho tương tác với database
- **Providers**: Service providers cho việc đăng ký các services

### 2. Thư mục `domain/`

Chứa logic nghiệp vụ cốt lõi, được tổ chức theo các domain:

- **Actions**: Các use cases cụ thể (tạo, cập nhật, xóa manga, v.v.)
- **DataTransferObjects**: Đóng gói dữ liệu giữa các layers
- **Events**: Domain events
- **Exceptions**: Domain-specific exceptions
- **Models**: Domain models (không phụ thuộc vào Eloquent)
- **Repositories**: Interfaces cho repositories

### 3. Thư mục `infrastructure/`

Chứa các implementation cụ thể cho interfaces trong domain layer:

- **Repositories**: Eloquent implementations của repository interfaces
- **Services**: Các dịch vụ bên ngoài như lưu trữ media, email, v.v.
- **QueryBuilders**: Custom query builders cho các truy vấn phức tạp

### 4. Thư mục `support/`

Chứa các lớp hỗ trợ, helpers, traits dùng chung trong ứng dụng.

## Luồng xử lý request

1. **Request** đi vào hệ thống qua routes
2. **Controller** nhận request, validate dữ liệu qua Request classes
3. **Controller** gọi **Action** tương ứng từ Domain layer
4. **Action** thực hiện logic nghiệp vụ, sử dụng **Repository** để tương tác với dữ liệu
5. **Repository** (implementation từ Infrastructure layer) tương tác với database
6. **Controller** nhận kết quả từ Action, trả về response thông qua Resource classes

## Lợi ích của cấu trúc này

1. **Tách biệt mối quan tâm**: Logic nghiệp vụ tách biệt khỏi framework và infrastructure
2. **Dễ test**: Domain layer có thể được test độc lập
3. **Dễ bảo trì**: Thay đổi trong một layer không ảnh hưởng đến các layer khác
4. **Dễ mở rộng**: Thêm tính năng mới không làm ảnh hưởng đến code hiện có
5. **Phản ánh nghiệp vụ**: Cấu trúc code phản ánh các khái niệm nghiệp vụ thực tế

## Điều chỉnh cho Laravel 11.x

1. Sử dụng file `bootstrap/app.php` mới của Laravel 11.x để cấu hình ứng dụng
2. Tận dụng event discovery tự động của Laravel 11.x
3. Sử dụng `Schedule` facade trong `routes/console.php` thay vì Console Kernel
4. Tận dụng các middleware được tích hợp sẵn trong framework
