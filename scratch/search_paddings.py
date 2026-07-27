import os

css_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css"
for root, dirs, files in os.walk(css_dir):
    for f in files:
        if f.endswith('.css'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                lines = file.readlines()
                for idx, line in enumerate(lines):
                    if 'padding-top' in line and ('1400px' in line or '1200px' in line or 'min-width' in line or 'clamp' in line):
                        print(f"{f}:{idx+1}: {line.strip()}")
