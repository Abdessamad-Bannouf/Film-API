
# Symfony 6 + PHP 8.0.13 avec Docker

**SEULEMENT pour l'environnement de DEV, pas pour la production**

Docker avec Symfony 6 & PHP 8.0.13

## Lancer localement

Lancer la commande docker-compose

```bash
  docker-compose up -d
```


Pour se connecter au containeur Symfony
```bash
  docker exec -it sf bash
```



*Pour le back, aller à l'adresse: http://127.0.0.1:9000*

*Pour le PHPMyadmin, il faut se rendre sur l'adresse: http://127.0.0.1:8080* 

Si il y a besoin de la base de données, il faut modifier le fichier .env comme cet exemple:

```yaml
  DATABASE_URL="postgresql://symfony:ChangeMe@database:5432/app?serverVersion=13&charset=utf8"
```

## Prêt à être utilisé avec

This docker-compose fournit :

- PHP-8.0.13-cli (Debian)
    - Composer
    - Symfony CLI
    - NodeJS, NPM, YARN
- postgres:13-alpine
- Node dernière version




# Installation du projet :  
  
    ● Cloner le projet : git clone https://github.com/Abdessamad-Bannouf/Film-API.git

    ● Lancer la commande : docker-compose up -d

    ● Lancer la commande : docker exec -it sf bash
    
    ● Installer le gestionnaire de dépendance : composer  
        
    ● Lancer la commande : php bin/console doctrine:database:create  
      
    ● Lancer la commande : php bin/console make:migration  

    ● Lancer la commande : php bin/console doctrine:migrations:migrate  

    ● Lancer la commande : php bin/console doctrine:fixtures:load
    
    ● Lancer Postman :
    
    ● Lancez les requêtes :

      - Requête GET Affiche une liste de films => localhost:9000/api/film

      - Requête GET Affiche un film => localhost:9000/api/film/{id}
            - Metre un id (de 1 à 5)

      - Requête POST Ajoute un film => localhost:9000/api/film 
            - allez dans l'onglet body -> raw et mettre un json comme celui-ci : 
            {
                "nom": "James Bond",
                "description": "Pïou piou description",
                "date": "2022-05-04",
                "note": 19
            }

      - Requête DELETE Supprime un film => localhost:9000/api/film/{id}
            - Metre un id (de 1 à 5)

      - Requête PATCH Modifie certains éléments = > localhost:9000/api/film/{id}
            - Metre un id (de 1 à 5)
            - allez dans l'onglet body -> raw ettre un json comme celui-ci :
            {
                "nom": "Alien",
                "note": 19
            }

      - Requête PUT Modifie en écrasant tout les données = > localhost:9000/api/film/{id}
            - Metre un id (de 1 à 5)
            - allez dans l'onglet body -> raw ettre un json comme celui-ci :
            {
                "nom": "Reservoir Dog",
                "description": "Des gens qui font le bracage du siècle, mais tout ne se passe pas comme prévu",
                "date": "2005-04-09",
                "note": 20
            }

