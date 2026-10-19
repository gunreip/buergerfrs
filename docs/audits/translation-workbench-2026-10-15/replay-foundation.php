<?php
/** Replay against a copy of a read-only DB snapshot. Original DB never selected here. */
$root=dirname(__DIR__,3);
$source=$argv[1]??'';
if(!str_starts_with($source,'/tmp/tw-status-snapshot-') || !is_file($source)) throw new RuntimeException('Supply the /tmp audit SQLite snapshot');
$work='/tmp/tw-status-replay-'.bin2hex(random_bytes(8)).'.sqlite';copy($source,$work);chmod($work,0600);
require $root.'/vendor/autoload.php';$app=require $root.'/bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer as Syncer;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner as Scanner;
config(['database.default'=>'audit_replay','database.connections.audit_replay'=>['driver'=>'sqlite','database'=>$work,'prefix'=>'','foreign_key_constraints'=>false],'translation-workbench.runtime_collector.enabled'=>false,'cache.default'=>'array']);
DB::purge('audit_replay');
$pdo=DB::connection()->getPdo();
foreach(['translation_workbench_findings'=>['source_signature','fingerprint','source_file_id','expression_fingerprint'],'translation_workbench_keys'=>['fingerprint','translation_key'],'translation_workbench_source_files'=>['path'],'translation_workbench_key_findings'=>['key_id','finding_id']] as $t=>$cols) foreach($cols as $c)$pdo->exec("CREATE INDEX IF NOT EXISTS idx_{$t}_{$c} ON $t ($c)");
function snap($table,$fields):array {return DB::table($table)->orderBy('id')->get(['id',...$fields])->keyBy('id')->map(fn($r)=>(array)$r)->all();}
function compare($old,$new):array {$created=array_diff_key($new,$old);$gone=array_diff_key($old,$new);$changed=[];foreach(array_intersect_key($old,$new) as $id=>$v)if($v!=$new[$id])$changed[]=['id'=>$id,'changed_fields'=>array_keys(array_filter($new[$id],fn($value,$field)=>$value!=$v[$field],ARRAY_FILTER_USE_BOTH))];return ['created'=>count($created),'removed'=>count($gone),'changed'=>count($changed),'sample'=>array_slice($changed,0,20)];}
$spec=[
 'translation_workbench_keys'=>['translation_key','review_status','status'],
 'translation_workbench_findings'=>['source_signature','raw_expression','status'],
 'translation_workbench_key_findings'=>['key_id','finding_id','status'],
 'translation_workbench_lang_values'=>['value','status'],
 'translation_workbench_reviews'=>['decision','old_values','new_values'],
];
$baseline=[];foreach($spec as $t=>$f)$baseline[$t]=snap($t,$f);
$reviewed=DB::table('translation_workbench_keys')->where('review_status','reviewed')->pluck('id')->all();
$scan=app(Scanner::class)->scanWithManifest();
$items=$scan['items'];
$out=['snapshot'=>basename($source),'working_copy'=>basename($work),'source_files'=>'Current repository, scanner reads only; no exporter/applier executed','items'=>$items->count(),'runs'=>[]];
foreach([false,true,true] as $mark){
 $before=[];foreach($spec as $t=>$f)$before[$t]=snap($t,$f);
 $r=app(Syncer::class)->sync($items,markObsolete:$mark,reconciliationPaths:$scan['scanned_paths']);
 $run=['mark_obsolete'=>$mark,'summary'=>$r,'diff'=>[]];
 foreach($spec as $t=>$f)$run['diff'][$t]=compare($before[$t],snap($t,$f));
 $out['runs'][]=$run;
}
$out['reviewed_key_changes']=compare(array_intersect_key($baseline['translation_workbench_keys'],array_flip($reviewed)),array_intersect_key(snap('translation_workbench_keys',$spec['translation_workbench_keys']),array_flip($reviewed)));
$out['baseline_values_unchanged']=$baseline['translation_workbench_lang_values']===snap('translation_workbench_lang_values',$spec['translation_workbench_lang_values']);
$out['baseline_reviews_unchanged']=$baseline['translation_workbench_reviews']===snap('translation_workbench_reviews',$spec['translation_workbench_reviews']);
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
