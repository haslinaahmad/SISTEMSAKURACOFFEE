<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::orderBy('due_date', 'asc')->paginate(10);
        $accounts = Account::where('is_active', true)->get();
        return view('debts.index', compact('debts', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:payable,receivable',
            'party_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'nullable|date'
        ]);

        Debt::create([
            'type' => $request->type,
            'party_name' => $request->party_name,
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Data hutang/piutang dicatat');
    }

    public function addPayment(Request $request, Debt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'account_id' => 'required|exists:accounts,id',
            'payment_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // 1. Catat Pembayaran
            DebtPayment::create([
                'debt_id' => $debt->id,
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes
            ]);

            // 2. Update Sisa Hutang
            $debt->remaining_amount -= $request->amount;
            
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
                $debt->remaining_amount = 0; // Prevent negative zero
            } else {
                $debt->status = 'partial';
            }
            $debt->save();

            // 3. Update Saldo Rekening
            $account = Account::lockForUpdate()->find($request->account_id);
            if ($debt->type == 'payable') {
                // Kita bayar hutang -> Saldo berkurang
                if ($account->balance < $request->amount) throw new \Exception('Saldo tidak cukup');
                $account->decrement('balance', $request->amount);
            } else {
                // Kita terima piutang -> Saldo bertambah
                $account->increment('balance', $request->amount);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil dicatat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}