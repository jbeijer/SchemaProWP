import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte()],
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        admin: path.resolve(__dirname, 'admin/Admin.svelte'),
        public: path.resolve(__dirname, 'public/Public.svelte'),
      },
      output: {
        dir: '../dist',
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name].[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];
          if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
            return `images/[name][extname]`;
          }
          if (/\.css$/i.test(assetInfo.name)) {
            return `css/[name][extname]`;
          }
          return `assets/[name][extname]`;
        },
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './'),
      '@components': path.resolve(__dirname, './components'),
      '@stores': path.resolve(__dirname, './stores'),
    },
  },
});
