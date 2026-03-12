@extends('layouts.portal')

@section('title', 'Online Request Portal')

@section('content')
<div style="animation: fadeInUp 0.6s ease-out;">
    <!-- Hero Section -->
    <div style="text-align: center; margin-bottom: 4rem;">
        <div style="display: inline-flex; position: relative; margin-bottom: 2rem;">
            <div style="position: absolute; inset: -15px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 30px; opacity: 0.1; filter: blur(20px);"></div>
            <div style="background: white; padding: 1.5rem; border-radius: 28px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); position: relative; z-index: 1; border: 1px solid rgba(0,0,0,0.05);">
                <img src="{{ asset('images/Department_of_Education_(DepEd).svg.png') }}" alt="DepEd" style="height: 60px; width: auto;">
            </div>
        </div>
        <h2 style="font-size: clamp(2rem, 5vw, 3.25rem); font-weight: 900; color: #1e293b; margin: 0; letter-spacing: -0.04em; font-family: 'Outfit', sans-serif; line-height: 1.1;">
            Online Request <span style="background: linear-gradient(135deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Portal</span>
        </h2>
        <p style="font-size: 1.125rem; color: #64748b; max-width: 620px; margin: 1.5rem auto 0; font-weight: 500; line-height: 1.7;">
            Welcome to the official Document Request Portal for Non-Teaching Personnel. Please fill out the form below to initiate your request.
        </p>
    </div>

    @if(session('success_message'))
    <div style="background: white; padding: 3rem 2rem; border-radius: 32px; border: 1px solid #e2e8f0; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); text-align: center; margin-bottom: 3rem; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(to right, #22c55e, #10b981);"></div>
        <div style="width: 80px; height: 80px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #d1fae5;">
            <i data-lucide="check-check" style="width: 40px; height: 40px;"></i>
        </div>
        <h3 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem; font-family: 'Outfit', sans-serif;">Submission Successful</h3>
        <p style="font-size: 1.125rem; color: #64748b; margin-bottom: 2.5rem; max-width: 480px; margin-left: auto; margin-right: auto;">{{ session('success_message') }}</p>
        <a href="{{ route('portal.index') }}" class="btn-portal-primary">
            <i data-lucide="plus-circle"></i>
            File Another Request
        </a>
    </div>
    @else

    <div class="portal-card">
        <!-- Card Header Overlay -->
        <div style="height: 8px; background: linear-gradient(90deg, #4f46e5, #7c3aed);"></div>
        
        <form action="{{ route('portal.submit') }}" method="POST" enctype="multipart/form-data" style="padding: 3.5rem;">
            @csrf
            
            <!-- Section: Personal Information -->
            <div style="margin-bottom: 4rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <div style="width: 38px; height: 38px; background: #eef2ff; color: #4f46e5; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="user" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: 'Outfit', sans-serif;">Employee Details</h4>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <div class="form-group">
                        <label class="portal-label">FULL NAME <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="employee_name" class="portal-input" placeholder="e.g. Juan De La Cruz" required>
                    </div>
                    <div class="form-group">
                        <label class="portal-label">SCHOOL / DEPARTMENT <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="school" class="portal-input" placeholder="e.g. SDO Quezon City" required>
                    </div>
                    <div class="form-group">
                        <label class="portal-label">NUMBER OF COPIES <span style="color: #ef4444;">*</span></label>
                        <div style="position: relative;">
                            <input type="number" name="num_copies" class="portal-input" value="1" min="1" required style="padding-left: 3.5rem;">
                            <i data-lucide="copy" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #94a3b8;"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="portal-label">REQUEST DATE</label>
                        <div style="position: relative;">
                            <input type="text" class="portal-input" value="{{ date('F d, Y') }}" readonly style="background: #f8fafc; color: #64748b; padding-left: 3.5rem; border-color: #f1f5f9;">
                            <i data-lucide="calendar" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #94a3b8;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Document Selection -->
            <div style="margin-bottom: 4rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <div style="width: 38px; height: 38px; background: #eef2ff; color: #4f46e5; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="files" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: 'Outfit', sans-serif;">Documents Requested</h4>
                </div>
                
                <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.5rem; font-weight: 600;">You may select multiple documents if needed:</p>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @php
                        $docs = [
                            'Appointment, Oath of Office, Certificate of Assumption',
                            'TOR and Diploma, Certification of Education',
                            'PDS with Work Experience Sheet',
                            'Service Record, Certificate of Employement',
                            'Certificates',
                            'IPCRF'
                        ];
                    @endphp
                    @foreach($docs as $doc)
                    <label style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem 1.75rem; background: #ffffff; border: 2px solid #f1f5f9; border-radius: 18px; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.02);" onmouseover="this.style.borderColor='#4f46e5'; this.style.background='#fbfbff';" onmouseout="this.style.borderColor='#f1f5f9'; this.style.background='#ffffff';">
                        <div style="position: relative; width: 22px; height: 22px;">
                            <input type="checkbox" name="doc_types[]" value="{{ $doc }}" style="width: 22px; height: 22px; cursor: pointer; accent-color: #4f46e5; position: relative; z-index: 1;">
                        </div>
                        <span style="font-size: 0.9375rem; font-weight: 600; color: #334155;">{{ $doc }}</span>
                    </label>
                    @endforeach
                    
                    <div style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem 1.75rem; background: #ffffff; border: 2px solid #f1f5f9; border-radius: 18px; transition: all 0.2s ease;">
                        <input type="checkbox" name="doc_types[]" value="OTHERS" id="chk_doc_others" style="width: 22px; height: 22px; accent-color: #4f46e5;">
                        <div style="flex: 1; display: flex; align-items: center; gap: 1rem;">
                            <span style="font-size: 0.9375rem; font-weight: 600; color: #334155; white-space: nowrap;">OTHERS:</span>
                            <input type="text" name="doc_others" class="portal-input" style="height: 3rem; background: white; border-width: 1px;" placeholder="Please specify if not in the list above">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Purpose -->
            <div style="margin-bottom: 4rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <div style="width: 38px; height: 38px; background: #fef3c7; color: #d97706; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="target" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: 'Outfit', sans-serif;">Purpose of Request</h4>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem;">
                    @php
                        $purposes = ['GSIS LOAN', 'PAG-IBIG LOAN', 'BANK LOAN', 'EMPLOYMENT', 'RETIREMENT', 'TRAVEL ABROAD'];
                    @endphp
                    @foreach($purposes as $p)
                    <label style="position: relative; cursor: pointer;">
                        <input type="radio" name="purpose" value="{{ $p }}" required style="position: absolute; opacity: 0; width: 0; height: 0;" onchange="updatePurpose(this)">
                        <div class="purpose-radio-box" style="padding: 1rem; text-align: center; border: 2px solid #f1f5f9; border-radius: 14px; font-weight: 700; font-size: 0.8125rem; color: #64748b; transition: all 0.2s ease; background: white;">
                            {{ $p }}
                        </div>
                    </label>
                    @endforeach
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background: #f8fafc; border-radius: 18px; border: 1px solid #e2e8f0;">
                    <label style="position: relative; cursor: pointer; display: flex; align-items: center; gap: 0.75rem;">
                        <input type="radio" name="purpose" value="OTHERS" style="width: 20px; height: 20px; accent-color: #d97706;">
                        <span style="font-weight: 700; font-size: 0.8125rem; color: #334155;">SPECIFY OTHER PURPOSE:</span>
                    </label>
                    <input type="text" name="purpose_other" class="portal-input" style="height: 3rem; background: white; border-width: 1px; flex: 1;" placeholder="State your purpose here...">
                </div>
            </div>

            <!-- Section: Requirements Upload -->
            <div style="margin-bottom: 4rem; padding: 2.5rem; background: #eef2ff; border-radius: 24px; border: 2px dashed #c7d2fe; position: relative;">
                <div style="display: flex; gap: 1.5rem;">
                    <div style="flex-shrink: 0; width: 64px; height: 64px; background: white; border-radius: 18px; display: flex; align-items: center; justify-content: center; color: #4f46e5; box-shadow: 0 10px 15px rgba(79, 70, 229, 0.1);">
                        <i data-lucide="upload-cloud" style="width: 32px; height: 32px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Supportive Documents</h4>
                        <p style="font-size: 0.875rem; color: #475569; margin-bottom: 1.5rem;">Please upload a valid DepEd ID, photocopy of your PR-ID or any government issued ID for verification purposes.</p>
                        
                        <div style="position: relative;">
                            <input type="file" name="requirements_file" required class="portal-input" style="background: white; border: 1px solid #c7d2fe; padding-top: 0.625rem; padding-bottom: 0.625rem;">
                            <p style="font-size: 0.75rem; font-weight: 600; color: #6366f1; margin: 0.75rem 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                                Accepted formats: PDF, JPG, PNG (Max: 10MB)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="padding-top: 2rem; border-top: 2px solid #f1f5f9;">
                <button type="submit" class="btn-portal-primary" style="width: 100%; height: 4.5rem; font-size: 1.25rem; letter-spacing: 0.05em; text-transform: uppercase;">
                    <i data-lucide="send" style="width: 24px; height: 24px;"></i>
                    Finalize & Submit Request
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

<script>
    function updatePurpose(radio) {
        // Reset all boxes
        document.querySelectorAll('.purpose-radio-box').forEach(box => {
            box.style.borderColor = '#f1f5f9';
            box.style.background = 'white';
            box.style.color = '#64748b';
            box.style.transform = 'none';
        });
        
        // Style selected box
        if (radio.checked) {
            const box = radio.nextElementSibling;
            box.style.borderColor = '#4f46e5';
            box.style.background = '#eef2ff';
            box.style.color = '#4f46e5';
            box.style.transform = 'translateY(-2px)';
        }
    }
</script>
@endsection
