<?php

$api = new UnitsAPI();
print_r($api->convert(5, 'kilometer', 'mile'));

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

        $unitFrom = $this->units[$from];
        $unitTo = $this->units[$to];

        // Only convert with like kinds
        if ($unitFrom['kind'] != $unitTo['kind']) {
            die('Cannot convert between different kinds of measurement units.');
        }

        // Execute the conversion factors differently based on the kind.  For example, temperature needs to be executed differently.
        switch ($unitFrom['kind']) {
            case 'temperature':
                die('TODO temperature');
                //$result = _unitsapi_convert_temperature($value, $unitTo['factor'][$from]);
                break;
            default:
                $from_si = $unitFrom['factors']['default'];
                $to_si = $unitTo['factors']['default'];
                $from_convert = $value * $from_si;
                $result = $from_convert / $to_si;
        }

        // Round to the 6 spaces after the decimal.
        $result = round($result, 6);

        return array(
            'from' => array(
                'value' => $value,
                'unit' => $unitFrom
            ),
            'to' => array(
                'value' => $result,
                'unit' => $unitTo
            )
        );
    }

    private function loadUnits() {
        $units = array();

        $groupedUnits = json_decode(file_get_contents(dirname(__FILE__) . '/units.json'), true);

        foreach ($groupedUnits as $kind => $group) {
            foreach ($group as $unit) {
                $unit['kind'] = $kind;
                foreach ($unit['factors'] as $k => $factor) {
                    $unit['factors'][$k] = str_replace(' ', '', $unit['factors'][$k]);
                    $unit['factors'][$k] = str_replace(array('—', '−', '–'), '-', $unit['factors'][$k]);
                }
                $units[$unit['key']] = $unit;
            }
        }

        return $units;
    }
}

?>
