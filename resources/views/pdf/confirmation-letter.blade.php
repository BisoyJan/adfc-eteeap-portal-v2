@extends('pdf.layout', ['title' => 'Confirmation Letter'])

@section('content')
    <div class="container">
        <div class="letterhead">
            <h1>Asian Development Foundation College</h1>
            <p>ETEEAP Program — Office of the Coordinator</p>
        </div>

        <div class="date">
            <p>{{ $date }}</p>
        </div>

        <div class="subject">
            <p><strong>Subject: Confirmation of Graduation Clearance</strong></p>
        </div>

        <p>To Whom It May Concern:</p>

        <div class="body-text">
            <p>This is to confirm that <strong>{{ $studentName }}</strong> (Email: {{ $studentEmail }}) has completed all requirements for graduation clearance under the Expanded Tertiary Education Equivalency and Accreditation Program (ETEEAP) at Asian Development Foundation College.</p>
            <p>The student has successfully fulfilled all academic and documentary requirements as verified by the Office of the Coordinator. All clearance documents have been reviewed and confirmed complete.</p>
            <p>This clearance certifies that the above-named student is eligible for graduation and issuance of the corresponding diploma and transcript of records, subject to the approval of the Vice President for Academic Affairs.</p>
        </div>

        <p>Thank you.</p>

        <div class="signature">
            <p>Sincerely,</p>
            <br>
            <p><strong>ETEEAP Coordinator</strong></p>
            <p>Asian Development Foundation College</p>
        </div>

        <div class="footer">
            <p>This document is system-generated and valid without a physical signature.</p>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 40px; }
    .letterhead { text-align: center; margin-bottom: 30px; }
    .letterhead h1 { font-size: 16pt; margin-bottom: 0; }
    .letterhead p { font-size: 10pt; color: #666; }
    .date { text-align: right; margin-bottom: 20px; }
    .subject { margin-bottom: 20px; }
    .subject strong { text-decoration: underline; }
    .body-text { margin-bottom: 20px; text-align: justify; }
    .signature { margin-top: 50px; }
    .signature p { margin: 5px 0; }
    .footer { margin-top: 40px; text-align: center; font-size: 10pt; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
</style>
@endsection
