<?php

namespace Gunreip\TranslationWorkbench\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WorkbenchModeChanged extends HttpException
{
    public function __construct()
    {
        parent::__construct(409, 'The workbench mode changed. Reload this page.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'heading' => __('Workbench session changed'),
            'message' => __('Reload this page to continue with the current dataset.'),
            'action' => __('Reload page'),
        ], 409, ['X-Workbench-Mode-Changed' => '1']);
    }
}
