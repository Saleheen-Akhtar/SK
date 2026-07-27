path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-about.css"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\extracted_rules.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
# Find all occurrences of .about-v16 and show their context
matches = [m.start() for m in re.finditer(r'\.about-v16\b', content)]

with open(output_path, 'w', encoding='utf-8') as out:
    for idx, match in enumerate(matches):
        out.write(f"\n--- Match {idx+1} at index {match} ---\n")
        out.write(content[max(0, match - 50):min(len(content), match + 500)])
        out.write("\n")
print(f"Extracted {len(matches)} matches to {output_path}")
