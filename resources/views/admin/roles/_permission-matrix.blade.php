<div x-data="{
    selectedPermissions: {{ json_encode($rolePermissions) }},
    selectAll(category) {
        const categoryPerms = this.$refs[category].querySelectorAll('input[type=checkbox]:not([disabled])');
        categoryPerms.forEach(checkbox => {
            if (!this.selectedPermissions.includes(checkbox.value)) {
                this.selectedPermissions.push(checkbox.value);
            }
        });
    },
    deselectAll(category) {
        const categoryPerms = this.$refs[category].querySelectorAll('input[type=checkbox]:not([disabled])');
        categoryPerms.forEach(checkbox => {
            const index = this.selectedPermissions.indexOf(checkbox.value);
            if (index > -1) {
                this.selectedPermissions.splice(index, 1);
            }
        });
    }
}">
    @foreach($permissionGroups as $category => $permissions)
        @if($permissions->isNotEmpty())
        <div class="mb-8" x-ref="{{ Str::slug($category) }}">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">{{ $category }}</h3>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="selectAll('{{ Str::slug($category) }}')"
                        class="text-xs text-primary-600 hover:text-primary-900 font-medium"
                    >
                        Select All
                    </button>
                    <span class="text-gray-400">|</span>
                    <button
                        type="button"
                        @click="deselectAll('{{ Str::slug($category) }}')"
                        class="text-xs text-gray-600 hover:text-gray-900 font-medium"
                    >
                        Deselect All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($permissions as $permission)
                    @php
                        $isCritical = in_array($permission->name, $criticalPermissions);
                        $isDisabled = $isCritical;
                    @endphp
                    <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors {{ $isDisabled ? 'bg-gray-50 opacity-75' : 'cursor-pointer' }}">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            x-model="selectedPermissions"
                            {{ $isDisabled ? 'disabled' : '' }}
                            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 {{ $isDisabled ? 'cursor-not-allowed' : '' }}"
                        >
                        <div class="flex-1">
                            <span class="text-sm {{ $isDisabled ? 'text-gray-500' : 'text-gray-700' }}">
                                {{ $permission->name }}
                            </span>
                            @if($isCritical)
                                <span class="ml-1 text-xs text-yellow-600">(required)</span>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    @if($isProtected && count($criticalPermissions) > 0)
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Note:</strong> Critical permissions marked as "required" cannot be removed from this protected role.
            </p>
        </div>
    @endif
</div>
