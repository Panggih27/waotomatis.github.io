<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionService {
    
    public static function calculation(Request $request, Product $product)
    {
        $total = $product->price;
        if ($product->discount > 0) {
            if ($product->discount_type == 'percentage') {
                $total = $product->price - ($product->price * ($product->discount / 100));
            } else {
                $total = $product->price - $product->discount;
            }
        }

        $total += $request->payment_code;

        return $total;
    }

    public static function save(Request $request, Product $product)
    {
        if (!Storage::exists('public/archive/' . $product->image)) {
            Storage::copy('public/' . $product->image, 'public/archive/' . $product->image);
        }
        $image = 'archive/' . $product->image;

        return Auth::user()->transactions()->create([
            'product_id' => $product->id,
            'bank_id' => $request->bank,
            'payment_code' => $request->payment_code,
            'grand_total' => static::calculation($request, $product),
            'product' => [
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $image,
                'price' => $product->price,
                'point' => $product->point,
                'duration' => $product->duration,
                'discount_type' => $product->discount_type,
                'discount' => intval($product->discount)
            ]
        ]);
    }
}