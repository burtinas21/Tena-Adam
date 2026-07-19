<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Healthcare Report</title>

    <style>

        body{
            font-family: DejaVu Sans,sans-serif;
            font-size:12px;
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,
        td{
            border:1px solid #000;
            padding:8px;
            text-align:left;
        }

        th{
            background:#f2f2f2;
        }

    </style>

</head>

<body>

<h2>

    Healthcare Report

</h2>

@if(count($data))

<table>

    <thead>

        <tr>

            @foreach(array_keys($data[0]) as $heading)

                <th>

                    {{ ucwords(str_replace('_',' ',$heading)) }}

                </th>

            @endforeach

        </tr>

    </thead>

    <tbody>

        @foreach($data as $row)

            <tr>

                @foreach($row as $value)

                    <td>

                        {{ is_array($value) ? json_encode($value) : $value }}

                    </td>

                @endforeach

            </tr>

        @endforeach

    </tbody>

</table>

@else

<p>

    No report data available.

</p>

@endif

</body>

</html>