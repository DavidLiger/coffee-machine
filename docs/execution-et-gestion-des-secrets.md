# Choix d'architecture : Gestion des secrets et lancement en une commande

## 🎯 Objectifs

- Respecter l’instruction : **“Le projet doit fonctionner via Docker et se lancer en une seule ligne de commande”**
- **Ne pas exposer de secrets sensibles** dans le dépôt Git (respect GitGuardian)
- Garantir la **portabilité du projet** sans étapes manuelles supplémentaires

---

## 🔐 Gestion des secrets

### Solution retenue

Les variables sensibles comme `APP_SECRET`, `DATABASE_URL` et `RABBITMQ_HOST` ne sont **pas codées en dur** dans un fichier `.env` suivi par Git.  
À la place, elles sont injectées directement via la directive `environment:` du `docker-compose.yml`, et la syntaxe suivante est utilisée :

```yaml
environment:
  APP_SECRET: ${APP_SECRET:-dev-secret}
  DATABASE_URL: mysql://root:root@mariadb:3306/coffeedb
  RABBITMQ_HOST: rabbitmq
```
Voici la version Markdown que tu peux utiliser pour ton document `execution-et-gestion-des-secrets.md` à placer dans ton dossier `docs/` :

````markdown
# Choix d'architecture : Gestion des secrets et lancement en une commande

## 🎯 Objectifs

- Respecter l’instruction : **“Le projet doit fonctionner via Docker et se lancer en une seule ligne de commande”**
- **Ne pas exposer de secrets sensibles** dans le dépôt Git (respect GitGuardian)
- Garantir la **portabilité du projet** sans étapes manuelles supplémentaires

---

## 🔐 Gestion des secrets

### Solution retenue

Les variables sensibles comme `APP_SECRET`, `DATABASE_URL` et `RABBITMQ_HOST` ne sont **pas codées en dur** dans un fichier `.env` suivi par Git.  
À la place, elles sont injectées directement via la directive `environment:` du `docker-compose.yml`, et la syntaxe suivante est utilisée :

```yaml
environment:
  APP_SECRET: ${APP_SECRET:-dev-secret}
  DATABASE_URL: mysql://root:root@mariadb:3306/coffeedb
  RABBITMQ_HOST: rabbitmq
````

Cela permet de :

* Utiliser un secret issu de l’environnement local si disponible (`APP_SECRET` est lu depuis le système hôte)
* Sinon, utiliser une valeur par défaut non critique (`dev-secret`) dans un environnement de développement

---

## ✅ Avantages de cette approche

* **Aucun secret dans Git** → conforme à GitGuardian
* **Pas de besoin de `.env.local`** → plus de risques de commits accidentels
* **Lancement en une seule ligne** :

```bash
docker-compose up --build
```

* **Portable sur toute machine** → aucun script manuel requis pour initialiser l’environnement
* **Extensible** facilement dans un environnement de CI/CD ou en production

---

## 📂 Organisation des fichiers

* Le fichier `.env.local` a été supprimé
* Le fichier `.env` reste ignoré via `.gitignore` pour éviter toute fuite ultérieure
* Toutes les variables attendues sont définies par `docker-compose` via l’environnement

---

## 🛠️ Exemple de `.env` local facultatif (non versionné)

Pour surcharger les variables en local, un développeur peut créer un fichier `.env` (hors Git) à la racine du projet avec :

```env
APP_SECRET=fake-secret-for-local
```

Et Docker les utilisera automatiquement grâce à la syntaxe `${VARIABLE}` dans `docker-compose.yml`.

---

## 📦 Résultat

Le projet est **100% conforme** aux attentes :

* Aucun secret exposé
* Lancement simple
* Configuration claire et centralisée

```

