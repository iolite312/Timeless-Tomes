<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class FileHelper
{
    public static function saveFile($file): bool
    {
        $base64String = $file;
        $data = explode(',', $base64String);
        if (count($data) === 2 && preg_match('/^data:image\/(\w+);base64/', $data[0], $matches) === 1) {
            $imageType = $matches[1];
            $allowedTypes = ['png', 'jpeg', 'jpg', 'gif'];

            if (in_array($imageType, $allowedTypes)) {
                $imageData = base64_decode($data[1]);
                $newAvatarName = Uuid::uuid4()->toString() . '.' . $imageType;
                $uploadDir = '/app/public/images/uploads/';
                $destination = "{$uploadDir}{$newAvatarName}";

                if (file_put_contents($destination, $imageData)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    public static function deleteFile($file)
    {
        if ($file == 'placeholder.jpg') {
            return;
        }
        $uploadDir = '/app/public/images/uploads/';
        $destination = "{$uploadDir}{$file}";
        file_exists($destination) && unlink($destination);
    }
}
