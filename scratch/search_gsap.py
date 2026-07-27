path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\js\gsap-animations.js"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\search_gsap.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = [m.start() for m in re.finditer(r'av16|about|content-card', content, re.IGNORECASE)]
with open(output_path, 'w', encoding='utf-8') as out:
    for idx, match in enumerate(matches):
        out.write(f"\nMatch {idx+1}:\n")
        out.write(content[max(0, match - 80):min(len(content), match + 200)])
        out.write("\n")
print(f"Extracted {len(matches)} matches")
