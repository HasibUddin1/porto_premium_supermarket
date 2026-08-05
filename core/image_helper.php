<?php
// core/image_helper.php
// Converts an uploaded image (jpg/png/gif/webp) to WebP and saves it into
// $destinationDir. Returns the saved filename on success, or null on
// failure/no-file (with $error describing what went wrong, if anything).

function save_uploaded_image_as_webp(array $file, string $destinationDir, ?string &$error = null): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // nothing uploaded — not necessarily an error, caller decides
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Image upload failed. Please try again.';
        return null;
    }

    if (!function_exists('imagewebp')) {
        $error = 'Server is missing the GD/WebP extension needed to process images.';
        return null;
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        $error = 'The uploaded file is not a valid image.';
        return null;
    }

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = imagecreatefrompng($file['tmp_name']);
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($file['tmp_name']);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($file['tmp_name']);
            break;
        default:
            $error = 'Unsupported image type. Please upload a JPG, PNG, GIF, or WebP file.';
            return null;
    }

    if (!$image) {
        $error = 'Could not process the uploaded image.';
        return null;
    }

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }

    $filename = uniqid('img_', true) . '.webp';
    $fullPath = rtrim($destinationDir, '/') . '/' . $filename;

    $saved = imagewebp($image, $fullPath, 82); // 82 = quality, good balance of size/clarity
    imagedestroy($image);

    if (!$saved) {
        $error = 'Could not save the converted image.';
        return null;
    }

    return $filename;
}

// Deletes a product/category image file, given just its stored filename/path.
function delete_stored_image(?string $relativePath, string $baseDir): void
{
    if (!$relativePath) {
        return;
    }
    $fullPath = $baseDir . '/' . basename($relativePath);
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}
