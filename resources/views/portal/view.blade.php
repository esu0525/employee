@extends('layouts.portal')

@section('title', 'Document Preview')

@section('content')
<div style="animation: fadeInUp 0.5s ease-out;">
    <!-- Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; background: white; padding: 1rem 1.5rem; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <a href="{{ route('employees.requests') }}" class="btn-portal-secondary">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
            Return to Management
        </a>
        <div style="display: flex; gap: 1rem;">
            <button onclick="window.print()" class="btn-portal-primary" style="padding: 0.625rem 1.75rem; font-size: 0.875rem; border-radius: 12px;">
                <i data-lucide="printer" style="width: 18px; height: 18px;"></i>
                Export / Print PDF
            </button>
        </div>
    </div>

    <!-- Official Document Paper -->
    <div style="background: white; padding: 60px; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; min-height: 1000px; position: relative;">
        <!-- Official Header -->
        <div style="text-align: center; margin-bottom: 40px; position: relative;">
            <div style="position: absolute; left: 0; top: 0;">
                <img src="{{ asset('images/Department_of_Education_(DepEd).svg.png') }}" style="width: 85px; height: auto;">
            </div>
            
            <p style="font-size: 11px; margin: 0; text-transform: uppercase; letter-spacing: 0.1em; color: #475569;">Republic of the Philippines</p>
            <p style="font-size: 18px; font-weight: 800; margin: 2px 0; color: #1e293b; font-family: 'Outfit', sans-serif;">Department of Education</p>
            <p style="font-size: 11px; margin: 0; text-transform: uppercase; color: #475569;">National Capital Region</p>
            <p style="font-size: 11px; margin: 0; text-transform: uppercase; font-weight: 700; color: #1e293b;">Schools Division Office Quezon City</p>
            
            <div style="margin: 30px auto 20px; width: 100%; height: 2px; background: #1e293b;"></div>
            
            <p style="font-size: 13px; font-weight: 700; margin: 10px 0; color: #1e293b; text-transform: uppercase;">Human Resource Non-Teaching Personnel Unit</p>
            <div style="margin-top: 15px;">
                <span style="font-size: 20px; font-weight: 900; border: 3px solid #1e293b; padding: 12px 30px; display: inline-block; letter-spacing: 0.05em; font-family: 'Outfit', sans-serif;">DOCUMENT REQUEST FORM</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 50px; font-family: 'Inter', sans-serif;">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="border-bottom: 1.5px solid #1e293b; padding-bottom: 4px;">
                    <p style="font-size: 10px; font-weight: 800; color: #64748b; margin: 0; text-transform: uppercase;">Requesting Employee</p>
                    <p style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 2px 0;">{{ $request->employee_name }}</p>
                </div>
                <div style="border-bottom: 1.5px solid #1e293b; padding-bottom: 4px;">
                    <p style="font-size: 10px; font-weight: 800; color: #64748b; margin: 0; text-transform: uppercase;">Agency</p>
                    <p style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 2px 0;">{{ $request->agency ?: 'SDO Quezon City' }}</p>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="border-bottom: 1.5px solid #1e293b; padding-bottom: 4px;">
                    <p style="font-size: 10px; font-weight: 800; color: #64748b; margin: 0; text-transform: uppercase;">Filing Date</p>
                    <p style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 2px 0;">{{ $request->request_date->format('F d, Y') }}</p>
                </div>
                <div style="border-bottom: 1.5px solid #1e293b; padding-bottom: 4px;">
                    <p style="font-size: 10px; font-weight: 800; color: #64748b; margin: 0; text-transform: uppercase;">Volume</p>
                    <p style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 2px 0;">{{ $request->num_copies }} {{ Str::plural('Copy', $request->num_copies) }}</p>
                </div>
            </div>
        </div>

        <!-- Checklists -->
        <div style="margin-top: 50px;">
            <p style="font-size: 13px; font-weight: 900; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.05em;">Document/s Requested:</p>
            <div style="display: grid; grid-template-columns: 1fr; gap: 12px; font-size: 13px;">
                @php
                    $docs = [
                        'Appointment, Oath of Office, Certificate of Assumption',
                        'TOR and Diploma, Certification of Education',
                        'PDS with Work Experience Sheet',
                        'Service Record, Certificate of Employement',
                        'Certificates',
                        'IPCRF'
                    ];
                    $requested_types = explode(', ', $request->request_type);
                    $other_val = '';
                    foreach($requested_types as $type) {
                        if (str_contains($type, '(')) {
                            $other_val = substr($type, strpos($type, '(') + 1, -1);
                        }
                    }
                @endphp
                @foreach($docs as $doc)
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 18px; height: 18px; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; flex-shrink: 0; margin-top: 1px;">
                        {{ in_array($doc, $requested_types) ? 'X' : '' }}
                    </div>
                    <span style="font-weight: 500; color: #1e293b;">{{ $doc }}</span>
                </div>
                @endforeach
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 18px; height: 18px; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; flex-shrink: 0; margin-top: 1px;">
                        {{ $other_val ? 'X' : '' }}
                    </div>
                    <div style="flex: 1; display: flex; align-items: baseline; gap: 10px;">
                        <span style="font-weight: 500; color: #1e293b;">OTHERS (Specify):</span>
                        <span style="border-bottom: 1px solid #1e293b; flex: 1; padding: 0 5px; min-height: 20px;">{{ $other_val }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 50px;">
            <p style="font-size: 13px; font-weight: 900; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.05em;">Purpose:</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px 30px; font-size: 13px;">
                @php
                    $purposes = ['GSIS LOAN', 'PAG-IBIG LOAN', 'BANK LOAN', 'EMPLOYMENT', 'RETIREMENT', 'TRAVEL ABROAD'];
                    $is_other_purpose = !in_array($request->purpose, $purposes);
                @endphp
                @foreach($purposes as $p)
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 18px; height: 18px; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; flex-shrink: 0;">
                        {{ $request->purpose == $p ? 'X' : '' }}
                    </div>
                    <span style="font-weight: 500; color: #1e293b;">{{ $p }}</span>
                </div>
                @endforeach
                <div style="display: flex; align-items: flex-start; gap: 12px; grid-column: 1 / -1; margin-top: 10px;">
                    <div style="width: 18px; height: 18px; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; flex-shrink: 0;">
                        {{ $is_other_purpose ? 'X' : '' }}
                    </div>
                    <div style="flex: 1; display: flex; align-items: baseline; gap: 10px;">
                        <span style="font-weight: 500; color: #1e293b;">OTHER/S:</span>
                        <span style="border-bottom: 1px solid #1e293b; flex: 1; padding: 0 5px; min-height: 20px;">{{ $is_other_purpose ? $request->purpose : '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Section -->
        <div style="margin-top: 80px; display: grid; grid-template-columns: 1fr 1fr; gap: 80px;">
            <div>
                <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin-bottom: 40px; text-transform: uppercase;">Prepared By:</p>
                <div style="text-align: center;">
                    <p style="font-size: 14px; font-weight: 800; border-bottom: 2px solid #1e293b; padding-bottom: 5px; margin: 0;">HR-NTPU STAFF</p>
                    <p style="font-size: 11px; color: #64748b; margin-top: 4px;">National Capital Region</p>
                </div>
            </div>
            <div>
                <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin-bottom: 40px; text-transform: uppercase;">Received By:</p>
                <div style="text-align: center;">
                    <div style="border-bottom: 2px solid #1e293b; height: 21px; margin-bottom: 5px;"></div>
                    <p style="font-size: 11px; font-weight: 700; color: #1e293b; margin: 0;">Signature over Printed Name</p>
                    <p style="font-size: 10px; color: #64748b;">Requesting Party / Representative</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 60px;">
            <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin-bottom: 40px; text-transform: uppercase;">Approved By:</p>
            <div style="width: 280px; text-align: center;">
                <p style="font-size: 15px; font-weight: 900; text-decoration: underline; margin: 0;">MICHELLE A. MAL-IN</p>
                <p style="font-size: 12px; font-weight: 700; color: #1e293b;">Administrative Officer IV</p>
            </div>
        </div>

        <!-- Official Footer/Notes -->
        <div style="margin-top: 60px; padding: 25px; border: 1.5px solid #e2e8f0; border-radius: 4px; font-size: 11px; color: #475569; line-height: 1.8; background: #fdfdfd;">
            <p style="font-weight: 900; color: #1e293b; text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                Document Filing Requirements:
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p>1. Fully accomplished Request Form (Digital/Physical)</p>
                    <p>2. Presentation of valid SDO / DepEd ID</p>
                </div>
                <div>
                    <p><b>For Authorized Representatives:</b></p>
                    <p>a. Authorization Letter (Signed by Requesting Party)</p>
                    <p>b. Photocopy of both Requesting Party & Representative ID</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .portal-navbar, .btn-portal-secondary, .btn-portal-primary, header, nav, .action-bar {
            display: none !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
        }
        .portal-container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        div[style*="background: white"] {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
        @page {
            size: auto;
            margin: 15mm;
        }
    }
</style>
@endsection
