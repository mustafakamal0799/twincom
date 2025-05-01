<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerCategoryController extends Controller
{
    public function index() {
        $token = env('ACCURATE_ACCESS_TOKEN');
        $session = env('ACCURATE_SESSION');

        $url = ('https://public.accurate.id/accurate/api/customer-category/list.do');

        $respon = Http::withHeaders([
            'Authorization' => 'Bearer' . $token,
            'X-Session-ID' => $session
        ])->get($url);
        
        $detailCategories = [];

        if ($respon->successful()) {
            $categories = $respon->json()['d'];  

            foreach ($categories as $data) {
                $detailUrl = 'https://public.accurate.id/accurate/api/customer-category/detail.do?id=' . $data['id'];
                $detailRespone = Http::withHeaders([
                    'Authorization' => 'Bearer' . $token,
                    'X-Session-ID' => $session
                ])->get($detailUrl);

                if($detailRespone->successful()) {
                    $detailCategories[] = $detailRespone->json()['d'];
                }
            }         

            return view('customersCategory.index', compact('detailCategories'));
        }else {
            return back()->withErrors('Gagal Mengambil Data');
        }
    }
}