# ChainView - Sistema de Gerenciamento de Processos

Sistema completo para visualização e gerenciamento de áreas e processos organizacionais.

## 🚀 Setup Rápido

### Pré-requisitos
- Docker e Docker Compose
- Node.js 18+ (para frontend)
- Make (opcional, para usar os scripts)

### Setup Completo (Recomendado)
```bash
# Setup completo com um comando
make setup
```

### Setup Manual
```bash
# 1. Subir containers
docker-compose up -d

# 2. Executar migrations e seeders
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan migrate:fresh --seed"

# 3. Instalar dependências do frontend
cd frontend
npm install
npm run dev
```

## 📚 Demo/Onboarding

### Cenário RH (Já Populado)
O sistema vem com dados de exemplo do cenário de Recursos Humanos:

**Áreas:**
- Recursos Humanos
- Recrutamento  
- Financeiro

**Processos Exemplo:**
- Folha de Pagamento (com subprocessos)
- Gestão de Benefícios
- Triagem de Currículos
- Entrevistas
- E muito mais...

### Credenciais de Acesso
- **Email:** `admin@chainview.com`
- **Senha:** `admin123`

## 🔧 Comandos Úteis

```bash
# Subir containers
make up

# Executar seeders
make seed

# Gerar documentação Swagger
make swagger

# Setup completo
make setup
```

## 🌐 URLs de Acesso

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8082
- **Documentação Swagger:** http://localhost:8082/api/documentation

## 🔐 Segurança

- **GET:** Acesso público para leitura
- **POST/PUT/DELETE:** Requer autenticação via Sanctum
- **Autenticação:** Bearer Token (JWT)

## 📖 Documentação da API

Acesse a documentação interativa em: http://localhost:8082/api/documentation

### Endpoints Principais

#### Autenticação
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout (autenticado)
- `GET /api/auth/user` - Dados do usuário (autenticado)

#### Áreas
- `GET /api/areas` - Listar áreas
- `POST /api/areas` - Criar área (autenticado)
- `PUT /api/areas/{id}` - Atualizar área (autenticado)
- `DELETE /api/areas/{id}` - Remover área (autenticado)

#### Processos
- `GET /api/processes` - Listar processos
- `POST /api/processes` - Criar processo (autenticado)
- `PUT /api/processes/{id}` - Atualizar processo (autenticado)
- `DELETE /api/processes/{id}` - Remover processo (autenticado)

## 🛠️ Tecnologias

### Backend
- **Laravel 8** - Framework PHP
- **PostgreSQL** - Banco de dados
- **Laravel Sanctum** - Autenticação API
- **L5-Swagger** - Documentação API

### Frontend
- **React 18** - Framework JavaScript
- **TypeScript** - Tipagem estática
- **Ant Design** - UI Components
- **React Query** - Gerenciamento de estado
- **Vite** - Build tool

### Infraestrutura
- **Docker** - Containerização
- **Nginx** - Web server
- **PHP-FPM** - Process manager

## 📱 Responsividade

O sistema é totalmente responsivo e otimizado para:
- **Desktop** - Layout completo com árvore colapsável
- **Mobile** - Layout em cards empilhados com elementos touch-friendly

## 🎯 Funcionalidades

- ✅ **Gestão de Áreas** - Criar, editar, remover áreas
- ✅ **Gestão de Processos** - Criar, editar, remover processos e subprocessos
- ✅ **Hierarquia Visual** - Árvore de processos com expansão automática
- ✅ **Filtros e Busca** - Filtrar por criticidade, status e busca textual
- ✅ **Autenticação** - Login/logout com tokens JWT
- ✅ **Responsividade** - Layout adaptativo para mobile
- ✅ **Documentação API** - Swagger interativo

## 🚀 Próximos Passos

1. Acesse http://localhost:3000
2. Faça login com as credenciais fornecidas
3. Explore as áreas e processos de exemplo
4. Teste a criação de novos processos e subprocessos
5. Experimente os filtros e busca
6. Teste a responsividade no mobile

**Divirta-se explorando o ChainView!** 🎉
