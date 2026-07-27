import os
import re

css_dir = r"c:\Users\sahil\Projects\fixed_v19 (9)\fixed_v19 (29)\fixed_v19\assets\css"
mobile_media_re = re.compile(r'@media\s*\([^)]*max-width\s*:\s*(?:768|860|900|1024)[^)]*\)\s*\{', re.IGNORECASE)
vh_re = re.compile(r'\b\d+(?:vh|dvh|svh)\b', re.IGNORECASE)

for root, dirs, files in os.walk(css_dir):
    for f in files:
        if f.endswith('.css'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                
                # Let's find all media queries
                # Find matching braces to extract blocks
                pos = 0
                for match in re.finditer(r'@media[^{]*\{', content):
                    media_query_head = match.group(0)
                    if 'max-width' in media_query_head:
                        # Find the corresponding closing brace for this media query
                        start_idx = match.end()
                        brace_count = 1
                        end_idx = start_idx
                        while brace_count > 0 and end_idx < len(content):
                            if content[end_idx] == '{':
                                brace_count += 1
                            elif content[end_idx] == '}':
                                brace_count -= 1
                            end_idx += 1
                        
                        media_block = content[start_idx:end_idx]
                        if vh_re.search(media_block):
                            print(f"File {f} has vh/dvh in mobile media query: {media_query_head.strip()}")
                            for line in media_block.splitlines():
                                if any(x in line for x in ['vh', 'dvh', 'svh', 'height']):
                                    print(f"  Line: {line.strip()}")
