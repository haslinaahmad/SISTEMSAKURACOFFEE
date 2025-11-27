<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index()
    {
        $finances = Finance::with(['account', 'user'])->latest()->paginate(15);
        $accounts = Account::all();
        
        // Ringkasan Bulan Ini
        $income = Finance::income()->whereMonth('transaction_date', date('m'))->sum('amount');
        $expense = Finance::expense()->whereMonth('transaction_date', date('m'))->sum('amount');

        return view('finance.index', compact('finances', 'accounts', 'income', 'expense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'proof' => 'nullable|file|max:2048' // Bukti foto
        ]);

        try {
            DB::beginTransaction();

            $proofPath = null;
            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('finance_proofs', 'public');
            }

            // Catat Keuangan
            Finance::create([
                'account_id' => $request->account_id,
                'user_id' => Auth::id(),
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'reference_proof' => $proofPath
            ]);

            // Update Saldo Rekening
            $account = Account::lockForUpdate()->find($request->account_id);
            if ($request->type == 'income') {
                $account->increment('balance', $request->amount);
            } else {
                if ($account->balance < $request->amount) {
                    throw new \Exception("Saldo rekening {$account->name} tidak mencukupi.");
                }
                $account->decrement('balance', $request->amount);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data keuangan berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}