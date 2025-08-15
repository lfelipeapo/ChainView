import { Button, Modal, Form, Input, Card, message, Tree, Collapse, Select, Popconfirm, Tooltip, Tag, Space, Image } from 'antd'
import { PlusOutlined, FolderOutlined, FileOutlined, EditOutlined, DeleteOutlined, ToolOutlined, UserOutlined, FileTextOutlined, LinkOutlined, EyeOutlined, UpOutlined, DownOutlined, SearchOutlined, ReloadOutlined, NodeIndexOutlined } from '@ant-design/icons'
import { useState, useEffect } from 'react'
import { useQueryClient } from '@tanstack/react-query'
import { useNavigate } from 'react-router-dom'
import api from '../api'
import { useAreaTree, type AreaNode } from '../hooks/useAreaTree'

interface ProcessNode {
  id: number
  area_id: number
  parent_id: number | null
  name: string
  description: string
  type: string
  criticality: string
  status: string
  created_at: string | null
  updated_at: string | null
  children?: ProcessNode[]
  tools?: string
  responsible?: string
  documentation?: string
}

interface ApiResponse<T> {
  success: boolean
  data: T
  timestamp?: string
}

interface ApiMessageResponse {
  success: boolean
  message: string
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  data?: any
  timestamp?: string
}

// Função para detectar URLs
const isUrl = (text: string): boolean => {
  try {
    new URL(text)
    return true
  } catch {
    return false
  }
}

// Função para detectar se é uma imagem
const isImageUrl = (url: string): boolean => {
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  const lowerUrl = url.toLowerCase()
  return imageExtensions.some(ext => lowerUrl.includes(ext))
}

// Função para extrair URLs do texto
const extractUrls = (text: string): string[] => {
  const urlRegex = /(https?:\/\/[^\s]+)/g
  return text.match(urlRegex) || []
}

// Componente para renderizar preview de links e descrições
const PreviewRenderer = ({ text, type }: { text: string, type: 'description' | 'documentation' }) => {
  const [showPreview, setShowPreview] = useState(false)
  const urls = extractUrls(text)
  const hasUrls = urls.length > 0
  const isImage = hasUrls && isImageUrl(urls[0])
  
  // Validar se o texto contém URLs válidas
  const hasValidUrls = isUrl(text) && hasUrls

  if (!text) return null

  return (
    <div style={{ marginTop: 8 }}>
      <div style={{
        fontSize: '12px',
        color: '#666',
        lineHeight: '1.4',
        whiteSpace: 'pre-wrap',
        wordBreak: 'break-word',
        maxWidth: '100%'
      }}>
        {text}
      </div>

      {hasValidUrls && (
        <div style={{ marginTop: 8 }}>
          <Button
            type="link"
            size="small"
            icon={<EyeOutlined />}
            onClick={() => setShowPreview(!showPreview)}
            style={{ padding: 0, height: 'auto', fontSize: '11px' }}
          >
            {showPreview ? 'Ocultar Preview' : `Ver Preview (${type})`}
          </Button>

          {showPreview && (
            <div style={{ marginTop: 8, maxWidth: '100%', overflow: 'hidden' }}>
              {urls.map((url, index) => (
                <div key={index} style={{ marginBottom: 8, maxWidth: '100%' }}>
                  <a
                    href={url}
                    target="_blank"
                    rel="noopener noreferrer"
                    style={{
                      color: '#1890ff',
                      textDecoration: 'none',
                      fontSize: '11px',
                      display: 'flex',
                      alignItems: 'center',
                      gap: 4,
                      wordBreak: 'break-all',
                      maxWidth: '100%',
                      overflow: 'hidden',
                      textOverflow: 'ellipsis'
                    }}
                  >
                    <LinkOutlined style={{ flexShrink: 0 }} />
                    <span style={{ overflow: 'hidden', textOverflow: 'ellipsis' }}>
                      {url}
                    </span>
                  </a>

                  {isImage && (
                    <div style={{ marginTop: 4, maxWidth: '100%' }}>
                      <Image
                        src={url}
                        alt="Preview"
                        width={200}
                        height={150}
                        style={{
                          objectFit: 'cover',
                          borderRadius: 4,
                          border: '1px solid #d9d9d9',
                          maxWidth: '100%'
                        }}
                        fallback="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMIAAADDCAYAAADQvc6UAAABRWlDQ1BJQ0MgUHJvZmlsZQAAKJFjYGASSSwoyGFhYGDIzSspCnJ3UoiIjFJgf8LAwSDCIMogwMCcmFxc4BgQ4ANUwgCjUcG3awyMIPqyLsis7PPOq3QdDFcvjV3jOD1boQVTPQrgSkktTgbSf4A4LbmgqISBgTEFyFYuLykAsTuAbJEioKOA7DkgdjqEvQHEToKwj4DVhAQ5A9k3gGyB5IxEoBmML4BsnSQk8XQkNtReEOBxcfXxUQg1Mjc0dyHgXNJBSWpFCYh2zi+oLMpMzyhRcASGUqqCZ16yno6CkYGRAQMDKMwhqj/fAIcloxgHQqxAjIHBEugw5sUIsSQpBobtQPdLciLEVJYzMPBHMDBsayhILEqEO4DxG0txmrERhM29nYGBddr//5/DGRjYNRkY/l7////39v///y4Dmn+LgeHANwDrkl1AuO+pmgAAADhlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAAqACAAQAAAABAAAAwqADAAQAAAABAAAAwwAAAAD9b/HnAAAHlklEQVR4Ae3dP3Ik1RnG4W+FgYxN"
                      />
                    </div>
                  )}

                  {!isImage && (
                    <div style={{
                      marginTop: 4,
                      padding: 8,
                      background: '#f5f5f5',
                      borderRadius: 4,
                      fontSize: '11px',
                      color: '#666',
                      maxWidth: '100%',
                      wordBreak: 'break-word'
                    }}>
                      <LinkOutlined style={{ marginRight: 4 }} />
                      Link externo - Clique para abrir
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  )
}

export default function AreaTree() {
  const queryClient = useQueryClient()
  const navigate = useNavigate()
  const { data: areas = [], isLoading: loading, error: areaError } = useAreaTree()
  const typedAreas: AreaNode[] = areas
  const [processes, setProcesses] = useState<ProcessNode[]>([])
  const [open, setOpen] = useState(false)
  const [editMode, setEditMode] = useState(false)
  const [editingItem, setEditingItem] = useState<ProcessNode | null>(null)
  const [parentId, setParentId] = useState<number | null>(null)
  const [submitting, setSubmitting] = useState(false)
  const [isMobile, setIsMobile] = useState(false)
  const [form] = Form.useForm()
  
  // Estados para filtros e pesquisa
  const [searchTerm, setSearchTerm] = useState('')
  const [activeFilters, setActiveFilters] = useState<{
    criticality: string[]
    status: string[]
  }>({
    criticality: [],
    status: []
  })

  const fetchProcesses = async () => {
    try {
      const response = await api.get<ApiResponse<ProcessNode[]>>('/processes')
      setProcesses(response.data.data)
    } catch (err) {
      console.error('Erro ao carregar processos:', err)
    }
  }

  // Função para verificar se um processo ou seus filhos contêm o termo de pesquisa
  const processContainsSearchTerm = (process: ProcessNode, term: string): boolean => {
    if (!term) return true
    
    const searchLower = term.toLowerCase()
    const processMatches = 
      process.name.toLowerCase().includes(searchLower) ||
      process.description.toLowerCase().includes(searchLower) ||
      (process.tools && process.tools.toLowerCase().includes(searchLower)) ||
      (process.responsible && process.responsible.toLowerCase().includes(searchLower)) ||
      (process.documentation && process.documentation.toLowerCase().includes(searchLower))
    
    if (processMatches) return true
    
    // Verificar filhos recursivamente
    if (process.children) {
      return process.children.some(child => processContainsSearchTerm(child, term))
    }
    
    return false
  }

  // Função para verificar se um processo passa pelos filtros ativos
  const processPassesFilters = (process: ProcessNode): boolean => {
    // Filtro por criticidade
    if (activeFilters.criticality.length > 0 && !activeFilters.criticality.includes(process.criticality)) {
      return false
    }
    
    // Filtro por status
    if (activeFilters.status.length > 0 && !activeFilters.status.includes(process.status)) {
      return false
    }
    
    return true
  }

  // Função para filtrar processos recursivamente
  const filterProcessesRecursively = (processList: ProcessNode[]): ProcessNode[] => {
    // Se não há filtros ativos, retorna todos os processos
    if (!hasActiveFilters) {
      return processList
    }
    
    return processList
      .filter(process => {
        const passesFilters = processPassesFilters(process)
        const containsSearch = processContainsSearchTerm(process, searchTerm)
        
        // Se o processo atual não passa pelos filtros, verificar se algum filho passa
        if (!passesFilters && process.children && process.children.length > 0) {
          const filteredChildren = filterProcessesRecursively(process.children)
          return filteredChildren.length > 0
        }
        
        return passesFilters && containsSearch
      })
      .map(process => ({
        ...process,
        children: process.children && process.children.length > 0 ? filterProcessesRecursively(process.children) : undefined
      }))
  }

  // Função para alternar filtros
  const toggleFilter = (type: 'criticality' | 'status', value: string) => {
    setActiveFilters(prev => ({
      ...prev,
      [type]: prev[type].includes(value)
        ? prev[type].filter(v => v !== value)
        : [...prev[type], value]
    }))
  }

  // Função para resetar todos os filtros
  const resetFilters = () => {
    setSearchTerm('')
    setActiveFilters({
      criticality: [],
      status: []
    })
  }

  // Verificar se há filtros ativos
  const hasActiveFilters = searchTerm || activeFilters.criticality.length > 0 || activeFilters.status.length > 0

  useEffect(() => {
    fetchProcesses()
  }, [])

  // Detecção responsiva de mobile
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth <= 768)
    }
    
    checkMobile()
    window.addEventListener('resize', checkMobile)
    
    return () => window.removeEventListener('resize', checkMobile)
  }, [])

  const onAdd = (id: number | null) => {
    setParentId(id)
    setEditMode(false)
    setEditingItem(null)
    form.resetFields()
    setOpen(true)
  }

  const onEdit = (process: ProcessNode) => {
    setEditingItem(process)
    setEditMode(true)
    form.setFieldsValue({
      name: process.name,
      description: process.description,
      type: process.type,
      criticality: process.criticality,
      status: process.status,
      tools: process.tools,
      responsible: process.responsible,
      documentation: process.documentation
    })
    setOpen(true)
  }

  const onDelete = async (id: number, type: 'area' | 'process') => {
    try {
      if (type === 'area') {
        await api.delete(`/areas/${id}`)
        queryClient.invalidateQueries({ queryKey: ['areaTree'] })
        message.success('Área removida com sucesso!')
      } else {
        await api.delete(`/processes/${id}`)
        setProcesses(prevProcesses => prevProcesses.filter(process => process.id !== id))
        message.success('Processo removido com sucesso!')
      }
    } catch (err) {
      message.error('Erro ao remover: ' + (err instanceof Error ? err.message : 'Erro desconhecido'))
    }
  }

  interface AreaFormValues {
    name: string
  }

  interface ProcessFormValues {
    name: string
    description: string
    type: string
    criticality: string
    status: string
    tools?: string
    responsible?: string
    documentation?: string
  }

  const onFinish = async (values: AreaFormValues | ProcessFormValues) => {
    try {
      setSubmitting(true)

      if (editMode && editingItem) {
        // Editando um processo existente
        const processValues = values as ProcessFormValues
        const response = await api.put<ApiMessageResponse>(`/processes/${editingItem.id}`, processValues)
        setProcesses(prevProcesses =>
          prevProcesses.map(process =>
            process.id === editingItem.id ? response.data.data : process
          )
        )
        message.success('Processo atualizado com sucesso!')
      } else if (parentId === null) {
        // Criando uma nova área
        const areaValues = values as AreaFormValues
        await api.post<ApiMessageResponse>('/areas', areaValues)
        queryClient.invalidateQueries({ queryKey: ['areaTree'] })
        message.success('Área criada com sucesso!')
      } else {
        // Verificar se parentId é uma área ou um processo
        const isArea = typedAreas.some(area => area.id === parentId)
        const processValues = values as ProcessFormValues

        if (isArea) {
          // Criando um novo processo na área
          const processData = {
            name: processValues.name,
            description: processValues.description,
            area_id: parentId,
            parent_id: null,
            type: processValues.type,
            criticality: processValues.criticality,
            status: processValues.status,
            tools: processValues.tools,
            responsible: processValues.responsible,
            documentation: processValues.documentation
          }
          const response = await api.post<ApiMessageResponse>('/processes', processData)
          setProcesses(prevProcesses => [...prevProcesses, response.data.data])
          queryClient.invalidateQueries({ queryKey: ['areaTree'] })
          message.success('Processo criado com sucesso!')
        } else {
          // Criando um subprocesso
          const processData = {
            name: processValues.name,
            description: processValues.description,
            area_id: processes.find(p => p.id === parentId)?.area_id || 1,
            parent_id: parentId,
            type: processValues.type,
            criticality: processValues.criticality,
            status: processValues.status,
            tools: processValues.tools,
            responsible: processValues.responsible,
            documentation: processValues.documentation
          }
          const response = await api.post<ApiMessageResponse>('/processes', processData)
          setProcesses(prevProcesses => [...prevProcesses, response.data.data])
          // Expandir o processo pai para mostrar o novo subprocesso
          const parentProcessId = response.data.data.parent_id?.toString()
          if (parentProcessId) {
            // Forçar re-render da árvore expandindo o processo pai
            setTimeout(() => {
              const treeElement = document.querySelector('.process-tree')
              if (treeElement) {
                const parentNode = treeElement.querySelector(`[data-key="${parentProcessId}"]`)
                if (parentNode) {
                  const expandButton = parentNode.querySelector('.ant-tree-switcher')
                  if (expandButton && !expandButton.classList.contains('ant-tree-switcher_open')) {
                    (expandButton as HTMLElement).click()
                  }
                }
              }
            }, 100)
          }
          queryClient.invalidateQueries({ queryKey: ['areaTree'] })
          message.success('Subprocesso criado com sucesso!')
        }
      }

      setOpen(false)

    } catch (err) {
      message.error('Erro ao salvar: ' + (err instanceof Error ? err.message : 'Erro desconhecido'))
    } finally {
      setSubmitting(false)
    }
  }

  const handleModalOk = () => {
    form.submit()
  }

  // Função para converter processos em estrutura de árvore
  const buildProcessTree = (processList: ProcessNode[]): ProcessNode[] => {
    const processMap = new Map<number, ProcessNode>()
    const rootProcesses: ProcessNode[] = []

    // Primeiro, mapeia todos os processos
    processList.forEach(process => {
      processMap.set(process.id, { ...process, children: [] })
    })

    // Depois, organiza a hierarquia
    processList.forEach(process => {
      const processWithChildren = processMap.get(process.id)!
      if (process.parent_id === null) {
        rootProcesses.push(processWithChildren)
      } else {
        const parent = processMap.get(process.parent_id)
        if (parent) {
          parent.children!.push(processWithChildren)
        }
      }
    })

    return rootProcesses
  }

  // Função para converter processos em formato de árvore para o componente Tree
  const convertToTreeData = (processes: ProcessNode[]): Array<{
    key: number
    title: React.ReactNode
    children?: Array<{
      key: number
      title: React.ReactNode
    }>
  }> => {
    return processes.map(process => ({
      key: process.id,
      title: (
        <span style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          width: '100%',
          padding: '4px 0'
        }}>
          <span style={{ display: 'flex', alignItems: 'center', flex: 1 }}>
            <FileOutlined style={{
              marginRight: 8,
              color: process.parent_id ? '#52c41a' : '#1890ff',
              fontSize: '14px'
            }} />
            <span style={{
              fontSize: '14px',
              fontWeight: process.parent_id ? 'normal' : '500'
            }}>
              {process.name}
            </span>
            <span style={{
              marginLeft: 8,
              fontSize: '11px',
              color: '#fff',
              background: getCriticalityColor(process.criticality),
              padding: '1px 6px',
              borderRadius: 8,
              textTransform: 'uppercase',
              fontWeight: 'bold'
            }}>
              {process.criticality === 'high' ? 'Alta' : process.criticality === 'medium' ? 'Média' : 'Baixa'}
            </span>
            <Tag color={getStatusColor(process.status)} style={{ marginLeft: 8, fontSize: '10px' }}>
              {process.status === 'active' ? 'Ativo' : 'Inativo'}
            </Tag>
          </span>
          <Space size="small">
            {process.description && (
              <Tooltip
                title={
                  <div style={{ maxWidth: 280, wordBreak: 'break-word' }}>
                    <strong>Descrição:</strong>
                    <PreviewRenderer text={process.description} type="description" />
                  </div>
                }
                placement="topLeft"
                overlayStyle={{ maxWidth: 300 }}
              >
                <Tag icon={<FileTextOutlined />} color="orange" style={{ fontSize: '10px' }}>
                  Desc
                </Tag>
              </Tooltip>
            )}
            {process.tools && (
              <Tooltip title="Ferramentas">
                <Tag icon={<ToolOutlined />} color="blue" style={{ fontSize: '10px' }}>
                  {process.tools.length > 20 ? process.tools.substring(0, 20) + '...' : process.tools}
                </Tag>
              </Tooltip>
            )}
            {process.responsible && (
              <Tooltip title="Responsável">
                <Tag icon={<UserOutlined />} color="green" style={{ fontSize: '10px' }}>
                  {process.responsible}
                </Tag>
              </Tooltip>
            )}
            {process.documentation && (
              <Tooltip
                title={
                  <div style={{ maxWidth: 280, wordBreak: 'break-word' }}>
                    <strong>Documentação:</strong>
                    <PreviewRenderer text={process.documentation} type="documentation" />
                  </div>
                }
                placement="topLeft"
                overlayStyle={{ maxWidth: 300 }}
              >
                <Tag icon={<FileTextOutlined />} color="purple" style={{ fontSize: '10px' }}>
                  Doc
                </Tag>
              </Tooltip>
            )}
            <Button
              type="link"
              size="small"
              icon={<PlusOutlined />}
              onClick={(e) => {
                e.stopPropagation()
                onAdd(process.id)
              }}
              style={{
                padding: '2px 6px',
                borderRadius: 4,
                border: '1px solid #d9d9d9',
                whiteSpace: 'nowrap',
                fontSize: '11px',
                height: '24px',
                marginLeft: 8
              }}
            >
              Adicionar Subprocesso
            </Button>
            <Button
              type="primary"
              size={isMobile ? 'middle' : 'small'}
              icon={<NodeIndexOutlined />}
              onClick={(e) => {
                e.stopPropagation()
                navigate(`/flow/process/${process.id}`)
              }}
              style={{
                padding: isMobile ? '6px 12px' : '2px 6px',
                borderRadius: 6,
                whiteSpace: 'nowrap',
                fontSize: isMobile ? '13px' : '11px',
                height: isMobile ? '36px' : '24px',
                minWidth: isMobile ? '80px' : 'auto',
                backgroundColor: isMobile ? '#1890ff' : 'transparent',
                color: isMobile ? 'white' : '#1890ff',
                border: isMobile ? '1px solid #1890ff' : '1px solid #d9d9d9',
                display: 'block',
                marginBottom: isMobile ? '8px' : '0'
              }}
            >
              {isMobile ? 'Ver Fluxo' : 'Fluxo'}
            </Button>
            <Button
              type="link"
              size="small"
              icon={<EditOutlined />}
              onClick={(e) => {
                e.stopPropagation()
                onEdit(process)
              }}
              style={{
                padding: '2px 6px',
                borderRadius: 4,
                border: '1px solid #d9d9d9',
                whiteSpace: 'nowrap',
                fontSize: '11px',
                height: '24px'
              }}
            />
            <Popconfirm
              title="Remover Processo"
              description="Tem certeza que deseja remover este processo?"
              onConfirm={(e) => {
                e?.stopPropagation()
                onDelete(process.id, 'process')
              }}
              okText="Sim"
              cancelText="Não"
            >
              <Button
                type="link"
                size="small"
                icon={<DeleteOutlined />}
                style={{
                  padding: '2px 6px',
                  borderRadius: 4,
                  border: '1px solid #d9d9d9',
                  whiteSpace: 'nowrap',
                  fontSize: '11px',
                  height: '24px'
                }}
              />
            </Popconfirm>
          </Space>
        </span>
      ),
      children: process.children ? convertToTreeData(process.children) : undefined
    }))
  }

  // Agrupa processos por área com filtros aplicados
  const processesByArea = typedAreas.map(area => {
    const areaProcesses = processes.filter(p => p.area_id === area.id)
    const treeData = buildProcessTree(areaProcesses)
    const filteredTreeData = filterProcessesRecursively(treeData)
    
    // Verificar se a área contém o termo de pesquisa
    const areaContainsSearch = searchTerm ? area.name.toLowerCase().includes(searchTerm.toLowerCase()) : false
    
    return {
      area,
      processes: filteredTreeData,
      showArea: hasActiveFilters ? (filteredTreeData.length > 0 || areaContainsSearch) : true
    }
  })

  // Função para obter a cor baseada na criticidade
  const getCriticalityColor = (criticality: string) => {
    switch (criticality) {
      case 'high': return '#ff4d4f'
      case 'medium': return '#faad14'
      case 'low': return '#52c41a'
      default: return '#666'
    }
  }

  // Função para obter a cor baseada no status
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return '#52c41a'
      case 'inactive': return '#ff4d4f'
      default: return '#666'
    }
  }

  return (
    <>
      <style>
        {`
          .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
          }
          .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
          }
          .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
          }
          .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
          }
          .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
          }
          
          .custom-collapse .ant-collapse-header {
            display: flex !important;
            align-items: center !important;
          }
          
          .custom-collapse .ant-collapse-expand-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 100% !important;
          }
        `}
      </style>
      <div style={{
        minHeight: 'calc(100vh - 60px)',
        width: '100%',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        padding: '20px',
        boxSizing: 'border-box'
      }}>
        <div style={{
          width: '100%',
          maxWidth: 1000,
          maxHeight: 'calc(100vh - 140px)',
          overflowY: 'auto',
          background: '#fff',
          borderRadius: 16,
          boxShadow: '0 10px 30px rgba(0,0,0,0.2)',
                      padding: isMobile ? '24px' : '32px',
          margin: '0 auto',
          boxSizing: 'border-box'
        }}
          className="custom-scrollbar">
          <h1 style={{
            marginBottom: 24,
            color: '#1890ff',
            textAlign: 'center',
            fontSize: isMobile ? '1.8rem' : '2rem',
            fontWeight: 'bold'
          }}>
            ChainView - Áreas e Processos
          </h1>

          <div style={{ textAlign: 'center', marginBottom: 24 }}>
            <Button
              type="primary"
              icon={<PlusOutlined />}
              onClick={() => onAdd(null)}
              size="large"
              style={{
                borderRadius: 8,
                            height: isMobile ? '48px' : '48px',
            padding: isMobile ? '0 20px' : '0 24px',
            fontSize: isMobile ? '16px' : '16px'
              }}
            >
              Adicionar Área
            </Button>
          </div>

          <div style={{ marginBottom: 8 }}>
            <p style={{ textAlign: 'center', margin: 0, color: '#1890ff', fontSize: '14px' }}>
              Status: {loading ? 'Carregando...' : areaError ? 'Erro' : 'Pronto'}
            </p>
            {areaError && <p style={{ color: 'red', textAlign: 'center', margin: '8px 0 0 0' }}>Erro: {areaError.message}</p>}
          </div>

          {loading && (
            <div style={{ textAlign: 'center', padding: '40px', color: '#666' }}>
              Carregando áreas...
            </div>
          )}

          {areaError && (
            <div style={{ textAlign: 'center', padding: '40px', color: '#ff4d4f' }}>
              Erro ao carregar áreas: {areaError.message}
            </div>
          )}

          {!loading && !areaError && (
            <>
              <p style={{
                marginBottom: 24,
                fontSize: isMobile ? '12px' : '14px',
                color: '#666',
                textAlign: 'center',
                padding: '12px',
                background: '#f5f5f5',
                borderRadius: 8
              }}>
                Total de áreas: {typedAreas.length} | Total de processos: {processes.length}
              </p>
              
              {/* Campo de pesquisa */}
              <div style={{
                marginBottom: 16,
                display: 'flex',
                justifyContent: 'center',
                gap: '12px',
                flexWrap: 'wrap',
                alignItems: 'center'
              }}>
                <Input
                  placeholder="Pesquisar por nome, descrição, ferramentas, responsável..."
                  prefix={<SearchOutlined />}
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  style={{
                    width: isMobile ? '100%' : '400px',
                    borderRadius: '8px'
                  }}
                  allowClear
                />
                {hasActiveFilters && (
                  <Button
                    icon={<ReloadOutlined />}
                    onClick={resetFilters}
                    style={{
                      borderRadius: '8px',
                      display: 'flex',
                      alignItems: 'center',
                      gap: '4px'
                    }}
                  >
                    Limpar Filtros
                  </Button>
                )}
              </div>

              {/* Tags de filtros */}
              <div style={{
                marginBottom: 24,
                display: 'flex',
                justifyContent: 'center',
                gap: '16px',
                flexWrap: 'wrap'
              }}>
                <Tag 
                  color={activeFilters.criticality.includes('high') ? 'red' : undefined}
                  style={{ 
                    fontSize: '12px', 
                    padding: '4px 8px',
                    cursor: 'pointer',
                    border: activeFilters.criticality.includes('high') ? '2px solid #ff4d4f' : undefined
                  }}
                  onClick={() => toggleFilter('criticality', 'high')}
                >
                  Alta: {processes.filter(p => p.criticality === 'high').length}
                </Tag>
                <Tag 
                  color={activeFilters.criticality.includes('medium') ? 'orange' : undefined}
                  style={{ 
                    fontSize: '12px', 
                    padding: '4px 8px',
                    cursor: 'pointer',
                    border: activeFilters.criticality.includes('medium') ? '2px solid #faad14' : undefined
                  }}
                  onClick={() => toggleFilter('criticality', 'medium')}
                >
                  Média: {processes.filter(p => p.criticality === 'medium').length}
                </Tag>
                <Tag 
                  color={activeFilters.criticality.includes('low') ? 'green' : undefined}
                  style={{ 
                    fontSize: '12px', 
                    padding: '4px 8px',
                    cursor: 'pointer',
                    border: activeFilters.criticality.includes('low') ? '2px solid #52c41a' : undefined
                  }}
                  onClick={() => toggleFilter('criticality', 'low')}
                >
                  Baixa: {processes.filter(p => p.criticality === 'low').length}
                </Tag>
                <Tag 
                  color={activeFilters.status.includes('active') ? 'blue' : undefined}
                  style={{ 
                    fontSize: '12px', 
                    padding: '4px 8px',
                    cursor: 'pointer',
                    border: activeFilters.status.includes('active') ? '2px solid #1890ff' : undefined
                  }}
                  onClick={() => toggleFilter('status', 'active')}
                >
                  Ativos: {processes.filter(p => p.status === 'active').length}
                </Tag>
                <Tag 
                  color={activeFilters.status.includes('inactive') ? 'red' : undefined}
                  style={{ 
                    fontSize: '12px', 
                    padding: '4px 8px',
                    cursor: 'pointer',
                    border: activeFilters.status.includes('inactive') ? '2px solid #ff4d4f' : undefined
                  }}
                  onClick={() => toggleFilter('status', 'inactive')}
                >
                  Inativos: {processes.filter(p => p.status === 'inactive').length}
                </Tag>
              </div>
              
              {/* Mensagem de filtros ativos */}
              {hasActiveFilters && (
                <div style={{
                  marginBottom: 16,
                  padding: '8px 16px',
                  background: '#e6f7ff',
                  border: '1px solid #91d5ff',
                  borderRadius: '8px',
                  textAlign: 'center',
                  fontSize: '14px',
                  color: '#1890ff'
                }}>
                  <SearchOutlined style={{ marginRight: 8 }} />
                  Filtros ativos: 
                  {searchTerm && ` Pesquisa: "${searchTerm}"`}
                  {activeFilters.criticality.length > 0 && ` Criticidade: ${activeFilters.criticality.join(', ')}`}
                  {activeFilters.status.length > 0 && ` Status: ${activeFilters.status.join(', ')}`}
                  {processesByArea.filter(({ showArea }) => showArea).length === 0 && ' - Nenhum resultado encontrado'}
                </div>
              )}
              
              {typedAreas.length > 0 ? (
                isMobile ? (
                  // Layout Mobile - Cards empilhados
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                    {processesByArea.filter(({ showArea }) => showArea).map(({ area, processes }) => (
                      <Card
                        key={area.id}
                        style={{
                          borderRadius: 12,
                          border: '1px solid #e8e8e8',
                          boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
                        }}
                        title={
                          <div style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            width: '100%',
                            padding: '16px 0'
                          }}>
                            <div style={{
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              gap: '8px',
                              flexWrap: 'wrap'
                            }}>
                              <FolderOutlined style={{ color: '#1890ff', fontSize: '24px' }} />
                              <span style={{ fontSize: '18px', fontWeight: '500' }}>
                                {area.name}
                              </span>
                              <Tag color="blue" style={{ fontSize: '13px' }}>
                                {processes.length} processos
                              </Tag>
                            </div>
                            <Button
                              type="primary"
                              size="small"
                              icon={<PlusOutlined />}
                              onClick={() => onAdd(area.id)}
                              style={{ 
                                borderRadius: 6,
                                fontSize: '14px',
                                height: '36px',
                                padding: '0 16px'
                              }}
                            >
                              Adicionar
                            </Button>
                          </div>
                        }
                      >
                        {processes.length > 0 ? (
                          <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            {processes.map((process) => (
                              <div
                                key={process.id}
                                style={{
                                  border: '1px solid #f0f0f0',
                                  borderRadius: 8,
                                  padding: '16px',
                                  background: '#fafafa'
                                }}
                              >
                                <div style={{
                                  display: 'flex',
                                  flexDirection: 'column',
                                  gap: '12px',
                                  marginBottom: '8px'
                                }}>
                                  {/* Primeira linha: Nome, ícone e prioridade */}
                                  <div style={{
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '8px',
                                    flexWrap: 'wrap',
                                    width: '100%'
                                  }}>
                                    <FileOutlined style={{ color: '#52c41a', fontSize: '20px' }} />
                                    <span style={{ fontSize: '16px', fontWeight: '500' }}>
                                      {process.name}
                                    </span>
                                    <Tag 
                                      color={process.criticality === 'high' ? 'red' : process.criticality === 'medium' ? 'orange' : 'green'}
                                      style={{ fontSize: '12px' }}
                                    >
                                      {process.criticality === 'high' ? 'Alta' : process.criticality === 'medium' ? 'Média' : 'Baixa'}
                                    </Tag>
                                  </div>
                                  
                                  {/* Segunda linha: Botões de ação */}
                                  <div style={{
                                    display: 'flex',
                                    justifyContent: 'flex-start',
                                    width: '100%'
                                  }}>
                                    <Space size="small">
                                      <Button
                                        type="primary"
                                        size="small"
                                        icon={<NodeIndexOutlined />}
                                        onClick={() => navigate(`/flow/process/${process.id}`)}
                                        style={{ 
                                          padding: '6px 12px',
                                          fontSize: '14px',
                                          backgroundColor: '#1890ff',
                                          color: 'white',
                                          border: '1px solid #1890ff',
                                          borderRadius: '6px',
                                          minWidth: '80px'
                                        }}
                                      >
                                        Ver Fluxo
                                      </Button>
                                      <Button
                                        type="link"
                                        size="small"
                                        icon={<EditOutlined />}
                                        onClick={() => onEdit(process)}
                                        style={{ 
                                          padding: '4px 8px',
                                          fontSize: '18px'
                                        }}
                                      />
                                      <Button
                                        type="link"
                                        size="small"
                                        danger
                                        icon={<DeleteOutlined />}
                                        onClick={() => onDelete(process.id, 'process')}
                                        style={{ 
                                          padding: '4px 8px',
                                          fontSize: '18px'
                                        }}
                                      />
                                    </Space>
                                  </div>
                                </div>
                                
                                {process.description && (
                                  <div style={{
                                    fontSize: '13px',
                                    color: '#666',
                                    marginBottom: '8px',
                                    lineHeight: '1.4'
                                  }}>
                                    {process.description}
                                  </div>
                                )}
                                
                                {process.children && process.children.length > 0 && (
                                  <div style={{ marginTop: '8px' }}>
                                    <div style={{
                                      fontSize: '12px',
                                      color: '#999',
                                      marginBottom: '4px',
                                      fontWeight: '500'
                                    }}>
                                      Subprocessos ({process.children.length}):
                                    </div>
                                    <div style={{ 
                                      display: 'flex', 
                                      flexDirection: 'column', 
                                      gap: '8px', 
                                      width: '100%',
                                      alignItems: 'center'
                                    }}>
                                      {process.children.map((child) => (
                                        <div
                                          key={child.id}
                                          style={{
                                            border: '1px solid #e8e8e8',
                                            borderRadius: 6,
                                            padding: '8px',
                                            background: '#fff',
                                            fontSize: '13px'
                                          }}
                                        >
                                          <div style={{
                                            display: 'flex',
                                            flexDirection: 'column',
                                            gap: '8px'
                                          }}>
                                            {/* Primeira linha: Nome, ícone e prioridade */}
                                            <div style={{
                                              display: 'flex',
                                              alignItems: 'center',
                                              gap: '6px',
                                              width: '100%'
                                            }}>
                                              <FileOutlined style={{ color: '#1890ff', fontSize: '15px' }} />
                                              <span>{child.name}</span>
                                              <Tag 
                                                color={child.criticality === 'high' ? 'red' : child.criticality === 'medium' ? 'orange' : 'green'}
                                                style={{ fontSize: '10px' }}
                                              >
                                                {child.criticality === 'high' ? 'Alta' : child.criticality === 'medium' ? 'Média' : 'Baixa'}
                                              </Tag>
                                            </div>
                                            
                                            {/* Segunda linha: Botões de ação */}
                                            <div style={{
                                              display: 'flex',
                                              justifyContent: 'flex-start',
                                              width: '100%'
                                            }}>
                                              <Space size="small">
                                                <Button
                                                  type="primary"
                                                  size="small"
                                                  icon={<NodeIndexOutlined />}
                                                  onClick={() => navigate(`/flow/process/${child.id}`)}
                                                  style={{ 
                                                    padding: '4px 8px',
                                                    fontSize: '12px',
                                                    backgroundColor: '#1890ff',
                                                    color: 'white',
                                                    border: '1px solid #1890ff',
                                                    borderRadius: '4px',
                                                    minWidth: '60px'
                                                  }}
                                                >
                                                  Fluxo
                                                </Button>
                                                <Button
                                                  type="link"
                                                  size="small"
                                                  icon={<EditOutlined />}
                                                  onClick={() => onEdit(child)}
                                                  style={{ padding: '1px 2px', fontSize: '14px' }}
                                                />
                                                <Button
                                                  type="link"
                                                  size="small"
                                                  danger
                                                  icon={<DeleteOutlined />}
                                                  onClick={() => onDelete(child.id, 'process')}
                                                  style={{ padding: '1px 2px', fontSize: '14px' }}
                                                />
                                              </Space>
                                            </div>
                                          </div>
                                          {child.description && (
                                            <div style={{
                                              fontSize: '12px',
                                              color: '#666',
                                              marginTop: '4px',
                                              lineHeight: '1.3'
                                            }}>
                                              {child.description}
                                            </div>
                                          )}
                                        </div>
                                      ))}
                                    </div>
                                  </div>
                                )}
                              </div>
                            ))}
                          </div>
                        ) : (
                          <div style={{
                            textAlign: 'center',
                            padding: '20px',
                            color: '#999',
                            fontSize: '14px'
                          }}>
                            Nenhum processo encontrado nesta área
                          </div>
                        )}
                      </Card>
                    ))}
                  </div>
                ) : (
                  // Layout Desktop - Collapse original
                <Collapse
                  style={{
                    background: '#fff',
                    borderRadius: 12,
                    border: '1px solid #e8e8e8'
                  }}
                  expandIconPosition="end"
                  expandIcon={({ isActive }) => (
                    <div style={{ 
                      display: 'flex', 
                      alignItems: 'center', 
                      justifyContent: 'center',
                      height: '100%',
                      width: '100%'
                    }}>
                      {isActive ? <UpOutlined /> : <DownOutlined />}
                    </div>
                  )}
                  className="custom-collapse"
                >
                    {processesByArea.filter(({ showArea }) => showArea).map(({ area, processes }) => (
                    <Collapse.Panel
                      key={area.id}
                      header={
                        <span style={{
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          width: '100%',
                          padding: '8px 0',
                          flexWrap: 'wrap',
                          gap: '8px'
                        }}>
                          <span style={{
                            display: 'flex',
                            alignItems: 'center',
                            flexWrap: 'wrap',
                            gap: '8px'
                          }}>
                            <FolderOutlined style={{
                              color: '#1890ff',
                                fontSize: '18px'
                            }} />
                            <span style={{
                                fontSize: '16px',
                              fontWeight: '500'
                            }}>
                              {area.name}
                            </span>
                            <span style={{
                              fontSize: '12px',
                              color: '#666',
                              background: '#f0f0f0',
                              padding: '2px 8px',
                              borderRadius: 12
                            }}>
                              {processes.length} processos
                            </span>
                          </span>
                          <Space size="small">
                            <Button
                              type="link"
                              size="small"
                              icon={<PlusOutlined />}
                              onClick={(e) => {
                                e.stopPropagation()
                                onAdd(area.id)
                              }}
                              style={{
                                padding: '4px 8px',
                                borderRadius: 6,
                                border: '1px solid #d9d9d9',
                                whiteSpace: 'nowrap'
                              }}
                            >
                              Adicionar Processo
                            </Button>
                            <Button
                              type="primary"
                              size={isMobile ? 'middle' : 'small'}
                              icon={<NodeIndexOutlined />}
                              onClick={(e) => {
                                e.stopPropagation()
                                navigate(`/flow/area/${area.id}`)
                              }}
                              style={{
                                padding: isMobile ? '8px 16px' : '4px 8px',
                                borderRadius: 6,
                                whiteSpace: 'nowrap',
                                fontSize: isMobile ? '14px' : '12px',
                                height: isMobile ? '40px' : '28px',
                                minWidth: isMobile ? '100px' : 'auto',
                                backgroundColor: isMobile ? '#1890ff' : 'transparent',
                                color: isMobile ? 'white' : '#1890ff',
                                border: isMobile ? '1px solid #1890ff' : '1px solid #d9d9d9',
                                display: 'block',
                                marginBottom: isMobile ? '8px' : '0'
                              }}
                            >
                              {isMobile ? 'Ver Fluxo' : 'Fluxo'}
                            </Button>
                            <Popconfirm
                              title="Remover Área"
                              description="Tem certeza que deseja remover esta área? Todos os processos serão removidos também."
                              onConfirm={(e) => {
                                e?.stopPropagation()
                                onDelete(area.id, 'area')
                              }}
                              okText="Sim"
                              cancelText="Não"
                            >
                              <Button
                                type="link"
                                size="small"
                                icon={<DeleteOutlined />}
                                style={{
                                  padding: '4px 8px',
                                  borderRadius: 6,
                                  border: '1px solid #d9d9d9',
                                  whiteSpace: 'nowrap'
                                }}
                              />
                            </Popconfirm>
                          </Space>
                        </span>
                      }
                    >
                      {processes.length > 0 ? (
                        <div style={{ padding: '16px 0' }}>
                          <div style={{
                            '--tree-indent': '24px',
                            '--tree-line-color': '#e8e8e8'
                          } as React.CSSProperties}>
                            <Tree
                              treeData={convertToTreeData(processes)}
                              showLine
                              defaultExpandAll
                              style={{
                                background: 'transparent',
                                fontSize: '14px'
                              }}
                              className="process-tree"
                            />
                          </div>
                        </div>
                      ) : (
                        <div style={{
                          padding: '24px',
                          textAlign: 'center',
                          color: '#666',
                          background: '#fafafa',
                          borderRadius: 8,
                          border: '1px dashed #d9d9d9'
                        }}>
                          Nenhum processo nesta área
                        </div>
                      )}
                    </Collapse.Panel>
                  ))}
                </Collapse>
                )
              ) : (
                <div style={{
                  textAlign: 'center',
                  padding: '60px 20px',
                  color: '#666',
                  background: '#fafafa',
                  borderRadius: 12,
                  border: '1px dashed #d9d9d9'
                }}>
                  <FolderOutlined style={{
                    fontSize: isMobile ? '42px' : '48px',
                    color: '#d9d9d9',
                    marginBottom: '16px'
                  }} />
                  <p style={{
                    fontSize: isMobile ? '16px' : '16px',
                    margin: 0
                  }}>
                    Nenhuma área encontrada
                  </p>
                  <p style={{
                    fontSize: isMobile ? '14px' : '14px',
                    margin: '8px 0 0 0',
                    color: '#999'
                  }}>
                    Clique em "Add Root Area" para começar
                  </p>
                </div>
              )}
            </>
          )}
        </div>
      </div>

      <Modal
        open={open}
        onCancel={() => setOpen(false)}
        onOk={handleModalOk}
        title={
          editMode
            ? "Editar Processo"
            : parentId === null
              ? "Nova Área"
              : "Novo Processo"
        }
        confirmLoading={submitting}
        okText={editMode ? "Atualizar" : "Criar"}
        cancelText="Cancelar"
        width={600}
      >
        <Form form={form} layout="vertical" onFinish={onFinish}>
          <Form.Item
            name="name"
            label={parentId === null && !editMode ? "Nome da Área" : "Nome do Processo"}
            rules={[{ required: true, message: parentId === null && !editMode ? 'Por favor, insira o nome da área' : 'Por favor, insira o nome do processo' }]}
          >
            <Input placeholder={parentId === null && !editMode ? "Digite o nome da área" : "Digite o nome do processo"} />
          </Form.Item>
          {(parentId !== null || editMode) && (
            <>
              <Form.Item
                name="description"
                label="Descrição"
                rules={[{ required: true, message: 'Por favor, insira a descrição do processo' }]}
              >
                <Input.TextArea
                  placeholder="Descrição detalhada do processo (suporta links e imagens)"
                  rows={3}
                />
              </Form.Item>
              <Form.Item
                name="type"
                label="Tipo"
                rules={[{ required: true, message: 'Por favor, selecione o tipo do processo' }]}
              >
                <Select placeholder="Selecione o tipo">
                  <Select.Option value="internal">Interno</Select.Option>
                  <Select.Option value="external">Externo</Select.Option>
                </Select>
              </Form.Item>
              <Form.Item
                name="criticality"
                label="Criticidade"
                rules={[{ required: true, message: 'Por favor, selecione a criticidade do processo' }]}
              >
                <Select placeholder="Selecione a criticidade">
                  <Select.Option value="low">Baixa</Select.Option>
                  <Select.Option value="medium">Média</Select.Option>
                  <Select.Option value="high">Alta</Select.Option>
                </Select>
              </Form.Item>
              <Form.Item
                name="status"
                label="Status"
                rules={[{ required: true, message: 'Por favor, selecione o status do processo' }]}
              >
                <Select placeholder="Selecione o status">
                  <Select.Option value="active">Ativo</Select.Option>
                  <Select.Option value="inactive">Inativo</Select.Option>
                </Select>
              </Form.Item>
              <Form.Item
                name="tools"
                label="Ferramentas"
              >
                <Input placeholder="Ferramentas utilizadas" />
              </Form.Item>
              <Form.Item
                name="responsible"
                label="Responsável"
              >
                <Input placeholder="Nome do responsável" />
              </Form.Item>
              <Form.Item
                name="documentation"
                label="Documentação"
              >
                <Input.TextArea
                  placeholder="Link ou descrição da documentação (suporta links e imagens)"
                  rows={2}
                />
              </Form.Item>
            </>
          )}
        </Form>
      </Modal>
    </>
  )
}
