<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Yêu cầu đặt lại mật khẩu</h2>
        <p>Xin chào,</p>
        <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">Đặt lại mật khẩu</a>
        </div>
        <p>Liên kết đặt lại mật khẩu này sẽ hết hạn sau 60 phút.</p>
        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #666;">Nếu bạn gặp sự cố khi nhấp vào nút "Đặt lại mật khẩu", hãy sao chép và dán URL bên dưới vào trình duyệt web của bạn:</p>
        <p style="font-size: 12px; color: #666; word-break: break-all;">{{ $url }}</p>
    </div>
</body>
</html>
