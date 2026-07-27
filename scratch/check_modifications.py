import os
import time

workspace_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19"
now = time.time()
two_hours = 2 * 3600

recently_modified = []

for root, dirs, files in os.walk(workspace_dir):
    # Skip standard folders like .git, node_modules, etc.
    if any(p in root for p in ['node_modules', '.git', '.gemini', 'scratch']):
        continue
    for f in files:
        path = os.path.join(root, f)
        try:
            mtime = os.path.getmtime(path)
            age = now - mtime
            if age < two_hours:
                recently_modified.append((path, mtime))
        except OSError:
            pass

recently_modified.sort(key=lambda x: x[1], reverse=True)

if recently_modified:
    print(f"Found {len(recently_modified)} recently modified files:")
    for path, mtime in recently_modified:
        local_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(mtime))
        rel_path = os.path.relpath(path, workspace_dir)
        print(f"- {rel_path} (modified at {local_time})")
else:
    print("No recently modified files found in the last 2 hours.")
