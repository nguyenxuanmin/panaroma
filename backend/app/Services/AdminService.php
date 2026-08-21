<?php 
namespace App\Services;

class AdminService
{
    public function generateImage($image,$folder) {
        $message = "";
        $targetFile = $folder.'/'. basename($image['name']);
        if (app()->environment('local')) {
            $uploadDir = public_path('storage/'.$folder.'/');
        } else {
            $uploadDir = base_path('../public_html/storage/' . $folder . '/');
        }
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $check = getimagesize($image["tmp_name"]);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if ($check === false) {
            $message = "Tệp không phải là hình ảnh.";
            return $message;
        }
        if (file_exists($targetFile)) {
            $message = "Xin lỗi, tệp này đã tồn tại.";
            return $message;
        }
        if ($image["size"] > 5000000) {
            $message = "Xin lỗi, tệp của bạn quá lớn.";
            return $message;
        }
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif" && $imageFileType != "webp") {
            $message = "Xin lỗi, chỉ các tệp JPG, JPEG, PNG, GIF, WEBP được phép.";
            return $message;
        }
        if (move_uploaded_file($image["tmp_name"], $uploadDir . basename($image['name']))) {
            return $message;
        } else {
            $message = "Có lỗi xảy ra khi tải tệp lên.";
            return $message;
        }
    }
}
