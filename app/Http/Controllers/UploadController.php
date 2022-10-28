<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request){
        //validate request body
        $request->validate([
            'image' => ['mimes:png,jpeg,gif,bmp', 'max:2048','required'],
            

          
        ]);

        //get the image
        $image = $request->file('image');
        //$image_path = $image->getPathName();
 
        // get original file name and replace any spaces with _
        // example: ofiice card.png = timestamp()_office_card.pnp
        $filename = time()."_".preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
 
        // move image to temp location (tmp disk)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');
 
        
        //upload address proof
        $upload = Upload::create([
            //'user_id'=>auth()->id()
            'image'=> $filename,
            'disk'=> config('site.upload_disk'),
           
            
        ]);

        //dispacth job to handle image manipulation
        $this->dispatch(new UploadImage($upload));

        //return cuccess response

        return response()->json([
            'success'=> true,
            'message'=>'successfully uploaded a file',
            'data' =>$upload
        ]);
    }
   public function getImage($id){
        $image = upload::find($id);
        if($image) {
            return view('image', compact('image'));
        }

       
    }
   
}
