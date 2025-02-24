import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import path from 'path';

const sharedConfig = {
  plugins: [svelte()],
  build: {
    outDir: 'dist',
    emptyOutDir: false,
    rollupOptions: {
      output: {
        entryFileNames: `[name].js`,
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') {
            return 'shared.css';
          }
          return `[name].[ext]`;
        }
      }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
    },
  },
};

export default defineConfig(({ command, mode }) => {
  if (mode === 'admin') {
    return {
      ...sharedConfig,
      build: {
        ...sharedConfig.build,
        lib: {
          entry: path.resolve(__dirname, 'src/admin/admin.js'),
          name: 'SchemaProWPAdmin',
          fileName: () => `admin.js`,
          formats: ['iife'],
        },
      },
    };
  } else {
    return {
      ...sharedConfig,
      build: {
        ...sharedConfig.build,
        lib: {
          entry: path.resolve(__dirname, 'src/public/public.js'),
          name: 'SchemaProWPPublic',
          fileName: () => `public.js`,
          formats: ['iife'],
        },
      },
    };
  }
});