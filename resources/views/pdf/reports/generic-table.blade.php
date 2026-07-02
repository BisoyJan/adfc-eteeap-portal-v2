@extends('pdf.layout', ['title' => $title])

@section('content')
    <h1>{{ $title }}</h1>
    <p class="subtitle">Generated on {{ now()->format('F j, Y') }}</p>

    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">ADFC ETEEAP Portal — Report</p>
@endsection

@section('styles')
<style>
    h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
    .subtitle { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { background-color: #f3f4f6; padding: 8px 6px; text-align: left; font-size: 10px; border: 1px solid #d1d5db; }
    td { padding: 6px; border: 1px solid #d1d5db; font-size: 10px; }
    tr:nth-child(even) { background-color: #f9fafb; }
    .footer { text-align: center; font-size: 9px; color: #999; margin-top: 20px; }
</style>
@endsection
