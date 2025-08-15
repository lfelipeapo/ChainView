# ChainView Frontend

## 📋 Descrição

Frontend do sistema ChainView desenvolvido em React + TypeScript com Ant Design.

## 🚀 Tecnologias

- **React 18**: Biblioteca JavaScript moderna
- **TypeScript**: Tipagem estática
- **Ant Design**: UI Library profissional
- **Vite**: Build tool rápido
- **Axios**: Cliente HTTP

## 🏗️ Estrutura

```
frontend/
├── src/
│   ├── components/          # Componentes React
│   │   └── AreaTree.tsx    # Componente principal
│   ├── hooks/              # Custom Hooks
│   │   └── useAreaTree.ts  # Hook para dados da árvore
│   ├── api.ts              # Configuração da API
│   ├── App.tsx             # Componente raiz
│   └── main.tsx            # Entry point
├── vite.config.ts          # Configuração do Vite
└── package.json            # Dependências
```

## 🚀 Como Executar

```bash
# Instalar dependências
npm install

# Executar em desenvolvimento
npm run dev

# Build para produção
npm run build

# Preview do build
npm run preview
```

## 🎨 Funcionalidades

- **Hierarquia Ilimitada**: Subprocessos em níveis ilimitados
- **Preview de Links**: Detecção e preview de URLs e imagens
- **CRUD Completo**: Criação, edição e remoção de processos
- **Interface Responsiva**: Design adaptável a diferentes telas
- **Validação**: Formulários com validação completa

## 🔧 Configuração

### Variáveis de Ambiente
```env
VITE_API_URL=http://localhost/api
```

### Porta Padrão
- **Desenvolvimento**: http://localhost:3000
- **API Backend**: http://localhost/api

## 📱 Componentes Principais

### AreaTree.tsx
Componente principal que gerencia:
- Visualização hierárquica de áreas e processos
- Formulários de criação/edição
- Preview de links e imagens
- CRUD completo

### PreviewRenderer
Componente para renderizar:
- Preview de links
- Preview de imagens
- Tooltips interativos

## 🎯 Casos de Uso

1. **Criar Área**: Clique em "Add Root Area"
2. **Criar Processo**: Clique em "Add Process" em uma área
3. **Criar Subprocesso**: Clique em "Add Sub" em um processo
4. **Editar**: Clique no ícone de edição
5. **Remover**: Clique no ícone de lixeira
6. **Ver Preview**: Passe o mouse sobre tags "Desc" ou "Doc"

## 🔗 Integração com Backend

O frontend se comunica com o backend Laravel através de:
- **Axios**: Cliente HTTP configurado
- **API REST**: Endpoints padronizados
- **JSON**: Formato de dados

## 🧪 Testes

```bash
# Executar testes
npm test

# Testes em modo watch
npm run test:watch
```

## 📦 Build

```bash
# Build para produção
npm run build

# Análise do bundle
npm run build -- --analyze
```

## 🚀 Deploy

O frontend pode ser deployado em:
- **Vercel**: Deploy automático
- **Netlify**: Deploy automático
- **GitHub Pages**: Deploy estático
- **Docker**: Containerização

## 🔧 Desenvolvimento

### Scripts Disponíveis
- `npm run dev`: Servidor de desenvolvimento
- `npm run build`: Build para produção
- `npm run preview`: Preview do build
- `npm run lint`: Linting do código
- `npm run type-check`: Verificação de tipos

### Estrutura de Dados

```typescript
interface AreaNode {
  id: number
  name: string
  children?: AreaNode[]
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
  tools?: string
  responsible?: string
  documentation?: string
  children?: ProcessNode[]
}
```

---

**ChainView Frontend** - Interface moderna e intuitiva para gestão de processos. 🎨
