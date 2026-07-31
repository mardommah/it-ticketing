.PHONY: help setup deploy update migrate seed optimize cache-clear restart backup test lint nginx-setup nginx-reload

help:
	@echo "IT Ticketing System - Deployment Commands"
	@echo ""
	@echo "Setup & Deployment:"
	@echo "  make setup          - Initial setup for fresh VPS"
	@echo "  make deploy         - Full deployment (pull, install, migrate, optimize)"
	@echo "  make update         - Quick update (pull, install, migrate)"
	@echo ""
	@echo "Database:"
	@echo "  make migrate        - Run database migrations"
	@echo "  make seed           - Seed database with default admin"
	@echo "  make backup         - Backup database"
	@echo ""
	@echo "Nginx:"
	@echo "  make nginx-setup    - Setup Nginx config (Cloudflare SSL)"
	@echo "  make nginx-reload   - Reload Nginx after config changes"
	@echo ""
	@echo "Maintenance:"
	@echo "  make optimize       - Optimize for production (cache routes, config, views)"
	@echo "  make cache-clear    - Clear all caches"
	@echo "  make restart        - Restart services (php-fpm, nginx)"
	@echo ""
	@echo "Development:"
	@echo "  make test           - Run tests"
	@echo "  make lint           - Run Laravel Pint"

setup:
	@echo "🚀 Initial VPS Setup..."
	composer install --no-dev --optimize-autoloader
	cp .env.example .env
	@echo "⚠️  IMPORTANT: Edit .env file with production values (DB, APP_KEY, etc.)"
	@read -p "Press enter after editing .env..."
	php artisan key:generate --force
	php artisan storage:link
	php artisan migrate --force
	php artisan db:seed --force
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	chmod -R 775 storage bootstrap/cache
	chown -R www-data:www-data storage bootstrap/cache
	@echo "✅ Setup complete! Don't forget to configure nginx/apache."

deploy:
	@echo "🚀 Deploying to production..."
	php artisan down --retry=60
	git pull origin main
	composer install --no-dev --optimize-autoloader --no-interaction
	npm ci --production
	npm run build
	php artisan migrate --force
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan queue:restart
	php artisan up
	@echo "✅ Deployment complete!"

update:
	@echo "📦 Updating application..."
	php artisan down --retry=60
	git pull origin main
	composer install --no-dev --optimize-autoloader --no-interaction
	php artisan migrate --force
	php artisan cache:clear
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan up
	@echo "✅ Update complete!"

migrate:
	php artisan migrate --force

seed:
	php artisan db:seed --force

optimize:
	@echo "⚡ Optimizing for production..."
	composer install --no-dev --optimize-autoloader --classmap-authoritative
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan event:cache
	@echo "✅ Optimization complete!"

cache-clear:
	@echo "🧹 Clearing all caches..."
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear
	@echo "✅ Caches cleared!"

restart:
	@echo "🔄 Restarting services..."
	@if command -v systemctl > /dev/null; then \
		sudo systemctl restart php8.2-fpm; \
		sudo systemctl restart nginx; \
		echo "✅ Services restarted (systemd)"; \
	else \
		sudo service php8.2-fpm restart; \
		sudo service nginx restart; \
		echo "✅ Services restarted (sysvinit)"; \
	fi

backup:
	@echo "💾 Backing up database..."
	@mkdir -p backups
	@php artisan db:backup backups/backup-$(shell date +%Y%m%d-%H%M%S).sql
	@echo "✅ Backup saved to backups/"

test:
	php artisan test

lint:
	./vendor/bin/pint

nginx-setup:
	@echo "🔧 Setting up Nginx config..."
	@echo "⚠️  Make sure you've edited docker/nginx/cloudflare.conf (domain + SSL paths)"
	@read -p "Press enter to continue..."
	sudo cp docker/nginx/cloudflare.conf /etc/nginx/sites-available/ticketing
	sudo ln -sf /etc/nginx/sites-available/ticketing /etc/nginx/sites-enabled/
	sudo nginx -t
	@echo "✅ Nginx config ready. Run 'make nginx-reload' to apply."

nginx-reload:
	@echo "🔄 Reloading Nginx..."
	sudo nginx -t && sudo systemctl reload nginx
	@echo "✅ Nginx reloaded!"
