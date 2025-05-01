<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Category</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailCategories as $data)
            <tr>
                <td>{{$data['id']}}</td>
                <td>{{$data['name'] ?? 'NULL'}}</td>
            </tr>
            @endforeach            
        </tbody>
    </table>
</body>
</html>