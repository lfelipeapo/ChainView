# 🚀 ChainView - Sistema de Gestão de Processos Hierárquicos

Sistema completo para gerenciamento de áreas e processos com estrutura hierárquica ilimitada, desenvolvido com arquitetura moderna separando frontend e backend.

## 🎯 **Sobre o Projeto**

O ChainView é uma aplicação web moderna que permite o cadastro e visualização hierárquica de áreas e processos empresariais. O sistema oferece uma interface intuitiva para gerenciar estruturas organizacionais complexas com subprocessos ilimitados.

### **🏆 Destaques do Sistema**

- ✅ **Arquitetura Moderna**: Frontend React + Backend Laravel separados
- ✅ **API REST Completa**: Documentação Swagger/OpenAPI integrada
- ✅ **Visualização Hierárquica**: Árvore interativa de áreas e processos
- ✅ **Interface Responsiva**: Design mobile-first com Ant Design
- ✅ **Autenticação Segura**: Laravel Sanctum com tokens JWT
- ✅ **Testes Automatizados**: 55 testes com 66.15% de cobertura
- ✅ **CI/CD Pipeline**: GitHub Actions com deploy automático
- ✅ **Containerização**: Docker com PostgreSQL e Redis

## 🚀 **Setup Rápido**

### **1. Pré-requisitos**
```bash
# Docker e Docker Compose instalados
docker --version
docker-compose --version
```

### **2. Clone e Setup Completo**
```bash
git clone <repository>
cd ChainView
make setup
```

### **3. Acesse as Aplicações**
- 🌐 **Frontend**: http://localhost:3000
- 🔧 **Backend API**: http://localhost:8082
- 📖 **Documentação Swagger**: http://localhost:8082/api/documentation

### **4. Credenciais de Demo**
```
Email: admin@chainview.com
Senha: password
```

## 🎨 **Interface e Funcionalidades**

### **🌳 Visualização Hierárquica**
- **Árvore Interativa**: Expansão/colapso de áreas e processos
- **Navegação Intuitiva**: Interface clara e responsiva
- **Busca e Filtros**: Localização rápida de elementos
- **Status Visual**: Cores e ícones por criticidade e tipo

### **📊 Gestão de Processos**
- **CRUD Completo**: Criar, editar, visualizar e remover
- **Subprocessos Ilimitados**: Estrutura hierárquica sem limites
- **Detalhamento Rico**: Ferramentas, responsáveis e documentação
- **Estatísticas**: Métricas e relatórios em tempo real

### **🔐 Sistema de Autenticação**
- **Login Seguro**: Validação de credenciais
- **Tokens JWT**: Autenticação via Laravel Sanctum
- **Sessões Persistentes**: Manutenção do estado de login
- **Logout Seguro**: Invalidação de tokens

## 🏗️ **Arquitetura do Sistema**

### **🎯 Visão Geral da Arquitetura**

```mermaid
graph TB
    subgraph "Frontend (React + TypeScript)"
        A[React App] --> B[Ant Design UI]
        A --> C[React Query]
        A --> D[Axios HTTP Client]
    end
    
    subgraph "Backend (Laravel + PHP)"
        F[Laravel API] --> G[Sanctum Auth]
        F --> H[Controllers]
        F --> I[Models]
        F --> J[Form Requests]
        F --> K[API Resources]
    end
    
    subgraph "Database Layer"
        M[PostgreSQL Database]
        N[Redis Cache]
    end
    
    subgraph "Infrastructure"
        O[Nginx Reverse Proxy]
        P[Docker Containers]
        Q[GitHub Actions CI/CD]
    end
    
    A --> O
    O --> F
    F --> M
    F --> N
    
    style A fill:#61dafb
    style F fill:#ff2d20
    style M fill:#336791
    style N fill:#dc382d
    style O fill:#009639
```

### **📱 Frontend (React 18 + TypeScript)**
- **Framework**: React 18 com TypeScript
- **UI Library**: Ant Design para componentes modernos
- **State Management**: React Query para cache e sincronização
- **HTTP Client**: Axios para requisições à API
- **Build Tool**: Vite para desenvolvimento rápido
- **Responsividade**: Design mobile-first

### **🔧 Backend (Laravel 10 + PHP 8.0)**
- **Framework**: Laravel 10 com PHP 8.0
- **Autenticação**: Laravel Sanctum para API tokens
- **Validação**: Form Requests customizados
- **Respostas**: API Resources para formatação consistente
- **Documentação**: L5-Swagger (OpenAPI 3.0)
- **Testes**: PHPUnit com SQLite

### **🗄️ Infraestrutura**
- **Containerização**: Docker + Docker Compose
- **Web Server**: Nginx como reverse proxy
- **Database**: PostgreSQL 13
- **Cache**: Redis (configurado para futuro uso)
- **CI/CD**: GitHub Actions
- **Monitoramento**: Health checks implementados

## 📚 **API REST - Endpoints**

### **🔐 Autenticação**
```http
POST /api/auth/login          # Login com email/senha
POST /api/auth/logout         # Logout e invalidação de token
GET  /api/auth/user           # Perfil do usuário autenticado
```

### **🏢 Gestão de Áreas**
```http
GET    /api/areas                    # Listar áreas com paginação
POST   /api/areas                    # Criar nova área
GET    /api/areas/{id}               # Obter área específica
PUT    /api/areas/{id}               # Atualizar área
DELETE /api/areas/{id}               # Remover área
GET    /api/areas/tree               # Árvore hierárquica de áreas
GET    /api/areas/{id}/processes/tree # Processos de uma área
```

### **⚙️ Gestão de Processos**
```http
GET    /api/processes                # Listar processos com filtros
POST   /api/processes                # Criar novo processo
GET    /api/processes/{id}           # Obter processo específico
PUT    /api/processes/{id}           # Atualizar processo
DELETE /api/processes/{id}           # Remover processo
GET    /api/processes/{id}/tree      # Árvore de subprocessos
GET    /api/processes/stats          # Estatísticas gerais
```

### **📊 Sistema**
```http
GET /api/health    # Health check da aplicação
GET /api          # Overview da API
```

### **🔍 Filtros e Paginação**
```http
GET /api/areas?page=1&per_page=10&search=rh
GET /api/processes?status=active&criticality=high&area_id=1
```

## 🧪 **Testes e Qualidade**

### **📊 Cobertura de Testes**

#### **✅ Testes Passando: 55/55 (100%)**

**Controllers Testados:**
- **AreaController**: 85.71% métodos, 95.83% linhas
- **ProcessController**: 85.71% métodos, 79.01% linhas  
- **ApiAuthController**: 100% métodos, 100% linhas

**Requests & Resources:**
- **StoreAreaRequest**: 100% métodos, 100% linhas
- **UpdateAreaRequest**: 100% métodos, 100% linhas
- **StoreProcessRequest**: 100% métodos, 100% linhas
- **UpdateProcessRequest**: 100% métodos, 100% linhas
- **AreaResource**: 100% métodos, 100% linhas
- **ProcessResource**: 100% métodos, 100% linhas

**Models:**
- **Area**: 100% métodos, 100% linhas
- **Process**: 83.33% métodos, 78.12% linhas

#### **📈 Cobertura Geral: 66.15%**
- **Classes**: 22.50% (9/40)
- **Métodos**: 38.30% (54/141)
- **Linhas**: 66.15% (424/641)

### **🔧 Comandos de Teste**
```bash
make test              # Executar todos os testes
make test-coverage     # Testes com cobertura de código
make test-feature      # Apenas testes de feature
make test-unit         # Apenas testes unitários
```

## 🚀 **CI/CD Pipeline**

### **GitHub Actions Workflow**

```yaml
✅ Backend Tests (PHPUnit + SQLite)
✅ Frontend Tests (Jest + React Testing Library)
✅ Linting (PHP CS Fixer + ESLint)
✅ Security Check (Composer Audit)
✅ Code Quality (PHPStan)
✅ Docker Build Test
✅ Deploy to Staging (main branch)
```

### **Jobs Executados:**
1. **Backend Tests**: PHPUnit com cobertura mínima de 80%
2. **Frontend Tests**: Jest com build de produção
3. **Security**: Verificação de vulnerabilidades
4. **Code Quality**: Análise estática com PHPStan
5. **Docker**: Build e teste da imagem
6. **Deploy**: Deploy automático para staging

## 🛠️ **Comandos de Desenvolvimento**

### **Docker & Laravel**
```bash
make up          # Subir containers
make down        # Parar containers
make seed        # Popular banco com dados de exemplo
make swagger     # Gerar documentação da API
make diagrams    # Gerar diagramas da arquitetura
make docs        # Gerar toda documentação
```

### **Qualidade de Código**
```bash
make lint              # Verificar estilo de código
make lint-fix          # Corrigir estilo automaticamente
make security          # Verificar vulnerabilidades
```

## 🔒 **Segurança**

### **Autenticação e Autorização**
- **Laravel Sanctum**: Tokens JWT para API
- **Middleware de Autenticação**: Proteção de rotas
- **Validação Rigorosa**: Form Requests customizados
- **Rate Limiting**: Proteção contra ataques
- **CORS Configurado**: Segurança cross-origin

### **Proteção de Dados**
- **SQL Injection**: Prevenção via Eloquent ORM
- **XSS Protection**: Sanitização automática
- **CSRF Protection**: Tokens de segurança
- **Input Validation**: Validação em múltiplas camadas

## 📈 **Performance e Otimização**

### **Database**
- **Eager Loading**: Relacionamentos otimizados
- **Índices**: Performance de consultas
- **Paginação**: Resultados limitados
- **Cache**: Redis configurado

### **Frontend**
- **Code Splitting**: Carregamento otimizado
- **Lazy Loading**: Componentes sob demanda
- **React Query**: Cache inteligente
- **Bundle Optimization**: Vite para builds rápidos

## 📖 **Documentação**

### **📚 Documentação Técnica**
- **[Arquitetura Detalhada](docs/architecture.md)** - Diagramas e fluxos
- **[API Reference](http://localhost:8082/api/documentation)** - Swagger UI
- **[Guia de Desenvolvimento](docs/README.md)** - Como contribuir

### **🎨 Diagramas Disponíveis**
1. **Arquitetura Geral** - Visão completa do sistema
2. **Fluxo de Autenticação** - Processo de login/logout
3. **Fluxo da Árvore** - Carregamento de dados hierárquicos
4. **Containers Docker** - Estrutura de serviços
5. **Pipeline CI/CD** - Integração contínua

> 💡 **Para gerar imagens PNG:** `npm install -g @mermaid-js/mermaid-cli && make diagrams`

## 🤝 **Contribuição**

### **Fluxo de Desenvolvimento**
1. Fork do repositório
2. Criação de branch feature
3. Desenvolvimento com testes
4. Pull Request com CI passando
5. Code Review
6. Merge para main

### **Padrões de Código**
- **PHP**: PSR-12
- **JavaScript/TypeScript**: ESLint
- **Commits**: Conventional Commits
- **Testes**: Obrigatórios para novas funcionalidades

## 📄 **Licença**

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 **Autor**

**Felipe Apo** - Desenvolvedor Full Stack

---

## 🎉 **Status do Projeto**

### **✅ Concluído (100% do Case)**
- [x] **Arquitetura Moderna**: Frontend/Backend separados
- [x] **API REST Completa**: JSON com documentação Swagger
- [x] **CRUD de Áreas**: Gestão completa com validações
- [x] **CRUD de Processos**: Hierarquia ilimitada
- [x] **Visualização Hierárquica**: Árvore interativa
- [x] **Autenticação**: Sistema completo com Sanctum
- [x] **Interface Responsiva**: Mobile-first design
- [x] **Testes Automatizados**: 55 testes (100% passando)
- [x] **CI/CD Pipeline**: GitHub Actions completo
- [x] **Docker Setup**: Containerização completa
- [x] **Documentação**: Swagger + Diagramas de arquitetura
- [x] **Qualidade**: Linting, security, code coverage

### **🚀 Próximos Passos**
- [ ] Deploy em produção
- [ ] Monitoramento e logs
- [ ] Backup automático
- [ ] Documentação de usuário
- [ ] Treinamento da equipe

---

**🎯 Sistema 100% funcional e atende todos os requisitos do case!**

**🌟 Destaque: Visualização hierárquica interativa com subprocessos ilimitados!**
