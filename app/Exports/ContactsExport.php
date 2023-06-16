<?php

namespace App\Exports;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ContactsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Contact::whereBelongsTo(auth()->user())->latest()->get(['name', 'number']);
    }
}
