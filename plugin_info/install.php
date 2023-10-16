<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
  function rainbird_install() {
      log::add('rainbird', 'warn', 'Installation du Plugin Rain Bird');

      if (version_compare(system::getOsVersion(), '11', '<')){
          message::add('rainbird', 'Attention, votre version de Debian doit être en 11 minimum');
      } else {
          message::add('rainbird', 'Merci pour l\'installation de ce plugin, consultez la documentation avant utilisation svp');
      }
  }

// Fonction exécutée automatiquement après la mise à jour du plugin
  function rainbird_update() {
      if (version_compare(system::getOsVersion(), '11', '<')){
          message::add('rainbird', 'Attention, votre version de Debian doit être en 11 minimum');
      } else {
          message::add('rainbird', 'Merci pour la mise à jour de ce plugin, consultez la documentation avant utilisation svp');
      }

      $dependencyInfo = rainbird::dependancy_info();
      if (!isset($dependencyInfo['state'])) {
          message::add('rainbird', __('Veuilez vérifier les dépendances', __FILE__));
      } elseif ($dependencyInfo['state'] == 'nok') {
          message::add('rainbird', __('Cette mise à jour nécessite absolument de relancer les dépendances même si elles apparaissent vertes', __FILE__));
      }

      foreach (rainbird::byType('rainbird') as $rainbird){
          $rainbird->save();
      }
  }

// Fonction exécutée automatiquement après la suppression du plugin
  function rainbird_remove() {

  }
