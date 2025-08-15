import { Routes, Route, useParams, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from './hooks/useAuth';
import Login from './components/Login';
import AreaTable from './components/AreaTable';
import AreaTree from './components/AreaTree';
import ProcessFlow from './components/ProcessFlow';
import { useAreaTree } from './hooks/useAreaTree';

export default function App() {
  const { isAuthenticated, isLoadingUser, logout, user } = useAuth();
  const { data = [] } = useAreaTree();
  const location = useLocation();
  const navigate = useNavigate();

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
    );
  }

  if (!isAuthenticated) return <Login />;

  return (
    <div className="app-container">
      <div className="app-header">
        <div style={{ fontSize: '18px', fontWeight: 'bold' }}>
          ChainView - Sistema de Gestão de Processos
        </div>
        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
          {location.pathname.startsWith('/flow') && (
            <button
              onClick={() => navigate(-1)}
              style={{
                background: '#555',
                color: 'white',
                border: 'none',
                padding: '6px 12px',
                borderRadius: '4px',
                cursor: 'pointer'
              }}
            >
              Voltar
            </button>
          )}
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

      <div className="app-content">
        <Routes>
          <Route path="/" element={<AreaTree />} />
          <Route path="/areas" element={<AreaTable data={data} />} />
          <Route path="/flow/area/:id" element={<FlowAreaWrapper />} />
          <Route path="/flow/process/:id" element={<FlowProcessWrapper />} />
        </Routes>
      </div>
    </div>
  );
}

function FlowAreaWrapper() {
  const { id } = useParams();
  return <ProcessFlow areaId={Number(id)} />;
}

function FlowProcessWrapper() {
  const { id } = useParams();
  return <ProcessFlow processId={Number(id)} />;
}
