@extends('layouts.app')

@section('title', 'Backup Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-database me-2"></i>
                    Backup Management
                </h2>
                <button class="btn btn-primary" onclick="createBackup()">
                    <i class="fas fa-plus-circle me-2"></i>
                    Create Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="backupTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="backups-tab" data-bs-toggle="tab" data-bs-target="#backups" type="button" role="tab">
                <i class="fas fa-list me-2"></i>
                Existing Backups
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
                <i class="fas fa-plus me-2"></i>
                Create Backup
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="import-export-tab" data-bs-toggle="tab" data-bs-target="#import-export" type="button" role="tab">
                <i class="fas fa-exchange-alt me-2"></i>
                Import/Export
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                <i class="fas fa-cog me-2"></i>
                Settings
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="backupTabContent">
        <!-- Existing Backups Tab -->
        <div class="tab-pane fade show active" id="backups" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-archive me-2"></i>
                        Backup Files
                    </h5>
                </div>
                <div class="card-body">
                    @if(empty($backups))
                    <div class="text-center py-5">
                        <i class="fas fa-database text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No backups found</h5>
                        <p class="text-muted">Create your first backup to get started</p>
                        <button class="btn btn-primary" onclick="$('#create-tab').click()">
                            Create Backup
                        </button>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                <tr>
                                    <td>
                                        <i class="fas fa-file-{{ $backup['type'] === 'sql' ? 'database' : ($backup['type'] === 'xml' ? 'code' : ($backup['type'] === 'excel' ? 'excel' : 'archive')) }} me-2"></i>
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $backup['type'] === 'sql' ? 'primary' : ($backup['type'] === 'xml' ? 'info' : ($backup['type'] === 'excel' ? 'success' : 'secondary')) }}">
                                            {{ strtoupper($backup['type']) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                    <td>{{ $backup['created_at']->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ $backup['download_url'] }}" class="btn btn-sm btn-outline-primary" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('{{ $backup['filename'] }}')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Create Backup Tab -->
        <div class="tab-pane fade" id="create" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create New Backup
                    </h5>
                </div>
                <div class="card-body">
                    <form id="createBackupForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Backup Format</label>
                                    <select class="form-select" name="format" required>
                                        <option value="sql">SQL Database Dump</option>
                                        <option value="xml">XML Export</option>
                                        <option value="excel">Excel/CSV Export</option>
                                        <option value="full">Full System Backup</option>
                                    </select>
                                    <div class="form-text">Choose the backup format based on your needs</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Specific Tables (Optional)</label>
                                    <input type="text" class="form-control" name="tables" placeholder="users,products,sales">
                                    <div class="form-text">Leave empty to backup all tables</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="compress" id="compress">
                                        <label class="form-check-label" for="compress">
                                            Compress backup file
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="cloud_upload" id="cloud_upload">
                                        <label class="form-check-label" for="cloud_upload">
                                            Upload to cloud storage
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database me-2"></i>
                                Create Backup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import/Export Tab -->
        <div class="tab-pane fade" id="import-export" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-export me-2"></i>
                                Export Data
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="exportForm">
                                <div class="mb-3">
                                    <label class="form-label">Export Format</label>
                                    <select class="form-select" name="format" required>
                                        <option value="xml">XML</option>
                                        <option value="excel">Excel/CSV</option>
                                        <option value="json">JSON</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tables to Export</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tables[]" value="products" id="exp_products" checked>
                                        <label class="form-check-label" for="exp_products">Products</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tables[]" value="customers" id="exp_customers" checked>
                                        <label class="form-check-label" for="exp_customers">Customers</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tables[]" value="sales" id="exp_sales" checked>
                                        <label class="form-check-label" for="exp_sales">Sales</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tables[]" value="categories" id="exp_categories" checked>
                                        <label class="form-check-label" for="exp_categories">Categories</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>
                                    Export Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-import me-2"></i>
                                Import Data
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="importForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Import File</label>
                                    <input type="file" class="form-control" name="import_file" accept=".xml,.xlsx,.csv,.json" required>
                                    <div class="form-text">Supported formats: XML, Excel, CSV, JSON</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Import Type</label>
                                    <select class="form-select" name="import_type" required>
                                        <option value="replace">Replace existing data</option>
                                        <option value="merge">Merge with existing data</option>
                                        <option value="append">Append to existing data</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> Importing data will modify your database. Make sure to create a backup first.
                                </div>
                                
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-upload me-2"></i>
                                    Import Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Backup Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Backup Retention (Days)</label>
                                    <input type="number" class="form-control" name="backup_retention_days" value="{{ $settings['retention_days'] }}" min="1" max="365" required>
                                    <div class="form-text">How long to keep backup files</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cloud Storage</label>
                                    <select class="form-select" name="backup_cloud_disk">
                                        <option value="">None</option>
                                        <option value="s3" {{ $settings['cloud_disk'] === 's3' ? 'selected' : '' }}>Amazon S3</option>
                                        <option value="google" {{ $settings['cloud_disk'] === 'google' ? 'selected' : '' }}>Google Drive</option>
                                        <option value="dropbox" {{ $settings['cloud_disk'] === 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="backup_auto_cloud" id="backup_auto_cloud" {{ $settings['auto_cloud'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="backup_auto_cloud">
                                            Auto-upload to cloud
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Notification Email</label>
                                    <input type="email" class="form-control" name="backup_notification_email" value="{{ $settings['notification_email'] }}" placeholder="admin@example.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Low Storage Threshold (MB)</label>
                            <input type="number" class="form-control" name="backup_low_storage_threshold" value="{{ $settings['low_storage_threshold'] }}" min="100">
                            <div class="form-text">Alert when storage space is low</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 id="loadingText">Processing...</h5>
                <p class="text-muted mb-0">Please wait while we process your request</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Create backup form
    $('#createBackupForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $('#loadingModal').modal('show');
        $('#loadingText').text('Creating backup...');
        
        $.ajax({
            url: '{{ route("backups.create") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                
                const response = xhr.responseJSON;
                Swal.fire({
                    title: 'Error!',
                    text: response?.message || 'An error occurred while creating backup',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Export form
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $('#loadingModal').modal('show');
        $('#loadingText').text('Exporting data...');
        
        $.ajax({
            url: '{{ route("backups.export") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                $('#loadingModal').modal('hide');
                
                const filename = xhr.getResponseHeader('Content-Disposition')?.split('filename=')[1] || 'export.csv';
                const url = window.URL.createObjectURL(new Blob([data]));
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Data exported successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                
                const response = xhr.responseJSON;
                Swal.fire({
                    title: 'Error!',
                    text: response?.message || 'An error occurred while exporting data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Import form
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $('#loadingModal').modal('show');
        $('#loadingText').text('Importing data...');
        
        $.ajax({
            url: '{{ route("backups.import") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message + (response.result ? ` (${response.result.imported_records} records)` : ''),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#importForm')[0].reset();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                
                const response = xhr.responseJSON;
                Swal.fire({
                    title: 'Error!',
                    text: response?.message || 'An error occurred while importing data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Settings form
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("backups.settings.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    title: 'Error!',
                    text: response?.message || 'An error occurred while updating settings',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

// Quick create backup function
function createBackup() {
    $('#create-tab').click();
}

// Delete backup function
function deleteBackup(filename) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This backup will be permanently deleted',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("backups.delete", ":filename") }}'.replace(':filename', filename),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error!',
                        text: response?.message || 'An error occurred while deleting backup',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
@endpush 