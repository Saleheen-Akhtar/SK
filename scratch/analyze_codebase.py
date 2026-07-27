import os
import re

workspace_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19"
output_file = r"C:\Users\sahil\.gemini\antigravity\brain\ffe83332-a7c2-421e-a519-b0ed2cc44d6e\codebase_analysis.md"

ignore_dirs = {'.git', '.agents', 'fixed_v19', 'scratch', 'Doc', 'node_modules'}
allowed_extensions = {'.php', '.css', '.js', '.json', '.txt'}

def get_file_info(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except Exception as e:
        return None
    
    lines = content.splitlines()
    line_count = len(lines)
    
    # Extract header comment (if any)
    header = ""
    comment_match = re.match(r'^\s*(/\*\*.*?\*/|/\*.*?\*/|<!--.*?-->)', content, re.DOTALL)
    if comment_match:
        header = comment_match.group(1).strip()
        # Clean up comment markings
        header = re.sub(r'^/\*\*?\s*', '', header)
        header = re.sub(r'\s*\*/$', '', header)
        header = re.sub(r'^\s*\*\s*', '', header, flags=re.MULTILINE)
        header = header.strip()
    
    # Identify functions/features based on extension
    features = []
    ext = os.path.splitext(filepath)[1]
    if ext == '.php':
        # Find functions
        fns = re.findall(r'function\s+([a-zA-Z0-9_]+)\s*\(', content)
        if fns:
            features.append(f"Functions: {', '.join(fns[:10])}" + ("..." if len(fns) > 10 else ""))
        
        # Find add_action / add_filter
        actions = re.findall(r"add_action\(\s*['\"]([a-zA-Z0-9_-]+)['\"]", content)
        if actions:
            features.append(f"Actions: {', '.join(set(actions[:5]))}" + ("..." if len(set(actions)) > 5 else ""))
        
        filters = re.findall(r"add_filter\(\s*['\"]([a-zA-Z0-9_-]+)['\"]", content)
        if filters:
            features.append(f"Filters: {', '.join(set(filters[:5]))}" + ("..." if len(set(filters)) > 5 else ""))
            
    elif ext == '.css':
        # Find media queries
        medias = re.findall(r'@media\s*([^{]+)', content)
        if medias:
            features.append(f"Media queries: {len(medias)}")
        # Count classes
        classes = len(set(re.findall(r'\.([a-zA-Z0-9_-]+)\s*[{,]', content)))
        features.append(f"Unique class styles: {classes}")
        
    elif ext == '.js':
        # Find functions
        fns = re.findall(r'function\s+([a-zA-Z0-9_]+)\s*\(', content)
        if fns:
            features.append(f"Functions: {', '.join(fns[:10])}")
        # Find GSAP animations
        gsaps = re.findall(r'gsap\.(to|from|timeline|fromTo)', content)
        if gsaps:
            features.append(f"GSAP animations: {len(gsaps)}")
            
    return {
        'line_count': line_count,
        'size': len(content),
        'header': header,
        'features': features
    }

def main():
    report = []
    report.append("# Sacred Kompass Codebase Analysis\n")
    report.append("This is an automatically generated index of the codebase structure and key components.\n")
    
    # Walk directories
    files_by_dir = {}
    for root, dirs, files in os.walk(workspace_dir):
        # Filter ignore dirs in-place
        dirs[:] = [d for d in dirs if d not in ignore_dirs]
        
        rel_dir = os.path.relpath(root, workspace_dir)
        if rel_dir == '.':
            rel_dir = 'Root'
            
        for file in files:
            ext = os.path.splitext(file)[1]
            if ext in allowed_extensions:
                full_path = os.path.join(root, file)
                info = get_file_info(full_path)
                if info:
                    if rel_dir not in files_by_dir:
                        files_by_dir[rel_dir] = []
                    files_by_dir[rel_dir].append((file, full_path, info))
                    
    # Generate report
    for folder, files_list in sorted(files_by_dir.items()):
        report.append(f"## Directory: {folder}\n")
        for filename, filepath, info in sorted(files_list, key=lambda x: x[0]):
            web_path = filepath.replace('\\', '/')
            report.append(f"### [{filename}](file:///{web_path})")
            report.append(f"- **Lines**: {info['line_count']} | **Size**: {info['size']} bytes")
            if info['header']:
                # Clean description
                desc = info['header'].split('\n')[0]
                report.append(f"- **Description**: {desc}")
            if info['features']:
                for feat in info['features']:
                    report.append(f"- **Details**: {feat}")
            report.append("") # newline
            
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write('\n'.join(report))
        
    print(f"Report written to {output_file}")

if __name__ == '__main__':
    main()
