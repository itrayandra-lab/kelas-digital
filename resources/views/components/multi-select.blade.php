<div x-data="{
        open: false,
        search: '',
        selected: {{ Js::encode($selected ?? []) }},
        options: {{ Js::encode($options ?? []) }},
        name: '{{ $name }}',
        placeholder: '{{ $placeholder ?? 'Pilih...' }}',
        required: {{ $required ?? false ? 'true' : 'false' }},
        allowCreate: {{ $allowCreate ?? false ? 'true' : 'false' }},
        newTagPlaceholder: '{{ $newTagPlaceholder ?? 'Ketik untuk membuat...' }}',

        get filteredOptions() {
            if (!this.search) {
                return this.options.filter(opt => !this.selected.includes(opt.id) && !this.selected.includes(String(opt.id)));
            }
            const searchLower = this.search.toLowerCase();
            return this.options.filter(opt =>
                opt.name.toLowerCase().includes(searchLower) &&
                !this.selected.includes(opt.id) &&
                !this.selected.includes(String(opt.id))
            );
        },

        get hasSelection() {
            return this.selected.length > 0;
        },

        toggle(id) {
            const strId = String(id);
            const numId = Number(id);
            if (this.selected.includes(strId) || this.selected.includes(numId)) {
                this.selected = this.selected.filter(s => String(s) !== strId && Number(s) !== numId);
            } else {
                this.selected = [...this.selected, id];
            }
        },

        remove(id) {
            const strId = String(id);
            const numId = Number(id);
            this.selected = this.selected.filter(s => String(s) !== strId && Number(s) !== numId);
        },

        createNewTag() {
            const name = this.search.trim();
            if (!name) return;

            const existing = this.options.find(o => o.name.toLowerCase() === name.toLowerCase());
            if (existing) {
                if (!this.selected.includes(existing.id) && !this.selected.includes(String(existing.id))) {
                    this.selected = [...this.selected, existing.id];
                }
                this.search = '';
                return;
            }

            if (!this.selected.includes(name)) {
                this.selected = [...this.selected, name];
            }
            this.search = '';
        },

        closeDropdown() {
            this.open = false;
            this.search = '';
        },

        getDisplayName(item) {
            const opt = this.options.find(o => o.id == item || o.id === item);
            return opt ? opt.name : item;
        }
    }"
    @keydown.escape="closeDropdown()"
    class="relative"
>

    <!-- Hidden inputs for form submission -->
    <template x-for="(item, index) in selected" :key="index">
        <input type="hidden" :name=\"name + '[]'\" :value=\"item\">
    </template>

    <!-- Label -->
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        <span x-show="required" class="text-red-500">*</span>
    </label>

    <!-- Multi-select trigger -->
    <div
        @click="open = !open"
        @click.away="closeDropdown()"
        :class="open ? 'ring-2 ring-primary-500 border-primary-500' : 'border-gray-300 hover:border-gray-400'"
        class="w-full min-h-[52px] px-3 py-2.5 bg-white border rounded-lg cursor-pointer transition-all duration-200 flex flex-wrap gap-2 items-center"
    >
        <!-- Selected chips -->
        <template x-if="hasSelection">
            <div class="flex flex-wrap gap-1.5">
                <template x-for="(item, index) in selected" :key="index">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary-100 text-primary-700 text-sm font-medium rounded-md group">
                        <span x-text="getDisplayName(item)"></span>
                        <button
                            type="button"
                            @click.stop="remove(item)"
                            class="w-4 h-4 flex items-center justify-center rounded-full hover:bg-primary-200 transition-colors"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                </template>
            </div>
        </template>

        <!-- Placeholder -->
        <span
            x-show="!hasSelection"
            class="text-gray-400 text-sm"
            x-text="placeholder"
        ></span>

        <!-- Dropdown arrow -->
        <div class="ml-auto flex-shrink-0">
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
        style="display: none;"
    >
        <!-- Search/Create input -->
        <div class="p-3 border-b border-gray-100 bg-gray-50/50">
            <input
                type="text"
                x-model="search"
                :placeholder="allowCreate ? newTagPlaceholder : 'Cari pilihan...'"
                @keydown.enter.prevent="allowCreate && search.trim() && createNewTag()"
                @keydown.comma.prevent="allowCreate && search.trim() && createNewTag()"
                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white"
            >
            <p x-show="allowCreate && search.trim()" class="mt-2 text-xs text-gray-500">
                Tekan <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs font-medium">Enter</kbd> atau <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs font-medium">,</kbd> untuk membuat tag baru
            </p>
        </div>

        <!-- Options list -->
        <div class="overflow-y-auto" style="max-height: 240px;">
            <!-- Create new option -->
            <template x-if="allowCreate && search.trim() && !options.some(o => o.name.toLowerCase() === search.toLowerCase())">
                <button
                    type="button"
                    @click="createNewTag()"
                    class="w-full px-4 py-3 text-left hover:bg-primary-50 transition-colors flex items-center gap-3 border-b border-gray-100"
                >
                    <span class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                    <span class="text-sm text-primary-700 font-medium">Buat "<span x-text="search"></span>"</span>
                </button>
            </template>

            <!-- Empty state -->
            <template x-if="filteredOptions.length === 0 && filteredOptions.length === 0">
                <div class="px-4 py-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-500" x-text="search ? 'Tidak ada hasil' : 'Tidak ada pilihan'"></p>
                </div>
            </template>

            <!-- Existing options -->
            <template x-for="option in filteredOptions" :key="option.id">
                <button
                    type="button"
                    @click="toggle(option.id)"
                    class="w-full px-4 py-2.5 text-left hover:bg-gray-50 transition-colors flex items-center justify-between group"
                >
                    <span class="text-sm text-gray-700 group-hover:text-gray-900" x-text="option.name"></span>
                    <svg class="w-5 h-5 text-primary-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </template>
        </div>

        <!-- Selected count footer -->
        <div x-show="hasSelection" class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex items-center justify-between">
            <span><span x-text="selected.length"></span> dipilih</span>
            <button type="button" @click="selected = []" class="text-primary-600 hover:text-primary-700 font-medium">Hapus semua</button>
        </div>
    </div>

    <!-- Help text -->
    <p class="mt-1.5 text-xs text-gray-500">{{ $helpText ?? 'Pilih satu atau lebih.' }}</p>

</div>
