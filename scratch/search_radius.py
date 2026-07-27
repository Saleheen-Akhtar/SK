import os

css_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css"
for root, dirs, files in os.walk(css_dir):
    for f in files:
        if f.endswith('.css'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                if '24px' in content or 'border-radius' in content:
                    print(f"Found border-radius in {f}:")
                    idx = 0
                    while True:
                        idx = content.find('border-radius', idx)
                        if idx == -1:
                            break
                        start = max(0, idx - 50)
                        end = min(len(content), idx + 100)
                        print(f"  Context: ...{content[start:end]}...")
                        idx += 13
