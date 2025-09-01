<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 focus:ring focus:ring-blue-300 active:opacity-95 transition ease-in-out duration-150']) }} style="background-color: #1E90FF;">
    {{ $slot }}
</button>