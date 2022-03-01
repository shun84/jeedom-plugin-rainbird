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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../resources/apirainbird/ApiRainBird.php';

class rainbird extends eqLogic {
	public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);

    public function updateRainbird() {
        $apirainbird = new ApiRainBird($this->getConfiguration('iprainbird'), $this->getConfiguration('mdprainbird'));

        $this->checkAndUpdateCmd('daterainbird', $apirainbird->get_current_date()[0]);
        $this->checkAndUpdateCmd('timerainbird', $apirainbird->get_current_time()[0]);
        $this->checkAndUpdateCmd('getraindelay', $apirainbird->get_rain_delay()[0]);

        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            $this->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
        }

        $this->refreshWidget();
    }

    public static function cron() {
        foreach (RainBird::byType('rainbird') as $eqLogic) {
            if ($eqLogic->getIsEnable() == 1) {
                $eqLogic->updateRainbird();
            }
        }
    }

    /**
     * Fonction exécutée automatiquement avant la mise à jour de l'équipement
     *
     * @throws Exception
     */
    public function preUpdate() {
        if ($this->getConfiguration('iprainbird') === '') {
            throw new Exception(__('Veuillez saisir l\'adresse IP du rainbird', __FILE__));
        }

        if ($this->getConfiguration('mdprainbird') === '') {
            throw new Exception(__('Veuillez saisir votre mot de passe du rainbird', __FILE__));
        }

        if ($this->getConfiguration('nbzone') === '') {
            throw new Exception(__('Veuillez sélectionner le nombre de zone du rainbird', __FILE__));
        }
    }

    /**
     * Fonction exécutée automatiquement après la mise à jour de l'équipement
     *
     * @throws Exception
     */
    public function postUpdate() {
        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            $zone = $this->getCmd(null, 'zone'.$i);
            if (!is_object($zone)) {
                $zone = new RainBirdCmd();
            }
            $zone->setName(__('Zone '.$i, __FILE__));
            $zone->setIsVisible(0);
            $zone->setLogicalId('zone'.$i);
            $zone->setEqLogic_id($this->getId());
            $zone->setType('info');
            $zone->setSubType('string');
            $zone->save();

            $zonelancer = $this->getCmd(null, 'zonelancer'.$i);
            if (!is_object($zonelancer)) {
                $zonelancer = new RainBirdCmd();
            }
            $zonelancer->setName(__('Zone Lancer '.$i, __FILE__));
            $zonelancer->setLogicalId('zonelancer'.$i);
            $zonelancer->setEqLogic_id($this->getId());
            $zonelancer->setType('action');
            $zonelancer->setSubType('other');
            $zonelancer->save();

            $getzonelancer = $this->getCmd(null, 'getzonelancer'.$i);
            if (!is_object($getzonelancer)) {
                $getzonelancer = new RainBirdCmd();
            }
            $getzonelancer->setName(__('Récupération Zone '.$i, __FILE__));
            $getzonelancer->setLogicalId('getzonelancer'.$i);
            $getzonelancer->setEqLogic_id($this->getId());
            $getzonelancer->setType('info');
            $getzonelancer->setSubType('binary');
            $getzonelancer->save();

            $zonestop = $this->getCmd(null, 'zonestop'.$i);
            if (!is_object($zonestop)) {
                $zonestop = new RainBirdCmd();
            }
            $zonestop->setName(__('Arreter la zone '.$i, __FILE__));
            $zonestop->setLogicalId('zonestop'.$i);
            $zonestop->setEqLogic_id($this->getId());
            $zonestop->setType('action');
            $zonestop->setSubType('other');
            $zonestop->save();
        }
    }

    /**
     * Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
     */
    public function preSave() {
        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            if($this->getConfiguration('nomzone'.$i) === ''){
                    $this->setConfiguration('nomzone'.$i, 'Zone '.$i);
            }

            if($this->getConfiguration('duree'.$i) === ''){
                $this->setConfiguration('duree'.$i, 0);
            }
        }

        if ($this->getConfiguration('dureetestzone') === ''){
            $this->setConfiguration('dureetestzone', 2);
        }

        if ($this->getConfiguration('nbzone') < "5"){
            $this->setDisplay("width","632px");
            $this->setDisplay("height","272px");
        }

        if ($this->getConfiguration('nbzone') > "4" && $this->getConfiguration('nbzone') < "9"){
            $this->setDisplay("width","632px");
            $this->setDisplay("height","370px");
        }
    }

    /**
     * Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
     *
     * @throws Exception
     */
    public function postSave() {
        $daterainbird = $this->getCmd(null, 'daterainbird');
        if (!is_object($daterainbird)) {
            $daterainbird = new RainBirdCmd();
        }
        $daterainbird->setName(__('Date Rainbird', __FILE__));
        $daterainbird->setLogicalId('daterainbird');
        $daterainbird->setEqLogic_id($this->getId());
        $daterainbird->setType('info');
        $daterainbird->setSubType('string');
        $daterainbird->save();

        $timerainbird = $this->getCmd(null, 'timerainbird');
        if (!is_object($timerainbird)) {
            $timerainbird = new RainBirdCmd();
        }
        $timerainbird->setName(__('Time Rainbird', __FILE__));
        $timerainbird->setLogicalId('timerainbird');
        $timerainbird->setEqLogic_id($this->getId());
        $timerainbird->setType('info');
        $timerainbird->setSubType('string');
        $timerainbird->save();

        $stopirrigation = $this->getCmd(null, 'stopirrigation');
        if (!is_object($stopirrigation)) {
            $stopirrigation = new RainBirdCmd();
        }
        $stopirrigation->setName(__('Stop Irrigation', __FILE__));
        $stopirrigation->setLogicalId('stopirrigation');
        $stopirrigation->setEqLogic_id($this->getId());
        $stopirrigation->setType('action');
        $stopirrigation->setSubType('other');
        $stopirrigation->save();

        $getraindelay = $this->getCmd(null, 'getraindelay');
        if (!is_object($getraindelay)) {
            $getraindelay = new RainBirdCmd();
        }
        $getraindelay->setName(__('Récupération Stop Irrigation sur un nombre de jours', __FILE__));
        $getraindelay->setLogicalId('getraindelay');
        $getraindelay->setEqLogic_id($this->getId());
        $getraindelay->setType('info');
        $getraindelay->setSubType('numeric');
        $getraindelay->setUnite('Jours');
        $getraindelay->setConfiguration('minValue',0);
        $getraindelay->setConfiguration('maxValue', 14);
        $getraindelay->save();

        $setraindelay = $this->getCmd(null, 'setraindelay');
        if (!is_object($setraindelay)) {
            $setraindelay = new RainBirdCmd();
        }
        $setraindelay->setName(__('Retarder l\'arrosage', __FILE__));
        $setraindelay->setLogicalId('setraindelay');
        $setraindelay->setEqLogic_id($this->getId());
        $setraindelay->setType('action');
        $setraindelay->setSubType('slider');
        $setraindelay->setConfiguration('minValue',0);
        $setraindelay->setConfiguration('maxValue', 14);
        $setraindelay->save();

        $zonetest = $this->getCmd(null, 'zonetest');
        if (!is_object($zonetest)) {
            $zonetest = new RainBirdCmd();
        }
        $zonetest->setName(__('Zone Test', __FILE__));
        $zonetest->setLogicalId('zonetest');
        $zonetest->setEqLogic_id($this->getId());
        $zonetest->setType('action');
        $zonetest->setSubType('other');
        $zonetest->save();

        if ($this->getIsEnable() == 1) {
            $this->updateRainbird();
        }
    }

    /**
     * Vérification si les dependances sont installé
     */
    public static function dependancy_info(): array
    {
        $return = array();
        $return['log'] = log::getPathToLog(__CLASS__.'_update');
        $return['progress_file'] = jeedom::getTmpFolder(__CLASS__).'/dependency';
        if (file_exists(jeedom::getTmpFolder(__CLASS__).'/dependency')) {
            $return['state'] = 'in_progress';
        } else {
            if (exec(system::getCmdSudo() . system::get('cmd_check') . '-Ec "python3\-pip|python3\-setuptools"') < 2) {
                $return['state'] = 'nok';
            } elseif (exec(system::getCmdSudo() . 'pip3 list | grep -Ewc "setuptools|pycryptodomex|requests|datetime"') < 3) {
                $return['state'] = 'nok';
            } else {
                $return['state'] = 'ok';
            }
        }
        return $return;
    }

    /**
     * Install les dépendances de python pour utiliser l'API de rainbird
     */
    public static function dependancy_install(): array
    {
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder(__CLASS__).'/dependency', 'log' => log::getPathToLog(__CLASS__.'_update'));
    }

    /**
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
     *
     * @throws Exception
     */
    public function toHtml($_version = 'dashboard') {
          $replace = $this->preToHtml($_version);
          if (!is_array($replace)) {
              return $replace;
          }
          $_version = jeedom::versionAlias($_version);

          $daterainbird = $this->getCmd(null, 'daterainbird');
          $replace['#daterainbird#'] = is_object($daterainbird) ? $daterainbird->execCmd() : '';

          $timerainbird = $this->getCmd(null, 'timerainbird');
          $timerainbird = substr(is_object($timerainbird) ? $timerainbird->execCmd() : '', 0,-3);
          $replace['#timerainbird#'] = $timerainbird;

          $stopirrigation = $this->getCmd(null, 'stopirrigation');
          $replace['#stopirrigation#'] = is_object($stopirrigation) ? $stopirrigation->getId() : '';

          $getraindelay = $this->getCmd(null, 'getraindelay');
          $replace['#getraindelay#'] = is_object($getraindelay) ? $getraindelay->execCmd() : '';

          $setraindelay = $this->getCmd(null, 'setraindelay');
          $replace['#setraindelay#'] = is_object($setraindelay) ? $setraindelay->getId() : '';

          $getdureetestzone = $this->getConfiguration('dureetestzone');
          $replace['#getdureetestzone#'] = $getdureetestzone;

          $zonetest = $this->getCmd(null, 'zonetest');
          $replace['#zonetest#'] = is_object($zonetest) ? $zonetest->getId() : '';

          $nbzone = $this->getConfiguration('nbzone');
          $replace['#nbzone#'] = $nbzone;

          for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
              $gettimezone = $this->getConfiguration('duree'.$i);
              $replace['#duree'.$i.'#'] = $gettimezone;

              $getnomzone = $this->getConfiguration('nomzone'.$i);
              $replace['#nomzone'.$i.'#'] = $getnomzone;

              $getzone = $this->getCmd(null, 'zone'.$i);
              $replace['#zone'.$i.'#'] = is_object($getzone) ? $getzone->getLogicalId() : '';

              $lancerzone = $this->getCmd(null, 'zonelancer'.$i);
              $replace['#zonelancer'.$i.'#'] = is_object($lancerzone) ? $lancerzone->getId() : '';

              $getzonelancer = $this->getCmd(null, 'getzonelancer'.$i);
              $replace['#getzonelancer'.$i.'#'] = is_object($getzonelancer) ? $getzonelancer->execCmd() : '';

              $stopzone = $this->getCmd(null, 'zonestop'.$i);
              $replace['#zonestop'.$i.'#'] = is_object($stopzone) ? $stopzone->getId() : '';
          }

          $html = template_replace($replace, getTemplate('core', $_version, 'rainbird', 'rainbird'));
          cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
          return $html;
    }
}

class RainBirdCmd extends cmd {
    /**
     * Exécution des commandes via le dashbord
     *
     * @param array $_options
     * @throws Exception
     */
    public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();
        $apirainbird = new ApiRainBird($eqLogic->getConfiguration('iprainbird'), $eqLogic->getConfiguration('mdprainbird'));

        if ($apirainbird->get_current_date()[0] == 'None'){
            throw new Exception(__('Vérifier votre login et mot de passe ou l\'application rainbird lancé sur votre Téléphone', __FILE__));
        }

        for ($i = 1; $i <= $eqLogic->getConfiguration('nbzone'); $i++){
            if ($this->getLogicalId() === 'zonelancer'.$i.''){
                $getintzonelancer = (int) substr($this->getLogicalId(), -1); // Récupération du dernier chiffre de la zone
                if ($getintzonelancer === 0){
                    $getintzonelancer = (int) substr($this->getLogicalId(), -2);
                }

                $gettimezone = (int) $eqLogic->getConfiguration('duree'.$i.'');
                if ($gettimezone !== 0){
                    $apirainbird->irrigate_zone($getintzonelancer, $gettimezone);
                }
                $eqLogic->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
                log::add('rainbird','debug','Lancement de l\'action irrigation pour la zone '.$getintzonelancer);
            }

            if ($this->getLogicalId() === 'zonestop'.$i.''){
                $apirainbird->stop_irrigation();
                $eqLogic->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
                log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation de la zone '.$i);
            }
        }

        if ($this->getLogicalId() === 'stopirrigation'){
            $apirainbird->stop_irrigation();
            $eqLogic->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
            log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation');
        }

        if ($this->getLogicalId() === 'setraindelay'){
            $getvaleurslider = $_options['slider'];
            $apirainbird->set_rain_delay($getvaleurslider);
            $eqLogic->checkAndUpdateCmd('getraindelay', $apirainbird->get_rain_delay()[0]);
            log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation sur un nombre de jours');
        }

        if ($this->getLogicalId() === 'zonetest'){
            $apirainbird->test_zone($eqLogic->getConfiguration('dureetestzone'));
            log::add('rainbird','debug','Lancement de l\'action test des zones');
        }

        $eqLogic->refreshWidget();
    }
}


