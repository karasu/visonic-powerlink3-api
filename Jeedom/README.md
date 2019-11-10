# Utilisation dans jeedom
Ce répertoire contient une utilisation de l'API pour jeedom. 
Le code moche (je ne connais pas le php...). Il est fourni pour le principe mais devrait fonctionner ailleurs. 

**Attention, lire avant toute chose le paragraphe sur la sécurité, l'activation de l'api et les problèmes connus dans [le README de l'API](https://github.com/Froggy-AA/visonic-powerlink3-api/blob/master/api/README.md)**

## Principe
Le mode de fonctionnement est basé sur un scenario ([source](https://github.com/Froggy-AA/visonic-powerlink3-api/blob/master/Jeedom/visonic.php)) qui va renseigner les données d'un objet virtuel. 

Le scénario va appeler ces ressources de l'API du powerlink : 
 - PmaxService/registerClient pour l'enregistrement de la machine jeedom
 - PmaxService/getBatteryLevel pour le niveau de batterie de l'alarme
 - PmaxService/getGsmLevel pour le niveau de réception de la carte GRPS ou 3G de l'alarme
 - PmaxService/isPanelConnected pour connaitre le status de connexion de l'alarme
 - PmaxService/getPanelStatuses pour les autres information sur l'alarme et les informations sur les partitions

Il est possible de commenter l'un de ces appels si par exemple vous n'avez pas de carte GPRS ou 3G. 

Aucun appel n'est effectué sur internet. C'est le module powerlink visonic qui retourne les informations.

## Pre-requis
Ce scénario part du principe que la gestion des partitions a été activée sur l'alarme.
Il faut selon les versions de l'alarme activer l'API du powerlink.

## Scénario

Commencer par créer un scénario. 

Dans l'onglet *Général* :

Renseigner les infos habituelles. Deux points d'attention : 

 1. Timeout
 Il faut lui attribuer un timeout plus court que la fréquence d'ordonnancement. Concrètement, pour un ordonnancement toutes les minutes, le time out doit être de 59000. 
 2. Le mode de déclanchement
 Pour récupérer les informations régulièrement et automatiquement, mettre le *mode du scénario* à `Programmé` et la *Programmation* à `* * * * *` pour le lancer toutes les minutes par exemple.


Dans l'onglet *Scénario* :

Ajouter un bloc de type *Code* et y copier le code du scenario du fichier [visonic.php](https://github.com/Froggy-AA/visonic-powerlink3-api/blob/master/Jeedom/visonic.php)

Il faut ensuite spécialiser les informations présentes en début de fichier : 

Nom de la variable php| Signification 
--|--
$IP_JEEDOM|Mettre l'ip de la machine hébergeant votre jeedom. Attention, utiliser automatiquement l'IP, pas de nom DNS  
$IP_ALARME|Mettre l'ip ou le nom DNS de votre alarme
$CODE_ALARME|Mettre le code utilisateur de l'alarme, c'est à dire le code que vous utilisez pour l'armer ou la désarmer. **Attention : l'API exposée est en http (et pas https), ce code va donc notamment circuler en clair sur votre réseau**
$PORT_ALARME|Le port de l'api de l'alarme. Normalement ne devrait pas être modifié (8181)

Et spécialiser les variables php contenant le nom des infos du virtuel à renseigner. Le format est celui jeddom habituel : `#[Nom de l'objet parent][Nom de l'objet][Nom de la zone d'information]#` exemple : `#[Alarme][Visonic][nombreErreurs]#`

Nom de la variable php|escription de la zone du virtuel|Type et valeur normale
--|--|--
$VIRT_ERRORS_NUMBER|Nom de l'information Nombre d'erreurs générées par l'appel du scénario. Ce compteur est remis à 0 à chaque appel réussi|0
$VIRT_ERRORS_EXECUTION_SCENARIO|Indicateur permettant de connaitre s'il y a eu une erreur lors de l'exécution du scenario. Réinitialisé à chaque appel|false
$VIRT_BATTERY_LEVEL|Niveau de la batterie interne de l'alarme en % |100
$VIRT_GSM_LEVEL| Niveau de reception de la carte GPRS ou 3G en %|Numérique. Dépend de votre niveau de réception
$VIRT_ALARM_CONNECTED|Alarme connectée ou non ?|true
$VIRT_AC_TROUBLE|Alerte sur un problème d'alimentation électrique|false
$VIRT_LOW_BATTERY|Alerte de l'alarme sur un niveau de battterie faible sur l'alarme. En lien direct avec $VIRT_BATTERY_LEVEL mais le flag d'alerte est géré par l'alarme|false
$VIRT_COMMUNICATION_FAILURE|Alerte de l'alarme sur un défaut de communication|false
$VIRT_PART_ARMEE__TAB|Tableau de nom spécifiant pour chaque partition si elle est armée|false si non armé, true sinon
$VIRT_PART_ALERTE__TAB|Tableau de nom spécifiant pour chaque partition si elle est en alerte|false
$VIRT_PART_TROUBLE__TAB|Tableau de nom spécifiant pour chaque partition si elle est en défaut|false
$VIRT_PART_PRETE__TAB|Tableau de nom spécifiant pour chaque partition si elle est prête pour l'armement|true. false peut simplement signifier qu'une porte avec détecteur d'ouverture est ouverte
$VIRT_PART_INCENDIE__TAB|Tableau de nom spécifiant pour chaque partition si elle est en alarme incendie|false
