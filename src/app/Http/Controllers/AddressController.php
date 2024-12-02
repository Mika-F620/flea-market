<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function edit($id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        return view('address', compact('user', 'product'));
    }

    public function update(AddressRequest $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);

        // セッションに一時的な配送先情報を保存
        session([
            'temp_address' => [
                'postal_code' => $request->input('postal_code'),
                'address' => $request->input('address'),
                'building_name' => $request->input('building_name'),
            ],
        ]);

        // $user->update([
        //     'postal_code' => $request->input('postal_code'),
        //     'address' => $request->input('address'),
        //     'building_name' => $request->input('building_name'),
        // ]);

        // 購入画面にリダイレクト
        return redirect()->route('purchase.show', ['id' => $id])->with('success', '配送先を更新しました！');
    }

}

