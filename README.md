![icon du site RainBird](https://camo.githubusercontent.com/7bfadb412f350a026aa329f71cb895697e7727f63d8b636bdc62ca8b9742282c/687474703a2f2f69717765622e7261696e626972642e636f6d2f69712f696d616765732f6c6f676f732f7261696e626972642e706e67)

Il y a aucune affiliation avec l'entreprise Rainbird.
J'utilise l'**API Python** sur le lien suivant : https://github.com/jbarrancos/pyrainbird

# Jeedom-plugin-rainbird

Plugin qui permet de gerer votre irrigation avec la solution Rainbird avec Jeedom systeme de domotique.

Il vous faut à tout prit le **WIFI Link** : https://wifi.rainbird.com/

# Installation

Installation depuis Github :

> cd /var/www/html/plugins  # ou autre suivant l'installation de votre Jeedom
> 
> sudo git clone https://github.com/shun84/jeedom-plugin-rainbird.git RainBird

Sinon par le plugin **JeeXplorer**

# Avant la configuration du plugin
Il faut récupérer l'adresse **IP** et le **Mot de passe** utilisé pour aller dans la configuration sur votre application **Rainbird**.

**TRES IMPORTANT : il faut juste déprogrammer vos arrossages sur l'application RainBird et que l'application Mobile ne soit pas lancé sinon il va y avoir un conflit avec l'utilisation sur Jeedom**

# Configuration du plugin

Après avoir activé le plugin, vérifier que les dépendences sont ok, si c'est pas le cas les installer.

Puis créer votre objet avec l'adresse **IP** et le **Mot de passe** et sélectionner le nombre de zones que vous avez et sauvegarder.

Rendez-vous sur l'onglet **Configuration des zones** pour saisir la durée pour chaque zone et le nom si besoin.

Actuellement pour programmer vos arrosages, c'est d'utiliser pour l'instant par **Scénario**.
