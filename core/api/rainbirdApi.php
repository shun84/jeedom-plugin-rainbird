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
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_model_and_version.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        if (substr($output[0],0, 5) === 'model'){
            $modelandversion = explode(',',$output[0]);
            $model = explode(':',$modelandversion[0]);
            $version = explode(':',$modelandversion[1]);

            return [
                $model[1],
                $version[1]
            ];
        }

        return $output;
    }

    public function get_available_stations(){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_available_stations.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        if (substr($output[0],0, 9) === 'available'){
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

        return $output;
    }

    public function get_serial_number(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_serial_number.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_date(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_current_date.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_time(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_current_time.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function test_zone(int $zone){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/test_zone.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$zone.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function stop_irrigation(){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/stop_irrigation.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function irrigate_zone(int $zone, int $timer){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/irrigate_zone.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$zone.'" "'.$timer.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_delay(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_rain_delay.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_rain_delay(string $days){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/set_rain_delay.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$days.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_zone_state(int $zone): array
    {
        $cmd = $this->resource_path.'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_zone_state.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" '.$zone;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function water_budget(int $budget){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/water_budget.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$budget.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_program(int $numbprog){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/set_program.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$numbprog.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function advance_zone(int $numbzone){
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/advance_zone.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '" "'.$numbzone.'"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_sensor_state(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/get_rain_sensor_state.py "' . $this->iprainbird . '" "' . $this->mdprainbird . '"';
        exec($cmd . ' 2>&1', $output);

        return $output;
    }
}