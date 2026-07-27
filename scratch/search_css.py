path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-about.css"
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()
for idx, line in enumerate(lines):
    if '.about-v16' in line:
        print(f"Line {idx+1}: {line.strip()}")
