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

}

?>
