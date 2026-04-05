@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@section('title', 'Add Employee')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Add Employee</h1>
        <p class="page-subtitle">Register a new employee into the system</p>
    </div>

    <!-- Success Toast Notification -->
    @if(session('success_message'))
    <div id="successToast" class="toast-notification toast-success">
        <div class="toast-icon">
            <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="toast-content">
            <p class="toast-title">Success!</p>
            <p class="toast-text">{{ session('success_message') }}</p>
        </div>
        <button class="toast-close" onclick="closeToast('successToast')">
            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div id="errorToast" class="toast-notification toast-error" style="background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c;">
        <div class="toast-icon">
            <i data-lucide="alert-circle" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="toast-content">
            <p class="toast-title" style="color: #991b1b; font-weight: 800;">Validation Error!</p>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button class="toast-close" onclick="closeToast('errorToast')">
            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
        </button>
    </div>
    @endif

    <!-- Add Employee Card -->
    <div class="add-emp-wrapper">
        <div class="add-emp-card">
            <!-- Card Header -->
            <div class="add-emp-card-header">
                <div class="add-emp-icon-box">
                    <i data-lucide="user-plus" style="width: 28px; height: 28px;"></i>
                </div>
                <div>
                    <h2 class="add-emp-card-title">New Employee Details</h2>
                    <p class="add-emp-card-subtitle">Fill in all required fields marked with <span style="color: #e11d48;">*</span></p>
                </div>
            </div>

            <form method="POST" action="{{ route('employees.store') }}" id="addEmployeeForm" enctype="multipart/form-data" data-masterlist-url="{{ route('employees.masterlist') }}">
                @csrf

                <!-- Section: Profile Picture -->
                <div class="add-emp-section" style="border-bottom: 2px solid #e2e8f0; background: #f8fafc; padding-top: 1rem; padding-bottom: 1rem;">
                    <div class="add-emp-section-label" style="margin-bottom: 0.75rem;">
                        <i data-lucide="image" style="width: 15px; height: 15px;"></i>
                        Profile Picture
                    </div>
                    <div class="profile-upload-container" style="gap: 1.5rem;">
                        <div class="profile-preview-outer">
                            <div class="profile-inner-circle">
                                <img id="avatarPreview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'%3E%3C/path%3E%3Ccircle cx='12' cy='7' r='4'%3E%3C/circle%3E%3C/svg%3E" alt="Preview">
                                <input type="hidden" name="cropped_image" id="croppedImageData">
                            </div>
                        </div>
                        <div class="profile-upload-info">
                            <label class="btn btn-outline btn-sm" for="profile_picture" style="margin-bottom: 0.5rem; cursor: pointer;">
                                <i data-lucide="upload" style="width: 14px; height: 14px;"></i>
                                Choose Photo
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <p class="upload-hint">Recommended: Square image, max 2MB (JPG, PNG)</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Personal Information -->
                <div class="add-emp-section">
                    <div class="add-emp-section-label">
                        <i data-lucide="user" style="width: 15px; height: 15px;"></i>
                        Personal Information
                    </div>
                    <div class="add-emp-grid">
                        <div class="form-group">
                            <label class="form-label" for="last_name">Surname <span class="required-star">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-input {{ $errors->has('last_name') ? 'input-error' : '' }}" placeholder="e.g. Dela Cruz" required oninput="syncFullName()" value="{{ old('last_name') }}">
                            @error('last_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name <span class="required-star">*</span></label>
                            <input type="text" id="first_name" name="first_name" class="form-input {{ $errors->has('first_name') ? 'input-error' : '' }}" placeholder="e.g. Juan" required oninput="syncFullName()" value="{{ old('first_name') }}">
                            @error('first_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-input" placeholder="e.g. Abad" oninput="syncFullName()" value="{{ old('middle_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="suffix">Suffix</label>
                            <input type="text" id="suffix" name="suffix" class="form-input" placeholder="e.g. Jr., Sr., III" oninput="syncFullName()" value="{{ old('suffix') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Birthday <span class="required-star">*</span></label>
                            <div class="date-input-container">
                                <input type="text" id="date_of_birth_picker" class="form-input {{ $errors->has('date_of_birth') ? 'input-error' : '' }}" placeholder="e.g. March 1, 1990" required value="{{ old('date_of_birth') }}">
                                <input type="hidden" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                <button type="button" class="calendar-trigger" id="calendarTrigger">
                                    <i data-lucide="calendar"></i>
                                </button>
                            </div>
                            @error('date_of_birth')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="age">Age</label>
                            <input type="text" id="age" class="form-input" placeholder="Auto-calculated" readonly style="background: var(--bg-main); color: var(--text-muted); cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="sex">Sex <span class="required-star">*</span></label>
                            <select id="sex" name="sex" class="form-input {{ $errors->has('sex') ? 'input-error' : '' }}" required>
                                <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Select sex</option>
                                <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="civil_status">Civil Status <span class="required-star">*</span></label>
                            <select id="civil_status" name="civil_status" class="form-input {{ $errors->has('civil_status') ? 'input-error' : '' }}" required>
                                <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>Select status</option>
                                <option value="Single" {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('civil_status')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="address">Address <span class="required-star">*</span></label>
                            <input type="text" id="address" name="address" class="form-input {{ $errors->has('address') ? 'input-error' : '' }}" placeholder="e.g. 123 Rizal Street, Brgy. Example, Caloocan City" required value="{{ old('address') }}">
                            @error('address')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Contact Information -->
                <div class="add-emp-section">
                    <div class="add-emp-section-label">
                        <i data-lucide="phone" style="width: 15px; height: 15px;"></i>
                        Contact Information
                    </div>
                    <div class="add-emp-grid">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-input" placeholder="e.g. 09123456789" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Personal Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="e.g. juan@example.com" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>

                <!-- Section: Employment Information -->
                <div class="add-emp-section">
                    <div class="add-emp-section-label">
                        <i data-lucide="briefcase" style="width: 15px; height: 15px;"></i>
                        Employment Information
                    </div>
                    <div class="add-emp-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="position">Current Position <span class="required-star">*</span></label>
                            <input type="text" id="position" name="position" class="form-input {{ $errors->has('position') ? 'input-error' : '' }}" placeholder="e.g. Teacher I" required style="text-transform: none !important;" value="{{ old('position') }}">
                            @error('position')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="agency">Agency <span class="required-star">*</span></label>
                            <input type="text" id="agency" name="agency" class="form-input {{ $errors->has('agency') ? 'input-error' : '' }}" placeholder="e.g. SDO - Caloocan City" required style="text-transform: none !important;" value="{{ old('agency') }}">
                            @error('agency')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="category">Category <span class="required-star">*</span></label>
                            <select id="category" name="category" class="form-input {{ $errors->has('category') ? 'input-error' : '' }}" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select Category</option>
                                <option value="National" {{ old('category') === 'National' ? 'selected' : '' }}>National</option>
                                <option value="City" {{ old('category') === 'City' ? 'selected' : '' }}>City</option>
                            </select>
                            @error('category')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="employment_status">Status of Appointment <span class="required-star">*</span></label>
                            <select id="employment_status" name="employment_status" class="form-input {{ $errors->has('employment_status') ? 'input-error' : '' }}" required>
                                <option value="" disabled {{ old('employment_status') ? '' : 'selected' }}>Select Status</option>
                                <option value="Permanent" {{ old('employment_status') === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="Contractual" {{ old('employment_status') === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                <option value="Original" {{ old('employment_status') === 'Original' ? 'selected' : '' }}>Original</option>
                            </select>
                            @error('employment_status')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="salary_grade">Salary Grade</label>
                            <input type="text" id="salary_grade" name="salary_grade" class="form-input" placeholder="e.g. 11" value="{{ old('salary_grade') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="level_of_position">Level of Position</label>
                            <input type="text" id="level_of_position" name="level_of_position" class="form-input" placeholder="e.g. 1st Level" value="{{ old('level_of_position') }}">
                        </div>
                    </div>
                </div>

                <!-- Section: Document Classification -->
                <div class="add-emp-section">
                    <div class="add-emp-section-label" style="justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="file-check" style="width: 15px; height: 15px;"></i>
                            Document Classification
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" onclick="addDocRow()" style="padding: 0.35rem 0.75rem;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                            Add Classification Row
                        </button>
                    </div>
                    
                    <div id="docRowsContainer">
                        <div class="doc-row" id="docRow_0">
                            <div class="add-emp-grid">
                                <div class="form-group">
                                    <label class="form-label">Classification</label>
                                    <select name="doc_items[0][classification]" class="form-input">
                                        <option value="UNCATEGORIZED">Select Classification</option>
                                        <option value="APPOINTMENT">Appointment</option>
                                        <option value="SERVICE RECORD">Service Record</option>
                                        <option value="LEAVE / DTR">Leave / DTR</option>
                                        <option value="PERSONAL DATA SHEET">Personal Data Sheet</option>
                                        <option value="CLEARANCES">Clearances</option>
                                        <option value="OTHERS">Others</option>
                                    </select>
                                </div>
                                <div class="form-group" style="grid-column: span 2; position: relative;">
                                    <label class="form-label">Upload Files</label>
                                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                                        <input type="file" name="doc_items[0][files][]" class="form-input" multiple acceptance=".pdf,.doc,.docx,.xlsx,.png,.jpg,.jpeg">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="upload-hint" style="margin-top: 1rem;">Select a classification and upload the corresponding files for that group.</p>
                </div>

                <!-- Hidden full name field -->
                <input type="hidden" id="name" name="name">

                <!-- Form Actions -->
                <div class="add-emp-actions">
                    <button type="button" class="btn btn-outline" onclick="resetForm()">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
                        Save Employee
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Panel -->
        <div class="add-emp-info-panel">
            <div class="info-panel-section">
                <div class="info-panel-icon" style="background: #ecfdf5; color: #10b981;">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="info-panel-title">After Saving</p>
                    <p class="info-panel-text">The employee will be added as <strong>Active</strong> and will appear in the Master List.</p>
                </div>
            </div>
            <div class="info-panel-section">
                <div class="info-panel-icon" style="background: #fef3c7; color: #d97706;">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="info-panel-title">Required Fields</p>
                    <p class="info-panel-text">All fields marked with (<span style="color: #e11d48; font-weight: 700;">*</span>) are required.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Progress Modal -->
<div id="uploadProgressModal" class="modal-modern" style="z-index: 10100; background: rgba(0,0,0,0.4); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; position: fixed; inset: 0;">
    <div class="modal-content-modern animate-scale-up" style="max-width: 450px; text-align: center; padding: 2.5rem; background: var(--bg-card, white); border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div class="upload-icon-wrapper" style="width: 80px; height: 80px; background: var(--bg-main, #f8fafc); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary, #3b82f6);">
            <i data-lucide="cloud-upload" style="width: 40px; height: 40px;" id="statusIcon"></i>
        </div>
        <h3 id="uploadStatusTitle" style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main, #0f172a);">Saving Employee Record</h3>
        <p id="uploadStatusText" style="color: var(--text-muted, #64748b); font-size: 0.95rem; margin-bottom: 2rem;">Please wait while we process the files and generate the record...</p>
        
        <div class="progress-box" style="margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: var(--text-main, #0f172a); margin-bottom: 0.6rem;">
                <span id="progressText">0% Complete</span>
                <span id="progressRate">0 KB/s</span>
            </div>
            <div style="width: 100%; height: 12px; background: var(--bg-main, #f8fafc); border-radius: 10px; overflow: hidden; border: 1px solid var(--border-light, #e2e8f0);">
                <div id="progressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6, #6366f1); border-radius: 10px; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"></div>
            </div>
        </div>
        
        <p style="font-size: 0.8rem; color: #ef4444; font-weight: 600;" id="uploadAlert">Do not close or refresh this page.</p>
    </div>
</div>

<!-- Cropper Modal -->
<div id="cropperModal" style="display: none; position: fixed; inset: 0; z-index: 10001; background: rgba(15,23,42,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card, white); border-radius: 16px; width: 100%; max-width: 500px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); border: 1px solid var(--border-light, #e2e8f0);">
        <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0;">Crop Profile Photo</h3>
            <button type="button" onclick="closeCropper()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 20px; max-height: 400px; overflow: hidden;">
            <img id="cropperImage" src="" style="max-width: 100%; display: block;">
        </div>
        <div style="padding: 1.25rem; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="closeCropper()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="applyCrop()">Apply Crop</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary: #3b82f6;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
    }

    body[data-theme="dark"] {
        --bg-main: #0f172a;
        --bg-card: #1e293b;
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --border-light: #334155;
    }

    body[data-theme="night"] {
        --bg-main: #f2ead3;
        --bg-card: #fffcf0;
        --text-main: #5c4137;
        --text-muted: #92400e;
        --border-light: #ead6bb;
        --primary: #d97706;
    }

    /* Toast Styles */
    .toast-notification {
        position: fixed;
        top: 2rem;
        right: 2rem;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.4);
        animation: toastSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-width: 420px;
    }
    .toast-success {
        background: #ecfdf5;
        border: 2px solid #059669;
        color: #065f46;
    }
    .toast-error {
        background: #fef2f2;
        border: 2px solid #ef4444;
        color: #991b1b;
    }
    .toast-icon {
        display: flex; align-items: center; justify-content: center;
        width: 2.5rem; height: 2.5rem;
        background: #10b981; color: white;
        border-radius: var(--radius-md);
        flex-shrink: 0;
    }
    .toast-content { flex: 1; }
    .toast-title { font-weight: 800; font-size: 0.9375rem; margin-bottom: 0.125rem; color: inherit; }
    .toast-text { font-size: 0.8125rem; line-height: 1.4; color: inherit; opacity: 0.9; }
    .toast-close { background: none; border: none; color: inherit; opacity: 0.5; cursor: pointer; padding: 0.25rem; border-radius: var(--radius-sm); transition: var(--transition); flex-shrink: 0; }
    .toast-close:hover { opacity: 1; background: rgba(0,0,0,0.05); }

    @keyframes toastSlideIn {
        from { opacity: 0; transform: translateX(100px) scale(0.95); }
        to   { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes toastSlideOut {
        from { opacity: 1; transform: translateX(0) scale(1); }
        to   { opacity: 0; transform: translateX(100px) scale(0.95); }
    }

    /* Layout Setup */
    .add-emp-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        width: 100%;
    }
    .add-emp-card {
        flex: 1;
        min-width: 0; /* Prevents flex items from overflowing child contents */
    }
    .add-emp-info-panel {
        width: 280px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex-shrink: 0;
    }

    /* Responsive Stacks */
    @media (max-width: 1024px) {
        .add-emp-wrapper {
            flex-direction: column;
        }
        .add-emp-info-panel {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15); /* Stronger shadow to define shape */
        overflow: hidden;
    }

    body[data-theme="dark"] .add-emp-card {
        border-color: #475569 !important;
    }

    body[data-theme="night"] .add-emp-card {
        border-color: #451a03 !important; /* Significantly darker brown for Night Mode */
        box-shadow: 0 10px 40px -5px rgba(69, 26, 3, 0.2); 
    }

    /* Profile Upload Layout */
    .profile-upload-container {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    .profile-upload-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .upload-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin: 0;
        font-weight: 500;
    }
    body[data-theme="dark"] .upload-hint,
    body[data-theme="night"] .upload-hint {
        color: rgba(255,255,255,0.7) !important;
    }
    body[data-theme="night"] .upload-hint {
        color: #92400e !important; /* Specific warm brown for night mode readability */
    }

    .add-emp-card-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.75rem 2rem;
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        border-bottom: 1px solid var(--border-light);
    }

    body[data-theme="dark"] .add-emp-card-header {
        background: linear-gradient(135deg, #1e293b 0%, #1e3a5f 100%);
    }

    .add-emp-icon-box {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #3b82f6, #4f46e5);
        display: flex; align-items: center; justify-content: center;
        color: white;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        flex-shrink: 0;
    }

    .add-emp-card-title {
        font-size: 1.125rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 0.25rem;
        font-family: 'Outfit', sans-serif;
    }
    .add-emp-card-subtitle {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Sections */
    .add-emp-section {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--border-light);
    }
    .add-emp-section:last-of-type { border-bottom: none; }

    .add-emp-section-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--primary);
        margin-bottom: 1.25rem;
    }

    .add-emp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 768px) {
        .add-emp-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .add-emp-grid { grid-template-columns: 1fr; }
        .add-emp-grid .form-group[style*="grid-column"] { grid-column: 1 !important; }
    }

    /* Form elements */
    .form-group { margin-bottom: 0; }
    .form-label {
        display: block;
        font-size: 0.7188rem;
        font-weight: 800;
        color: #0f172a; /* Uniform Dark Label */
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .form-input {
        width: 100%;
        padding: 0.72rem 1rem;
        border: 2px solid #64748b; /* Darker Borders */
        border-radius: 10px;
        outline: none;
        font-size: 0.9rem;
        color: var(--text-main);
        background: var(--bg-card);
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        font-family: 'Inter', sans-serif;
    }

    /* Date picker wrapper */
    .date-input-container {
        position: relative;
        display: flex;
        align-items: center;
    }
    .calendar-trigger {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        transition: color 0.2s;
    }
    .calendar-trigger:hover { color: var(--primary); }
    .calendar-trigger i { width: 18px; height: 18px; }
    .date-input-container .form-input { padding-right: 2.5rem; }
    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        background: #fff;
    }
    body[data-theme="dark"] .form-input {
        border-color: #475569;
        background: #1e293b;
    }
    body[data-theme="dark"] .form-input:focus {
        border-color: #818cf8;
        background: #0f172a;
    }
    .input-error { border-color: #e11d48 !important; }
    .field-error { font-size: 0.75rem; color: #e11d48; margin-top: 0.3rem; display: block; }
    .required-star { color: #e11d48; }

    select.form-input {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    /* Actions */
    .add-emp-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.5rem 2rem;
        background: var(--bg-main);
        border-top: 1px solid var(--border-light);
    }

    /* Info Panel */
    .add-emp-info-panel {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-panel-section {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-light);
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .info-panel-section:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .info-panel-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: #3b82f6;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .info-panel-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }
    .info-panel-text {
        font-size: 0.775rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Dark Mode Label Overrides */
    body[data-theme="dark"] .form-label,
    body[data-theme="night"] .form-label {
        color: #f8fafc !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    /* Theme-specific Overrides */
    body[data-theme="dark"] .add-emp-section {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    body[data-theme="night"] .add-emp-card {
        background: #fdfbf7 !important;
        border-color: #451a03 !important; /* Deep dark brown */
    }

    body[data-theme="night"] .add-emp-section {
        background: #fdfbf7 !important;
        border-color: #ead6bb !important; /* Darker tan for sections */
    }

    body[data-theme="dark"] .profile-preview-outer {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }
    
    body[data-theme="night"] .profile-preview-outer {
        background: linear-gradient(135deg, #f59e0b, #fbbf24, #f87171) !important;
        box-shadow: 0 10px 25px rgba(217, 119, 6, 0.2);
    }

    /* 3-Layer Profile Picture Structure */
    .profile-preview-outer {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        padding: 3px; /* Outer Gradient Width */
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 12px 24px -8px rgba(0,0,0,0.2);
    }
    
    .profile-inner-circle {
        width: 100%;
        height: 100%;
        background: white; /* Middle White Ring */
        border-radius: 50%;
        padding: 2px; /* White ring width before reaching the image */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    body[data-theme="dark"] .profile-inner-circle {
        background: #1e293b; /* White ring becomes dark blue in dark mode for better look */
    }
    
    body[data-theme="night"] .profile-inner-circle {
        background: #fdfbf7; /* White ring matches yellowish bg in night mode */
    }

    .profile-inner-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%; /* Inner Picture */
        background: #f1f5f9;
    }

    /* Doc Row Animation */
    .doc-row {
        padding: 1.25rem;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        background: rgba(248, 250, 252, 0.5);
        animation: fadeIn 0.3s ease;
    }
    body[data-theme="dark"] .doc-row,
    body[data-theme="night"] .doc-row {
        border-color: #334155;
        background: rgba(30, 41, 59, 0.3);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Cropper Modal Fix */
    #cropperModal {
        display: none;
    }
    #cropperModal.active {
        display: flex !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="{{ asset('assets/js/add-employee.js') }}"></script>
@endpush
