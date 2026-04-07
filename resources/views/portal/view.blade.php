@extends('layouts.portal')

@section('title', 'Document Request Form - Preview')

@section('content')
    <div class="printable-paper-container"
        style="animation: fadeInUp 0.5s ease-out; background: #f1f5f9; min-height: 100vh; padding: {{ request()->has('compact') ? '0' : '20px 0 150px 0' }};">
        <!-- Action Bar (Hidden in Print) -->
        @if(!request()->has('compact'))
            <div class="action-bar-no-print"
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1rem 1.5rem; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">

                <div style="display: flex; gap: 1rem;">
                    <button onclick="window.print()" class="btn-portal-primary"
                        style="padding: 0.625rem 1.75rem; font-size: 0.875rem; border-radius: 12px;">
                        <i data-lucide="printer" style="width: 18px; height: 18px;"></i>
                        Export / Print Form
                    </button>
                </div>
            </div>
        @endif

        <!-- Official Document Paper -->
        <div class="printable-paper"
            style="background: white; padding: 10mm 15mm; border-radius: 2px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; width: 210mm; min-height: 297mm; margin: 0 auto 50px auto; position: relative; box-sizing: border-box; font-family: 'Bookman Old Style', 'Bookman', serif; color: black; line-height: 1.4;">

            <!-- Header Section -->
            <div style="text-align: center; margin-bottom: 5px;">
                <img src="{{ asset('images/logos/kagawaran-logo.png') }}"
                    style="width: 75px; height: auto; margin: -2px 0 0 0">
                <p
                    style="font-size: 11pt; margin: -5px 0 0 0; color: #000; font-family: 'Old English Text MT', serif; font-weight: bold; line-height: 1.1;">
                    Republic of the Philippines</p>
                <p
                    style="font-size: 16.5pt; margin: 5px 0 0 0; color: #000; font-family: 'Old English Text MT', serif; font-weight: bold; line-height: 1.1;">
                    Department of Education</p>
                <p style="font-size: 10pt; margin: 0; color: #000; font-family: 'Calibri', sans-serif; font-weight: bold;">
                    NATIONAL CAPITAL REGION</p>
                <p
                    style="font-size: 10pt; margin: -2px 0 0 0; color: #000; font-weight: bold; font-family: 'Calibri', sans-serif;">
                    SCHOOLS DIVISION OFFICE QUEZON CITY</p>
                <hr style="border: none; border-top: 1.5pt solid black; width: 93%; margin: 0 auto; margin-right: 32px;">
            </div>

            <!-- Title Section -->
            <div style="text-align: center; margin: 40px 0 15px 0;">
                <p style="font-size: 15pt; font-weight: bold; margin: 0; font-family: 'Bookman Old Style', serif;">
                    HUMAN
                    RESOURCE NON-TEACHING PERSONNEL UNIT</p>
                <p style="font-size: 15pt; font-weight: bold; margin: 0; font-family: 'Bookman Old Style', serif;">
                    DOCUMENT
                    REQUEST FORM</p>
            </div>

            <!-- Form Fields -->
            <div style="font-size: 11pt; margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div style="flex: 1; margin-left: 20px;">
                        <span>Number of Copies:</span>
                        <span
                            style="margin: 0; border-bottom: 1pt solid black; display: inline-block; min-width: 45px; padding-left: 5px; line-height: 0.8; padding-bottom: 1px;">{{ $request->num_copies }}</span>
                    </div>
                    <div style="flex: 1; text-align: right; margin-right: 50px;">
                        <span>Date:</span>
                        <span
                            style="margin: 0; border-bottom: 0.5pt solid black; display: inline-block; min-width: 100px; text-align: left; padding-left: 5px; line-height: 0.8; padding-bottom: 1px;">{{ $request->request_date->format('F d, Y') }}</span>
                    </div>
                </div>

                <div style="display: flex; margin-left: 20px; align-items: flex-end; margin-bottom: 4px;">
                    <span style="white-space: nowrap; line-height: 1;">Name:</span>
                    <span
                        style="border-bottom: 1pt solid black; flex-grow: 1; padding-left: 10px; padding-bottom: 1.5px; font-weight: bold; margin-right: 150px; line-height: 1; display: inline-block; margin-left: 5px;">{{ $request->employee_name }}</span>
                </div>

                <div style="margin: 0; display: flex; margin-left: 20px; align-items: flex-end; margin-bottom: 8px;">
                    <span style="white-space: nowrap; line-height: 1;">School:</span>
                    <span
                        style="border-bottom: 1pt solid black; flex-grow: 1; padding-left: 10px; padding-bottom: 1.5px; margin-right: 150px; line-height: 1; display: inline-block; margin-left: 5px;">{{ $request->agency }}</span>
                </div>

                <div style="margin: 0; display: flex; margin-left: 20px; align-items: flex-end; margin-bottom: 4px;">
                    <span style="white-space: nowrap;">Document/s Requested:</span>
                </div>
                <div style="margin: 0; display: flex; margin-left: 20px; align-items: flex-end; margin-bottom: 8px;">
                    <span
                        style="border-bottom: 1pt solid black; flex-grow: 1; padding-left: 10px; padding-bottom: 2px; margin-right: 150px; line-height: 1; display: inline-block;">{{ strtoupper($request->request_type) }}</span>
                </div>


                <div style="margin: 0; display: flex; margin-left: 20px; align-items: flex-end;">
                    <span style="white-space: nowrap;">Purpose:</span>
                    <span
                        style="border-bottom: 1pt solid black; flex-grow: 1; padding-left: 10px; padding-bottom: 2px; margin-right: 150px; line-height: 1; display: inline-block; margin-left: 5px;">{{ strtoupper($request->purpose) }}</span>
                </div>

            </div>

            <!-- Signatures Section -->
            <div
                style="margin-left: 20px; margin-top: 40px; display: flex; justify-content: space-between; font-size: 11pt;">
                <div style="width: 45%;">
                    <p style="font-weight: bold; margin-bottom: 25px; font-family: 'Bookman Old Style', serif;">
                        Prepared By:</p>
                    <div style="text-align: center; width: 180px;">
                        @php
                            $preparedBy = $request->prepared_by;
                            if (!$preparedBy && $request->status === 'approved') {
                                $preparedBy = session('auth_user_name') ?? 'HR Personnel';
                            }
                            if (!$preparedBy && $request->status !== 'approved') {
                                $preparedBy = '(Click to edit)';
                            }
                            $color = ($request->status === 'approved' || $preparedBy !== '(Click to edit)') ? 'black' : 'gray';
                        @endphp
                        <span contenteditable="{{ $request->status === 'approved' ? 'false' : 'true' }}" 
                            class="editable-name"
                            style="display: block; min-height: 20px; outline: none; margin-bottom: -2px; font-size: 11pt; color: {{ $color }};"
                            @if($request->status !== 'approved')
                            onfocus="if(this.innerText.trim() === '(Click to edit)') { this.innerText = ''; this.style.color = 'black'; }"
                            onblur="if(this.innerText.trim() === '') { this.innerText = '(Click to edit)'; this.style.color = 'gray'; }"
                            @endif>{{ $preparedBy }}</span>
                        <div style="border-bottom: 1pt solid black; width: 100%; margin-bottom: 4px;"></div>
                        <p style="font-size: 11pt; margin: 0;">HR-NTPU Personnel</p>
                    </div>
                </div>
                <div
                    style="width: 48%; display: flex; flex-direction: column; align-items: flex-start; margin-left: -40px;">
                    <p
                        style="font-weight: bold; margin-bottom: 25px; font-family: 'Bookman Old Style', serif; margin-left: 12px;">
                        Received By:</p>
                    <div style="text-align: center; width: 280px;">
                        <span style="display: block; min-height: 20px; margin-bottom: -2px; font-weight: bold; margin-left: 23px;">{{ $request->employee_name }}</span>
                        <div style="border-bottom: 1pt solid black; width: 240px; margin-bottom: 4px; margin-left: 23px;">
                        </div>
                        <p style="font-size: 11pt; margin-left: 2px;">Signature over printed name</p>
                        <p style="font-size: 11pt; margin-left: -32px; width: 340px;">Requesting Person/Authorized
                            Representative</p>
                    </div>
                </div>
            </div>


            <!-- Approved By -->
            <div style="margin-left: 20px; margin-top: 30px; font-size: 11pt;">
                <p
                    style="margin: -5px 0 0 0; font-weight: bold; margin-bottom: 30px; font-family: 'Bookman Old Style', serif;">
                    Approved By:</p>
                <div style="text-align: center; width: auto; display: inline-block; margin-left: 40px;">
                    <p
                        style="margin: 0; font-weight: bold; border-bottom: 1pt solid black; display: inline-block; min-width: 200px; font-size: 11pt; text-transform: uppercase; padding-bottom: 0; line-height: 1.1;">
                        MICHELLE A. MAL-IN
                    </p>
                    <p style="font-size: 11pt;">Administrative Officer IV</p>
                </div>
            </div>

            <!-- Requirements Section -->
            <div style="margin-left: 20px; margin-top: 50px; font-size: 10.5pt; margin-bottom: 10px;">
                <p style="font-weight: bold; font-family: 'Bookman Old Style', serif;">Requirements:</p>
                <div style="margin-left: 25px; font-style: italic; padding-bottom: 2px;">
                    <ol style="margin: 0; padding: 0; list-style-type: none;">
                        <li style="margin-bottom: -2px;">1.&nbsp;&nbsp;Accomplished Request Form</li>
                        <li style="margin-bottom: -2px;">2.&nbsp;&nbsp;Photocopy of Valid ID</li>
                    </ol>
                </div>
                <p style="font-weight: bold; margin: 15px 0 0 0; font-family: 'Bookman Old Style', serif;">
                    If authorized Representative:</p>
                <div style="margin-left: 75px; font-style: italic; padding-bottom: 2px;">
                    <ol style="margin-bottom: 5px; padding: 0; list-style-type: none;">
                        <li style="margin-bottom: -2px;">a.&nbsp;&nbsp;Accomplished Request Form</li>
                        <li style="margin-bottom: -2px;">b.&nbsp;&nbsp;Authorization Letter</li>
                        <li style="margin-bottom: -2px;">c.&nbsp;&nbsp;I.D. of requesting party and authorized
                            representative</li>
                    </ol>
                </div>
            </div>

            <!-- Footer Section -->
            <div style="position: absolute; bottom: 18mm; left: 15mm; right: 15mm;">
                <hr
                    style="border: none; border-top: 1.5pt solid black; width: 93%; margin: 0 auto; margin-top: -22px; margin-bottom: 8px; margin-right: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <!-- Footer Left Logos -->
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="{{ asset('images/logos/SDO-Logo.png') }}"
                            style="margin-left: -15px; width: 80px; height: auto;">
                        <img src="{{ asset('images/logos/csc-logo.png') }}" style="width: 120px; height: auto;">
                    </div>

                    <!-- Footer Address Info (Left Aligned next to CSC) -->
                    <div
                        style="text-align: left; font-size: 9pt; flex-grow: 1; padding-left: 15px; font-family: 'Bookman Old Style', serif; white-space: nowrap;">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 1px;">
                            <i data-lucide="home" style="width: 12px; height: 12px; color: #000;"></i>
                            <span style="display: inline-block; transform: translateY(-1px); margin-left: 10px;">Nueva Ecija
                                St., Bago Bantay, Quezon City</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 1px;">
                            <i data-lucide="phone" style="width: 12px; height: 12px; color: #000;"></i>
                            <span style="display: inline-block; transform: translateY(-1px); margin-left: 15px;">8538-6900
                                to 8538-6919</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 1px;">
                            <i data-lucide="mail" style="width: 12px; height: 12px; color: #000;"></i>
                            <span
                                style="display: inline-block; transform: translateY(-1px); margin-left: 15px;">sdo.quezoncity@deped.gov.ph</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="globe" style="width: 12px; height: 12px; color: #000;"></i>
                            <span
                                style="display: inline-block; transform: translateY(-1px); text-decoration: underline; color: #0000EE; margin-left: 15px;">www.depedqc.ph</span>
                        </div>
                    </div>

                    <!-- Footer Right Logos -->
                    <div style="display: flex; align-items: center; gap: 4px; margin-right: -25px;">
                        <img src="{{ asset('images/logos/deped-wordmark.png') }}"
                            style=" margin-top: 12px; width: 120px; height: auto;">
                        <img src="{{ asset('images/logos/bagong-pilipinas-logo.png') }}" style="width: 75px; height: auto;">
                    </div>
                </div>
            </div>

            <script>
                // Initialize Lucide icons after they might have been hidden/shown
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            </script>

            <style>
                /* Screen View */
                @media screen {
                    .printable-paper-container {
                        padding: 2rem;
                        background: #f1f5f9;
                        display: flex;
                        justify-content: center;
                    }
                }

                /* Print View */
                @media print {

                    /* I-hide ang layout elements gamit ang kanilang class/id */
                    .portal-navbar,
                    .sidebar-portal,
                    .footer-portal,
                    .action-bar-no-print,
                    nav,
                    header,
                    footer {
                        display: none !important;
                    }

                    /* I-reset ang body para hindi ito mag-shrink o mag-flex */
                    html, body {
                        background: white !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        width: 100% !important;
                        height: auto !important;
                        display: block !important;
                        -webkit-print-color-adjust: exact !important;
                        zoom: 1 !important; /* Disable global zoom for printing */
                        overflow: visible !important;
                    }

                    .printable-paper-container {
                        background: white !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        min-height: auto !important;
                        width: 100% !important;
                        display: block !important;
                        overflow: visible !important;
                    }

                    /* Remove any backgrounds and shadows */
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }

                    /* Reset main containers for printing */
                    main,
                    article,
                    .portal-container,
                    .container-fluid,
                    .content-wrapper {
                        display: block !important;
                        width: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        max-width: none !important;
                        border: none !important;
                        overflow: visible !important;
                    }

                    .printable-paper {
                        width: 100% !important; /* Use 100% to fill the A4 width defined in @page */
                        max-width: 210mm !important; /* But capped at A4 width */
                        min-height: 297mm !important;
                        padding: 10mm 15mm !important;
                        /* Standard A4 padding */
                        margin: 0 auto !important;
                        background: white !important;
                        position: relative !important;
                        box-sizing: border-box !important;
                        border: none !important;
                        box-shadow: none !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        page-break-after: avoid;
                        overflow: visible !important;
                    }

                    /* Siguraduhin na ang absolute footer ay nasa dulo ng paper */
                    .printable-paper div[style*="position: absolute; bottom:"] {
                        position: absolute !important;
                        bottom: 10mm !important;
                        /* Itaas nang kaunti para hindi maputol */
                        left: 15mm !important;
                        right: 15mm !important;
                        width: calc(100% - 30mm) !important;
                    }

                    @page {
                        size: A4;
                        margin: 0;
                    }
                }
            </style>
        </div>
    </div>
@endsection