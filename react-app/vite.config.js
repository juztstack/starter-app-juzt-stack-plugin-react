import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

// Obtiene el comando actual (serve o build)
export default defineConfig(({ command }) => {
  const isDev = command === 'serve';
  
  // La ruta base de producción, tal como la tenías configurada
  const prodBase = '/wp-content/plugins/starter-app-juzt-stack-plugin-react-v1/assets/';

  return {
    // En desarrollo (serve), la base DEBE ser '/', ya que Vite se sirve desde la raíz de su propio puerto (5173).
    // En producción (build), usamos la ruta completa del plugin.
    base: isDev ? '/' : prodBase,
    
    plugins: [react()],

    server: {
      cors: true,
    },
    
    build: {
      // Configuraciones de producción (build)
      outDir: path.resolve(__dirname, '../assets'),
      emptyOutDir: true,
      manifest: true,
      
      rollupOptions: {
        input: path.resolve(__dirname, 'src/main.jsx')
      }
    }
  }
});