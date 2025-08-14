import { useQuery } from '@tanstack/react-query'
import api from '../api'

export interface AreaNode {
  id: number
  name: string
  children?: AreaNode[]
  key?: number | string
}

async function fetchAreaTree(): Promise<AreaNode[]> {
  const { data } = await api.get<AreaNode[]>('/areas/tree')
  return data
}

export function useAreaTree() {
  return useQuery({ queryKey: ['areaTree'], queryFn: fetchAreaTree })
}
