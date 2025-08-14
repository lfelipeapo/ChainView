import { Button, Modal, Form, Input, List, Card, message, Tree, Collapse, Select, Popconfirm, Tooltip, Tag, Space } from 'antd'
import { PlusOutlined, FolderOutlined, FileOutlined, EditOutlined, DeleteOutlined, ToolOutlined, UserOutlined, FileTextOutlined, SettingOutlined } from '@ant-design/icons'
import { useState, useEffect } from 'react'
import api from '../api'

interface AreaNode {
  id: number
  name: string
  children?: AreaNode[]
  key?: number | string
}

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

export default function AreaTree() {
  const [areas, setAreas] = useState<AreaNode[]>([])
  const [processes, setProcesses] = useState<ProcessNode[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [open, setOpen] = useState(false)
  const [editMode, setEditMode] = useState(false)
  const [editingItem, setEditingItem] = useState<ProcessNode | null>(null)
  const [parentId, setParentId] = useState<number | null>(null)
  const [submitting, setSubmitting] = useState(false)
  const [form] = Form.useForm()

  const fetchAreas = async () => {
    try {
      const response = await api.get<AreaNode[]>('/areas/tree')
      setAreas(response.data)
      setLoading(false)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Erro desconhecido')
      setLoading(false)
    }
  }

  const fetchProcesses = async () => {
    try {
      const response = await api.get<ProcessNode[]>('/processes')
      setProcesses(response.data)
    } catch (err) {
      console.error('Erro ao carregar processos:', err)
    }
  }

  useEffect(() => {
    fetchAreas()
    fetchProcesses()
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
        setAreas(prevAreas => prevAreas.filter(area => area.id !== id))
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
        const response = await api.put<ProcessNode>(`/processes/${editingItem.id}`, processValues)
        setProcesses(prevProcesses => 
          prevProcesses.map(process => 
            process.id === editingItem.id ? response.data : process
          )
        )
        message.success('Processo atualizado com sucesso!')
      } else if (parentId === null) {
        // Criando uma nova área
        const areaValues = values as AreaFormValues
        const response = await api.post<AreaNode>('/areas', areaValues)
        setAreas(prevAreas => [...prevAreas, response.data])
        message.success('Área criada com sucesso!')
      } else {
        // Verificar se parentId é uma área ou um processo
        const isArea = areas.some(area => area.id === parentId)
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
          const response = await api.post<ProcessNode>('/processes', processData)
          setProcesses(prevProcesses => [...prevProcesses, response.data])
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
          const response = await api.post<ProcessNode>('/processes', processData)
          setProcesses(prevProcesses => [...prevProcesses, response.data])
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
  const convertToTreeData = (processes: ProcessNode[]): any[] => {
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
              {process.criticality}
            </span>
            {process.status === 'inactive' && (
              <Tag color="red" style={{ marginLeft: 8, fontSize: '10px' }}>
                Inativo
              </Tag>
            )}
          </span>
          <Space size="small">
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
              <Tooltip title="Documentação">
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
              Add Sub
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

  // Agrupa processos por área
  const processesByArea = areas.map(area => {
    const areaProcesses = processes.filter(p => p.area_id === area.id)
    const treeData = buildProcessTree(areaProcesses)
    return {
      area,
      processes: treeData
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
        `}
      </style>
      <div style={{ 
        minHeight: '100vh',
        width: '100vw',
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
          maxHeight: 'calc(100vh - 80px)',
          overflowY: 'auto',
          background: '#fff',
          borderRadius: 16,
          boxShadow: '0 10px 30px rgba(0,0,0,0.2)',
          padding: window.innerWidth <= 768 ? '20px' : '32px',
          margin: '20px auto',
          boxSizing: 'border-box'
        }}
        className="custom-scrollbar">
          <h1 style={{ 
            marginBottom: 24, 
            color: '#1890ff',
            textAlign: 'center',
            fontSize: window.innerWidth <= 768 ? '1.5rem' : '2rem',
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
                height: window.innerWidth <= 768 ? '40px' : '48px',
                padding: window.innerWidth <= 768 ? '0 16px' : '0 24px',
                fontSize: window.innerWidth <= 768 ? '14px' : '16px'
              }}
            >
              Add Root Area
            </Button>
          </div>
          
          <div style={{ marginBottom: 16 }}>
            <p style={{ textAlign: 'center', margin: 0 }}>
              Status: {loading ? 'Carregando...' : error ? 'Erro' : 'Pronto'}
            </p>
            {error && <p style={{ color: 'red', textAlign: 'center', margin: '8px 0 0 0' }}>Erro: {error}</p>}
          </div>
          
          {loading && (
            <div style={{ textAlign: 'center', padding: '40px', color: '#666' }}>
              Carregando áreas...
            </div>
          )}
          
          {error && (
            <div style={{ textAlign: 'center', padding: '40px', color: '#ff4d4f' }}>
              Erro ao carregar áreas: {error}
            </div>
          )}
          
          {!loading && !error && (
            <>
              <p style={{ 
                marginBottom: 24, 
                fontSize: '14px', 
                color: '#666',
                textAlign: 'center',
                padding: '12px',
                background: '#f5f5f5',
                borderRadius: 8
              }}>
                Total de áreas: {areas.length} | Total de processos: {processes.length}
              </p>
              <div style={{ 
                marginBottom: 24, 
                display: 'flex', 
                justifyContent: 'center', 
                gap: '16px',
                flexWrap: 'wrap'
              }}>
                <Tag color="red" style={{ fontSize: '12px', padding: '4px 8px' }}>
                  Alta: {processes.filter(p => p.criticality === 'high').length}
                </Tag>
                <Tag color="orange" style={{ fontSize: '12px', padding: '4px 8px' }}>
                  Média: {processes.filter(p => p.criticality === 'medium').length}
                </Tag>
                <Tag color="green" style={{ fontSize: '12px', padding: '4px 8px' }}>
                  Baixa: {processes.filter(p => p.criticality === 'low').length}
                </Tag>
                <Tag color="blue" style={{ fontSize: '12px', padding: '4px 8px' }}>
                  Ativos: {processes.filter(p => p.status === 'active').length}
                </Tag>
                <Tag color="red" style={{ fontSize: '12px', padding: '4px 8px' }}>
                  Inativos: {processes.filter(p => p.status === 'inactive').length}
                </Tag>
              </div>
              {areas.length > 0 ? (
                <Collapse 
                  style={{ 
                    background: '#fff', 
                    borderRadius: 12, 
                    border: '1px solid #e8e8e8'
                  }}
                  expandIconPosition="end"
                >
                  {processesByArea.map(({ area, processes }) => (
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
                              fontSize: window.innerWidth <= 768 ? '16px' : '18px'
                            }} />
                            <span style={{ 
                              fontSize: window.innerWidth <= 768 ? '14px' : '16px', 
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
                              Add Process
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
                          } as any}>
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
                    fontSize: window.innerWidth <= 768 ? '36px' : '48px', 
                    color: '#d9d9d9', 
                    marginBottom: '16px'
                  }} />
                  <p style={{ 
                    fontSize: window.innerWidth <= 768 ? '14px' : '16px', 
                    margin: 0
                  }}>
                    Nenhuma área encontrada
                  </p>
                  <p style={{ 
                    fontSize: window.innerWidth <= 768 ? '12px' : '14px', 
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
                  placeholder="Descrição detalhada do processo" 
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
                  placeholder="Link ou descrição da documentação" 
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
