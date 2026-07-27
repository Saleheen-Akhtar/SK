path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css\home-philosophy.css"
output_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\scratch\strip_circular_props.txt"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('body .strip--circular')
if idx != -1:
    with open(output_path, 'w', encoding='utf-8') as out:
        out.write(content[idx:idx+800])
    print(f"Extracted properties to {output_path}")
else:
    print("Not found")
