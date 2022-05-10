const path = require('path')
const fs = require('fs')
const mix = require('laravel-mix')
// const TerserPlugin = require('terser-webpack-plugin')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// this plugin is required to automatically load Vuetify components
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')
const { GenerateSW } = require('workbox-webpack-plugin')

const outputPath = function (path, prefix) {
  const matches = path.match(/^packages\/([a-z0-9-]+)\/.+\/([a-z0-9-_]+\.[a-z0-9]{3,4})$/i)

  return matches !== null && matches.length === 3
    ? `${prefix}/${matches[1]}/${matches[2]}`
    : `${path.replace('resources/', '')}`
}

const _module = {
  rules: [
    // { test: /\.css$/, loader: 'css-loader' },
    {
      test: /\.(wav|mp3|mp4|webm)$/i,
      use: [
        {
          loader: 'file-loader',
          options: {
            // it's important to specify [path], so it's passed to outputPath() function
            name: '[path][name].[ext]',
            // generate custom output path depending on package ID
            outputPath: path => outputPath(path, 'audio')
          }
        }
      ]
    },
    {
      test: /\.glb$/i,
      use: [
        {
          loader: 'file-loader',
          options: {
            // it's important to specify [path], so it's passed to outputPath() function
            name: '[path][name].[ext]',
            // generate custom output path depending on package ID
            outputPath: path => outputPath(path, 'models')
          }
        }
      ]
    },
    {
      test: /_variables.scss$/,
      include: path.resolve(__dirname, 'resources/sass'),
      use: [
        'vue-style-loader',
        'css-loader',
        {
          loader: 'sass-loader',
          options: {
            additionalData: "@import '@/resources/sass/_variables.scss';"
          }
        }
      ]
    }
  ]
}

const plugins = [
  //
]

const resolve = {
  alias: {
    '~': path.resolve(__dirname, './resources/js'),
    scss: path.resolve(__dirname, './resources/sass'),
    packages: path.resolve(__dirname, './packages')
  },
  extensions: ['.js', '.json', '.vue'],
  fallback: {
    crypto: require.resolve('crypto-browserify'),
    http: require.resolve('stream-http'),
    https: require.resolve('https-browserify'),
    os: false,
    stream: require.resolve('stream-browserify')
  }
}

const optimization = {
  // minimizer: [
  //   new TerserPlugin({
  //     extractComments: false
  //   })
  // ],
  splitChunks: {
    minSize: 0,
    automaticNameDelimiter: '-',
    cacheGroups: {
      default: false, // To disable any of the default cache groups, set them to false.
      // packages: {
      //   maxAsyncRequests: 1,
      //   test: (module, chunks) => module.resource && module.resource.match(/\\packages\\[a-z0-9-]+\\resources\\.+\.vue$/),
      //   name: (module, chunks, cacheGroupKey) => {
      //     const gameId = module.resource.match(/\\packages\\([a-z0-9-]+)\\resources\\/)[1]
      //     const [name, ext] = (path.parse(module.resource).base).split('.')
      //     return `js/packages/${gameId}/${name}`
      //   }
      // },
      vueComponents: {
        test: module => module.resource && module.resource.match(/[\\/]js[\\/]components[\\/].+\.vue$/),
        name: 'components'
      },
      vueMixins: {
        test: module => module.resource && module.resource.match(/[\\/]js[\\/]mixins[\\/].+\.vue$/),
        name: 'mixins'
      },
      vendor: {
        test: /[\\/]node_modules[\\/]/,
        // cacheGroupKey here is `vendor` as the key of the cacheGroup
        name: (module, chunks, cacheGroupKey) => {
          // get the module name (folder name in node_modules)
          const packageName = module.resource.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1]
          // npm package names are URL-safe, but some servers don't like @ symbols, so strip it
          return `vendor/${packageName.replace('@', '')}`
        }
      }
    }
  }
}


// https://stackoverflow.com/a/63255974/2767324
mix.extend('vuetify', new class {
  webpackConfig (config) {
    config.plugins.push(new VuetifyLoaderPlugin())
  }
})

mix.webpackConfig({ module: _module, plugins, resolve, optimization })

mix
  .js('resources/js/app.js', 'public/js')
  .vuetify('vuetify-loader')
  .vue()
  .sourceMaps()

if (mix.inProduction()) {
  mix.version()
}
