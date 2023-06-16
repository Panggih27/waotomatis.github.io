<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Bank;
use App\Models\History;
use App\Models\Point;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $user = Auth::user()->loadMissing('roles');
        if (request()->ajax()) {
            if (request()->type == 'pending') {
                $transactions = Transaction::when(in_array('customer', $user->roles->pluck('name')->toArray()), function($q) use($user) {
                    return $q->whereBelongsTo($user);
                })->where([
                    'status' => 'pending'
                ])->whereNotNull('confirmation')->with(['user', 'product_real', 'bank'])->latest()->get();
            } elseif (request()->type == 'paid') {
                $transactions = Transaction::when(in_array('customer', $user->roles->pluck('name')->toArray()), function($q) use($user) {
                    return $q->whereBelongsTo($user);
                })->where([
                    'status' => 'paid'
                ])->whereNotNull('confirmation')->with(['user', 'product_real', 'bank'])->latest()->get();
            } elseif (request()->type == 'cancelled') {
                $transactions = Transaction::when(in_array('customer', $user->roles->pluck('name')->toArray()), function($q) use($user) {
                    return $q->whereBelongsTo($user);
                })->where([
                    'status' => 'cancelled'
                ])->whereNotNull('cancelled_reason')->with(['user', 'product_real', 'bank'])->latest()->get();
            } else {
                $transactions = Transaction::when(in_array('customer', $user->roles->pluck('name')->toArray()), function($q) use($user) {
                    return $q->whereBelongsTo($user);
                })->where([
                    'status' => 'pending'
                ])->whereNull('confirmation')->with(['user', 'product_real', 'bank'])->latest()->get();
            }

            return response()->json([
                'type' => request()->type,
                'res' => count($transactions) > 0 ? view('pages.transaction.list', ['transactions' => $transactions])->render() : [],
            ]);
        }

        return view('pages.transaction.index', [
            'products' => Product::where('is_active', true)->get(),
            'banks' => Bank::where(['status' => true, 'is_owner' => true])->get(),
            'pending' => Transaction::when(in_array('customer', $user->roles->pluck('name')->toArray()), function ($q) use ($user) {
                return $q->whereBelongsTo($user);
            })->where([
                'status' => 'pending'
            ])->whereNotNull('confirmation')->exists(),
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $transaction
     * @return void
     */
    public function show(Transaction $transaction)
    {
        if (request()->ajax()) {
            return response()->json([
                'transaction' => $transaction->loadMissing(['user', 'product_real', 'bank'])
            ]);
        }

        return $transaction->loadMissing([
            'user', 'product_real', 'bank'
        ]);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(TransactionRequest $request)
    {
        DB::transaction(function() use ($request) {
            $product = Product::find($request->product);

            TransactionService::save($request, $product);
        }, 3);

        return redirect(route('transaction.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Success Bought This Product!'
        ]);
    }
    
    /**
     * update transaction status
     *
     * @param  mixed $transaction
     * @return void
     */
    public function status(Request $request, Transaction $transaction)
    {
        DB::transaction(function() use ($request, $transaction) {
            $request->validate([
                'status' => ['required', 'in:pending,paid,cancelled'],
                'reason' => ['required_if:status,cancelled', 'string', 'min:10', 'max:255']
            ]);

            if ($transaction->status == 'pending') {
                if ($request->status == 'paid') {
                    if (!is_null($transaction->confirmation)) {
                        $transaction->update([
                            'status' => $request->status,
                            'cancelled_reason' => null,
                        ]);

                        $transaction = $transaction->fresh()->loadMissing(['user']);

                        $transaction->history()->create([
                            'user_id' => $transaction->user_id,
                            'point' => $transaction->product->point,
                            'type' => '+'
                        ]);
                        
                        $latest = Transaction::whereBelongsTo($transaction->user)->where([
                            'status' => 'paid'
                        ])->latest()->first();
                        $point = Point::whereBelongsTo($transaction->user)->first();

                        if ($latest) {
                            if ($latest->product->duration > $transaction->product->duration) {
                                $point->update([
                                    'point' => $point->point + $transaction->product->point,
                                    'expired_at' => Carbon::parse($point->expired_at)->addMonths($transaction->product->duration)
                                ]);
                            } elseif ($latest->product->duration == $transaction->product->duration) {
                                $point->update([
                                    'point' => $point->point + $transaction->product->point,
                                    'expired_at' => Carbon::now()->addMonths($transaction->product->duration)
                                ]);
                            } else {
                                $point->update([
                                    'point' => $transaction->product->point,
                                    'expired_at' => Carbon::now()->addMonths($transaction->product->duration)
                                ]);
                            }
                        } else {
                            $point->update([
                                'point' => $transaction->product->point,
                                'expired_at' => Carbon::now()->addMonths($transaction->product->duration)
                            ]);
                        }
                        
                    } else {
                        return redirect(route('transaction.index'))->with('alert', [
                            'type' => 'danger',
                            'msg' => 'Transaction Hasn\'t Upload The Payment Confirmation!'
                        ]);
                    }
                } elseif ($request->status == 'pending') {
                    return redirect(route('transaction.index'))->with('alert', [
                        'type' => 'success',
                        'msg' => 'Transaction Status Updated!'
                    ]);
                } else {
                    $transaction->update([
                        'status' => $request->status,
                        'cancelled_reason' => $request->reason . ' - ' . explode('-', Auth::id())[0]
                    ]);
                }
            } elseif ($transaction->status == 'cancelled') {
                return redirect(route('transaction.index'))->with('alert', [
                    'type' => 'danger',
                    'msg' => 'Transaction Already Cancelled!'
                ]);
            } else {
                return redirect(route('transaction.index'))->with('alert', [
                    'type' => 'danger',
                    'msg' => 'Transaction Already Paid!'
                ]);
            }
        }, 3);

        return redirect(route('transaction.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Transaction Status Updated!'
        ]);
    }
    
    /**
     * confirmation
     *
     * @param  mixed $request
     * @param  mixed $transaction
     * @return void
     */
    public function confirmation(Request $request, Transaction $transaction)
    {
        DB::transaction(function() use($request, $transaction) {
            $request->validate([
                'confirmation' => 'required|image|mimes:jpeg,png,jpg|max:1024'
            ]);

            $confirmation = $request->file('confirmation')->store('confirmations', 'public');

            if (! is_null($transaction->confirmation)) {
                Storage::delete('public/' . $transaction->confirmation);
            }

            $transaction->update([
                'confirmation' => $confirmation
            ]);
        });

        return redirect(route('transaction.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Thank You For Trusting Us, We Will Process Your Payment In The Nex Couple Hours!'
        ]);
    }
    
    /**
     * cancel
     *
     * @param  mixed $request
     * @param  mixed $transaction
     * @return void
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => ['required', 'in:cancelled'],
            'reason' => ['required', 'string', 'min:10', 'max:255']
        ]);

        $transaction->update([
            'status' => $request->status,
            'cancelled_reason' => $request->reason 
        ]);

        return redirect(route('transaction.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Transaction Cancelled!'
        ]);
    }
}
