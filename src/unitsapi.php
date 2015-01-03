<?php

class UnitsAPI {

    private $units;

    public function UnitsAPI() {
        $this->units = $this->loadUnits();
    }

    public function units() {
        return $this->units;
    }

    public function convert($value, $from, $to) {
        // Only accept numeric values.
        if (!is_numeric($value)) {
            throw new ConversionException('Unit conversion value must be numeric.');
        }

        $unitFrom = $this->findUnit($from);
        $unitTo = $this->findUnit($to);

        // Check to see if the unit key was found in the array.
        if (!isset($unitFrom) || !isset($unitTo)) {
            throw new ConversionException('Cannot find the specified measurement units.');
        }

        // Only convert with like kinds
        if ($unitFrom['kind'] != $unitTo['kind']) {
            throw new ConversionException('Cannot convert between different kinds of measurement units.');
        }

        // Execute the conversion factors differently based on the kind.  For example, temperature needs to be executed differently.
        switch ($unitFrom['kind']) {
            case 'temperature':
                $result = $this->convertTemperature($value, $unitTo['factors'][$from]);
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

    private function convertTemperature($value, $factor) {

        // Security note: Execute a variety of checks to make sure the equation is not something mischievous
        $equation = str_replace(array('t/°C', 't/°F', 'T/K'), $value, $factor);
        $equation = preg_replace('/\s+/', '', $equation);

        $number = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?)';
        $operators = '[\/*\^\+-,]';
        $regexp = '/^([+-]?('.$number.'|'.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/';

        if (preg_match($regexp, $equation)) {
            eval('$result = '.$equation.';');
        } else {
            throw new ConversionException("Invalid temperature conversion equation");
        }

        return $result;
    }

    private function findUnit($unit) {
        $result = array_key_exists($unit, $this->units) ? $this->units[$unit] : null;

        if (!isset($result)) {
            foreach ($this->units as $k => $v) {
                if ((strtolower($v['singular']) == strtolower($unit))
                    || (strtolower($v['plural']) == strtolower($unit))
                    || (strtolower($v['symbol']) == strtolower($unit))) {
                    $result= $v;
                    break;
                }
            }
        }

        return $result;
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

class ConversionException extends Exception {

}

?>
