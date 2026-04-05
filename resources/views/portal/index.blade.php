@extends('layouts.portal')

@section('title', 'Online Request Portal')

@section('content')
<div style="animation: fadeInUp 0.6s ease-out; padding-bottom: 3rem;">
    <!-- Hero Section -->
    <div style="text-align: center; margin-bottom: clamp(2rem, 8vw, 4rem); padding: 0 1rem;">
        <div style="display: inline-flex; position: relative; margin-bottom: clamp(1rem, 4vw, 2rem);">
            <div style="position: absolute; inset: -15px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 30px; opacity: 0.1; filter: blur(20px);"></div>
            <div style="background: white; padding: 0.5rem; border-radius: 50%; box-shadow: 0 15px 35px rgba(0,0,0,0.1); position: relative; z-index: 1; border: 1px solid rgba(0,0,0,0.05); width: clamp(80px, 15vw, 100px); height: clamp(80px, 15vw, 100px); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="HRNTP" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
            </div>
        </div>
        <h2 style="font-size: clamp(1.75rem, 6vw, 3.25rem); font-weight: 900; color: #1e293b; margin: 0; letter-spacing: -0.04em; font-family: 'Outfit', sans-serif; line-height: 1.1;">
            Online Request <span style="background: linear-gradient(135deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Portal</span>
        </h2>
        <p style="font-size: clamp(0.9375rem, 2.5vw, 1.125rem); color: #64748b; max-width: 620px; margin: clamp(1rem, 3vw, 1.5rem) auto 0; font-weight: 500; line-height: 1.7;">
            Welcome to the official Document Request Portal for Non-Teaching Personnel. Please fill out the form below to initiate your request.
        </p>
    </div>

    @if(session('success_message'))
    <div style="background: white; padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem); border-radius: 32px; border: 1px solid #e2e8f0; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); text-align: center; margin-bottom: 3rem; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(to right, #22c55e, #10b981);"></div>
        <div style="width: 80px; height: 80px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #d1fae5;">
            <i data-lucide="check-check" style="width: 40px; height: 40px;"></i>
        </div>
        <h3 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem; font-family: 'Outfit', sans-serif;">Submission Successful</h3>
        <p style="font-size: 1.125rem; color: #64748b; margin-bottom: 2.5rem; max-width: 480px; margin-left: auto; margin-right: auto;">{{ session('success_message') }}</p>
        <a href="{{ route('portal.index') }}" class="btn-portal-primary" style="width: 100%; max-width: 320px;">
            <i data-lucide="plus-circle"></i>
            File Another Request
        </a>
    </div>
    @else

    <div class="portal-card" style="border-radius: clamp(1rem, 4vw, 2rem);">
        <!-- Card Header Overlay -->
        <div style="height: 8px; background: linear-gradient(90deg, #4f46e5, #7c3aed);"></div>
        
        <form id="portalRequestForm" action="{{ route('portal.submit') }}" method="POST" enctype="multipart/form-data" style="padding: clamp(1.5rem, 5vw, 3.5rem);">
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
                        <label class="portal-label">AGENCY <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="agency" class="portal-input" placeholder="e.g. SDO Quezon City" required>
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

            <!-- Section: Document Requested -->
            <div style="margin-bottom: clamp(2rem, 8vw, 4rem);">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <div style="width: 38px; height: 38px; background: #eef2ff; color: #4f46e5; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="files" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: 'Outfit', sans-serif;">Document Requested</h4>
                </div>
                
                <div class="form-group">
                    <label class="portal-label">SPECIFY DOCUMENT <span style="color: #ef4444;">*</span></label>
                    <textarea name="document_request" class="portal-input" style="min-height: 120px; padding: 1.25rem; resize: vertical;" placeholder="e.g. Service Record, TOR, Diploma, etc." required></textarea>
                    <p style="font-size: 0.8125rem; color: #64748b; margin-top: 0.75rem; font-weight: 500;">Please clearly state all documents you are requesting.</p>
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
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    @php
                        $purposes = ['GSIS LOAN', 'PAG-IBIG LOAN', 'BANK LOAN', 'EMPLOYMENT', 'RETIREMENT', 'TRAVEL ABROAD'];
                    @endphp
                    @foreach($purposes as $p)
                    <label style="position: relative; cursor: pointer;">
                        <input type="checkbox" name="purpose" value="{{ $p }}" style="position: absolute; opacity: 0; width: 0; height: 0;" onclick="handleToggle(this)">
                        <div class="purpose-radio-box" style="padding: 0.875rem 0.5rem; text-align: center; border: 2px solid #f1f5f9; border-radius: 14px; font-weight: 700; font-size: 0.75rem; color: #64748b; transition: all 0.2s ease; background: white; white-space: nowrap;">
                            {{ $p }}
                        </div>
                    </label>
                    @endforeach
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem; padding: 1.25rem; background: #f8fafc; border-radius: 18px; border: 1px solid #e2e8f0;">
                    <label style="position: relative; cursor: pointer; display: flex; align-items: center; gap: 0.75rem;">
                        <input type="checkbox" name="purpose" value="OTHERS" style="width: 20px; height: 20px; accent-color: #d97706;" onclick="handleToggle(this)">
                        <span style="font-weight: 700; font-size: 0.8125rem; color: #334155;">SPECIFY OTHER PURPOSE:</span>
                    </label>
                    <input type="text" name="purpose_other" id="purpose_other_input" class="portal-input" style="height: 3rem; background: white; border-width: 1px; width: 100%; display: none;" placeholder="State your purpose here...">
                </div>
            </div>

            <!-- Section: Requirements Upload -->
            <div style="margin-bottom: clamp(2rem, 8vw, 4rem); padding: clamp(1rem, 4vw, 2.5rem); background: #f8fafc; border-radius: 24px; border: 2px dashed #cbd5e1; position: relative;">
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                        <div style="flex-shrink: 0; width: 44px; height: 44px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #4f46e5;">
                            <i data-lucide="upload-cloud" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin: 0;">Supportive Documents</h4>
                            <p style="font-size: 0.8125rem; color: #64748b; margin: 0.25rem 0 0 0;">Upload DepEd ID or any government-issued ID.</p>
                        </div>
                    </div>
                    
                    <div id="fileInputsContainer">
                        <div class="file-input-wrapper" style="margin-bottom: 1rem; display: flex; gap: 0.75rem; align-items: center;">
                            <div style="flex: 1; position: relative;">
                                <input type="file" name="requirements_files[]" required class="portal-input" style="background: white; border: 1px solid #e2e8f0; padding-top: 0.625rem; padding-bottom: 0.625rem;">
                            </div>
                            <button type="button" onclick="addFileInput()" class="btn-portal-secondary" style="height: 3.25rem; width: 3.25rem; display: flex; align-items: center; justify-content: center; border-radius: 14px; padding: 0;">
                                <i data-lucide="plus" style="width: 20px; height: 20px;"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p style="font-size: 0.75rem; font-weight: 600; color: #6366f1; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                        Accepted formats: PDF, JPG, PNG (Max: 500MB each)
                    </p>
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

    <!-- Upload Progress Modal (Portal) -->
    <div id="uploadProgressModal" style="position: fixed; inset: 0; z-index: 10000; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;">
        <div style="background: white; width: 100%; max-width: 480px; padding: 3rem; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); text-align: center; margin: 1.5rem;">
            <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #4f46e5;">
                <i data-lucide="cloud-upload" style="width: 40px; height: 40px;"></i>
            </div>
            <h3 id="uploadStatusTitle" style="font-family: 'Outfit'; font-size: 1.75rem; font-weight: 900; color: #1e293b; margin-bottom: 0.75rem;">Submitting Request</h3>
            <p id="uploadStatusText" style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 2.5rem;">Please wait while we securely upload your supportive documents...</p>
            
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem;">
                    <span id="progressText">0% Complete</span>
                    <span id="progressRate">0 KB/s</span>
                </div>
                <div style="width: 100%; height: 14px; background: #f1f5f9; border-radius: 100px; overflow: hidden; border: 1px solid #e2e8f0;">
                    <div id="progressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #4f46e5, #7c3aed); border-radius: 100px; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                </div>
            </div>
            
            <div style="padding: 1rem; background: #fff1f2; border-radius: 14px; border: 1px solid #ffe4e6; display: flex; align-items: center; gap: 0.75rem; justify-content: center;">
                <i data-lucide="shield-alert" style="width: 18px; height: 18px; color: #e1b31dff;"></i>
                <p style="font-size: 0.8125rem; color: #e1b31dff; font-weight: 700; margin: 0;">Do not close your browser until complete.</p>
            </div>
        </div>
    </div>

    </div>
    @endif
</div>

<script src="{{ asset('assets/js/portal.js') }}"></script>
@endsection
