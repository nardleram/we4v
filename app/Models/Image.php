<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = ['imageable_type', 'imageable_id', 'format', 'path'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function imageable()
    {
        return $this->morphTo();
    }

    public static function get_user_bkgrnd_image(User $user)
    {
        return Image::where('imageable_id', $user->id)
            ->where('imageable_type', 'App\Models\User')
            ->where('format', 'bkgrnd')
            ->pluck('path');
    }

    public static function get_user_profile_image(User $user)
    {
        return Image::where('imageable_id', $user->id)
            ->where('imageable_type', 'App\Models\User')
            ->where('format', 'profile')
            ->pluck('path');
    }

    public static function resizeImage($file, $setWidth, $setHeight, $directory) : string
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = Str::random(15).'.'.$extension;
        
        list($width, $height) = getimagesize(asset($file));
        $ratio = $width / $height;
        if ($ratio < 1) {
            $myWidth = $setWidth*$ratio;
            $myHeight = $setHeight;
        } else {
            $myHeight = $setHeight/$ratio;
            $myWidth = $setWidth;
        }

        if ($extension === 'jpg') {
            $src = imagecreatefromjpeg('/Users/toby/sites/we4v/public/storage/'.$file);
        }
        if ($extension === 'png') {
            $src = imagecreatefrompng('/Users/toby/sites/we4v/public/storage/'.$file);
        }
        $imgDst = imagecreatetruecolor($myWidth, $myHeight);
        imagecopyresampled($imgDst, $src, 0, 0, 0, 0, $myWidth, $myHeight, $width, $height);
        if ($extension === 'jpg') {
            imagejpeg($imgDst, '/Users/toby/sites/we4v/public/storage/'.$directory.$fileName);
        }
        if ($extension === 'png') {
            imagepng($imgDst, '/Users/toby/sites/we4v/public/storage/'.$directory.$fileName);
        }

        $imagePath = $directory.$fileName;
    
        return $imagePath;
    }
}
