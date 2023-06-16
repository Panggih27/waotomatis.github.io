<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.banks.index', [
            'banks' => Bank::whereBelongsTo(Auth::user(), 'user')->latest()->get()
        ]);
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('pages.banks.create');
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(BankRequest $request)
    {
        Bank::create([
            'user_id' => Auth::user()->id,
            'bank' => [
                'name' => $request->bank,
                'image' => static::images($request->bank)
            ],
            'account_name' => strtoupper($request->account_name),
            'account_number' => $request->account_number,
            'is_owner' => true
        ]);

        return redirect(route('bank.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Bank Berhasil Ditambahkan!'
        ]);
    }

    public function show(Bank $bank)
    {
        return view('pages.banks.create', [
            'bank' => $bank
        ]);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $bank
     * @return void
     */
    public function update(BankRequest $request, Bank $bank)
    {
        $this->authorize('save', $bank);

        if ($request->has('status')) {
            $bank->update([
                'status' => ! $bank->status
            ]);
        } else {
            $bank->update([
                'bank' => [
                    'name' => $request->bank,
                    'image' => static::images($request->bank)
                ],
                'account_name' => strtoupper($request->account_name),
                'account_number' => $request->account_number
            ]);
        }

        return redirect(route('bank.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Bank Berhasil Diperbarui!'
        ]);
    }

    /**
     * destroy
     *
     * @param  mixed $bank
     * @return void
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect(route('bank.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Bank Berhasil Dihapus!'
        ]);
    }
    
    /**
     * status
     *
     * @param  mixed $request
     * @param  mixed $bank
     * @return void
     */
    public function status(Request $request, Bank $bank)
    {
        $this->authorize('save', $bank);
        
        $bank->update([
            'status' => ! $bank->status
        ]);

        return true;
    }
    
    /**
     * get the bank logo
     *
     * @param  mixed $bank
     * @return void
     */
    public static function images($bank)
    {
        switch ($bank) {
            case 'BNI':
                $image = 'assets/images/banks/bni.png';
                break;
            case 'BSI':
                $image = 'assets/images/banks/bsi.png';
                break;
            case 'MANDIRI':
                $image = 'assets/images/banks/mandiri.png';
                break;
            case 'PERMATA':
                $image = 'assets/images/banks/permata.png';
                break;
            case 'BRI':
                $image = 'assets/images/banks/bri.png';
                break;
            case 'CIMB':
                $image = 'assets/images/banks/cimb.png';
                break;
            case 'BTN':
                $image = 'assets/images/banks/btn.png';
                break;
            case 'DANAMON':
                $image = 'assets/images/banks/danamon.png';
                break;
            default:
                $image = 'assets/images/banks/bca.png';
                break;
        }

        return $image;
    }
}
