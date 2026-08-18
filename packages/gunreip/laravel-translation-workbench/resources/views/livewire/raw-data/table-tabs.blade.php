{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-tabs.blade.php --}}

<div
    class="flex min-w-0 items-center gap-1"
    x-on:translation-workbench:raw-data-table-tab-changed.window="
        $nextTick(() => window.setTimeout(() => scrollTableTab($event.detail.table, $event.detail.scroll), 80))
    "
    x-data="{
        tabScroller() {
            const candidates = Array.from(this.$refs.tabsWrap.querySelectorAll('*'));
            return candidates.find((element) => element.scrollWidth > element.clientWidth)
                ?? this.$refs.tabsWrap;
        },
        scrollTableTab(table, direction = 'nearest') {
            const scroller = this.tabScroller();

            if (direction === 'first') {
                scroller.scrollTo({ left: 0, behavior: 'smooth' });
                return;
            }

            if (direction === 'last') {
                scroller.scrollTo({ left: scroller.scrollWidth, behavior: 'smooth' });
                return;
            }

            const target = this.$refs.tabsWrap.querySelector(`[data-translation-workbench-table-tab='${table}']`);

            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
            }
        },
    }"
>
    <div class="flex shrink-0 items-center gap-0.5">
        <x-ui.button.tab-scroll
            direction="first"
            :disabled="$activeTableIndex <= 0"
            wire:click="openFirstTableTab"
        />
        <x-ui.button.tab-scroll
            direction="previous"
            :disabled="$activeTableIndex <= 0"
            wire:click="openPreviousTableTab"
        />
    </div>

    <div
        class="min-w-0 flex-1"
        x-ref="tabsWrap"
    >
        <flux:tabs
            class="min-w-max"
            scrollable
            scrollable:fade
            scrollable:scrollbar="hide"
            wire:model.live="activeTable"
        >
                @foreach ($tables as $tableName)
                    {{-- Tabs --}}
                    <flux:tab name="{{ $tableName }}">
                        <span
                            class="inline-flex items-center gap-2"
                            data-translation-workbench-table-tab="{{ $tableName }}"
                        >
                            <span>{{ $tableName }}</span>

                            <x-ui.tooltip.simple
                                :title="$tableName"
                                :text="$tableDescriptions[$tableName] ?? __('Raw database table used by the Translation Workbench.')"
                            />

                            {{-- Tabs Counter Badge --}}
                            <flux:badge
                                class="tabular-nums"
                                size="sm"
                                variant="subtle"
                            >
                                {{ number_format($tableCounts[$tableName] ?? 0) }}
                            </flux:badge>
                        </span>
                    </flux:tab>
                @endforeach
        </flux:tabs>
    </div>

    <div class="flex shrink-0 items-center gap-0.5">
        <x-ui.button.tab-scroll
            direction="next"
            :disabled="$activeTableIndex >= $lastTableIndex"
            wire:click="openNextTableTab"
        />
        <x-ui.button.tab-scroll
            direction="last"
            :disabled="$activeTableIndex >= $lastTableIndex"
            wire:click="openLastTableTab"
        />
    </div>
</div>
