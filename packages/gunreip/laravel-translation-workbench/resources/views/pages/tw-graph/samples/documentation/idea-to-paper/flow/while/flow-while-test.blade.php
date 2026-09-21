<section class="mt-4 min-w-0 space-y-4" id="flow-while-test-proposals">
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('WHILE Test — proposed examples') }}</flux:callout.heading>
        <flux:callout.text>Start with the basic loop, then extend the examples step by step. Each graph will be authored independently with visible props and its own code example. This page currently lists proposals; no loop component or preview has been implemented yet.</flux:callout.text>
    </flux:callout>
    <flux:callout class="min-w-0" color="zinc" icon="list-bullet">
        <flux:callout.heading>{{ __('Suggested sequence') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Example</flux:table.column>
                <flux:table.column>What it demonstrates</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">WHILE basic</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Condition → TRUE → action → condition. FALSE goes to the next action outside the loop. Show left/right layouts and an initially false condition (zero iterations) using the same structure.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>2</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Multiple body actions</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Process an item → advance to the next item → retest. Only the final body action returns to the condition; advancing belongs inside the loop.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>3</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">WHILE with IF / SWITCH</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Branches inside the body merge before the loop returns. A SWITCH BREAK exits its SWITCH and continues the WHILE body.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>4</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Nested WHILE</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The inner FALSE exits only the inner loop; execution continues in the outer body. Each return must target its own condition.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>5</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Two independent inner loops</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Two inner loops in separate outer-body sections, each with its own condition, body, exit and return.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>6</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Mixed sides and crossings</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Outer left / inner right and vice versa. Explicit line-jumps or entry detours distinguish crossings from connected lanes.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>7</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Action → Nested WHILE → Action</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Prepare inner state → inner loop → finish processing → outer condition. The final action follows the inner FALSE exit, including when the inner body runs zero times.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="sky" icon="information-circle">
        <flux:callout.heading>{{ __('Common rules for the examples') }}</flux:callout.heading>
        <flux:callout.text>Keep the condition separate from body actions. Label TRUE, FALSE and the return direction clearly. The return rejoins the condition after initialization, so initialization is not repeated accidentally. Use conditions and body updates that make progress explicit; drawing a return alone does not guarantee termination.</flux:callout.text>
        <flux:text class="mt-2">DO WHILE, FOR and FOREACH will get their own sections. Loop BREAK, CONTINUE, RETURN and THROW remain part of the later control-transfer examples.</flux:text>
    </flux:callout>
    <flux:field class="flex justify-end font-mono text-xs text-zinc-400">
        .../flow/while/flow-while-test.blade.php
    </flux:field>
</section>
