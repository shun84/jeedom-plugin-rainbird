<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class rainbirdApi
{
    private $iprainbird;
    private $mdprainbird;
    private $resource_path;

    public function __construct(string $iprainbird, string $mdprainbird)
    {
        $this->iprainbird = $iprainbird;
        $this->mdprainbird = $mdprainbird;
        $this->resource_path = realpath(dirname(__FILE__) . '/../../resources');
    }

    public function get_model_and_version(): array
    {
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_model_and_version.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        $modelandversion = explode(',',$output[0]);
        $model = explode(':',$modelandversion[0]);
        $version = explode(':',$modelandversion[1]);

        return [$model[1],$version[1]];
    }

    public function get_available_stations(){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_available_stations.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        $availablestations = explode(',',$output[0]);
        $stations = explode(':',$availablestations[0]);
        $binstations = base_convert(trim($stations[1]), 16, 2);
        $nbstation = [];
        $i = 1;
        foreach (str_split($binstations) as $binstation){
            if ($binstation === '1'){
                $nbstation[] = $i++;
            }
        }

        return end($nbstation);
    }

    public function get_serial_number(){
        $cmd = $this->getResourcePath() .'/pyrainbird/env/bin/python3 ' . $this->getResourcePath(). '/get_serial_number.py "' . $this->getIprainbird() . '" "' . $this->getMdprainbird() . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
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

    public function getMdprainbird(): string
    {
        return $this->mdprainbird;
    }

    public function getResourcePath(): string
    {
        return $this->resource_path;
    }
}