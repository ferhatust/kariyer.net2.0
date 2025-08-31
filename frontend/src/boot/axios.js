import { defineBoot } from '#q-app/wrappers'
import axios from 'axios'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: '/api', headers: { 'Accept': 'application/json' } })

export default defineBoot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  // attach token if exists
  api.interceptors.request.use((config) => {
    const token = localStorage.getItem('api_token')
    if (token) {
      config.headers = config.headers || {}
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  })

  // Global error handler
  api.interceptors.response.use(
    (res) => res,
    (error) => {
      try {
        const status = error?.response?.status
        const msg = error?.response?.data?.message || error?.message || 'Beklenmedik bir hata oluştu'
        // Lazy import to avoid circular deps; Notify may not be available here so use window if present
        if (typeof window !== 'undefined') {
          const { Notify } = require('quasar')
          // Network/offline errors have no response
          const isNetwork = !error?.response || error?.code === 'ERR_NETWORK'
          if (isNetwork) {
            Notify.create({ type: 'warning', message: 'Ağ bağlantısı yok veya erişilemiyor. Lütfen internetinizi kontrol edin.' })
          } else if (status === 401) {
            Notify.create({ type: 'warning', message: 'Oturum süresi doldu. Lütfen tekrar giriş yapın.' })
            // Optional: localStorage.removeItem('api_token')
          } else if (status === 429) {
            Notify.create({ type: 'warning', message: 'Çok fazla istek. Lütfen biraz sonra tekrar deneyin.' })
          } else if (status >= 500) {
            Notify.create({ type: 'negative', message: 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.' })
          } else {
            Notify.create({ type: 'negative', message: msg })
          }
        }
      } catch { /* ignore notify errors */ }
      return Promise.reject(error)
    }
  )

  app.config.globalProperties.$api = api
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { api }
