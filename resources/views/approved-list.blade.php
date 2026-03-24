@extends('layouts.app')

@section('title', 'Approved List')

@section('content')
<div class="page-content" style="padding: 2rem;">
    <!-- Modern Header Section -->
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 900; color: #1e1b4b; margin: 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em;">Approved <span style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">List</span></h1>
            <p style="font-size: 1rem; color: #64748b; margin-top: 0.5rem; font-weight: 500;">History of all processed and approved document requests</p>
        </div>
        
        <div class="search-container" style="width: 380px; position: relative; background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <i data-lucide="search" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #94a3b8;"></i>
            <form method="GET" action="{{ route('employees.approved-list') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    style="width: 100%; border: none; padding: 0.5rem 0.5rem 0.5rem 2rem; font-size: 1rem; font-family: 'Inter', sans-serif; outline: none; background: transparent; color: #1e293b;" 
                    placeholder="Search approved files..." 
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
    </div>

    <!-- Quick Stats for Approved List -->
    <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem;">
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 1.5rem 2.5rem; border-radius: 24px; color: white; display: flex; align-items: center; gap: 1.5rem; box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.3);">
            <div style="width: 3.5rem; height: 3.5rem; background: rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="check-check" style="width: 2rem; height: 2rem;"></i>
            </div>
            <div>
                <span style="display: block; font-size: 0.875rem; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.05em;">Total Disbursed</span>
                <span style="font-size: 2.5rem; font-weight: 900; font-family: 'Outfit', sans-serif;">{{ $approved_requests->count() }} <span style="font-size: 1rem; font-weight: 500; opacity: 0.8;">Files</span></span>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div style="background: white; border-radius: 28px; border: 1px solid #e2e8f0; box-shadow: 0 20px 50px -12px rgba(0,0,0,0.08); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">ID</th>
                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">Employee / Agency</th>
                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">Document / Purpose</th>
                    <th style="padding: 1.25rem 1.5rem; text-align: center; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">Copies</th>
                    <th style="padding: 1.25rem 1.5rem; text-align: center; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">Date Approved</th>
                    <th style="padding: 1.25rem 1.5rem; text-align: center; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 800; border-bottom: 2px solid #f1f5f9;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($approved_requests as $req)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease;" onmouseover="this.style.background='#f0fdf4';" onmouseout="this.style.background='white';">
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle;">
                        <span style="font-weight: 800; color: #10b981; font-size: 1rem; padding: 0.5rem 0.75rem; background: #ecfdf5; border-radius: 10px;">#{{ $req->id }}</span>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle;">
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <span style="font-size: 1.125rem; font-weight: 800; color: #1e293b;">{{ $req->employee_name }}</span>
                            <span style="font-size: 0.875rem; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.4rem;">
                                <i data-lucide="building" style="width: 14px; height: 14px;"></i>
                                {{ $req->agency ?? $req->employee_id }}
                            </span>
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle;">
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            <span style="font-weight: 700; font-size: 1.05rem; color: #059669;">{{ $req->request_type }}</span>
                            <span style="font-size: 0.875rem; color: #64748b; font-weight: 500; font-style: italic;">
                                <i data-lucide="help-circle" style="width: 14px; height: 14px; position: relative; top: 1px;"></i>
                                {{ $req->purpose ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle; text-align: center;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 800; color: #1e293b;">
                            {{ $req->num_copies ?? 1 }}
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle; text-align: center; white-space: nowrap;">
                        <span style="font-weight: 700; font-size: 0.9375rem; color: #334155;">{{ $req->request_date ? $req->request_date->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle; text-align: center;">
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; border-radius: 14px; font-size: 0.875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                            Approved
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div style="padding: 6rem 2rem; text-align: center;">
                            <div style="width: 100px; height: 100px; background: #f8fafc; color: #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #e2e8f0;">
                                <i data-lucide="inbox" style="width: 50px; height: 50px;"></i>
                            </div>
                            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif;">Empty Record</h3>
                            <p style="font-size: 1rem; color: #64748b; max-width: 320px; margin: 0 auto;">No requests have been approved yet. Use the Request List to start processing.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
