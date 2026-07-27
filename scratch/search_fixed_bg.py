import os
import re

css_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css"
fixed_bg_re = re.compile(r'background-attachment\s*:\s*fixed', re.IGNORECASE)

for root, dirs, files in os.walk(css_dir):
    for f in files:
        if f.endswith('.css'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                matches = [m.start() for m in re.finditer(fixed_bg_re, content)]
                if matches:
                    print(f"File {f} contains background-attachment: fixed:")
                    for match in matches:
                        print("  Snippet:", content[max(0, match - 80):min(len(content), match + 120)].replace('\n', ' ').strip())
