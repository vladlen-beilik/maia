<router-link tag="h3" :to="{name: 'maia-seo'}" class="cursor-pointer flex items-center font-normal dim text-white mb-6 text-base no-underline">
    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path fill="var(--sidebar-icon)" d="M4 16v5a1 1 0 0 1-2 0V3a1 1 0 0 1 1-1h8.5a1 1 0 0 1 .7.3l.71.7H21a1 1 0 0 1 .9 1.45L19.11 10l2.77 5.55A1 1 0 0 1 21 17h-8.5a1 1 0 0 1-.7-.3l-.71-.7H4zm7-12H4v10h7.5a1 1 0 0 1 .7.3l.71.7h6.47l-2.27-4.55a1 1 0 0 1 0-.9L19.38 5H13v4a1 1 0 0 1-2 0V4z"/>
    </svg>
    <span class="sidebar-label">
        {{ trans('maia::resources.sidebar.seo') }}
    </span>
</router-link>
