<div align="center">

# 📡 MonitorPro
### Système Intelligent de Monitoring des Sites Web avec Alertes Automatiques

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)](LICENSE)

> **Projet de Fin d'Études — Licence Informatique IRM**  
> Faculté des Sciences et Techniques de Mohammedia (FSTM)  
> Université Hassan II de Casablanca — 2026

</div>

---

## 📌 Présentation

**MonitorPro** est une application web développée dans le cadre d'un stage de fin d'études chez **Soft Seven Art**, agence digitale basée à Casablanca. Elle permet de surveiller en temps réel la disponibilité, le temps de réponse et la validité SSL des sites web clients, avec envoi automatique d'alertes par email en cas d'incident.

### 🎯 Objectifs
- Surveiller automatiquement la disponibilité (uptime) des sites web
- Mesurer les temps de réponse et détecter les ralentissements
- Vérifier la validité des certificats SSL
- Envoyer des alertes email automatiques en cas d'incident
- Générer des rapports PDF hebdomadaires par client

---

## ✨ Fonctionnalités

| Fonctionnalité | Description |
|---|---|
| 📊 **Dashboard temps réel** | KPIs, graphiques Chart.js, donut uptime, auto-refresh 30s |
| 🌐 **Surveillance HTTP/HTTPS** | Vérification automatique toutes les 5 minutes |
| 🔒 **Vérification SSL** | Validité et date d'expiration des certificats |
| ⚡ **Check Now** | Vérification instantanée à la demande |
| 📧 **Alertes automatiques** | Email lors de panne, lenteur ou résolution |
| 📈 **Historique uptime** | Barre de 30 dernières vérifications par site |
| ⚠️ **Gestion incidents** | Timeline complète des incidents avec durée |
| 📄 **Rapports PDF** | Génération et téléchargement de rapports hebdomadaires |
| 👤 **Authentification** | Login sécurisé avec gestion de profil |

---

## 🛠️ Stack Technique

### Backend
| Technologie | Version | Rôle |
|---|---|---|
| **Laravel** | 10.x | Framework PHP MVC |
| **PHP** | 8.3 | Langage de programmation |
| **MySQL** | 8.4 | Base de données relationnelle |
| **Guzzle HTTP** | 7.x | Client HTTP pour le monitoring |
| **DomPDF** | 3.1 | Génération de rapports PDF |
| **Laravel Breeze** | 1.x | Authentification |
| **Laravel Scheduler** | — | Automatisation des vérifications |

### Frontend
| Technologie | Version | Rôle |
|---|---|---|
| **Blade** | — | Moteur de templates Laravel |
| **Tailwind CSS** | 3.x | Framework CSS |
| **Chart.js** | Latest | Graphiques et visualisations |
| **Font Awesome** | 6.5 | Icônes |

### Services
| Service | Rôle |
|---|---|
| **Mailtrap** | Sandbox SMTP pour les tests |
| **Gmail / Mailgun** | SMTP production |
| **Laragon** | Environnement de développement local |

---

## 🚀 Installation

### Prérequis
- [Laragon](https://laragon.org) (Apache + MySQL + PHP 8.x)
- [Composer](https://getcomposer.org) 2.x
- [Node.js](https://nodejs.org) 18+ et npm

### Étapes d'installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/ibniabdraman62-sketch/monitoring-app.git
cd monitoring-app

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JavaScript et compiler
npm install && npm run build

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_DATABASE=monitoring_db
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Créer la base de données "monitoring_db" dans MySQL
# Puis exécuter les migrations
php artisan migrate

# 7. Créer le compte administrateur
php artisan db:seed --class=UserSeeder
```

### Configuration Email

**Mode développement (Mailtrap) :**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="monitoring@softseven.ma"
MAIL_FROM_NAME="Monitoring System"
```

**Mode production (Gmail) :**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=votre_app_password_google
MAIL_ENCRYPTION=tls
```

---

## ▶️ Lancement

Ouvrir **deux terminaux** dans le dossier du projet :

```bash
# Terminal 1 — Serveur de développement
php artisan serve
# Application disponible sur http://127.0.0.1:8000

# Terminal 2 — Scheduler automatique (monitoring toutes les minutes)
php artisan schedule:work
```

> ⚠️ Le Terminal 2 doit rester ouvert en permanence pour que le monitoring automatique fonctionne.

---

## 🔐 Accès

Les credentials sont configurés dans `database/seeders/UserSeeder.php`.

```bash
# Pour créer ou réinitialiser le compte admin :
php artisan db:seed --class=UserSeeder
```

> 🔒 Pour des raisons de sécurité, les credentials ne sont pas affichés dans ce README.  
> Consulter le fichier `UserSeeder.php` pour les modifier avant le déploiement.

---

## 🗄️ Structure de la base de données

```
monitoring_db
├── users              → Administrateurs du système
├── sites              → Sites web à surveiller
├── verifications      → Résultats de chaque vérification
├── incidents          → Pannes et lenteurs détectées
├── alertes            → Emails d'alertes envoyés
├── rapports           → Rapports PDF générés
└── ...                → Tables système Laravel
```

---

## 📁 Structure du projet

```
monitoring-app/
├── app/
│   ├── Http/Controllers/    → DashboardController, SiteController, RapportController...
│   ├── Models/              → User, Site, Verification, Incident, Alerte, Rapport
│   ├── Services/            → MonitoringService (logique de vérification)
│   ├── Jobs/                → CheckSiteJob (tâche asynchrone)
│   └── Console/             → Kernel.php (configuration du scheduler)
├── resources/views/         → Vues Blade (dashboard, sites, rapports, incidents)
├── database/
│   ├── migrations/          → 9 migrations pour toutes les tables
│   └── seeders/             → UserSeeder, DemoSeeder
└── routes/web.php           → Définition de toutes les routes
```

---

## 🔄 Architecture du monitoring

```
Scheduler (chaque minute)
    └── CheckSiteJob (par site actif)
            └── MonitoringService::checkSite()
                    ├── Guzzle HTTP → GET request → http_code + response_time
                    ├── OpenSSL → Vérification SSL + date expiration
                    ├── MySQL → INSERT verifications
                    └── Si incident détecté
                            ├── MySQL → INSERT incidents
                            └── AlerteService → Mail::raw() → SMTP
```

---

## 📊 Commandes utiles

```bash
# Vérifier tous les sites manuellement
php artisan tinker
>>> App\Models\Site::all()->each(fn($s) => (new App\Services\MonitoringService())->checkSite($s));

# Lister toutes les routes
php artisan route:list

# Vider les caches
php artisan config:clear && php artisan cache:clear

# Réinitialiser la base de données
php artisan migrate:fresh --seed
```

---
## 👨‍💻 Auteurs

<div align="center">

### 👤 ABDRAMAN IBNI Abdraman
Étudiant en Licence Informatique — Spécialité IRM
Faculté des Sciences et Techniques de Mohammedia (FSTM)
📧 ibniabdraman62@gmail.com
**Rôle : Développement Frontend et Intégration système**

---

### 👤 ACHRAF MABROUK
Étudiant en Licence Informatique — Spécialité IRM
Faculté des Sciences et Techniques de Mohammedia (FSTM)
**Rôle : Développement Backend et Base de données**

---

🏢 Stage effectué chez **Soft Seven Art** — Casablanca, Maroc

**Encadrant académique :** Prof. Abdellah ADIB — FSTM

Université Hassan II de Casablanca

</div>