<?php
require __DIR__ . '/../vendor/autoload.php';

/*
 * Problème de config de PHPUnit avec Zend Studio et Composer
 * TODO : étudier la vidéo suivante
 * https://www.youtube.com/watch?v=84j61_aI0q8
*/

use \Logistic as Logistic;

/**
 * DB2Layer test case.
 */
class DB2LayerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var DB2Layer
     */
    private $dB2Layer;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated DB2LayerTest::setUp()

        /*
         * Valeurs forcées pour le test unitaire
         */
        $system = '172.30.14.2';
        $user = 'GJARRIGE' ;
        $password = 'JARRIGEG' ;

        $options = array() ;
        $options['i5_naming'] = true ;
        $options['i5_libl'] = 'GJARRIGE' ;
        $options['DB2_ATTR_CASE'] = 'LOWER'  ;

        $persistent = false ;
        $this->dB2Layer = new Logistic\DB2Layer($system, $user, $password, $options, $persistent);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DB2LayerTest::tearDown()
        $this->dB2Layer = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    public function testSelect1() {
        $sql = 'select * from sysibm/sysdummy1' ;
        $result = $this->dB2Layer->selectOne($sql) ;
        $this->assertArrayHasKey('IBMREQD') ;
        $this->assertEquals($result[IBMREQD], 'Y');

    }
}
