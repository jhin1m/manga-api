# Thiết kế Cơ sở dữ liệu cho Manga CMS

Dựa trên tài liệu được cung cấp và các thực hành tốt nhất của Laravel 11.x, dưới đây là thiết kế cơ sở dữ liệu cho Manga CMS.

## Bảng dữ liệu

### 1. users (Người dùng)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username', 50)->unique();
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['admin', 'user', 'mod', 'translator'])->default('user');
    $table->string('avatar')->nullable();
    $table->text('bio')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes(); // Thêm soft delete để không mất dữ liệu quan trọng
});
```

### 2. authors (Tác giả)
```php
Schema::create('authors', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('avatar')->nullable();
    $table->timestamps();
});
```

### 3. artists (Họa sĩ)
```php
Schema::create('artists', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('avatar')->nullable();
    $table->timestamps();
});
```

### 4. categories (Thể loại)
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique();
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 5. tags (Thẻ)
```php
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique();
    $table->string('slug', 100)->unique();
    $table->timestamps();
});
```

### 6. mangas (Truyện)
```php
Schema::create('mangas', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->enum('status', ['ongoing', 'completed', 'hiatus'])->default('ongoing');
    $table->string('cover_image')->nullable();
    $table->string('thumbnail')->nullable(); // Thêm thumbnail để tối ưu hiển thị
    $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('artist_id')->nullable()->constrained()->nullOnDelete();
    $table->year('release_year')->nullable();
    $table->boolean('is_featured')->default(false); // Đánh dấu truyện nổi bật
    $table->boolean('is_published')->default(true); // Trạng thái xuất bản
    $table->integer('views')->default(0); // Số lượt xem
    $table->float('average_rating')->default(0); // Đánh giá trung bình
    $table->timestamps();
    $table->softDeletes(); // Thêm soft delete để không mất dữ liệu quan trọng
});
```

### 7. chapters (Chương)
```php
Schema::create('chapters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->float('chapter_number'); // Cho phép số thập phân như 1.5, 2.5
    $table->string('title')->nullable();
    $table->string('slug');
    $table->text('description')->nullable();
    $table->dateTime('release_date')->nullable();
    $table->integer('views')->default(0);
    $table->boolean('is_published')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->unique(['manga_id', 'chapter_number']); // Không cho phép trùng số chapter trong cùng manga
    $table->index(['manga_id', 'is_published', 'release_date']); // Index cho việc tìm kiếm nhanh
});
```

### 8. pages (Trang)
```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
    $table->integer('page_number');
    $table->string('image_url');
    $table->string('thumbnail_url')->nullable(); // Thêm thumbnail để tối ưu hiển thị
    $table->timestamps();
    
    $table->unique(['chapter_id', 'page_number']); // Không cho phép trùng số trang trong cùng chapter
});
```

### 9. manga_categories (Quan hệ nhiều-nhiều giữa manga và thể loại)
```php
Schema::create('manga_categories', function (Blueprint $table) {
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->primary(['manga_id', 'category_id']);
});
```

### 10. manga_tags (Quan hệ nhiều-nhiều giữa manga và tag)
```php
Schema::create('manga_tags', function (Blueprint $table) {
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->primary(['manga_id', 'tag_id']);
});
```

### 11. bookmarks (Đánh dấu)
```php
Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    
    $table->unique(['user_id', 'manga_id']);
});
```

### 12. reading_history (Lịch sử đọc)
```php
Schema::create('reading_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
    $table->integer('last_read_page')->default(1);
    $table->timestamp('last_read_at')->useCurrent();
    
    $table->unique(['user_id', 'chapter_id']);
    $table->index(['user_id', 'last_read_at']); // Index cho việc tìm kiếm nhanh
});
```

### 13. comments (Bình luận)
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->foreignId('chapter_id')->nullable()->constrained()->cascadeOnDelete();
    $table->text('content');
    $table->foreignId('parent_id')->nullable()->references('id')->on('comments')->cascadeOnDelete(); // Cho phép trả lời comment
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['manga_id', 'created_at']); // Index cho việc tìm kiếm nhanh
    $table->index(['chapter_id', 'created_at']); // Index cho việc tìm kiếm nhanh
});
```

### 14. reviews (Đánh giá)
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->tinyInteger('rating')->unsigned(); // 1-5 sao
    $table->text('content')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->unique(['user_id', 'manga_id']);
});
```

### 15. notifications (Thông báo)
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

### 16. translation_teams (Nhóm dịch)
```php
Schema::create('translation_teams', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('logo')->nullable();
    $table->timestamps();
});
```

### 17. team_members (Thành viên nhóm dịch)
```php
Schema::create('team_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained('translation_teams')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('role', ['leader', 'translator', 'editor', 'member'])->default('member');
    $table->timestamps();
    
    $table->unique(['team_id', 'user_id']);
});
```

### 18. manga_teams (Quan hệ nhiều-nhiều giữa manga và nhóm dịch)
```php
Schema::create('manga_teams', function (Blueprint $table) {
    $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
    $table->foreignId('team_id')->constrained('translation_teams')->cascadeOnDelete();
    $table->primary(['manga_id', 'team_id']);
});
```

### 19. settings (Cài đặt hệ thống)
```php
Schema::create('settings', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->text('value')->nullable();
    $table->string('group')->nullable();
    $table->string('type')->default('string'); // string, boolean, integer, array, json
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 20. media (Quản lý media)
```php
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->morphs('mediable'); // Polymorphic relationship
    $table->string('file_name');
    $table->string('file_path');
    $table->string('mime_type');
    $table->integer('file_size');
    $table->string('collection_name')->nullable(); // Nhóm media (ví dụ: avatars, covers, pages)
    $table->json('custom_properties')->nullable();
    $table->timestamps();
});
```

## Cải tiến và Tối ưu hóa

1. **Sử dụng Soft Deletes**: Đã thêm `softDeletes()` cho các bảng quan trọng để tránh mất dữ liệu khi xóa.

2. **Indexes tối ưu**: Đã thêm các indexes cho các cột thường xuyên được sử dụng trong truy vấn để tăng hiệu suất.

3. **Polymorphic Relationships**: Sử dụng quan hệ đa hình cho media để có thể liên kết với nhiều loại đối tượng khác nhau.

4. **Cấu trúc bình luận phân cấp**: Cho phép trả lời bình luận thông qua trường `parent_id`.

5. **Quản lý nhóm dịch**: Thêm các bảng liên quan đến nhóm dịch và phân công công việc.

6. **Cài đặt hệ thống**: Bảng settings cho phép quản lý các cài đặt hệ thống một cách linh hoạt.

7. **Quản lý media tập trung**: Bảng media giúp quản lý tất cả các tệp tin media trong hệ thống.

8. **Thông báo**: Sử dụng hệ thống thông báo của Laravel để gửi thông báo đến người dùng.

## Lưu ý

1. Tất cả các bảng đều sử dụng `id` làm khóa chính với kiểu `bigIncrements` (Laravel mặc định).

2. Các mối quan hệ được thiết lập thông qua các ràng buộc khóa ngoại với các hành động cascade hoặc set null khi xóa.

3. Các cột timestamp (`created_at`, `updated_at`) được tự động thêm vào hầu hết các bảng.

4. Các slug được sử dụng để tạo URL thân thiện với SEO.

5. Các bảng trung gian cho quan hệ nhiều-nhiều sử dụng khóa chính kết hợp từ hai khóa ngoại.
