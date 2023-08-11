<?php

namespace App\Helpers;


use App\Models\Common\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use File as FileSystem;


class FileHelpers
{

    public static function uploadFiles($files, $folder, $id = null, $class = null, $custom = null, $type = null)
    {
        $rsl = [];
        foreach ($files as $file) {
            $rsl[] = self::uploadFile($file['thumbUrl'], $folder, $id, $class, $custom, $type);
        }
        return $rsl;
    }

    // public static function uploadFiles($files, $folder, $id = null, $class = null, $custom = null)
    // {

    //     foreach ($files as $file) {
    //         $filename[] = $file['name'];
    //         $urlBasr64[] = $file['thumbUrl'];
    //     }

    // }




    public static function uploadFile($file, $folder, $id = null, $class = null, $custom = null, $type = null)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;

        if ($file !== '') {

            // $filename = $file->getClientOriginalName();

            $filename = time() . '.' . explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
            $name = Str::slug($filename);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $destinationFileName = $name . '-' . time() . '-' . Str::random(4);
            $destinationFile = $destinationFileName . '.' . $extension;
            if (!is_dir($destinationPath)) {
                FileSystem::makeDirectory($destinationPath, 0777, true);
            }
            // Resize ảnh nếu là ảnh bài viết, sản phẩm, dịch vụ
            // Type = 1 =>> sản phẩm
            // Type = 2 =>> Danh mục sản phẩm
            // Type = 3 =>> Bài viết
            // Type = 4 =>> Danh mục bài viết
            // Type = 5 =>> Logo thương hiệu sản phẩm
            // Type = 6 =>> banners
            // Type = 7 =>> favicon
            // Type = 8 =>> logo đối tác
            // Type = 9 =>> ảnh ban quản trị

            if ($type == 1) {
                //                Image::make($file)->resize(600, 600, function ($constraint) {
                //                    $constraint->aspectRatio();
                //                }
                //                )->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
                Image::make($file)->resize(
                    600,
                    600,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    }
                )->resizeCanvas(600, 600)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 2) {
                Image::make($file)->resize(120, 120)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 3) {
                Image::make($file)->resize(
                    748,
                    364,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    }
                )
                    ->resizeCanvas(748, 364)
                    ->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 5) {
                Image::make($file)->resize(115, 75)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 6) {
                $file->move($destinationPath, $destinationFile);
                //                Image::make($file)->resize(1920, 700)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 7) {
                Image::make($file)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 8) {
                Image::make($file)->resize(115, 75)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 9) {
                $file->move($destinationPath, $destinationFile);
            } else if ($type == 10) {
                Image::make($file)->resize(300, 200)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else {
                $file->move($destinationPath, $destinationFile);
            }

            $file_data = [
                'name' => $filename,
                'path' => DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]),
                'custom_field' => $custom,
            ];

            if ($id && $class) {
                self::saveFile($file_data, $id, $class);
            }

            return $file_data;
        }
        return [];
    }

    public static function copyFile($fileObject, $folder, $id = null, $class = null, $custom = null)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;

        // make destination file name
        $info = pathinfo($fileObject->path);
        $name = $info['filename'];
        $extension = $info['extension'];
        $destinationFileName = $name . '-' . time() . '-' . Str::random(4);
        $destinationFile = $destinationFileName . '.' . $extension;

        $originalPath = public_path($fileObject->path);
        $targetPath = implode(DIRECTORY_SEPARATOR, [$destinationPath, $destinationFile]);

        if (!is_dir($destinationPath)) {
            File::makeDirectory($destinationPath);
        }

        File::copy($originalPath, $targetPath);

        $file_data = [
            'name' => $fileObject->name,
            'path' => DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]),
            'custom_field' => $custom,
        ];

        if ($id && $class) {
            self::saveFile($file_data, $id, $class);
        }

        return $file_data;
    }

    public static function saveFile($file_data, $id, $class)
    {

        $file_data['model_id'] = $id;
        $file_data['model_type'] = $class;
        $file = new File($file_data);
        $file->save();

        return $file;
    }

    public static function updateFile($file_data, $id, $class)
    {
        $file_data['model_type'] = $class;
        $file_data['model_id'] = $id;
        $file = File::where('model_id', $id)->update($file_data);

        return $file;
    }

    /**
     * Chỉ cập nhật lại trong db, ko xóa khỏi db, ko xóa file
     * @param $fileIds
     * @param $id
     * @param $class
     */
    public static function deleteFiles($fileIds, $id, $class, $custom = null)
    {
        if (!is_array($fileIds)) {
            $fileIds = [$fileIds];
        }
        File::query()
            ->where('model_id', $id)
            ->where('model_type', $class)
            ->where('custom_field', $custom)
            ->whereIn('id', $fileIds)
            ->update([
                'model_id' => null,
                'model_type' => null
            ]);
    }

    /**
     * Xóa trong db và xóa file
     * @param $fileIds
     * @param $id
     * @param $class
     */
    public static function forceDeleteFiles($fileIds, $id, $class, $custom = null)
    {
        if (!is_array($fileIds)) {
            $fileIds = [$fileIds];
        }
        $file = File::query()
            ->where('model_id', $id)
            ->where('model_type', $class)
            ->where('custom_field', $custom)
            ->whereIn('id', $fileIds);

        if (file_exists(public_path($file->first()->path))) {
            unlink(public_path($file->first()->path));
        }

        $file->delete();
    }
}
