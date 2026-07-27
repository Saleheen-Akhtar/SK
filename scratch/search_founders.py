path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-founders.css"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\founders_medias.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = [m.start() for m in re.finditer(r'@media', content)]
with open(output_path, 'w', encoding='utf-8') as out:
    for idx, match in enumerate(matches):
        brace_count = 0
        in_block = False
        end_idx = match
        while end_idx < len(content):
            if content[end_idx] == '{':
                brace_count += 1
                in_block = True
            elif content[end_idx] == '}':
                brace_count -= 1
            end_idx += 1
            if in_block and brace_count == 0:
                break
        out.write(f"\n--- Media Query {idx+1} at index {match} ---\n")
        out.write(content[match:end_idx])
        out.write("\n")
print(f"Extracted {len(matches)} queries to {output_path}")
