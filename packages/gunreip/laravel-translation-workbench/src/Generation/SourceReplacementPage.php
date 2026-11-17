<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Illuminate\Pagination\LengthAwarePaginator;

final class SourceReplacementPage extends LengthAwarePaginator
{
    public bool $allAssigned = false;
    public int $awaitingScan = 0;
}
