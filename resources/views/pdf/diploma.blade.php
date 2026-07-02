@extends('pdf.layout', ['title' => 'Diploma'])

@section('content')
    <div class="container">
        <div class="border">
            <div class="college">Asian Development Foundation College</div>
            <div class="subtitle">Expanded Tertiary Education Equivalency and Accreditation Program</div>

            <div class="title">Diploma</div>

            <div class="statement">This is to certify that</div>

            <div class="student-name">{{ $studentName }}</div>

            <div class="statement">has successfully completed all the requirements for graduation under the ETEEAP Program and is hereby awarded the corresponding degree and privileges appertaining thereto.</div>

            <div class="rgn">Registry Number: {{ $registryNumber }}</div>
            <div class="date">Issued on {{ $date }}</div>
        </div>

        <div class="footer">
            <p>This document is system-generated and valid without a physical signature.</p>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .container { width: 100%; max-width: 700px; margin: 60px auto; padding: 40px; text-align: center; }
    .border { border: 3px double #333; padding: 40px; }
    .college { font-size: 18pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
    .subtitle { font-size: 11pt; color: #666; margin-bottom: 30px; }
    .title { font-size: 14pt; margin-bottom: 40px; }
    .student-name { font-size: 24pt; font-weight: bold; margin: 30px 0; text-transform: uppercase; letter-spacing: 2px; }
    .statement { font-size: 12pt; margin: 20px 0; line-height: 1.8; }
    .rgn { font-size: 11pt; margin-top: 30px; color: #555; }
    .date { font-size: 11pt; margin-top: 10px; color: #555; }
    .footer { margin-top: 50px; font-size: 10pt; color: #888; }
</style>
@endsection
