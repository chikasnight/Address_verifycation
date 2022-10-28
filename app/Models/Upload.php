<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'upload_successful',
        'disk',
        'address_verified'
    ];
    public function getImagesAttribute()
    {
        return [
            "original" => $this->getImagePath("original"),
        ];
    }

    public function getImagePath($size)
    {
        return Storage::disk($this->disk)->url("uploads/original/{$size}/" . $this->image);
    }
}
