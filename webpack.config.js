const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'scripts': path.resolve(process.cwd(), 'src', 'index.js'), // Assuming your main JS file is src/index.js
    },
    // You might need additional configuration here to handle Font Awesome's assets
};
