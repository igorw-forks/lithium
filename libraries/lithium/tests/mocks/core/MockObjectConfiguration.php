<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\tests\mocks\core;

class MockObjectConfiguration extends \lithium\core\Object {
	protected $_testScalar = 'default';
	protected $_testArray = array('default');

	public function __construct($config = array()) {
		if (isset($config['autoConfig'])) {
			$this->_autoConfig = (array) $config['autoConfig'];
			unset($config['autoConfig']);
		}
		parent::__construct($config);
	}

	public function testScalar($value) {
		$this->_testScalar = 'called';
	}

	public function getConfig() {
		return array(
			'testScalar' => $this->_testScalar,
			'testArray' => $this->_testArray
		);
	}
}

?>