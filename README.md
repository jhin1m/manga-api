# Manga CMS Backend

Manga CMS Backend là một hệ thống quản lý nội dung manga được xây dựng bằng Laravel 11.x, áp dụng kiến trúc Domain-Driven Design (DDD).

## Tính năng chính

- 📚 Quản lý manga và chương
- 👥 Hệ thống người dùng và phân quyền
- 🔍 Tìm kiếm nâng cao với nhiều bộ lọc
- 📖 Theo dõi lịch sử đọc
- ⭐ Đánh dấu và đánh giá
- 💬 Bình luận và tương tác
- 👥 Quản lý nhóm dịch
- 🖼️ Quản lý media tối ưu
- 🔔 Hệ thống thông báo

## Yêu cầu hệ thống

- PHP >= 8.2
- Laravel 11.x
- MySQL/MariaDB
- Redis (cho caching)
- Node.js và PNPM (cho asset building)

## Cài đặt

1. Clone repository:
```bash
git clone https://github.com/jhin1m/manga-api.git
cd manga-cms
```

2. Cài đặt dependencies:
```bash
composer install
pnpm install
```

3. Tạo file .env:
```bash
cp .env.example .env
```

4. Cấu hình database trong .env:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manga_cms
DB_USERNAME=root
DB_PASSWORD=
```

5. Tạo application key:
```bash
php artisan key:generate
```

6. Chạy migrations:
```bash
php artisan migrate
```

7. Chạy seeders (tùy chọn):
```bash
php artisan db:seed
```

8. Build assets:
```bash
pnpm run build
```

## Cấu trúc dự án

```
MangaCMS/
├── app/                    # Laravel application
│   ├── Http/              # Controllers, Requests, Resources
│   ├── Models/            # Eloquent Models
│   └── Providers/         # Service Providers
├── domain/                # Domain Layer (Core Business Logic)
│   ├── Manga/            # Manga Domain
│   ├── Chapter/          # Chapter Domain
│   └── User/             # User Domain
├── infrastructure/        # Infrastructure Layer
│   ├── Repositories/     # Repository Implementations
│   └── Services/         # External Services
└── ...
```

## API Endpoints

### Manga

- `GET /api/v1/mangas` - Danh sách manga
- `GET /api/v1/mangas/{slug}` - Chi tiết manga
- `POST /api/v1/mangas` - Tạo manga mới
- `PUT /api/v1/mangas/{slug}` - Cập nhật manga
- `DELETE /api/v1/mangas/{slug}` - Xóa manga

### Chapter

- `GET /api/v1/mangas/{slug}/chapters` - Danh sách chapter
- `GET /api/v1/chapters/{id}` - Chi tiết chapter
- `POST /api/v1/chapters` - Tạo chapter mới
- `PUT /api/v1/chapters/{id}` - Cập nhật chapter
- `DELETE /api/v1/chapters/{id}` - Xóa chapter

### User

- `POST /api/v1/auth/register` - Đăng ký
- `POST /api/v1/auth/login` - Đăng nhập
- `POST /api/v1/auth/logout` - Đăng xuất
- `GET /api/v1/user/profile` - Thông tin người dùng
- `PUT /api/v1/user/profile` - Cập nhật thông tin

## Phát triển

### Thêm tính năng mới

1. Xác định yêu cầu nghiệp vụ
2. Thiết kế Domain Model
3. Triển khai Infrastructure Layer
4. Triển khai Application Layer
5. Triển khai Presentation Layer
6. Triển khai Authorization
7. Kiểm thử
8. Cập nhật tài liệu

### Coding Standards

- Tuân thủ PSR-12
- Sử dụng kiến trúc DDD
- Viết unit tests cho logic nghiệp vụ
- Tài liệu hóa API endpoints
- Sử dụng type hints và return types

## Bảo mật

- Xác thực API bằng Laravel Sanctum
- Phân quyền dựa trên vai trò (RBAC)
- Validate tất cả input
- Bảo vệ khỏi CSRF
- Rate limiting cho API endpoints
- Upload file an toàn

## Tối ưu hiệu suất

- Caching với Redis
- Eager loading để tránh N+1 queries
- Phân trang kết quả
- Index database hợp lý
- Tối ưu hình ảnh và thumbnails
- Soft deletes cho dữ liệu quan trọng

## Đóng góp

1. Fork repository
2. Tạo branch mới (`git checkout -b feature/amazing-feature`)
3. Commit thay đổi (`git commit -m 'Add amazing feature'`)
4. Push lên branch (`git push origin feature/amazing-feature`)
5. Tạo Pull Request

## License

MIT License - xem [LICENSE](LICENSE) để biết thêm chi tiết.

## Liên hệ

- Website: [jhin1m.github.io](https://jhin1m.github.io)
- Email: ducanhfake@gmail.com
- Issue Tracker: [GitHub Issues](https://github.com/jhin1m/manga-api/issues)
