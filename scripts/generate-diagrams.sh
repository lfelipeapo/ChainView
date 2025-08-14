#!/bin/bash

# Script para gerar imagens PNG dos diagramas Mermaid
# Requer: npm install -g @mermaid-js/mermaid-cli

echo "🎨 Gerando diagramas da arquitetura..."

# Criar diretório para imagens
mkdir -p docs/images

# Diagrama da Arquitetura Geral
echo "📊 Gerando diagrama da arquitetura geral..."
mmdc -i docs/architecture.md -o docs/images/architecture-general.png -b transparent

# Diagrama do Fluxo de Autenticação
echo "🔐 Gerando diagrama do fluxo de autenticação..."
cat > temp-auth.mmd << 'EOF'
sequenceDiagram
    participant U as Usuário
    participant F as Frontend
    participant A as API Laravel
    participant D as Database
    
    U->>F: Acessa aplicação
    F->>A: GET /auth/user
    A->>D: Verifica token
    D-->>A: Dados do usuário
    A-->>F: 401 Unauthorized
    F->>U: Mostra tela de login
    
    U->>F: Insere credenciais
    F->>A: POST /auth/login
    A->>D: Valida credenciais
    D-->>A: Usuário válido
    A-->>F: Token + dados do usuário
    F->>F: Armazena token no localStorage
    F->>U: Redireciona para dashboard
EOF

mmdc -i temp-auth.mmd -o docs/images/auth-flow.png -b transparent
rm temp-auth.mmd

# Diagrama do Fluxo da Árvore
echo "🌳 Gerando diagrama do fluxo da árvore..."
cat > temp-tree.mmd << 'EOF'
flowchart TD
    A[Usuário acessa dashboard] --> B[Frontend carrega]
    B --> C[React Query busca dados]
    C --> D[GET /areas/tree]
    D --> E[API Laravel]
    E --> F[AreaController@tree]
    F --> G[Area Model]
    G --> H[Database Query]
    H --> I[PostgreSQL]
    I --> J[Áreas + Processos]
    J --> K[AreaResource]
    K --> L[JSON Response]
    L --> M[Frontend recebe dados]
    M --> N[AreaTree Component]
    N --> O[Renderiza árvore]
    O --> P[Usuário vê estrutura]
    
    style A fill:#e1f5fe
    style E fill:#fff3e0
    style I fill:#e8f5e8
    style P fill:#f3e5f5
EOF

mmdc -i temp-tree.mmd -o docs/images/tree-flow.png -b transparent
rm temp-tree.mmd

# Diagrama dos Containers Docker
echo "🐳 Gerando diagrama dos containers Docker..."
cat > temp-docker.mmd << 'EOF'
graph LR
    subgraph "Docker Compose"
        subgraph "Container: doc-viewer"
            A[PHP 8.0]
            B[Laravel Framework]
            C[Nginx Web Server]
            D[Supervisor]
            E[Xdebug]
        end
        
        subgraph "Container: postgres"
            F[PostgreSQL 13]
            G[Database Data]
        end
        
        subgraph "Container: redis (futuro)"
            H[Redis Cache]
        end
    end
    
    subgraph "Host Machine"
        I[Port 8082:80]
        J[Port 5432:5432]
        K[Port 6379:6379]
    end
    
    A --> B
    B --> C
    C --> D
    A --> E
    
    I --> C
    J --> F
    K --> H
    
    style A fill:#ff5722
    style F fill:#2196f3
    style H fill:#f44336
EOF

mmdc -i temp-docker.mmd -o docs/images/docker-containers.png -b transparent
rm temp-docker.mmd

# Diagrama do Pipeline CI/CD
echo "🚀 Gerando diagrama do pipeline CI/CD..."
cat > temp-cicd.mmd << 'EOF'
graph TD
    A[Push para GitHub] --> B[GitHub Actions Trigger]
    B --> C[Backend Tests]
    B --> D[Frontend Tests]
    B --> E[Security Check]
    B --> F[Code Quality]
    
    C --> G[PHPUnit Tests]
    C --> H[Code Coverage]
    
    D --> I[ESLint]
    D --> J[Jest Tests]
    D --> K[Build Check]
    
    E --> L[Composer Audit]
    
    F --> M[PHPStan]
    F --> N[PHP CS Fixer]
    
    G --> O[Test Results]
    H --> P[Coverage Report]
    I --> Q[Lint Results]
    J --> R[Test Results]
    K --> S[Build Status]
    L --> T[Security Report]
    M --> U[Code Analysis]
    N --> V[Code Style]
    
    O --> W[All Tests Pass?]
    P --> W
    Q --> W
    R --> W
    S --> W
    T --> W
    U --> W
    V --> W
    
    W --> X{Success?}
    X -->|Yes| Y[Deploy to Staging]
    X -->|No| Z[Notify Failure]
    
    style A fill:#28a745
    style Y fill:#007bff
    style Z fill:#dc3545
EOF

mmdc -i temp-cicd.mmd -o docs/images/cicd-pipeline.png -b transparent
rm temp-cicd.mmd

echo "✅ Diagramas gerados com sucesso!"
echo "📁 Imagens salvas em: docs/images/"
echo ""
echo "📊 Diagramas criados:"
echo "  - architecture-general.png"
echo "  - auth-flow.png"
echo "  - tree-flow.png"
echo "  - docker-containers.png"
echo "  - cicd-pipeline.png"
