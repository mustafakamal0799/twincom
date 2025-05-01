<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ItemController extends Controller
{   
    public function index() {
        $token = env('ACCURATE_ACCESS_TOKEN');
        $session = env('ACCURATE_SESSION');

        $itemId = 53656; // contoh id

        $detailUrl = 'https://public.accurate.id/accurate/api/item/detail.do?id=' . $itemId;

        $respon = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Session-ID' => $session
        ])->get($detailUrl);

        if ($respon->successful()) {
            $item = $respon->json()['d'];

            // dd($item);
            return view('items.index', compact('item'));
        } else {
            return back()->withErrors('Gagal Mengambil Data Item');
        }
    }
}




   
