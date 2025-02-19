<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class FileHelper
{
    public static function saveFile($file, $type): string|bool
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
                $destination = "{$uploadDir}{$type}/{$newAvatarName}";

                if (file_put_contents($destination, $imageData)) {
                    return $newAvatarName;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    public static function deleteFile($file, $type)
    {
        if ($file == 'profile_placeholder.png') {
            return;
        }
        $uploadDir = '/app/public/images/uploads/';
        $destination = "{$uploadDir}{$type}/{$file}";
        file_exists($destination) && unlink($destination);
    }
}
