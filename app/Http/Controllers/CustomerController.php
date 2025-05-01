<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index(Request $request) {
        $token = env('ACCURATE_ACCESS_TOKEN');
        $session = env('ACCURATE_SESSION');

        // $url = "https://public.accurate.id/accurate/api/customer/list.do";

        // $respon = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $token,
        //     'X-Session-ID' => $session
        // ])->get($url);
        
        // $detailCustomer = [];

        // if ($respon->successful()) {
        //     $categories = $respon->json()['d'];

        //     foreach ($categories as $data) {
        //         $detailUrl = 'https://public.accurate.id/accurate/api/customer/detail.do?id=' . $data['id'];
        //         $detailRespone = Http::withHeaders([
        //             'Authorization' => 'Bearer' . $token,
        //             'X-Session-ID' => $session
        //         ])->get($detailUrl);

        //         if($detailRespone->successful()) {
        //             $detail = $detailRespone->json()['d'];

        //             // if (!empty($detail['reseller']) && $detail['reseller'] === true) {
        //             //     $detailCustomer[] = $detail;
        //             // }

        //             $debugList[] = [
        //                 'name' => $detail['name'] ?? 'N/A',
        //                 'reseller' => $detail['reseller'] ?? 'tidak ada field',
        //             ];
        //         }
        //     }         
        //     dd($debugList);
        //     return view('customer.index', compact('detailCustomer', 'pagination'));
        // }else {
        //     return back()->withErrors('Gagal Mengambil Data');
        // }





        $allResellerCustomers = [];
        $page = 1;

        do {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Session-ID' => $session
            ])->get("https://public.accurate.id/accurate/api/customer/list.do?page=$page");

            if (!$response->successful()) {
                break; // stop jika error
            }

            $result = $response->json();

            $customers = $result['d'] ?? [];
            foreach ($customers as $customer) {
                $detailUrl = 'https://public.accurate.id/accurate/api/customer/detail.do?id=' . $customer['id'];

                $detailResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Session-ID' => $session
                ])->get($detailUrl);

                if ($detailResponse->successful()) {
                    $detail = $detailResponse->json()['d'];

                    // Filter hanya reseller
                    if (!empty($detail['categoryId']) && $detail['categoryId'] === 2701) {  
                        $allResellerCustomers[] = $detail;
                    }
                }

                // Untuk testing, batasi hanya 2 halaman dulu
                if ($page >= 4) {
                    break 2; // keluar dari do-while
                }
            }

            $page++;
        } while ($page <= $result['sp']['pageCount']); // loop hingga halaman terakhir

        dd($allResellerCustomers);

    }



    public function syncCustomer() {
        
        $token = env('ACCURATE_ACCESS_TOKEN');
        $session = env('ACCURATE_SESSION');

        $url = "https://public.accurate.id/accurate/api/customer/list.do";

        // Ambil halaman pertama untuk mendapatkan informasi total halaman
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Session-ID' => $session
        ])->get($url);

        // Mengecek apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            
            // Ambil informasi paginasi
            $pageCount = $data['sp']['pageCount'];  // Total halaman yang tersedia

            $allCustomers = []; // Tempat untuk menampung semua data customer

            // Loop untuk mengambil semua halaman
            for ($page = 1; $page <= $pageCount; $page++) {
                // Request ke halaman berikutnya
                $pageUrl = $url . '?page=' . $page;

                $pageResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Session-ID' => $session
                ])->get($pageUrl);

                if ($pageResponse->successful()) {
                    $customers = $pageResponse->json()['d'];
                    $allCustomers = array_merge($allCustomers, $customers);  // Gabungkan data dari setiap halaman
                    Log::info('Successfully fetched page ' . $page);
                } else {
                    Log::error('Failed to fetch page ' . $page, ['error' => $pageResponse->body()]);
                }
            }

            // Log jumlah total customer yang berhasil diambil
            Log::info('Total customers fetched: ' . count($allCustomers));

            // Lakukan sesuatu dengan $allCustomers, misalnya disinkronkan ke database
            // return view('customer.index', compact('allCustomers'));
        } else {
            // Log jika gagal mengambil daftar customer
            Log::error('Failed to fetch customer data', ['error' => $response->body()]);
        }
    }
}
