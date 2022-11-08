![icon du site RainBird](https://camo.githubusercontent.com/7bfadb412f350a026aa329f71cb895697e7727f63d8b636bdc62ca8b9742282c/687474703a2f2f69717765622e7261696e626972642e636f6d2f69712f696d616765732f6c6f676f732f7261696e626972642e706e67)

# Vérification pour le bon fonctionnement du plugin et du contrôleur RainBird
- Le module WiFi RainBird ne doit pas être sur le **Channel 13** de votre wifi.
- Définissez une adresse IP statique pour votre contrôleur **RainBird** via votre Box / Routeur
- L'utilisation de l'application RainBird via le mobile en cours d'exécution peut entraîner des problèmes de connectivité

# Plugin RainBird

Plugin qui permet de gerer votre irrigation avec la solution Rainbird avec Jeedom systeme de domotique.

Il vous faut à tout prit le **WIFI Link** : https://wifi.rainbird.com/

# Configuration du plugin

Après avoir activé le plugin, vérifier que les dépendences sont ok, si c'est pas le cas les installer.

Puis créer votre objet avec l'adresse **IP** et le **Mot de passe** et sauvegarder.

Nombre de Zone récupérer par une fonction de **l'API**

![img_2.png](../../images/equipement.png)

# Informations

![Informations.png](../../images/informations.png)

- No Serial : si c'est un **ESP-RZXE** ça sera = 0

# Configuration des zones

Rendez-vous sur l'onglet **Configuration des zones** pour saisir la durée pour chaque zone et le nom si besoin.

![Configuration zones](../../images/configuration-zones.png)

# Configuration des zones lier au plugin Agenda

Si le plugin **Agenda**, est activé sinon on n'a pas l'onglet **Programmation des Zones** 

![Agenda](../../images/agenda-1.png)

Cliquer sur **plugin Agenda** pour pouvoir saisir l'agenda en lien avec la zone à arroser

Après avoir créer l'agenda voici un exemple ci-dessous :

![Agenda](../../images/agenda-2.png)