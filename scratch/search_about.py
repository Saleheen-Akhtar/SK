path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\inc\helpers.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

lines = content.split('\n')
for idx, line in enumerate(lines):
    if 'function sk_acf' in line:
        for i in range(max(0, idx - 2), min(len(lines), idx + 20)):
            print(f"{i+1}: {lines[i]}")
