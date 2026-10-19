<?php
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
try {
$result = DB::transaction(function () {
 DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
 $paths = ['resources/views/livewire/admin/pages/settings/⚡profile.blade.php', 'resources/views/components/ui/button/save.blade.php'];
 $files = DB::table('translation_workbench_source_files')->whereIn('path', $paths)->get();
 $findings = DB::table('translation_workbench_findings')->whereIn('source_file_id', $files->pluck('id'))->orderBy('id')->get();
 return ['files'=>$files,'findings'=>$findings,
 'links'=>DB::table('translation_workbench_key_findings')->whereIn('key_id',[4507,4613])->orWhereIn('finding_id',$findings->pluck('id'))->orderBy('id')->get(),
 'keys'=>DB::table('translation_workbench_keys')->whereIn('id',[4507,4613])->get(),
 'source_plan'=>app(TranslationWorkbenchSourceReconciliation::class)->plan($paths),
 'scan'=>collect($paths)->mapWithKeys(fn($p)=>[$p=>app(TranslationWorkbenchScanner::class)->scanFile(base_path($p))]),
 'events'=>DB::table('translation_workbench_timeline_events')->where('finding_id',5093)->where('event_type','!=','finding_seen')->orderBy('id')->get()];
});
echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
} catch(Throwable $e) {fwrite(STDERR,$e->getMessage().PHP_EOL);exit(1);}
