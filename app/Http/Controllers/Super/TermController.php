<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TermController extends Controller
{
    public function index()
    {
        $data = Content::where('flag', 'term')->first();
        return view('pages.term.index', [
            'data' => Content::where('flag', 'term')->first()
        ]);
    }

    public function store()
    {
        
    }

    public function update($id=null, Request $request)
    {
        try {
            $data = Content::where('flag', 'term')->first();
            $data->content = $request->content;
            $data->update();
            
            $json = [
                'success' => true,
                'message' => 'Data succesfully updated!'
            ];
    
            return response()->json($json, 201);

        } catch (\Exception $e) {
            $json = [
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]
            ];
    
            return response()->json($json, 500);
        }
        

    }
}
