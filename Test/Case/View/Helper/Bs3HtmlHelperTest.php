<?php
/**
 * Bs3HtmlHelperTest file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/cakephp-bootstrap3-helpers
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('Helper', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Bs3HtmlHelper', 'Bs3Helpers.View/Helper');

/**
 * TheHtmlTestController class
 */
class TheHtmlTestController extends Controller {

/**
 * name property
 *
 * @var string
 */
	public $name = 'TheTest';

/**
 * uses property
 *
 * @var mixed null
 */
	public $uses = null;
}

class TestHtmlHelper extends HtmlHelper {

/**
 * expose a method as public
 *
 * @param string $options
 * @param string $exclude
 * @param string $insertBefore
 * @param string $insertAfter
 * @return void
 */
	public function parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null) {
		return $this->_parseAttributes($options, $exclude, $insertBefore, $insertAfter);
	}

/**
 * Get a protected attribute value
 *
 * @param string $attribute
 * @return mixed
 */
	public function getAttribute($attribute) {
		if (!isset($this->{$attribute})) {
			return null;
		}
		return $this->{$attribute};
	}

}

/**
 * HtmlHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class Bs3HtmlHelperTest extends CakeTestCase {

/**
 * html property
 *
 * @var object
 */
	public $Html = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->View = $this->getMock('View', array('append'), array(new TheHtmlTestController()));
		$this->Html = new Bs3HtmlHelper($this->View);
		$this->Html->request = new CakeRequest(null, false);
		$this->Html->request->webroot = '';

		App::build(array(
			//'Plugin' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS)
		));

		//Configure::write('Asset.timestamp', false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Html, $this->View);
	}

/**
 * testIcon method
 *
 * @return void
 */
	public function testIcon() {
		$result = $this->Html->icon('fa-home');
		$expected = array(
			'i' => array('class' => 'fa fa-home'),
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->icon('glyphicon-star');
		$expected = array(
			'i' => array('class' => 'glyphicon glyphicon-star'),
		);
		$this->assertTags($result, $expected);

		Configure::write('Bs3.Html.defaultIconVendorPrefix', 'fa');
		$this->Html = new Bs3HtmlHelper($this->View);
		$result = $this->Html->icon('globe');
		$expected = array(
			'i' => array('class' => 'fa fa-globe'),
		);
		$this->assertTags($result, $expected);

		Configure::write('Bs3.Html.defaultIconVendorPrefix', 'glyphicon');
		$this->Html = new Bs3HtmlHelper($this->View);
		$result = $this->Html->icon('leaf');
		$expected = array(
			'i' => array('class' => 'glyphicon glyphicon-leaf'),
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testDropdown() {
		$result = $this->Html->dropdown('My dropdown', array(
			$this->Html->link('Link 1', '/link1'),
			array('html' => $this->Html->link('Link 2', '/link2')),
			array('divider' => true),
			array('html' => $this->Html->link('Link 3', '#', array('class' => 'my-class')), 'divider' => 'after'),
			'<a href="#">Link 4</a>',
			array('html' => $this->Html->link('Link 5', '#'), 'active' => true),
		));
		$expected = array(
			'div' => array('class' => 'dropdown'),
				'button' => array(
					'type' => 'button',
					'class' => 'btn btn-default sr-only dropdown-toggle',
					'data-toggle' => 'dropdown',
				),
					'My dropdown',
					'span' => array('class' => 'caret'),
					'/span',
				'/button',
				'ul' => array('class' => 'dropdown-menu'),
					'<li',
						array('a' => array('href' => '/link1')),
							'Link 1',
						'/a',
					'/li',
					'<li',
						array('a' => array('href' => '/link2')),
							'Link 2',
						'/a',
					'/li',
					array('li' => array('class' => 'divider')),
					'/li',
					'<li',
						array('a' => array('href' => '#', 'class' => 'my-class')),
							'Link 3',
						'/a',
					'/li',
					array('li' => array('class' => 'divider')),
					'/li',
					'<li',
						array('a' => array('href' => '#')),
							'Link 4',
						'/a',
					'/li',
					array('li' => array('class' => 'active')),
						array('a' => array('href' => '#')),
							'Link 5',
						'/a',
					'/li',
				'/ul',
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->dropdown('My dropdown', '<li><a href="#">My passed html</a></li>');
		$expected = array(
			'div' => array('class' => 'dropdown'),
				'button' => array(
					'type' => 'button',
					'class' => 'btn btn-default sr-only dropdown-toggle',
					'data-toggle' => 'dropdown',
				),
					'My dropdown',
					'span' => array('class' => 'caret'),
					'/span',
				'/button',
				'ul' => array('class' => 'dropdown-menu'),
					'<li',
						array('a' => array('href' => '#')),
							'My passed html',
						'/a',
					'/li',
				'/ul',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testDropdownBlockRendering() {
		$this->Html->dropdownStart('My dropdown');
		echo '<li>' . $this->Html->link('Link 1', '/link1') . '</li>';
		echo '<li>' . $this->Html->link('Link 2', '/link2') . '</li>';
		echo '<li class="divider"></li>';
		echo '<li>' . $this->Html->link('Link 3', '#', array('class' => 'my-class')) . '</li>';
		echo '<li class="divider"></li>';
		echo '<li><a href="#">Link 4</a></li>';
		echo '<li class="active">' . $this->Html->link('Link 5', '#') . '</li>';
		$result = $this->Html->dropdownEnd();
		$expected = array(
			'div' => array('class' => 'dropdown'),
				'button' => array(
					'type' => 'button',
					'class' => 'btn btn-default sr-only dropdown-toggle',
					'data-toggle' => 'dropdown',
				),
					'My dropdown',
					'span' => array('class' => 'caret'),
					'/span',
				'/button',
				'ul' => array('class' => 'dropdown-menu'),
					'<li',
						array('a' => array('href' => '/link1')),
							'Link 1',
						'/a',
					'/li',
					'<li',
						array('a' => array('href' => '/link2')),
							'Link 2',
						'/a',
					'/li',
					array('li' => array('class' => 'divider')),
					'/li',
					'<li',
						array('a' => array('href' => '#', 'class' => 'my-class')),
							'Link 3',
						'/a',
					'/li',
					array('li' => array('class' => 'divider')),
					'/li',
					'<li',
						array('a' => array('href' => '#')),
							'Link 4',
						'/a',
					'/li',
					array('li' => array('class' => 'active')),
						array('a' => array('href' => '#')),
							'Link 5',
						'/a',
					'/li',
				'/ul',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testPanel() {
		$result = $this->Html->panelHeading('A panel title');
		$expected = array('div' => array('class' => 'panel-heading'), 'A panel title', '/div');
		$this->assertTags($result, $expected);
                
		$result = $this->Html->panelFooter('A panel footer');
		$expected = array('div' => array('class' => 'panel-footer'), 'A panel footer', '/div');
		$this->assertTags($result, $expected);

		$result = $this->Html->panelBody('A panel body');
		$expected = array('div' => array('class' => 'panel-body'), 'A panel body', '/div');
		$this->assertTags($result, $expected);

		$result = $this->Html->panelHeading('A panel title', array(
			'class' => 'my-panel-heading', 'data-my-value' => '123'
		));
		$expected = array(
			'div' => array(
				'class' => 'my-panel-heading panel-heading',
				'data-my-value' => '123'
			),
			'A panel title',
			'/div'
		);
		$this->assertTags($result, $expected);
                
                $result = $this->Html->panelFooter('A panel footer', array(
			'class' => 'my-panel-footer', 'data-my-value' => '123'
		));
		$expected = array(
			'div' => array(
				'class' => 'my-panel-footer panel-footer',
				'data-my-value' => '123'
			),
			'A panel footer',
			'/div'
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->panelBody('A panel body', array(
			'class' => 'my-panel-body', 'data-my-value' => '456'
		));
		$expected = array(
			'div' => array(
				'class' => 'my-panel-body panel-body',
				'data-my-value' => '456'
			),
			'A panel body',
			'/div'
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->panel('A panel title', 'A panel body');
		$expected = array(
			array('div' => array('class' => 'panel-default panel')),
			array('div' => array('class' => 'panel-heading')), 'A panel title', '/div',
			array('div' => array('class' => 'panel-body')), 'A panel body', '/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->panel('A panel title', 'A panel body', null, array(
			'headingOptions' => array('class' => 'my-panel-heading'),
			'bodyOptions' => array('class' => 'my-panel-body'),
		));
		$expected = array(
			array('div' => array('class' => 'panel-default panel')),
			array('div' => array('class' => 'my-panel-heading panel-heading')), 'A panel title', '/div',
			array('div' => array('class' => 'my-panel-body panel-body')), 'A panel body', '/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testPanelBlockRendering() {
		// Heading rendering
		$this->Html->panelHeadingStart();
		echo $this->Html->tag('div', 'A panel heading with block rendering');
		$result = $this->Html->panelHeadingEnd();
		$expected = array(
			'div' => array('class' => 'panel-heading'),
			'<div',
			'A panel heading with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		// Body rendering
		$this->Html->panelBodyStart();
		echo $this->Html->tag('div', 'A panel body with block rendering');
		$result = $this->Html->panelBodyEnd();
		$expected = array(
			'div' => array('class' => 'panel-body'),
			'<div',
			'A panel body with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
                
		// Footer rendering
		$this->Html->panelFooterStart();
		echo $this->Html->tag('div', 'A panel footer with block rendering');
		$result = $this->Html->panelFooterEnd();
		$expected = array(
			'div' => array('class' => 'panel-footer'),
			'<div',
			'A panel footer with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
                

		// Heading with options rendering
		$this->Html->panelHeadingStart(array('class' => 'my-panel-heading'));
		echo $this->Html->tag('div', 'A panel heading with block rendering');
		$result = $this->Html->panelHeadingEnd();
		$expected = array(
			'div' => array('class' => 'my-panel-heading panel-heading'),
			'<div',
			'A panel heading with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		// Body with options rendering
		$this->Html->panelBodyStart();
		echo $this->Html->tag('div', 'A panel body with block rendering');
		$result = $this->Html->panelBodyEnd();
		$expected = array(
			'div' => array('class' => 'panel-body'),
			'<div',
			'A panel body with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
                
		// Footer with options rendering
		$this->Html->panelFooterStart(array('class' => 'my-panel-footer'));
		echo $this->Html->tag('div', 'A panel footer with block rendering');
		$result = $this->Html->panelFooterEnd();
		$expected = array(
			'div' => array('class' => 'my-panel-footer panel-footer'),
			'<div',
			'A panel footer with block rendering',
			'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		// Complete panel rendering
		$this->Html->panelStart();
		echo $this->Html->panelHeading('A panel title');
		echo $this->Html->panelBody('A panel body');
		$result = $this->Html->panelEnd();
		$expected = array(
			array('div' => array('class' => 'panel-default panel')),
			array('div' => array('class' => 'panel-heading')), 'A panel title', '/div',
			array('div' => array('class' => 'panel-body')), 'A panel body', '/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		// Complete panel with options rendering
		$this->Html->panelStart(array('class' => 'my-panel'));
		echo $this->Html->panelHeading('A panel title', array('class' => 'my-panel-heading'));
		echo $this->Html->panelBody('A panel body', array('class' => 'my-panel-body'));
		$result = $this->Html->panelEnd();
		$expected = array(
			array('div' => array('class' => 'my-panel panel')),
			array('div' => array('class' => 'my-panel-heading panel-heading')), 'A panel title', '/div',
			array('div' => array('class' => 'my-panel-body panel-body')), 'A panel body', '/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testAccordion() {
		$result = $this->Html->accordion();
		$expected = array(
			'div' => array(
				'class' => 'panel-group',
				'id' => 'preg:/accordion_\w+/'
			),
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->accordionItem('Item title', 'Item body', array('accordionId' => 'my-accordion'));
		$expected = array(
			array('div' => array('class' => 'panel-default panel')),
				array('div' => array('class' => 'panel-heading')),
					array('h4' => array('class' => 'panel-title')),
						array('a' => array(
							'data-parent' => '#my-accordion', 'data-toggle' => 'collapse',
							'href' => 'preg:/\#accordion_body_\w+/'
						)),
							'Item title',
						'/a',
					'/h4',
				'/div',
				array('div' => array('class' => 'panel-collapse collapse in', 'id' => 'preg:/accordion_body_\w+/')),
					array('div' => array('class' => 'panel-body')), 'Item body', '/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Html->accordion(array(
			'Item 1 title' => 'Item 1 body',
			'Item 2 title' => 'Item 2 body',
		));
		$expected = array(
			'div' => array(
				'class' => 'panel-group',
				'id' => 'preg:/accordion_\w+/'
			),
				array('div' => array('class' => 'panel-default panel')),
					array('div' => array('class' => 'panel-heading')),
						array('h4' => array('class' => 'panel-title')),
							array('a' => array(
								'data-parent' => 'preg:/\#accordion_\w+/', 'data-toggle' => 'collapse',
								'href' => 'preg:/\#accordion_body_\w+/'
							)),
								'Item 1 title',
							'/a',
						'/h4',
					'/div',
					array('div' => array('class' => 'panel-collapse collapse in', 'id' => 'preg:/accordion_body_\w+/')),
						array('div' => array('class' => 'panel-body')), 'Item 1 body', '/div',
					'/div',
				'/div',
				array('div' => array('class' => 'panel-default panel')),
					array('div' => array('class' => 'panel-heading')),
						array('h4' => array('class' => 'panel-title')),
							array('a' => array(
								'data-parent' => 'preg:/\#accordion_\w+/', 'data-toggle' => 'collapse',
								'href' => 'preg:/\#accordion_body_\w+/'
							)),
								'Item 2 title',
							'/a',
						'/h4',
					'/div',
					array('div' => array('class' => 'panel-collapse collapse in', 'id' => 'preg:/accordion_body_\w+/')),
						array('div' => array('class' => 'panel-body')), 'Item 2 body', '/div',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testAccordionBlockRendering() {
		$this->Html->accordionStart(array('id' => 'my-accordion'));
			echo $this->Html->accordionItem('Item 1 title', 'Item 1 body', array('accordionId' => 'my-accordion'));
			echo $this->Html->accordionItem('Item 2 title', 'Item 2 body', array('accordionId' => 'my-accordion'));
		$result = $this->Html->accordionEnd();
		$expected = array(
			'div' => array(
				'class' => 'panel-group',
				'id' => 'my-accordion'
			),
				array('div' => array('class' => 'panel-default panel')),
					array('div' => array('class' => 'panel-heading')),
						array('h4' => array('class' => 'panel-title')),
							array('a' => array(
								'data-parent' => '#my-accordion', 'data-toggle' => 'collapse',
								'href' => 'preg:/\#accordion_body_\w+/'
							)),
								'Item 1 title',
							'/a',
						'/h4',
					'/div',
					array('div' => array('class' => 'panel-collapse collapse in', 'id' => 'preg:/accordion_body_\w+/')),
						array('div' => array('class' => 'panel-body')), 'Item 1 body', '/div',
					'/div',
				'/div',
				array('div' => array('class' => 'panel-default panel')),
					array('div' => array('class' => 'panel-heading')),
						array('h4' => array('class' => 'panel-title')),
							array('a' => array(
								'data-parent' => '#my-accordion', 'data-toggle' => 'collapse',
								'href' => 'preg:/\#accordion_body_\w+/'
							)),
								'Item 2 title',
							'/a',
						'/h4',
					'/div',
					array('div' => array('class' => 'panel-collapse collapse in', 'id' => 'preg:/accordion_body_\w+/')),
						array('div' => array('class' => 'panel-body')), 'Item 2 body', '/div',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}
}
