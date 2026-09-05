{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/01-overview.blade.php --}}

<flux:callout
    color="zinc"
    icon="map"
>
    <flux:callout.heading>
        {{ __('1. Graph idea') }}
    </flux:callout.heading>
    <flux:callout.text>
        <div class="space-y-3 text-sm leading-6">
            <p>
                {{ __('The roadmap sample uses one central trunk as the project timeline. Milestones live on the trunk, feature work leaves the trunk as side branches, and release consolidation is shown as a merge back into the main milestone line.') }}
            </p>
            <ul class="list-disc space-y-1 pl-5">
                <li><code>strang.trunk</code> {{ __('renders the milestone axis.') }}</li>
                <li><code>strang.branch-left/right</code> {{ __('renders independent feature tracks.') }}</li>
                <li><code>segments.step</code> {{ __('marks decisions or status changes inside a branch.') }}</li>
                <li><code>strang.merge-left</code> {{ __('shows release scope being accepted back into the roadmap.') }}</li>
                <li><code>strang.branch-end</code> {{ __('keeps postponed scope visible without pretending it reached the release.') }}</li>
            </ul>
        </div>
    </flux:callout.text>
</flux:callout>
