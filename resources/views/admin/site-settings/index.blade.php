@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Site Settings</h1>
        <p class="mt-2 text-sm text-gray-600">Manage contact information and social media links displayed across the site.</p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.site-settings.update') }}" method="POST" x-data="{ tab: 'contact' }">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Tabs Header -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button type="button" @click="tab = 'contact'"
                        :class="tab === 'contact' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                        <i class="fas fa-address-book mr-2"></i>
                        Contact Information
                    </button>
                    <button type="button" @click="tab = 'social'"
                        :class="tab === 'social' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                        <i class="fas fa-share-alt mr-2"></i>
                        Social Media
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6 md:p-8">
                <!-- Contact Info Tab -->
                <div x-show="tab === 'contact'" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="contact_email" id="contact_email"
                            value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                            required
                            placeholder="info@beautyversity.id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" name="contact_phone" id="contact_phone"
                            value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                            required
                            placeholder="+62 123 456 7890"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea name="contact_address" id="contact_address"
                            required
                            rows="3"
                            placeholder="Bandung, Jawa Barat, Indonesia"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                        @error('contact_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Social Media Tab -->
                <div x-show="tab === 'social'" class="space-y-6">
                    <!-- Facebook -->
                    <div>
                        <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-facebook-f mr-2 text-primary-600"></i>
                            Facebook URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_facebook" id="social_facebook"
                            value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                            placeholder="https://facebook.com/beautyversitydotid"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_facebook')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Twitter/X -->
                    <div>
                        <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-twitter mr-2 text-primary-600"></i>
                            Twitter/X URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_twitter" id="social_twitter"
                            value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                            placeholder="https://x.com/beautyversityid"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_twitter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instagram -->
                    <div>
                        <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-instagram mr-2 text-primary-600"></i>
                            Instagram URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_instagram" id="social_instagram"
                            value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                            placeholder="https://instagram.com/beautyversity_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- YouTube -->
                    <div>
                        <label for="social_youtube" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-youtube mr-2 text-primary-600"></i>
                            YouTube URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_youtube" id="social_youtube"
                            value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                            placeholder="https://youtube.com/@beautyversitydotid"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_youtube')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TikTok -->
                    <div>
                        <label for="social_tiktok" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-tiktok mr-2 text-primary-600"></i>
                            TikTok URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_tiktok" id="social_tiktok"
                            value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}"
                            placeholder="https://tiktok.com/@beautyversity"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_tiktok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp Business -->
                    <div>
                        <label for="social_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-whatsapp mr-2 text-primary-600"></i>
                            WhatsApp Business URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_whatsapp" id="social_whatsapp"
                            value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}"
                            placeholder="https://wa.me/62123456789"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_whatsapp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- LinkedIn -->
                    <div>
                        <label for="social_linkedin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-linkedin-in mr-2 text-primary-600"></i>
                            LinkedIn URL <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="social_linkedin" id="social_linkedin"
                            value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                            placeholder="https://linkedin.com/company/beautyversity"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('social_linkedin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
