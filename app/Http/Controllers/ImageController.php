<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::orderBy('order')->get();
        return response()->json(['images' => $images]);
    }

    public function updateOrder(Request $request)
    {
        $data = $request->input('imageOrder');

        foreach ($data as $index => $imageId) {
            $image = Image::find($imageId);
            $image->order = $index + 1;
            $image->save();
        }

        return response()->json(['message' => 'Image order updated successfully']);
    }
}
