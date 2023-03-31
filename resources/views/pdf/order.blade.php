<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Define any styles you want to use in your PDF here */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
        }

        /* Style the two tables to be side by side */
        .table-wrapper {
            display: inline-block;
            width: 49%;
            vertical-align: top;
        }
    </style>
</head>
<body>
<div class="table-wrapper">

            <p>Customer: {{$user->first_name}} {{$user->last_name}}</p>
            <p>Email: {{$user->email}}</p>
            <p>ID: {{$user->uuid}}</p>

</div>
<div>
    <table>
        <thead>
        <tr>
            <th>Product UUID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Column 3</th>
        </tr>
        </thead>
        <tbody>
        @foreach($productsAndQuantity as $single)

            <tr>
                <td>{{ $single['product']->uuid }} </td>
                <td>{{ $single['product']->title }} </td>
                <td>{{ $single['product']->price }} </td>
                <td>{{ $single['quantity'] }} </td>
                <td>{{ number_format($single['product']->price * $single['quantity'],2) }}</td>
                <td>Column 3</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
