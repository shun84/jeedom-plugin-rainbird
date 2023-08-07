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
require_once __DIR__ . '/../../core/api/rainbirdApi.php';

class rainbird extends eqLogic {

    /* ***********************Methode static*************************** */
    public static function cron() {
        foreach (rainbird::byType('rainbird') as $eqLogic) {
            if ($eqLogic->getIsEnable() == 1) {
                $eqLogic->updateRainbird();
            }
        }
    }

    /* *********************Méthodes d'instance************************* */
    /**
     * @throws Exception
     */
    public function verifConnexion(rainbirdApi $apirainbird){
        $getcurrentdate = $apirainbird->get_current_date();

        if (count($getcurrentdate) > 1){
            foreach ($getcurrentdate as $value){
                log::add('rainbird','debug',$value);
            }
            throw new Exception(__('Vérifier votre login et mot de passe ou l\'application rainbird lancé sur votre Téléphone', __FILE__));
        }
    }

    public function updateRainbird() {
        $apirainbird = new rainbirdApi($this->getConfiguration('iprainbird'), $this->getConfiguration('mdprainbird'));

        $this->checkAndUpdateCmd('daterainbird', $apirainbird->get_current_date()[0]);
        $this->checkAndUpdateCmd('timerainbird', $apirainbird->get_current_time()[0]);
        $this->checkAndUpdateCmd('getraindelay', $apirainbird->get_rain_delay()[0]);
        $this->checkAndUpdateCmd('getrainsensorstate', $apirainbird->get_rain_sensor_state()[0]);
        $this->checkAndUpdateCmd('dureezonetest',$this->getConfiguration('dureetestzone'));

        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            $this->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
            $this->checkAndUpdateCmd('timezone'.$i, $this->getConfiguration('duree'.$i));
            $this->checkAndUpdateCmd('namezone'.$i, $this->getConfiguration('nomzone'.$i));
        }

        $this->refreshWidget();
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

        $apirainbird = new rainbirdApi($this->getConfiguration('iprainbird'), $this->getConfiguration('mdprainbird'));

        $this->verifConnexion($apirainbird);

        $availablestations = $apirainbird->get_available_stations();
        $getserialnumber = $apirainbird->get_serial_number();
        $modelandversion = $apirainbird->get_model_and_version();

        $this->setConfiguration('model', $modelandversion[0]);
        $this->setConfiguration('version', $modelandversion[1]);
        $this->setConfiguration('serial', $getserialnumber[0]);
        $this->setConfiguration('nbzone', $availablestations);

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
    }

    /**
     * Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
     *
     * @throws Exception
     */
    public function postSave() {
        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            $namezone = $this->getCmd(null, 'namezone'.$i);
            if (!is_object($namezone)) {
                $namezone = new rainbirdCmd();
            }
            $namezone->setName(__('Name Zone'.$i, __FILE__));
            $namezone->setLogicalId('namezone'.$i);
            $namezone->setEqLogic_id($this->getId());
            $namezone->setType('info');
            $namezone->setSubType('string');
            $namezone->setGeneric_type('GENERIC_INFO');
            $namezone->save();

            $getzonelancer = $this->getCmd(null, 'getzonelancer'.$i);
            if (!is_object($getzonelancer)) {
                $getzonelancer = new rainbirdCmd();
            }
            $getzonelancer->setName(__('Zone '.$i, __FILE__));
            $getzonelancer->setLogicalId('getzonelancer'.$i);
            $getzonelancer->setEqLogic_id($this->getId());
            $getzonelancer->setType('info');
            $getzonelancer->setSubType('binary');
            $getzonelancer->setIsHistorized(1);
            $getzonelancer->setGeneric_type('GENERIC_INFO');
            $getzonelancer->save();

            $timezone = $this->getCmd(null, 'timezone'.$i);
            if (!is_object($timezone)) {
                $timezone = new rainbirdCmd();
            }
            $timezone->setName(__('Durée zone '.$i, __FILE__));
            $timezone->setLogicalId('timezone'.$i);
            $timezone->setEqLogic_id($this->getId());
            $timezone->setType('info');
            $timezone->setSubType('numeric');
            $timezone->setUnite('minutes');
            $timezone->setGeneric_type('GENERIC_INFO');
            $timezone->save();

            $zonelancer = $this->getCmd(null, 'zonelancer'.$i);
            if (!is_object($zonelancer)) {
                $zonelancer = new rainbirdCmd();
            }
            $zonelancer->setName(__('Lancer la zone '.$i, __FILE__));
            $zonelancer->setLogicalId('zonelancer'.$i);
            $zonelancer->setEqLogic_id($this->getId());
            $zonelancer->setType('action');
            $zonelancer->setSubType('other');
            $zonelancer->setGeneric_type('GENERIC_ACTION');
            $zonelancer->setValue($getzonelancer->getId());
            $zonelancer->save();

            $zonestop = $this->getCmd(null, 'zonestop'.$i);
            if (!is_object($zonestop)) {
                $zonestop = new rainbirdCmd();
            }
            $zonestop->setName(__('Arreter la zone '.$i, __FILE__));
            $zonestop->setLogicalId('zonestop'.$i);
            $zonestop->setEqLogic_id($this->getId());
            $zonestop->setType('action');
            $zonestop->setSubType('other');
            $zonestop->setGeneric_type('GENERIC_ACTION');
            $zonestop->setValue($getzonelancer->getId());
            $zonestop->save();
        }

        $daterainbird = $this->getCmd(null, 'daterainbird');
        if (!is_object($daterainbird)) {
            $daterainbird = new rainbirdCmd();
        }
        $daterainbird->setName(__('Date', __FILE__));
        $daterainbird->setLogicalId('daterainbird');
        $daterainbird->setEqLogic_id($this->getId());
        $daterainbird->setType('info');
        $daterainbird->setSubType('string');
        $daterainbird->save();

        $timerainbird = $this->getCmd(null, 'timerainbird');
        if (!is_object($timerainbird)) {
            $timerainbird = new rainbirdCmd();
        }
        $timerainbird->setName(__('Heure', __FILE__));
        $timerainbird->setLogicalId('timerainbird');
        $timerainbird->setEqLogic_id($this->getId());
        $timerainbird->setType('info');
        $timerainbird->setSubType('string');
        $timerainbird->save();

        $stopirrigation = $this->getCmd(null, 'stopirrigation');
        if (!is_object($stopirrigation)) {
            $stopirrigation = new rainbirdCmd();
        }
        $stopirrigation->setName(__('Stop Irrigation', __FILE__));
        $stopirrigation->setLogicalId('stopirrigation');
        $stopirrigation->setEqLogic_id($this->getId());
        $stopirrigation->setType('action');
        $stopirrigation->setSubType('other');
        $stopirrigation->setGeneric_type('GENERIC_ACTION');
        $stopirrigation->save();

        $getraindelay = $this->getCmd(null, 'getraindelay');
        if (!is_object($getraindelay)) {
            $getraindelay = new rainbirdCmd();
        }
        $getraindelay->setName(__('Stop Irrigation sur un nombre de jours', __FILE__));
        $getraindelay->setLogicalId('getraindelay');
        $getraindelay->setEqLogic_id($this->getId());
        $getraindelay->setType('info');
        $getraindelay->setSubType('numeric');
        $getraindelay->setUnite('Jours');
        $getraindelay->setIsVisible(0);
        $getraindelay->setConfiguration('minValue',0);
        $getraindelay->setConfiguration('maxValue', 14);
        $getraindelay->save();

        $setraindelay = $this->getCmd(null, 'setraindelay');
        if (!is_object($setraindelay)) {
            $setraindelay = new rainbirdCmd();
        }
        $setraindelay->setName(__('Retarder arrosage', __FILE__));
        $setraindelay->setLogicalId('setraindelay');
        $setraindelay->setEqLogic_id($this->getId());
        $setraindelay->setType('action');
        $setraindelay->setSubType('slider');
        $setraindelay->setConfiguration('minValue',0);
        $setraindelay->setConfiguration('maxValue', 14);
        $setraindelay->setValue($getraindelay->getId());
        $setraindelay->save();

        $dureezonetest = $this->getCmd(null, 'dureezonetest');
        if (!is_object($dureezonetest)) {
            $dureezonetest = new rainbirdCmd();
        }
        $dureezonetest->setName(__('Durée Zone Test', __FILE__));
        $dureezonetest->setLogicalId('dureezonetest');
        $dureezonetest->setEqLogic_id($this->getId());
        $dureezonetest->setType('info');
        $dureezonetest->setSubType('numeric');
        $dureezonetest->setUnite('minutes');
        $dureezonetest->setGeneric_type('GENERIC_INFO');
        $dureezonetest->save();

        $zonetest = $this->getCmd(null, 'zonetest');
        if (!is_object($zonetest)) {
            $zonetest = new rainbirdCmd();
        }
        $zonetest->setName(__('Zone Test', __FILE__));
        $zonetest->setLogicalId('zonetest');
        $zonetest->setEqLogic_id($this->getId());
        $zonetest->setType('action');
        $zonetest->setSubType('other');
        $zonetest->setValue($dureezonetest->getId());
        $zonetest->setGeneric_type('GENERIC_ACTION');
        $zonetest->save();

        $getrainsensorstate = $this->getCmd(null, 'getrainsensorstate');
        if (!is_object($getrainsensorstate)) {
            $getrainsensorstate = new rainbirdCmd();
        }
        $getrainsensorstate->setName(__('Sensor', __FILE__));
        $getrainsensorstate->setLogicalId('getrainsensorstate');
        $getrainsensorstate->setEqLogic_id($this->getId());
        $getrainsensorstate->setType('info');
        $getrainsensorstate->setSubType('binary');
        $getrainsensorstate->setGeneric_type('GENERIC_INFO');
        $getrainsensorstate->save();

        $getwaterbudget = $this->getCmd(null, 'getwaterbudget');
        if (!is_object($getwaterbudget)) {
            $getwaterbudget = new rainbirdCmd();
        }
        $getwaterbudget->setName(__('Pourcentage saisonnier', __FILE__));
        $getwaterbudget->setLogicalId('getwaterbudget');
        $getwaterbudget->setEqLogic_id($this->getId());
        $getwaterbudget->setType('info');
        $getwaterbudget->setSubType('numeric');
        $getwaterbudget->setUnite('%');
        $getwaterbudget->setIsVisible(0);
        $getwaterbudget->setGeneric_type('GENERIC_INFO');
        $getwaterbudget->save();

        $waterbudget = $this->getCmd(null, 'waterbudget');
        if (!is_object($waterbudget)) {
            $waterbudget = new rainbirdCmd();
        }
        $waterbudget->setName(__('Ajustement saisonnier', __FILE__));
        $waterbudget->setLogicalId('waterbudget');
        $waterbudget->setEqLogic_id($this->getId());
        $waterbudget->setType('action');
        $waterbudget->setSubType('slider');
        $waterbudget->setGeneric_type('GENERIC_ACTION');
        $waterbudget->setValue($getwaterbudget->getId());
        $waterbudget->save();

        $program = $this->getCmd(null, 'program');
        if (!is_object($program)) {
            $program = new rainbirdCmd();
        }
        $program->setName(__('Programme', __FILE__));
        $program->setLogicalId('program');
        $program->setEqLogic_id($this->getId());
        $program->setType('info');
        $program->setSubType('numeric');
        $program->setIsVisible(0);
        $program->setGeneric_type('GENERIC_INFO');
        $program->save();

        $setprogram = $this->getCmd(null, 'setprogram');
        if (!is_object($setprogram)) {
            $setprogram = new rainbirdCmd();
        }
        $setprogram->setName(__('Lancer un programme', __FILE__));
        $setprogram->setLogicalId('setprogram');
        $setprogram->setEqLogic_id($this->getId());
        $setprogram->setType('action');
        $setprogram->setSubType('slider');
        $setprogram->setValue($program->getId());
        $setprogram->setGeneric_type('GENERIC_ACTION');
        $setprogram->save();

        $getadvancezone= $this->getCmd(null, 'getadvancezone');
        if (!is_object($getadvancezone)) {
            $getadvancezone = new rainbirdCmd();
        }
        $getadvancezone->setName(__('Zone suivante', __FILE__));
        $getadvancezone->setLogicalId('getadvancezone');
        $getadvancezone->setEqLogic_id($this->getId());
        $getadvancezone->setType('info');
        $getadvancezone->setSubType('numeric');
        $getadvancezone->setIsVisible(0);
        $getadvancezone->setGeneric_type('GENERIC_INFO');
        $getadvancezone->save();

        $advancezone = $this->getCmd(null, 'advancezone');
        if (!is_object($advancezone)) {
            $advancezone = new rainbirdCmd();
        }
        $advancezone->setName(__('Prochaine zone', __FILE__));
        $advancezone->setLogicalId('advancezone');
        $advancezone->setEqLogic_id($this->getId());
        $advancezone->setType('action');
        $advancezone->setSubType('slider');
        $advancezone->setValue($getadvancezone->getId());
        $advancezone->setGeneric_type('GENERIC_ACTION');
        $advancezone->save();

        if ($this->getIsEnable() == 1) {
            $this->updateRainbird();
        }
    }

    /**
     * Vérification si les dependances sont installé
     */
    public static function dependancy_info(): array
    {
        $return = [];
        $return['log'] = log::getPathToLog(__CLASS__.'_update');
        $return['progress_file'] = jeedom::getTmpFolder(__CLASS__).'/dependency';
        if (file_exists(jeedom::getTmpFolder(__CLASS__).'/dependency')) {
            $return['state'] = 'in_progress';
        } else {
            if (exec(system::getCmdSudo() . system::get('cmd_check') . '-Ec "python3\-venv"') < 1) {
                $return['state'] = 'nok';
//            } elseif (exec(realpath(dirname(__FILE__) . '/../../resources/pyrainbird'). ' && source env/bin/activate && pip3 list | grep -Ewc "pycryptodome|requests|DateTime|setuptools" && deactivate') < 4) {
//                $return['state'] = 'nok';
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
        return [
            'script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder(__CLASS__).'/dependency',
            'log' => log::getPathToLog(__CLASS__.'_update')
        ];
    }

    /**
     * @throws Exception
     */
    public static function stopIrrigationPlay(eqLogic $eqLogic){
        $apirainbird = new rainbirdApi($eqLogic->getConfiguration('iprainbird'), $eqLogic->getConfiguration('mdprainbird'));
        if ($apirainbird->get_rain_delay()[0] != 0){
            throw new Exception(__('L\'arrossage est arrêté', __FILE__));
        }
    }
}

class rainbirdCmd extends cmd {

    /**
     * Exécution des commandes via le dashbord
     *
     * @param array $_options
     * @throws Exception
     */
    public function execute($_options = []) {
        $apirainbird = new rainbirdApi($this->getEqLogic()->getConfiguration('iprainbird'), $this->getEqLogic()->getConfiguration('mdprainbird'));
//        $rainbird = new rainbird();
//        $rainbird->verifConnexion($apirainbird);

        for ($i = 1; $i <= $this->getEqLogic()->getConfiguration('nbzone'); $i++){
            if ($this->getLogicalId() === 'zonelancer'.$i){
                rainbird::stopIrrigationPlay($this->getEqLogic());
                $getintzonelancer = (int) substr($this->getLogicalId(), -1); // Récupération du dernier chiffre de la zone
                if ($getintzonelancer === 0){
                    $getintzonelancer = (int) substr($this->getLogicalId(), -2);
                }

                $gettimezone = (int) $this->getEqLogic()->getConfiguration('duree'.$i);
                if ($gettimezone !== 0){
                    $apirainbird->irrigate_zone($getintzonelancer, $gettimezone);
                }
                $this->getEqLogic()->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
                log::add('rainbird','debug','Lancement de l\'action irrigation pour la zone '.$getintzonelancer);
            }

            if ($this->getLogicalId() === 'zonestop'.$i){
                $apirainbird->stop_irrigation();
                $this->getEqLogic()->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
                log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation de la zone '.$i);
            }
        }

        if ($this->getLogicalId() === 'stopirrigation'){
            $apirainbird->stop_irrigation();
            $this->getEqLogic()->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
            log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation');
        }

        if ($this->getLogicalId() === 'setraindelay'){
            $getvaleurslider = $_options['slider'];
            $apirainbird->set_rain_delay($getvaleurslider);
            $this->getEqLogic()->checkAndUpdateCmd('getraindelay', $apirainbird->get_rain_delay()[0]);
            log::add('rainbird','debug','Lancement de l\'action pour arreter l\'irrigation sur un nombre de jours');
        }

        if ($this->getLogicalId() === 'zonetest'){
            rainbird::stopIrrigationPlay($this->getEqLogic());
            $apirainbird->test_zone($this->getEqLogic()->getConfiguration('dureetestzone'));
            log::add('rainbird','debug','Lancement de l\'action test des zones');
        }

        if ($this->getLogicalId() === 'waterbudget'){
            $ajustsaison = $_options['slider'];
            $waterbudget = $apirainbird->water_budget($ajustsaison);
            if (count($waterbudget) > 1){
                foreach ($waterbudget as $value){
                    log::add('rainbird','debug',$value);
                }
                throw new Exception(__('Vous avez pas accès à cette fonction pour votre RainBird', __FILE__));
            }
            $this->getEqLogic()->checkAndUpdateCmd('getwaterbudget',$ajustsaison);
        }

        if ($this->getLogicalId() === 'setprogram'){
            $program = $_options['slider'];
            $setprogram = $apirainbird->set_program($program);
            if (count($setprogram) > 1){
                foreach ($setprogram as $value){
                    log::add('rainbird','debug',$value);
                }
                throw new Exception(__('Vous avez pas accès à cette fonction pour votre RainBird', __FILE__));
            }
            $this->getEqLogic()->checkAndUpdateCmd('program',$program);
        }

        if ($this->getLogicalId() === 'advancezone'){
            $getadvancezone = $_options['slider'];
            $advancezone = $apirainbird->advance_zone($getadvancezone);
            if (count($advancezone) > 1){
                foreach ($advancezone as $value){
                    log::add('rainbird','debug',$value);
                }
                throw new Exception(__('Vous avez pas accès à cette fonction pour votre RainBird', __FILE__));
            }
            $this->getEqLogic()->checkAndUpdateCmd('getadvancezone',$getadvancezone);
        }

        $this->getEqLogic()->refreshWidget();
    }
}


