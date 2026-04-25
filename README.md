# 📡 MonitorPro — Système de Monitoring des Sites Web

Développé par **ABDRAMAN IBNI Abdraman** — FST Mohammedia — Licence IRM 2026
Entreprise : **Soft Seven Art** — Casablanca

---

## 🚀 Démarrage rapide

### Prérequis
- Laragon (Apache + MySQL + PHP 8.x)
- Composer
- Node.js / npm

### Installation

```bash
# 1. Cloner le projet
git clone https://github.com/ibniabdraman62-sketch/monitoring-app.git
cd monitoring-app

# 2. Installer les dépendances
composer install
npm install && npm run build

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données monitoring_db dans MySQL
# Puis migrer
php artisan migrate

# 5. Créer le compte admin
php artisan db:seed --class=UserSeeder
```

### Lancement

```bash
# Terminal 1 — Serveur
php artisan serve

# Terminal 2 — Scheduler automatique
php artisan schedule:work
```

### Accès
- **URL** : http://127.0.0.1:8000
- **Email** : admin@softseven.ma
- **Mot de passe** : SoftSeven@2026

---

## 📋 Fonctionnalités

- ✅ Dashboard temps réel avec KPIs et graphiques
- ✅ Surveillance automatique HTTP/HTTPS
- ✅ Vérification certificats SSL
- ✅ Mesure temps de réponse
- ✅ Alertes email automatiques (panne/lenteur/résolution)
- ✅ Historique des incidents
- ✅ Rapports PDF hebdomadaires
- ✅ Barre historique uptime par site
- ✅ Vérification instantanée "Check Now"
- ✅ Auto-refresh dashboard toutes les 30s

---

## 🛠️ Technologies

| Technologie | Version | Rôle |
|---|---|---|
| Laravel | 10.x | Framework PHP MVC |
| PHP | 8.3 | Langage backend |
| MySQL | 8.4 | Base de données |
| Guzzle HTTP | 7.x | Client HTTP monitoring |
| DomPDF | 3.1 | Génération PDF |
| Chart.js | Latest | Graphiques dashboard |
| Mailtrap/SMTP | — | Envoi alertes email |

---

## ⚙️ Configuration Email

### Mode test (Mailtrap)
```env
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
```

### Mode production (Gmail)
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=votre_app_password
```

---

## 📞 Contact
**ABDRAMAN IBNI Abdraman** — ibniabdraman62@gmail.com