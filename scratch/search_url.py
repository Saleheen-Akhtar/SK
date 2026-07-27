import os

theme_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19"
for root, dirs, files in os.walk(theme_dir):
    for f in files:
        if 'config' in f.lower() or 'setting' in f.lower():
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                if 'localhost' in content or '.local' in content or '127.0.0.1' in content:
                    print(f"Found URL reference in {f}")
