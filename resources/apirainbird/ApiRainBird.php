<?php

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class ApiRainBird
{
    private $iprainbird;
    private $mdprainbird;
    private $resource_path;

    public function __construct($iprainbird, $mdprainbird)
    {
        $this->setIprainbird($iprainbird);
        $this->setMdprainbird($mdprainbird);
        $this->resource_path = realpath(dirname(__FILE__) . '/../../resources');
    }

    /**
     * @return mixed
     */
    public function get_current_date(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_current_date.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @param int $zone
     * @return mixed
     */
    public function test_zone(int $zone){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 test_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$zone.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @return mixed
     */
    public function stop_irrigation(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 stop_irrigation.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @param int $zone
     * @param $timer
     * @return mixed
     */
    public function irrigate_zone(int $zone, $timer){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 irrigate_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" '.$zone.' '.$timer.'';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @return mixed
     */
    public function get_rain_delay(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @param $days
     * @return mixed
     */
    public function set_rain_delay($days){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 set_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$days.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @param int $zone
     * @return mixed
     */
    public function get_zone_state(int $zone){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_zone_state.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" '.$zone.'';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    /**
     * @return mixed
     */
    public function getIprainbird()
    {
        return $this->iprainbird;
    }

    /**
     * @param mixed $iprainbird
     */
    public function setIprainbird($iprainbird)
    {
        $this->iprainbird = $iprainbird;
    }

    /**
     * @return mixed
     */
    public function getMdprainbird()
    {
        return $this->mdprainbird;
    }

    /**
     * @param mixed $mdprainbird
     */
    public function setMdprainbird($mdprainbird)
    {
        $this->mdprainbird = $mdprainbird;
    }

    /**
     * @return false|string
     */
    public function getResourcePath()
    {
        return $this->resource_path;
    }

    /**
     * @param false|string $resource_path
     */
    public function setResourcePath($resource_path)
    {
        $this->resource_path = $resource_path;
    }

}