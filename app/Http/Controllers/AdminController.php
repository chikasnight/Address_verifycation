<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use image;

class AdminController extends Controller
{
    
    public function view(){
        $uploads = Upload::all();
        

        return view('welcome', compact('uploads'));
    }
    public function verify($id)
    {
        $upload = Upload::find($id);
        if($upload) {
            $upload->update([
                'address_verified' => true
            ]);
        }
       
        return redirect()->back()->with('success',' address verified!');
    }
    /*public function getGrocery(Request $request, $groceryId){
        $grocery = Grocery::find($groceryId);
        if(!$grocery) {
            return response() ->json([
                'success' => false,
                'message' => 'grocery not found'
            ]);
        }

        return response() ->json([
            'success'=> true,
            'message'  => 'grocery found',
            'data'   => [
                'grocery'=> new GroceryResource($grocery),
                
            ]
        ]);
    }*/

   
}
