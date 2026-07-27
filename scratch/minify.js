const fs = require('fs');
const path = require('path');

const srcPath = path.join(__dirname, '../assets/js/hero-cycle-v2.js');
const distPath = path.join(__dirname, '../assets/js/hero-cycle-v2.min.js');

try {
  let code = fs.readFileSync(srcPath, 'utf8');

  // Strip single-line comments
  code = code.replace(/\/\/.*$/gm, '');
  // Strip block comments
  code = code.replace(/\/\*[\s\S]*?\*\//g, '');
  // Collapse whitespace
  code = code.replace(/\s+/g, ' ');
  // Compress around operators and punctuation
  code = code.replace(/\s*([=+\-*/{}()\[\];,<>:])\s*/g, '$1');

  fs.writeFileSync(distPath, code.trim(), 'utf8');
  console.log('Minified hero-cycle-v2.js successfully!');
} catch (err) {
  console.error('Error during minification:', err);
  process.exit(1);
}
