import { useQuery } from '@tanstack/react-query'
import api from '../api'

export interface AreaNode {
  id: number
  name: string
  children?: AreaNode[]
  key?: number | string
}

interface ApiResponse<T> {
  success: boolean
  data: T
  timestamp?: string
}

async function fetchAreaTree(): Promise<AreaNode[]> {
  const response = await api.get<ApiResponse<AreaNode[]>>('/areas/tree')
  return response.data.data
}

export function useAreaTree() {
  return useQuery({ 
    queryKey: ['areaTree'], 
    queryFn: fetchAreaTree,
    retry: 1,
    refetchOnWindowFocus: false
  })
}
