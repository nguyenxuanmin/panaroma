<?php 
namespace App\Services;

class AdminService
{
    public function generateImage($image,$folder) {        $message = "";
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
        $uploadDir = public_path('storage/'.$folder.'/');
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

    public function generateVideo($file, $folder) {
        $message = "";
        if (!$file->isValid()) {
            return "Invalid upload.";
        }
        if ($file->getSize() > 524288000) {
            return "Sorry, your video is too large (max 500MB).";
        }
        $extension = strtolower($file->getClientOriginalExtension());
        if (empty($extension)) {
            $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        }
        if (!in_array($extension, ["mp4", "webm", "ogg", "m4v", "mov"])) {
            return "Sorry, only MP4, WEBM, OGG, M4V, and MOV files are allowed.";
        }
        $imageName = time() . '_' . basename($file->getClientOriginalName());
        $uploadDir = public_path('storage/'.$folder.'/');
        $targetFile = $uploadDir . $imageName;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (file_exists($targetFile)) {
            return "Sorry, this file already exists.";
        }
        try {
            $file->move($uploadDir, $imageName);
            return "";
        } catch (\Exception $e) {
            return "An error occurred while uploading the video.";
        }
    }
}
