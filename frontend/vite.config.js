import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
// Strategy: Tránh CORS bằng 2 lớp
// 1. Dev: Vite proxy /api và /storage -> Laravel (pano-admin.test) => same-origin cho browser
// 2. Production: Build React vào Laravel public/ => frontend và backend cùng origin, không CORS bao giờ
// Khi deploy hosting chỉ cần upload Laravel (đã chứa build), không cần CORS config
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const proxyTarget = env.VITE_PROXY_TARGET || 'http://127.0.0.1:8000'
  return {
  plugins: [react()],
  server: {
    // proxy để dev không bị CORS
    proxy: {
      '/api': {
        target: proxyTarget,
        changeOrigin: true,
        secure: false,
      },
      '/storage': {
        target: proxyTarget,
        changeOrigin: true,
        secure: false,
      },
    },
  },
  // Build ra thư mục mà Laravel có thể serve - giữ nguyên dist cho dev,
  // khi deploy sẽ copy vào laravel public (script deploy)
  build: {
    outDir: 'dist',
    emptyOutDir: true,
  },
  }
})
