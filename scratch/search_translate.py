path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-about.css"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\search_translate.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = [m.start() for m in re.finditer(r'translate', content)]
with open(output_path, 'w', encoding='utf-8') as out:
    for idx, match in enumerate(matches):
        line_no = content.count('\n', 0, match) + 1
        out.write(f"\nMatch {idx+1} at line {line_no}:\n")
        out.write(content[max(0, match - 80):min(len(content), match + 200)])
        out.write("\n")
print(f"Extracted {len(matches)} matches")
