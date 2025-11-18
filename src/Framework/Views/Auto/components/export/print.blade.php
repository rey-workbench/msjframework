<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print - {{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #00b7bd;
            color: #000;
            padding: 10px;
            text-align: center;
            border: 1px solid #000;
            font-weight: bold;
        }

        td {
            padding: 8px;
            border: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .inactive-row {
            background-color: #ffe9ed;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            display: inline-block;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .print-info {
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background-color: #00b7bd; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    {!! $html !!}
</body>

</html>
