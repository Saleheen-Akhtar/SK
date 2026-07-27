import json

log_path = r'C:\Users\sahil\.gemini\antigravity\brain\5519e7b2-d74e-4748-a0fd-16661e9b2845\.system_generated\logs\transcript_full.jsonl'
out_path = r'scratch/all_edits.txt'

with open(log_path, 'r', encoding='utf-8') as f, open(out_path, 'w', encoding='utf-8') as out:
    for line in f:
        data = json.loads(line)
        if data.get('type') == 'CODE_ACTION':
            content = data.get('content', '')
            if 'stories-archive.css' in content:
                out.write(f"--- Step {data.get('step_index')} ---\n")
                out.write(content + "\n\n")
