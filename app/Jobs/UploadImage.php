<?php

namespace App\Jobs;

use App\Models\Upload;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Bus\Queueable;
use Image;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->upload->disk;
        Log::info("Disk: " . $disk);
        $imageName = $this->upload->image;
        $original_file = storage_path() . '/uploads/original/' . $imageName;

        try {
            // create the large Image and save to tmp disk
            Image::make($original_file)->fit(800, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($large = storage_path('/uploads/large/' . $imageName));

            // create the thumbnal Image and save to tmp disk
            Image::make($original_file)->fit(250, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumbnail = storage_path('/uploads/thumbnail/' . $imageName));


            // store images to permanent disk

            // Original
            if (Storage::disk($disk)->put('/uploads/uploads/original/' . $imageName, fopen($original_file, 'r+'))) {
                File::delete($original_file);
            }

            // Large
            if (Storage::disk($disk)->put('/uploads/uploads/large/' . $imageName, fopen($large, 'r+'))) {
                File::delete($large);
            }

            // Thumbnail
            if (Storage::disk($disk)->put('/uploads/uploads/thumbnail/' . $imageName, fopen($thumbnail, 'r+'))) {
                File::delete($thumbnail);
            }

            // update database record with success flag
            $this->upload->update([
                'upload_successful' => true
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}