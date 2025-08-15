import axios from 'axios'

// Detecta automaticamente a URL da API baseado no ambiente
const getApiUrl = () => {
  // Se tem localhost no link, usa localhost
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    return 'http://localhost/api'
  }
  // Para todos os outros casos, usa a URL atual + /api
  return import.meta.env.VITE_API_URL || `${window.location.origin}/api`
}

const apiUrl = getApiUrl()

const api = axios.create({
  baseURL: apiUrl,
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api
