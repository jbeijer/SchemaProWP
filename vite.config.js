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
      '@': path.resolve(__dirname, './src'),
      process: 'process/browser'
    }
  },
  define: {
    'process.env': {
      NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'production')
    }
  }
};

export default defineConfig(({ command, mode }) => {
  const isDev = mode === 'development';
  
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

  // Public build configuration
  return {
    ...sharedConfig,
    build: {
      ...sharedConfig.build,
      outDir: 'dist',
      lib: {
        entry: path.resolve(__dirname, 'src/public/public.js'),
        name: 'SchemaProWP',
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
});