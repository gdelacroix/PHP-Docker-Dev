# 🚀 TP – Développer en PHP dans un conteneur Docker avec base de données et mailer

## 🎯 Objectifs pédagogiques

- Créer un environnement de développement PHP avec Docker
- Utiliser PDO pour se connecter à une base de données MySQL
- Envoyer des emails avec PHPMailer
- Travailler dans Visual Studio Code avec **Dev Containers**
- Importer une base de données automatiquement au lancement
- Visualiser les e-mails envoyés avec Maildev

## 🔧 Prérequis

- Avoir **Docker** installé
- Avoir **Visual Studio Code**
- Installer l'extension **Dev Containers** dans VS Code
- Connaître les bases de PHP et SQL

## 📁 Structure du projet

```
php-docker-dev/
├── .devcontainer/
│   └── devcontainer.json
├── mysql-init/
│   └── init.sql
├── src/
│   ├── index.php
│   ├── db.php
│   └── mailer.php
├── composer.json
├── Dockerfile
└── docker-compose.yml
```

## 📦 Étape 1 – Créer le fichier `docker-compose.yml`

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8000:80"
    volumes:
      - ./src:/var/www/html
    depends_on:
      - db
      - maildev

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: testdb
    volumes:
      - ./mysql-init:/docker-entrypoint-initdb.d
    ports:
      - "3306:3306"

  maildev:
    image: maildev/maildev
    ports:
      - "1080:1080"
      - "1025:1025"
```

## 🐘 Étape 2 – Créer le `Dockerfile`

```dockerfile
FROM php:8.2.10-apache-bullseye

RUN apt-get update && apt-get install -y --no-install-recommends     git     unzip     zip     libpq-dev     libzip-dev     && docker-php-ext-install zip pdo pdo_mysql     && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
```

## 🐳 Étape 3 – Configurer Dev Containers

`.devcontainer/devcontainer.json`

```json
{
    "name": "PHP DevContainer",
    "dockerComposeFile": "../docker-compose.yml",
    "service": "web",
    "workspaceFolder": "/var/www/html",
    "customizations": {
        "vscode": {
            "extensions": [
                "bmewburn.vscode-intelephense-client"
            ]
        }
    },
    "postCreateCommand": "composer install"
  }
```

## 📦 Étape 4 – Initialiser Composer

`composer.json`

```json
{
  "require": {
    "phpmailer/phpmailer": "^6.9"
  }
}
```

## 🗃️ Étape 5 – Script SQL : `mysql-init/init.sql`

```sql
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO users (name, email) VALUES
('Alice', 'alice@example.com'),
('Bob', 'bob@example.com');
```

## 🧠 Étape 6 – Fichiers PHP dans `src/`

### `index.php`

```php
<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo $user['name'] . " - " . $user['email'] . "<br>";
}
?>
```

### `db.php`

```php
<?php
$tries = 10;
do {
    try {
        $pdo = new PDO("mysql:host=db;dbname=testdb", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break;
    } catch (PDOException $e) {
        echo "En attente de MySQL...<br>";
        sleep(2);
    }
} while (--$tries);
?>
```

### `mailer.php`

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'maildev';
    $mail->SMTPAuth   = false;
    $mail->Port       = 1025;

    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('recipient@example.com');

    $mail->Subject = 'Test via Maildev';
    $mail->Body    = 'Ceci est le contenu de l’email via Maildev.';

    $mail->send();
    echo 'Email envoyé !';
} catch (Exception $e) {
    echo "Erreur lors de l'envoi : {$mail->ErrorInfo}";
}
?>
```

## ▶️ Étape 7 – Lancer le projet dans VS Code

1. Ouvre le dossier `php-docker-dev` dans Visual Studio Code
2. Clique en bas à gauche sur `><` puis **"Reopen in Container"**
3. Une fois dans le conteneur :
   ```bash
   composer install
   ```

## ✅ Étape 8 – Tester le projet

1. Accède à `http://localhost:8000` → liste des utilisateurs
2. Accède à `http://localhost:8000/mailer.php` → test d’envoi d’e-mail
3. Accède à `http://localhost:1080` → visualiser les e-mails avec Maildev
