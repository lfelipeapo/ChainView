import { Form, Input, Button, Card, message } from 'antd'
import { UserOutlined, LockOutlined, LoginOutlined } from '@ant-design/icons'
import { useAuth } from '../hooks/useAuth'
import { useEffect } from 'react'

interface LoginForm {
  email: string
  password: string
}

export default function Login() {
  const { login, isLoggingIn, loginError } = useAuth()

  // Teste da API ao carregar o componente
  useEffect(() => {
    const testAPI = async () => {
      try {
        console.log('Testando conexão com API...')
        const response = await fetch(`${window.location.origin}/api/areas/tree`)
        const data = await response.json()
        console.log('API funcionando:', data)
      } catch (error) {
        console.error('Erro ao conectar com API:', error)
      }
    }
    testAPI()
  }, [])

  const onFinish = async (values: LoginForm) => {
    console.log('Tentando fazer login com:', values)
    try {
      const result = await login({
        ...values,
        device_name: 'ChainView Web'
      })
      console.log('Login bem-sucedido:', result)
      message.success('Login realizado com sucesso!')
    } catch (error) {
      console.error('Erro no login:', error)
      const errorMessage = 'Erro ao fazer login'
      message.error(errorMessage)
    }
  }

  return (
    <div style={{
      minHeight: '100vh',
      width: '100vw',
      background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      padding: '20px'
    }}>
      <Card
        title={
          <div style={{ textAlign: 'center' }}>
            <h1 style={{ color: '#1890ff', margin: 0 }}>ChainView</h1>
            <p style={{ margin: '8px 0 0 0', color: '#666' }}>Sistema de Gestão de Processos</p>
          </div>
        }
        style={{
          width: '100%',
          maxWidth: 400,
          borderRadius: 12,
          boxShadow: '0 10px 30px rgba(0,0,0,0.2)'
        }}
      >
        <Form
          name="login"
          onFinish={onFinish}
          layout="vertical"
          size="large"
        >
          <Form.Item
            name="email"
            label="Email"
            rules={[
              { required: true, message: 'Por favor, insira seu email!' },
              { type: 'email', message: 'Por favor, insira um email válido!' }
            ]}
          >
            <Input
              prefix={<UserOutlined />}
              placeholder="admin@chainview.com"
            />
          </Form.Item>

          <Form.Item
            name="password"
            label="Senha"
            rules={[
              { required: true, message: 'Por favor, insira sua senha!' }
            ]}
          >
            <Input.Password
              prefix={<LockOutlined />}
              placeholder="admin123"
            />
          </Form.Item>

          <Form.Item>
            <Button
              type="primary"
              htmlType="submit"
              loading={isLoggingIn}
              icon={<LoginOutlined />}
              style={{
                width: '100%',
                height: 48,
                borderRadius: 8,
                fontSize: 16
              }}
            >
              {isLoggingIn ? 'Entrando...' : 'Entrar'}
            </Button>
          </Form.Item>

          {loginError && (
            <div style={{
              padding: '12px',
              background: '#fff2f0',
              border: '1px solid #ffccc7',
              borderRadius: 6,
              color: '#ff4d4f',
              textAlign: 'center'
            }}>
              Erro ao fazer login
            </div>
          )}

          <div style={{
            marginTop: 16,
            padding: '12px',
            background: '#f6ffed',
            border: '1px solid #b7eb8f',
            borderRadius: 6,
            fontSize: '12px',
            color: '#52c41a'
          }}>
            <strong>Credenciais de teste:</strong><br />
            Email: admin@chainview.com<br />
            Senha: admin123
          </div>
        </Form>
      </Card>
    </div>
  )
}
