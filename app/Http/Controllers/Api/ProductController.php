<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('images')->get();

        return response()->json(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'brand' => 'required|string',
            'processor' => 'required|string',
            'ram' => 'required|string',
            'storage' => 'required|string',
            'images' => 'array',
            'images.*.image_path' => 'required|string|url',
        ]);

        // Create the product
        $product = Product::create($request->only(['name', 'price', 'brand', 'processor', 'ram', 'storage']));

        // Attach images to the product
        if ($request->has('images')) {
            foreach ($request->input('images') as $imageData) {
                $image = new ProductImage(['image_path' => $imageData['image_path']]);
                $product->images()->save($image);
            }
        }

        $product->load('images'); // Load the created images

        return response()->json(['message' => 'Product and images created successfully', 'product' => $product]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::with('images')->find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'brand' => 'string',
            'processor' => 'string',
            'ram' => 'string',
            'storage' => 'string',
            'new_image_path' => 'string|url',
        ]);
    
        $product->update($request->only(['name', 'price', 'brand', 'processor', 'ram', 'storage']));
    

        if ($request->has('new_image_path')) {
            $newImage = new ProductImage(['image_path' => $request->input('new_image_path')]);
            $product->images()->save($newImage);
        }
    
        $product->load('images');
    
        return response()->json(['message' => 'Product and image updated successfully', 'product' => $product]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Delete associated images
        $product->images()->delete();
    
        // Delete the product
        $product->delete();
    
        return response()->json(['message' => 'Product and associated images deleted successfully']);
    }

    public function deleteImage($productId, $imageId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $image = $product->images()->find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Image not found for the product'], 404);
        }

        // Delete the image
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
    
}
