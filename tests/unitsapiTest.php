<?php

require_once(dirname(__FILE__) . '/../src/unitsapi.php');

class UnitsAPITest extends PHPUnit_Framework_TestCase {

    private $api;

    protected function setUp() {
        $this->api = new UnitsAPI();
    }

    public function testSimpleConversion() {
        $result = $this->api->convert(1, 'kilometer', 'mile');

        $this->assertEquals(1, $result['from']['value']);
        $this->assertEquals('kilometer', $result['from']['unit']['key']);

        $this->assertEquals(0.621371, $result['to']['value']);
        $this->assertEquals('mile', $result['to']['unit']['key']);
    }

    public function testTemperatureConversion() {
        $result = $this->api->convert(10, 'kelvin', 'fahrenheit');

        $this->assertEquals(10, $result['from']['value']);
        $this->assertEquals('kelvin', $result['from']['unit']['key']);

        $this->assertEquals(-441.670, $result['to']['value']);
        $this->assertEquals('fahrenheit', $result['to']['unit']['key']);
    }

    /**
     * @expectedException ConversionException
     */
    public function testInvalidValue() {
        $this->api->convert('rhino', 'kilogram', 'pound');
    }
    /**
     * @expectedException ConversionException
     */
    public function testInvalidUnit() {
        $this->api->convert(1, 'elephant', 'lion');
    }

    /**
     * @expectedException ConversionException
     */
    public function testInvalidKinds() {
        $this->api->convert(1, 'hour', 'meter');
    }
}

?>
