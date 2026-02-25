@extends('layouts.app')

@section('title', $employee->name)

@section('content')
<div class="page-content">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">
            <i data-lucide="arrow-left"></i>
            Back to Master List
        </a>
    </div>

    <!-- Header -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 class="page-title">{{ $employee->name }}</h1>
            <p class="page-subtitle">{{ $employee->position }}</p>
        </div>
        <span class="badge badge-{{ $employee->status }}">
            {{ ucfirst($employee->status) }}
        </span>
    </div>

    @if(session('success_message'))
    <div class="alert alert-success">
        <i data-lucide="check-circle" class="alert-icon"></i>
        <span class="alert-text">{{ session('success_message') }}</span>
    </div>
    @endif

    @if(session('error_message'))
    <div class="alert alert-error">
        <i data-lucide="alert-circle" class="alert-icon"></i>
        <span class="alert-text">{{ session('error_message') }}</span>
    </div>
    @endif

    <!-- Work Information -->
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i data-lucide="building-2" style="color: #4f46e5;"></i>
            Work Information
        </h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Employee ID</p>
                    <p class="info-value">{{ $employee->id }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="building-2" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Department</p>
                    <p class="info-value">{{ $employee->department }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Email</p>
                    <p class="info-value">{{ $employee->email }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Phone</p>
                    <p class="info-value">{{ $employee->phone }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Date Joined</p>
                    <p class="info-value">{{ $employee->date_joined ? $employee->date_joined->format('F j, Y') : 'N/A' }}</p>
                </div>
            </div>
            @if($employee->transfer_location)
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Transfer Location</p>
                    <p class="info-value">{{ $employee->transfer_location }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Personal Information -->
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i data-lucide="user" style="color: #4f46e5;"></i>
            Personal Information
        </h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Date of Birth</p>
                    <p class="info-value">
                        {{ $employee->date_of_birth ? $employee->date_of_birth->format('F j, Y') : 'Not provided' }}
                    </p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Address</p>
                    <p class="info-value">{{ $employee->address ?: 'Not provided' }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Emergency Contact</p>
                    <p class="info-value">{{ $employee->emergency_contact ?: 'Not provided' }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Emergency Phone</p>
                    <p class="info-value">{{ $employee->emergency_phone ?: 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="file-text" style="color: #4f46e5;"></i>
                Documents ({{ $doc_count }}/5)
            </h3>
            @if($doc_count < 5)
            <label for="file-upload" class="btn btn-primary" style="cursor: pointer;">
                <i data-lucide="upload"></i>
                Upload PDF
            </label>
            <form method="POST" action="{{ route('employees.upload', ['id' => $employee->id]) }}" enctype="multipart/form-data" id="uploadForm" style="display: none;">
                @csrf
                <input 
                    type="file" 
                    id="file-upload" 
                    name="documents[]" 
                    accept=".pdf" 
                    multiple 
                    onchange="document.getElementById('uploadForm').submit()"
                >
            </form>
            @endif
        </div>

        @if($doc_count < 5)
        <div class="alert alert-info">
            <i data-lucide="alert-circle" class="alert-icon"></i>
            <span class="alert-text">You can upload up to {{ 5 - $doc_count }} more PDF document{{ (5 - $doc_count) !== 1 ? 's' : '' }}.</span>
        </div>
        @endif

        @if($documents->count() > 0)
        <div class="document-list">
            @foreach ($documents as $doc)
            <div class="document-item">
                <div class="document-info">
                    <div class="document-icon">
                        <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div class="document-details">
                        <div class="document-name">{{ $doc->document_name }}</div>
                        <div class="document-date">Uploaded on {{ $doc->upload_date->format('m/d/Y') }}</div>
                    </div>
                </div>
                <div class="document-actions">
                    <a href="{{ asset($doc->file_path) }}" download class="icon-btn icon-btn-blue" title="Download">
                        <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                    </a>
                    <form action="{{ route('employees.delete-doc', ['id' => $doc->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="icon-btn icon-btn-red" 
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this document?')">
                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 2px dashed #d1d5db;">
            <i data-lucide="file-text" style="width: 48px; height: 48px; color: #9ca3af; margin: 0 auto 1rem;"></i>
            <p style="color: #6b7280; font-weight: 500; margin-bottom: 0.25rem;">No documents uploaded yet</p>
            <p style="color: #9ca3af; font-size: 0.875rem;">Upload up to 5 PDF documents</p>
        </div>
        @endif
    </div>
</div>
@endsection