<?php
namespace App\Http\Controllers;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller {
    public function index() {
        $accounts = Account::all();
        return view('accounts.index', compact('accounts'));
    }
    public function store(Request $request) {
        Account::create($request->all());
        return back();
    }
}