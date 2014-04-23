<?php
/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Codaxis (http://codaxis.com)
 * @author        augusto-cdxs (https://github.com/augusto-cdxs/
 * @link          https://github.com/Codaxis/parsley-helper ParsleyHelper
 * @package       ParsleyHelper.Test.Case
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * AllTestsTest class
 *
 * This test group will run ParsleyFormHelperTest and ParsleyFormTraitTest only 
 * if version greather than 5.4.
 *
 * @package       ParsleyHelper.Test.Case
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