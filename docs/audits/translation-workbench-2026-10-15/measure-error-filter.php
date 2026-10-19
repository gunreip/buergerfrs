<?php
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingErrors;
// Read-only current-plan measurement; original before/after plans are archived separately.
DB::transaction(function () {
 if (DB::getDriverName() !== 'pgsql') { throw new RuntimeException('PostgreSQL required'); }
 DB::statement('SET TRANSACTION READ ONLY');
 DB::statement("SET LOCAL statement_timeout = '45000ms'");
 $links=DB::table('translation_workbench_key_findings')->selectRaw('finding_id, MIN(key_id) as key_id')->where('status','active')->groupBy('finding_id');
 $q=DB::table('translation_workbench_findings as findings')->join('translation_workbench_source_files as source_files','source_files.id','=','findings.source_file_id')->leftJoinSub($links,'key_links',fn($j)=>$j->on('key_links.finding_id','=','findings.id'))->leftJoin('translation_workbench_keys as keys','keys.id','=','key_links.key_id')->whereNotIn('findings.status',['obsolete','commented_out']);
 TranslationWorkbenchFindingErrors::apply($q);
 $ids=(clone $q)->orderBy('findings.id')->pluck('findings.id')->all();
 $plan=DB::select('EXPLAIN (ANALYZE, FORMAT JSON) '.$q->selectRaw('COUNT(*)')->toSql(),$q->getBindings());
 echo json_encode(['ids'=>$ids,'plan'=>json_decode($plan[0]->{'QUERY PLAN'},true)],JSON_PRETTY_PRINT)."\n";
});
