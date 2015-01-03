<?php

class ConvertTest extends PHPUnit_Framework_TestCase {

    public function testSimpleConversion() {
        $_GET['value'] = '1';
        $_GET['from'] = 'kilometer';
        $_GET['to'] = 'mile';

        $this->setOutputCallback(function($out) {
            $result = json_decode($out, true);

            $this->assertEquals(1, $result['from']['value']);
            $this->assertEquals('kilometer', $result['from']['unit']['key']);

            $this->assertEquals(0.621371, $result['to']['value']);
            $this->assertEquals('mile', $result['to']['unit']['key']);
        });

        include(dirname(__FILE__) . '/../src/convert.php');
    }

    public function testInvalidValue() {
        $_GET['value'] = '2.sd';
        $_GET['from'] = 'kilogram';
        $_GET['to'] = 'pound';

        $this->setOutputCallback(function($out) {
            $result = json_decode($out, true);

            $this->assertNotNull($result['error']);
        });

        include(dirname(__FILE__) . '/../src/convert.php');
    }

    public function testInvalidUnit() {
        $_GET['value'] = '1';
        $_GET['from'] = 'elephant';
        $_GET['to'] = 'lion';

        $this->setOutputCallback(function($out) {
            $result = json_decode($out, true);

            $this->assertNotNull($result['error']);
        });

        include(dirname(__FILE__) . '/../src/convert.php');
    }

    public function testInvalidKinds() {
        $_GET['value'] = '1';
        $_GET['from'] = 'hour';
        $_GET['to'] = 'meter';

        $this->setOutputCallback(function($out) {
            $result = json_decode($out, true);

            $this->assertNotNull($result['error']);
        });

        include(dirname(__FILE__) . '/../src/convert.php');
    }
}

?>
