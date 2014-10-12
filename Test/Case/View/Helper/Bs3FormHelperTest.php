<?php
/**
 * Bs3FormHelperTest file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Codaxis (http://codaxis.com)
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/parsley-helper
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Model', 'Model');
App::uses('Security', 'Utility');
App::uses('CakeRequest', 'Network');
App::uses('HtmlHelper', 'View/Helper');
App::uses('Bs3FormHelper', 'Bs3Helpers.View/Helper');
App::uses('FormHelper', 'Helpers.View/Helper');
App::uses('Router', 'Routing');

/**
 * ContactTestController class
 */
class ContactTestController extends Controller {

/**
 * uses property
 *
 * @var mixed null
 */
	public $uses = null;
}

/**
 * Contact class
 */
class Contact extends CakeTestModel {

/**
 * useTable property
 *
 * @var boolean
 */
	public $useTable = false;

/**
 * Default schema
 *
 * @var array
 */
	protected $_schema = array(
		'id' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'),
		'name' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'email' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'phone' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'gender' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '1'),
		'active' => array('type' => 'boolean', 'null' => '', 'default' => ''),
		'password' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'published' => array('type' => 'date', 'null' => true, 'default' => null, 'length' => null),
		'created' => array('type' => 'date', 'null' => '1', 'default' => '', 'length' => ''),
		'updated' => array('type' => 'datetime', 'null' => '1', 'default' => '', 'length' => null),
		'age' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => null)
	);

/**
 * validate property
 *
 * @var array
 */
	public $validate = array(
		'non_existing' => array(),
		'idontexist' => array(),
		'imrequired' => array('rule' => array('between', 5, 30), 'allowEmpty' => false),
		'imrequiredonupdate' => array('notEmpty' => array('rule' => 'alphaNumeric', 'on' => 'update')),
		'imrequiredoncreate' => array('required' => array('rule' => 'alphaNumeric', 'on' => 'create')),
		'imrequiredonboth' => array(
			'required' => array('rule' => 'alphaNumeric'),
		),
		'string_required' => 'notEmpty',
		'imalsorequired' => array('rule' => 'alphaNumeric', 'allowEmpty' => false),
		'imrequiredtoo' => array('rule' => 'notEmpty'),
		'required_one' => array('required' => array('rule' => array('notEmpty'))),
		'imnotrequired' => array('required' => false, 'rule' => 'alphaNumeric', 'allowEmpty' => true),
		'imalsonotrequired' => array(
			'alpha' => array('rule' => 'alphaNumeric', 'allowEmpty' => true),
			'between' => array('rule' => array('between', 5, 30)),
		),
		'imalsonotrequired2' => array(
			'alpha' => array('rule' => 'alphaNumeric', 'allowEmpty' => true),
			'between' => array('rule' => array('between', 5, 30), 'allowEmpty' => true),
		),
		'imnotrequiredeither' => array('required' => true, 'rule' => array('between', 5, 30), 'allowEmpty' => true),
		'iamrequiredalways' => array(
			'email' => array('rule' => 'email'),
			'rule_on_create' => array('rule' => array('maxLength', 50), 'on' => 'create'),
			'rule_on_update' => array('rule' => array('between', 1, 50), 'on' => 'update'),
		),
		'boolean_field' => array('rule' => 'boolean')
	);
}

/**
 * Bs3FormHelperTest class
 */
class Bs3FormHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Configure::write('Config.language', 'eng');
		Configure::write('App.base', '');
		Configure::delete('Asset');
		$this->Controller = new ContactTestController();
		$this->View = new View($this->Controller);

		CakePlugin::load('Bs3Helpers', array('bootstrap' => true));
		$this->Form = new Bs3FormHelper($this->View);
		$this->Form->request = new CakeRequest('contacts/add', false);
		$this->Form->request->here = '/contacts/add';
		$this->Form->request['action'] = 'add';
		$this->Form->request->webroot = '';
		$this->Form->request->base = '';

		ClassRegistry::addObject('Contact', new Contact());
		$this->Contact = ClassRegistry::init('Contact');

		$this->oldSalt = Configure::read('Security.salt');
		Configure::write('Security.salt', 'foo!');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Form->Html, $this->Form, $this->Controller, $this->View);
		Configure::write('Security.salt', $this->oldSalt);
	}

/**
 * testConfiguration method
 *
 * @return void
 */
	public function testConfiguration() {
		// Test default options
		$this->Form->create('Contact', array());
		$result = $this->Form->formOptions;
		$expected = array(
			'role' => 'form',
			'custom' => array(
				'submitDiv' => null,
				'submitButton' => null,
			)
		);
		$this->assertEquals($result, $expected);

		$result = $this->Form->inputOptions;
		$expected = array(
			'class' => 'form-control',
			'div' => array(
				'class' => 'form-group'
			),
			'label' => array(
				'class' => 'control-label'
			),
			'error' => array(
				'attributes' => array(
					'class' => 'help-block'
				)
			),
			'custom' => array(
				'externalWrap' => false,
				'wrap' => false,
				'beforeInput' => false,
				'afterInput' => false,
				'help' => false,
				'errorClass' => 'has-error',
				'errorsAlwaysAsList' => false,
				'feedback' => false,
				'inputGroup' => false,
				'checkboxLabel' => false,
				'inline' => false,
			)
		);
		$this->assertEquals($result, $expected);

		// Test configured options
		Configure::write('Bs3.Form', array(
			'formDefaults' => array(
				'class' => 'my-default-class',
				'data-attribute' => 'my-value',
			),
			'inputDefaults' => array(
				'class' => 'my-control-class',
				'div' => array(
					'class' => 'my-form-group'
				),
				'custom' => array(
					'wrap' => 'col-sm-10',
				)
			),
			'styles' => array(
				'my-style' => array(
					'formDefaults' => array(
						'class' => 'my-form-style-class',
					),
					'inputDefaults' => array(
						'class' => 'my-style-control-class',
						'div' => false,
						'custom' => array(
							'wrap' => 'col-sm-12',
							'externalWrap' => 'col-sm-10'
						)
					),
				)
			)
		));
		$this->Form->create('Contact', array());

		$result = $this->Form->formOptions;
		$expected = array(
			'role' => 'form',
			'custom' => array(
				'submitDiv' => null,
				'submitButton' => null,
			),
			'class' => 'my-default-class',
			'data-attribute' => 'my-value'
		);
		$this->assertEquals($result, $expected);

		$result = $this->Form->inputOptions;
		$expected = array(
			'class' => 'my-control-class',
			'div' => array(
				'class' => 'my-form-group'
			),
			'label' => array(
				'class' => 'control-label'
			),
			'error' => array(
				'attributes' => array(
					'class' => 'help-block'
				)
			),
			'custom' => array(
				'externalWrap' => false,
				'wrap' => 'col-sm-10',
				'beforeInput' => false,
				'afterInput' => false,
				'help' => false,
				'errorClass' => 'has-error',
				'errorsAlwaysAsList' => false,
				'feedback' => false,
				'inputGroup' => false,
				'checkboxLabel' => false,
				'inline' => false,
			)
		);
		$this->assertEquals($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'my-style'));

		$result = $this->Form->formOptions;
		$expected = array(
			'role' => 'form',
			'custom' => array(
				'submitDiv' => null,
				'submitButton' => null,
			),
			'class' => 'my-form-style-class',
			'data-attribute' => 'my-value'
		);
		$this->assertEquals($result, $expected);

		$result = $this->Form->inputOptions;
		$expected = array(
			'class' => 'my-style-control-class',
			'div' => false,
			'label' => array(
				'class' => 'control-label'
			),
			'error' => array(
				'attributes' => array(
					'class' => 'help-block'
				)
			),
			'custom' => array(
				'wrap' => 'col-sm-12',
				'externalWrap' => 'col-sm-10',
				'beforeInput' => false,
				'afterInput' => false,
				'help' => false,
				'errorClass' => 'has-error',
				'errorsAlwaysAsList' => false,
				'feedback' => false,
				'inputGroup' => false,
				'checkboxLabel' => false,
				'inline' => false,
			)
		);
		$this->assertEquals($result, $expected);
	}

/**
 * testListFormStyles method
 *
 * @return void
 */
	public function testListFormStyles() {
		$result = $this->Form->listFormStyles();
		$expected = array('horizontal', 'inline');
		$this->assertEquals($result, $expected);
	}

/**
 * testFormCreate method
 *
 * @return void
 */
	public function testFormCreate() {
		$this->Form->request['_Token'] = array('key' => 'testKey');
		$encoding = strtolower(Configure::read('App.encoding'));
		$result = $this->Form->create('Contact');
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Form->create('Contact');
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'class' => 'form-horizontal', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Form->create('Contact', array('formStyle' => 'inline'));
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'class' => 'form-inline', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testDetectFormStyle method
 *
 * @return void
 */
	public function testDetectFormStyle() {
		$result = $this->Form->create('Contact');
		$this->assertEqual($this->Form->formStyle, null);

		$result = $this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$this->assertEqual($this->Form->formStyle, 'horizontal');

		$result = $this->Form->create('Contact', array('class' => 'my-class form-horizontal my-other-class'));
		$this->assertEqual($this->Form->formStyle, 'horizontal');

		$result = $this->Form->create('Contact', array('formStyle' => 'inline'));
		$this->assertEqual($this->Form->formStyle, 'inline');

		$result = $this->Form->create('Contact', array('class' => 'my-class form-inline my-other-class'));
		$this->assertEqual($this->Form->formStyle, 'inline');
	}

/**
 * testFormEnd method
 *
 * @return void
 */
	public function testFormEnd() {
		$this->Form->create('Contact');
		$result = $this->Form->end();
		$expected = array(
			'/form'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->end('Submit');
		$expected = array(
			'div' => array('class' => 'submit'),
			'input' => array('type' => 'submit', 'value' => 'Submit'),
			'/div',
			'/form'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->end('Submit');
		$expected = array(
			'div' => array('class' => 'submit'),
			'input' => array('type' => 'submit', 'value' => 'Submit'),
			'/div',
			'/form'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->end('Submit');
		$expected = array(
			array('div' => array('class' => 'col-sm-10 col-sm-offset-2')),
				array('input' => array('type' => 'submit', 'value' => 'Submit')),
			'/div',
			'/form',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('submitDiv' => 'my-submit-div'));
		$result = $this->Form->end('Submit');
		$expected = array(
			array('div' => array('class' => 'my-submit-div')),
				array('input' => array('type' => 'submit', 'value' => 'Submit')),
			'/div',
			'/form',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->end(array('button' => true));
		$expected = array(
			'<button',
				'Submit',
			'/button',
			'/form',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->end(array('label' => '<span>Button allows tags</span>', 'name' => 'my-submit-button', 'button' => true, 'data-my-attr' => 'my-value'));
		$expected = array(
			array('button' => array('name' => 'my-submit-button', 'data-my-attr' => 'my-value')),
				'<span',
					'Button allows tags',
				'/span',
			'/button',
			'/form',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWithForm method
 *
 * @return void
 */
	public function testInputWithForm() {
		// Default form
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$result .= $this->Form->input('email');
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactEmail', 'class' => 'control-label')),
					'Email',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][email]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'email', 'id' => 'ContactEmail',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInput method
 *
 * @return void
 */
	public function testInput() {
		// Default form
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		// Inline form
		$this->Form->create('Contact', array('formStyle' => 'inline'));
		$result = $this->Form->input('name');
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'sr-only')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control',
					'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		// Horizontal form
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'col-sm-2 control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control',
						'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
					)),
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputLabel method
 *
 * @return void
 */
	public function testInputLabel() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('label' => 'My label'));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'My label',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('label' => array('text' => 'My label', 'class' => 'my-label-class')));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'my-label-class')),
					'My label',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWithHelp method
 *
 * @return void
 */
	public function testInputWithHelp() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('help' => 'This is the help text'));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('div' => array('class' => 'help-block')),
					'This is the help text',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWithError method
 *
 * @return void
 */
	public function testInputWithError() {
		$this->Contact->invalidate('name', 'This input has error!');
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group has-error error'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control form-error', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('div' => array('class' => 'help-block')),
					'This input has error!',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testErrorRendering method
 *
 * @return void
 */
	public function testErrorRendering() {
		$this->Contact->invalidate('name', 'This input has error #1!');
		$this->Contact->invalidate('name', 'This input has error #2!');
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group has-error error'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control form-error', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('div' => array('class' => 'help-block')),
					'<ul',
						'<li',
							'This input has error #1!',
						'/li',
						'<li',
							'This input has error #2!',
						'/li',
					'/ul',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testErrorAlwaysAsList method
 *
 * @return void
 */
	public function testErrorAlwaysAsList() {
		$this->Contact->invalidate('name', 'This input has error!');
		$this->Form->create('Contact', array('inputDefaults' => array('errorsAlwaysAsList' => true)));
		$result = $this->Form->input('name');
		$expected = array(
			array('div' => array('class' => 'form-group has-error error')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control form-error', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('div' => array('class' => 'help-block')),
					'<ul',
						'<li',
							'This input has error!',
						'/li',
					'/ul',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testHidden method
 *
 * @return void
 */
	public function testHidden() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('id');
		$expected = array(
			array('input' => array('type' => 'hidden', 'name' => 'data[Contact][id]', 'id' => 'ContactId'))
		);
		$this->assertTags($result, $expected);
	}

/**
 * testCheckboxAndRadio method
 *
 * @return void
 */
	public function testCheckboxAndRadio() {
		$this->Form->create('Contact');
		$result = $this->Form->input('active', array('label' => false, 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'checkbox')),
					'label' => array('for' => 'ContactActive'),
						array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
						' My checkbox label',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('active', array('label' => 'Horizontal label', 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactActive', 'class' => 'col-sm-2 control-label')),
					'Horizontal label',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'checkbox')),
						'label' => array('for' => 'ContactActive'),
							array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
							array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
							' My checkbox label',
						'/label',
					'/div',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('gender', array('label' => false, 'type' => 'radio', 'options' => array('F' => 'Female', 'M' => 'Male')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('input' => array('type' => 'hidden', 'name' => 'data[Contact][gender]', 'id' => 'ContactGender_', 'value' => '')),
				array('div' => array('class' => 'radio')),
					array('label' => array('for' => 'ContactGenderF')),
						array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderF', 'value' => 'F')),
						' Female',
					'/label',
				'/div',
				array('div' => array('class' => 'radio')),
					array('label' => array('for' => 'ContactGenderM')),
						array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderM', 'value' => 'M')),
						' Male',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('gender', array('label' => false, 'legend' => true, 'type' => 'radio', 'options' => array('F' => 'Female', 'M' => 'Male')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				'<fieldset',
					'<legend',
						'Gender',
					'/legend',
					array('input' => array('type' => 'hidden', 'name' => 'data[Contact][gender]', 'id' => 'ContactGender_', 'value' => '')),
					array('div' => array('class' => 'radio')),
						array('label' => array('for' => 'ContactGenderF')),
							array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderF', 'value' => 'F')),
							' Female',
						'/label',
					'/div',
					array('div' => array('class' => 'radio')),
						array('label' => array('for' => 'ContactGenderM')),
							array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderM', 'value' => 'M')),
							' Male',
						'/label',
					'/div',
				'/fieldset',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('active', array('label' => false, 'checkboxLabel' => 'My checkbox label', 'class' => 'myClass'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'checkbox')),
					'label' => array('for' => 'ContactActive'),
						array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive', 'class' => 'myClass')),
						' My checkbox label',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('active', array('label' => false, 'checkboxLabel' => 'My checkbox label', 'class' => 'form-control'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'checkbox')),
					'label' => array('for' => 'ContactActive'),
						array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
						' My checkbox label',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInlineCheckboxAndRadio method
 *
 * @return void
 */
	public function testInlineCheckboxAndRadio() {

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('favorites', array('label' => 'Choose your favorites', 'multiple' => 'checkbox', 'inline' => true, 'options' => array('ice-cream' => 'Ice cream', 'chocolate' => 'Chocolate')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactFavorites', 'class' => 'col-sm-2 control-label')),
					'Choose your favorites',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('input' => array('type' => 'hidden', 'name' => 'data[Contact][favorites]', 'value' => '', 'id' => 'ContactFavorites')),
					array('label' => array('for' => 'ContactFavoritesIceCream', 'class' => 'checkbox-inline')),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][favorites][]', 'value' => 'ice-cream', 'id' => 'ContactFavoritesIceCream')),
						' Ice cream',
					'/label',
					array('label' => array('for' => 'ContactFavoritesChocolate', 'class' => 'checkbox-inline')),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][favorites][]', 'value' => 'chocolate', 'id' => 'ContactFavoritesChocolate')),
						' Chocolate',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('gender', array('label' => false, 'type' => 'radio', 'inline' => true, 'options' => array('F' => 'Female', 'M' => 'Male')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('input' => array('type' => 'hidden', 'name' => 'data[Contact][gender]', 'id' => 'ContactGender_', 'value' => '')),
				array('label' => array('for' => 'ContactGenderF', 'class' => 'radio-inline')),
					array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderF', 'value' => 'F')),
					' Female',
				'/label',
				array('label' => array('for' => 'ContactGenderM', 'class' => 'radio-inline')),
					array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderM', 'value' => 'M')),
					' Male',
				'/label',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testStaticControl method
 *
 * @return void
 */
	public function testStaticControl() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->staticControl('The label', 'The html content');
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('class' => 'col-sm-2 control-label')),
					'The label',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'form-control-static')),
						'The html content',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWrapping method
 *
 * @return void
 */
	public function testInputWrapping() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('name', array('externalWrap' => 'col-sm-10', 'wrap' => 'col-sm-6'));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'col-sm-2 control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'row')),
						array('div' => array('class' => 'col-sm-6')),
							array('input' => array(
								'name' => 'data[Contact][name]', 'class' => 'form-control',
								'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
							)),
						'/div',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testFeedback method
 *
 * @return void
 */
	public function testFeedback() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('feedback' => 'fa-check'));
		$expected = array(
			array('div' => array('class' => 'form-group has-feedback')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('i' => array('class' => 'fa fa-check form-control-feedback')),
				'/i',
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('feedback' => 'icon-other-vendor'));
		$expected = array(
			array('div' => array('class' => 'form-group has-feedback')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('i' => array('class' => 'icon-other-vendor form-control-feedback')),
				'/i',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputGroup method
 *
 * @return void
 */
	public function testInputGroup() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('inputGroup' => array('prepend' => 'fa-check')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'input-group')),
					array('span' => array('class' => 'input-group-addon')),
						array('i' => array('class' => 'fa fa-check')),
						'/i',
					'/span',
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
						'type' => 'text', 'id' => 'ContactName',
					)),
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('inputGroup' => array('append' => 'fa-bars')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'input-group')),
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
						'type' => 'text', 'id' => 'ContactName',
					)),
					array('span' => array('class' => 'input-group-addon')),
						array('i' => array('class' => 'fa fa-bars')),
						'/i',
					'/span',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('inputGroup' => array('append' => '<span>Enter only numbers</span>')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'input-group')),
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
						'type' => 'text', 'id' => 'ContactName',
					)),
					array('span' => array('class' => 'input-group-addon')),
						'<span',
							'Enter only numbers',
						'/span',
					'/span',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('inputGroup' => array(
			'size' => 'lg',
			'prepend' => 'fa-check',
			'append' => '<span>Enter only numbers</span>'
		)));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'input-group input-group-lg')),
					array('span' => array('class' => 'input-group-addon')),
						array('i' => array('class' => 'fa fa-check')),
						'/i',
					'/span',
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
						'type' => 'text', 'id' => 'ContactName',
					)),
					array('span' => array('class' => 'input-group-addon')),
						'<span',
							'Enter only numbers',
						'/span',
					'/span',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}
}
