@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Share Domain</h1>
        <a href="{{ route('admin.share-domains.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.share-domains.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('domain_name') is-invalid @enderror" 
                                   id="domain_name" 
                                   name="domain_name" 
                                   value="{{ old('domain_name') }}" 
                                   placeholder="e.g., example.com"
                                   required>
                            @error('domain_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter the domain name without http:// or https://</small>
                        </div>

                        <div class="mb-3">
                            <label for="webhook_url" class="form-label">Webhook URL <span class="text-danger">*</span></label>
                            <input type="url" 
                                   class="form-control @error('webhook_url') is-invalid @enderror" 
                                   id="webhook_url" 
                                   name="webhook_url" 
                                   value="{{ old('webhook_url') }}" 
                                   placeholder="https://example.com/webhook"
                                   required>
                            @error('webhook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Full URL where webhooks will be sent</small>
                        </div>

                        <div class="mb-3">
                            <label for="api_key" class="form-label">API Key</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('api_key') is-invalid @enderror" 
                                       id="api_key" 
                                       name="api_key" 
                                       value="{{ old('api_key') }}" 
                                       placeholder="Leave empty to auto-generate">
                                <button class="btn btn-outline-secondary" type="button" onclick="generateApiKey()">
                                    <i class="fas fa-sync"></i> Generate
                                </button>
                            </div>
                            @error('api_key')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">64-character API key. Will be auto-generated if left empty.</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Set domain status</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.share-domains.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Share Domain
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Help</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Domain Name</h6>
                    <p class="small text-muted">Enter the domain without protocol (http/https). Example: example.com or subdomain.example.com</p>

                    <h6 class="font-weight-bold mt-3">Webhook URL</h6>
                    <p class="small text-muted">The complete URL endpoint that will receive webhook notifications from this domain.</p>

                    <h6 class="font-weight-bold mt-3">API Key</h6>
                    <p class="small text-muted">A secure key used for authentication. Click "Generate" to create a random 64-character key, or leave empty for auto-generation.</p>

                    <h6 class="font-weight-bold mt-3">Status</h6>
                    <p class="small text-muted">
                        <strong>Active:</strong> Domain is operational<br>
                        <strong>Inactive:</strong> Domain is disabled
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateApiKey() {
    // Generate a random 64-character hex string
    const array = new Uint8Array(32);
    crypto.getRandomValues(array);
    const apiKey = Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    
    document.getElementById('api_key').value = apiKey;
}
</script>
@endpush
@endsection