<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laptop;
use App\Models\LaptopImage;
use Illuminate\Http\Request;

class LaptopController extends Controller
{
    public function index()
    {
        $laptops = Laptop::with(['images' => function ($query) {
            $query->orderBy('image_order');
        }])->orderBy('id', 'desc')->paginate(10);

        return response()->json(['laptops' => $laptops]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'CPU' => 'required|string',
            'GPU' => 'required|string',
            'ram' => 'required|string',
            'storage' => 'required|string',
            'screen' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'string',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Create the laptop
        $laptop = Laptop::create($request->only([
            'name', 
            'brand', 
            'CPU', 
            'GPU', 
            'ram', 
            'storage', 
            'screen',
            'price',
            'description'
        ]));

        // Attach images to the product
        if ($request->has('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $imageFile->store('public/laptop_images');
                // Remove 'public/' from the beginning of the path
                $imagePath = str_replace('public/', '', $imagePath);

                $image = new LaptopImage(['image_path' => $imagePath]);
                $laptop->images()->save($image);
            }
        }

        $laptop->load('images');

        return response()->json(['message' => 'Laptop and images created successfully', 'laptop' => $laptop]);
    }

    public function show($id)
    {
        $laptop = Laptop::with(['images' => function ($query) {
            $query->orderBy('image_order');
        }])->find($id);

        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }
        
        return response()->json(['laptop' => $laptop]);
    }

    public function update(Request $request, $id)
    {
        $laptop = Laptop::with('images')->find($id);
    
        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }

        $request->validate([
            'name' => 'string',
            'brand' => 'string',
            'CPU' => 'string',
            'GPU' => 'string',
            'ram' => 'string',
            'storage' => 'string',
            'screen' => 'string',
            'price' => 'numeric',
            'description' => 'string',
            'new_image_path' => 'array',
            'new_image_path.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $laptop->update($request->only([
            'name', 
            'brand', 
            'CPU', 
            'GPU', 
            'ram', 
            'storage', 
            'screen',
            'price',
            'description'
        ]));

        if ($request->has('new_image_path')) {
            foreach ($request->file('new_image_path') as $imageFile) {
                $imagePath = $imageFile->store('public/laptop_images');
                $imagePath = str_replace('public/', '', $imagePath);
                $image = new LaptopImage(['image_path' => $imagePath]);
                $laptop->images()->save($image);
            }
        }

        $laptop->setRelation(
            'images',
            $laptop->images->sortBy('image_order')
        );

        $laptop->load('images');
        return response()->json(['message' => 'Laptop and image updated successfully', 'Laptop' => $laptop]);
    }

    public function destroy($id)
    {
        $laptop = Laptop::find($id);

        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }

        // Delete associated images
        $laptop->images()->delete();
    
        // Delete the laptop
        $laptop->delete();
    
        return response()->json(['message' => 'Laptop and associated images deleted successfully']);
    }

    public function deleteLaptopImage($laptopId, $imageId)
    {
        $laptop = Laptop::find($laptopId);

        if (!$laptop) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $image = $laptop->images()->find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Image not found for the laptop$laptop'], 404);
        }

        // Delete the image
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function images()
    {
        return $this->hasMany(LaptopImage::class);
    }

    public function updateImageOrder(Request $request)
    {
        $laptopImagesOrder = $request->input('imageOrder');
    
        foreach ($laptopImagesOrder as $index => $imageId) {
            $image = LaptopImage::find($imageId);
    
            if ($image) {
                $image->image_order = $index + 1;
                $image->save();
            }
        }
    
        return response()->json(['message' => 'Image updated successfully']);
    }
    
}
