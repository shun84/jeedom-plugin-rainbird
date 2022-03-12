![icon du site RainBird](https://camo.githubusercontent.com/7bfadb412f350a026aa329f71cb895697e7727f63d8b636bdc62ca8b9742282c/687474703a2f2f69717765622e7261696e626972642e636f6d2f69712f696d616765732f6c6f676f732f7261696e626972642e706e67)

# Vérification pour le bon fonctionnement du plugin et du contrôleur RainBird
- Le module WiFi RainBird ne doit pas être sur le **Channel 13** de votre wifi.
- L'utilisation de l'application RainBird via le mobile en cours d'exécution peut entraîner des problèmes de connectivité

# Plugin RainBird

Plugin qui permet de gerer votre irrigation avec la solution Rainbird avec Jeedom systeme de domotique.

Il vous faut à tout prit le **WIFI Link** : https://wifi.rainbird.com/

# Configuration du plugin

Après avoir activé le plugin, vérifier que les dépendences sont ok, si c'est pas le cas les installer.

1. Définissez une adresse IP statique pour votre contrôleur **RainBird** via votre Box / Routeur
2. Puis créer votre objet avec l'adresse **IP** et le **Mot de passe** et sélectionner le nombre de zones que vous avez et sauvegarder.

Rendez-vous sur l'onglet **Configuration des zones** pour saisir la durée pour chaque zone et le nom si besoin.

Actuellement pour programmer vos arrosages, c'est d'utiliser pour l'instant par **Scénario**.
