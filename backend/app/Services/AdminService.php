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
            $message = "The file is not an image.";
            return $message;
        }
        if (file_exists($targetFile)) {
            $message = "Sorry, this file already exists.";
            return $message;
        }
        if ($image["size"] > 20000000) {
            $message = "Sorry, your file is too large.";
            return $message;
        }
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif" && $imageFileType != "webp") {
            $message = "Sorry, only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
            return $message;
        }
        if (move_uploaded_file($image["tmp_name"], $uploadDir . basename($image['name']))) {
            return $message;
        } else {
            $message = "An error occurred while uploading the file.";
            return $message;
        }
    }
}
