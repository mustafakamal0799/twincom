<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data customer dari Accurate API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = env('ACCURATE_ACCESS_TOKEN');
        $session = env('ACCURATE_SESSION');

        $page = 1;
        $totalPages = 2; // Sementara, bisa update otomatis nanti

        while ($page <= $totalPages) {
            $url = "https://public.accurate.id/accurate/api/customer/list.do?page=$page";

            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Session-ID' => $session
            ])->get($url);

            if ($res->successful()) {
                $json = $res->json();

                $dataList = $json['d']['customer'] ?? [];
                $totalPages = $json['d']['sp']['pageCount'] ?? 1;

                foreach ($dataList as $data) {
                    // Ambil detail jika perlu
                    $detailUrl = "https://public.accurate.id/accurate/api/customer/detail.do?id=" . $data['id'];

                    $detailRes = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'X-Session-ID' => $session
                    ])->get($detailUrl);

                    if ($detailRes->successful()) {
                        $detail = $detailRes->json()['d'];

                        // Contoh simpan ke DB
                        // DB::table('customers')->updateOrInsert([...]);

                        $this->info("Synced customer: " . $detail['name']);
                    }
                }
            } else {
                $this->error("Gagal ambil data di halaman $page");
                break;
            }

            $page++;
        }

        $this->info('Sinkronisasi selesai.');
    }
    
}
