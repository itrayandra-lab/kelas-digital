@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Share Domain Details</h1>
        <div>
            <a href="{{ route('admin.share-domains.edit', $shareDomain->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.share-domains.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Information</h6>
                    {!! $shareDomain->status_badge !!}
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%" class="bg-light">ID</th>
                                <td>{{ $shareDomain->id }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Domain Name</th>
                                <td>
                                    <strong class="text-primary">{{ $shareDomain->domain_name }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Webhook URL</th>
                                <td>
                                    <a href="{{ $shareDomain->webhook_url }}" target="_blank" rel="noopener">
                                        {{ $shareDomain->webhook_url }}
                                        <i class="fas fa-external-link-alt fa-sm"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">API Key</th>
                                <td>
                                    @if($shareDomain->api_key)
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="apiKeyField" 
                                                   value="{{ $shareDomain->api_key }}" 
                                                   readonly>
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="toggleApiKey()">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="copyApiKey()">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted">No API key set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status</th>
                                <td>
                                    {!! $shareDomain->status_badge !!}
                                    <span class="text-muted">
                                        ({{ $shareDomain->isActive() ? 'Domain is active and operational' : 'Domain is currently inactive' }})
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Created At</th>
                                <td>
                                    {{ $shareDomain->created_at->format('F d, Y \a\t H:i') }}
                                    <span class="text-muted">({{ $shareDomain->created_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Last Updated</th>
                                <td>
                                    {{ $shareDomain->updated_at->format('F d, Y \a\t H:i') }}
                                    <span class="text-muted">({{ $shareDomain->updated_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Log Card (placeholder for future implementation) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Activity logging coming soon...</p>
                        <small class="text-muted">This section will show webhook calls, status changes, and other domain activities.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.share-domains.edit', $shareDomain->id) }}" 
                       class="btn btn-primary btn-block w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Domain
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

                    <form action="{{ route('admin.share-domains.regenerate-api-key', $shareDomain->id) }}" 
                          method="POST" 
                          class="mb-2"
                          onsubmit="return confirm('Are you sure you want to regenerate the API key? The current key will stop working.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning btn-block w-100">
                            <i class="fas fa-key"></i> Regenerate API Key
                        </button>
                    </form>

                    <hr>

                    <form action="{{ route('admin.share-domains.destroy', $shareDomain->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Are you absolutely sure you want to delete this domain? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block w-100">
                            <i class="fas fa-trash"></i> Delete Domain
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6 border-end">
                                <h4 class="text-primary mb-0">
                                    <i class="fas fa-calendar-alt"></i>
                                </h4>
                                <p class="text-muted mb-0 small">Age</p>
                                <p class="font-weight-bold">{{ $shareDomain->created_at->diffForHumans(null, true) }}</p>
                            </div>
                            <div class="col-6">
                                <h4 class="{{ $shareDomain->isActive() ? 'text-success' : 'text-secondary' }} mb-0">
                                    <i class="fas fa-{{ $shareDomain->isActive() ? 'check-circle' : 'times-circle' }}"></i>
                                </h4>
                                <p class="text-muted mb-0 small">Status</p>
                                <p class="font-weight-bold">{{ ucfirst($shareDomain->status) }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="small text-muted">
                        <p class="mb-1">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Last Modified:</strong><br>
                            {{ $shareDomain->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 bg-light">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Integration Code</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Use this code to integrate with your application:</p>
                    
                    <div class="mb-3">
                        <label class="small text-muted">Domain:</label>
                        <pre class="bg-white p-2 border rounded"><code>{{ $shareDomain->domain_name }}</code></pre>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Webhook Endpoint:</label>
                        <pre class="bg-white p-2 border rounded"><code>{{ $shareDomain->webhook_url }}</code></pre>
                    </div>

                    @if($shareDomain->api_key)
                        <div class="mb-0">
                            <label class="small text-muted">API Key:</label>
                            <pre class="bg-white p-2 border rounded"><code id="apiKeyCode">{{ Str::limit($shareDomain->api_key, 20) }}...</code></pre>
                            <button class="btn btn-sm btn-outline-primary" onclick="showFullApiKey()">
                                <i class="fas fa-eye"></i> Show Full Key
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isApiKeyVisible = false;

function toggleApiKey() {
    const field = document.getElementById('apiKeyField');
    const icon = document.getElementById('toggleIcon');
    
    if (isApiKeyVisible) {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    } else {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    }
    
    isApiKeyVisible = !isApiKeyVisible;
}

function copyApiKey() {
    const field = document.getElementById('apiKeyField');
    field.type = 'text';
    field.select();
    document.execCommand('copy');
    field.type = 'password';
    
    alert('API Key copied to clipboard!');
}

function showFullApiKey() {
    const codeElement = document.getElementById('apiKeyCode');
    codeElement.textContent = '{{ $shareDomain->api_key }}';
    event.target.remove();
}
</script>
@endpush
@endsection