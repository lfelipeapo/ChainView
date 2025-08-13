# Doc Viewer

Aplicação Laravel para visualização e categorização de documentos. O projeto roda em contêineres Docker e utiliza PostgreSQL como banco de dados.

## Arquitetura

- **doc-viewer**: contêiner com PHP 8 e Nginx onde a aplicação Laravel é executada.
- **postgres**: banco de dados PostgreSQL 14.
- O código da aplicação está em `applications/doc-viewer` e inclui a camada de front-end construída com Laravel Mix/Tailwind.

## Setup

1. Inicialize os contêineres:
   ```bash
   sh script-start-docker-compose.sh
   ```
2. Acesse o contêiner e execute as migrações com seeds:
   ```bash
   docker exec -it doc-viewer bash
   npm run migrate
   ```
3. Inicie o ambiente de desenvolvimento front-end:
   ```bash
   npm start
   ```
4. A aplicação estará disponível em `http://localhost`.

## Uso da API

- `GET /api/user` – retorna o usuário autenticado (requer token Sanctum).
- `GET /categories` – lista as categorias cadastradas (rota autenticada).
- `GET /categories/{id}` – exibe detalhes de uma categoria e seus atributos.

As rotas protegidas exigem autenticação prévia via sistema padrão do Laravel.
