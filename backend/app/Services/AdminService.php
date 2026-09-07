<?php 
namespace App\Services;

class AdminService
{
    public function generateImage($image,$folder) {
        $message = "";
        if (!$image->isValid()) {
            $message = "Invalid upload.";
            return $message;
        }
        if ($image->getSize() > 20000000) {
            $message = "Sorry, your file is too large.";
            return $message;
        }
        $imageFileType = strtolower($image->getClientOriginalExtension());
        if (empty($imageFileType)) {
            $imageFileType = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION));
        }
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
            $message = "Sorry, only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
            return $message;
        }
        $check = getimagesize($image->getRealPath());
        if ($check === false) {
            $message = "The file is not an image.";
            return $message;
        }
        $imageName = time() . '_' . basename($image->getClientOriginalName());
        if (app()->environment('local')) {
            $uploadDir = public_path('storage/'.$folder.'/');
        } else {
            $uploadDir = base_path('../public_html/storage/' . $folder . '/');
        }
        $targetFile = $uploadDir . $imageName;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (file_exists($targetFile)) {
            $message = "Sorry, this file already exists.";
            return $message;
        }
        try {
            $image->move($uploadDir, $imageName);
            return $message;
        } catch (\Exception $e) {
            $message = "An error occurred while uploading the file.";
            return $message;
        }
    }
}
