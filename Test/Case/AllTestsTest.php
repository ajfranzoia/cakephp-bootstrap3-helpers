<?php
/**
 * AllTestsTest file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/cakephp-bootstrap3-helpers
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * AllTestsTest class
 */
class AllTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Bs3Helpers tests');
		$path = CakePlugin::path('Bs3Helpers') . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}
}
