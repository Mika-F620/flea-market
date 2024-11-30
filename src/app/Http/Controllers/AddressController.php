<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function edit($id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        return view('address', compact('user', 'product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $user->update([
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
        ]);

        return redirect()->route('purchase.show', ['id' => $id])->with('success', '配送先を更新しました！');
    }

}

