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
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_model_and_version ' . $this->iprainbird . ' ' . $this->mdprainbird;
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
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_available_stations ' . $this->iprainbird . ' ' . $this->mdprainbird;
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
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_serial_number ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_date(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_current_date ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_current_time(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_current_time ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function test_zone(int $zone): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py test_zone ' . $zone . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function stop_irrigation(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py stop_irrigation ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function irrigate_zone(int $zone, int $timer): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py irrigate_zone ' . $zone . ' ' . $timer . ' '.$this->iprainbird.' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_delay(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_rain_delay ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_rain_delay(string $days): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py set_rain_delay ' . $days . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_zone_state(int $zone): array
    {
        $cmd = $this->resource_path.'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_zone_state ' . $zone . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function water_budget(int $budget): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py water_budget ' . $budget . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function set_program(int $numbprog): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py set_program ' . $numbprog . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function advance_zone(int $numbzone): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py advance_zone ' . $numbzone . ' ' . $this->iprainbird . ' '.$this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }

    public function get_rain_sensor_state(): array
    {
        $cmd = $this->resource_path .'/pyrainbird/env/bin/python3 ' . $this->resource_path. '/rainbird_tool.py get_rain_sensor_state ' . $this->iprainbird . ' ' . $this->mdprainbird;
        exec($cmd . ' 2>&1', $output);

        return $output;
    }
}