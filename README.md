# ChainView - Sistema de Gestão de Processos Hierárquicos

## 📋 Descrição do Projeto

O **ChainView** é um sistema inovador de gestão de processos empresariais que permite organizar e visualizar processos e subprocessos de forma hierárquica ilimitada. O sistema resolve o problema de complexidade na gestão de processos organizacionais, oferecendo uma visualização clara e interativa da cadeia de processos.

### 🎯 Problema que Resolve

- **Complexidade Organizacional**: Empresas com processos complexos e interconectados
- **Falta de Visualização**: Dificuldade em visualizar a hierarquia de processos
- **Gestão Manual**: Processos gerenciados de forma desorganizada
- **Falta de Detalhamento**: Ausência de informações detalhadas sobre cada processo

### 💡 Solução Proposta

O ChainView oferece uma solução completa para gestão de processos com:
- **Hierarquia Ilimitada**: Subprocessos ilimitados em níveis
- **Visualização Interativa**: Interface moderna e intuitiva
- **Detalhamento Completo**: Ferramentas, responsáveis e documentação
- **Preview de Links**: Suporte a links e imagens com preview
- **Gestão Centralizada**: Tudo em uma única plataforma

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 8**: Framework PHP robusto e maduro
- **PostgreSQL**: Banco de dados relacional avançado
- **Docker**: Containerização para desenvolvimento
- **Nginx**: Servidor web de alta performance
- **PHP-FPM**: Process Manager para PHP

### Frontend
- **React 18**: Biblioteca JavaScript moderna
- **TypeScript**: Tipagem estática para maior confiabilidade
- **Ant Design**: UI Library profissional
- **Vite**: Build tool rápido e moderno
- **Axios**: Cliente HTTP para APIs

### DevOps & Infraestrutura
- **Docker Compose**: Orquestração de containers
- **Nginx**: Proxy reverso e servidor web
- **Supervisord**: Gerenciamento de processos
- **Git**: Controle de versão

## 🏗️ Arquitetura do Sistema

### Backend (Laravel API)
```
applications/doc-viewer/
├── app/Http/Controllers/     # Controllers da API
├── app/Models/              # Modelos Eloquent
├── database/migrations/     # Migrações do banco
├── database/seeders/        # Seeders para dados iniciais
├── routes/api.php          # Rotas da API
└── config/                 # Configurações
```

### Frontend (React)
```
frontend/
├── src/components/         # Componentes React
├── src/hooks/             # Custom Hooks
├── src/api.ts             # Configuração da API
└── vite.config.ts         # Configuração do Vite
```

## 🎨 Diferenciais Implementados

### 1. **Hierarquia Ilimitada de Processos**
- Subprocessos em níveis ilimitados
- Visualização em árvore interativa
- Navegação intuitiva entre níveis

### 2. **Preview de Links e Imagens**
- Detecção automática de URLs
- Preview de imagens inline
- Links clicáveis com abertura em nova aba
- Tooltips interativos com preview

### 3. **Detalhamento Completo de Processos**
- **Ferramentas**: Sistemas e ferramentas utilizadas
- **Responsáveis**: Pessoas responsáveis pelo processo
- **Documentação**: Links e descrições de documentação
- **Criticidade**: Níveis de criticidade (Alta, Média, Baixa)
- **Status**: Controle de processos ativos/inativos

### 4. **Interface Moderna e Responsiva**
- Design centralizado e responsivo
- Cores baseadas na criticidade dos processos
- Scrollbar customizada
- Estatísticas visuais no topo

### 5. **CRUD Completo**
- Criação de áreas e processos
- Edição de processos existentes
- Remoção com confirmação
- Visualização hierárquica

### 6. **Validação e Feedback**
- Validação de formulários
- Mensagens de sucesso/erro
- Confirmações para exclusões
- Loading states

## 📊 Estrutura do Banco de Dados

### Tabela `areas`
- `id`: Identificador único
- `name`: Nome da área
- `created_at`, `updated_at`: Timestamps

### Tabela `processes`
- `id`: Identificador único
- `area_id`: Referência à área
- `parent_id`: Referência ao processo pai (hierarquia)
- `name`: Nome do processo
- `description`: Descrição detalhada
- `type`: Tipo (interno/externo)
- `criticality`: Criticidade (alta/média/baixa)
- `status`: Status (ativo/inativo)
- `tools`: Ferramentas utilizadas
- `responsible`: Responsável pelo processo
- `documentation`: Documentação associada
- `created_at`, `updated_at`: Timestamps

## 🚀 Como Executar o Projeto

### Pré-requisitos
- Docker e Docker Compose
- Node.js 16+ (para desenvolvimento frontend)
- Git

### 1. Clone o Repositório
```bash
git clone <repository-url>
cd ChainView
```

### 2. Backend (Laravel)
```bash
# Iniciar containers
docker-compose up -d

# Executar migrações
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan migrate"

# Executar seeders (opcional)
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan db:seed"

# Verificar se está rodando
curl http://localhost:8082/api/health
```

### 3. Frontend (React)
```bash
# Instalar dependências
cd frontend
npm install

# Executar em modo desenvolvimento
npm run dev
```

### 4. Acessar o Sistema
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8082/api
- **Health Check**: http://localhost:8082/api/health

## 📡 Endpoints da API

### Áreas
- `GET /api/areas` - Listar todas as áreas
- `GET /api/areas/tree` - Listar áreas em formato de árvore
- `POST /api/areas` - Criar nova área
- `PUT /api/areas/{id}` - Atualizar área
- `DELETE /api/areas/{id}` - Remover área

### Processos
- `GET /api/processes` - Listar todos os processos
- `POST /api/processes` - Criar novo processo
- `PUT /api/processes/{id}` - Atualizar processo
- `DELETE /api/processes/{id}` - Remover processo
- `GET /api/processes/{id}/tree` - Obter árvore de um processo

### Health Check
- `GET /api/health` - Status do sistema e estatísticas

## 🎯 Casos de Uso

### 1. **Gestão de RH**
- Área: Recursos Humanos
- Processos: Recrutamento, Folha de Pagamento
- Subprocessos: Triagem de CV, Cálculo de Salários

### 2. **Gestão Financeira**
- Área: Financeiro
- Processos: Contas a Pagar, Contas a Receber
- Subprocessos: Aprovação de Pagamentos, Conciliação

### 3. **Gestão de Projetos**
- Área: Projetos
- Processos: Planejamento, Execução
- Subprocessos: Cronograma, Controle de Qualidade

## 🔧 Configurações Avançadas

### Variáveis de Ambiente
```env
# Backend
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=chainview
DB_USERNAME=postgres
DB_PASSWORD=password

# Frontend
VITE_API_URL=http://localhost:8082/api
```

### Docker Compose
```yaml
services:
  doc-viewer:
    image: lfelipeapo/php-nginx-web:1.0.0
    ports:
      - "8082:8082"
    volumes:
      - ./applications/doc-viewer:/var/www/doc-viewer
    depends_on:
      - postgres

  postgres:
    image: postgres:14.3-alpine
    ports:
      - "20000:5432"
    environment:
      POSTGRES_DB: chainview
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
```

## 🧪 Testes

### Backend
```bash
# Executar testes
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan test"
```

### Frontend
```bash
# Executar testes
cd frontend
npm test
```

## 📈 Melhorias Futuras

- [ ] **Autenticação**: Sistema de login e permissões
- [ ] **Relatórios**: Geração de relatórios em PDF
- [ ] **Notificações**: Sistema de alertas e notificações
- [ ] **Importação**: Importação de dados via CSV/Excel
- [ ] **API Externa**: Integração com sistemas externos
- [ ] **Mobile**: Aplicativo mobile nativo
- [ ] **Analytics**: Dashboard com métricas e KPIs

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Felipe Apo** - Desenvolvedor Full Stack

- LinkedIn: [Felipe Apo](https://linkedin.com/in/felipeapo)
- GitHub: [@lfelipeapo](https://github.com/lfelipeapo)

## 🙏 Agradecimentos

- Laravel Framework pela base sólida
- Ant Design pela UI Library
- Comunidade open source pelo suporte
- Todos os contribuidores do projeto

---

**ChainView** - Transformando a gestão de processos empresariais através da tecnologia moderna e inovação. 🚀
