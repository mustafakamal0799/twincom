<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Item</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
@php
    use Illuminate\Support\Str;

    // Filter gudang yang name-nya diawali dengan "TSC"
    $filteredWarehouses = collect($item['detailWarehouseData'])
        ->filter(fn($w) => Str::startsWith($w['name'], 'TSC'))
        ->values();

    $warehouses = $filteredWarehouses->pluck('warehouseName')->toArray();
    $warehouseBalances = $filteredWarehouses->pluck('balance', 'warehouseName')->toArray();

    $totalBalances = collect($warehouseBalances)->sum();


    $sellingPrices = collect($item['detailSellingPrice']);

    // Ambil harga berdasarkan kategori
    $resellerPrice = $sellingPrices
        ->first(fn($p) => strtolower($p['priceCategory']['name']) === 'reseller')['price'] ?? 0;

    $userPrice = $sellingPrices
        ->first(fn($p) => strtolower($p['priceCategory']['name']) === 'user')['price'] ?? 0;
@endphp
<nav class="navbar bg-dark">
    Halaman navbar
</nav>

<div class="container">
    <div class="title mb-4 mt-3">
        <h4 style="text-align: center">DAFTAR ITEM</h4>
    </div>

    <div class="table">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th rowspan="2">Nama Item</th>
                    <th rowspan="2">Total Stok</th>
                    <th rowspan="2">Harga Reseller</th>
                    <th rowspan="2">Harga User</th>
                    <th colspan="{{ count($warehouses) }}">Gudang TSC</th>
                </tr>
                <tr>
                    @foreach ($warehouses as $warehouse)
                        <th>{{ $warehouse }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $totalBalances }}</td>
                    <td>Rp {{ number_format($resellerPrice, 0, ',', '.') }}</td>
                    <td>
                        <span class="text-muted text-decoration-line-through">
                            Rp {{ number_format($userPrice, 0, ',', '.') }}
                        </span>
                    </td>
                    @foreach ($warehouses as $warehouse)
                        <td>{{ $warehouseBalances[$warehouse] ?? 0 }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>