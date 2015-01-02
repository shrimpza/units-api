<?php

/*
$api = new UnitsAPI();
print_r($api->convert(5, 'kilometer', 'mile'));
*/

class UnitsAPI {

    private $units;

    public function UnitsAPI() {
        $this->units = $this->loadUnits();
    }

    function convert($value, $from, $to) {
        // Only accept numeric values.
        if (!is_numeric($value)) {
            die('Unit conversion value must be numeric.');
        }

        // Check to see if the unit key was found in the array.
        if (!isset($this->units[$from]) || !isset($this->units[$to])) {
            die('Cannot find the specified measurement units.');
        }

        // Only convert with like kinds
        if ($this->units[$from]['kind'] != $this->units[$to]['kind']) {
            die('Cannot convert between different kinds of measurement units.');
        }

        // Execute the conversion factors differently based on the kind.  For example, temperature needs to be executed differently.
        switch ($this->units[$from]['kind']) {
            case 'temperature':
                $result = _unitsapi_convert_temperature($value, $this->units[$to]['factor'][$from]);
                break;
            default:
                $from_si = $this->units[$from]['factors']['default'];
                $to_si = $this->units[$to]['factors']['default'];
                $from_convert = $value * $from_si;
                $result = $from_convert / $to_si;
        }

        // Round to the 6 spaces after the decimal.
        $result = round($result, 6);

        $result_array = array(
            'value' => $value,
            'result' => $result,
            'from' => $this->units[$from],
            'to' => $this->units[$to],
        );

        return $result_array;
    }

    private function loadUnits() {
        $units = array();

        $groupedUnits = json_decode(file_get_contents(dirname(__FILE__) . '/units.json'), true);

        foreach ($groupedUnits as $kind => $group) {
            foreach ($group as $unit) {
                $unit['kind'] = $kind;
                $units[$unit['key']] = $unit;
            }
        }

        return $units;
    }
}

?>
