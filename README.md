<div align="center">

# MonitorPro
### Système Intelligent de Monitoring des Sites Web avec Alertes Automatiques et IA

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)](LICENSE)

> **Projet de Fin d'Etudes — Licence Informatique, Reseaux et Multimedia (IRM)**
> Faculte des Sciences et Techniques de Mohammedia (FSTM)
> Universite Hassan II de Casablanca — 2026

</div>

---

## Presentation

**MonitorPro** est une application web complete developpee dans le cadre d'un stage de fin
d'etudes chez **Soft Seven Art**, agence digitale basee a Casablanca. Elle permet de surveiller
en temps reel la disponibilite, les performances et la securite des sites web clients, avec
envoi automatique d'alertes par email, generation de rapports PDF et assistant intelligent
base sur l'IA.

### Objectifs
- Surveiller automatiquement la disponibilite (uptime) des sites web toutes les 5 minutes
- Mesurer les temps de reponse et detecter les ralentissements
- Verifier la validite et l'expiration des certificats SSL
- Consulter les informations WHOIS et suivre l'expiration des domaines
- Envoyer des alertes email automatiques via Brevo SMTP
- Generer des rapports PDF hebdomadaires par client
- Offrir un assistant conversationnel intelligent base sur Google Gemini

---

## Fonctionnalites

| Fonctionnalite | Description |
|---|---|
| **Dashboard temps reel** | KPIs, graphiques Chart.js, histogramme 7 jours, auto-refresh 30s |
| **Surveillance uptime** | Verification HTTP/HTTPS automatique toutes les 5 minutes |
| **Verification SSL** | Validite et date d'expiration, alertes a 30/15/7 jours |
| **Verification WHOIS** | Informations registrar, expiration de domaine, alertes preventives |
| **Alertes automatiques** | Email lors de panne, lenteur, resolution, expiration SSL/domaine |
| **Rapports PDF** | Generation et envoi automatique de rapports hebdomadaires |
| **Gestion multi-roles** | 3 profils : Super Administrateur, Agent, Client |
| **Gestion des clients** | Creation, modification, desactivation, reset mot de passe |
| **Email de bienvenue** | Envoi automatique des identifiants au client a la creation |
| **Assistant IA** | Chatbot conversationnel base sur Google Gemini via n8n |
| **Supervision Cron** | Tableau de bord des 5 taches planifiees avec historique |
| **Securite renforcee** | 3 middlewares, blocage comptes desactives, isolation des donnees |

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
| Laravel Scheduler | — | Automatisation des taches planifiees |
| Laravel Queues | — | Traitement asynchrone des jobs |

### Frontend
| Technologie | Version | Role |
|---|---|---|
| Blade | — | Moteur de templates Laravel |
| CSS personnalise | — | Palette beige cream + bleu ciel professionnel |
| Chart.js | Latest | Graphiques et visualisations |
| Font Awesome | 6.5 | Icones |

### Services externes
| Service | Role |
|---|---|
| Brevo SMTP | Envoi des emails d'alerte et de bienvenue (port 587 TLS) |
| n8n Cloud | Orchestration du workflow IA |
| Google Gemini | Modele de langage pour l'assistant conversationnel |
| ngrok | Exposition de l'API locale pour n8n |
| Laragon | Environnement de developpement local (Apache + MySQL + PHP) |

---

## Architecture du monitoring
Scheduler Laravel (php artisan schedule:work)
├── monitor:check-uptime  (toutes les 5 min)
│       └── CheckUptimeJob → HTTP GET → is_up + response_time
│               ├── INSERT verifications
│               └── Si anomalie → INSERT incidents → Alerte email
│
├── monitor:check-ssl     (toutes les heures)
│       └── CheckSslJob → Handshake TLS → ssl_valid + ssl_expires_at
│               └── Si expiration < 30j → Alerte email
│
├── monitor:check-whois   (chaque semaine)
│       └── CheckWhoisJob → Serveur WHOIS → registrar + expiry_date
│               └── Si expiration < 30j → Alerte email
│
├── monitor:send-weekly-report  (lundi 08h00)
│       └── SendWeeklyReportJob → DomPDF → Email PDF
│
└── monitor:cleanup       (chaque jour 00h00)
└── Nettoyage des anciennes donnees
Queue Worker (php artisan queue:work --tries=3)
└── Consomme les jobs depuis la table "jobs"
└── En cas d'echec → table "failed_jobs" (apres 3 tentatives)

### Architecture du Chatbot IA
Navigateur (JS fetch)
└── POST /chatbot/send  [route Laravel — proxy anti-CORS]
└── HTTP::post() → n8n Cloud webhook
└── Workflow n8n → Google Gemini API
└── Reponse formatee (Markdown, tableaux)
└── Retour au navigateur → Affichage

---

## Gestion des roles (RBAC)

| Permission | Super Admin | Agent | Client |
|---|---|---|---|
| Voir tous les sites | ✅ | ✅ | ❌ |
| Voir ses propres sites | ✅ | ✅ | ✅ |
| Ajouter / Modifier / Supprimer des sites | ✅ | ✅ | ❌ |
| Gestion des agents | ✅ | ❌ | ❌ |
| Gestion des clients | ✅ | ❌ | ❌ |
| Supervision Cron Jobs | ✅ | ❌ | ❌ |
| Assistant IA | ✅ | ✅ | ✅ |
| Rapports et alertes | ✅ | ✅ | Les siens |

---

## Structure de la base de donnees

monitoring_db  (13 tables)
│
├── Tables metier
│   ├── users              → Utilisateurs (super_admin, agent, client)
│   ├── sites              → Sites web surveilles, rattaches a un client
│   ├── verifications      → Resultats de chaque verification (uptime + SSL)
│   ├── whois_info         → Informations WHOIS et expiration des domaines
│   ├── incidents          → Pannes et lenteurs detectees
│   ├── alertes            → Emails d'alerte envoyes
│   ├── rapports           → Rapports PDF generes
│   └── cron_logs          → Historique d'execution des taches planifiees
│
└── Tables systeme Laravel
├── jobs                      → File d'attente des jobs asynchrones
├── failed_jobs               → Jobs en echec
├── migrations                → Historique des migrations
├── password_reset_tokens     → Tokens de reinitialisation
└── personal_access_tokens    → Tokens d'acces API

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
# Puis executer les migrations
php artisan migrate

# 7. Creer le lien symbolique pour le stockage
php artisan storage:link
```

### Configuration Email (Brevo SMTP)

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

> Brevo (ex-Sendinblue) est utilise pour l'envoi de tous les emails :
> alertes, rapports hebdomadaires et emails de bienvenue clients.

---

## Lancement

Ouvrir **trois terminaux** dans le dossier du projet :

```bash
# 1. Laragon : demarrer Apache + MySQL depuis l'interface graphique

# Terminal 1 — Scheduler (taches automatiques de monitoring)
cd C:\laragon\www\monitoring-app
php artisan schedule:work

# Terminal 2 — Queue Worker (execution asynchrone des jobs)
php artisan queue:work --tries=3

# Terminal 3 — Optionnel : exposition via ngrok (pour n8n)
ngrok http --host-header=rewrite monitoring-app.test:80
```

> **Important** : les terminaux 1 et 2 doivent rester ouverts en permanence
> pour que le monitoring automatique et les alertes fonctionnent.

**Acceder a l'application** : http://monitoring-app.test

---

## Comptes de demonstration

| Role | Email | Mot de passe |
|---|---|---|
| Super Administrateur | admin@softseven.ma | *(voir UserSeeder)* |
| Agent | agent@softseven.ma | *(voir UserSeeder)* |
| Client (exemple) | client@test.ma | *(defini a la creation)* |

> Les clients sont crees directement depuis l'interface "Gestion Clients"
> ou lors de l'ajout d'un site ("Nouveau client" dans le formulaire).

---

## Structure du projet

monitoring-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/         → Dashboard, Site, Incident, Alerte, Rapport, Agent...
│   │   ├── Middleware/          → CheckActiveUser.php, ClientMiddleware.php
│   │   └── Kernel.php           → Enregistrement des middlewares
│   ├── Jobs/                    → CheckUptimeJob, CheckSslJob, CheckWhoisJob,
│   │                              SendWeeklyReportJob
│   ├── Mail/                    → AlerteDownMail, AlerteSlowMail, AlerteSslMail,
│   │                              AlerteDomainMail, AlerteResolvedMail,
│   │                              RapportHebdoMail, ClientWelcomeMail
│   ├── Models/                  → User, Site, Verification, Incident, Alerte,
│   │                              Rapport, CronLog, WhoisInfo
│   └── Services/                → MonitoringService.php
├── resources/
│   └── views/
│       ├── layouts/             → monitoring.blade.php (layout principal)
│       ├── auth/                → login, forgot-password, reset-password
│       ├── admin/               → cron_jobs, agents, clients
│       ├── sites/               → index, show, create, edit
│       ├── alertes/             → index
│       ├── incidents/           → index
│       ├── rapports/            → index, pdf
│       ├── chatbot/             → index
│       ├── profile/             → edit
│       └── emails/              → alerte-down, alerte-slow, alerte-ssl,
│                                   alerte-domain, alerte-resolved,
│                                   rapport-hebdo, client-welcome
├── database/
│   └── migrations/              → 13 tables MySQL
└── routes/web.php               → Routes de l'application + proxy chatbot

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
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Lister toutes les routes
php artisan route:list

# Acceder a Tinker
php artisan tinker
```

---

## Auteurs

<div align="center">

**ABDRAMAN IBNI ABDRAMAN**
Etudiant en Licence IRM — FSTM
ibniabdraman62@gmail.com
Role : Developpement full-stack, integration IA, securite

---

**ACHRAF MABROUK**
Etudiant en Licence IRM — FSTM
mabroukachraf.fstm@gmail.com
Role : Developpement backend, base de donnees, jobs asynchrones

---

Stage effectue chez **Soft Seven Art** — Casablanca, Maroc

**Encadrant entreprise :** M. Jail OTHMANE — Soft Seven Art

**Encadrant academique :** Pr. Noureddine MOUMKINE — FSTM

Universite Hassan II de Casablanca

</div>

---

<div align="center">

Projet realise dans le cadre du stage de fin d'etudes — Annee universitaire 2025-2026

</div>
