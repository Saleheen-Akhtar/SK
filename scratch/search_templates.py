import os

dir_path = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\template-parts"
for root, dirs, files in os.walk(dir_path):
    for f in files:
        if 'story' in f.lower() or 'stories' in f.lower():
            print(os.path.join(root, f))
