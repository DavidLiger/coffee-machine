# Explication des choix techniques ☕️

## Pourquoi utiliser RabbitMQ ?

Dans ce projet de simulation de machine à café, nous avons choisi RabbitMQ comme système de messagerie entre l’API (Symfony) et le processus de préparation du café (worker PHP). Voici pourquoi :

### 🔄 Découplage des responsabilités
RabbitMQ agit comme un tampon entre les requêtes utilisateurs (API) et le traitement (worker). Cela permet à l’API de rester rapide et réactive, même si le traitement des commandes de café est long ou complexe.

### 🕒 Gestion du temps réel et du délai
La préparation d’un café est simulée en plusieurs étapes (chauffe, broyage, infusion...), ce qui peut prendre plusieurs secondes. RabbitMQ permet de mettre les commandes en file d’attente pour les traiter dans l’ordre, sans bloquer l’API ou le front.

### 📈 Scalabilité
En cas d’augmentation de charge, il est facile d’ajouter plusieurs workers pour traiter les messages RabbitMQ en parallèle, sans modifier l’API.

### ⚖️ Pourquoi pas Redis ?
Redis peut aussi gérer une file, mais RabbitMQ est conçu dès le départ pour la communication entre services, avec des options avancées (accusés de réception, retries, dead-letter queues…). Il est donc plus adapté pour un usage orienté "messages" comme ici.

## En résumé :
| Critère                 | RabbitMQ | Redis   |
|-------------------------|----------|---------|
| File d’attente native   | ✅        | 🔶 (possible mais basique) |
| Communication asynchrone | ✅        | 🔶       |
| Fonctionnalités avancées | ✅        | ❌       |
| Facilité à mettre en œuvre pour des events | ✅ | ❌ |

RabbitMQ s’impose donc naturellement dans une architecture orientée messages avec des traitements différés comme celui d’une machine à café connectée. ☕️

