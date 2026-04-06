@extends('layouts.admin')

@section('title', 'Add New Share Domain')

@section('content')

    <form action="{{ route('admin.share-domains.store') }}" method="POST">
        @csrf
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
            <div>
                <a href="{{ route('admin.share-domains.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
                    Back to Share Domains
                </a>
            </div>
            <div class="mt-4 sm:mt-0">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                    <i class="fas fa-save mr-2"></i>
                    Create Share Domain
                </button>
            </div>
        </div>

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
                                       value="{{ old('domain_name') }}" 
                                       placeholder="e.g., kangwendra.com"
                                       required
                                       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <p class="mt-1 text-xs text-gray-500">Enter domain without http:// or https:// (e.g., example.com or subdomain.example.com)</p>
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
                                       value="{{ old('webhook_url') }}" 
                                       placeholder="https://kangwendra.com/api/webhook"
                                       required
                                       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <p class="mt-1 text-xs text-gray-500">Full URL endpoint that will receive webhook notifications</p>
                                @error('webhook_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ apiKey: '{{ old('api_key') }}' }">
                                <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                    API Key
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           name="api_key" 
                                           id="api_key" 
                                           x-model="apiKey"
                                           placeholder="Leave empty to auto-generate"
                                           class="flex-1 block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    <button type="button" 
                                            @click="apiKey = Array.from(crypto.getRandomValues(new Uint8Array(32)), byte => byte.toString(16).padStart(2, '0')).join('')"
                                            class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-sync mr-2"></i>Generate
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">64-character API key. Will be auto-generated if left empty.</p>
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
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Set domain status (Active = operational, Inactive = disabled)</p>
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
                            Create Share Domain
                        </button>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-primary-600 mr-2"></i>Help
                    </h3>
                    
                    <div class="space-y-4 text-sm">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-1">Domain Name</h4>
                            <p class="text-gray-600">Enter domain without protocol (http/https).</p>
                            <p class="text-xs text-gray-500 mt-1">
                                ✅ example.com<br>
                                ✅ subdomain.example.com<br>
                                ❌ https://example.com
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-700 mb-1">Webhook URL</h4>
                            <p class="text-gray-600">Complete URL endpoint for webhook notifications.</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Example:<br>
                                https://kangwendra.com/api/webhook
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-700 mb-1">API Key</h4>
                            <p class="text-gray-600">Secure key for authentication. Click "Generate" for random key.</p>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated if left empty</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-700 mb-1">Status</h4>
                            <p class="text-gray-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mb-1">Active</span> - Domain is operational<br>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactive</span> - Domain is disabled
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection