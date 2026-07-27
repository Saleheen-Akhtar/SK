import os

theme_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19"
for root, dirs, files in os.walk(theme_dir):
    for f in files:
        if f.endswith('.php') or f.endswith('.css'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                if 'vedic astrology' in content.lower():
                    print(f"Found in {f}")
