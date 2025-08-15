import axios from 'axios'

// Detecta automaticamente a URL da API baseado no ambiente
const getApiUrl = () => {
  // Se estiver no Render, usa a URL do Render
  if (window.location.hostname.includes('render.com') || window.location.hostname.includes('onrender.com')) {
    return window.location.origin + '/api'
  }
  // Se estiver no GitHub Pages, não tem API
  if (window.location.hostname.includes('github.io')) {
    return null
  }
  // Local development
  return import.meta.env.VITE_API_URL || 'http://localhost/api'
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
