import { useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Card, Tag, Tooltip, Space, Typography, Button, Row, Col } from 'antd';
import { FileOutlined, ToolOutlined, UserOutlined, FileTextOutlined, ReloadOutlined, ArrowRightOutlined } from '@ant-design/icons';
import api from '../api';

const { Text, Title } = Typography;

type Criticality = 'low' | 'medium' | 'high';
type Status = 'active' | 'inactive';
type ProcType = 'internal' | 'external';

export type ProcessTreeNode = {
    id: number;
    name: string;
    description?: string;
    area_id?: number;
    parent_id?: number | null;
    type: ProcType;
    criticality: Criticality;
    status: Status;
    tools?: string;
    responsible?: string;
    documentation?: string;
    children?: ProcessTreeNode[];
};

type ProcessesTreeResponse = {
    success: boolean;
    data: { area: unknown; processes: ProcessTreeNode[] };
};
type ProcessTreeResponse = {
    success: boolean;
    data: { data: ProcessTreeNode };
};

const CRIT_COLOR: Record<Criticality, string> = {
    low: '#52c41a',
    medium: '#faad14',
    high: '#ff4d4f',
};

const STATUS_COLOR: Record<Status, string> = {
    active: '#52c41a',
    inactive: '#ff4d4f',
};

// Componente para renderizar um processo individual
const ProcessCard = ({ process, level = 0 }: { process: ProcessTreeNode; level?: number }) => {
    return (
        <Card 
            size="small" 
            style={{ 
                width: '100%',
                maxWidth: 'calc(100% - 20px)',
                border: `2px solid ${CRIT_COLOR[process.criticality]}`,
                borderRadius: '8px',
                marginBottom: '12px',
                marginLeft: `${Math.min(level * 20, 60)}px`,
                boxShadow: process.status === 'active' ? '0 2px 8px rgba(24, 144, 255, 0.2)' : '0 1px 3px rgba(0,0,0,0.1)',
                overflow: 'hidden'
            }}
        >
            <div style={{ display: 'flex', alignItems: 'center', marginBottom: '8px' }}>
                <FileOutlined style={{ marginRight: '8px', color: '#1890ff' }} />
                <Text strong style={{ fontSize: '16px' }}>{process.name}</Text>
            </div>
            
            <Space direction="vertical" size="small" style={{ width: '100%' }}>
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: '0px' }}>
                    <Tag color={process.type === 'internal' ? 'blue' : 'purple'}>
                        {process.type === 'internal' ? 'Interno' : 'Externo'}
                    </Tag>
                    <Tag color={process.criticality === 'high' ? 'red' : process.criticality === 'medium' ? 'orange' : 'green'}>
                        {process.criticality === 'high' ? 'Alta' : process.criticality === 'medium' ? 'Média' : 'Baixa'} Criticidade
                    </Tag>
                    <Tag color={process.status === 'active' ? STATUS_COLOR.active : STATUS_COLOR.inactive}>
                        {process.status === 'active' ? 'Ativo' : 'Inativo'}
                    </Tag>
                </div>
                
                {process.description && (
                    <div style={{ fontSize: '13px', color: '#666', marginTop: '4px' }}>
                        {process.description}
                    </div>
                )}
                
                <Row gutter={[16, 8]} wrap>
                    {process.tools && (
                        <Col xs={24} sm={12} md={8}>
                            <div style={{ display: 'flex', alignItems: 'center', wordBreak: 'break-word' }}>
                                <ToolOutlined style={{ marginRight: '4px', color: '#1890ff', flexShrink: 0 }} />
                                <Text type="secondary" style={{ fontSize: '12px', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {process.tools}
                                </Text>
                            </div>
                        </Col>
                    )}
                    
                    {process.responsible && (
                        <Col xs={24} sm={12} md={8}>
                            <div style={{ display: 'flex', alignItems: 'center', wordBreak: 'break-word' }}>
                                <UserOutlined style={{ marginRight: '4px', color: '#52c41a', flexShrink: 0 }} />
                                <Text type="secondary" style={{ fontSize: '12px', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {process.responsible}
                                </Text>
                            </div>
                        </Col>
                    )}
                    
                    {process.documentation && (
                        <Col xs={24} sm={12} md={8}>
                            <Tooltip title={process.documentation}>
                                <div style={{ display: 'flex', alignItems: 'center', wordBreak: 'break-word' }}>
                                    <FileTextOutlined style={{ marginRight: '4px', color: '#722ed1', flexShrink: 0 }} />
                                    <Text type="secondary" style={{ fontSize: '12px', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                        Documentação
                                    </Text>
                                </div>
                            </Tooltip>
                        </Col>
                    )}
                </Row>
            </Space>
        </Card>
    );
};

// Componente para renderizar a árvore de processos
const ProcessTree = ({ processes, level = 0 }: { processes: ProcessTreeNode[]; level?: number }) => {
    return (
        <div style={{ width: '100%' }}>
            {processes.map((process) => (
                <div key={process.id}>
                    <ProcessCard process={process} level={level} />
                    {process.children && process.children.length > 0 && (
                        <div style={{ marginTop: '8px', maxWidth: '100%', overflow: 'hidden' }}>
                            <div style={{ 
                                display: 'flex', 
                                alignItems: 'center', 
                                marginBottom: '8px',
                                color: '#1890ff',
                                fontSize: '12px'
                            }}>
                                <ArrowRightOutlined style={{ marginRight: '4px' }} />
                                Subprocessos
                            </div>
                            <ProcessTree processes={process.children} level={level + 1} />
                        </div>
                    )}
                </div>
            ))}
        </div>
    );
};

export default function ProcessFlow({
    areaId,
    processId,
}: {
    areaId?: number;
    processId?: number;
}) {
    // Buscar dados da área para mostrar o nome
    const { data: areaData } = useQuery({
        queryKey: ['area', areaId],
        queryFn: async () => {
            if (areaId) {
                const r = await api.get(`/areas/${areaId}`);
                return r.data;
            }
            return null;
        },
        enabled: !!areaId,
        staleTime: 30000,
    });

    const { data, isLoading, error, refetch } = useQuery({
        queryKey: ['process-flow', areaId, processId],
        queryFn: async () => {
            if (areaId) {
                const r = await api.get<ProcessesTreeResponse>(`/areas/${areaId}/processes/tree`);
                return r.data;
            }
            if (processId) {
                const r = await api.get<ProcessTreeResponse>(`/processes/${processId}/tree`);
                return r.data;
            }
            return { success: true, data: { processes: [] as ProcessTreeNode[] } } as ProcessesTreeResponse;
        },
        staleTime: 30000,
    });

    // Função para contar todos os processos recursivamente
    const countAllProcesses = (processList: ProcessTreeNode[]): number => {
        return processList.reduce((total, process) => {
            return total + 1 + (process.children ? countAllProcesses(process.children) : 0);
        }, 0);
    };

    // Função para contar processos por critério recursivamente
    const countProcessesByCriteria = (processList: ProcessTreeNode[], criteria: (p: ProcessTreeNode) => boolean): number => {
        return processList.reduce((total, process) => {
            const matches = criteria(process) ? 1 : 0;
            const childrenMatches = process.children ? countProcessesByCriteria(process.children, criteria) : 0;
            return total + matches + childrenMatches;
        }, 0);
    };

    // Extrair dados para mostrar informações
    const processes = useMemo(() => {
        if (!data) return [];
        const payload: any = (data as any).data;
        if (payload && Array.isArray(payload.processes)) return payload.processes;
        if (payload && payload.data) return [payload.data];
        return [];
    }, [data]);

    // Calcular estatísticas
    const totalProcesses = countAllProcesses(processes);
    const activeProcesses = countProcessesByCriteria(processes, (p: ProcessTreeNode) => p.status === 'active');
    const highCriticalityProcesses = countProcessesByCriteria(processes, (p: ProcessTreeNode) => p.criticality === 'high');
    const internalProcesses = countProcessesByCriteria(processes, (p: ProcessTreeNode) => p.type === 'internal');
    const externalProcesses = countProcessesByCriteria(processes, (p: ProcessTreeNode) => p.type === 'external');

    if (isLoading) {
        return (
            <div style={{ 
                display: 'flex', 
                justifyContent: 'center', 
                alignItems: 'center', 
                height: '400px',
                background: '#f5f5f5',
                borderRadius: '8px'
            }}>
                <Text>Carregando fluxo de processos...</Text>
            </div>
        );
    }

    if (error) {
        return (
            <div style={{ 
                display: 'flex', 
                justifyContent: 'center', 
                alignItems: 'center', 
                height: '400px',
                background: '#fff2f0',
                borderRadius: '8px',
                border: '1px solid #ffccc7'
            }}>
                <Text type="danger">Erro ao carregar fluxo: {error.message}</Text>
            </div>
        );
    }

    return (
        <div style={{ padding: '20px', background: '#f5f5f5', minHeight: '100vh', maxWidth: '100%', overflow: 'hidden' }}>
            <Row gutter={[0, 20]}>
                <Col xs={24} sm={24} md={24} lg={24}>
                    <div style={{ 
                        background: 'white', 
                        borderRadius: '12px', 
                        padding: '24px',
                        boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
                    }}>
                        <Row justify="space-between" align="middle">
                            <Col>
                                <Title level={2} style={{ marginBottom: '0', color: '#1890ff' }}>
                                    Fluxo de Processos
                                    {areaData?.data?.name && ` - ${areaData.data.name}`}
                                </Title>
                            </Col>
                            <Col>
                                <Button 
                                    type="primary" 
                                    icon={<ReloadOutlined />} 
                                    onClick={() => refetch()}
                                    loading={isLoading}
                                >
                                    Recarregar
                                </Button>
                            </Col>
                        </Row>
                        
                        {processes.length > 0 && (
                            <div style={{ marginTop: '16px' }}>
                                <div style={{ 
                                    background: '#f8f9fa', 
                                    border: '1px solid #e9ecef', 
                                    borderRadius: '8px', 
                                    padding: '16px',
                                    marginTop: '16px'
                                }}>
                                    <div style={{ 
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '16px',
                                        margin: '0 auto'
                                    }}>
                                        <div style={{ 
                                            display: 'grid', 
                                            gridTemplateColumns: 'repeat(3, 1fr)', 
                                            gap: '16px',
                                            width: '100%'
                                        }}>
                                            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                                                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#1890ff' }}>
                                                    {totalProcesses}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#666', textTransform: 'uppercase' }}>
                                                    Total de Processos
                                                </div>
                                            </div>
                                            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                                                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#52c41a' }}>
                                                    {activeProcesses}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#666', textTransform: 'uppercase' }}>
                                                    Ativos
                                                </div>
                                            </div>
                                            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                                                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#ff4d4f' }}>
                                                    {highCriticalityProcesses}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#666', textTransform: 'uppercase' }}>
                                                    Alta Criticidade
                                                </div>
                                            </div>
                                        </div>
                                        <div style={{ 
                                            display: 'grid', 
                                            gridTemplateColumns: 'repeat(2, 1fr)', 
                                            gap: '16px',
                                            width: '50%'
                                        }}>
                                            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                                                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#722ed1' }}>
                                                    {internalProcesses}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#666', textTransform: 'uppercase' }}>
                                                    Internos
                                                </div>
                                            </div>
                                            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                                                <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#fa8c16' }}>
                                                    {externalProcesses}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#666', textTransform: 'uppercase' }}>
                                                    Externos
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </Col>

                <Col span={24}>
                    <div style={{ 
                        background: 'white', 
                        borderRadius: '12px', 
                        padding: '24px',
                        boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
                        minHeight: '600px',
                        maxWidth: '100%',
                        overflow: 'hidden'
                    }}>
                        <Title level={3} style={{ marginBottom: '16px', color: '#1890ff' }}>
                            Visualização Hierárquica dos Processos
                        </Title>
                        
                        {processes.length > 0 ? (
                            <ProcessTree processes={processes} />
                        ) : (
                            <div style={{ 
                                display: 'flex', 
                                justifyContent: 'center', 
                                alignItems: 'center', 
                                height: '400px',
                                background: '#fafafa',
                                borderRadius: '8px',
                                border: '1px dashed #d9d9d9'
                            }}>
                                <Text type="secondary">Nenhum processo encontrado para visualização</Text>
                            </div>
                        )}
                    </div>
                </Col>
            </Row>
        </div>
    );
}
