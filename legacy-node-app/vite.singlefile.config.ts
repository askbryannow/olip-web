// Produces a self-contained single-file HTML build (npm run build:demo) that always
// runs on the seeded demo dataset — useful for sharing a clickable preview of the
// dashboard without a backend. Not used for the real deployable build (see vite.config.ts).
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { viteSingleFile } from 'vite-plugin-singlefile';

export default defineConfig({
  plugins: [react(), tailwindcss(), viteSingleFile()],
  build: {
    outDir: 'dist-singlefile',
    cssCodeSplit: false,
    assetsInlineLimit: 100000000,
  },
});
