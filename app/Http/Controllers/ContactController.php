<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Imports\ContactImport;
use App\Models\Contact;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.contact', [
            'contacts' => Contact::whereBelongsTo(auth()->user())->latest()->with('tags')->get(),
            'tags' => Tag::whereBelongsTo(auth()->user())->latest()->get(),
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
            'number' => ['required', 'min:10', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/', Rule::unique('contacts', 'number')->where('user_id', Auth::user()->id)],
            'name' => ['required', 'string', 'max:50'],
            'tag' => ['required', 'array'],
            'tag.*' => ['required', Rule::exists('tags', 'id')->where('user_id', Auth::user()->id)],
        ]);

        $contact = Contact::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'number' => $request->number
        ]);

        $contact->tags()->attach($request->tag);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Contact added!'
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $contact
     * @return void
     */
    public function show(Contact $contact)
    {
        if (request()->ajax()) {
            return $contact->loadMissing('tags');
        }

        return [];
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $contact
     * @return void
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'number' => ['required', 'min:10', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/', Rule::unique('contacts', 'number')->where('user_id', Auth::user()->id)->ignore($contact)],
            'name' => ['required', 'string', 'max:50'],
            'tag' => ['required', 'array'],
            'tag.*' => ['required', Rule::exists('tags', 'id')->where('user_id', Auth::user()->id)],
        ]);

        $contact->update([
            'name' => $request->name,
            'number' => $request->number,
        ]);

        $contact->tags()->sync($request->tag);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Contact Updated!'
        ]);
    }

    
    /**
     * import
     *
     * @param  mixed $request
     * @return void
     */
    public function import(Request $request)
    {
        $request->validate([
            'tag' => ['required', Rule::exists('tags', 'id')->where('user_id', Auth::user()->id)],
            'fileContacts' => ['required', 'max:5120']
        ]);
        
        try {
            (new ContactImport($request->tag, auth()->id()))->import($request->file('fileContacts')->store('temp'));
            return back()->with('alert', [
                'type' => 'success',
                'msg' => 'Import Contacts still in progress...'
            ]);
        } catch (Exception $err) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => $err->getMessage()
            ]);
        }
    }
        
    /**
     * export
     *
     * @return void
     */
    public function export()
    {
        return  Excel::download(new ContactsExport, 'contacts.xlsx');
    }
    
    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'contacts' => ['required', 'array'],
            'contacts.*' => ['required', Rule::exists('contacts', 'id')->where('user_id', Auth::user()->id)],
        ]);
        
        $contacts = Contact::find($request->contacts);
        $contacts->each(function ($contact) {
            $contact->tags()->detach();
            $contact->delete();
        });

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Kontak Berhasil Dihapus'
        ]);
    }
    
    /**
     * download latest error from import
     *
     * @param  mixed $type
     * @return void
     */
    public function download($type)
    {
        return Storage::download('excels/failures/' . auth()->id() . '.xlsx');
    }
}
