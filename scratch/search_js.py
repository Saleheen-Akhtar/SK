import os

js_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\js"
for root, dirs, files in os.walk(js_dir):
    for f in files:
        if f.endswith('.js'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                if 'offsetHeight' in content:
                    print(f"Found offsetHeight in {f}:")
                    idx = 0
                    while True:
                        idx = content.find('offsetHeight', idx)
                        if idx == -1:
                            break
                        start = max(0, idx - 50)
                        end = min(len(content), idx + 100)
                        print(f"  Context: ...{content[start:end]}...")
                        idx += 12
