@extends('layouts.admin')

@section('title', 'Manage Share Domains')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div class="flex-1">
        </div>
        <a href="{{ route('admin.share-domains.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
            <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
            Add New Domain
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
            @if(session('new_api_key'))
                <div class="mt-3 p-3 bg-white rounded border border-green-300">
                    <p class="font-semibold mb-2">New API Key (Save this now!):</p>
                    <div class="flex gap-2">
                        <code class="flex-1 px-3 py-2 bg-gray-50 rounded text-xs break-all">{{ session('new_api_key') }}</code>
                        <button onclick="copyToClipboard('{{ session('new_api_key') }}')" 
                                class="px-3 py-2 bg-primary-600 text-white rounded text-xs hover:bg-primary-700">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Domain Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Webhook URL</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">API Key</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Created</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($shareDomains as $domain)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $domain->domain_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 max-w-xs truncate" title="{{ $domain->webhook_url }}">
                                    {{ $domain->webhook_url }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($domain->api_key)
                                    <code class="text-xs text-gray-600">{{ Str::limit($domain->api_key, 12) }}...</code>
                                @else
                                    <span class="text-sm text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($domain->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $domain->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.share-domains.show', $domain->id) }}" class="text-gray-500 hover:text-gray-800">View</a>
                                <a href="{{ route('admin.share-domains.edit', $domain->id) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                <form action="{{ route('admin.share-domains.destroy', $domain->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this domain?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                No share domains found. Click "Add New Domain" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $shareDomains->links() }}
    </div>

@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('API Key copied to clipboard!');
    });
}
</script>
@endpush