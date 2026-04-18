import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    rolldownOptions: {
      output: {
        codeSplitting: {
          groups: [
            {
              test: /node_modules\/react|react-dom/,
              name: 'vendor-react',
            },
            {
              test: /node_modules\/@mui|@lucide/,
              name: 'vendor-ui',
            },
          ],
        },
        // Option B: Traditional manualChunks
        // manualChunks(id) {
        //   if (id.includes('node_modules')) {
        //     return 'vendor';
        //   }
        // },
      },
    },
  },
  server: {
    origin: 'http://localhost:5173',
    port: 5173,
  },  
})
