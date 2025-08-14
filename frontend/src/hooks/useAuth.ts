import { useState, useEffect } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import api from '../api'

interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

interface LoginCredentials {
  email: string
  password: string
  device_name: string
}

interface AuthResponse {
  message: string
  user: User
  token: string
  token_type: string
}

export function useAuth() {
  const [isAuthenticated, setIsAuthenticated] = useState(false)
  const [user, setUser] = useState<User | null>(null)
  const [isLoadingUser, setIsLoadingUser] = useState(false)
  const queryClient = useQueryClient()

  // Verificar se há token no localStorage
  useEffect(() => {
    const token = localStorage.getItem('token')
    console.log('Token no localStorage:', token ? 'existe' : 'não existe')
    setIsAuthenticated(!!token)
  }, [])

  // Buscar dados do usuário quando autenticado
  useEffect(() => {
    if (isAuthenticated && !user) {
      const fetchUser = async () => {
        setIsLoadingUser(true)
        try {
          const response = await api.get('/auth/user')
          const userData = response.data?.user || response.data?.data?.user
          if (userData) {
            setUser(userData)
          }
        } catch (error) {
          console.error('Erro ao buscar usuário:', error)
          localStorage.removeItem('token')
          setIsAuthenticated(false)
        } finally {
          setIsLoadingUser(false)
        }
      }
      fetchUser()
    }
  }, [isAuthenticated, user])

  // Mutation para login
  const loginMutation = useMutation({
    mutationFn: async (credentials: LoginCredentials) => {
      const response = await api.post<AuthResponse>('/auth/login', credentials)
      return response.data
    },
    onSuccess: (data) => {
      console.log('Login bem-sucedido, salvando token...')
      localStorage.setItem('token', data.token)
      setUser(data.user)
      setIsAuthenticated(true)
    },
    onError: (error) => {
      console.error('Erro na mutation de login:', error)
    }
  })

  // Mutation para logout
  const logoutMutation = useMutation({
    mutationFn: async () => {
      await api.post('/auth/logout')
    },
    onSuccess: () => {
      localStorage.removeItem('token')
      setUser(null)
      setIsAuthenticated(false)
      queryClient.clear()
    }
  })

  const login = (credentials: LoginCredentials) => {
    return loginMutation.mutateAsync(credentials)
  }

  const logout = () => {
    return logoutMutation.mutateAsync()
  }

  return {
    user,
    isAuthenticated,
    isLoadingUser,
    login,
    logout,
    isLoggingIn: loginMutation.isPending,
    isLoggingOut: logoutMutation.isPending,
    loginError: loginMutation.error
  }
}
