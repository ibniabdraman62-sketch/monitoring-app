<div align="center">

# MonitorPro
### Système Intelligent de Monitoring des Sites Web avec Alertes Automatiques

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)](LICENSE)

> **Projet de Fin d'Etudes — Licence Informatique IRM**
> Faculte des Sciences et Techniques de Mohammedia (FSTM)
> Universite Hassan II de Casablanca — 2026

</div>

---

## Presentation

**MonitorPro** est une application web developpee dans le cadre d'un stage de fin d'etudes chez **Soft Seven Art**, agence digitale basee a Casablanca. Elle permet de surveiller en temps reel la disponibilite, le temps de reponse et la validite SSL des sites web clients, avec envoi automatique d'alertes par email en cas d'incident.

### Objectifs
- Surveiller automatiquement la disponibilite (uptime) des sites web
- Mesurer les temps de reponse et detecter les ralentissements
- Verifier la validite des certificats SSL
- Envoyer des alertes email automatiques en cas d'incident
- Generer des rapports PDF hebdomadaires par client

---

## Fonctionnalites

| Fonctionnalite | Description |
|---|---|
| Dashboard temps reel | KPIs, graphiques Chart.js, donut uptime, auto-refresh 30s |
| Surveillance HTTP/HTTPS | Verification automatique toutes les 5 minutes |
| Verification SSL | Validite et date d'expiration des certificats |
| Check Now | Verification instantanee a la demande |
| Alertes automatiques | Email lors de panne, lenteur ou resolution |
| Historique uptime | Barre de 30 dernieres verifications par site |
| Gestion incidents | Timeline complete des incidents avec duree |
| Rapports PDF | Generation et telechargement de rapports hebdomadaires |
| Authentification | Login securise avec gestion de profil |

---

## Stack Technique

### Backend
| Technologie | Version | Role |
|---|---|---|
| Laravel | 10.x | Framework PHP MVC |
| PHP | 8.3 | Langage de programmation |
| MySQL | 8.4 | Base de donnees relationnelle |
| Guzzle HTTP | 7.x | Client HTTP pour le monitoring |
| DomPDF | 3.1 | Generation de rapports PDF |
| Laravel Breeze | 1.x | Authentification |
| Laravel Scheduler | — | Automatisation des verifications |

### Frontend
| Technologie | Version | Role |
|---|---|---|
| Blade | — | Moteur de templates Laravel |
| Tailwind CSS | 3.x | Framework CSS |
| Chart.js | Latest | Graphiques et visualisations |
| Font Awesome | 6.5 | Icones |

### Services
| Service | Role |
|---|---|
| Mailtrap | Sandbox SMTP pour les tests |
| Gmail / Mailgun | SMTP production |
| Laragon | Environnement de developpement local |

---

## Installation

### Prerequis
- [Laragon](https://laragon.org) (Apache + MySQL + PHP 8.x)
- [Composer](https://getcomposer.org) 2.x
- [Node.js](https://nodejs.org) 18+ et npm

### Etapes d'installation

```bash
# 1. Cloner le depot
git clone https://github.com/ibniabdraman62-sketch/monitoring-app.git
cd monitoring-app

# 2. Installer les dependances PHP
composer install

# 3. Installer les dependances JavaScript et compiler
npm install && npm run build

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de donnees dans .env
# DB_DATABASE=monitoring_db
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Creer la base de donnees "monitoring_db" dans MySQL
# Puis executer les migrations
php artisan migrate

# 7. Creer le compte administrateur
php artisan db:seed --class=UserSeeder
```

### Configuration Email

**Mode developpement (Mailtrap) :**
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

## Lancement

Ouvrir **deux terminaux** dans le dossier du projet :

```bash
# Terminal 1 — Serveur de developpement
php artisan serve
# Application disponible sur http://127.0.0.1:8000

# Terminal 2 — Scheduler automatique (monitoring toutes les minutes)
php artisan schedule:work
```

> Le Terminal 2 doit rester ouvert en permanence pour que le monitoring automatique fonctionne.

---

## Acces

Les credentials sont configures dans `database/seeders/UserSeeder.php`.

```bash
# Pour creer ou reinitialiser le compte admin :
php artisan db:seed --class=UserSeeder
```

> Pour des raisons de securite, les credentials ne sont pas affiches dans ce README.
> Consulter le fichier `UserSeeder.php` pour les modifier avant le deploiement.

---

## Structure de la base de donnees

```
monitoring_db
├── users              → Administrateurs du systeme
├── sites              → Sites web a surveiller
├── verifications      → Resultats de chaque verification
├── incidents          → Pannes et lenteurs detectees
├── alertes            → Emails d'alertes envoyes
├── rapports           → Rapports PDF generes
└── ...                → Tables systeme Laravel
```

---

## Structure du projet

```
monitoring-app/
├── app/
│   ├── Http/Controllers/    → DashboardController, SiteController, RapportController...
│   ├── Models/              → User, Site, Verification, Incident, Alerte, Rapport
│   ├── Services/            → MonitoringService (logique de verification)
│   ├── Jobs/                → CheckSiteJob (tache asynchrone)
│   └── Console/             → Kernel.php (configuration du scheduler)
├── resources/views/         → Vues Blade (dashboard, sites, rapports, incidents)
├── database/
│   ├── migrations/          → 9 migrations pour toutes les tables
│   └── seeders/             → UserSeeder, DemoSeeder
└── routes/web.php           → Definition de toutes les routes
```

---

## Architecture du monitoring

```
Scheduler (chaque minute)
    └── CheckSiteJob (par site actif)
            └── MonitoringService::checkSite()
                    ├── Guzzle HTTP → GET request → http_code + response_time
                    ├── OpenSSL    → Verification SSL + date expiration
                    ├── MySQL      → INSERT verifications
                    └── Si incident detecte
                            ├── MySQL        → INSERT incidents
                            └── AlerteService → Mail::raw() → SMTP
```

---

## Commandes utiles

```bash
# Verifier tous les sites manuellement
php artisan tinker
>>> App\Models\Site::all()->each(fn($s) => (new App\Services\MonitoringService())->checkSite($s));

# Lister toutes les routes
php artisan route:list

# Vider les caches
php artisan config:clear && php artisan cache:clear

# Reinitialiser la base de donnees
php artisan migrate:fresh --seed
```

---

## Auteurs

<div align="center">

**ABDRAMAN IBNI ABDRAMAN**

Etudiant en Licence Informatique — Specialite IRM

Faculte des Sciences et Techniques de Mohammedia (FSTM)

ibniabdraman62@gmail.com

Role : Developpement Frontend et Integration systeme

---

**ACHRAF MABROUK**

Etudiant en Licence Informatique — Specialite IRM

Faculte des Sciences et Techniques de Mohammedia (FSTM)

Role : Developpement Backend et Base de donnees

---

Stage effectue chez **Soft Seven Art** — Casablanca, Maroc

**Encadrant academique :** Pr. Noureddine MOUMKINE — FSTM

noureddine.moumkine@fstm.ac.ma

Universite Hassan II de Casablanca

</div>

---

<div align="center">

Projet realise dans le cadre du stage de fin d'etudes — Annee universitaire 2025-2026

</div>