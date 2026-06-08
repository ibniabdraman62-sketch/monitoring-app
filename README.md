<div align="center">

# MonitorPro
### Système Intelligent de Monitoring des Sites Web avec Alertes Automatiques et IA

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)](LICENSE)

> *Projet de Fin d'Etudes — Licence Informatique, Reseaux et Multimedia (IRM)*  
> Faculte des Sciences et Techniques de Mohammedia (FSTM)  
> Universite Hassan II de Casablanca — 2026

</div>

---

## Presentation

*MonitorPro* est une application web complete developpee dans le cadre d'un stage de fin d'etudes chez *Soft Seven Art*, agence digitale basee a Casablanca. Elle permet de surveiller en temps reel la disponibilite, les performances et la securite des sites web clients, avec envoi automatique d'alertes par email, generation de rapports PDF, assistant intelligent base sur l'IA, systeme de traçabilite complet et export Excel multi-modules.

### Objectifs

- Surveiller automatiquement la disponibilite (uptime) des sites web toutes les 5 minutes
- Mesurer les temps de reponse et detecter les ralentissements
- Verifier la validite et l'expiration des certificats SSL
- Consulter les informations WHOIS et suivre l'expiration des domaines
- Envoyer des alertes email automatiques via Brevo SMTP
- Generer des rapports PDF hebdomadaires par client
- Offrir un assistant conversationnel intelligent base sur Google Gemini
- Tracer toutes les actions sensibles via un systeme d'audit log complet
- Exporter les donnees metier au format Excel (.xlsx)
- Notifier les utilisateurs en temps reel via une cloche in-app

---

## Fonctionnalites

| Fonctionnalite | Description |
|---|---|
| *Dashboard temps reel* | KPIs, graphiques Chart.js, histogramme 7 jours, anneau uptime SVG, auto-refresh 30s |
| *Surveillance uptime* | Verification HTTP/HTTPS automatique toutes les 5 minutes |
| *Verification SSL* | Validite et date d'expiration, alertes a 30/15/7 jours |
| *Verification WHOIS* | Informations registrar, expiration de domaine, alertes preventives |
| *Alertes automatiques* | Email lors de panne, lenteur, resolution, expiration SSL/domaine |
| *Rapports PDF* | Generation et envoi automatique de rapports hebdomadaires |
| *Gestion multi-roles* | 3 profils : Super Administrateur, Agent, Client |
| *Gestion des clients* | Creation, modification, desactivation, reset mot de passe |
| *Gestion des agents* | CRUD complet avec activation/desactivation |
| *Email de bienvenue* | Envoi automatique des identifiants au client a la creation |
| *Assistant IA (Chatbot)* | Chatbot natif Laravel via OpenRouter + Google Gemini 2.5 Flash |
| *Rapport IA intelligent* | Analyse automatique du parc via workflow n8n + Google Gemini |
| *Notifications in-app* | Cloche en topbar avec badge, dropdown 10 alertes, polling 30s |
| *Statistiques globales* | Tableau de bord executif 30 jours (8 KPIs, 4 graphiques, top sites) |
| *Audit log complet* | Tracabilite de toutes les actions sensibles avec interface de filtrage |
| *Export Excel* | Export .xlsx formate pour Sites, Incidents, Alertes et Rapports |
| *Supervision Cron* | Tableau de bord des 5 taches planifiees avec declenchement manuel |
| *Securite renforcee* | RBAC 3 niveaux, middlewares, isolation des donnees par client |

---

## Stack Technique

### Backend

| Technologie | Version | Role |
|---|---|---|
| Laravel | 10.x | Framework PHP MVC |
| PHP | 8.3 | Langage de programmation |
| MySQL | 8.4 | Base de donnees relationnelle (15 tables) |
| Guzzle HTTP | 7.x | Client HTTP pour le monitoring |
| DomPDF | 3.1 | Generation de rapports PDF |
| Maatwebsite/Excel | 3.1 | Export Excel (.xlsx) via PhpSpreadsheet |
| Laravel Breeze | 1.x | Authentification |
| Laravel Scheduler | — | Automatisation des taches planifiees |
| Laravel Queues | — | Traitement asynchrone des jobs |
| Laravel Events | — | Listeners d'audit (Login, Logout, Failed, PasswordReset) |

### Frontend

| Technologie | Version | Role |
|---|---|---|
| Blade | — | Moteur de templates Laravel |
| CSS personnalise | — | Palette beige cream + bleu ciel professionnel |
| Chart.js | Latest | Graphiques et visualisations |
| Font Awesome | 6.5 | Icones |
| SVG + JavaScript | — | Anneau uptime anime, carte sante du parc |

### Services externes

| Service | Role |
|---|---|
| Brevo SMTP | Envoi des emails d'alerte et de bienvenue (port 587 TLS) |
| OpenRouter API | Passerelle LLM pour le chatbot (google/gemini-2.5-flash) |
| n8n Cloud | Orchestration du workflow IA pour le rapport intelligent |
| Google Gemini | Modele de langage pour l'assistant conversationnel et le rapport |
| ngrok | Exposition de l'API locale pour le workflow n8n |
| Laragon | Environnement de developpement local (Apache + MySQL + PHP) |

---

## Architecture du monitoring


Scheduler Laravel  (php artisan schedule:work)
│
├── monitor:check-uptime          (toutes les 5 min)
│       └── CheckUptimeJob
│               ├── HTTP GET  →  is_up + response_time_ms
│               ├── INSERT verifications
│               └── Si anomalie  →  INSERT incidents  →  Alerte email
│
├── monitor:check-ssl             (toutes les heures)
│       └── CheckSslJob
│               ├── Handshake TLS  →  ssl_valid + ssl_expires_at
│               └── Si expiration < 30j  →  Alerte email
│
├── monitor:check-whois           (chaque semaine)
│       └── CheckWhoisJob
│               ├── Requete WHOIS  →  registrar + expiry_date
│               └── Si expiration < 30j  →  Alerte email
│
├── monitor:send-weekly-report    (lundi 08h00)
│       └── SendWeeklyReportJob
│               └── DomPDF  →  Rapport PDF  →  Email
│
└── monitor:cleanup               (chaque jour 00h00)
        └── Nettoyage des anciennes donnees

Queue Worker  (php artisan queue:work --tries=3)
└── Consomme les jobs depuis la table "jobs"
        └── En cas d'echec  →  table "failed_jobs" (apres 3 tentatives)


### Architecture du Chatbot IA


Navigateur (JS fetch)
└── POST /chatbot/send   [route Laravel — integration native]
        └── Collecte donnees monitoring temps reel (sites, incidents, SSL)
                └── Construction prompt contextuel structure
                        └── Http::post()  →  OpenRouter API
                                └── google/gemini-2.5-flash
                                        └── Reponse Markdown  →  Rendu HTML


### Architecture du Rapport Intelligent (Dashboard)


Workflow n8n Cloud
└── GET /api/monitoring-data  (via ngrok tunnel)
        └── Collecte etat complet du parc
                └── Formatage contexte structure
                        └── Google Gemini API
                                └── Analyse en francais
                                        └── Rapport Markdown  →  Dashboard


### Architecture du Systeme d'Audit


Action utilisateur (login, CRUD site, rapport...)
└── Controleur ou Listener Laravel
        └── AuditService::log()   [service centralise]
                └── AuditLog::create()
                        └── Table audit_logs
                                └── Interface /audit  (admin uniquement)


---

## Gestion des roles (RBAC)

| Permission | Super Admin | Agent | Client |
|---|---|---|---|
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

```

---

## Structure de la base de donnees


monitoring_db  —  15 tables
│
├── Tables metier (8)
│   ├── users                →  Utilisateurs (super_admin / agent / client)
│   ├── sites                →  Sites web surveilles, rattaches a un client
│   ├── verifications        →  Resultats de chaque verification (uptime + SSL)
│   ├── whois_info           →  Informations WHOIS et expiration des domaines
│   ├── incidents            →  Pannes et lenteurs detectees
│   ├── alertes              →  Emails d'alerte envoyes
│   ├── rapports             →  Rapports PDF generes
│   └── cron_logs            →  Historique d'execution des taches planifiees
│
├── Tables techniques complementaires (2)
│   ├── audit_logs           →  Journal d'audit (traçabilite des actions sensibles)
│   └── alerte_lectures      →  Statut de lecture des notifications in-app
│
└── Tables systeme Laravel (5)
    ├── jobs                      →  File d'attente des jobs asynchrones
    ├── failed_jobs               →  Jobs en echec
    ├── migrations                →  Historique des migrations
    ├── password_reset_tokens     →  Tokens de reinitialisation
    └── personal_access_tokens    →  Tokens d'acces API


---

## Installation

### Prerequis

- [Laragon](https://laragon.org) (Apache + MySQL + PHP 8.3)
- [Composer](https://getcomposer.org) 2.x
- [Node.js](https://nodejs.org) 18+ et npm
- [ngrok](https://ngrok.com) (optionnel, pour exposer l'API a n8n)

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
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring_db
DB_USERNAME=root
DB_PASSWORD=

# 6. Creer la base de donnees "monitoring_db" dans MySQL
# puis executer les migrations (15 tables)
php artisan migrate

# 7. Creer le lien symbolique pour le stockage
php artisan storage:link


### Configuration Email — Brevo SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre_username_brevo
MAIL_PASSWORD=votre_cle_api_brevo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="monitoring@softseven.ma"
MAIL_FROM_NAME="MonitorPro"
```

### Configuration IA — OpenRouter (Chatbot)

```env
OPENROUTER_API_KEY=votre_cle_openrouter


```

> OpenRouter est utilise pour le chatbot conversationnel (modele google/gemini-2.5-flash).
> Le rapport intelligent du dashboard utilise un workflow n8n separe via ngrok.

---

## Lancement

Ouvrir *trois terminaux* dans le dossier du projet apres avoir demarre Laragon :

```bash
# Laragon : demarrer Apache + MySQL depuis l'interface graphique

# Terminal 1 — Scheduler (taches automatiques de monitoring)
cd C:\laragon\www\monitoring-app
php artisan schedule:work

# Terminal 2 — Queue Worker (execution asynchrone des jobs)
php artisan queue:work --tries=3

# Terminal 3 — Optionnel : exposition via ngrok (pour rapport IA n8n)
ngrok http --host-header=rewrite monitoring-app.test:80


> *Important* : les terminaux 1 et 2 doivent rester ouverts en permanence
> pour que le monitoring automatique et les alertes fonctionnent.

*Acceder a l'application* : http://monitoring-app.test

---

## Comptes de demonstration

| Role | Email | Mot de passe |
|---|---|---|
| Super Administrateur | admin@softseven.ma | SoftSeven@2026 |
| Agent | agent@softseven.ma | Agent@2026 |
| Client (exemple) | client@test.ma | Client@2026 |

> Les clients sont crees depuis l'interface *Gestion Clients* ou lors de l'ajout
> d'un site via le bouton *"Nouveau client"* dans le formulaire.

```

---

## Structure du projet


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
│       ├── statistiques/    →  index (tableau de bord executif)
│       ├── chatbot/         →  index
│       ├── profile/         →  edit
│       └── emails/          →  alerte-down, alerte-slow, alerte-ssl,
│                                alerte-domain, alerte-resolved,
│                                rapport-hebdo, client-welcome
├── database/
│   └── migrations/          →  15 tables MySQL
└── routes/web.php           →  Routes de l'application


---

## Commandes Artisan utiles

```bash
# Lancer manuellement une tache de monitoring
php artisan monitor:check-uptime
php artisan monitor:check-ssl
php artisan monitor:check-whois
php artisan monitor:send-weekly-report
php artisan monitor:cleanup

# Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Lister toutes les routes
php artisan route:list

# Acceder a Tinker (console interactive)
php artisan tinker

# Verifier le statut des migrations
php artisan migrate:status


---

## Auteurs

<div align="center">

*ABDRAMAN IBNI ABDRAMAN*  
Etudiant en Licence IRM — FSTM  
ibniabdraman62@gmail.com  
Role : Developpement full-stack, integration IA, securite, audit log

---

*ACHRAF MABROUK*  
Etudiant en Licence IRM — FSTM  
mabroukachraf.fstm@gmail.com  
Role : Developpement backend, base de donnees, jobs asynchrones, export Excel

---

Stage effectue chez *Soft Seven Art* — Casablanca, Maroc

*Encadrant entreprise :* M. Jail OTHMANE — Soft Seven Art  
*Encadrant academique :* Pr. Noureddine MOUMKINE — FSTM  
Universite Hassan II de Casablanca

</div>

---

<div align="center">

Projet realise dans le cadre du stage de fin d'etudes — Annee universitaire 2025-2026

</div>