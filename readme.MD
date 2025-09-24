# Mytheresa Symfony Project

This project is a Symfony-based application for **Mytheresa**.
It uses **PHP 8.3**, **Symfony Framework**, and **MySQL 8.0**.
The app is containerized with **Docker** for easier local development.

---

## 🚀 Requirements

Before starting, ensure you have:

- PHP >= 8.1 (8.3 recommended)
- Composer
- MySQL >= 8.0
- Symfony CLI (optional, but recommended)
- Docker & Docker Compose (if using container setup)

---

## ⚙️ Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/your-username/mytheresa.git
cd mytheresa
```
### Configure environment variables
Copy the example .env file and adjust as needed:
```bash
cp .env .env.example
```
update the credentials
```bash
APP_ENV=dev
APP_SECRET=your_app_secret_here

DB_USER=mytheresa
DB_PASSWORD=sapi123
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=mytheresa
DB_SERVER_VERSION=8.0.32
DB_CHARSET=utf8mb4
```

### 2. 🐘 Manual Setup (without Docker)
#### 1. Install dependencies:
```bash
composer install
```
#### 2. Create the database:
```bash
php bin/console doctrine:database:create
```
#### 3. Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```
#### 4. (Optional) Load fixtures:
```bash
php bin/console doctrine:fixtures:load
```
#### 5. Start Symfony server:
```bash
symfony serve:start
```
#### 6. Access the app:
👉 http://127.0.0.1:8000

### 3. 🐳 Run with Docker Compose
#### 1. Run the docker compose command
```bash
docker-compose up --build -d
```
All the migration and fixtures loader already put into `entrypoint.sh` file

#### 2. Access the app:
👉 http://127.0.0.1:8000

### 4. 🧪 Running Tests
```bash
# Inside PHP container
docker compose exec php ./vendor/bin/phpunit

# Or manually
./vendor/bin/phpunit

# With readable report
./vendor/bin/phpunit --testdox
```

---

## 📝 Notes
`entrypoint.sh` handles waiting for DB, clearing cache, running migrations, and loading fixtures in non-prod environments.

In production, set `APP_ENV=prod` and avoid running fixtures.

To connect to MySQL from your host:
```
mysql -h127.0.0.1 -P3308 -umytheresa -psapi123 mytheresa
```

---

## 📂 Project Structure
```
mytheresa/
├── bin/              # Symfony console
├── config/           # Symfony configuration
├── docker/           # Docker-related files
├── migrations/       # Doctrine migrations
├── public/           # Web entry point
├── src/              # PHP source code
├── tests/            # PHPUnit tests
├── var/              # Cache & logs
├── vendor/           # Composer dependencies
└── docker-compose.yml
```
