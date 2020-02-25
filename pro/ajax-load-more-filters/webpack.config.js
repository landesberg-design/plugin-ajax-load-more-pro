var ExtractTextPlugin = require('extract-text-webpack-plugin');
var path = require('path');
var dir = 'dist';

module.exports = {
   entry: {
      'filters': "./src/js/frontend/index.js",
      'styles': './src/scss/filters.scss',
      'admin': "./src/js/admin/index.js",
      'admin_styles': './src/scss/admin.scss'
   }, 
	output: {
   	path: path.join(__dirname, 'dist'),
      filename: "js/[name].js",
      library: 'almfilters',
      libraryTarget: 'var'
   },
	watch: true,
	module: {
		rules: [
		{
			test: /.jsx?$/,
			loader: 'babel-loader',
			exclude: /node_modules/,
			query: {
				presets: ['env']
			}
		},
		{ 
   		test: /\.(jpe?g|gif|png|svg|woff|ttf|wav|mp3)$/, 
   		loader: "file-loader",
   		options: {
            name: 'img/[name].[ext]',
            publicPath: '../'
         }
      },
		{
			test: /\.scss$/,
			use: ExtractTextPlugin.extract({
				fallback: "style-loader",
				use: [
               { loader: 'css-loader' },
               { loader: 'postcss-loader' },
               { loader: 'sass-loader', 
                  options: {
                     outputStyle: 'expanded'
                  },
               }
             ]
			}),
			exclude: /node_modules/,
		}
	]},	
	plugins: [
		new ExtractTextPlugin({ filename: './css/[name].css' })
	]
	
};