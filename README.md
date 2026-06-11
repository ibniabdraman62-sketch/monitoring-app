<div align="center">

<img src="https://img.shields.io/badge/MonitorPro-Système%20de%20Monitoring%20Web-1E3A5F?style=for-the-badge" alt="MonitorPro"/>

# 🖥️ MonitorPro
### Système Intelligent de Monitoring des Sites Web avec Alertes Automatiques et IA

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-D4A857?style=for-the-badge)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-059669?style=for-the-badge)](https://github.com/ibniabdraman62-sketch/monitoring-app)

> 🎓 **Projet de Fin d'Études — Licence Sciences et Techniques**
> Spécialité : Informatique, Réseaux et Multimédia (IRM)
> Faculté des Sciences et Techniques de Mohammedia (FSTM)
> Université Hassan II de Casablanca — Année universitaire 2025–2026

</div>

---

## 📋 Table des matières

- [Présentation](#-présentation)
- [Fonctionnalités](#-fonctionnalités)
- [Stack Technique](#-stack-technique)
- [Architecture](#-architecture)
- [Gestion des rôles RBAC](#-gestion-des-rôles-rbac)
- [Base de données](#-base-de-données)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Lancement](#-lancement)
- [Comptes de démonstration](#-comptes-de-démonstration)
- [Structure du projet](#-structure-du-projet)
- [Commandes Artisan](#-commandes-artisan-utiles)
- [Auteurs](#-auteurs)

---

## 🚀 Présentation

**MonitorPro** est une plateforme web de monitoring intelligente développée dans le cadre d'un stage de fin d'études chez **Soft Seven Art**, agence digitale basée à Casablanca.

Elle permet de **surveiller en temps réel** la disponibilité, les performances et la sécurité des sites web clients, avec :
- Envoi automatique d'**alertes par email** via Brevo SMTP
- Génération de **rapports PDF** hebdomadaires
- **Assistant conversationnel IA** basé sur Google Gemini
- **Système de traçabilité complet** (audit log)
- **Export Excel** multi-modules
- **Notifications in-app** en temps réel

### 🎯 Objectifs

| # | Objectif |
|---|---|
| 1 | Surveiller automatiquement la disponibilité (uptime) des sites web toutes les 5 minutes |
| 2 | Mesurer les temps de réponse et détecter les ralentissements |
| 3 | Vérifier la validité et l'expiration des certificats SSL |
| 4 | Consulter les informations WHOIS et suivre l'expiration des domaines |
| 5 | Envoyer des alertes email automatiques via Brevo SMTP |
| 6 | Générer des rapports PDF hebdomadaires par client |
| 7 | Offrir un assistant conversationnel intelligent basé sur Google Gemini |
| 8 | Tracer toutes les actions sensibles via un système d'audit log complet |
| 9 | Exporter les données métier au format Excel (.xlsx) |
| 10 | Notifier les utilisateurs en temps réel via une cloche in-app |

---

## ✨ Fonctionnalités

| Fonctionnalité | Description |
|---|---|
| 📊 **Dashboard temps réel** | KPIs, graphiques Chart.js, histogramme 7 jours, anneau uptime SVG animé, auto-refresh 30s |
| 🔍 **Surveillance uptime** | Vérification HTTP/HTTPS automatique selon la fréquence configurée (5–30 min) |
| 🔒 **Vérification SSL** | Validité et date d'expiration, alertes préventives à 30, 15 et 7 jours |
| 🌐 **Vérification WHOIS** | Informations registrar, expiration de domaine, alertes préventives |
| 🚨 **Alertes automatiques** | Email lors de panne, lenteur, rétablissement, expiration SSL/domaine |
| 📄 **Rapports PDF** | Génération et envoi automatique de rapports hebdomadaires via DomPDF |
| 👥 **Gestion multi-rôles** | 3 profils : Super Administrateur, Agent, Client |
| 🧑‍💼 **Gestion des clients** | Création, modification, désactivation, réinitialisation de mot de passe |
| 🛠️ **Gestion des agents** | CRUD complet avec activation/désactivation |
| 📧 **Email de bienvenue** | Envoi automatique des identifiants au client à la création du compte |
| 🤖 **Assistant IA (Chatbot)** | Chatbot natif Laravel via OpenRouter + Google Gemini 2.5 Flash |
| 📈 **Rapport IA intelligent** | Analyse automatique du parc via workflow n8n + Google Gemini |
| 🔔 **Notifications in-app** | Cloche en topbar avec badge rouge, dropdown 10 dernières alertes, polling 30s |
| 📊 **Statistiques globales** | Tableau de bord exécutif 30 jours : 8 KPIs, 4 graphiques, top sites |
| 🕵️ **Audit log complet** | Traçabilité de toutes les actions sensibles avec interface de filtrage et diff JSON |
| 📤 **Export Excel** | Export .xlsx formaté pour Sites, Incidents, Alertes et Rapports (avec RBAC) |
| ⚙️ **Supervision Cron** | Tableau de bord des 5 tâches planifiées avec déclenchement manuel |
| 🔐 **Sécurité renforcée** | RBAC 3 niveaux, middlewares, isolation stricte des données par client |

---

## 🛠️ Stack Technique

### Backend

| Technologie | Version | Rôle |
|---|---|---|
| Laravel | 10.x | Framework PHP MVC — cœur applicatif |
| PHP | 8.3 | Langage de programmation |
| MySQL | 8.4 | Base de données relationnelle (15 tables) |
| Guzzle HTTP | 7.x | Client HTTP pour les vérifications de monitoring |
| DomPDF | 3.1 | Génération dynamique de rapports PDF |
| Maatwebsite/Excel | 3.1 | Export Excel (.xlsx) via PhpSpreadsheet |
| Laravel Breeze | 1.x | Authentification sécurisée clé en main |
| Laravel Scheduler | — | Automatisation des tâches planifiées |
| Laravel Queues | — | Traitement asynchrone des jobs de monitoring |
| Laravel Events | — | Listeners d'audit (Login, Logout, Failed, PasswordReset) |

### Frontend

| Technologie | Version | Rôle |
|---|---|---|
| Blade | — | Moteur de templates Laravel (rendu côté serveur) |
| CSS personnalisé | — | Palette bleu navy + doré — design system MonitorPro |
| Chart.js | Latest | Graphiques interactifs et visualisations |
| Font Awesome | 6.5 | Icônes vectorielles |
| SVG + JavaScript | — | Anneau uptime animé, carte santé du parc, polling |

### Services externes

| Service | Rôle |
|---|---|
| **Brevo SMTP** | Envoi des emails d'alerte et de bienvenue (port 587, TLS) |
| **OpenRouter API** | Passerelle LLM pour le chatbot (modèle google/gemini-2.5-flash) |
| **n8n Cloud** | Orchestration du workflow IA pour le rapport intelligent du dashboard |
| **Google Gemini** | Modèle de langage pour l'assistant conversationnel et l'analyse du parc |
| **ngrok** | Exposition de l'API locale pour le workflow n8n |
| **Laragon** | Environnement de développement local (Apache + MySQL + PHP) |

---

## 🏗️ Architecture

### Architecture du monitoring automatique

```
Scheduler Laravel  (php artisan schedule:work)
│
├── monitor:check-uptime          (toutes les N minutes, configurable)
│       └── CheckUptimeJob
│               ├── HTTP GET  →  is_up + response_time_ms + http_code
│               ├── INSERT verifications
│               └── Si anomalie  →  INSERT incidents  →  Alerte email (Brevo)
│
├── monitor:check-ssl             (toutes les heures)
│       └── CheckSslJob
│               ├── Handshake TLS  →  ssl_valid + ssl_expires_at
│               └── Si expiration < 30j  →  Alerte email
│
├── monitor:check-whois           (chaque semaine)
│       └── CheckWhoisJob
│               ├── Requête WHOIS  →  registrar + expiry_date
│               └── Si expiration < 30j  →  Alerte email
│
├── monitor:send-weekly-report    (lundi 08h00)
│       └── SendWeeklyReportJob
│               └── DomPDF  →  Rapport PDF  →  Email client
│
└── monitor:cleanup               (chaque jour 00h00)
        └── Suppression des anciennes vérifications

Queue Worker  (php artisan queue:work --tries=3)
└── Consomme les jobs depuis la table "jobs"
        └── En cas d'échec  →  table "failed_jobs" (après 3 tentatives)
```

### Architecture du Chatbot IA (natif Laravel)

```
Navigateur (JS fetch)
└── POST /chatbot/send
        └── Collecte état temps réel du parc (sites, incidents, SSL, WHOIS)
                └── Construction du prompt contextuel structuré
                        └── Http::post()  →  OpenRouter API
                                └── google/gemini-2.5-flash
                                        └── Réponse Markdown  →  Rendu HTML
```

### Architecture du Rapport Intelligent (Dashboard)

```
Workflow n8n Cloud
└── GET /api/monitoring-data  (via tunnel ngrok)
        └── Collecte état complet du parc
                └── Formatage contexte structuré
                        └── Google Gemini API
                                └── Analyse en français (Markdown)
                                        └── Rapport  →  Dashboard
```

### Architecture du Système d'Audit

```
Action utilisateur (login, CRUD site, rapport, export...)
└── Contrôleur ou Listener Laravel
        └── AuditService::log()   [service centralisé]
                ├── Snapshot user (nom, rôle)
                ├── Diff intelligent old_values / new_values (JSON)
                └── AuditLog::create()
                        └── Table audit_logs
                                └── Interface /audit  (admin uniquement)
```

---

## 🔐 Gestion des rôles (RBAC)

| Permission | Super Admin | Agent | Client |
|---|:---:|:---:|:---:|
| Tableau de bord | ✅ | ✅ | ✅ (filtré) |
| Voir tous les sites | ✅ | ✅ | ❌ |
| Voir ses propres sites | ✅ | ✅ | ✅ |
| Ajouter / Modifier / Supprimer des sites | ✅ | ✅ | ❌ |
| Gestion des agents | ✅ | ❌ | ❌ |
| Gestion des clients | ✅ | ✅ | ❌ |
| Supervision Cron Jobs | ✅ | ❌ | ❌ |
| Assistant IA | ✅ | ✅ | ✅ |
| Rapports et alertes | ✅ | ✅ | Les siens uniquement |
| Statistiques globales | ✅ | ✅ | ❌ |
| Historique d'audit | ✅ | ❌ | ❌ |
| Export Excel | ✅ | ✅ | Les siens uniquement |
| Notifications in-app | ✅ | ✅ | Les siennes uniquement |

> **Implémentation :** 3 middlewares Laravel — `auth`, `active_user`, `not_client` — complétés par un filtrage systématique sur `user_id` au niveau des contrôleurs.

---

## 🗄️ Base de données

```
monitoring_db  —  15 tables MySQL
│
├── 📦 Tables métier (8)
│   ├── users              →  Utilisateurs (super_admin / agent / client)
│   ├── sites              →  Sites web surveillés, rattachés à un client
│   ├── verifications      →  Résultats de chaque vérification (uptime + SSL)
│   ├── whois_info         →  Informations WHOIS et expiration des domaines
│   ├── incidents          →  Pannes et lenteurs détectées
│   ├── alertes            →  Emails d'alerte envoyés
│   ├── rapports           →  Rapports PDF générés
│   └── cron_logs          →  Historique d'exécution des tâches planifiées
│
├── 🔧 Tables techniques complémentaires (2)
│   ├── audit_logs         →  Journal d'audit (traçabilité des actions sensibles)
│   └── alerte_lectures    →  Statut de lecture des notifications in-app
│
└── ⚙️ Tables système Laravel (5)
    ├── jobs                    →  File d'attente des jobs asynchrones
    ├── failed_jobs             →  Jobs en échec (après 3 tentatives)
    ├── migrations              →  Historique des migrations
    ├── password_reset_tokens   →  Tokens de réinitialisation de mot de passe
    └── personal_access_tokens  →  Tokens d'accès API (Laravel Sanctum)
```

---

## 📦 Installation

### Prérequis

- [Laragon](https://laragon.org) (Apache + MySQL + PHP 8.3)
- [Composer](https://getcomposer.org) 2.x
- [Node.js](https://nodejs.org) 18+ et npm
- [ngrok](https://ngrok.com) *(optionnel — pour exposer l'API au workflow n8n)*

### Étapes d'installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/ibniabdraman62-sketch/monitoring-app.git
cd monitoring-app

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JavaScript et compiler les assets
npm install && npm run build

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Créer la base de données "monitoring_db" dans MySQL
# puis exécuter les migrations (15 tables)
php artisan migrate

# 6. Créer le lien symbolique pour le stockage
php artisan storage:link
```

---

## ⚙️ Configuration

### Base de données

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring_db
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=database
```

### Email — Brevo SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre_username_brevo
MAIL_PASSWORD=votre_cle_smtp_brevo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="monitoring@softseven.ma"
MAIL_FROM_NAME="MonitorPro"
```

> ⚠️ `MAIL_PASSWORD` = clé SMTP générée par Brevo (**Profil → SMTP & API → Clés SMTP**), pas votre mot de passe de compte.

### IA — OpenRouter (Chatbot)

```env
OPENROUTER_API_KEY=votre_cle_openrouter
```

---

## ▶️ Lancement

Démarrer Laragon (Apache + MySQL), puis ouvrir **3 terminaux** :

```bash
# Terminal 1 — Scheduler (déclenchement automatique des tâches de monitoring)
cd C:\laragon\www\monitoring-app
php artisan schedule:work

# Terminal 2 — Queue Worker (exécution asynchrone des jobs)
php artisan queue:work --tries=3

# Terminal 3 — Optionnel : tunnel ngrok (pour le rapport IA du dashboard)
ngrok http --host-header=rewrite monitoring-app.test:80
```

> ⚠️ Les terminaux 1 et 2 doivent rester ouverts en permanence pour que le monitoring automatique et les alertes fonctionnent.

🌐 **Accéder à l'application** : [http://monitoring-app.test](http://monitoring-app.test)

---

## 🔑 Comptes de démonstration

| Rôle | Email | Mot de passe | Accès |
|---|---|---|---|
| Super Administrateur | admin@softseven.ma | SoftSeven@2026 | Complet |
| Agent | agent@softseven.ma | Agent@2026 | Opérationnel |
| Client (exemple) | client@test.ma | Client@2026 | Consultation seule |

> Les clients sont créés depuis **Gestion Clients** ou lors de l'ajout d'un site via le bouton **"Nouveau client"** dans le formulaire.

---

## 📁 Structure du projet

```
monitoring-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/     →  Dashboard, Site, Incident, Alerte, Rapport,
│   │   │                       Agent, Audit, Notification, Statistique, Export
│   │   ├── Middleware/      →  CheckActiveUser.php, ClientMiddleware.php
│   │   └── Kernel.php       →  Enregistrement des middlewares
│   ├── Jobs/                →  CheckUptimeJob, CheckSslJob,
│   │                           CheckWhoisJob, SendWeeklyReportJob
│   ├── Listeners/           →  LogSuccessfulLogin, LogFailedLogin,
│   │                           LogLogout, LogPasswordReset
│   ├── Exports/             →  SitesExport, IncidentsExport,
│   │                           AlertesExport, RapportsExport
│   ├── Mail/                →  AlerteDownMail, AlerteSlowMail, AlerteSslMail,
│   │                           AlerteDomainMail, AlerteResolvedMail,
│   │                           RapportHebdoMail, ClientWelcomeMail
│   ├── Models/              →  User, Site, Verification, Incident,
│   │                           Alerte, Rapport, CronLog, WhoisInfo,
│   │                           AuditLog, AlerteLecture
│   └── Services/            →  MonitoringService.php, AuditService.php
│
├── resources/
│   └── views/
│       ├── layouts/         →  monitoring.blade.php (layout principal)
│       ├── components/      →  notification-bell.blade.php, export-button.blade.php
│       ├── auth/            →  login, forgot-password, reset-password
│       ├── admin/           →  cron_jobs, agents, clients
│       ├── audit/           →  index (historique d'audit)
│       ├── sites/           →  index, show, create, edit
│       ├── alertes/         →  index
│       ├── incidents/       →  index
│       ├── rapports/        →  index, pdf
│       ├── statistiques/    →  index (tableau de bord exécutif)
│       ├── chatbot/         →  index
│       ├── profile/         →  edit
│       └── emails/          →  alerte-down, alerte-slow, alerte-ssl,
│                                alerte-domain, alerte-resolved,
│                                rapport-hebdo, client-welcome
│
├── database/
│   └── migrations/          →  15 tables MySQL
│
└── routes/
    └── web.php              →  Routes de l'application
```

---

## 🔧 Commandes Artisan utiles

```bash
# Monitoring manuel
php artisan monitor:check-uptime
php artisan monitor:check-ssl
php artisan monitor:check-whois
php artisan monitor:send-weekly-report
php artisan monitor:cleanup

# Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Diagnostic
php artisan route:list
php artisan migrate:status
php artisan queue:failed

# Console interactive
php artisan tinker
```

---

## 👥 Auteurs

<div align="center">

| | Auteur | Contact | Rôle |
|---|---|---|---|
| 👨‍💻 | **ABDRAMAN IBNI ABDRAMAN** | ibniabdraman62@gmail.com | Développement full-stack, intégration IA, sécurité, audit log |
| 👨‍💻 | **ACHRAF MABROUK** | mabroukachraf.fstm@gmail.com | Développement backend, base de données, jobs asynchrones, export Excel |

---

🏢 Stage effectué chez **Soft Seven Art** — Casablanca, Maroc

| Rôle | Nom | Établissement |
|---|---|---|
| Encadrant entreprise | **M. Jail OTHMANE** | Soft Seven Art |
| Encadrant académique | **Pr. Noureddine MOUMKINE** | FSTM — Université Hassan II de Casablanca |

</div>

---

<div align="center">

📅 *Projet réalisé dans le cadre du stage de fin d'études — Année universitaire 2025–2026*

[![GitHub](https://img.shields.io/badge/GitHub-ibniabdraman62--sketch-1E3A5F?style=flat-square&logo=github)](https://github.com/ibniabdraman62-sketch/monitoring-app)

</div>