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
require_once __DIR__ . '/../../core/api/newApi.php';

class rainbird extends eqLogic {
	public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);


    /* ***********************Methode static*************************** */
    public static function cron() {
        foreach (rainbird::byType('rainbird') as $eqLogic) {
            if ($eqLogic->getIsEnable() == 1) {
                $eqLogic->updateRainbird();
            }
        }
    }

    public static function pluginGenericTypes(): array
    {
        return [
            'RAINBIRD_STOPRAIN' => [
                'name' => __('Arreter l\'irrigation',__FILE__),
                'familyid' => 'rainbird',
                'family' => __('Plugin RainBird',__FILE__),
                'type' => 'Action',
                'subtype' => ['other']
            ],
            'RAINBIRD_STARTRAIN' => [
                'name' => __('Lancer l\'irrigation',__FILE__),
                'familyid' => 'rainbird',
                'family' => __('Plugin RainBird',__FILE__),
                'type' => 'Action',
                'subtype' => ['other']
            ],
            'RAINBIRD_GETRAIN' => [
                'name' => __('Récupération l\'irrigation',__FILE__),
                'familyid' => 'rainbird',
                'family' => __('Plugin RainBird',__FILE__),
                'type' => 'Info',
                'subtype' => ['binary']
            ]
        ];
    }

    /* *********************Méthodes d'instance************************* */
    public function updateRainbird() {
        $apirainbird = new rainbirdApi($this->getConfiguration('iprainbird'), $this->getConfiguration('mdprainbird'));

        $this->checkAndUpdateCmd('daterainbird', $apirainbird->get_current_date()[0]);
        $this->checkAndUpdateCmd('timerainbird', $apirainbird->get_current_time()[0]);
        $this->checkAndUpdateCmd('getraindelay', $apirainbird->get_rain_delay()[0]);

        for ($i = 1; $i <= $this->getConfiguration('nbzone'); $i++){
            $this->checkAndUpdateCmd('getzonelancer'.$i, $apirainbird->get_zone_state($i)[0]);
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
            $zonelancer = $this->getCmd(null, 'zonelancer'.$i);
            if (!is_object($zonelancer)) {
                $zonelancer = new rainbirdCmd();
            }
            $zonelancer->setName(__('Zone Lancer '.$i, __FILE__));
            $zonelancer->setLogicalId('zonelancer'.$i);
            $zonelancer->setEqLogic_id($this->getId());
            $zonelancer->setType('action');
            $zonelancer->setSubType('other');
            $zonelancer->setGeneric_type('RAINBIRD_STARTRAIN');
            $zonelancer->save();

            $getzonelancer = $this->getCmd(null, 'getzonelancer'.$i);
            if (!is_object($getzonelancer)) {
                $getzonelancer = new rainbirdCmd();
            }
            $getzonelancer->setName(__('Récupération Zone '.$i, __FILE__));
            $getzonelancer->setLogicalId('getzonelancer'.$i);
            $getzonelancer->setEqLogic_id($this->getId());
            $getzonelancer->setType('info');
            $getzonelancer->setSubType('binary');
            $getzonelancer->setGeneric_type('RAINBIRD_GETRAIN');
            $getzonelancer->save();

            $zonestop = $this->getCmd(null, 'zonestop'.$i);
            if (!is_object($zonestop)) {
                $zonestop = new rainbirdCmd();
            }
            $zonestop->setName(__('Arreter la zone '.$i, __FILE__));
            $zonestop->setLogicalId('zonestop'.$i);
            $zonestop->setEqLogic_id($this->getId());
            $zonestop->setType('action');
            $zonestop->setSubType('other');
            $zonestop->setGeneric_type('RAINBIRD_STOPRAIN');
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
            $this->setDisplay("height","220px");
        }

        if ($this->getConfiguration('nbzone') > "4" && $this->getConfiguration('nbzone') < "9"){
            $this->setDisplay("height","325px");
        }

        if ($this->getConfiguration('nbzone') > "8" && $this->getConfiguration('nbzone') < "13"){
            $this->setDisplay("height","430px");
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
            $daterainbird = new rainbirdCmd();
        }
        $daterainbird->setName(__('Date Rainbird', __FILE__));
        $daterainbird->setLogicalId('daterainbird');
        $daterainbird->setEqLogic_id($this->getId());
        $daterainbird->setType('info');
        $daterainbird->setSubType('string');
        $daterainbird->save();

        $timerainbird = $this->getCmd(null, 'timerainbird');
        if (!is_object($timerainbird)) {
            $timerainbird = new rainbirdCmd();
        }
        $timerainbird->setName(__('Time Rainbird', __FILE__));
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
        $stopirrigation->setGeneric_type('RAINBIRD_STOPRAIN');
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

        $zonetest = $this->getCmd(null, 'zonetest');
        if (!is_object($zonetest)) {
            $zonetest = new rainbirdCmd();
        }
        $zonetest->setName(__('Zone Test', __FILE__));
        $zonetest->setLogicalId('zonetest');
        $zonetest->setEqLogic_id($this->getId());
        $zonetest->setType('action');
        $zonetest->setSubType('other');
        $zonetest->setGeneric_type('RAINBIRD_STARTRAIN');
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
        $return = [];
        $return['log'] = log::getPathToLog(__CLASS__.'_update');
        $return['progress_file'] = jeedom::getTmpFolder(__CLASS__).'/dependency';
        if (file_exists(jeedom::getTmpFolder(__CLASS__).'/dependency')) {
            $return['state'] = 'in_progress';
        } else {
            if (exec(system::getCmdSudo() . system::get('cmd_check') . '-Ec "python3\-venv"') < 1) {
                $return['state'] = 'nok';
//            } elseif (exec(system::getCmdSudo() . 'pip3 list | grep -Ewc "pycryptodome|requests|DateTime|PyYAML|setuptools"') < 5) {
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
        return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder(__CLASS__).'/dependency', 'log' => log::getPathToLog(__CLASS__.'_update'));
    }

    public function zoneHtml(int $i, string $version): string
    {
        $replacezone = [];

        $getzonelancer = $this->getCmd(null, 'getzonelancer'.$i);
        $replacezone['#id#'] = is_object($getzonelancer) ? $getzonelancer->getId() : '';
        $replacezone['#state#'] = is_object($getzonelancer) ? $getzonelancer->execCmd() : '';
        $replacezone['#_icon_on_#'] = '<i class=\'fas fa-tint fa-3x\'></i>';
        $replacezone['#_icon_off_#'] = '<i class=\'fas fa-tint-slash fa-3x\'></i>';

        $getnomzone = $this->getConfiguration('nomzone'.$i);
        $replacezone['#name_display#'] = $getnomzone;

        $gettimezone = $this->getConfiguration('duree'.$i);
        $replacezone['#gettimezone#'] = $gettimezone;

        $lancerzone = $this->getCmd(null, 'zonelancer'.$i);
        $replacezone['#zonelancer#'] = is_object($lancerzone) ? $lancerzone->getId() : '';

        $stopzone = $this->getCmd(null, 'zonestop'.$i);
        $replacezone['#zonestop#'] = is_object($stopzone) ? $stopzone->getId() : '';

        return template_replace($replacezone, getTemplate('core', $version, 'zone', __CLASS__));
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
        $version = jeedom::versionAlias($_version);

        $daterainbird = $this->getCmd(null, 'daterainbird');
        $replace['#daterainbird#'] = is_object($daterainbird) ? $daterainbird->execCmd() : '';

        $timerainbird = $this->getCmd(null, 'timerainbird');
        $timerainbird = substr(is_object($timerainbird) ? $timerainbird->execCmd() : '', 0,-3);
        $replace['#timerainbird#'] = $timerainbird;

        $stopirrigation = $this->getCmd(null, 'stopirrigation');
        $replace['#stopirrigation#'] = is_object($stopirrigation) ? $stopirrigation->getId() : '';

        $replaceraindelay = [];

        $setraindelay = $this->getCmd(null, 'setraindelay');
        $replaceraindelay['#id#'] = is_object($setraindelay) ? $setraindelay->getId() : '';
        $replaceraindelay['#maxValue#'] = is_object($setraindelay) ? $setraindelay->getConfiguration('maxValue') : '';
        $replaceraindelay['#minValue#'] = is_object($setraindelay) ? $setraindelay->getConfiguration('minValue') : '';

        $getraindelay = $this->getCmd(null, 'getraindelay');
        $replaceraindelay['#name_display#'] = is_object($getraindelay) ? $getraindelay->getName() : '';
        $replaceraindelay['#uid#'] = is_object($getraindelay) ? $getraindelay->getId() : '';
        $replaceraindelay['#state#'] = is_object($getraindelay) ? $getraindelay->execCmd() : '';
        $replaceraindelay['#unite#'] = is_object($getraindelay) ? $getraindelay->getUnite() : '';

        $replace['#raindelay#'] = template_replace($replaceraindelay, getTemplate('core', $version, 'raindelay', __CLASS__));

        $getdureetestzone = $this->getConfiguration('dureetestzone');
        $replace['#getdureetestzone#'] = $getdureetestzone;

        $zonetest = $this->getCmd(null, 'zonetest');
        $replace['#zonetest#'] = is_object($zonetest) ? $zonetest->getId() : '';

        $getnbzone = $this->getConfiguration('nbzone');

        $classandleft = '<div class="col-md-6" style="float: left">';
        $closediv = '</div>';

        if ($version === 'mobile'){
            if ($getnbzone < "3"){
                $replace['#height#'] = '390px';
                for ($i = 1; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
            }

            if ($getnbzone > "2" && $getnbzone < "5"){
                $replace['#height#'] = '515px';
                for ($i = 1; $i <= 2; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 3; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
            }

            if ($getnbzone > "4" && $getnbzone < "7"){
                $replace['#height#'] = '625px';
                for ($i = 1; $i <= 2; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 3; $i <= 4; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 5; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
            }

            if ($getnbzone > "6" && $getnbzone < "9"){
                $replace['#height#'] = '755px';
                for ($i = 1; $i <= 2; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 3; $i <= 4; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 5; $i <= 6; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 7; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $classandleft;
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
            }

            if ($getnbzone > "8" && $getnbzone < "11") {
                $replace['#height#'] = '885px';
                for ($i = 1; $i <= 2; $i++) {
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i, $version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 3; $i <= 4; $i++) {
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i, $version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 5; $i <= 6; $i++) {
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i, $version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 7; $i <= 8; $i++) {
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i, $version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 9; $i <= $getnbzone; $i++) {
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i, $version);
                    $replace['#zone#'] .= $closediv;
                }
            }

            if ($getnbzone > "10" && $getnbzone < "13"){
                $replace['#height#'] = '995px';
                for ($i = 1; $i <= 2; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 3; $i <= 4; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 5; $i <= 6; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 7; $i <= 8; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 9; $i <= 10; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
                for ($i = 11; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $classandleft;
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    $replace['#zone#'] .= $closediv;
                }
            }
        }else{
            if ($getnbzone < "5"){
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                    for ($i = 1; $i <= $getnbzone; $i++){
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    }
                $replace['#zone#'] .= $closediv;
            }

            if ($getnbzone > "4" && $getnbzone < "9"){
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                    for ($i = 1; $i <= 4; $i++){
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    }
                $replace['#zone#'] .= $closediv;
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                    for ($i = 5; $i <= $getnbzone; $i++){
                        $replace['#zone#'] .= $this->zoneHtml($i,$version);
                    }
                $replace['#zone#'] .= $closediv;
            }

            if ($getnbzone > "8" && $getnbzone < "13"){
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                for ($i = 1; $i <= 4; $i++){
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                }
                $replace['#zone#'] .= $closediv;
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                for ($i = 5; $i <= 8; $i++){
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                }
                $replace['#zone#'] .= $closediv;
                $replace['#zone#'] .= '<div style="margin-top: 10px; height: 100px">';
                for ($i = 9; $i <= $getnbzone; $i++){
                    $replace['#zone#'] .= $this->zoneHtml($i,$version);
                }
                $replace['#zone#'] .= $closediv;
            }
        }

        $html = $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'rainbird', 'rainbird')));
        cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
        return $html;
    }
}

class rainbirdCmd extends cmd {

    public static $_widgetPossibility = ['custom' => false];

    /**
     * Exécution des commandes via le dashbord
     *
     * @param array $_options
     * @throws Exception
     */
    public function execute($_options = []) {
        $apirainbird = new rainbirdApi($this->getEqLogic()->getConfiguration('iprainbird'), $this->getEqLogic()->getConfiguration('mdprainbird'));
        if ($apirainbird->get_current_date()[0] == 'None'){
            throw new Exception(__('Vérifier votre login et mot de passe ou l\'application rainbird lancé sur votre Téléphone', __FILE__));
        }

        for ($i = 1; $i <= $this->getEqLogic()->getConfiguration('nbzone'); $i++){
            if ($this->getLogicalId() === 'zonelancer'.$i){
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
//            $test = new newApi('192.168.1.12','Niafron84Rd');
//            $test->encrypt('{"id":1654522644,"jsonrpc":"2.0","method":"tunnelSip","params":{"data":"10","length":1}}','Niafron84Rd');
//            $test->decrypt('\xa6\xaf\xf0W/\xa8\x13#\xd5\xd3\x96\x99\xea\x8cf\xf2\x9a7\x83\x85\xea\xdd\xa8\xbbe\xa2\xda\xb9\xa0F\x8a\xe2\xa9\x1b\xb0A3L\xb3\x10\xbd\n\xa4=\xaeE\\T:<\xc9\x1c\xcflD\xe4o\x95\x1d}\xf6\x8a\xb6\xc9c\xb2\x1f\xd6[H\xa0\x06\xb8>)\x051\xcd3[\x08\x163{\xadv\xdc\xdbH`\x10\x10E:\xc6[e\xdbt\xa5l\xf3I\xf1\x87a\xc4\xe7$\xbb\xd7\x88\xb2cb\x9a\xfc"\xba8\x99\xa1\x0f\x02\xb0_+y','Niafron84Rd');
//            $test->addPadding('{"id":1654376764,"jsonrpc":"2.0","method":"tunnelSip","params":{"data":"10","length":1}} ');
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
            $apirainbird->test_zone($this->getEqLogic()->getConfiguration('dureetestzone'));
            log::add('rainbird','debug','Lancement de l\'action test des zones');
        }

        $this->getEqLogic()->refreshWidget();
    }
}


