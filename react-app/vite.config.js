import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  base: '/wp-content/plugins/starter-app-juzt-stack-plugin-react-v1/assets/',
  build: {
    outDir: path.resolve(__dirname, '../assets'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.jsx')
    }
  }
})