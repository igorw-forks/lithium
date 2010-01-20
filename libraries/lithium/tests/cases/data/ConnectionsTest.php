<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\tests\cases\data;

use \lithium\data\Connections;

class ConnectionsTest extends \lithium\test\Unit {

	public $config = array(
		'adapter' => 'MySql',
		'host' => 'localhost',
		'login' => '--user--',
		'password' => '--pass--',
		'database' => 'db'
	);

	protected $_preserved = array();

	public function setUp() {
		if (empty($this->_preserved)) {
			foreach (Connections::get() as $conn) {
				$this->_preserved[$conn] = Connections::get($conn, array('config' => true));
			}
		}
		Connections::reset();
	}

	public function tearDown() {
		foreach ($this->_preserved as $name => $config) {
			Connections::add($name, $config['type'], $config);
		}
	}

	public function testConnectionCreate() {
		$result = Connections::add('conn-test', 'database', $this->config);
		$expected = $this->config + array('type' => 'database');
		$this->assertEqual($expected, $result);

		$this->expectException('/mysql_get_server_info/');
		$this->expectException('/mysql_select_db/');
		$this->expectException('/mysql_connect/');
		$result = Connections::get('conn-test');
		$this->assertTrue($result instanceof \lithium\data\source\database\adapter\MySql);

		$result = Connections::add('conn-test-2', $this->config);
		$this->assertEqual($expected, $result);

		$this->expectException('/mysql_get_server_info/');
		$this->expectException('/mysql_select_db/');
		$this->expectException('/mysql_connect/');
		$result = Connections::get('conn-test-2');
		$this->assertTrue($result instanceof \lithium\data\source\database\adapter\MySql);
	}

	public function testConnectionGetAndReset() {
		Connections::add('conn-test', $this->config);
		Connections::add('conn-test-2', $this->config);
		$this->assertEqual(array('conn-test', 'conn-test-2'), Connections::get());

		$expected = $this->config + array(
			'type' => 'database', 'filters' => array(), 'strategies' => array()
		);
		$this->assertEqual($expected, Connections::get('conn-test', array('config' => true)));

		$this->assertNull(Connections::reset());
		$this->assertFalse(Connections::get());

		Connections::__init();
		$this->assertTrue(Connections::get());
	}

	public function testConnectionAutoInstantiation() {
		Connections::add('conn-test', $this->config);
		Connections::add('conn-test-2', $this->config);

		$this->expectException('/mysql_get_server_info/');
		$this->expectException('/mysql_select_db/');
		$this->expectException('/mysql_connect/');
		$result = Connections::get('conn-test');
		$this->assertTrue($result instanceof \lithium\data\source\database\adapter\MySql);

		$result = Connections::get('conn-test');
		$this->assertTrue($result instanceof \lithium\data\source\database\adapter\MySql);

		$this->assertNull(Connections::get('conn-test-2', array('autoCreate' => false)));
	}

	public function testInvalidConnection() {
		$this->assertNull(Connections::get('conn-invalid'));
	}

	public function testStreamConnection() {
		$config = array(
			'socket' => 'Stream',
			'host' => 'localhost',
			'login' => 'root',
			'password' => '',
			'port' => '80'
		);

		Connections::add('stream-test', 'Http', $config);
		$result = Connections::get('stream-test');
		$this->assertTrue($result instanceof \lithium\data\source\Http);
	}
/*
	public function testErrorExceptions() {
		$config = array(
			'adapter' => 'None',
			'type' => 'Error'
		);
		Connections::add('NoConnection', 'Error', $config);
		$result = false;
		try {
			Connections::get('NoConnection');
		} catch(Exception $e) {
			$result = true;
		}
		$this->assertTrue($result, 'Exception is not thrown');
	}
*/
}

?>