path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-philosophy.css"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\philosophy_strip.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = [m.start() for m in re.finditer(r'\bstrip\b', content, re.IGNORECASE)]
with open(output_path, 'w', encoding='utf-8') as out:
    for idx, match in enumerate(matches):
        out.write(f"\n--- Match {idx+1} at index {match} ---\n")
        out.write(content[max(0, match - 50):min(len(content), match + 300)])
        out.write("\n")
print(f"Extracted matches to {output_path}")
