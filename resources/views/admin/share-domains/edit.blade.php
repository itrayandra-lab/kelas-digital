@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Share Domain</h1>
        <a href="{{ route('admin.share-domains.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            @if(session('new_api_key'))
                <hr>
                <strong>New API Key:</strong>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="{{ session('new_api_key') }}" id="apiKeyDisplay" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
                <small class="text-muted">Please save this API key now. You won't be able to see it again!</small>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Information</h6>
                    <div>
                        @if($shareDomain->isActive())
                            <form action="{{ route('admin.share-domains.deactivate', $shareDomain->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="fas fa-pause"></i> Deactivate
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.share-domains.activate', $shareDomain->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-play"></i> Activate
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.share-domains.update', $shareDomain->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('domain_name') is-invalid @enderror" 
                                   id="domain_name" 
                                   name="domain_name" 
                                   value="{{ old('domain_name', $shareDomain->domain_name) }}" 
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
                                   value="{{ old('webhook_url', $shareDomain->webhook_url) }}" 
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
                                       value="{{ old('api_key', $shareDomain->api_key) }}" 
                                       placeholder="API Key">
                                <button class="btn btn-outline-secondary" type="button" onclick="generateApiKey()">
                                    <i class="fas fa-sync"></i> Generate New
                                </button>
                            </div>
                            @error('api_key')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">64-character API key for authentication</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Alternative:</strong> You can also regenerate the API key using the button below without changing other fields.
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', $shareDomain->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $shareDomain->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <i class="fas fa-save"></i> Update Share Domain
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Regenerate API Key Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Regenerate API Key</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-exclamation-triangle text-warning"></i> 
                        This will generate a completely new API key and invalidate the current one. 
                        Make sure to update any applications using the old key.
                    </p>
                    <form action="{{ route('admin.share-domains.regenerate-api-key', $shareDomain->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to regenerate the API key? The current key will stop working immediately.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Regenerate API Key
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card shadow mb-4 border-danger">
                <div class="card-header py-3 bg-danger">
                    <h6 class="m-0 font-weight-bold text-white">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-exclamation-circle text-danger"></i> 
                        Once you delete this share domain, there is no going back. Please be certain.
                    </p>
                    <form action="{{ route('admin.share-domains.destroy', $shareDomain->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you absolutely sure? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete This Share Domain
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $shareDomain->id }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>{!! $shareDomain->status_badge !!}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $shareDomain->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td>{{ $shareDomain->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.share-domains.show', $shareDomain->id) }}" class="btn btn-info btn-block w-100 mb-2">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    
                    @if($shareDomain->isActive())
                        <form action="{{ route('admin.share-domains.deactivate', $shareDomain->id) }}" 
                              method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-pause"></i> Deactivate Domain
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.share-domains.activate', $shareDomain->id) }}" 
                              method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-block w-100">
                                <i class="fas fa-play"></i> Activate Domain
                            </button>
                        </form>
                    @endif
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

function copyApiKey() {
    const apiKeyInput = document.getElementById('apiKeyDisplay');
    apiKeyInput.select();
    document.execCommand('copy');
    
    alert('API Key copied to clipboard!');
}
</script>
@endpush
@endsection