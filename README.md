# API CO-LIVING V.1
### Cette api a pour but de répondre aux attentes minimum du cachier des charges du business case

***Sommaire***

1. Récupérer et installer le projet
2. Les entités
3. Les fixtures
4. Les ressources

## RECUPERER ET INSTALLER LE PROJET

*Ouvrir un terminal dans le projet*

- Recréer le dossier vendor avec ``` composer install ```

- Configurer l'accès à la base de donnnée dans un fichier ```.env.local```

- Créer la base de donnée avec la commande ``` php bin/console d:d:c ```

- Exécuter les migrations avec ``` php bin/console d:m:m ```

- Charger les données de tests avec ``` php bin/console d:f:l ```

- Générer une paire de clé pour l'authentification par jeton JWT avec la commande ``` php bin/console lexik:jwt:generate-keypair ```

*Ces étapes efféctués vous devriez être en mesure de lancer un serveur local et commencer à requêter l'API*

## LES ENTITES

***User***
Générée avec le bundle security. Reconnu par son adresse</br>
mail.
Un User possède:
- un id
- email
-password
- date de naissance
- des informations sur son adresse postale. 

Certain User peuvent aussi avoir un role ```ROLE_OWNER``` pour indiquer qu'il sont propriétaires d'un ou plusieurs espaces.

User est lié à :
- Spaces (OneToMany)     -> $spaces
- Review (OneToMany)     -> $reviews
- Messages(OneToMany)    -> $messages
- Reservation(OneToMany) -> $reservations

***Space***
L'entité Space possède les propriétés suivantes: 
- id
- name
- description
- information sur l'adresse postale
- un prix
- liaison Reviews (OneToMany)   -> $review
- liaison Features (ManyToMany) -> $features
- liaison User (ManyToOne)      -> $owner
- liaison Room (OneToMany)      -> $rooms

Space est lié à:

***Room***
L'entité Room possède les propriétés suivantes:
- id
- name
- price
- liaison Space(ManyToOne)       -> $space
- liaison Reservation(OneToMany) -> $reservations

***Reservation***
Cette entité possède:
- id
- une date de début
- une date de fin
- une date de création
- liaison ReservationStatus(Enum) -> $status- un montant payé
- liaison User(ManyToOne)         -> $user
- liaison Room(ManyToOne)         -> $room


***Features***
Entité qui regroupe les services et les équipement d'un espace. Une Feature possède simplement un id, un name et une liaison ManyToMany sur l'entité Space

***Review***
Entité qui servira a stocker les avis des utilisateurs. Omis des fixtures pour le moment.</br>
Cette entité possède:
- un id
- une note
- Une liaison ManyToOne vers User -> $author
- Une liaison ManyToOne vers Space -> $space

***Messages***
Cette entité servira a stocker les messages des utilisateurs entre eux. Omis des fixtures pour le moment.</br>
Cette entité possède:
- id
- content
- date d'envoie
- liaison ManyToOne vers User -> $sender
- liaison ManyToOne vers User -> $receiver

***Image***
Cette entité sert de banque pour les images. Elle contient:
- id
- filename
- une liaison OneToOne vers User -> $user
- une liaison ManyToOne vers Space -> $space
- une liaison ManyToOne vers Room -> $room

## LES FIXTURES
Pour augmenter ou baisser le nombre d'espaces et d'utilisateurs généré, modifier les constantes de la classe AppFixtures.
```php
    private const NB_USER = 3;
    private const NB_SPACES = 5;
    private const FEATURES = [
        'Wi-fi',
        'Table de ping pong',
        'Conciergerie',
        'Cuisine moderne',
        'Climatisation',
        'Laverie'
    ];
```
Par défaut on génère 3 utilisateurs hors admin et 5 espaces. On peut également ajouter ou retirer des services ou des équipement dans l'array ```FEATURES```

***Les users***
Un seul admin est généré. Il s'appelle Admin Adminson, son **email** et ```admin@mail.com``` est **son mot de passe** c'est ```admin```.
Pour tout autre user, les données seront générés aléatoirement grâce à l'API faker, mais leur **mot de passe** à tous est ```user```
Tout les Users possède par défault une image de profile stockés dans le dossier public/uploads/images.</br>

***Les espaces***
A la génération des espaces, un propriétaires est choisi aléatoirement parmis les Users qui ont reçu le role de propriétaire.</br>
Chaque espace à 70% de chance de recevoir chaque feature. 
Une a 3 chambre sont crée pour chaque espace.</br></br>
Toutes ces valeurs peuvent être ajusté directement dans le code.</br>
Tout les espaces et toutes les chambres récupèrent une image par défaut stockée dans le dossier public/upload/images.</br>
Aucune review, reservation ou messages ne seront crée à la génération des fixtures.

## LES RESSOURCES
Seule **Space** et **User** sont des ressources d'API pour le moment. Une fois un serveur locale lancé avec ```symfony serve --no-tls``` vous pouvez consulter les réponses attendues pour chaque requête.</br>
Vous pouvez modifier la réponse en ajustant les groupes de sérialisation dans les entités.</br></br>
***Il faut posséder un token JWT valide pour consulter l'API***, il faut donc faire une requete POST vers le endpoint ```/api/login``` pour s'authentifier via l'admin ou un utilisateur.</br>
*Exemple de connexion à l'API:*
```json
{
  "email": "admin@mail.com",
  "password": "admin"
}
```
***Endpoints***</br>
- ```/api/login``` Renvoie le token JWT
- ```/api/spaces``` Renvoie plusieurs informations sur tous les espaces, leurs commodités, leur propriétaires, les chambres, ...
- ```/api/users``` Renvoie des information sur les utilisateurs et l'uri de leurs espaces s'ils en possèdent.