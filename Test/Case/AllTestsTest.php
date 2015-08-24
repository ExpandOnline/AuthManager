<?php
/**
 * Custom test suite to execute all tests
 */

class AllTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All tests');
		$dirs = array(
			'Controller',
			'Model',
			array('Model', 'Behavior'),
			'View',
			array('View', 'Helper'),
			'Shell',
			'Lib',
		);

		$path = realpath(dirname(__FILE__)) . DS;
		self::addTestDirectories($suite, $path, $dirs);
		return $suite;
	}

	public static function addTestDirectories(CakeTestSuite $suite, $path, array $dirs) {
		foreach ($dirs as $dir) {
			$suite->addTestDirectory($path . (is_array($dir) ? implode(DS, $dir) : $dir) . DS);
		}
	}

}