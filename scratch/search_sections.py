path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\front-page.php"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\sections.txt"

with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

with open(output_path, 'w', encoding='utf-8') as out:
    for idx, line in enumerate(lines):
        if 'id=' in line or 'section' in line or 'offerings' in line or 'astrology' in line:
            out.write(f"Line {idx+1}: {line.strip()}\n")
print(f"Extracted section references to {output_path}")
