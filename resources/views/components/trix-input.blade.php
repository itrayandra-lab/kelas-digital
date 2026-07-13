@props(['id', 'name', 'value' => '', 'uploadEndpoint' => route('attachments.store')])

<input
    type="hidden"
    name="{{ $name }}"
    id="{{ $id }}_input"
    value="{{ $value }}"
/>

<trix-toolbar
    class="[&_.trix-button]:bg-white [&_.trix-button.trix-active]:bg-gray-300"
    id="{{ $id }}_toolbar"
></trix-toolbar>

<trix-editor
    id="{{ $id }}"
    toolbar="{{ $id }}_toolbar"
    input="{{ $id }}_input"
    data-upload-endpoint="{{ $uploadEndpoint }}"
    {{ $attributes->merge(['class' => 'trix-content border-gray-300 focus:ring-1 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white']) }}
></trix-editor>
