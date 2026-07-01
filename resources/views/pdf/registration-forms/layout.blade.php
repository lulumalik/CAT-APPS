<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berkas Pendaftaran</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .page {
            padding: 28px 34px 34px;
            page-break-after: always;
        }
        .page:last-child { page-break-after: auto; }
        h1 {
            font-size: 13px;
            text-align: center;
            text-transform: uppercase;
            margin: 0 0 10px;
            line-height: 1.35;
        }
        h2 {
            font-size: 12px;
            text-align: center;
            text-transform: uppercase;
            margin: 0 0 14px;
            line-height: 1.35;
        }
        p { margin: 0 0 8px; text-align: justify; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .upper { text-transform: uppercase; }
        .field-line {
            display: inline-block;
            min-width: 180px;
            border-bottom: 1px dotted #333;
            padding: 0 2px 1px;
            line-height: 1.2;
        }
        .field-block {
            display: block;
            min-height: 14px;
            border-bottom: 1px dotted #333;
            margin: 2px 0 6px;
            padding: 0 2px 1px;
        }
        .label-row { margin: 4px 0; }
        .label-row .label { display: inline-block; min-width: 150px; vertical-align: top; }
        .indent { margin-left: 18px; }
        .list-num { margin: 0 0 6px 18px; padding: 0; }
        .list-num li { margin-bottom: 4px; }
        .materai {
            margin-top: 18px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
        .sign-row {
            margin-top: 24px;
            width: 100%;
        }
        .sign-col {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            text-align: center;
        }
        table.form-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 8px;
        }
        table.form-table th,
        table.form-table td {
            border: 1px solid #333;
            padding: 4px 5px;
            vertical-align: top;
        }
        table.form-table th { background: #f3f3f3; }
        .small { font-size: 9px; }
        .photo-box {
            float: right;
            width: 90px;
            height: 120px;
            border: 1px solid #333;
            text-align: center;
            font-size: 9px;
            padding-top: 42px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
@yield('content')
</body>
</html>
