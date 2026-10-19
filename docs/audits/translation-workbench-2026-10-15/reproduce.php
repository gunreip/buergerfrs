<?php
/** Characterization audit, not desired-behavior regression tests. SQLite + /tmp only. */
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner as Scanner;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer as Syncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangValueImporter as Importer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter as Exporter;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey as Key;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue as Value;
$fixture = sys_get_temp_dir().'/tw-status-audit-'.bin2hex(random_bytes(8));
mkdir($fixture.'/resources/views',0700,true);
mkdir($fixture.'/lang/en',0700,true);
mkdir($fixture.'/lang/de',0700,true);
$app->setBasePath($fixture);$app->useLangPath($fixture.'/lang');$app->useStoragePath($fixture.'/storage');
config(['database.default'=>'audit_isolated','database.connections.audit_isolated'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'','foreign_key_constraints'=>true],'translation-workbench.paths'=>['resources/views'],'translation-workbench.exclude_paths'=>[],'translation-workbench.runtime_collector.enabled'=>false,'cache.default'=>'array']);
function resetAudit(): void {
 global $root,$fixture;
 DB::purge('audit_isolated');
 if(DB::getDriverName()!=='sqlite' || DB::connection()->getConfig('database')!==':memory:') throw new RuntimeException('Isolation failed');
 Schema::create('users',fn(Blueprint $t)=>$t->id());
 Schema::create('languages',fn(Blueprint $t)=>$t->id());Schema::create('countries',fn(Blueprint $t)=>$t->id());
 (require $root.'/database/migrations/2026_05_13_082530_create_locales_table.php')->up();
 foreach(glob($root.'/packages/gunreip/laravel-translation-workbench/database/migrations/*.php') as $m) (require $m)->up();
 foreach(['resources/views','lang/en','lang/de'] as $dir) foreach(glob($fixture.'/'.$dir.'/*') as $f) if(is_file($f)) unlink($f);
 $settings = (new ReflectionClass(App\Settings\AppGeneralSettings::class))->newInstanceWithoutConstructor();
 $settings->locale='en';$settings->availableLocales=['en','de'];$settings->addedPrimaryLocales=[];
 app()->instance(App\Settings\AppGeneralSettings::class,$settings);
 app()->forgetInstance('translation-workbench-event-types');
}
function source(string $name,string $text):void {file_put_contents(base_path('resources/views/'.$name.'.blade.php'),$text);}
function scanItems(?array $paths=null) {return app(Scanner::class)->scan($paths);}
function syncItems():array {return app(Syncer::class)->sync(scanItems());}
function langValue(string $namespace,string $key,string $value):void {
 Value::create(['locale'=>'en','namespace'=>$namespace,'lang_key'=>$key,'translation_key'=>$namespace.'.'.$key,'value'=>$value,'value_type'=>'string','source_path'=>'lang/en/'.$namespace.'.php','source_hash'=>sha1($value),'status'=>'active','meta'=>['source'=>'translation-workbench-editor']]);
}
$out=['isolation'=>'SQLite :memory:, source/lang/reports under unique /tmp directory','fixture'=>$fixture,'cases'=>[]];
function runCase(string $name,Closure $fn):void {global $out;try {resetAudit();$out['cases'][$name]=$fn();} catch(Throwable $e){$out['cases'][$name]=['audit_error'=>get_class($e).': '.$e->getMessage()];}}
runCase('legacy_partial_scan_obsoletes_outside_scope',function(){
 source('a',"{{ __('Alpha original') }}");source('b',"{{ __('Beta original') }}");
 Artisan::call('translation-workbench:scan');
 Artisan::call('translation-workbench:scan',['--paths'=>'resources/views/a.blade.php','--mark-obsolete'=>true]);
 return ['outside_entry'=>DB::table('translation_workbench_entries')->where('literal_text','Beta original')->value('status'),'outside_occurrence'=>DB::table('translation_workbench_occurrences')->where('source_path','resources/views/b.blade.php')->value('status')];
});
runCase('legacy_nonexistent_path_obsoletes_all',function(){
 source('a',"{{ __('Alpha original') }}");Artisan::call('translation-workbench:scan');
 Artisan::call('translation-workbench:scan',['--paths'=>'does-not-exist','--mark-obsolete'=>true]);
 return ['entry'=>DB::table('translation_workbench_entries')->value('status')];
});
runCase('foundation_last_occurrence_removed',function(){
 source('a',"{{ __('Alpha original') }}");syncItems();source('a','<p>No translation call remains</p>');
 $scan=app(Scanner::class)->scanWithManifest();
 $r=app(Syncer::class)->sync($scan['items'],markObsolete:true,reconciliationPaths:$scan['scanned_paths']);
 return ['finding'=>DB::table('translation_workbench_findings')->value('status'),'relation'=>DB::table('translation_workbench_key_findings')->value('status'),'obsoleted'=>$r['findings_obsoleted']];
});
runCase('same_line_identical_occurrences',function(){
 source('a',"{{ __('Alpha original') }} {{ __('Alpha original') }}");
 return ['authored_calls'=>2,'scanner_items'=>scanItems()->count()];
});
runCase('commented_calls_still_captured',function(){
 source('a',"{{-- {{ __('Alpha original') }} --}}");syncItems();
 return ['scanner_items'=>scanItems()->count(),'finding_status'=>DB::table('translation_workbench_findings')->value('status'),'key_count'=>Key::count()];
});
runCase('import_overwrites_saved_unpublished_value',function(){
 file_put_contents(lang_path('en/audit.php'),"<?php return ['title' => 'Published old'];");
 langValue('audit','title','Saved new draft');
 app(Importer::class)->import('en');
 return ['before'=>'Saved new draft','after'=>Value::sole()->value,'meta_source'=>Value::sole()->meta['source']??null];
});
runCase('scalar_export_accepts_pending_key',function(){
 source('a',"{{ __('audit.title') }}");syncItems();langValue('audit','title','Not reviewed');
 $review=Key::sole()->review_status;$r=app(Exporter::class)->export(['en'],write:true);
 return ['key_review_status'=>$review,'files_written'=>$r['files_written'],'written_value'=>is_file(lang_path('en/audit.php'))?(require lang_path('en/audit.php'))['title']:null];
});
runCase('repeated_legacy_scan_identical_source',function(){
 source('a',"{{ __('Repeated text') }}");source('b',"{{ __('Repeated text') }}");
 Artisan::call('translation-workbench:scan');$before=DB::table('translation_workbench_events')->count();
 Artisan::call('translation-workbench:scan');
 return ['entries'=>DB::table('translation_workbench_entries')->count(),'extra_events'=>DB::table('translation_workbench_events')->count()-$before,'changed_fields'=>DB::table('translation_workbench_events')->where('event_type','changed')->pluck('context')->all()];
});
runCase('repeated_foundation_sync_preserves_reviewed_key',function(){
 source('a',"{{ __('Original text') }}");syncItems();$key=Key::sole();$key->update(['translation_key'=>'audit.reviewed','review_status'=>'reviewed']);
 $before=DB::table('translation_workbench_timeline_events')->count();syncItems();$first=DB::table('translation_workbench_timeline_events')->count();syncItems();
 return ['translation_key'=>$key->fresh()->translation_key,'review_status'=>$key->fresh()->review_status,'first_rescan_events'=>$first-$before,'second_rescan_events'=>DB::table('translation_workbench_timeline_events')->count()-$first];
});
runCase('legacy_import_identical_source_relogs_observation',function(){
 source('a',"{{ __('audit.title') }}");file_put_contents(lang_path('en/audit.php'),"<?php return ['title'=>'Same text'];");
 Artisan::call('translation-workbench:scan');Artisan::call('translation-workbench:import-existing');
 $before=DB::table('translation_workbench_events')->where('event_type','existing_translation_value_changed')->count();
 Illuminate\Support\Carbon::setTestNow(now()->addMinute());
 Artisan::call('translation-workbench:import-existing');Illuminate\Support\Carbon::setTestNow();
 return ['extra_change_events'=>DB::table('translation_workbench_events')->where('event_type','existing_translation_value_changed')->count()-$before];
});
runCase('export_conflict_in_second_file_leaves_first_written',function(){
 source('a',"{{ __('aaa.title') }}\n{{ __('zzz.parent.child') }}");syncItems();
 langValue('aaa','title','New value');langValue('zzz','parent.child','Child');
 file_put_contents(lang_path('en/zzz.php'),"<?php return ['parent'=>'Existing scalar'];");
 $r=app(Exporter::class)->export(['en'],write:true);
 return ['files_written'=>$r['files_written'],'conflicts'=>$r['values_conflicted'],'first_file_exists'=>is_file(lang_path('en/aaa.php')),'second_file'=>require lang_path('en/zzz.php')];
});
runCase('export_command_returns_success_despite_conflict',function(){
 source('a',"{{ __('aaa.title') }}\n{{ __('zzz.parent.child') }}");syncItems();
 langValue('aaa','title','New value');langValue('zzz','parent.child','Child');
 file_put_contents(lang_path('en/zzz.php'),"<?php return ['parent'=>'Existing scalar'];");
 $code=Artisan::call('translation-workbench:export-lang-files',['--locale'=>['en'],'--write'=>true]);
 return ['exit_code'=>$code,'first_file_written'=>is_file(lang_path('en/aaa.php')),'conflict_warning'=>str_contains(Artisan::output(),'structures conflict')];
});
runCase('distinct_literal_whitespace_on_same_line',function(){
 source('a',"{{ __('Hello  world') }} {{ __('Hello world') }}");
 return ['authored_calls'=>2,'scanner_items'=>scanItems()->count(),'texts'=>scanItems()->pluck('literalText')->all()];
});
runCase('last_language_file_removed_leaves_active_value',function(){
 file_put_contents(lang_path('en/audit.php'),"<?php return ['title'=>'Old value'];");
 app(Importer::class)->import('en');unlink(lang_path('en/audit.php'));
 app(Importer::class)->import('en');
 return ['remaining_status'=>Value::sole()->status];
});
runCase('legacy_rewriter_chooses_first_key_for_all_contexts',function(){
 Schema::create('translation_keys',function(Blueprint $t){$t->id();$t->text('native_text');$t->string('key');});
 DB::table('translation_keys')->insert([['native_text'=>'Open','key'=>'audit.open_door'],['native_text'=>'Open','key'=>'audit.open_file']]);
 source('door',"{{ __('Open') }}");source('file',"{{ __('Open') }}");
 Artisan::call('translations:rewrite-literals');
 return ['door'=>file_get_contents(base_path('resources/views/door.blade.php')),'file'=>file_get_contents(base_path('resources/views/file.blade.php')),'foundation_provenance_events'=>DB::table('translation_workbench_timeline_events')->count()];
});
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).PHP_EOL;

foreach($out['cases'] as $case) if(isset($case['audit_error'])) exit(1);
