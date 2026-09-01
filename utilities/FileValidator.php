<?php

class FileValidator
{
    public static function validate(array $file, array $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'], int $maxSizeBytes = 2097152): array
    {
        $errors = [];

        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $errors;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Profile photo upload failed.';
            return $errors;
        }

        if (!in_array($file['type'] ?? '', $allowedTypes, true)) {
            $errors[] = 'Profile photo must be a JPG, PNG, or WEBP image.';
        }

        if (($file['size'] ?? 0) > $maxSizeBytes) {
            $errors[] = 'Profile photo must be smaller than 2MB.';
        }

        return $errors;
    }

    public static function moveUploadedFile(array $file, string $uploadDir): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = 'employee_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return null;
        }

        return '/uploads/profile-photos/' . $safeName;
    }
}
