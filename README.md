# Messenger-Test-App

A Symfony 8.0 web application for testing messaging functionality, including user registration, authentication, and messaging between users.

## Requirements

- PHP >= 8.4
- Composer
- A database supported by Doctrine (e.g. MySQL, PostgreSQL, SQLite)

## Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/123ABC7890/Messenger-Test-App.git
   cd Messenger-Test-App
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

4. **Run migrations**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

5. **(Optional) Load fixtures**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Running the App

```bash
symfony server:start
```

Or with PHP's built-in server:

```bash
php -S localhost:8000 -t public/
```
