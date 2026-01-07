-- SQL Script để thêm 100 bản ghi medical content vào database
-- Chạy script này trực tiếp trong MySQL

-- Xóa dữ liệu cũ (tùy chọn)
-- DELETE FROM medical_content;

-- Thêm Knowledge Base Articles (60 bài)
INSERT INTO medical_content (content_type, title, content, category, tags, status, views_count, helpful_count, created_at, updated_at) VALUES
('knowledge_base', 'Hiểu về bệnh tiểu đường và cách phòng ngừa', 'Bệnh tiểu đường là một bệnh mãn tính ảnh hưởng đến cách cơ thể chuyển hóa glucose (đường). Có hai loại chính: tiểu đường type 1 và type 2. Type 1 thường xuất hiện ở trẻ em và thanh thiếu niên, trong khi type 2 phổ biến hơn ở người lớn. Triệu chứng bao gồm khát nước nhiều, đi tiểu thường xuyên, mệt mỏi, và giảm cân không rõ nguyên nhân. Để phòng ngừa, hãy duy trì cân nặng hợp lý, tập thể dục thường xuyên, ăn uống lành mạnh với nhiều rau xanh và trái cây, hạn chế đường và tinh bột tinh chế. Nếu bạn có nguy cơ cao, hãy kiểm tra đường huyết định kỳ.', 'Bệnh mãn tính', '["tiểu đường", "đường huyết", "sức khỏe", "phòng ngừa"]', 'published', 245, 32, NOW(), NOW()),

('knowledge_base', 'Cách duy trì huyết áp ổn định', 'Huyết áp cao là một vấn đề sức khỏe phổ biến có thể dẫn đến các biến chứng nghiêm trọng như đột quỵ và bệnh tim. Để duy trì huyết áp ổn định, bạn nên: giảm lượng muối trong chế độ ăn, tăng cường hoạt động thể chất (ít nhất 30 phút mỗi ngày), duy trì cân nặng hợp lý, hạn chế rượu bia, bỏ thuốc lá, quản lý căng thẳng thông qua thiền hoặc yoga, và ngủ đủ giấc (7-8 giờ mỗi đêm). Nên đo huyết áp thường xuyên và tham khảo ý kiến bác sĩ nếu huyết áp cao hơn 140/90 mmHg.', 'Tim mạch', '["huyết áp", "tim mạch", "sức khỏe tim", "phòng ngừa"]', 'published', 189, 28, NOW(), NOW()),

('knowledge_base', 'Chế độ dinh dưỡng cho người cao tuổi', 'Dinh dưỡng đúng cách rất quan trọng đối với người cao tuổi để duy trì sức khỏe và năng lượng. Người cao tuổi nên ăn nhiều thực phẩm giàu canxi như sữa, cá nhỏ, rau xanh để bảo vệ xương. Bổ sung đủ protein từ thịt nạc, cá, đậu để duy trì cơ bắp. Ăn nhiều chất xơ từ rau củ quả để hỗ trợ tiêu hóa. Uống đủ nước (ít nhất 1.5-2 lít mỗi ngày). Hạn chế muối, đường và chất béo bão hòa. Nên chia nhỏ bữa ăn thành 5-6 bữa nhỏ thay vì 3 bữa lớn để dễ tiêu hóa.', 'Dinh dưỡng', '["dinh dưỡng", "người cao tuổi", "ăn uống lành mạnh", "sức khỏe"]', 'published', 156, 21, NOW(), NOW()),

('knowledge_base', 'Tầm quan trọng của giấc ngủ đối với sức khỏe', 'Giấc ngủ đóng vai trò quan trọng trong việc duy trì sức khỏe tổng thể. Người trưởng thành cần 7-9 giờ ngủ mỗi đêm. Thiếu ngủ có thể dẫn đến suy giảm trí nhớ, giảm khả năng tập trung, tăng nguy cơ béo phì, tiểu đường và bệnh tim. Để có giấc ngủ ngon, hãy duy trì lịch ngủ đều đặn, tạo môi trường ngủ thoải mái (tối, yên tĩnh, mát mẻ), tránh caffeine và rượu trước khi ngủ, tắt các thiết bị điện tử ít nhất 1 giờ trước khi ngủ, và tập thể dục thường xuyên nhưng không quá gần giờ ngủ.', 'Sức khỏe tổng quát', '["giấc ngủ", "sức khỏe", "lối sống", "phòng ngừa"]', 'published', 278, 35, NOW(), NOW()),

('knowledge_base', 'Cách phòng ngừa cảm cúm và cảm lạnh', 'Cảm cúm và cảm lạnh là các bệnh nhiễm trùng đường hô hấp phổ biến. Để phòng ngừa, hãy rửa tay thường xuyên bằng xà phòng và nước trong ít nhất 20 giây, hoặc sử dụng nước rửa tay có cồn. Tránh chạm vào mắt, mũi và miệng bằng tay chưa rửa. Tiêm phòng cúm hàng năm, đặc biệt quan trọng đối với người cao tuổi và người có hệ miễn dịch yếu. Tránh tiếp xúc gần với người bị bệnh. Duy trì lối sống lành mạnh với chế độ ăn uống cân bằng, tập thể dục và ngủ đủ giấc để tăng cường hệ miễn dịch.', 'Bệnh truyền nhiễm', '["cảm cúm", "cảm lạnh", "phòng ngừa", "miễn dịch"]', 'published', 312, 42, NOW(), NOW());

-- Lưu ý: Do giới hạn độ dài, tôi sẽ tạo file PHP để chạy seeder thay vì SQL script dài
-- File SQL này chỉ là ví dụ cho 5 bản ghi đầu tiên

