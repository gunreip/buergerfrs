                <flux:table class="[&_table]:table-fixed">
                    <flux:table.rows>
                        @foreach ($fields as $field => $value)
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal! wrap-anywhere w-1/3 align-top font-medium">
                                    {{ __(str($field)->replace('_', ' ')->headline()->toString()) }}
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal! wrap-anywhere align-top">
                                    <pre class="wrap-anywhere whitespace-pre-wrap text-sm text-zinc-700 dark:text-zinc-200">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value ?? '—' }}</pre>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
