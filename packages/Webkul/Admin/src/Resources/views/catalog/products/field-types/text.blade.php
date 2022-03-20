<input
    type="text"
    name="{{ $attribute->code }}"
    value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
    class="control"
    id="{{ $attribute->code }}"

    {{-- To Do (@devansh-webkul): Need to handle from the backend. --}}
    @if ($attribute->code === 'sku')
        v-validate="{ required: true, regex: /^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/ }"
    @else
        v-validate="'{{ $validations }}'"
    @endif

    data-vv-as="&quot;{{ $attribute->admin_name }}&quot;"
    {{ in_array($attribute->code, ['url_key']) ? 'v-slugify' : '' }}
    {{ $attribute->code == 'name' && ! $product[$attribute->code]  ? 'v-slugify-target=\'url_key\'' : ''  }}>