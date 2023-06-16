<?php

namespace App\Imports;

use App\Imports\ToCollectionImport;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class ContactImport extends ToCollectionImport implements WithHeadingRow, WithBatchInserts, WithValidation, WithChunkReading, ShouldQueue, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{    
    use SkipsFailures, SkipsErrors, Importable;
    
    /**
     * user
     *
     * @var mixed
     */
    public $user;

    /**
     * tag
     *
     * @var mixed
     */
    protected $tag;

        
    /**
     * __construct
     *
     * @param  mixed $tag
     * @return void
     */
    public function __construct($tag, $user)
    {
        $this->tag = $tag;
        $this->user = $user;
    }
        
    /**
     * collection
     *
     * @param  mixed $collection
     * @return void
     */
    public function processImport(Collection $collection)
    {
        try {
            $collection->unique('number')->each(function ($row) {
                $contact = Contact::firstOrCreate([
                    'user_id' => $this->user,
                    'name' => $row['name'],
                    'number' => $row['number'],
                    'var1' => $row['var1'],
                    'var2' => $row['var2'],
                    'var3' => $row['var3'],
                    'var4' => $row['var4'],
                    'var5' => $row['var5']
                ]);

                $contact->tags()->attach($this->tag);
            });
        } catch (Throwable $err) {
            Log::error($err);
            throw $err;
        }
      
    }

    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * batchSize
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 500;
    }
    
    /**
     * chunkSize
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }
    
    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'min:10', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/', Rule::unique('contacts', 'number')->where('user_id', $this->user)],
            'var1' => ['nullable', 'alpha_num', 'max:255'],
            'var2' => ['nullable', 'alpha_num', 'max:255'],
            'var3' => ['nullable', 'alpha_num', 'max:255'],
            'var4' => ['nullable', 'alpha_num', 'max:255'],
            'var5' => ['nullable', 'alpha_num', 'max:255'],
        ];
    }
}
