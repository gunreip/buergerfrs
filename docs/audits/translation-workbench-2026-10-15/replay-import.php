<?php
/** Import current language files into a COPY of the snapshot, never the live DB. */
$root=dirname(__DIR__,3);$source=$argv[1]??'';
if(!str_starts_with($source,'/tmp/tw-status-snapshot-')||!is_file($source))throw new RuntimeException('Expected audit snapshot under /tmp');
$work='/tmp/tw-status-import-'.bin2hex(random_bytes(8)).'.sqlite';copy($source,$work);chmod($work,0600);
require $root.'/vendor/autoload.php';$app=require $root.'/bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangValueImporter as Importer;
config(['database.default'=>'audit_import','database.connections.audit_import'=>['driver'=>'sqlite','database'=>$work,'prefix'=>'','foreign_key_constraints'=>false],'translation-workbench.runtime_collector.enabled'=>false,'cache.default'=>'array']);
DB::purge('audit_import');
DB::statement('CREATE INDEX audit_import_values ON translation_workbench_lang_values(locale,namespace,lang_key)');
$before=DB::table('translation_workbench_lang_values')->get()->keyBy('id');
$service=app(Importer::class);$locales=$service->availableLocales();$result=$service->importLocales($locales);
$after=DB::table('translation_workbench_lang_values')->get()->keyBy('id');
$diffs=[];
foreach($before as $id=>$old){$new=$after[$id]??null;if(!$new){$diffs[]=['id'=>$id,'removed'=>true];continue;}$fields=[];foreach(['value','status','meta','translation_key'] as $f)if($old->$f!=$new->$f)$fields[]=$f;if($fields)$diffs[]=['id'=>$id,'translation_key'=>$old->translation_key,'locale'=>$old->locale,'fields'=>$fields];}
echo json_encode(['snapshot'=>basename($source),'locales'=>$locales,'summary'=>$result,'existing_row_differences'=>$diffs],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
