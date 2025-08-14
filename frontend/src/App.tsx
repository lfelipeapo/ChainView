import { Routes, Route } from 'react-router-dom'
import { useAuth } from './hooks/useAuth'
import Login from './components/Login'
import AreaTable from './components/AreaTable'
import AreaTree from './components/AreaTree'
import { useAreaTree } from './hooks/useAreaTree'

export default function App() {
  const { isAuthenticated, isLoadingUser, logout, user } = useAuth()
  const { data = [] } = useAreaTree()

  // Mostrar loading enquanto verifica autenticação
  if (isLoadingUser) {
    return (
      <div style={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
      }}>
        <div style={{ color: 'white', fontSize: '18px' }}>Carregando...</div>
      </div>
    )
  }

  // Se não estiver autenticado, mostrar tela de login
  if (!isAuthenticated) {
    return <Login />
  }

  return (
    <div>
      {/* Header com informações do usuário */}
      <div style={{
        background: '#001529',
        color: 'white',
        padding: '12px 24px',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        borderBottom: '1px solid #1890ff'
      }}>
        <div style={{ fontSize: '18px', fontWeight: 'bold' }}>
          ChainView - Sistema de Gestão de Processos
        </div>
        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
          <span>Olá, {user?.name}</span>
          <button
            onClick={logout}
            style={{
              background: '#ff4d4f',
              color: 'white',
              border: 'none',
              padding: '6px 12px',
              borderRadius: '4px',
              cursor: 'pointer'
            }}
          >
            Sair
          </button>
        </div>
      </div>

      <Routes>
        <Route path="/" element={<AreaTree />} />
        <Route path="/areas" element={<AreaTable data={data} />} />
      </Routes>
    </div>
  )
}
