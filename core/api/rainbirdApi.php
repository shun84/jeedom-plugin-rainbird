<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class rainbirdApi
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
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_current_date.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_time(){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_current_time.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function test_zone(int $zone){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/test_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$zone.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function stop_irrigation(){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/stop_irrigation.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function irrigate_zone(int $zone, int $timer){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/irrigate_zone.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$zone.'" "'.$timer.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_delay(){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_rain_delay(string $days){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/set_rain_delay.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" "'.$days.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_zone_state(int $zone){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_zone_state.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '" '.$zone;
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