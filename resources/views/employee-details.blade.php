@extends('layouts.app')

@section('title', $employee->last_name . ', ' . $employee->first_name)

@push('styles')
<!-- docx-preview for local .docx viewing -->
<script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
<script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>
@endpush

@section('content')
<div class="page-content" style="padding: 1rem 10px; width: 100%; margin: 0;">
    <!-- Modern Header & Banner -->
    <div class="profile-banner-container animate-fade-in">
        <div class="profile-banner">
            <div class="banner-overlay"></div>
            
            <div class="banner-navigation">
                <a href="{{ route('employees.masterlist') }}" class="banner-back-btn">
                    <i data-lucide="arrow-left"></i>
                </a>
                
                <div class="status-badge status-{{ $employee->status }}">
                    <span class="status-dot"></span>
                    {{ strtoupper($employee->status) }}
                </div>
            </div>
        </div>
        
        <div class="profile-header-content">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar-circle" id="avatarDisplay" onclick="document.getElementById('avatarInput').click()" style="cursor: pointer; overflow: hidden;">
                    @if($employee->profile_picture)
                        <img src="{{ asset($employee->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($employee->first_name ?? $employee->name, 0, 1)) }}{{ strtoupper(substr($employee->last_name ?? '', 0, 1)) }}
                    @endif
                </div>
                <button class="avatar-camera-btn" onclick="document.getElementById('avatarInput').click()">
                    <i data-lucide="camera" style="width: 18px;"></i>
                </button>
                <form id="avatarForm" action="{{ route('employees.update-avatar', ['id' => $employee->id]) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="file" name="profile_picture" id="avatarInput" accept="image/*" onchange="this.form.submit()">
                </form>
            </div>
            
            <div class="profile-main-meta">
                <h1 class="employee-display-name">{{ $employee->name }}</h1>
                <div class="employee-sub-meta">
                    <span class="meta-item"><i data-lucide="briefcase"></i> {{ $employee->position }}</span>
                    <span class="meta-item"><i data-lucide="building"></i> {{ $employee->department }}</span>
                    <span class="meta-item"><i data-lucide="map-pin"></i> {{ $employee->address ? explode(',', $employee->address)[0] : 'No Address set' }}</span>
                </div>
            </div>


        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="profile-tabs-nav animate-slide-up" style="animation-delay: 0.1s;">
        <button class="tab-btn active" onclick="switchTab(this, 'personal')">
            <i data-lucide="user"></i>
            Personal & Contact
        </button>
        <button class="tab-btn" onclick="switchTab(this, 'work')">
            <i data-lucide="briefcase"></i>
            Work Info
        </button>
        <button class="tab-btn" onclick="switchTab(this, 'documents')">
            <i data-lucide="file-text"></i>
            Documents & Viewer
        </button>
    </div>

    @if(session('success_message'))
    <div id="successToast" class="toast-modern animate-fade-in">
        <div class="toast-content">
            <i data-lucide="check-circle" class="toast-icon"></i>
            <span>{{ session('success_message') }}</span>
        </div>
    </div>
    @endif

    <div class="main-profile-layout">
        <!-- Main Information Content Panes -->
        <div class="info-content-area full-width animate-slide-up" style="animation-delay: 0.2s;">
            
            <!-- Personal Info Tab (Now holds Contact & Emergency) -->
            <div id="personalTab" class="info-tab-pane active">
                <div class="personal-grid-modern">
                    <!-- Identity Card -->
                    <div class="card-premium info-card">
                        <div class="card-header-modern">
                            <h3><i data-lucide="user-check" style="color: #3b82f6;"></i> Identity Details</h3>
                            <button class="btn-edit-icon" onclick="openEditModal('personal')">
                                <i data-lucide="pencil"></i>
                            </button>
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group"><label>Full Name</label><span>{{ $employee->name }}</span></div>
                            <div class="info-group"><label>Current Position</label><span>{{ $employee->position }}</span></div>
                            <div class="info-group"><label>Birthday</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '--' }}</span></div>
                            <div class="info-group"><label>Age</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->age . ' years' : '--' }}</span></div>
                            <div class="info-group"><label>Sex</label><span>{{ $employee->sex ?: '--' }}</span></div>
                            <div class="info-group full-width"><label>Address</label><span>{{ $employee->address ?: '--' }}</span></div>
                            <div class="info-group full-width"><label>School/Department</label><span>{{ $employee->department }}</span></div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="card-premium animate-slide-up" style="animation-delay: 0.2s;">
                        <div class="card-header-modern">
                            <h3><i data-lucide="contact" style="color: #10b981;"></i> Contact Information</h3>
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group full-width"><label><i data-lucide="mail"></i> Email Address</label><span>{{ $employee->email ?: '--' }}</span></div>
                            <div class="info-group full-width"><label><i data-lucide="phone"></i> Phone Number</label><span>{{ $employee->phone ?: '--' }}</span></div>
                            
                            <div style="grid-column: span 2; margin-top: 1rem; border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                                <h4 style="font-size: 0.7rem; font-weight: 800; color: #ef4444; text-transform: uppercase; margin-bottom: 0.75rem;">Emergency Contact</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div class="info-group"><label>Contact Person</label><span>{{ $employee->emergency_contact ?: '--' }}</span></div>
                                    <div class="info-group"><label>Contact Number</label><span>{{ $employee->emergency_phone ?: '--' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Info Tab -->
            <div id="workTab" class="info-tab-pane">
                <div class="card-premium info-card" style="width: 100%; margin: 0 auto;">
                    <div class="card-header-modern">
                        <h3><i data-lucide="briefcase" style="color: #3b82f6; width: 20px; height: 20px;"></i> Employment Information</h3>
                        <button class="btn-edit-icon" onclick="openEditModal('work')">
                            <i data-lucide="pencil"></i>
                        </button>
                    </div>
                    <div class="info-grid-modern">
                        <div class="info-group">
                            <label><i data-lucide="award" style="color: #3b82f6;"></i> Position</label>
                            <span>{{ $employee->position }}</span>
                        </div>
                        <div class="info-group">
                            <label><i data-lucide="building-2" style="color: #10b981;"></i> Office</label>
                            <span>{{ $employee->department }}</span>
                        </div>
                        <div class="info-group">
                            <label><i data-lucide="hash" style="color: #8b5cf6;"></i> S.O Number</label>
                            <span>{{ $employee->so_number ?: '--' }}</span>
                        </div>
                        <div class="info-group">
                            <label><i data-lucide="info" style="color: #f59e0b;"></i> Employment Status</label>
                            @if($employee->status == 'active')
                                <span class="badge-status-green">ACTIVE</span>
                            @else
                                <span class="text-danger" style="font-weight: 800;">{{ strtoupper($employee->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab (List + Viewer Side by Side) -->
            <div id="documentsTab" class="info-tab-pane">
                <div class="docs-tab-layout">
                    <!-- Documents List -->
                    <div class="card-premium docs-mini-card">
                        <div class="mini-card-header">
                            <div>
                                <h4>Scanned Files</h4>
                                <p>{{ $doc_count }} total items</p>
                            </div>
                            @if($employee->status === 'active')
                            <button class="btn-mini-import" onclick="triggerImport()" title="Import PDF">
                                <i data-lucide="plus"></i>
                                <span>Import</span>
                            </button>
                            @endif
                        </div>
                        
                        <div class="mini-search">
                            <input type="text" id="docSearch" placeholder="Search documents..." oninput="filterDocuments()">
                            <i data-lucide="search"></i>
                        </div>

                        <div id="documentListContainer" class="mini-docs-list">
                            @php
                                $categories = [
                                    'APPOINTMENT' => 'briefcase',
                                    'EDUCATION' => 'graduation-cap',
                                    'PERSONAL DATA SHEET' => 'file-user',
                                    'EXPERIENCE' => 'award',
                                    'CERTIFICATES FOR TRAINING' => 'scroll',
                                    'PERFORMANCE RATING' => 'trending-up',
                                ];
                                
                                $generalDocs = $documents->filter(function($doc) {
                                    return empty($doc->category) || strtoupper($doc->category) === 'UNCATEGORIZED' || strtoupper($doc->category) === 'GENERAL';
                                });
                                
                                $classifiedDocs = $documents->filter(function($doc) {
                                    return !empty($doc->category) && strtoupper($doc->category) !== 'UNCATEGORIZED' && strtoupper($doc->category) !== 'GENERAL';
                                })->groupBy(fn($doc) => strtoupper($doc->category));
                            @endphp

                            <!-- Uploaded Files / General Section -->
                            <div class="general-docs-section">
                                <h5 class="section-title"><i data-lucide="upload-cloud" style="width: 14px; color: #3b82f6;"></i> Uploaded Files</h5>
                                @forelse($generalDocs as $doc)
                                <div class="mini-doc-item" id="doc-item-{{ $doc->id }}" 
                                     data-url="{{ asset($doc->file_path) }}" 
                                     data-name="{{ $doc->document_name }}" 
                                     onclick="previewDocById('{{ $doc->id }}', '{{ asset($doc->file_path) }}', '{{ $doc->document_name }}')">
                                    <div class="mini-doc-icon"><i data-lucide="file-text"></i></div>
                                    <div class="mini-doc-info">
                                        <span class="name">{{ $doc->document_name }}</span>
                                        <span class="date">Modified {{ $doc->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($employee->status === 'active')
                                    <form action="{{ route('employees.delete-doc', ['id' => $doc->id]) }}" method="POST" onclick="event.stopPropagation()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mini-btn-delete" title="Delete" onclick="return confirm('Delete this document?')">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                <div class="mini-empty" style="padding: 0.5rem; font-size: 0.75rem;">No general files yet.</div>
                                @endforelse
                            </div>

                            <div style="height: 1rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 1rem;"></div>
                            <h5 class="section-title"><i data-lucide="layers" style="width: 14px; color: #8b5cf6;"></i> Classifications</h5>

                            @foreach($categories as $cat => $icon)
                            @php
                                $catColors = [
                                    'APPOINTMENT' => ['#3b82f6', '#eff6ff'],
                                    'EDUCATION' => ['#10b981', '#ecfdf5'],
                                    'PERSONAL DATA SHEET' => ['#8b5cf6', '#f5f3ff'],
                                    'EXPERIENCE' => ['#f59e0b', '#fffbeb'],
                                    'CERTIFICATES FOR TRAINING' => ['#ec4899', '#fdf2f8'],
                                    'PERFORMANCE RATING' => ['#06b6d4', '#ecfeff'],
                                ];
                                $colors = $catColors[$cat] ?? ['#64748b', '#f8fafc'];
                            @endphp
                            <div class="doc-category-group" data-category="{{ $cat }}">
                                <div class="category-header" onclick="toggleCategory(this)" style="border-left: 4px solid {{ $colors[0] }};">
                                    <div class="category-title">
                                        <div class="cat-icon-box" style="background: {{ $colors[1] }}; color: {{ $colors[0] }};">
                                            <i data-lucide="{{ $icon }}"></i>
                                        </div>
                                        <span style="font-weight: 700;">{{ $cat }}</span>
                                        <span class="cat-count">({{ $classifiedDocs->has($cat) ? $classifiedDocs->get($cat)->count() : 0 }})</span>
                                    </div>
                                    <div class="category-actions">
                                        @if($employee->status === 'active')
                                        <button class="btn-upload-small" onclick="event.stopPropagation(); triggerCategoryUpload('{{ $cat }}')" title="Upload to {{ $cat }}">
                                            <i data-lucide="plus"></i>
                                        </button>
                                        @endif
                                        <i data-lucide="chevron-down" class="chevron-icon"></i>
                                    </div>
                                </div>
                                <div class="category-content" id="cat-{{ Str::slug($cat) }}">
                                    @if($classifiedDocs->has($cat))
                                        @foreach($classifiedDocs->get($cat) as $doc)
                                        <div class="mini-doc-item" id="doc-item-{{ $doc->id }}" 
                                             data-url="{{ asset($doc->file_path) }}" 
                                             data-name="{{ $doc->document_name }}" 
                                             onclick="previewDocById('{{ $doc->id }}', '{{ asset($doc->file_path) }}', '{{ $doc->document_name }}')">
                                            <div class="mini-doc-icon"><i data-lucide="file-text"></i></div>
                                            <div class="mini-doc-info">
                                                <span class="name">{{ $doc->document_name }}</span>
                                                <span class="date">Modified {{ $doc->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($employee->status === 'active')
                                            <form action="{{ route('employees.delete-doc', ['id' => $doc->id]) }}" method="POST" onclick="event.stopPropagation()">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="mini-btn-delete" title="Delete" onclick="return confirm('Delete this document?')">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="mini-empty" style="padding: 0.5rem; font-size: 0.75rem;">No files uploaded.</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- PDF Viewer -->
                    <div class="card-premium preview-mini-card">
                        <div class="preview-header-modern">
                            <div class="preview-title"><i data-lucide="monitor"></i> Document Viewer</div>
                            <div id="previewControls" class="preview-tools" style="display: none;">
                                <button onclick="prevDoc()" id="btnPrev" class="tool-btn"><i data-lucide="chevron-left"></i></button>
                                <span id="docCounter" class="tool-counter">0/0</span>
                                <button onclick="nextDoc()" id="btnNext" class="tool-btn"><i data-lucide="chevron-right"></i></button>
                                <div class="divider"></div>
                                <button onclick="printPreview()" class="tool-btn print"><i data-lucide="printer"></i></button>
                            </div>
                        </div>
                        
                        <div id="noPreview" class="mini-placeholder">
                            <div class="p-icon"><i data-lucide="file-search"></i></div>
                            <h3>Preview Area</h3>
                            <p>Select a document from the list to view its contents.</p>
                        </div>

                        <iframe id="pdfFrame" class="mini-iframe" style="display: none; height: 100%;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="workEditModal" class="modal-modern">
    <div class="modal-content-modern animate-scale-up">
        <div class="modal-header-modern">
            <h3>Update Work Details</h3>
            <button onclick="closeEditModal('work')"><i data-lucide="x"></i></button>
        </div>
        <form action="{{ route('employees.details-update', ['id' => $employee->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="last_name" value="{{ $employee->last_name }}">
            <input type="hidden" name="first_name" value="{{ $employee->first_name }}">
            <input type="hidden" name="middle_name" value="{{ $employee->middle_name }}">
            <div class="modal-body-modern">
                <div class="modal-input"><label>Position</label><input type="text" name="position" value="{{ $employee->position }}" required></div>
                <div class="modal-input"><label>Office</label><input type="text" name="department" value="{{ $employee->department }}" required></div>
                <div class="modal-input"><label>S.O Number</label><input type="text" name="so_number" value="{{ $employee->so_number }}"></div>
            </div>
            <div class="modal-footer-modern"><button type="button" class="btn-cancel" onclick="closeEditModal('work')">Cancel</button><button type="submit" class="btn-save">Save Changes</button></div>
        </form>
    </div>
</div>

<div id="personalEditModal" class="modal-modern">
    <div class="modal-content-modern animate-scale-up">
        <div class="modal-header-modern">
            <h3>Edit Personal Profile</h3>
            <button onclick="closeEditModal('personal')"><i data-lucide="x"></i></button>
        </div>
        <form action="{{ route('employees.details-update', ['id' => $employee->id]) }}" method="POST">
            @csrf
            <div class="modal-body-modern scrollable">
                <div class="grid-2">
                    <div class="modal-input"><label>First Name</label><input type="text" name="first_name" value="{{ $employee->first_name }}" required></div>
                    <div class="modal-input"><label>Last Name</label><input type="text" name="last_name" value="{{ $employee->last_name }}" required></div>
                </div>
                <div class="grid-2">
                    <div class="modal-input"><label>Middle Name</label><input type="text" name="middle_name" value="{{ $employee->middle_name }}"></div>
                    <div class="modal-input"><label>Date of Birth</label><input type="date" name="date_of_birth" value="{{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '' }}"></div>
                </div>
                <div class="grid-2">
                    <div class="modal-input">
                        <label>Sex</label>
                        <select name="sex">
                            <option value="Male" {{ $employee->sex == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $employee->sex == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="modal-input">
                        <label>Marital Status</label>
                        <select name="marital_status">
                            <option value="Single" {{ $employee->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $employee->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ $employee->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ $employee->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="modal-input"><label>Blood Type</label><input type="text" name="blood_type" value="{{ $employee->blood_type }}" placeholder="e.g. O+"></div>
                    <div class="modal-input"><label>Religion</label><input type="text" name="religion" value="{{ $employee->religion }}"></div>
                </div>
                <div class="modal-input"><label>Nationality</label><input type="text" name="nationality" value="{{ $employee->nationality ?: 'Filipino' }}"></div>
                <div class="grid-2">
                    <div class="modal-input"><label>Email</label><input type="email" name="email" value="{{ $employee->email }}"></div>
                    <div class="modal-input"><label>Phone</label><input type="text" name="phone" value="{{ $employee->phone }}"></div>
                </div>
                <div class="modal-input"><label>Address</label><textarea name="address" rows="2">{{ $employee->address }}</textarea></div>
                <div class="modal-input"><label>Emergency Contact</label><input type="text" name="emergency_contact" value="{{ $employee->emergency_contact }}"></div>
                <div class="modal-input"><label>Emergency Phone</label><input type="text" name="emergency_phone" value="{{ $employee->emergency_phone }}"></div>
                
                <input type="hidden" name="position" value="{{ $employee->position }}">
                <input type="hidden" name="department" value="{{ $employee->department }}">
            </div>
            <div class="modal-footer-modern"><button type="button" class="btn-cancel" onclick="closeEditModal('personal')">Cancel</button><button type="submit" class="btn-save">Save Profile</button></div>
        </form>
    </div>
</div>

<form id="importForm" method="POST" action="{{ route('employees.upload', ['id' => $employee->id]) }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="hidden" name="category" id="importCategory" value="GENERAL">
    <input type="file" name="documents[]" id="importFileInput" accept=".pdf,image/*,.docx,.doc,.xlsx" multiple onchange="this.form.submit()">
</form>

@push('styles')
<style>
    :root {
        --erp-bg: #f8fafc;
        --erp-primary: #3b82f6;
        --erp-card: #ffffff;
        --erp-text: #0f172a;
        --erp-muted: #64748b;
        --erp-border: #e2e8f0;
        --erp-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    }

    /* Animations */
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .animate-scale-up { animation: scaleUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scaleUp { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    @keyframes progress { from { stroke-dasharray: 0, 100; } }

    /* Layout System */
    .personal-grid-modern { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 991px) {
        .personal-grid-modern { grid-template-columns: 1fr; gap: 1.25rem; }
    }
    .info-content-area.full-width { width: 100%; position: relative; z-index: 10; }
    .docs-tab-layout { 
        display: grid; grid-template-columns: 350px 1fr; 
        gap: 1.5rem; min-height: 600px;
        transition: 0.3s; 
    }
    
    @media (max-width: 1200px) {
        .docs-tab-layout { grid-template-columns: 320px 1fr; gap: 1rem; }
    }

    @media (max-width: 991px) {
        .docs-tab-layout { 
            grid-template-columns: 1fr; 
            height: auto;
            gap: 1.5rem; 
        }
        .docs-mini-card { height: 500px; }
        .preview-mini-card { min-height: 500px; }
        .profile-header-content { flex-direction: column; text-align: center; padding: 1.5rem; margin-top: -60px; gap: 1rem; }
        .profile-avatar-circle { width: 100px; height: 100px; font-size: 2.5rem; border-width: 4px; }
        .employee-display-name { font-size: 1.5rem; }
        .profile-main-meta { padding-top: 0; }
        .employee-sub-meta { justify-content: center; gap: 0.75rem; }
        .profile-stats { padding-top: 20px; border-top: 1px solid #f1f5f9; width: 100%; margin-top: 1rem; }
        .profile-banner { height: 100px; }
        .info-grid-modern { grid-template-columns: 1fr; gap: 1rem; }
        .tab-btn { padding: 0.75rem 1rem; font-size: 0.8rem; }
    }

    .docs-mini-card { display: flex; flex-direction: column; height: 100%; overflow: hidden; background: white; border-radius: 16px; box-shadow: var(--erp-shadow); }
    .mini-docs-list { flex: 1; overflow-y: auto; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.8rem; scrollbar-width: thin; }
    .mini-card-header { flex-shrink: 0; padding: 1.25rem; border-bottom: 1px solid #f8fafc; }
    .mini-search { flex-shrink: 0; padding: 0.75rem 1.25rem; }


    /* Banner & Header */
    .profile-banner-container {
        position: relative;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: var(--erp-shadow);
        border: 1px solid var(--erp-border);
    }
    .profile-banner { 
        height: 180px; 
        position: relative; 
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%); 
        background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=1350&q=80'); 
        background-size: cover; 
        background-position: center; 
    }
    .banner-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.4)); }
    
    .banner-navigation {
        position: absolute;
        top: 0; left: 0; right: 0;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }

    .banner-back-btn { 
        width: 40px; height: 40px; 
        background: rgba(255,255,255,0.2); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.3); 
        border-radius: 12px; 
        display: flex; align-items: center; justify-content: center; 
        color: white; transition: all 0.3s; 
    }
    .banner-back-btn:hover { background: white; color: var(--erp-primary); transform: translateX(-3px); }

    .status-badge { 
        display: flex; align-items: center; gap: 0.5rem; 
        padding: 0.5rem 1rem; 
        background: rgba(255,255,255,0.2); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.3); 
        border-radius: 100px; 
        color: white; 
        font-size: 0.7rem; font-weight: 800; 
        letter-spacing: 0.05em;
    }
    .status-active .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px #10b981; }

    .profile-header-content { 
        display: flex; 
        align-items: center; 
        padding: 0 2.5rem 1.5rem; 
        margin-top: -50px; 
        gap: 2.5rem; 
        position: relative;
        flex-wrap: wrap;
    }

    .profile-avatar-wrapper { position: relative; z-index: 5; }
    .profile-avatar-circle { 
        width: 140px; height: 140px; 
        border-radius: 50%; 
        background: var(--erp-primary); 
        color: white; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 3.5rem; font-weight: 800; 
        border: 6px solid white; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        font-family: 'Outfit', sans-serif;
    }

    .avatar-camera-btn { 
        position: absolute; 
        bottom: 5px; right: 5px; 
        width: 38px; height: 38px; 
        background: white; 
        border: none; border-radius: 50%; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        color: var(--erp-muted); cursor: pointer; 
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s; 
    }
    .avatar-camera-btn:hover { color: var(--erp-primary); transform: scale(1.1); }

    .profile-main-meta { flex: 1; padding-top: 50px; }
    .employee-display-name { 
        font-size: 2rem; 
        font-weight: 800; 
        color: var(--erp-text); 
        margin-bottom: 0.5rem; 
        font-family: 'Outfit', sans-serif; 
    }
    .employee-sub-meta { display: flex; flex-wrap: wrap; gap: 1.5rem; }
    .meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--erp-muted); font-weight: 600; }
    .meta-item i { width: 14px; color: var(--erp-primary); opacity: 0.7; }

    .profile-stats { padding-top: 50px; }
    .profile-progress-circle { display: flex; align-items: center; gap: 1rem; }
    .circular-chart { width: 50px; height: 50px; }
    .circle-bg { fill: none; stroke: #f1f5f9; stroke-width: 3.5; }
    .circle { fill: none; stroke-width: 3.5; stroke-linecap: round; stroke: var(--erp-primary); animation: progress 1s ease-out forwards; }
    .percentage { fill: var(--erp-text); font-family: 'Outfit', sans-serif; font-size: 0.65rem; font-weight: 800; text-anchor: middle; }
    .progress-label { font-size: 0.75rem; font-weight: 700; color: var(--erp-muted); white-space: nowrap; }

    /* Tabs Bar */
    .profile-tabs-nav { 
        display: flex; gap: 0.5rem; 
        margin-bottom: 1.5rem; 
        border-bottom: 2px solid #f1f5f9; 
        padding: 0 0.5rem;
        position: relative;
        z-index: 20;
        overflow-x: auto;
        scrollbar-width: none; /* Hide scrollbar for Firefox */
        -ms-overflow-style: none; /* Hide scrollbar for IE/Edge */
    }
    .profile-tabs-nav::-webkit-scrollbar { display: none; } /* Hide scrollbar for Chrome/Safari */

    .tab-btn { 
        background: none; border: none; 
        padding: 0.85rem 1.5rem; 
        font-size: 0.9rem; font-weight: 700; 
        color: var(--erp-muted); cursor: pointer; 
        position: relative; transition: 0.3s; 
        border-radius: 10px 10px 0 0;
        display: flex; align-items: center; gap: 0.6rem;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tab-btn i { width: 16px; height: 16px; }
    .tab-btn:hover { color: var(--erp-primary); background: #f8fafc; }
    .tab-btn.active { color: var(--erp-primary); }
    .tab-btn.active::after { 
        content: ''; position: absolute; bottom: -2px; left: 0; right: 0; 
        height: 3px; background: var(--erp-primary); border-radius: 10px; 
    }

    /* Card Systems */
    .card-premium { 
        background: white; border-radius: 16px; 
        box-shadow: var(--erp-shadow); 
        border: 1px solid var(--erp-border); 
        overflow: hidden; 
    }
    .card-header-modern { 
        display: flex; justify-content: space-between; align-items: center; 
        padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; 
        background: #fafbff; 
    }
    .card-header-modern h3 { 
        font-size: 1rem; font-weight: 800; 
        color: var(--erp-text); margin: 0; 
        font-family: 'Outfit', sans-serif; 
        display: flex; align-items: center; gap: 0.6rem; 
    }
    .btn-edit-icon { 
        width: 34px; height: 34px; border-radius: 10px; 
        border: 1px solid #eef2f6; background: white; 
        color: var(--erp-muted); cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-edit-icon:hover { background: var(--erp-primary); color: white; transform: translateY(-2px); }

    .info-grid-modern { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem; }
    .info-group label { 
        font-size: 0.75rem; font-weight: 800; 
        color: var(--erp-muted); text-transform: uppercase; 
        letter-spacing: 0.05em; margin-bottom: 0.5rem; 
        display: flex; align-items: center; gap: 0.5rem;
    }
    .info-group label i { width: 16px; height: 16px; opacity: 0.8; }
    .info-group span { font-size: 1rem; font-weight: 700; color: var(--erp-text); }
    .blood-badge { background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 800; font-size: 0.75rem; }
    .emp-id-badge { color: var(--erp-primary); font-weight: 800; font-family: monospace; }

    .info-tab-pane { display: none; }
    .info-tab-pane.active { display: block; animation: fadeIn 0.4s ease-out; }

    /* Documents Tab Specifics */
    .mini-card-header { padding: 1.25rem; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; }
    .mini-card-header h4 { font-size: 0.95rem; font-weight: 800; margin: 0; font-family: 'Outfit', sans-serif; }
    .mini-card-header p { font-size: 0.7rem; color: var(--erp-muted); margin: 2px 0 0; }
    .btn-mini-import { 
        background: var(--erp-primary); color: white; 
        padding: 0.5rem 1rem; border-radius: 10px; 
        border: none; font-weight: 700; font-size: 0.75rem; 
        display: flex; align-items: center; gap: 0.4rem; cursor: pointer; 
    }

    .mini-search { padding: 0.75rem 1.25rem; position: relative; background: #fdfdff; display: flex; align-items: center; }
    .mini-search i { position: absolute; left: 1.85rem; top: 50%; transform: translateY(-50%); width: 14px; color: var(--erp-muted); pointer-events: none; }
    .mini-search input { 
        width: 100%; padding: 0.7rem 1rem 0.7rem 2.5rem; 
        border: 1px solid #eef2f6; border-radius: 12px; 
        font-size: 0.85rem; outline: none; transition: 0.2s;
    }
    .mini-search input:focus { border-color: var(--erp-primary); background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }

    .mini-docs-list { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.6rem; }
    .mini-doc-item { 
        display: flex; align-items: center; gap: 0.75rem; 
        padding: 0.85rem; border-radius: 12px; 
        background: #f8fafc; border: 1px solid transparent; 
        cursor: pointer; transition: 0.2s; 
    }
    .mini-doc-item:hover { background: white; border-color: #eef2f6; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .mini-doc-item.active { background: #f0f7ff; border-color: var(--erp-primary); }
    .mini-doc-item .mini-btn-delete { margin-left: auto; flex-shrink: 0; }

    /* docx-preview styling */
    #docx-container {
        display: flex; justify-content: center;
        background: #f1f5f9;
        min-height: 100%;
    }
    #docx-container .docx-wrapper {
        background: transparent;
        padding: 2rem 0;
        width: 100%;
        max-width: 900px;
    }
    #docx-container .docx {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin: 0 auto;
        background: white;
    }

    
    .mini-doc-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: var(--erp-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mini-doc-info { flex: 1; overflow: hidden; }
    .mini-doc-info .name { font-size: 0.8rem; font-weight: 700; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mini-doc-info .date { font-size: 0.65rem; color: var(--erp-muted); }

    .cat-icon-box { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; }
    .category-header { 
        display: flex; justify-content: space-between; align-items: center; 
        padding: 0.75rem 1rem; background: #fff; border: 1px solid #f1f5f9; 
        border-radius: 12px; cursor: pointer; transition: 0.2s; 
        margin-bottom: 0.5rem;
    }
    .category-header:hover { background: #f8fafc; }
    .category-header.active { background: #fdfdff; border-bottom-left-radius: 0; border-bottom-right-radius: 0; margin-bottom: 0; }
    .category-title { display: flex; align-items: center; }
    .category-content { 
        display: none; padding: 1rem; background: #fafbff; 
        border: 1px solid #f1f5f9; border-top: none; 
        border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;
        margin-bottom: 0.75rem;
    }
    .category-content.active { display: flex; flex-direction: column; gap: 0.5rem; }

    .mini-btn-delete { color: #cbd5e1; background: none; border: none; padding: 0.4rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
    .mini-btn-delete:hover { color: #ef4444; background: #fee2e2; border-radius: 8px; }


    .preview-header-modern { 
        padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; 
        display: flex; justify-content: space-between; align-items: center; 
        background: #fafbff; 
    }
    .preview-title { font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 0.6rem; font-family: 'Outfit', sans-serif; }
    .preview-tools { display: flex; align-items: center; gap: 0.4rem; }
    .tool-btn { 
        width: 32px; height: 32px; border-radius: 8px; 
        border: none; background: transparent; 
        color: var(--erp-muted); cursor: pointer; 
        display: flex; align-items: center; justify-content: center; 
    }
    .tool-btn:hover:not(:disabled) { color: var(--erp-primary); background: #f0f7ff; }
    .tool-btn:disabled { opacity: 0.3; cursor: not-allowed; }
    .tool-counter { font-size: 0.75rem; font-weight: 700; padding: 0 0.5rem; }
    
    .preview-mini-card { height: 100%; display: flex; flex-direction: column; }
    .mini-placeholder { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: var(--erp-muted); background: #fafbff; }
    .mini-placeholder .p-icon { font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem; }
    .mini-placeholder h3 { font-size: 1.1rem; margin: 0 0 0.5rem; font-family: 'Outfit', sans-serif; }
    .mini-placeholder p { font-size: 0.85rem; margin: 0; }
    .mini-iframe { flex: 1; border: none; width: 100%; }

    /* Modals */
    .modal-modern { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-modern.active { display: flex; }
    .modal-content-modern { background: white; border-radius: 20px; width: 100%; max-width: 550px; box-shadow: 0 25px 60px rgba(0,0,0,0.2); overflow: hidden; }
    .modal-header-modern { padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .modal-header-modern button { background: none; border: none; padding: 0.5rem; color: var(--erp-muted); cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .modal-header-modern button:hover { color: #ef4444; }
    .modal-body-modern { padding: 2rem; }
    .modal-body-modern.scrollable { max-height: 60vh; overflow-y: auto; }
    .modal-input { margin-bottom: 1.25rem; }
    .modal-input label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--erp-muted); text-transform: uppercase; margin-bottom: 0.4rem; }
    .modal-input input, .modal-input select, .modal-input textarea { width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; font-size: 0.9rem; font-weight: 600; outline: none; transition: 0.2s; }
    .modal-input input:focus { border-color: var(--erp-primary); background: white; }
    .modal-footer-modern { 
        padding: 1.5rem 2rem; 
        background: #f8fafc; 
        display: flex; 
        justify-content: flex-end; 
        gap: 1rem; 
        border-top: 1px solid #f1f5f9;
    }
    .btn-cancel { 
        background: white; 
        color: var(--erp-muted); 
        padding: 0.75rem 1.5rem; 
        border-radius: 12px; 
        border: 1px solid #e2e8f0; 
        font-weight: 700; 
        cursor: pointer; 
        transition: 0.2s;
    }
    .btn-cancel:hover { background: #f1f5f9; color: var(--erp-text); }
    .btn-save { 
        background: var(--erp-primary); 
        color: white; 
        padding: 0.75rem 2rem; 
        border-radius: 12px; 
        border: none; 
        font-weight: 700; 
        cursor: pointer; 
        box-shadow: 0 4px 12px rgba(59,130,246,0.25); 
        transition: 0.2s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59,130,246,0.35); }

    /* Toast Notification */
    .toast-modern {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 9999;
        background: #0f172a;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .toast-content { display: flex; align-items: center; gap: 0.75rem; }
    /* Categorized Docs Styling */
    .doc-category-group { margin-bottom: 0.75rem; border-radius: 12px; background: white; border: 1px solid #f1f5f9; overflow: hidden; transition: 0.3s; }
    .doc-category-group:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .category-header { 
        padding: 0.85rem 1rem; background: #f8fafc; display: flex; 
        justify-content: space-between; align-items: center; cursor: pointer; transition: 0.2s;
    }
    .category-header:hover { background: #f0f7ff; }
    .category-title { display: flex; align-items: center; gap: 0.6rem; color: #1e293b; }
    .category-title i { width: 16px; color: var(--erp-primary); }
    .category-title span { font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.02em; }
    .cat-count { font-size: 0.65rem; color: var(--erp-muted); font-weight: 600; }
    .category-actions { display: flex; align-items: center; gap: 0.5rem; }
    .btn-upload-small { 
        width: 26px; height: 26px; border-radius: 8px; border: none; 
        background: var(--erp-primary); color: white; display: flex; 
        align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;
    }
    .btn-upload-small:hover { transform: scale(1.1); background: #2563eb; }
    .category-content { display: none; padding: 0.5rem; border-top: 1px solid #f1f5f9; background: white; }
    .category-content.active { display: block; animation: slideDown 0.3s ease-out; }
    .chevron-icon { transition: transform 0.3s; width: 14px; color: var(--erp-muted); }
    .category-header.active .chevron-icon { transform: rotate(180deg); }
    
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .mini-btn-delete { color: #f87171; background: none; border: none; padding: 0.4rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
    .mini-btn-delete:hover { color: #991b1b; background: #fee2e2; border-radius: 8px; }

    .badge-status-green { background: #ecfdf5; color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 800; font-size: 0.75rem; border: 1px solid #d1fae5; }

    .section-title { font-size: 0.7rem; font-weight: 800; color: var(--erp-muted); text-transform: uppercase; letter-spacing: 0.05em; margin: 1.25rem 0 0.75rem; }
    
    /* Ensure nothing blocks clicks */
    .sidebar-overlay { pointer-events: none; }
    .modal-modern { pointer-events: none; }
    .modal-modern.active { pointer-events: auto; }
    .modal-content-modern { pointer-events: auto; }

    .general-docs-section { display: flex; flex-direction: column; gap: 0.5rem; }



</style>
@endpush

@push('scripts')
<script>
    let currentIndex = -1;
    let allDocuments = [];
    let documentsList = [];

    document.addEventListener('DOMContentLoaded', () => {
        // Handle tab persistence from URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            const tabBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn => 
                btn.getAttribute('onclick')?.includes(`'${activeTab}'`)
            );
            if (tabBtn) {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.info-tab-pane').forEach(p => p.classList.remove('active'));
                
                tabBtn.classList.add('active');
                const pane = document.getElementById(activeTab + 'Tab');
                if (pane) pane.classList.add('active');
            }
        }

        if(typeof lucide !== 'undefined') lucide.createIcons();

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            const docsTab = document.getElementById('documentsTab');
            if (docsTab && docsTab.classList.contains('active')) {
                if (e.key === 'ArrowRight') { e.preventDefault(); nextDoc(); }
                if (e.key === 'ArrowLeft') { e.preventDefault(); prevDoc(); }
            }
        });

        // Initialize documents list for keys
        refreshDocList();
    });

    function refreshDocList() {
        const items = Array.from(document.querySelectorAll('.mini-doc-item:not([style*="display: none"])'));
        documentsList = items.map(item => ({
            id: item.id.replace('doc-item-', ''),
            url: item.dataset.url,
            name: item.dataset.name
        }));
    }

    function switchTab(btn, tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.info-tab-pane').forEach(pane => pane.classList.remove('active'));
        
        btn.classList.add('active');
        const pane = document.getElementById(tabId + 'Tab');
        if (pane) pane.classList.add('active');

        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState({}, '', url);
    }

        // For Overhauled Documents Tab
    function toggleCategory(header) {
        const group = header.parentElement;
        const content = group.querySelector('.category-content');
        header.classList.toggle('active');
        content.classList.toggle('active');
    }

    function triggerCategoryUpload(cat) {
        document.getElementById('importCategory').value = cat;
        document.getElementById('importFileInput').click();
    }

    function triggerImport() {
        document.getElementById('importCategory').value = 'GENERAL';
        document.getElementById('importFileInput').click();
    }

    function previewDocById(id, url, name) {
        const frame = document.getElementById('pdfFrame');
        const noPreview = document.getElementById('noPreview');
        const controls = document.getElementById('previewControls');
        
        document.querySelectorAll('.mini-doc-item').forEach(item => item.classList.remove('active'));
        const activeItem = document.getElementById('doc-item-' + id);
        if (activeItem) activeItem.classList.add('active');

        // Viewer logic for images vs pdfs vs docs
        const extension = url.split('.').pop().toLowerCase();
        
        // Reset
        noPreview.innerHTML = '';
        noPreview.style.display = 'none';
        frame.style.display = 'none';
        frame.src = '';

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            noPreview.innerHTML = `<img src="${url}" style="max-width: 90%; max-height: 90%; object-fit: contain; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border-radius: 12px; border: 4px solid white;">`;
            noPreview.style.display = 'flex';
        } else if (['docx'].includes(extension)) {
            // Local viewing for .docx
            noPreview.style.display = 'flex';
            noPreview.innerHTML = '<div id="docx-container" style="width: 100%; height: 100%; overflow: auto; padding: 2rem; background: #f1f5f9;"></div>';
            fetch(url)
                .then(res => res.blob())
                .then(blob => {
                    docx.renderAsync(blob, document.getElementById("docx-container"))
                        .then(x => console.log("docx: finished"));
                });
        } else if (['pdf'].includes(extension)) {
            frame.src = url;
            frame.style.display = 'block';
        } else if (['doc', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
            // Attempt to use Google Docs Viewer
            noPreview.style.display = 'flex';
            noPreview.innerHTML = `
                <div style="text-align: center; width: 100%; height: 100%; display: flex; flex-direction: column;">
                    <div style="padding: 1rem; background: #f8fafc; border-bottom: 1px solid #eef2f6; display: flex; justify-content: center; gap: 1rem;">
                         <button onclick="window.open('https://docs.google.com/viewer?url=${encodeURIComponent(url)}', '_blank')" class="btn-save" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;">
                            <i data-lucide="external-link"></i> Open in Google Viewer
                         </button>
                         <a href="${url}" download class="btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 0.3rem;">
                            <i data-lucide="download"></i> Download
                         </a>
                    </div>
                    <iframe src="https://docs.google.com/viewer?url=${encodeURIComponent(url)}&embedded=true" style="flex: 1; width: 100%; border: none;"></iframe>
                </div>
            `;
            if(typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            // Fallback for other formats
            let icon = 'file-text';
            if(['xlsx', 'xls'].includes(extension)) icon = 'file-spreadsheet';
            
            noPreview.style.display = 'flex';
            noPreview.innerHTML = `
                <div style="text-align: center; padding: 3rem;">
                    <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #3b82f6;">
                        <i data-lucide="${icon}" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem; font-family: 'Outfit';">Preview not available for .${extension}</h3>
                    <p style="color: #64748b; margin-bottom: 2rem;">Please download the file to view it on your device.</p>
                    <a href="${url}" download class="btn-save" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                        <i data-lucide="download"></i> Download & Open File
                    </a>
                </div>
            `;
            if(typeof lucide !== 'undefined') lucide.createIcons();
        }
        
        controls.style.display = 'flex';
        
        // Local list update for prev/next
        const items = Array.from(document.querySelectorAll('.mini-doc-item:not([style*="display: none"])'));
        documentsList = items.map(item => ({
            id: item.id.replace('doc-item-', ''),
            url: item.dataset.url,
            name: item.dataset.name
        }));
        currentIndex = documentsList.findIndex(d => d.url === url);
        updateControls();
    }

    function filterDocuments() {
        const query = document.getElementById('docSearch').value.toLowerCase();
        document.querySelectorAll('.mini-doc-item').forEach(item => {
            const matches = item.dataset.name.toLowerCase().includes(query);
            item.style.display = matches ? 'flex' : 'none';
        });
        
        document.querySelectorAll('.doc-category-group').forEach(group => {
            const items = group.querySelectorAll('.mini-doc-item');
            let hasVisible = false;
            items.forEach(i => { if(i.style.display !== 'none') hasVisible = true; });
            
            const content = group.querySelector('.category-content');
            const header = group.querySelector('.category-header');
            const chevron = header.querySelector('.chevron-icon');

            if (hasVisible && query !== '') {
                group.style.display = 'block';
                content.classList.add('active');
                header.classList.add('active');
            } else if (query === '') {
                group.style.display = 'block';
                // Reset categories to collapsed state on clear search if you want
            } else {
                group.style.display = 'none';
            }
        });
        refreshDocList();
        updateControls();
    }

    function updateControls() {
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const counter = document.getElementById('docCounter');
        if (!btnPrev || !btnNext || !counter) return;
        btnPrev.disabled = currentIndex <= 0;
        btnNext.disabled = currentIndex >= documentsList.length - 1 || currentIndex === -1;
        counter.textContent = documentsList.length === 0 ? '0/0' : `${currentIndex + 1}/${documentsList.length}`;
    }

    function nextDoc() { if (currentIndex < documentsList.length - 1) { const d = documentsList[currentIndex+1]; previewDocById(d.id, d.url, d.name); } }
    function prevDoc() { if (currentIndex > 0) { const d = documentsList[currentIndex-1]; previewDocById(d.id, d.url, d.name); } }

    function printPreview() {
        const frame = document.getElementById('pdfFrame');
        if (frame.src) frame.contentWindow.print();
    }

    function openEditModal(type) { document.getElementById(type + 'EditModal').classList.add('active'); }
    function closeEditModal(type) { document.getElementById(type + 'EditModal').classList.remove('active'); }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-modern')) event.target.classList.remove('active');
    }
</script>
@endpush
@endsection