const fs = require('fs');
const postcss = require('postcss');
const tailwindcss = require('tailwindcss');
const cssnano = require('cssnano');

const css = fs.readFileSync('assets/css/admin.css', 'utf8');

postcss([
  tailwindcss,
  cssnano
])
.process(css, { from: 'assets/css/admin.css', to: 'assets/css/admin.min.css' })
.then(result => {
  fs.writeFileSync('assets/css/admin.min.css', result.css);
});