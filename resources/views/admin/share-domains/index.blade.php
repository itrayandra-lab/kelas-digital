@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Share Domains</h1>
        <a href="{{ route('admin.share-domains.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Domain
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Share Domains</h6>
        </div>
        <div class="card-body">
            @if($shareDomains->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Domain Name</th>
                                <th width="25%">Webhook URL</th>
                                <th width="15%">API Key</th>
                                <th width="10%">Status</th>
                                <th width="15%">Created At</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shareDomains as $domain)
                                <tr>
                                    <td>{{ $domain->id }}</td>
                                    <td>
                                        <strong>{{ $domain->domain_name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($domain->webhook_url, 40) }}</small>
                                    </td>
                                    <td>
                                        @if($domain->api_key)
                                            <code class="small">{{ Str::limit($domain->api_key, 12) }}...</code>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>{!! $domain->status_badge !!}</td>
                                    <td>{{ $domain->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.share-domains.show', $domain->id) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.share-domains.edit', $domain->id) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="confirmDelete({{ $domain->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-{{ $domain->id }}" 
                                              action="{{ route('admin.share-domains.destroy', $domain->id) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $shareDomains->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No share domains found. Create your first one!</p>
                    <a href="{{ route('admin.share-domains.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Domain
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this share domain? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
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