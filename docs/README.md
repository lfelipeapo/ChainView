# 📚 Documentação do ChainView

Este diretório contém toda a documentação técnica do projeto ChainView.

## 📁 Estrutura

```
docs/
├── README.md              # Este arquivo
├── architecture.md        # Documentação completa da arquitetura
├── images/               # Diagramas em PNG (gerados automaticamente)
│   ├── architecture-general.png
│   ├── auth-flow.png
│   ├── tree-flow.png
│   ├── docker-containers.png
│   └── cicd-pipeline.png
└── scripts/              # Scripts de geração de documentação
    └── generate-diagrams.sh
```

## 🎨 Diagramas da Arquitetura

### Como Gerar os Diagramas

1. **Instalar Mermaid CLI:**
   ```bash
   npm install -g @mermaid-js/mermaid-cli
   ```

2. **Gerar diagramas:**
   ```bash
   make diagrams
   # ou
   ./scripts/generate-diagrams.sh
   ```

### Diagramas Disponíveis

#### 1. **Arquitetura Geral** (`architecture-general.png`)
- Visão geral de todos os componentes do sistema
- Frontend, Backend, Database e Infraestrutura
- Fluxo de dados entre componentes

#### 2. **Fluxo de Autenticação** (`auth-flow.png`)
- Sequência completa de login/logout
- Interação entre Frontend, API e Database
- Gerenciamento de tokens

#### 3. **Fluxo da Árvore de Processos** (`tree-flow.png`)
- Como os dados são carregados e exibidos
- Processo de expansão de áreas e processos
- Interação com a API

#### 4. **Containers Docker** (`docker-containers.png`)
- Estrutura dos containers
- Mapeamento de portas
- Serviços disponíveis

#### 5. **Pipeline CI/CD** (`cicd-pipeline.png`)
- Fluxo completo de integração contínua
- Jobs do GitHub Actions
- Processo de deploy

## 📊 Como Usar os Diagramas

### Para Apresentações
- Use os arquivos PNG em apresentações
- Diagramas têm fundo transparente
- Alta resolução para slides

### Para Documentação
- Inclua nos READMEs
- Use em documentação técnica
- Referencie em issues e PRs

### Para Desenvolvimento
- Entenda o fluxo de dados
- Visualize a arquitetura
- Planeje novas funcionalidades

## 🔧 Personalização

### Modificar Diagramas
1. Edite o arquivo `architecture.md`
2. Execute `make diagrams` para regenerar
3. Os PNGs serão atualizados automaticamente

### Adicionar Novos Diagramas
1. Adicione o código Mermaid em `architecture.md`
2. Crie um novo script em `generate-diagrams.sh`
3. Execute `make diagrams`

## 📝 Convenções

### Cores dos Componentes
- **Frontend**: Azul (#61dafb)
- **Backend**: Vermelho (#ff2d20)
- **Database**: Azul escuro (#336791)
- **Cache**: Vermelho (#dc382d)
- **Infraestrutura**: Verde (#009639)

### Estilo dos Diagramas
- Fundo transparente
- Cores consistentes
- Labels claros
- Fluxo lógico

## 🚀 Comandos Úteis

```bash
# Gerar apenas diagramas
make diagrams

# Gerar toda documentação
make docs

# Apenas Swagger
make swagger

# Ver documentação online
open http://localhost:8082/api/documentation
```

## 📋 Checklist de Documentação

- [x] Arquitetura geral documentada
- [x] Fluxos principais mapeados
- [x] Diagramas gerados automaticamente
- [x] Documentação integrada ao README
- [x] Scripts de automação criados
- [x] Convenções definidas
- [x] Exemplos incluídos

## 🤝 Contribuição

Para contribuir com a documentação:

1. **Modifique** os arquivos `.md` ou scripts
2. **Execute** `make diagrams` para atualizar
3. **Teste** se os diagramas estão corretos
4. **Commit** as mudanças
5. **Push** para o repositório

## 📞 Suporte

Se encontrar problemas com a documentação:

1. Verifique se o Mermaid CLI está instalado
2. Confirme se os scripts têm permissão de execução
3. Verifique se o diretório `docs/images/` existe
4. Consulte os logs de erro do script

---

**📚 Documentação sempre atualizada e sincronizada com o código!** 🚀
