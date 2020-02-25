var webpack = require('webpack');
var config = require('../webpack.config.js');

config.watch = false;

config.entry = {
	'filters.min': "./src/js/frontend/index.js",
	'styles': './src/scss/filters.scss',
	'admin': "./src/js/admin/index.js",
	'admin_styles': './src/scss/admin.scss'
};

config.plugins.push(
	new webpack.DefinePlugin({
		'process.env': {
			NODE_ENV: '"production"'
		}
	})
);

module.exports = config;
