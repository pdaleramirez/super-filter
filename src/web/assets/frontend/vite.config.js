import {fileURLToPath, URL} from 'node:url'
import {defineConfig, loadEnv} from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/

export default ({mode}) => {
  process.env = {...process.env, ...loadEnv(mode, process.cwd())};
  return defineConfig({
    plugins: [
      vue(),
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
        vue: 'vue/dist/vue.esm-bundler.js'
      }
    },
    build: {
      // rollupOptions: {
      //   input: {
      //     app: './src/main.html',
      //   },
      // },
      manifest: true,
    },
    server: {
      proxy: {
        "/api": {
          target: process.env.VITE_SITE_URL,
          changeOrigin: true,
          secure: false,
          rewrite: (path) => path.replace(/^\/api/, ""),
        }
      }
    }
  })
}
