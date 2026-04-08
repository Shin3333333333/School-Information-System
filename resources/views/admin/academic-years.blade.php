@extends('layouts.app')

@section('title', 'Academic Years — School Information System')

@section('page-title')
    <h2>Academic Years Management</h2>
@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════════════
   Academic Years Management Page Styles
   ══════════════════════════════════════════════════════════════════════════════ */

/* Container */
.ay-container { 
    max-width: 1000px; 
    margin: 0 auto; 
}

/* Header */
.ay-header { 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    margin-bottom: 24px; 
}
.ay-header h3 { 
    font-size: 16px; 
    font-weight: 600; 
    color: var(--dk-t1); 
    margin: 0; 
}

/* ── Form Card ──────────────────────────────────────────────────────────────── */
.ay-form-card { 
    background: var(--dk-surface);
    border: 1.5px solid var(--dk-b2);
    border-radius: var(--radius-lg);
    padding: 20px;
    margin-bottom: 24px;
    transition: border-color .15s;
}
.ay-form-card:focus-within { 
    border-color: rgba(96,165,250,0.4); 
}

.ay-form-content {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.ay-form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 200px;
}

.ay-form-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--dk-t3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ay-input {
    padding: 10px 14px;
    border: 1.5px solid var(--dk-b1);
    border-radius: var(--radius-md);
    font-size: 13px;
    background: var(--dk-surface2);
    color: var(--dk-t1);
    font-family: var(--font-body);
    transition: all .15s;
}

.ay-input:focus {
    outline: none;
    border-color: rgba(96,165,250,0.5);
    background: var(--dk-surface2);
    box-shadow: 0 0 0 3px rgba(96,165,250,0.1);
}

.ay-input::placeholder { 
    color: var(--dk-t4); 
}

.ay-add-btn {
    background: linear-gradient(135deg, #34d399, #10b981);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all .15s;
    height: 40px;
}

.ay-add-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #10b981, #059669);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}

.ay-add-btn:active:not(:disabled) { 
    transform: translateY(0); 
}

.ay-add-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ── Message Alert ──────────────────────────────────────────────────────────── */
.ay-msg {
    display: none;
    padding: 12px 16px;
    border-radius: var(--radius-md);
    margin-top: 12px;
    font-weight: 500;
    font-size: 13px;
    animation: slideDown .3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.ay-msg.success {
    background: rgba(16,185,129,0.15);
    color: #10b981;
    border: 1px solid rgba(16,185,129,0.3);
}

.ay-msg.error {
    background: rgba(239,68,68,0.15);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,0.3);
}

/* ── Empty State ────────────────────────────────────────────────────────────── */
.ay-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--dk-t4);
}

.ay-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 20px;
    opacity: 0.5;
}

.ay-empty h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dk-t2);
    margin-bottom: 8px;
}

.ay-empty p {
    font-size: 13px;
    color: var(--dk-t4);
}

/* ── Table Styles ───────────────────────────────────────────────────────────── */
.ay-table-wrapper {
    background: var(--dk-surface);
    border: 1.5px solid var(--dk-b1);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.ay-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.ay-table th {
    background: var(--dk-surface2);
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: var(--dk-t3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--dk-b1);
}

.ay-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--dk-b1);
    font-size: 13px;
    color: var(--dk-t1);
}

.ay-table tbody tr {
    transition: background .15s;
}

.ay-table tbody tr:hover {
    background: rgba(255,255,255,0.03);
}

.ay-table tbody tr:last-child td {
    border-bottom: none;
}

.ay-table th:first-child,
.ay-table td:first-child {
    width: 60px;
    text-align: center;
}

.ay-table th:last-child {
    text-align: right;
}

.ay-table td:last-child {
    text-align: right;
}

/* ── Badge ──────────────────────────────────────────────────────────────────── */
.ay-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.ay-badge.active {
    background: rgba(16,185,129,0.15);
    color: #10b981;
    border-color: rgba(16,185,129,0.3);
}

.ay-badge.active::before {
    content: '●';
    font-size: 6px;
}

.ay-badge.inactive {
    background: rgba(107,114,128,0.1);
    color: #9ca3af;
    border-color: rgba(107,114,128,0.2);
}

/* ── Actions ────────────────────────────────────────────────────────────────── */
.ay-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.ay-btn {
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.ay-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ay-btn-activate {
    background: rgba(16,185,129,0.15);
    color: #10b981;
    border-color: rgba(16,185,129,0.3);
}

.ay-btn-activate:hover:not(:disabled) {
    background: rgba(16,185,129,0.25);
    border-color: rgba(16,185,129,0.4);
}

.ay-btn-delete {
    background: rgba(239,68,68,0.15);
    color: #ef4444;
    border-color: rgba(239,68,68,0.3);
}

.ay-btn-delete:hover:not(:disabled) {
    background: rgba(239,68,68,0.25);
    border-color: rgba(239,68,68,0.4);
}

/* ── Loading indicator ──────────────────────────────────────────────────────── */
.ay-loading {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 2px solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin .6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ── Responsive ─────────────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .ay-form-content {
        flex-direction: column;
    }
    
    .ay-form-group {
        min-width: auto;
    }
    
    .ay-table {
        font-size: 12px;
    }
    
    .ay-table th,
    .ay-table td {
        padding: 10px 12px;
    }
    
    .ay-actions {
        flex-direction: column;
    }
    
    .ay-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="ay-container">
    
    {{-- Form Card --}}
    <div class="ay-form-card">
        <div class="ay-header">
            <h3>Add New Academic Year</h3>
        </div>

        <div class="ay-form-content">
            <div class="ay-form-group" style="flex: 1; min-width: 250px;">
                <label class="ay-form-label">Year Label</label>
                <input type="text" id="newYearInput" class="ay-input" 
                    placeholder="e.g. 2025–2026" 
                    maxlength="20"
                    aria-label="Academic year label">
            </div>

            <button id="addNewYearBtn" class="ay-add-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                Add Year
            </button>
        </div>

        <div id="ayMessage" class="ay-msg"></div>
    </div>

    {{-- Years Table --}}
    @if($years->count() === 0)
        <div class="ay-empty">
            <svg class="ay-empty-icon" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h3>No Academic Years Yet</h3>
            <p>Create your first academic year above to get started with your school system.</p>
        </div>
    @else
        <div class="ay-table-wrapper">
            <table class="ay-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($years as $index => $year)
                        <tr>
                            <td>{{ $year->id }}</td>
                            <td>
                                <div style="font-weight: 500; letter-spacing: 0.5px;">
                                    {{ $year->year_label }}
                                </div>
                            </td>
                            <td>
                                <span class="ay-badge {{ $year->is_active ? 'active' : 'inactive' }}">
                                    {{ $year->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($year->created_at)->format('M d, Y') }}</td>
                            <td>
                                <div class="ay-actions">
                                    @if(!$year->is_active)
                                        <button class="ay-btn ay-btn-activate btn-ay-set-active" 
                                            data-id="{{ $year->id }}" 
                                            data-label="{{ $year->year_label }}"
                                            title="Set this year as active">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Set Active
                                        </button>
                                    @else
                                        <button class="ay-btn ay-btn-activate" disabled title="This year is currently active">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Active
                                        </button>
                                    @endif

                                    @if(!$year->is_active)
                                        <button class="ay-btn ay-btn-delete btn-ay-delete" 
                                            data-id="{{ $year->id }}"
                                            title="Delete this year">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Delete
                                        </button>
                                    @else
                                        <button class="ay-btn ay-btn-delete" disabled title="Cannot delete the active year">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Configuration
    const URL_SET_ACTIVE = '{{ route("academic-years.setActive") }}';
    const URL_STORE = '{{ route("academic-years.store") }}';
    const URL_DESTROY = '{{ route("academic-years.destroy") }}';
    const LOADING_MODAL = '#loading-modal';
    const MSG_EL = '#ayMessage';

    /**
     * Display a message to the user with animation
     */
    function showMsg(text, type = 'success') {
        const $msg = $(MSG_EL);
        $msg.removeClass('success error')
            .addClass(type)
            .text(text)
            .slideDown(300);
        
        // Auto-hide after 4 seconds
        setTimeout(() => $msg.slideUp(300), 4000);
    }

    /**
     * Add new academic year
     */
    $('#addNewYearBtn').on('click', function() {
        const label = $('#newYearInput').val().trim();
        
        // Validation
        if (!label) {
            return showMsg('Please enter a year label (e.g., 2025–2026)', 'error');
        }

        // Validate format (YYYY–YYYY or YYYY-YYYY)
        if (!/^\d{4}[–-]\d{4}$/.test(label)) {
            return showMsg('Year label must be in format like 2025–2026 or 2025-2026', 'error');
        }

        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Disable button and show loading state
        $btn.prop('disabled', true).html('<span class="ay-loading"></span> Adding…');
        showLoading();

        // Send AJAX request
        $.ajax({
            url: URL_STORE,
            method: 'POST',
            dataType: 'json',
            data: {
                year_label: label,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status === 'success') {
                    showMsg(res.message);
                    // Reload page after a short delay to show success message
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showMsg(res.message || 'Failed to add year', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message ?? 'Failed to add year. Please try again.';
                showMsg(msg, 'error');
            },
            complete: function() {
                // Restore button
                $btn.prop('disabled', false).html(originalHtml);
                hideLoading();
                // Clear input and focus for next entry
                $('#newYearInput').val('').focus();
            }
        });
    });

    /**
     * Set academic year as active
     */
    $(document).on('click', '.btn-ay-set-active', function() {
        const id = $(this).data('id');
        const label = $(this).data('label');
        const $btn = $(this);
        
        // Confirmation dialog
        if (!confirm(`Set "${label}" as the active academic year?\n\nThis will deactivate the current year.`)) {
            return;
        }

        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="ay-loading"></span> Setting…');
        showLoading();

        $.ajax({
            url: URL_SET_ACTIVE,
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status === 'success') {
                    showMsg(res.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showMsg(res.message || 'Failed to update', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message ?? 'Failed to update year.';
                showMsg(msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
                hideLoading();
            }
        });
    });

    /**
     * Delete academic year
     */
    $(document).on('click', '.btn-ay-delete', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        
        // Confirmation dialog
        if (!confirm('Delete this academic year?\n\nThis action cannot be undone.')) {
            return;
        }

        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="ay-loading"></span> Deleting…');
        showLoading();

        $.ajax({
            url: URL_DESTROY,
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status === 'success') {
                    showMsg(res.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showMsg(res.message || 'Delete failed', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message ?? 'Failed to delete year.';
                showMsg(msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
                hideLoading();
            }
        });
    });

    /**
     * Press Enter to add year
     */
    $('#newYearInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#addNewYearBtn').click();
        }
    });
});
</script>
@endpush