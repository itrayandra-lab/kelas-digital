@extends('layouts.admin')

@section('title', 'Edit Share Domain')

@section('content')

    <form action="{{ route('admin.share-domains.update', $shareDomain->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
            <div>
                <a href="{{ route('admin.share-domains.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
                    Back to Share Domains
                </a>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                @if($shareDomain->status === 'active')
                    <form action="{{ route('admin.share-domains.deactivate', $shareDomain->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-yellow-600 text-white font-semibold text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-pause mr-2"></i>Deactivate
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.share-domains.activate', $shareDomain->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white font-semibold text-sm rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>Activate
                        </button>
                    </form>
                @endif
                
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                    <i class="fas fa-save mr-2"></i>
                    Update Domain
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 md:p-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Domain Information</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="domain_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Domain Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="domain_name" 
                                       id="domain_name" 
                                       value="{{ old('domain_name', $shareDomain->domain_name) }}" 
                                       placeholder="e.g., kangwendra.com"
                                       required
                                       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <p class="mt-1 text-xs text-gray-500">Enter domain without http:// or https://</p>
                                @error('domain_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="webhook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    Webhook URL <span class="text-red-500">*</span>
                                </label>
                                <input type="url" 
                                       name="webhook_url" 
                                       id="webhook_url" 
                                       value="{{ old('webhook_url', $shareDomain->webhook_url) }}" 
                                       placeholder="https://kangwendra.com/api/webhook"
                                       required
                                       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <p class="mt-1 text-xs text-gray-500">Full URL endpoint for webhook notifications</p>
                                @error('webhook_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                    API Key <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="api_key" 
                                       id="api_key" 
                                       value="{{ old('api_key', $shareDomain->api_key) }}"
                                       placeholder="API Key"
                                       required
                                       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <p class="mt-1 text-xs text-gray-500">API key for authentication</p>
                                @error('api_key')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" 
                                        id="status" 
                                        required
                                        class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    <option value="active" {{ old('status', $shareDomain->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $shareDomain->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                            <i class="fas fa-save mr-2"></i>
                            Update Domain
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Domain Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">ID</dt>
                            <dd class="font-medium text-gray-900">{{ $shareDomain->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                @if($shareDomain->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Created</dt>
                            <dd class="font-medium text-gray-900">{{ $shareDomain->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Last Updated</dt>
                            <dd class="font-medium text-gray-900">{{ $shareDomain->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.share-domains.show', $shareDomain->id) }}" 
                           class="block w-full text-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection