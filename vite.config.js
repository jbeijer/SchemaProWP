import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import path from 'path';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

const sharedConfig = {
  plugins: [
    svelte({
      preprocess: vitePreprocess(),
      compilerOptions: {
        dev: false
      }
    })
  ],
  build: {
    emptyOutDir: false,
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  }
};

export default defineConfig(({ command, mode }) => {
  if (mode === 'admin') {
    return {
      ...sharedConfig,
      build: {
        ...sharedConfig.build,
        outDir: 'dist',
        lib: {
          entry: path.resolve(__dirname, 'src/admin/main.js'),
          name: 'SchemaProWPAdmin',
          fileName: () => 'admin.js',
          formats: ['iife']
        },
        rollupOptions: {
          output: {
            assetFileNames: (assetInfo) => {
              if (assetInfo.name === 'style.css') return 'admin.css';
              return `assets/admin-[name].[ext]`;
            }
          }
        }
      }
    };
  }

  if (mode === 'public') {
    return {
      ...sharedConfig,
      build: {
        ...sharedConfig.build,
        outDir: 'dist',
        lib: {
          entry: path.resolve(__dirname, 'src/public/public.js'),
          name: 'SchemaProWPPublic',
          fileName: () => 'public.js',
          formats: ['iife']
        },
        rollupOptions: {
          output: {
            assetFileNames: (assetInfo) => {
              if (assetInfo.name === 'style.css') return 'public.css';
              return `assets/public-[name].[ext]`;
            }
          }
        }
      }
    };
  }
});