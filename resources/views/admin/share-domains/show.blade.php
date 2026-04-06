@extends('layouts.admin')

@section('title', 'View Share Domain')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
        <div>
            <a href="{{ route('admin.share-domains.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
                Back to Share Domains
            </a>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.share-domains.edit', $shareDomain->id) }}"
                class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                <i class="fas fa-edit mr-2"></i>
                Edit Domain
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Domain Information</h2>
                    @if($shareDomain->status === 'active')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-times-circle mr-2"></i>Inactive
                        </span>
                    @endif
                </div>

                <div class="p-6 md:p-8">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $shareDomain->id }}</dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Domain Name</dt>
                            <dd class="text-sm font-semibold text-primary-600 col-span-2">{{ $shareDomain->domain_name }}</dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Webhook URL</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                <a href="{{ $shareDomain->webhook_url }}" target="_blank" class="text-primary-600 hover:text-primary-800 break-all">
                                    {{ $shareDomain->webhook_url }}
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">API Key</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                @if($shareDomain->api_key)
                                    <div class="flex gap-2 items-center" x-data="{ show: false }">
                                        <input :type="show ? 'text' : 'password'" 
                                               value="{{ $shareDomain->api_key }}" 
                                               readonly
                                               class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs font-mono">
                                        <button type="button" 
                                                @click="show = !show"
                                                class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                        <button type="button" 
                                                onclick="copyToClipboard('{{ $shareDomain->api_key }}')"
                                                class="px-3 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">No API key set</span>
                                @endif
                            </dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                @if($shareDomain->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span class="text-gray-500 ml-2">(Domain is operational)</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                    <span class="text-gray-500 ml-2">(Domain is disabled)</span>
                                @endif
                            </dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                {{ $shareDomain->created_at->format('F d, Y \a\t H:i') }}
                                <span class="text-gray-500">({{ $shareDomain->created_at->diffForHumans() }})</span>
                            </dd>
                        </div>

                        <div class="py-4 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                {{ $shareDomain->updated_at->format('F d, Y \a\t H:i') }}
                                <span class="text-gray-500">({{ $shareDomain->updated_at->diffForHumans() }})</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Integration Code -->
            <div class="mt-6 bg-gray-50 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-code text-primary-600 mr-2"></i>Integration Code
                </h3>
                <p class="text-sm text-gray-600 mb-4">Use this code to integrate with your application:</p>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Domain:</label>
                        <pre class="mt-1 p-3 bg-white border border-gray-200 rounded text-xs font-mono overflow-x-auto">{{ $shareDomain->domain_name }}</pre>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Webhook Endpoint:</label>
                        <pre class="mt-1 p-3 bg-white border border-gray-200 rounded text-xs font-mono overflow-x-auto">{{ $shareDomain->webhook_url }}</pre>
                    </div>

                    @if($shareDomain->api_key)
                        <div x-data="{ showFull: false }">
                            <label class="text-xs font-medium text-gray-500 uppercase">API Key:</label>
                            <pre class="mt-1 p-3 bg-white border border-gray-200 rounded text-xs font-mono overflow-x-auto" 
                                 x-text="showFull ? '{{ $shareDomain->api_key }}' : '{{ Str::limit($shareDomain->api_key, 20) }}...'"></pre>
                            <button type="button" 
                                    @click="showFull = !showFull"
                                    class="mt-2 text-xs text-primary-600 hover:text-primary-800">
                                <i class="fas" :class="showFull ? 'fa-eye-slash' : 'fa-eye'"></i>
                                <span x-text="showFull ? 'Hide' : 'Show'"></span> Full Key
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.share-domains.edit', $shareDomain->id) }}" 
                       class="block w-full text-center px-4 py-2.5 bg-primary-600 text-white font-medium text-sm rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Domain
                    </a>

                    @if($shareDomain->status === 'active')
                        <form action="{{ route('admin.share-domains.deactivate', $shareDomain->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="block w-full px-4 py-2.5 bg-yellow-600 text-white font-medium text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-pause mr-2"></i>Deactivate Domain
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.share-domains.activate', $shareDomain->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="block w-full px-4 py-2.5 bg-green-600 text-white font-medium text-sm rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Activate Domain
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.share-domains.regenerate-api-key', $shareDomain->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Are you sure? The current key will stop working.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="block w-full px-4 py-2.5 bg-orange-600 text-white font-medium text-sm rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-key mr-2"></i>Regenerate API Key
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-gray-50 rounded">
                        <div class="text-2xl font-bold text-primary-600">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Age</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $shareDomain->created_at->diffForHumans(null, true) }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <div class="text-2xl font-bold {{ $shareDomain->status === 'active' ? 'text-green-600' : 'text-gray-400' }}">
                            <i class="fas fa-{{ $shareDomain->status === 'active' ? 'check-circle' : 'times-circle' }}"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Status</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ ucfirst($shareDomain->status) }}</p>
                    </div>
                </div>
            </div>
        </div>
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