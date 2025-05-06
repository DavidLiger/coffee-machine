# ☕ Coffee Machine Simulator

Projet réalisé dans le cadre d’un test technique. Il s’agit d’une application simulant une machine à café connectée avec un processus de préparation de cafés, une API REST pour le contrôle, et un front-end en Next.js.

---

## 🎯 Objectif

- Simuler la préparation de cafés avec un processus en continu.
- Piloter la machine via une API REST (démarrer, arrêter, passer des commandes).
- Afficher l'état et les interactions en temps réel via une interface Next.js.
- Le tout dans un environnement Dockerisé, prêt à l’emploi avec une seule commande.

---

## 🧱 Architecture

### Back-End (PHP/Symfony)
- **API REST** développée avec Symfony 7
- **Processus continu** simulé par un worker PHP
- **RabbitMQ** pour la gestion des commandes en file
- **MariaDB** pour la persistance des données

### Front-End (Next.js)
- Interface de contrôle en temps réel
- Création de commandes café
- Affichage de la file d’attente et de l’historique

---

## 🚀 Lancer le projet

### Prérequis
- Docker & Docker Compose

### Commande unique pour tout démarrer

```bash
docker-compose up --build
