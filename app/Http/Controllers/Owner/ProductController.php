<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.products.index',[
            'products' => Product::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // get all validated data
        $data = $request->validated();

        // get image
        $data['image'] = $request->file('image')->store('products', 'public');

        // create slug from title
        $data['slug'] = Str::slug($data['title']);

        // add who created the product to the data
        $data['created_by'] = auth()->id();

        // checking discount type
        if (!array_key_exists('discount_type', $data)) {
            $data['discount'] = 0;
        }
        
        // create the product
        Product::create($data);

        // redirect to the products page
        return redirect(route('product.index'))->with('alert',[
            'type' => 'success',
            'msg' => 'Produk Berhasil Ditambahkan!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['createdBy', 'updatedBy'])->findOrFail($id);

        if (request()->ajax()) {
            return json_encode($product);
        }
        
        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validated();

        $transaction = $product->transactions()->where('status', 'pending')->exists();
        if ($transaction) {
            // redirect to the products page
            return redirect(route('product.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Produck tidak dapat diperbarui, terdapat Konsumen yang sedang melakukan transaksi dengan produk ini!'
            ]);
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/'.$product->image);
            // get image
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            $data['image'] = $product->image;
        }

        // checking discount type
        if (is_null($data['discount_type'])) {
            $data['discount'] = 0;
        }

        // create slug from title
        $data['slug'] = Str::slug($data['title']);

        // add who created the product to the data
        $data['updated_by'] = auth()->id();

        $product->update($data);

        // redirect to the products page
        return redirect(route('product.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Produk Berhasil Diperbarui!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $transaction = $product->transactions()->where('status', 'pending')->exists();
        if ($transaction) {
            // redirect to the products page
            return redirect(route('product.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Produck tidak dapat dihapus, terdapat Konsumen yang sedang melakukan transaksi dengan produk ini!'
            ]);
        }

        Storage::delete('public/'.$product->image);

        $product->delete();

        return redirect(route('product.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Produk Berhasil Dihapus!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function activating($id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'is_active' => ! $product->is_active
        ]);

        return true;
    }
}
