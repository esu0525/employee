@extends('layouts.app')

@section('title', $employee->last_name . ', ' . $employee->first_name)

@push('styles')
    <!-- docx-preview for local .docx viewing -->
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@section('content')
    <div class="page-content" style="padding: 1rem 10px; width: 100%; margin: 0;">
        <!-- Modern Header & Banner -->
        <div class="profile-banner-container animate-fade-in">
            <div class="profile-banner">
                <div class="banner-overlay"></div>

                <div class="banner-navigation">
                    @php
                        $isArchived = in_array($employee->status, ['resign', 'retired', 'transfer', 'others']);

                        // Fetch Current User Permissions
                        $currentUser = \App\Models\User::find(session('auth_user_id'));
                        $userPerms = $currentUser ? ($currentUser->permissions ?? []) : [];
                        $isAdmin = $currentUser && $currentUser->role === 'admin';

                        $canEdit = $currentUser && ($isArchived ? $currentUser->hasPermission('edit_archive') : $currentUser->hasPermission('edit_masterlist'));
                        $canManageDocs = $currentUser && $currentUser->hasPermission('manage_documents');
                        $canDelete = $currentUser && ($isArchived ? $currentUser->hasPermission('edit_archive') : $currentUser->hasPermission('edit_masterlist'));

                        // Construct Full Name with FULL Middle Name for Detail View
                        $fullNameDisplay = $employee->last_name . ', ' . $employee->first_name;
                        if ($employee->middle_name) {
                            $fullNameDisplay .= ' ' . $employee->middle_name;
                        }
                        if ($employee->suffix) {
                            $fullNameDisplay .= ' ' . $employee->suffix;
                        }
                    @endphp
                    <a href="{{ $isArchived ? route('employees.archive') : route('employees.masterlist') }}"
                        class="banner-back-btn" 
                        onclick="const key = '{{ $isArchived ? 'archiveLastUrl' : 'masterlistLastUrl' }}'; const lastUrl = localStorage.getItem(key); if(lastUrl) { event.preventDefault(); window.location.href = lastUrl; }">
                        <i data-lucide="{{ $isArchived ? 'rotate-ccw' : 'arrow-left' }}"></i>
                    </a>

                    <div class="status-badge status-{{ $employee->status }}">
                        <span class="status-dot"></span>
                        {{ strtoupper($employee->status) }}
                    </div>
                </div>
            </div>

            <div class="profile-header-content">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar-3layer" id="avatarDisplay" onclick="viewPhoto()" style="cursor: pointer;">
                        <div class="avatar-ring-outer">
                            <div class="avatar-ring-inner">
                                <div class="avatar-photo-box">
                                    @if($employee->profile_picture)
                                        <img src="{{ asset($employee->profile_picture) }}" alt="Profile" id="mainAvatarImage"
                                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    @else
                                        <div class="avatar-initials">
                                            {{ strtoupper(substr($employee->first_name ?? $employee->name, 0, 1)) }}{{ strtoupper(substr($employee->last_name ?? '', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($canEdit)
                        <button class="avatar-camera-btn" onclick="document.getElementById('avatarInput').click()"
                            title="Change Photo">
                            <i data-lucide="camera" style="width: 18px;"></i>
                        </button>
                    @endif
                    <form id="avatarForm" action="{{ route('employees.update-avatar', ['id' => $employee->id]) }}"
                        method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="hidden" name="cropped_image" id="croppedImageData">
                        <input type="file" name="profile_picture" id="avatarInput" accept="image/*"
                            onchange="initCropper(this)">
                    </form>
                </div>

                <div class="profile-main-meta">
                    <h1 class="employee-display-name">{{ $fullNameDisplay }}</h1>
                    <div class="employee-sub-meta">
                        <span class="meta-item"><i data-lucide="briefcase"></i> {{ $employee->position }}</span>
                        <span class="meta-item"><i data-lucide="building"></i> {{ $employee->agency }}</span>
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
                                @if($canEdit)
                                    <button class="btn-edit-icon" onclick="openEditModal('personal')">
                                        <i data-lucide="pencil"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="info-grid-modern">
                                <div class="info-group"><label>Full Name</label><span>{{ $fullNameDisplay }}</span></div>
                                <div class="info-group"><label>Current
                                        Position</label><span>{{ $employee->position }}</span></div>
                                <div class="info-group">
                                    <label>Birthday</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '--' }}</span>
                                </div>
                                <div class="info-group">
                                    <label>Age</label><span>{{ $employee->date_of_birth ? $employee->date_of_birth->age . ' years' : '--' }}</span>
                                </div>
                                <div class="info-group"><label>Sex</label><span>{{ $employee->sex ?: '--' }}</span></div>
                                <div class="info-group"><label>Civil
                                        Status</label><span>{{ $employee->civil_status ?: '--' }}</span></div>
                                <div class="info-group full-width">
                                    <label>Address</label><span>{{ $employee->address ?: '--' }}</span>
                                </div>
                                <div class="info-group full-width"><label>Agency</label><span>{{ $employee->agency }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Card -->
                        <div class="card-premium animate-slide-up" style="animation-delay: 0.2s;">
                            <div class="card-header-modern">
                                <h3><i data-lucide="contact" style="color: #10b981;"></i> Contact Information</h3>
                            </div>
                            <div class="info-grid-modern">
                                <div class="info-group full-width"><label><i data-lucide="mail"></i> Email
                                        Address</label><span>{{ $employee->email ?: '--' }}</span></div>
                                <div class="info-group full-width"><label><i data-lucide="phone"></i> Phone
                                        Number</label><span>{{ $employee->phone ?: '--' }}</span></div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Info Tab -->
                <div id="workTab" class="info-tab-pane">
                    <div class="card-premium info-card" style="width: 100%; margin: 0 auto;">
                        <div class="card-header-modern">
                            <h3><i data-lucide="briefcase" style="color: #3b82f6; width: 20px; height: 20px;"></i>
                                Employment Information</h3>
                            @if($canEdit)
                                <button class="btn-edit-icon" onclick="openEditModal('work')">
                                    <i data-lucide="pencil"></i>
                                </button>
                            @endif
                        </div>
                        <div class="info-grid-modern">
                            <div class="info-group">
                                <label><i data-lucide="award" style="color: #3b82f6;"></i> Position</label>
                                <span>{{ $employee->position }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="building-2" style="color: #10b981;"></i> Agency</label>
                                <span>{{ $employee->agency }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="tag" style="color: #8b5cf6;"></i> Category</label>
                                <span>{{ $employee->category ?: '--' }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="user-cog" style="color: #ec4899;"></i> Status of Appointment</label>
                                <span>{{ $employee->employment_status ?: '--' }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="line-chart" style="color: #f59e0b;"></i> Salary Grade</label>
                                <span>{{ $employee->salary_grade ?: '--' }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="layers" style="color: #10b981;"></i> Level of Position</label>
                                <span>{{ $employee->level_of_position ?: '--' }}</span>
                            </div>
                            <div class="info-group">
                                <label><i data-lucide="shield-check" style="color: #6366f1;"></i> Record Status</label>
                                @if($employee->status == 'active')
                                    <span class="badge-status-green">ACTIVE</span>
                                @else
                                    <span class="text-danger"
                                        style="font-weight: 800;">{{ strtoupper($employee->status) }}</span>
                                @endif
                            </div>

                            @if($employee->status != 'active')
                                <div class="info-group">
                                    <label><i data-lucide="calendar-x" style="color: #ef4444;"></i> Separation Date</label>
                                    <span>{{ $employee->effective_date ? $employee->effective_date->format('M d, Y') : '--' }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i data-lucide="hash" style="color: #64748b;"></i> S.O. Number</label>
                                    <span>{{ $employee->so_no ?: '--' }}</span>
                                </div>
                                <div class="info-group full-width">
                                    <label><i data-lucide="info" style="color: #3b82f6;"></i> Separation Details</label>
                                    <span>{{ $employee->status_specify ?: '--' }}</span>
                                </div>
                                @if($employee->status == 'transfer')
                                    <div class="info-group full-width">
                                        <label><i data-lucide="map-pin" style="color: #10b981;"></i> Transferred To</label>
                                        <span>{{ $employee->transfer_to ?: '--' }}</span>
                                    </div>
                                @endif
                            @endif
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
                                @if($canManageDocs)
                                    <button class="btn-mini-import" onclick="triggerImport()" title="Import PDF">
                                        <i data-lucide="plus"></i>
                                    </button>
                                @endif
                            </div>

                            <div class="mini-search">
                                <input type="text" id="docSearch" placeholder="Search documents..."
                                    oninput="filterDocuments()">
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
                                        'Daily Time Record' => 'clock',
                                    ];

                                    $generalDocs = $documents->filter(function ($doc) {
                                        return empty($doc->category) || strtoupper($doc->category) === 'UNCATEGORIZED' || strtoupper($doc->category) === 'GENERAL';
                                    });

                                    $classifiedDocs = $documents->filter(function ($doc) {
                                        return !empty($doc->category) && strtoupper($doc->category) !== 'UNCATEGORIZED' && strtoupper($doc->category) !== 'GENERAL';
                                    })->groupBy(fn($doc) => strtoupper($doc->category));
                                @endphp

                                <!-- Uploaded Files / General Section -->
                                <div class="general-docs-section">
                                    <h5 class="section-title"><i data-lucide="upload-cloud"
                                            style="width: 14px; color: #3b82f6;"></i> Uploaded Files</h5>
                                    @forelse($generalDocs as $doc)
                                        <div class="mini-doc-item" id="doc-item-{{ $doc->id }}"
                                            data-url="{{ route('display.document', ['id' => $doc->id]) }}"
                                            data-name="{{ $doc->document_name }}"
                                            onclick="previewDocById('{{ $doc->id }}', '{{ route('display.document', ['id' => $doc->id]) }}', '{{ $doc->document_name }}')">
                                            <div class="mini-doc-icon"><i data-lucide="file-text"></i></div>
                                            <div class="mini-doc-info">
                                                <span class="name">{{ $doc->document_name }}</span>
                                                <span class="date">Modified {{ $doc->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($canManageDocs)
                                                <form action="{{ route('employees.delete-doc', ['id' => $doc->id]) }}" method="POST"
                                                    onclick="event.stopPropagation()">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="mini-btn-delete" title="Delete"
                                                        onclick="confirmDeleteDocument(event, this)">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="mini-empty" style="padding: 0.5rem; font-size: 0.75rem;">No general files
                                            yet.</div>
                                    @endforelse
                                </div>

                                <div style="height: 1rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 1rem;"></div>
                                <h5 class="section-title"><i data-lucide="layers" style="width: 14px; color: #8b5cf6;"></i>
                                    Classifications</h5>

                                @foreach($categories as $cat => $icon)
                                    @php
                                        $catColors = [
                                            'APPOINTMENT' => ['#3b82f6', '#eff6ff'],
                                            'EDUCATION' => ['#10b981', '#ecfdf5'],
                                            'PERSONAL DATA SHEET' => ['#8b5cf6', '#f5f3ff'],
                                            'EXPERIENCE' => ['#f59e0b', '#fffbeb'],
                                            'CERTIFICATES FOR TRAINING' => ['#ec4899', '#fdf2f8'],
                                            'PERFORMANCE RATING' => ['#06b6d4', '#ecfeff'],
                                            'Daily Time Record' => ['#14b8a6', '#f0fdfa'],
                                        ];
                                        $colors = $catColors[$cat] ?? ['#64748b', '#f8fafc'];
                                    @endphp
                                    <div class="doc-category-group" data-category="{{ $cat }}">
                                        <div class="category-header" onclick="toggleCategory(this)"
                                            style="border-left: 4px solid {{ $colors[0] }};">
                                            <div class="category-title">
                                                <div class="cat-icon-box"
                                                    style="background: {{ $colors[1] }}; color: {{ $colors[0] }};">
                                                    <i data-lucide="{{ $icon }}"></i>
                                                </div>
                                                <span style="font-weight: 700;">{{ $cat }}</span>
                                                <span
                                                    class="cat-count">({{ $classifiedDocs->has(strtoupper($cat)) ? $classifiedDocs->get(strtoupper($cat))->count() : 0 }})</span>
                                            </div>
                                            <div class="category-actions">
                                                @if($canManageDocs)
                                                    <button class="btn-upload-small"
                                                        onclick="event.stopPropagation(); triggerCategoryUpload('{{ $cat }}')"
                                                        title="Upload to {{ $cat }}">
                                                        <i data-lucide="plus"></i>
                                                    </button>
                                                @endif
                                                <i data-lucide="chevron-down" class="chevron-icon"></i>
                                            </div>
                                        </div>
                                        <div class="category-content" id="cat-{{ Str::slug($cat) }}">
                                            @if($classifiedDocs->has(strtoupper($cat)))
                                                @foreach($classifiedDocs->get(strtoupper($cat)) as $doc)
                                                    <div class="mini-doc-item" id="doc-item-{{ $doc->id }}"
                                                        data-url="{{ route('display.document', ['id' => $doc->id]) }}"
                                                        data-name="{{ $doc->document_name }}"
                                                        onclick="previewDocById('{{ $doc->id }}', '{{ route('display.document', ['id' => $doc->id]) }}', '{{ $doc->document_name }}')">
                                                        <div class="mini-doc-icon"><i data-lucide="file-text"></i></div>
                                                        <div class="mini-doc-info">
                                                            <span class="name">{{ $doc->document_name }}</span>
                                                            <span class="date">Modified {{ $doc->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        @if($canManageDocs)
                                                            <form action="{{ route('employees.delete-doc', ['id' => $doc->id]) }}"
                                                                method="POST" onclick="event.stopPropagation()">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="mini-btn-delete" title="Delete"
                                                                    onclick="confirmDeleteDocument(event, this)">
                                                                    <i data-lucide="trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="mini-empty" style="padding: 0.5rem; font-size: 0.75rem;">No files
                                                    uploaded.</div>
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
                                    <button onclick="prevDoc()" id="btnPrev" class="tool-btn"><i
                                            data-lucide="chevron-left"></i></button>
                                    <span id="docCounter" class="tool-counter">0/0</span>
                                    <button onclick="nextDoc()" id="btnNext" class="tool-btn"><i
                                            data-lucide="chevron-right"></i></button>
                                    <button onclick="fullscreenDoc()" class="tool-btn maximize" title="Fullscreen View"><i
                                            data-lucide="maximize"></i></button>
                                    <button onclick="openExternal()" class="tool-btn external" title="Open in New Tab"><i
                                            data-lucide="external-link"></i></button>
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
                <input type="hidden" name="active_tab" value="work">
                <input type="hidden" name="last_name" value="{{ $employee->last_name }}">
                <input type="hidden" name="first_name" value="{{ $employee->first_name }}">
                <input type="hidden" name="middle_name" value="{{ $employee->middle_name }}">
                <input type="hidden" name="suffix" value="{{ $employee->suffix }}">
                <div class="modal-body-modern scrollable">
                    <div class="modal-input"><label>Position</label><input type="text" name="position"
                            value="{{ $employee->position }}" required></div>
                    <div class="modal-input"><label>Agency</label><input type="text" name="agency"
                            value="{{ $employee->agency }}" required></div>
                    <div class="modal-input">
                        <label>Category</label>
                        <select name="category">
                            <option value="National" {{ $employee->category == 'National' ? 'selected' : '' }}>National</option>
                            <option value="City" {{ $employee->category == 'City' ? 'selected' : '' }}>City</option>
                        </select>
                    </div>
                    <div class="modal-input">
                        <label>Status of Appointment</label>
                        <select name="employment_status">
                            <option value="Permanent" {{ $employee->employment_status == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="Contractual" {{ $employee->employment_status == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                            <option value="Original" {{ $employee->employment_status == 'Original' ? 'selected' : '' }}>Original</option>
                        </select>
                    </div>
                    <div class="modal-input">
                        <label>Salary Grade</label>
                        <input type="text" name="salary_grade" value="{{ $employee->salary_grade }}" placeholder="e.g. 11">
                    </div>
                    <div class="modal-input">
                        <label>Level of Position</label>
                        <input type="text" name="level_of_position" value="{{ $employee->level_of_position }}" placeholder="e.g. 1st Level">
                    </div>
                    
                    @if($employee->status != 'active')
                    <div class="modal-input">
                        <label>Separation Status</label>
                        <select name="status">
                            <option value="resign" {{ $employee->status === 'resign' ? 'selected' : '' }}>Resign</option>
                            <option value="retired" {{ $employee->status === 'retired' ? 'selected' : '' }}>Retired</option>
                            <option value="transfer" {{ $employee->status === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="others" {{ $employee->status === 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                    <div class="modal-input">
                        <label>Separation Date (Effective Date)</label>
                        <input type="date" name="effective_date" value="{{ $employee->effective_date ? $employee->effective_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="modal-input">
                        <label>S.O Number</label>
                        <input type="text" name="so_no" value="{{ $employee->so_no }}">
                    </div>
                    <div class="modal-input">
                        <label>Separation Details (Specific Reason/Remark)</label>
                        <textarea name="status_specify" rows="2">{{ $employee->status_specify }}</textarea>
                    </div>
                    @if($employee->status == 'transfer')
                    <div class="modal-input">
                        <label>Transferred To</label>
                        <input type="text" name="transfer_to" value="{{ $employee->transfer_to }}">
                    </div>
                    @endif
                    @endif
                </div>
                <div class="modal-footer-modern"><button type="button" class="btn-cancel"
                        onclick="closeEditModal('work')">Cancel</button><button type="submit" class="btn-save">Save
                        Changes</button></div>
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
                <input type="hidden" name="active_tab" value="personal">
                <div class="modal-body-modern scrollable">
                    <div class="grid-2">
                        <div class="modal-input"><label>First Name</label><input type="text" name="first_name"
                                value="{{ $employee->first_name }}" required></div>
                        <div class="modal-input"><label>Last Name</label><input type="text" name="last_name"
                                value="{{ $employee->last_name }}" required></div>
                    </div>
                    <div class="grid-2">
                        <div class="modal-input"><label>Middle Name</label><input type="text" name="middle_name"
                                value="{{ $employee->middle_name }}"></div>
                        <div class="modal-input"><label>Suffix</label><input type="text" name="suffix"
                                value="{{ $employee->suffix }}" placeholder="e.g. Jr., III"></div>
                    </div>
                    <div class="grid-2">
                        <div class="modal-input"><label>Date of Birth</label><input type="date" name="date_of_birth"
                                value="{{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '' }}">
                        </div>
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
                            <label>Civil Status</label>
                            <select name="civil_status">
                                <option value="Single" {{ $employee->civil_status == 'Single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="Married" {{ $employee->civil_status == 'Married' ? 'selected' : '' }}>Married
                                </option>
                                <option value="Divorced" {{ $employee->civil_status == 'Divorced' ? 'selected' : '' }}>
                                    Divorced</option>
                                <option value="Widowed" {{ $employee->civil_status == 'Widowed' ? 'selected' : '' }}>Widowed
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-input"><label>Nationality</label><input type="text" name="nationality"
                            value="{{ $employee->nationality ?: 'Filipino' }}"></div>
                    <div class="grid-2">
                        <div class="modal-input"><label>Email</label><input type="email" name="email"
                                value="{{ $employee->email }}"></div>
                        <div class="modal-input"><label>Phone</label><input type="text" name="phone"
                                value="{{ $employee->phone }}"></div>
                    </div>
                    <div class="modal-input"><label>Address</label><textarea name="address"
                            rows="2">{{ $employee->address }}</textarea></div>

                    <input type="hidden" name="position" value="{{ $employee->position }}">
                    <input type="hidden" name="agency" value="{{ $employee->agency }}">
                </div>
                <div class="modal-footer-modern"><button type="button" class="btn-cancel"
                        onclick="closeEditModal('personal')">Cancel</button><button type="submit" class="btn-save">Save
                        Profile</button></div>
            </form>
        </div>
    </div>

    <form id="importForm" method="POST" action="{{ route('employees.upload', ['id' => $employee->id]) }}"
        enctype="multipart/form-data" style="display: none;" data-employee-id="{{ $employee->id }}" data-upload-url-base="{{ url('/employee-details/upload') }}">
        @csrf
        <input type="hidden" name="category" id="importCategory" value="GENERAL">
        <input type="file" name="documents[]" id="importFileInput" accept=".pdf,image/*,.docx,.doc,.xlsx" multiple
            onchange="handleBatchUpload(this)">
    </form>

    <!-- Cropper Modal -->
    <div id="cropperModal" class="modal-modern" style="z-index: 10001; background: rgba(0,0,0,0.8);">
        <div class="modal-content-modern animate-scale-up" style="max-width: 500px;">
            <div class="modal-header-modern">
                <h3>Crop Profile Photo</h3>
                <button type="button" onclick="closeCropper()"><i data-lucide="x"></i></button>
            </div>
            <div
                style="padding: 20px; max-height: 400px; overflow: hidden; display: flex; justify-content: center; background: #000;">
                <img id="cropperImage" src="" style="max-width: 100%; display: block;">
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn-cancel" onclick="closeCropper()">Cancel</button>
                <button type="button" class="btn-save" onclick="applyCrop()">Apply & Upload</button>
            </div>
        </div>
    </div>

    <!-- View Photo Modal -->
    <div id="viewPhotoModal" class="modal-modern" style="z-index: 10002; background: rgba(0,0,0,0.9);"
        onclick="closePhotoView()">
        <div style="position: relative; max-width: 90vw; max-height: 90vh;">
            <img id="viewingImage" src=""
                style="max-width: 100%; max-height: 90vh; border-radius: 8px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <button
                style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; cursor: pointer;">
                <i data-lucide="x" style="width: 32px; height: 32px;"></i>
            </button>
        </div>
    </div>

    <!-- Upload Progress Modal -->
    <div id="uploadProgressModal" class="modal-modern"
        style="z-index: 10100; background: rgba(0,0,0,0.4); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center;">
        <div class="modal-content-modern animate-scale-up" style="max-width: 450px; text-align: center; padding: 2.5rem;">
            <div class="upload-icon-wrapper"
                style="width: 80px; height: 80px; background: var(--erp-bg); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--erp-primary);">
                <i data-lucide="cloud-upload" style="width: 40px; height: 40px;" id="statusIcon"></i>
            </div>
            <h3 id="uploadStatusTitle"
                style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Uploading
                Documents</h3>
            <p id="uploadStatusText" style="color: var(--erp-muted); font-size: 0.95rem; margin-bottom: 2rem;">Please wait
                while we process your files...</p>

            <div class="progress-box" style="margin-bottom: 1rem;">
                <div
                    style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: var(--erp-text); margin-bottom: 0.6rem;">
                    <span id="progressText">0% Complete</span>
                    <span id="progressFiles">0/0 Files</span>
                </div>
                <div
                    style="width: 100%; height: 12px; background: var(--erp-bg); border-radius: 10px; overflow: hidden; border: 1px solid var(--erp-border);">
                    <div id="progressBar"
                        style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6, #6366f1); border-radius: 10px; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                    </div>
                </div>
            </div>

            <p style="font-size: 0.8rem; color: #ef4444; font-weight: 600;" id="uploadAlert">Do not close or refresh this
                page.</p>
        </div>
    </div>

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

            body[data-theme="dark"] {
                --erp-bg: #0f172a;
                --erp-card: #1e293b;
                --erp-text: #f8fafc;
                --erp-muted: #94a3b8;
                --erp-border: #334155;
            }

            body[data-theme="night"] {
                --erp-bg: #f2ead3;
                --erp-card: #fffcf0;
                --erp-text: #5c4137;
                --erp-muted: #92400e;
                --erp-border: #ead6bb;
                --erp-primary: #d97706;
            }

            .page-content {
                width: 100%;
            }

            /* Animations */
            .animate-fade-in {
                animation: fadeIn 0.6s ease-out;
            }

            .animate-slide-up {
                animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .animate-scale-up {
                animation: scaleUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scaleUp {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes progress {
                from {
                    stroke-dasharray: 0, 100;
                }
            }

            /* Layout System */
            .personal-grid-modern {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }

            @media (max-width: 991px) {
                .personal-grid-modern {
                    grid-template-columns: 1fr;
                    gap: 1.25rem;
                }
            }

            .info-content-area.full-width {
                width: 100%;
                position: relative;
                z-index: 10;
            }

            .docs-tab-layout {
                display: grid;
                grid-template-columns: 350px 1fr;
                gap: 1.5rem;
                min-height: 600px;
                transition: 0.3s;
            }

            @media (max-width: 1200px) {
                .docs-tab-layout {
                    grid-template-columns: 320px 1fr;
                    gap: 1rem;
                }
            }

            @media (max-width: 991px) {
                .docs-tab-layout {
                    grid-template-columns: 1fr;
                    height: auto;
                    gap: 1.5rem;
                }

                .docs-mini-card {
                    height: 500px;
                }

                .preview-mini-card {
                    min-height: 500px;
                }

                .profile-header-content {
                    flex-direction: column;
                    text-align: center;
                    padding: 1.5rem;
                    margin-top: -60px;
                    gap: 1rem;
                }

                .profile-avatar-circle {
                    width: 100px;
                    height: 100px;
                    font-size: 2.5rem;
                    border-width: 4px;
                }

                .employee-display-name {
                    font-size: 1.5rem;
                }

                .profile-main-meta {
                    padding-top: 0;
                }

                .employee-sub-meta {
                    justify-content: center;
                    gap: 0.75rem;
                }

                .profile-stats {
                    padding-top: 20px;
                    border-top: 1px solid #f1f5f9;
                    width: 100%;
                    margin-top: 1rem;
                }

                .profile-banner {
                    height: 100px;
                }

                .info-grid-modern {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .tab-btn {
                    padding: 0.75rem 1rem;
                    font-size: 0.8rem;
                }
            }

            .docs-mini-card {
                display: flex;
                flex-direction: column;
                height: 100%;
                overflow: hidden;
                background: white;
                border-radius: 16px;
                box-shadow: var(--erp-shadow);
            }

            .mini-docs-list {
                flex: 1;
                overflow-y: auto;
                padding: 1.25rem;
                display: flex;
                flex-direction: column;
                gap: 0.8rem;
                scrollbar-width: thin;
            }

            .mini-card-header {
                flex-shrink: 0;
                padding: 1.25rem;
                border-bottom: 1px solid #f8fafc;
            }

            .mini-search {
                flex-shrink: 0;
                padding: 0.75rem 1.25rem;
            }


            /* Banner & Header */
            .profile-banner-container {
                position: relative;
                background: var(--erp-card);
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

            .banner-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.4));
            }

            .banner-navigation {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                padding: 1.25rem 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 10;
            }

            .banner-back-btn {
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                transition: all 0.3s;
            }

            .banner-back-btn:hover {
                background: white;
                color: var(--erp-primary);
                transform: translateX(-3px);
            }

            .status-badge {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 100px;
                color: white;
                font-size: 0.7rem;
                font-weight: 800;
                letter-spacing: 0.05em;
            }

            .status-active .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #10b981;
                box-shadow: 0 0 10px #10b981;
            }

            .profile-header-content {
                display: flex;
                align-items: center;
                padding: 0 2.5rem 1.5rem;
                margin-top: -50px;
                gap: 2.5rem;
                position: relative;
                flex-wrap: wrap;
            }

            .profile-avatar-wrapper {
                position: relative;
                z-index: 5;
            }

            .profile-avatar-circle {
                width: 140px;
                height: 140px;
                border-radius: 50%;
                background: var(--erp-primary);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3.5rem;
                font-weight: 800;
                border: 6px solid white;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                font-family: 'Outfit', sans-serif;
            }

            .avatar-camera-btn {
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 38px;
                height: 38px;
                background: white;
                border: none;
                border-radius: 50%;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                color: var(--erp-muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: 0.2s;
            }

            .avatar-camera-btn:hover {
                color: var(--erp-primary);
                transform: scale(1.1);
            }

            .profile-main-meta {
                flex: 1;
                padding-top: 50px;
            }

            .employee-display-name {
                font-size: 2rem;
                font-weight: 800;
                color: var(--erp-text);
                margin-bottom: 0.5rem;
                font-family: 'Outfit', sans-serif;
            }

            .employee-sub-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .meta-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.85rem;
                color: var(--erp-muted);
                font-weight: 600;
            }

            .meta-item i {
                width: 14px;
                color: var(--erp-primary);
                opacity: 0.7;
            }

            .profile-stats {
                padding-top: 50px;
            }

            .profile-progress-circle {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .circular-chart {
                width: 50px;
                height: 50px;
            }

            .circle-bg {
                fill: none;
                stroke: #f1f5f9;
                stroke-width: 3.5;
            }

            .circle {
                fill: none;
                stroke-width: 3.5;
                stroke-linecap: round;
                stroke: var(--erp-primary);
                animation: progress 1s ease-out forwards;
            }

            .percentage {
                fill: var(--erp-text);
                font-family: 'Outfit', sans-serif;
                font-size: 0.65rem;
                font-weight: 800;
                text-anchor: middle;
            }

            .progress-label {
                font-size: 0.75rem;
                font-weight: 700;
                color: var(--erp-muted);
                white-space: nowrap;
            }

            /* Tabs Bar */
            .profile-tabs-nav {
                display: flex;
                gap: 0.5rem;
                margin-bottom: 1.5rem;
                border-bottom: 2px solid #f1f5f9;
                padding: 0 0.5rem;
                position: relative;
                z-index: 20;
                overflow-x: auto;
                scrollbar-width: none;
                /* Hide scrollbar for Firefox */
                -ms-overflow-style: none;
                /* Hide scrollbar for IE/Edge */
            }

            .profile-tabs-nav::-webkit-scrollbar {
                display: none;
            }

            /* Hide scrollbar for Chrome/Safari */

            .tab-btn {
                background: none;
                border: none;
                padding: 0.85rem 1.5rem;
                font-size: 0.9rem;
                font-weight: 700;
                color: var(--erp-muted);
                cursor: pointer;
                position: relative;
                transition: 0.3s;
                border-radius: 10px 10px 0 0;
                display: flex;
                align-items: center;
                gap: 0.6rem;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .tab-btn i {
                width: 16px;
                height: 16px;
            }

            .tab-btn:hover {
                color: var(--erp-primary);
                background: #f8fafc;
            }

            .tab-btn.active {
                color: var(--erp-primary);
            }

            .tab-btn.active::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                right: 0;
                height: 3px;
                background: var(--erp-primary);
                border-radius: 10px;
            }

            /* Card Systems */
            .card-premium {
                background: var(--erp-card);
                border-radius: 16px;
                box-shadow: var(--erp-shadow);
                border: 1px solid var(--erp-border);
                overflow: hidden;
            }

            .card-header-modern {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--erp-border);
                background: var(--erp-bg);
            }

            .card-header-modern h3 {
                font-size: 1rem;
                font-weight: 800;
                color: var(--erp-text);
                margin: 0;
                font-family: 'Outfit', sans-serif;
                display: flex;
                align-items: center;
                gap: 0.6rem;
            }

            .btn-edit-icon {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                border: 1px solid var(--erp-border);
                background: var(--erp-card);
                color: var(--erp-muted);
                cursor: pointer;
                transition: 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-edit-icon:hover {
                background: var(--erp-primary);
                color: white;
                transform: translateY(-2px);
            }

            .info-grid-modern {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                padding: 1.5rem;
            }

            .info-group label {
                font-size: 0.75rem;
                font-weight: 800;
                color: var(--erp-muted);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .info-group label i {
                width: 16px;
                height: 16px;
                opacity: 0.8;
            }

            .info-group span {
                font-size: 1rem;
                font-weight: 700;
                color: var(--erp-text);
            }

            .blood-badge {
                background: #fee2e2;
                color: #dc2626;
                padding: 0.2rem 0.6rem;
                border-radius: 6px;
                font-weight: 800;
                font-size: 0.75rem;
            }

            .emp-id-badge {
                color: var(--erp-primary);
                font-weight: 800;
                font-family: monospace;
            }

            .info-tab-pane {
                display: none;
            }

            .info-tab-pane.active {
                display: block;
                animation: fadeIn 0.4s ease-out;
            }

            /* Documents Tab Specifics */
            .mini-card-header {
                padding: 1.25rem;
                border-bottom: 1px solid var(--erp-border);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .mini-card-header h4 {
                font-size: 0.95rem;
                font-weight: 800;
                margin: 0;
                font-family: 'Outfit', sans-serif;
                color: var(--erp-text);
            }

            .mini-card-header p {
                font-size: 0.7rem;
                color: var(--erp-muted);
                margin: 2px 0 0;
            }

            .btn-mini-import {
                background: var(--erp-primary);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                border: none;
                font-weight: 700;
                font-size: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.4rem;
                cursor: pointer;
            }

            .mini-search {
                padding: 0.75rem 1.25rem;
                position: relative;
                background: var(--erp-bg);
                display: flex;
                align-items: center;
            }

            .mini-search i {
                position: absolute;
                left: 1.85rem;
                top: 50%;
                transform: translateY(-50%);
                width: 14px;
                color: var(--erp-muted);
                pointer-events: none;
            }

            .mini-search input {
                width: 100%;
                padding: 0.7rem 1rem 0.7rem 2.5rem;
                border: 1px solid var(--erp-border);
                border-radius: 12px;
                font-size: 0.85rem;
                outline: none;
                transition: 0.2s;
                background: var(--erp-card);
                color: var(--erp-text);
            }

            .mini-search input:focus {
                border-color: var(--erp-primary);
                background: var(--erp-card);
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            }

            .mini-docs-list {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.6rem;
            }

            .mini-doc-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.85rem;
                border-radius: 12px;
                background: var(--erp-bg);
                border: 1px solid transparent;
                cursor: pointer;
                transition: 0.2s;
            }

            .mini-doc-item:hover {
                background: var(--erp-card);
                border-color: var(--erp-border);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }

            .mini-doc-item.active {
                background: var(--erp-bg);
                border-color: var(--erp-primary);
            }

            .mini-doc-item .mini-btn-delete {
                margin-left: auto;
                flex-shrink: 0;
            }

            /* docx-preview styling */
            #docx-container {
                display: block;
                /* Removed flex to allow safe scrolling */
                background: #f1f5f9;
                min-height: 100%;
                overflow: auto;
            }

            #docx-container .docx-wrapper {
                background: transparent;
                padding: 2rem;
                width: 100%;
                min-width: max-content;
                /* Allow wrapper to stretch for wide docs */
            }

            #docx-container .docx {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                margin: 0 auto;
                background: white;
                overflow-x: auto !important;
                /* Extremely important: allows table scrolling rather than clipping */
            }

            /* Force tables from Google Docs to not explicitly break boundaries unless necessary */
            #docx-container .docx table {
                max-width: 100% !important;
            }


            .mini-doc-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: rgba(59, 130, 246, 0.1);
                color: var(--erp-primary);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .mini-doc-info {
                flex: 1;
                overflow: hidden;
            }

            .mini-doc-info .name {
                font-size: 0.8rem;
                font-weight: 700;
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mini-doc-info .date {
                font-size: 0.65rem;
                color: var(--erp-muted);
            }

            .cat-icon-box {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 0.75rem;
            }

            .category-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                background: var(--erp-card);
                border: 1px solid var(--erp-border);
                border-radius: 12px;
                cursor: pointer;
                transition: 0.2s;
                margin-bottom: 0.5rem;
                color: var(--erp-text);
            }

            .category-header:hover {
                background: var(--erp-bg);
            }

            .category-header.active {
                background: var(--erp-bg);
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
                margin-bottom: 0;
            }

            .category-title {
                display: flex;
                align-items: center;
            }

            .category-content {
                display: none;
                padding: 1rem;
                background: var(--erp-bg);
                border: 1px solid var(--erp-border);
                border-top: none;
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
                margin-bottom: 0.75rem;
            }

            .category-content.active {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .mini-btn-delete {
                color: #cbd5e1;
                background: none;
                border: none;
                padding: 0.4rem;
                cursor: pointer;
                transition: 0.2s;
                display: flex;
                align-items: center;
            }

            .mini-btn-delete:hover {
                color: #ef4444;
                background: #fee2e2;
                border-radius: 8px;
            }


            .preview-header-modern {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--erp-border);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: var(--erp-bg);
            }

            .preview-title {
                font-weight: 800;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 0.6rem;
                font-family: 'Outfit', sans-serif;
            }

            .preview-tools {
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .tool-btn {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                border: none;
                background: transparent;
                color: var(--erp-muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .tool-btn:hover:not(:disabled) {
                color: var(--erp-primary);
                background: #f0f7ff;
            }

            .tool-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
            }

            .tool-counter {
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0 0.5rem;
            }

            .preview-mini-card {
                height: 100%;
                display: flex;
                flex-direction: column;
                background: var(--erp-card);
            }

            .mini-placeholder {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: var(--erp-muted);
                background: var(--erp-bg);
            }

            .mini-placeholder .p-icon {
                font-size: 3rem;
                color: var(--erp-border);
                margin-bottom: 1rem;
            }

            .mini-placeholder h3 {
                font-size: 1.1rem;
                margin: 0 0 0.5rem;
                font-family: 'Outfit', sans-serif;
            }

            .mini-placeholder p {
                font-size: 0.85rem;
                margin: 0;
            }

            .mini-iframe {
                flex: 1;
                border: none;
                width: 100%;
            }

            /* Modals */
            .modal-modern {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 2000;
                background: rgba(0, 0, 0, 0.7);
                /* Deep overlay with blur */
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }

            .modal-modern.active {
                display: flex;
            }

            .modal-content-modern {
                background: #ffffff;
                /* Default solid white */
                border-radius: 20px;
                width: 100%;
                max-width: 550px;
                box-shadow: 0 30px 70px rgba(0, 0, 0, 0.5);
                overflow: hidden;
                border: 1px solid #e2e8f0;
                position: relative;
            }

            body[data-theme="dark"] .modal-content-modern {
                background: #1e293b;
                border-color: #334155;
            }

            body[data-theme="night"] .modal-content-modern {
                background: #fffcf0;
                /* Solid warm white for Night Mode */
                border-color: #8c7662;
            }

            .modal-header-modern {
                padding: 1.25rem 2rem;
                border-bottom: 2px solid var(--erp-border);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                color: #0f172a;
            }

            body[data-theme="dark"] .modal-header-modern {
                background: #0f172a;
                color: #f8fafc;
                border-bottom-color: #334155;
            }

            body[data-theme="night"] .modal-header-modern {
                background: #fdf6e3;
                color: #5c4137;
                border-bottom-color: #ead6bb;
            }

            .modal-header-modern h3 {
                font-family: 'Outfit', sans-serif;
                font-weight: 800;
                margin: 0;
            }

            .modal-header-modern button {
                background: none;
                border: none;
                padding: 0.5rem;
                color: #64748b;
                cursor: pointer;
                transition: 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modal-header-modern button:hover {
                color: #ef4444;
            }

            .modal-body-modern {
                padding: 2rem;
                background: #ffffff;
            }

            body[data-theme="dark"] .modal-body-modern {
                background: #1e293b;
            }

            body[data-theme="night"] .modal-body-modern {
                background: #fffcf0;
            }

            .modal-footer-modern {
                padding: 1.5rem 2rem;
                background: #f8fafc;
                display: flex;
                justify-content: flex-end;
                gap: 1rem;
                border-top: 1px solid var(--erp-border);
            }

            body[data-theme="dark"] .modal-footer-modern {
                background: #0f172a;
                border-top-color: #334155;
            }

            body[data-theme="night"] .modal-footer-modern {
                background: #fdf6e3;
                border-top-color: #ead6bb;
            }

            .modal-body-modern.scrollable {
                max-height: 60vh;
                overflow-y: auto;
            }

            .modal-input {
                margin-bottom: 1.5rem;
            }

            .modal-input label {
                display: block;
                font-size: 0.8rem;
                font-weight: 800;
                color: var(--erp-muted);
                text-transform: uppercase;
                margin-bottom: 0.6rem;
                letter-spacing: 0.05em;
            }

            body[data-theme="dark"] .modal-input label {
                color: #94a3b8;
            }

            body[data-theme="night"] .modal-input label {
                color: #92400e;
            }

            .modal-input input,
            .modal-input select,
            .modal-input textarea {
                width: 100%;
                padding: 0.85rem 1.15rem;
                border-radius: 12px;
                border: 2px solid var(--erp-border);
                background: #f8fafc;
                color: #0f172a;
                font-size: 0.95rem;
                font-weight: 600;
                outline: none;
                transition: 0.2s;
            }

            body[data-theme="dark"] .modal-input input,
            body[data-theme="dark"] .modal-input select,
            body[data-theme="dark"] .modal-input textarea {
                background: #0f172a;
                color: #f8fafc;
                border-color: #334155;
            }

            body[data-theme="night"] .modal-input input,
            body[data-theme="night"] .modal-input select,
            body[data-theme="night"] .modal-input textarea {
                background: #fdf6e3;
                color: #5c4137;
                border-color: #ead6bb;
            }

            .modal-input input:focus,
            .modal-input select:focus,
            .modal-input textarea:focus {
                border-color: var(--erp-primary);
                background: #ffffff;
            }

            body[data-theme="dark"] .modal-input input:focus {
                background: #000000;
            }

            .modal-footer-modern {
                padding: 1.5rem 2rem;
                background: var(--erp-bg);
                display: flex;
                justify-content: flex-end;
                gap: 1rem;
                border-top: 1px solid var(--erp-border);
            }

            .btn-cancel {
                background: var(--erp-card);
                color: var(--erp-muted);
                padding: 0.75rem 1.5rem;
                border-radius: 12px;
                border: 1px solid var(--erp-border);
                font-weight: 700;
                cursor: pointer;
                transition: 0.2s;
            }

            .btn-cancel:hover {
                background: var(--erp-bg);
                color: var(--erp-text);
            }

            .btn-save {
                background: var(--erp-primary);
                color: white;
                padding: 0.75rem 2rem;
                border-radius: 12px;
                border: none;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
                transition: 0.2s;
            }

            .btn-save:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(59, 130, 246, 0.35);
            }

            /* Toast Notification */
            .toast-modern {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                z-index: 9999;
                background: var(--erp-card);
                color: var(--erp-text);
                padding: 1rem 1.5rem;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                gap: 0.75rem;
                border: 1px solid var(--erp-border);
            }

            .toast-content {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            /* Categorized Docs Styling */
            .doc-category-group {
                margin-bottom: 0.75rem;
                border-radius: 12px;
                background: var(--erp-card);
                border: 1px solid var(--erp-border);
                overflow: hidden;
                transition: 0.3s;
            }

            .doc-category-group:hover {
                border-color: var(--erp-muted);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }

            .category-header {
                padding: 0.85rem 1rem;
                background: var(--erp-bg);
                display: flex;
                justify-content: space-between;
                align-items: center;
                cursor: pointer;
                transition: 0.2s;
            }

            .category-header:hover {
                background: var(--erp-card);
            }

            .category-title {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                color: var(--erp-text);
            }

            .category-title i {
                width: 16px;
                color: var(--erp-primary);
            }

            .category-title span {
                font-weight: 800;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.02em;
            }

            .cat-count {
                font-size: 0.65rem;
                color: var(--erp-muted);
                font-weight: 600;
            }

            .category-actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .btn-upload-small {
                width: 26px;
                height: 26px;
                border-radius: 8px;
                border: none;
                background: var(--erp-primary);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: 0.2s;
            }

            .btn-upload-small:hover {
                transform: scale(1.1);
                background: #2563eb;
            }

            .category-content {
                display: none;
                padding: 0.5rem;
                border-top: 1px solid var(--erp-border);
                background: var(--erp-bg);
            }

            .category-content.active {
                display: block;
                animation: slideDown 0.3s ease-out;
            }

            .chevron-icon {
                transition: transform 0.3s;
                width: 14px;
                color: var(--erp-muted);
            }

            .category-header.active .chevron-icon {
                transform: rotate(180deg);
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .mini-btn-delete {
                color: #f87171;
                background: none;
                border: none;
                padding: 0.4rem;
                cursor: pointer;
                transition: 0.2s;
                display: flex;
                align-items: center;
            }

            .mini-btn-delete:hover {
                color: #991b1b;
                background: #fee2e2;
                border-radius: 8px;
            }

            .badge-status-green {
                background: #10b981;
                color: white;
                padding: 0.4rem 0.8rem;
                border-radius: 8px;
                font-weight: 800;
                font-size: 0.75rem;
                border: none;
            }

            .section-title {
                font-size: 0.7rem;
                font-weight: 800;
                color: var(--erp-muted);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin: 1.25rem 0 0.75rem;
            }

            /* Ensure nothing blocks clicks */
            .sidebar-overlay {
                pointer-events: none;
            }

            .modal-modern {
                pointer-events: none;
            }

            .modal-modern.active {
                pointer-events: auto;
            }

            .modal-content-modern {
                pointer-events: auto;
            }

            .general-docs-section {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }



            /* 3-Layer Avatar Styles */
            .profile-avatar-3layer {
                width: 150px;
                height: 150px;
                position: relative;
            }

            .avatar-ring-outer {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                padding: 3px;
                background: linear-gradient(135deg, #6366f1 0%, #3b82f6 50%, #ec4899 100%);
                box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            }

            body[data-theme="night"] .avatar-ring-outer {
                background: linear-gradient(135deg, #f59e0b 0%, #fb923c 50%, #facc15 100%);
                box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
            }

            .avatar-ring-inner {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                padding: 2px;
                background: #fff;
            }

            body[data-theme="dark"] .avatar-ring-inner {
                background: #1e293b;
            }

            body[data-theme="night"] .avatar-ring-inner {
                background: #3d2b1f;
            }

            .avatar-photo-box {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background: #f1f5f9;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-photo-box img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .avatar-initials {
                font-size: 3.5rem;
                font-weight: 800;
                color: #3b82f6;
                font-family: 'Outfit', sans-serif;
            }

            body[data-theme="night"] .avatar-initials {
                color: #fb923c;
            }

            #cropperModal,
            #viewPhotoModal {
                display: none;
            }

            #cropperModal.active,
            #viewPhotoModal.active {
                display: flex !important;
            }

            /* Fullscreen Viewer Styles */
            .preview-mini-card.is-fullscreen {
                height: 100vh !important;
                width: 100vw !important;
                margin: 0 !important;
                border-radius: 0 !important;
                padding: 0 !important;
                display: flex;
                flex-direction: column;
                z-index: 9999;
                background: #000;
            }

            .preview-mini-card.is-fullscreen .preview-header-modern {
                padding: 1rem 2rem;
                background: var(--bg-card);
                border-bottom: 2px solid var(--border);
            }

            .preview-mini-card.is-fullscreen .mini-iframe,
            .preview-mini-card.is-fullscreen .mini-placeholder {
                flex: 1;
                height: calc(100vh - 70px) !important;
                background: #000;
            }
        </style>
    @endpush

    @push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="{{ asset('assets/js/employee-details.js') }}"></script>
@endpush
@endsection