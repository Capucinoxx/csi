# csi

Gestionnaire de projet pour https://www.csisher.com/

## Configuration

La modification des variables d'environnements peut se faire dans le fichier `.env`. Lorsque la branche est clonée, on crée un fichier `.env` avec certaines valuers par défaut de la manière suivante:

```bash
$ cp .env.dist .env
```

Après modification, exécuter la commande suivante:

```bash
$ docker-compose build
```

Pour installer l'image Docker, lancer la commande suivante:

```bash
$ docker-compose up -d
```
