<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\CostRequest;
use App\Models\Cost;

class CostController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.cost',[
            'costs' => Cost::all()
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $cost
     * @return void
     */
    public function show(Cost $cost)
    {
        if (request()->ajax()) {
           return response()->json($cost);
        }

        return $cost;
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(CostRequest $request)
    {
        Cost::create([
            'name' => strtolower($request->name),
            'slug' => $request->name,
            'point' => $request->point,
            'description' => $request->description,
            'created_by' => auth()->id()
        ]);

        return redirect(route('cost.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Tarif Poin Berhasil Ditambahkan!'
        ]);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $cost
     * @return void
     */
    public function update(CostRequest $request, Cost $cost)
    {
        $data = $request->validated();

        $cost->update([
            // 'name' => strtolower($data['name']),
            // 'slug' => $data['name'],
            'point' => $data['point'],
            'description' => $data['description'],
            'updated_by' => auth()->id()
        ]);

        return redirect(route('cost.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Tarif Poin Berhasil Diperbarui!'
        ]);
    }

    public function destroy(Cost $cost)
    {
        $cost->delete();

        return redirect(route('cost.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Tarif Poin Berhasil Dihapus!'
        ]);
    }
}
