import os
import re

theme_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19"
viewport_re = re.compile(r'viewport', re.IGNORECASE)

for root, dirs, files in os.walk(theme_dir):
    for f in files:
        if f.endswith('.php'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                if viewport_re.search(content):
                    print(f"Found viewport in: {f}")
                    for line in content.splitlines():
                        if 'viewport' in line.lower():
                            print(f"  Line: {line.strip()}")
