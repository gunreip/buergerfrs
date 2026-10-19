<?php
// Included only inside the read-only PostgreSQL transaction in inspect-database.php.
$queries = [
 'active_findings_without_active_relation'=>"SELECT count(*) FROM translation_workbench_findings f WHERE status='active' AND NOT EXISTS(SELECT 1 FROM translation_workbench_key_findings r WHERE r.finding_id=f.id AND r.status='active')",
 'active_findings_multiple_active_relations'=>"SELECT count(*) FROM (SELECT f.id FROM translation_workbench_findings f JOIN translation_workbench_key_findings r ON r.finding_id=f.id AND r.status='active' WHERE f.status='active' GROUP BY f.id HAVING count(*)>1) q",
 'active_relations_to_obsolete_findings'=>"SELECT count(*) FROM translation_workbench_key_findings r JOIN translation_workbench_findings f ON f.id=r.finding_id WHERE r.status='active' AND f.status='obsolete'",
 'active_relations_to_obsolete_keys'=>"SELECT count(*) FROM translation_workbench_key_findings r JOIN translation_workbench_keys k ON k.id=r.key_id WHERE r.status='active' AND k.status='obsolete'",
 'reviewed_nonobsolete_keys_without_active_findings'=>"SELECT count(*) FROM translation_workbench_keys k WHERE k.status<>'obsolete' AND review_status='reviewed' AND NOT EXISTS(SELECT 1 FROM translation_workbench_key_findings r JOIN translation_workbench_findings f ON f.id=r.finding_id WHERE r.key_id=k.id AND r.status='active' AND f.status='active')",
 'duplicate_nonobsolete_translation_keys'=>"SELECT count(*) FROM (SELECT translation_key FROM translation_workbench_keys WHERE status<>'obsolete' AND nullif(translation_key,'') IS NOT NULL GROUP BY translation_key HAVING count(*)>1) q",
 'duplicate_nonobsolete_reviewed_keys'=>"SELECT count(*) FROM (SELECT translation_key FROM translation_workbench_keys WHERE status<>'obsolete' AND review_status='reviewed' AND nullif(translation_key,'') IS NOT NULL GROUP BY translation_key HAVING count(*)>1) q",
 'duplicate_lang_locale_namespace_key'=>"SELECT count(*) FROM (SELECT locale,namespace,lang_key FROM translation_workbench_lang_values GROUP BY locale,namespace,lang_key HAVING count(*)>1) q",
 'active_values_without_nonobsolete_key'=>"SELECT count(*) FROM translation_workbench_lang_values v WHERE status='active' AND NOT EXISTS(SELECT 1 FROM translation_workbench_keys k WHERE k.translation_key=v.translation_key AND k.status<>'obsolete')",
 'active_values_without_reviewed_key'=>"SELECT count(*) FROM translation_workbench_lang_values v WHERE status='active' AND NOT EXISTS(SELECT 1 FROM translation_workbench_keys k WHERE k.translation_key=v.translation_key AND k.status<>'obsolete' AND k.review_status='reviewed')",
 'active_key_findings_owner_differs_from_code'=>"SELECT count(DISTINCT f.id) FROM translation_workbench_findings f JOIN translation_workbench_key_findings r ON r.finding_id=f.id AND r.status='active' JOIN translation_workbench_keys k ON k.id=r.key_id AND k.status<>'obsolete' WHERE f.status='active' AND nullif(f.found_translation_key,'') IS NOT NULL AND nullif(k.translation_key,'') IS NOT NULL AND f.found_translation_key<>k.translation_key",
 'legacy_workbench_common_values'=>"SELECT count(*) FROM translation_values v JOIN translation_keys k ON k.id=v.translation_key_id JOIN translation_workbench_lang_values w ON w.translation_key=k.key AND w.locale=v.locale WHERE w.status='active'",
 'legacy_workbench_different_common_values'=>"SELECT count(*) FROM translation_values v JOIN translation_keys k ON k.id=v.translation_key_id JOIN translation_workbench_lang_values w ON w.translation_key=k.key AND w.locale=v.locale WHERE w.status='active' AND w.value IS DISTINCT FROM v.value",
];
$out['checks']=[];
foreach($queries as $name=>$sql) $out['checks'][$name]=['sql'=>$sql,'count'=>(int)$pdo->query($sql)->fetchColumn()];
$out['foreign_key_orphans']=[];
foreach($out['tables'] as $t=>$info) foreach($info['constraints'] as $con) if($con['contype']==='f' && preg_match('/FOREIGN KEY \((\w+)\) REFERENCES (\w+)\((\w+)\)/',$con['definition'],$m)) {
 [$all,$col,$parent,$pk]=$m;
 $n=(int)$pdo->query("SELECT count(*) FROM \"$t\" c LEFT JOIN \"$parent\" p ON p.\"$pk\"=c.\"$col\" WHERE c.\"$col\" IS NOT NULL AND p.\"$pk\" IS NULL")->fetchColumn();
 $out['foreign_key_orphans'][]=['table'=>$t,'column'=>$col,'parent'=>$parent,'orphans'=>$n];
}
$out['lang_value_origins']=$pdo->query("SELECT meta->>'source' AS source,status,count(*) AS count FROM translation_workbench_lang_values GROUP BY meta->>'source',status ORDER BY count(*) DESC")->fetchAll(PDO::FETCH_ASSOC);
$out['recent_legacy_changed_fields']=$pdo->query("SELECT context->>'changed_fields' AS fields,count(*) AS count FROM (SELECT context FROM translation_workbench_events WHERE event_type='changed' ORDER BY id DESC LIMIT 10000) e GROUP BY context->>'changed_fields' ORDER BY count(*) DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
// A working copy for replay: only rows relevant to Foundation/values; no historical giant event tables.
$snapshot='/tmp/tw-status-snapshot-'.date('Ymd-His').'.sqlite';
$copy=new PDO('sqlite:'.$snapshot);$copy->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$copy->beginTransaction();
$selected=['translation_workbench_source_files','translation_workbench_findings','translation_workbench_keys','translation_workbench_key_findings','translation_workbench_lang_values','translation_workbench_key_values','translation_workbench_dynamic_key_values','translation_workbench_reviews','translation_workbench_event_types'];
foreach($out['tables'] as $table=>$info) {
 if(!str_starts_with($table,'translation_workbench_'))continue;
 $cols=[];$names=[];
 foreach($info['columns'] as $col){$name=$col['column_name'];$names[]='"'.$name.'"';$cols[]='"'.$name.'" '.($name==='id'?'INTEGER PRIMARY KEY':(in_array($col['data_type'],['bigint','integer','boolean'])?'INTEGER':'TEXT'));}
 $copy->exec('CREATE TABLE "'.$table.'" ('.implode(',',$cols).')');
 if(!in_array($table,$selected))continue;
 $insert=$copy->prepare('INSERT INTO "'.$table.'" ('.implode(',',$names).') VALUES ('.implode(',',array_fill(0,count($names),'?')).')');
 $read=$pdo->query('SELECT * FROM "'.$table.'" ORDER BY id');
 while($row=$read->fetch(PDO::FETCH_ASSOC)){$values=[];foreach($info['columns'] as $col){$v=$row[$col['column_name']];if(is_bool($v))$v=(int)$v;$values[]=$v;}$insert->execute($values);}
}
$copy->commit();chmod($snapshot,0600);$out['isolated_snapshot']=$snapshot;
