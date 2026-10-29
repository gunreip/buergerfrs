import json,re,collections,pathlib
import argparse, hashlib
parser=argparse.ArgumentParser(description='Offline classification of read-only native-label evidence; no import decisions.')
parser.add_argument('input',type=pathlib.Path)
parser.add_argument('output',type=pathlib.Path)
args=parser.parse_args()
if args.input.resolve()==args.output.resolve():raise ValueError('Input must not be overwritten')
raw=args.input.read_bytes()
r=json.loads(raw)
r['raw_evidence_sha256']=hashlib.sha256(raw).hexdigest()
index={x['native_id']:x for x in r['comparison']}
alltexts={x['value'] for x in r['lang_values'] if x['status']=='active'}|{x['value'] for x in r['dynamic_translations'] if x['status']=='active'}|{x['literal_text'] for x in r['active_literal_findings']}|{x['native_label'] for x in r['dynamic_source_labels'] if x['status']=='active' and x['source_status']=='active'}
for f in r['referenced_lang_files'].values():
 alltexts.update(x['value'] for x in f.get('values',[]))
def leaves(x,path=()):
 if isinstance(x,dict):return [v for k,t in x.items() for v in leaves(t,path+(k,))]
 if isinstance(x,list):return [v for k,t in enumerate(x) for v in leaves(t,path+(k,))]
 return [{'segments':path,'value':x,'is_string':isinstance(x,str),'same_text_elsewhere':isinstance(x,str) and x in alltexts}]
for row in r['native_rows']:
 label=row['native_label'];c=index[row['id']];c['current_reference_values']=[]
 m=re.fullmatch(r'(lang/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)\.php):(.+)',row['source_reference'] or '')
 if m:
  key=m[3]+'.'+m[4]
  c['current_reference_values']=[v for v in r['referenced_lang_files'].get(m[1],{}).get('values',[]) if v['key']==key or v['key'].startswith(key+'.')]
  c['same_text_at_reference']=any(v['key']==key and v['value']==label for v in c['current_reference_values'])
 try:parsed=json.loads(label)
 except (ValueError,TypeError):parsed=None
 if isinstance(parsed,(dict,list)):
  c['structured_text_candidate']=leaves(parsed)
  c['classification']='structured_payload_review'
 elif c.get('same_text_at_reference'):c['classification']='same_text_at_recorded_file_key'
 elif label in alltexts:c['classification']='same_text_other_evidence_only'
 else:c['classification']='no_current_text_witness'
r['review_summary']=dict(collections.Counter(c['classification'] for c in r['comparison']))
assert len(r['native_rows'])==len(r['comparison'])
assert len({x['native_id'] for x in r['comparison']})==len(r['native_rows'])
assert all(c['decision'] is None for c in r['comparison'])
assert all(f.get('unchanged_after_read') for f in r['referenced_lang_files'].values() if f['exists'])
r['compared_collection_counts']={k:len(r[k]) for k in ['active_literal_findings','dynamic_source_labels','lang_values','dynamic_translations']}
# Keep the witnesses actually referenced by comparisons; counts describe the original scope.
for table,field in [('active_literal_findings','active_literal_finding_ids'),('dynamic_source_labels','dynamic_source_value_ids'),('lang_values','active_lang_value_ids'),('dynamic_translations','active_dynamic_translation_ids')]:
 ids={i for c in r['comparison'] for i in c[field]}
 # Also retain witnesses for structured leaf texts.
 texts={leaf['value'] for c in r['comparison'] for leaf in c.get('structured_text_candidate',[]) if leaf['is_string']}
 valuefield={'active_literal_findings':'literal_text','dynamic_source_labels':'native_label'}.get(table,'value')
 r[table]=[x for x in r[table] if x['id'] in ids or x[valuefield] in texts]
args.output.write_text(json.dumps(r,ensure_ascii=False,indent=2)+'\n')
print(json.dumps(r['review_summary']))
