
# Informations sur l'API exposée par le powermaster 3

Le power master 3 expose une API JSON RPC sur le port 8181.
Cette API n'est pas documentée. L'objectif de ce repository est de rassembler les informations à ce propos. Le powerlink 3 fonctionne différemment des versions précédentes. Ce qui suit est donc spécifique à cette version. 

**ATTENTION : Lire de manière attentive les informations relatives à la sécurité**

## Sécurité

Voici la liste des problèmes de sécurité identifiés. D'autres problèmes peuvent exister mais ne pas être listés ici.

### HTTP
L'API n'est exposée qu'en http. Il n'y a pas de port https exposé. De ce fait, toutes les informations transmises entre l'alarme et le client sont en clair sur le réseau et peuvent donc être interceptées et utilisées par toute personne ayant accès au réseau sur lequel le powerlink est connecté.

### Code utilisateur
Pour fonctionner l'alarme impose un appel pour l'enregistrement du client. Cet appel nécessite de transmettre le code utilisateur, c'est à dire permettant d'armer ou de désarmer l'alarme. Ce code est donc transporté en clair.
De plus, si une personne accède à la machine enregistrée, elle peut utiliser l'API sans avoir besoin du code utilisateur.

Il **semble** que la ressource d’enregistrement ne soit pas protégée contre une attaque consistant à tester un par un tous les codes possibles. 

### Internet
Il est plus que conseillé de bloquer les accès entrant externes vers le port 8181 de l'alarme. Cela permet de minimiser les risques d'attaque provenant d'internet. 

## Activation

Sur certaines alarmes, l'api est activée par défaut. Sur d'autres, il faut l'activer via le mode installateur dans les menus : 
03: Centrale  -> 80: DOM. TIER. PART -> activer


## Problèmes connus

### Timeout
Je ne sais pas si ce problème est spécifique à mon installation mais dans certains cas, l'api ne répond plus, notamment via un time out client ou par un retour en erreur de la ressource spécifiant un time out.
En l'état, l'utilisation de l'api n'est pas fiable.

### IP enregistrées
Une seule IP ne peut être enregistrée à un instant donné. On ne peut pas "dé-enregistrer" une ip, on peut juste en enregistrer une autre en remplacement. 
L'ip enregistrée a une durée de vie de quelques jours.

## Utilisation

L'API exposée est en JSON RPC. Se référer [aux spécifications](https://www.jsonrpc.org/specification).
L'utilisation nécessite avant toute chose l'appel à la ressource PmaxService/registerClient. Cela permet d'enregistrer une ip pour l'utilisation de l'API.
Une fois le client enregistré, il est possible de faire appel aux ressources.


## Erreurs
Liste des erreurs courantes

TODO

## Ressources

Dans ce qui suit, les variables sur {{entre accolades}}. 

 - ip : l'ip de l'alarme ou son non DNS
 - port : port http de l'api : 8181


### Liste des commandes
C'est la seule ressource qui n'est pas JSON RPC.
Enregistrement du client nécessaire : non
Appel : GET http://{{ip}}:{{port}}/remote/json-rpc
Retour : La liste des ressources disponibles
Erreurs : Aucune connue

TODO

## Postman de test
La collection postman du repository contient un ensemble de tests non exhaustifs.
Il est nécessaire de la spécialiser en modifiant les valeurs de la partie environnement du postman.
