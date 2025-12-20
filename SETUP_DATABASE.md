# Hướng dẫn thiết lập Database

## Vấn đề hiện tại

**Lỗi: "could not find driver"** - PHP không có extension SQLite được cài đặt.

## Giải pháp: Sử dụng MySQL

Vì PHP của bạn đã có `pdo_mysql` extension và bạn đã có cấu hình MySQL trong `.env`, hãy chuyển sang dùng MySQL:

### Bước 1: Sửa file `.env`

Mở file `.env` và đảm bảo có các dòng sau:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthcare
DB_USERNAME=root
DB_PASSWORD=root
```

### Bước 2: Đảm bảo MySQL đang chạy và database đã được tạo

1. Kiểm tra MySQL service đang chạy
2. Đảm bảo database `healthcare` đã được tạo:
```sql
CREATE DATABASE IF NOT EXISTS healthcare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 3: Chạy migrations

```bash
php artisan migrate
```

## Nếu muốn dùng SQLite (cần cài extension)

1. Mở file `php.ini` (tìm bằng lệnh: `php --ini`)
2. Tìm và bỏ comment dòng:
```ini
extension=pdo_sqlite
extension=sqlite3
```
3. Restart web server hoặc PHP-FPM
4. Sửa `.env`:
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```
5. Tạo file `database/database.sqlite` (file rỗng)

## Các migration đã được tạo

1. `create_specialists_table` - Bảng chuyên gia y tế
2. `create_consultations_table` - Bảng tư vấn
3. `create_ai_sessions_table` - Bảng phiên AI
4. `create_medical_content_table` - Bảng nội dung y tế
5. `create_health_topics_table` - Bảng chủ đề sức khỏe
6. `create_system_logs_table` - Bảng nhật ký hệ thống
7. `create_feedback_table` - Bảng phản hồi
8. `add_healthcare_fields_to_users_table` - Cập nhật bảng users

