TAG=$(shell git log -1 --format=%h)

# Setup completo do projeto
setup:
	@echo "🚀 Iniciando setup completo do ChainView..."
	@echo "📦 Subindo containers..."
	docker-compose up -d
	@echo "⏳ Aguardando containers ficarem prontos..."
	sleep 10
	@echo "🗄️ Executando migrations e seeders..."
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan migrate:fresh --seed"
	@echo "📚 Gerando documentação Swagger..."
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan l5-swagger:generate"
	@echo "✅ Setup completo! Acesse:"
	@echo "   🌐 Frontend: http://localhost:3000"
	@echo "   🔧 Backend: http://localhost:8082"
	@echo "   📖 Swagger: http://localhost:8082/api/documentation"

# Comandos individuais
up:
	docker-compose up -d

seed:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan migrate:fresh --seed"

swagger:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan l5-swagger:generate"

# Testes
test:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan test"

test-coverage:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage --coverage-text"

test-feature:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan test --testsuite=Feature"

test-unit:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && php artisan test --testsuite=Unit"

lint:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && ./vendor/bin/php-cs-fixer fix --dry-run --diff"
	cd frontend && npm run lint

lint-fix:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && ./vendor/bin/php-cs-fixer fix"
	cd frontend && npm run lint:fix

security:
	docker exec -it doc-viewer bash -c "cd /var/www/doc-viewer && composer audit"

# Documentação
diagrams:
	./scripts/generate-diagrams.sh

docs: diagrams swagger

build:
	docker build -t php-nginx-web ./docker/
login:
	docker login
tag: login
	docker tag php-nginx-web lfelipeapo/php-nginx-web:$(TAG)
push: tag
	docker push lfelipeapo/php-nginx-web:$(TAG)
