@extends('layouts.app')

@section('title', $employee->name . ' - Profile')

@section('content')
<div class="page-content" style="padding: 1.5rem; max-width: 1600px; margin: 0 auto;">
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

            <div class="profile-stats">
                <div class="profile-progress-circle">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle" stroke-dasharray="85, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <text x="18" y="20.35" class="percentage">85%</text>
                    </svg>
                    <span class="progress-label">Profile Strength</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="profile-tabs-nav animate-slide-up" style="animation-delay: 0.1s;">
        <button class="tab-btn active" onclick="switchTab('personal')">Personal & Contact</button>
        <button class="tab-btn" onclick="switchTab('work')">Work Info</button>
        <button class="tab-btn" onclick="switchTab('documents')">Documents & Viewer</button>
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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Identity Card -->
                    <div class="card-premium info-card">
                        <div class="card-header-modern">
                            <h3>Identity Details</h3>
                            <button class="btn-edit-icon" onclick="openEditModal('personal')">
                                <i data-lucide="pencil"></i>
                            </button>
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group"><label>Full Name</label><span>{{ $employee->name }}</span></div>
                            <div class="info-group"><label>Date of Birth</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '--' }}</span></div>
                            <div class="info-group"><label>Age</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->age . ' years' : '--' }}</span></div>
                            <div class="info-group"><label>Gender</label><span>{{ $employee->sex ?: '--' }}</span></div>
                            <div class="info-group"><label>Marital Status</label><span>{{ $employee->marital_status ?: 'Single' }}</span></div>
                            <div class="info-group"><label>Nationality</label><span>{{ $employee->nationality ?: 'Filipino' }}</span></div>
                            <div class="info-group"><label>Religion</label><span>{{ $employee->religion ?: '--' }}</span></div>
                            <div class="info-group"><label>Blood Type</label><span class="blood-badge">{{ $employee->blood_type ?: '--' }}</span></div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="card-premium info-card">
                        <div class="card-header-modern">
                            <h3>Contact Information</h3>
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group full-width"><label><i data-lucide="mail"></i> Email Address</label><span>{{ $employee->email ?: '--' }}</span></div>
                            <div class="info-group full-width"><label><i data-lucide="phone"></i> Phone Number</label><span>{{ $employee->phone ?: '--' }}</span></div>
                            <div class="info-group full-width"><label><i data-lucide="home"></i> Home Address</label><span class="address-text">{{ $employee->address ?: '--' }}</span></div>
                        </div>
                    </div>

                    <!-- Emergency Card -->
                    <div class="card-premium info-card" style="grid-column: span 2;">
                        <div class="card-header-modern">
                            <h3 style="color: #ef4444;"><i data-lucide="alert-circle"></i> Emergency Contact</h3>
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group"><label>Contact Person</label><span>{{ $employee->emergency_contact ?: '--' }}</span></div>
                            <div class="info-group"><label>Contact Number</label><span>{{ $employee->emergency_phone ?: '--' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Info Tab -->
            <div id="workTab" class="info-tab-pane">
                <div class="card-premium info-card" style="width: 100%; margin: 0 auto;">
                    <div class="card-header-modern">
                        <h3>Employment Information</h3>
                        <button class="btn-edit-icon" onclick="openEditModal('work')">
                            <i data-lucide="pencil"></i>
                        </button>
                    </div>
                    <div class="info-grid-modern">
                        <div class="info-group"><label>Employee ID</label><span class="emp-id-badge">{{ $employee->id }}</span></div>
                        <div class="info-group"><label>Position</label><span>{{ $employee->position }}</span></div>
                        <div class="info-group"><label>Department</label><span>{{ $employee->department }}</span></div>
                        <div class="info-group"><label>Box Number</label><span>{{ $employee->box_number ?: '--' }}</span></div>
                        <div class="info-group"><label>Date Joined</label><span>{{ $employee->date_joined ? $employee->date_joined->format('M d, Y') : '--' }}</span></div>
                        <div class="info-group">
                            <label>Employment Status</label>
                            <span class="text-{{ $employee->status == 'active' ? 'success' : 'danger' }}" style="font-weight: 800;">{{ strtoupper($employee->status) }}</span>
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
                            @forelse($documents as $index => $doc)
                            <div class="mini-doc-item" id="doc-item-{{ $index }}" 
                                 data-index="{{ $index }}" data-url="{{ asset($doc->file_path) }}" 
                                 data-name="{{ $doc->document_name }}" onclick="previewFileByIndex({{ $index }})">
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
                                <i data-lucide="chevron-right" style="width: 14px; opacity: 0.3;"></i>
                            </div>
                            @empty
                            <div class="mini-empty">No scanned documents yet.</div>
                            @endforelse
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
                <div class="modal-input"><label>Department</label><input type="text" name="department" value="{{ $employee->department }}" required></div>
                <div class="modal-input"><label>Box Number</label><input type="text" name="box_number" value="{{ $employee->box_number }}"></div>
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
    <input type="hidden" name="category" value="GENERAL">
    <input type="file" name="documents[]" id="importFileInput" accept=".pdf" multiple onchange="this.form.submit()">
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
    .info-content-area.full-width { width: 100%; }
    .docs-tab-layout { display: grid; grid-template-columns: 350px 1fr; gap: 1.5rem; height: 850px; }

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
        gap: 2rem; 
        position: relative;
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
    }
    .tab-btn { 
        background: none; border: none; 
        padding: 0.85rem 1.5rem; 
        font-size: 0.9rem; font-weight: 700; 
        color: var(--erp-muted); cursor: pointer; 
        position: relative; transition: 0.3s; 
        border-radius: 10px 10px 0 0;
    }
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
        font-size: 0.7rem; font-weight: 800; 
        color: var(--erp-muted); text-transform: uppercase; 
        letter-spacing: 0.05em; margin-bottom: 0.4rem; 
        display: flex; align-items: center; gap: 0.4rem;
    }
    .info-group span { font-size: 0.95rem; font-weight: 700; color: var(--erp-text); }
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

    .mini-search { padding: 0.75rem 1.25rem; position: relative; background: #fdfdff; }
    .mini-search i { position: absolute; left: 1.8rem; top: 50%; transform: translateY(-50%); width: 14px; color: var(--erp-muted); }
    .mini-search input { 
        width: 100%; padding: 0.6rem 1rem 0.6rem 2.2rem; 
        border: 1px solid #eef2f6; border-radius: 10px; 
        font-size: 0.8rem; outline: none; 
    }

    .mini-docs-list { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.6rem; }
    .mini-doc-item { 
        display: flex; align-items: center; gap: 0.75rem; 
        padding: 0.85rem; border-radius: 12px; 
        background: #f8fafc; border: 1px solid transparent; 
        cursor: pointer; transition: 0.2s; 
    }
    .mini-doc-item:hover { background: white; border-color: #eef2f6; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .mini-doc-item.active { background: #f0f7ff; border-color: var(--erp-primary); }
    .mini-doc-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: var(--erp-primary); display: flex; align-items: center; justify-content: center; }
    .mini-doc-info .name { font-size: 0.8rem; font-weight: 700; display: block; overflow: hidden; text-overflow: ellipsis; }
    .mini-doc-info .date { font-size: 0.65rem; color: var(--erp-muted); }
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
    .toast-icon { color: #10b981; width: 20px; }
    .toast-modern span { font-size: 0.9rem; font-weight: 600; }

</style>
@endpush

@push('scripts')
<script>
    let currentIndex = -1;
    let allDocuments = [];
    let documentsList = [];

    document.addEventListener('DOMContentLoaded', () => {
        const items = document.querySelectorAll('.mini-doc-item');
        allDocuments = Array.from(items).map(item => ({
            url: item.dataset.url,
            name: item.dataset.name,
            originalIndex: parseInt(item.dataset.index)
        }));
        documentsList = [...allDocuments];
        updateControls();
        if(typeof lucide !== 'undefined') lucide.createIcons();

        // Auto-hide toast
        const toast = document.getElementById('successToast');
        if(toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                toast.style.transition = 'all 0.5s ease-out';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        // Keyboard Navigation for PDF
        document.addEventListener('keydown', (e) => {
            if (document.getElementById('documentsTab').classList.contains('active')) {
                if (e.key === 'ArrowRight') nextDoc();
                if (e.key === 'ArrowLeft') prevDoc();
            }
        });
    });

    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.info-tab-pane').forEach(pane => pane.classList.remove('active'));
        
        event.currentTarget.classList.add('active');
        document.getElementById(tabId + 'Tab').classList.add('active');
        
        // Auto-preview first document if switching to documents tab
        if(tabId === 'documents' && documentsList.length > 0 && currentIndex === -1) {
            previewFileByIndex(0);
        }
    }

    function triggerImport() { document.getElementById('importFileInput').click(); }

    function filterDocuments() {
        const query = document.getElementById('docSearch').value.toLowerCase();
        allDocuments.forEach(doc => {
            const item = document.getElementById('doc-item-' + doc.originalIndex);
            item.style.display = doc.name.toLowerCase().includes(query) ? 'flex' : 'none';
        });
        documentsList = allDocuments.filter(doc => doc.name.toLowerCase().includes(query));
        if (documentsList.length === 0) {
            document.getElementById('noPreview').style.display = 'flex';
            document.getElementById('pdfFrame').style.display = 'none';
            document.getElementById('previewControls').style.display = 'none';
        } else {
            updateControls();
        }
    }

    function previewFileByIndex(index) {
        if (index < 0 || index >= documentsList.length) return;
        currentIndex = index;
        const doc = documentsList[index];
        const frame = document.getElementById('pdfFrame');
        const noPreview = document.getElementById('noPreview');
        const controls = document.getElementById('previewControls');
        
        document.querySelectorAll('.mini-doc-item').forEach(item => item.classList.remove('active'));
        const activeItem = document.getElementById('doc-item-' + doc.originalIndex);
        if (activeItem) {
            activeItem.classList.add('active');
            activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        frame.src = doc.url;
        frame.style.display = 'block';
        noPreview.style.display = 'none';
        controls.style.display = 'flex';
        updateControls();
    }

    function nextDoc() { if (currentIndex < documentsList.length - 1) previewFileByIndex(currentIndex + 1); }
    function prevDoc() { if (currentIndex > 0) previewFileByIndex(currentIndex - 1); }

    function updateControls() {
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const counter = document.getElementById('docCounter');
        btnPrev.disabled = currentIndex <= 0;
        btnNext.disabled = currentIndex >= documentsList.length - 1 || currentIndex === -1;
        counter.textContent = documentsList.length === 0 ? '0/0' : `${currentIndex + 1}/${documentsList.length}`;
    }

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