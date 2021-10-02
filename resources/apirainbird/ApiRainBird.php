<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class ApiRainBird
{
    private $iprainbird;
    private $mdprainbird;
    private $resource_path;

    public function __construct(string $iprainbird, string $mdprainbird)
    {
        $this->setIprainbird($iprainbird);
        $this->setMdprainbird($mdprainbird);
        $this->resource_path = realpath(dirname(__FILE__) . '/../../resources');
    }

    public function get_current_date(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_current_date.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_time(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_current_time.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }


    public function test_zone(int $zone){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 test_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$zone.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function stop_irrigation(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 stop_irrigation.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function irrigate_zone(int $zone, int $timer){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 irrigate_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" '.$zone.' '.$timer.'';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_delay(){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_rain_delay(string $days){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 set_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$days.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_zone_state(int $zone){
        $cmd = 'cd ' . $this->getResourcePath() . ' && python3 get_zone_state.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" '.$zone.'';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function getIprainbird() : string
    {
        return $this->iprainbird;
    }

    public function setIprainbird(string $iprainbird)
    {
        $this->iprainbird = $iprainbird;
    }

    public function getMdprainbird(): string
    {
        return $this->mdprainbird;
    }

    public function setMdprainbird(string $mdprainbird)
    {
        $this->mdprainbird = $mdprainbird;
    }

    public function getResourcePath(): string
    {
        return $this->resource_path;
    }

    public function setResourcePath(string $resource_path)
    {
        $this->resource_path = $resource_path;
    }

}