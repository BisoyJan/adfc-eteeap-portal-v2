<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'ADFC ETEEAP Document')</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a5276; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #1a5276; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #666; margin: 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
        .content { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .page-break { page-break-before: always; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="header">
        <h1>ADFC ETEEAP</h1>
        <p>{{ config('app.name') }} — ETEEAP Document System</p>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }} | ADFC ETEEAP Portal</p>
    </div>
</body>
</html>
