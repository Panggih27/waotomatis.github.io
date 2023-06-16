<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class BuyPointController extends Controller
{
    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.products.list',[
            'products' => Product::where('is_active', true)->get()
        ]);
    }
    
    /**
     * detail product index
     *
     * @param  mixed $product
     * @return void
     */
    public function detail(Product $product)
    {
        if (request()->ajax()) {
            return response()->json([
                'product' => $product,
                'code' => rand(100, 500)
            ]);
        }

        return [];
    }
}
