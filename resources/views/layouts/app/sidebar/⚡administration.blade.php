{{-- resources/views/layouts/app/sidebar/⚡administration.blade.php --}}

@php
    $administrationNeedsAttention = \App\Support\Navigation\AdminAttention::administration();
    $referenceNeedsAttention = \App\Support\Navigation\AdminAttention::reference();
    $htmlViewAuditNeedsAttention = \App\Support\Navigation\AdminAttention::htmlViewAudit();

    $attentionDotClass =
        "relative after:pointer-events-none after:absolute after:right-3 after:top-3 after:size-2 after:rounded-full after:bg-red-500 after:content-['']";
@endphp

<flux:sidebar.group
    class="{{ $administrationNeedsAttention ? $attentionDotClass : '' }} mt-4 grid"
    :heading="__('Administration')"
    icon="settings-2"
    @class([
        'sidebar-group-attention-dot' => $htmlViewAuditNeedsAttention,
    ])
    expandable
    :expanded="request()->routeIs('admin.*')"
>
    {{-- Admin -> users --}}
    <flux:sidebar.item
        icon="users"
        :href="route('admin.users')"
        :current="request()->routeIs('admin.users')"
        wire:navigate
    >
        {{ __('Users') }}
    </flux:sidebar.item>

    {{-- Admin -> persons --}}
    <flux:sidebar.item
        icon="id-card"
        :href="route('admin.people')"
        :current="request()->routeIs('admin.people')"
        wire:navigate
    >
        {{ __('Persons') }}
    </flux:sidebar.item>

    {{-- Admin -> clients --}}
    <flux:sidebar.item
        icon="building-2"
        :href="route('admin.clients')"
        :current="request()->routeIs('admin.clients')"
        wire:navigate
    >
        {{ __('Clients') }}
    </flux:sidebar.item>

    {{-- Admin -> roles --}}
    <flux:sidebar.item
        icon="shield-check"
        :href="route('admin.roles')"
        :current="request()->routeIs('admin.roles')"
        wire:navigate
    >
        {{ __('ui.labels.roles') }}
    </flux:sidebar.item>

    {{-- Admin -> permissions --}}
    <flux:sidebar.item
        icon="key-round"
        :href="route('admin.permissions')"
        :current="request()->routeIs('admin.permissions')"
        wire:navigate
    >
        {{ __('Permissions') }}
    </flux:sidebar.item>

    {{-- Admin -> translations (group) --}}
    <flux:sidebar.group
        class="grid"
        :heading="__('Translations')"
        icon="languages"
        expandable
        :expanded="request()->routeIs('admin.translations')
                                || request()->routeIs('admin.translation-statistics')
                                || request()->routeIs('admin.translation-sub-languages')
                                || request()->routeIs('admin.translation-usage')
                                || request()->routeIs('admin.translation-lang-ballast')"
    >
        {{-- Admin -> translations -> management --}}
        <flux:sidebar.item
            icon="table-cells"
            :href="route('admin.translations')"
            :current="request()->routeIs('admin.translations')"
            wire:navigate
        >
            {{ __('Management') }}
        </flux:sidebar.item>

        {{-- Admin -> translations -> sub-languages --}}
        <flux:sidebar.item
            icon="languages"
            :href="route('admin.translation-sub-languages')"
            :current="request()->routeIs('admin.translation-sub-languages')"
            wire:navigate
        >
            {{ __('Sub-Languages') }}
        </flux:sidebar.item>

        {{-- Admin -> translations -> usage --}}
        <flux:sidebar.item
            icon="scan-search"
            :href="route('admin.translation-usage')"
            :current="request()->routeIs('admin.translation-usage')"
            wire:navigate
        >
            {{ __('admin.translation_list.modal.usage') }}
        </flux:sidebar.item>

        {{-- Admin -> translations -> lang ballast --}}
        <flux:sidebar.item
            icon="archive-x"
            :href="route('admin.translation-lang-ballast')"
            :current="request()->routeIs('admin.translation-lang-ballast')"
            wire:navigate
        >
            {{ __('Lang Ballast') }}
        </flux:sidebar.item>

        {{-- Admin -> translations -> statistics --}}
        <flux:sidebar.item
            icon="chart-bar"
            :href="route('admin.translation-statistics')"
            :current="request()->routeIs('admin.translation-statistics')"
            wire:navigate
        >
            {{ __('Statistics') }}
        </flux:sidebar.item>
    </flux:sidebar.group>

    {{-- Admin -> app settings --}}
    <flux:sidebar.item
        icon="settings"
        :href="route('admin.app-settings')"
        :current="request()->routeIs('admin.app-settings')"
        wire:navigate
    >
        {{ __('admin.app_settings.app_settings') }}
    </flux:sidebar.item>

    {{-- Admin -> reference --}}
    <flux:sidebar.group
        class="{{ $referenceNeedsAttention ? $attentionDotClass : '' }} grid"
        :heading="__('Reference')"
        icon="globe"
        @class([
            'sidebar-group-attention-dot' => $htmlViewAuditNeedsAttention,
        ])
        expandable
        :expanded="request()->routeIs('admin.country-references') || request()->routeIs('admin.flag-references') || request()->routeIs('admin.html-view-audit')"
    >

        {{-- Admin -> reference -> countries --}}
        <flux:sidebar.item
            icon="globe"
            :href="route('admin.country-references')"
            :current="request()->routeIs('admin.country-references')"
            wire:navigate
        >
            {{ __('layouts.sidebar.administration.countries') }}
        </flux:sidebar.item>

        {{-- Admin -> reference -> flags --}}
        <flux:sidebar.item
            icon="flag"
            :href="route('admin.flag-references')"
            :current="request()->routeIs('admin.flag-references')"
            wire:navigate
        >
            {{ __('admin.permissions.table.columns.flags') }}
        </flux:sidebar.item>

        {{-- Admin -> reference -> HTML tags check --}}
        <flux:sidebar.item
            icon="code-xml"
            :href="route('admin.html-view-audit')"
            :current="request()->routeIs('admin.html-view-audit')"
            :badge="$htmlViewAuditNeedsAttention ? ' ' : null"
            @class([
                'sidebar-attention-dot' => $htmlViewAuditNeedsAttention,
            ])
            wire:navigate
        >
            <span class="flex-1 text-left text-sm font-medium leading-none rtl:text-right">
                {{ __('HTML-Tags-Check') }}
            </span>
        </flux:sidebar.item>

    </flux:sidebar.group>

    {{-- Admin -> fallback reports --}}
    <flux:sidebar.item
        icon="triangle-alert"
        :href="route('admin.fallback-reports')"
        :current="request()->routeIs('admin.fallback-reports')"
        wire:navigate
    >
        {{ __('layouts.sidebar.administration.fallback_reports') }}
    </flux:sidebar.item>

    @role('Super-Admin')
        {{-- Admin -> phpdoc documentation --}}
        <flux:sidebar.item
            icon="book-open-text"
            :href="route('admin.phpdoc')"
            :current="request()->routeIs('admin.phpdoc')"
        >
            {{ __('PHPDoc') }}
        </flux:sidebar.item>
    @endrole
</flux:sidebar.group>
