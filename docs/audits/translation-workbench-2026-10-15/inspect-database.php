<?php
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = Illuminate\Support\Facades\DB::connection();
$pdo = $c->getPdo();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
if ($driver !== 'pgsql') { throw new RuntimeException('Read-only audit expects PostgreSQL; got '.$driver); }
$pdo->exec('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
$tables=$pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' AND (tablename LIKE 'translation%' OR tablename LIKE 'dynamic_translation%') ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
$out=['captured_at'=>date(DATE_ATOM),'driver'=>$driver,'transaction'=>'REPEATABLE READ READ ONLY','tables'=>[]];
foreach($tables as $table) {
 $quoted='"'.str_replace('"','""',$table).'"';
 $info=['rows'=>(int)$pdo->query("SELECT count(*) FROM $quoted")->fetchColumn()];
 $q=$pdo->prepare("SELECT column_name,data_type,is_nullable,column_default FROM information_schema.columns WHERE table_schema='public' AND table_name=? ORDER BY ordinal_position");$q->execute([$table]);$info['columns']=$q->fetchAll(PDO::FETCH_ASSOC);
 $q=$pdo->prepare("SELECT indexname,indexdef FROM pg_indexes WHERE schemaname='public' AND tablename=?");$q->execute([$table]);$info['indexes']=$q->fetchAll(PDO::FETCH_ASSOC);
 $q=$pdo->prepare("SELECT conname,contype,pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE conrelid=CAST(? AS regclass)");$q->execute([$table]);$info['constraints']=$q->fetchAll(PDO::FETCH_ASSOC);
 $q=$pdo->prepare('SELECT pg_total_relation_size(CAST(? AS regclass))');$q->execute([$table]);$info['total_bytes']=(int)$q->fetchColumn();
 foreach(['status','review_status','kind','event_type','relation_type'] as $col) if(in_array($col,array_column($info['columns'],'column_name'))) {
 $info['distribution'][$col]=$pdo->query("SELECT \"$col\" AS value,count(*) AS count FROM $quoted GROUP BY \"$col\" ORDER BY count(*) DESC LIMIT 80")->fetchAll(PDO::FETCH_ASSOC);
 }
 $out['tables'][$table]=$info;
}
require __DIR__.'/database-checks.php';
$pdo->exec('ROLLBACK');
file_put_contents(__DIR__.'/database.json',json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
foreach($out['tables'] as $t=>$v) echo $t.' | '.$v['rows'].' | '.round($v['total_bytes']/1048576,2).' MiB'.PHP_EOL;
