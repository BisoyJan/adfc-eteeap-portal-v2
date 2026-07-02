@extends('pdf.layout', ['title' => 'Transcript of Records'])

@section('content')
    <div class="container">
        <div class="header">
            <h1>Asian Development Foundation College</h1>
            <p>ETEEAP Program — Transcript of Records</p>
        </div>

        <div class="info">
            <table>
                <tr><td><strong>Student Name:</strong></td><td>{{ $studentName }}</td></tr>
                <tr><td><strong>Registry Number:</strong></td><td>{{ $registryNumber }}</td></tr>
                <tr><td><strong>Date Issued:</strong></td><td>{{ $date }}</td></tr>
            </table>
        </div>

        <table class="subjects">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Subject Title</th>
                    <th class="text-right">Units</th>
                    <th class="text-center">Grade</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                <tr>
                    <td>{{ $subject['code'] }}</td>
                    <td>{{ $subject['title'] }}</td>
                    <td class="text-right">{{ $subject['units'] }}</td>
                    <td class="text-center">{{ $subject['grade'] }}</td>
                    <td class="text-center">{{ $subject['status'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Total Units:</strong> {{ $totalUnits }}</p>

        <div class="footer">
            <p>This document is system-generated and valid without a physical signature.</p>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { font-size: 14pt; margin-bottom: 0; }
    .header p { font-size: 9pt; color: #666; margin: 3px 0; }
    .info { margin-bottom: 20px; }
    .info table { width: 100%; }
    .info td { padding: 2px 5px; }
    table.subjects { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.subjects th { background: #f0f0f0; border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-weight: bold; }
    table.subjects td { border: 1px solid #ccc; padding: 5px 8px; }
    table.subjects .text-right { text-align: right; }
    table.subjects .text-center { text-align: center; }
    .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
</style>
@endsection
