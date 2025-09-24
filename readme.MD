# Mytheresa Symfony Project

This project is a Symfony-based application for **Mytheresa**.
It uses **PHP 8.3**, **Symfony Framework**, and **MySQL 8.0**.
The app is containerized with **Docker** for easier local development.

---

## 🚀 Requirements

Before starting, ensure you have:

- PHP >= 8.3 (8.3 recommended)
- Composer
- MySQL >= 8.0
- Symfony CLI (optional for manual setup)
- Docker & Docker Compose (if using container setup)

---

## ⚙️ Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/your-username/mytheresa.git
```
```bash
cd mytheresa && composer install
```
### Configure environment variables
Copy the example .env file and adjust as needed:
```bash
cp .env.example .env
```
update the credentials
```bash
# MYSQL ENVIRONMENT
MYSQL_ROOT_PASSWORD=root_pass
MYSQL_DATABASE=mytheresa
MYSQL_USER=mytheresa_user
MYSQL_PASSWORD=mytheresa_pass
MYSQL_HOST=database
MYSQL_PORT=3306
MYSQL_SERVER_VERSION=8.0.32
MYSQL_CHARSET=utf8mb4

# SYMFONY ENVIRONMENT
APP_ENV=dev
APP_SECRET=your_symfony_secret_key
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
All the migration and fixtures loader already put into `entrypoint.sh` file when `APP_ENV=dev`

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
