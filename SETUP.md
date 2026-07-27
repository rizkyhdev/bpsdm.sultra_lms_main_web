# 🚀 System Setup Guide

Welcome to the Sobat ASR LMS platform! This guide covers the essential steps required to get your application fully functional **after** you have run `docker compose` for the first time. 

---

## 🛠️ Development Environment

If you have just started the development environment using:
```bash
make dev-up
# or: docker compose up -d
```

Follow these steps to initialize your system:

### 1. Install PHP & Node Dependencies (First time only)
Since the development image doesn't pre-install the vendor directory (it uses a volume mount), you must install dependencies inside the container:
```bash
docker compose exec app composer install
docker compose exec app npm install
```

### 2. Run Database Migrations
Create all the necessary tables in your PostgreSQL database:
```bash
make migrate
# or: docker compose exec app php artisan migrate
```

### 3. Generate Application Key (If missing)
If your `.env` file doesn't have an `APP_KEY`, generate one now:
```bash
docker compose exec app php artisan key:generate
```

### 4. Build Frontend Assets
Compile your Vite assets for development:
```bash
docker compose exec app npm run build
# Or run the dev server: docker compose exec app npm run dev
```

### 5. Storage Link
Create the symbolic link to make local storage files publicly accessible:
```bash
docker compose exec app php artisan storage:link
```

---

## 🌍 Production Environment

If you have just deployed the production environment using:
```bash
make prod-up
# or: docker compose -f docker-compose.prod.yml up -d
```

Production images are pre-built with dependencies and frontend assets, so the setup is much simpler. Follow these steps:

### 1. Run Database Migrations
In production, you must use the `--force` flag. We have a convenient make command for this:
```bash
make prod-migrate
# or: docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### 2. Run Seeders (Optional)
If you need to populate the database with default roles, permissions, or a default admin user, run the seeders:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force
```

### 3. Storage Link (First time only)
Ensure uploaded files are accessible to the public web server:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### 4. Cache Configuration & Routes (Recommended)
Since we removed config caching from the Docker build step to allow dynamic `.env` variables, you should cache them at runtime for maximum performance:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

---

## 🔧 Useful Commands (Makefile)

We've bundled common Docker commands into the `Makefile` for convenience. If your Linux server supports `make`, you can use these shortcuts:

- `make dev-up` : Starts development containers.
- `make bash` : Opens a bash shell inside the development app container.
- `make migrate` : Runs migrations in development.
- `make prod-build` : Rebuilds the production Docker image.
- `make prod-up` : Starts production containers.
- `make prod-bash` : Opens a bash shell inside the production app container.
- `make prod-migrate` : Runs forced migrations in production.

---

## ⚠️ Troubleshooting

**1. "500 Internal Server Error" / "No application encryption key has been specified."**
This means your `APP_KEY` is missing. Ensure your `docker-compose.prod.yml` passes the `APP_KEY` environment variable, or that your local `.env` has it set.

**2. Assets / Images are 404 Not Found**
Ensure you have run `php artisan storage:link` (Step 5 above) so that Nginx can serve files from the `storage/app/public` directory.

**3. "File not found" Error on Root URL**
This happens if you run production without building the image first. Always run `make prod-build` (or `docker compose -f docker-compose.prod.yml build`) before `prod-up` when deploying for the first time or after changing dependencies.
