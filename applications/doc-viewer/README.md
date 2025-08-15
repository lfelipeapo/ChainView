# ChainView Backend (Laravel API)

## 📋 Descrição

Backend do sistema ChainView desenvolvido em Laravel 8 com API REST para gestão de processos hierárquicos.

## 🚀 Tecnologias

- **Laravel 8**: Framework PHP robusto
- **PostgreSQL**: Banco de dados relacional
- **Docker**: Containerização
- **Nginx**: Servidor web
- **PHP-FPM**: Process Manager

## 🏗️ Estrutura

```
applications/doc-viewer/
├── app/
│   ├── Http/Controllers/    # Controllers da API
│   │   ├── AreaController.php
│   │   ├── ProcessController.php
│   │   └── DocumentController.php
│   ├── Models/             # Modelos Eloquent
│   │   ├── Area.php
│   │   └── Process.php
│   └── Http/Middleware/    # Middlewares
├── database/
│   ├── migrations/         # Migrações
│   └── seeders/           # Seeders
├── routes/
│   └── api.php            # Rotas da API
└── config/                # Configurações
```

## 🚀 Como Executar

### Com Docker
```bash
# Iniciar containers
docker-compose up -d

# Executar migrações
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan migrate"

# Executar seeders
docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan db:seed"

# Verificar status
curl http://localhost/api/health
```

### Localmente
```bash
# Instalar dependências
composer install

# Configurar .env
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Executar migrações
php artisan migrate

# Executar seeders
php artisan db:seed

# Iniciar servidor
php artisan serve
```

## 📡 Endpoints da API

### Áreas
```http
GET    /api/areas          # Listar todas as áreas
GET    /api/areas/tree     # Listar áreas em formato de árvore
POST   /api/areas          # Criar nova área
PUT    /api/areas/{id}     # Atualizar área
DELETE /api/areas/{id}     # Remover área
```

### Processos
```http
GET    /api/processes              # Listar todos os processos
POST   /api/processes              # Criar novo processo
PUT    /api/processes/{id}         # Atualizar processo
DELETE /api/processes/{id}         # Remover processo
GET    /api/processes/{id}/tree    # Obter árvore de um processo
```

### Health Check
```http
GET    /api/health         # Status do sistema
```

## 📊 Modelos de Dados

### Area Model
```php
class Area extends Model
{
    protected $fillable = ['name'];
    
    public function processes()
    {
        return $this->hasMany(Process::class);
    }
}
```

### Process Model
```php
class Process extends Model
{
    protected $fillable = [
        'name', 'description', 'area_id', 'parent_id',
        'type', 'criticality', 'status',
        'tools', 'responsible', 'documentation'
    ];
    
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(Process::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Process::class, 'parent_id');
    }
}
```

## 🗄️ Estrutura do Banco

### Tabela `areas`
```sql
CREATE TABLE areas (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Tabela `processes`
```sql
CREATE TABLE processes (
    id BIGSERIAL PRIMARY KEY,
    area_id BIGINT REFERENCES areas(id),
    parent_id BIGINT REFERENCES processes(id),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50),
    criticality VARCHAR(50),
    status VARCHAR(50),
    tools TEXT,
    responsible VARCHAR(255),
    documentation TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## 🔧 Configurações

### .env
```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=chainview
DB_USERNAME=postgres
DB_PASSWORD=password

APP_DEBUG=true
APP_ENV=local
```

### Docker
```yaml
services:
  doc-viewer:
    image: lfelipeapo/php-nginx-web:1.0.0
    ports:
      - "80:8000"
    volumes:
      - ./applications/doc-viewer:/var/www/doc-viewer
    depends_on:
      - postgres
```

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Testes específicos
php artisan test --filter=AreaControllerTest
php artisan test --filter=ProcessControllerTest
```

## 📈 Funcionalidades

### 1. **Hierarquia Ilimitada**
- Subprocessos em níveis ilimitados
- Relacionamento parent-child
- Construção de árvores

### 2. **CRUD Completo**
- Criação, leitura, atualização e remoção
- Validação de dados
- Respostas JSON padronizadas

### 3. **Health Check**
- Status do sistema
- Estatísticas do banco
- Informações de identificação

### 4. **UTF-8 Support**
- Configuração para caracteres especiais
- Middleware para charset UTF-8
- Respostas com encoding correto

## 🔒 Segurança

- **CORS**: Configurado para frontend
- **Validação**: Validação de entrada
- **Sanitização**: Limpeza de dados
- **Headers**: Headers de segurança

## 📊 Performance

- **Eloquent**: ORM otimizado
- **Queries**: Queries eficientes
- **Indexes**: Índices no banco
- **Caching**: Cache configurado

## 🚀 Deploy

### Docker
```bash
# Build da imagem
docker build -t chainview-backend .

# Executar container
docker run -p 80:8000 chainview-backend
```

### Produção
```bash
# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar supervisor
# Configurar nginx
# Configurar SSL
```

## 🔧 Desenvolvimento

### Comandos Úteis
```bash
# Criar migration
php artisan make:migration create_processes_table

# Criar controller
php artisan make:controller ProcessController --api

# Criar model
php artisan make:model Process

# Criar seeder
php artisan make:seeder ProcessSeeder

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Debug
```bash
# Logs
tail -f storage/logs/laravel.log

# Tinker
php artisan tinker

# Queue
php artisan queue:work
```

---

**ChainView Backend** - API robusta e escalável para gestão de processos. 🚀
