<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TagController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.tag.tag', [
            'tags' => Tag::whereBelongsTo(auth()->user())->withCount('contacts')->latest()->get(),
        ]);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3', Rule::unique('tags', 'name')->where('user_id', Auth::user()->id)],
        ]);

        Tag::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name
        ]);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Success add tag!'
        ]);
    }

    
    /**
     * destroy
     *
     * @param  mixed $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $tag = Tag::find($request->id);
        $tag->contacts()->detach();
        $tag->delete();
        
        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Success delete tag!'
        ]);
    }

    
    /**
     * view list contacts
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function view($id, Request $request)
    {
        if ($request->ajax()) {

            $contacts = Tag::find($id)->contacts()->latest()->get();
            return view('pages.tag.view', [
                'contacts' => $contacts
            ])->render();
        }

        return [];
    }
}
