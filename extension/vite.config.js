import { defineConfig } from 'vite';
import preact from '@preact/preset-vite';
import { resolve } from 'path';
import { copyFileSync, mkdirSync, existsSync, readdirSync } from 'fs';

// Plugin to copy static extension assets (manifest.json, icons, CSS) to dist
function copyExtensionAssets() {
  return {
    name: 'copy-extension-assets',
    closeBundle() {
      const distDir = resolve(__dirname, 'dist');

      // Copy manifest.json
      copyFileSync(
        resolve(__dirname, 'manifest.json'),
        resolve(distDir, 'manifest.json')
      );

      // Copy icons directory
      const iconsSource = resolve(__dirname, 'icons');
      const iconsDest = resolve(distDir, 'icons');
      if (!existsSync(iconsDest)) mkdirSync(iconsDest, { recursive: true });
      for (const file of readdirSync(iconsSource)) {
        copyFileSync(resolve(iconsSource, file), resolve(iconsDest, file));
      }

      // Copy content script CSS
      const contentDest = resolve(distDir, 'src', 'content');
      if (!existsSync(contentDest)) mkdirSync(contentDest, { recursive: true });
      copyFileSync(
        resolve(__dirname, 'src', 'content', 'highlight.css'),
        resolve(contentDest, 'highlight.css')
      );

      console.log('✅ Extension assets copied to dist/');
    },
  };
}

export default defineConfig({
  plugins: [preact(), copyExtensionAssets()],
  base: './',
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: {
        popup: resolve(__dirname, 'popup.html'),
        sidebar: resolve(__dirname, 'sidebar.html'),
        options: resolve(__dirname, 'options.html'),
        background: resolve(__dirname, 'src/background/index.js'),
        content: resolve(__dirname, 'src/content/index.js'),
      },
      output: {
        entryFileNames: 'src/[name]/index.js',
        chunkFileNames: 'chunks/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]',
      },
    },
    emptyOutDir: true,
  },
});
