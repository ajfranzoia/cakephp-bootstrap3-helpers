<?php
/**
 * Bs3HtmlHelper file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/cakephp-bootstrap3-helpers
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('HtmlHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class Bs3HtmlHelper extends HtmlHelper {

/**
 * Default configuration.
 *
 * @var array
 */
	protected $_defaults = array(
		'iconVendorPrefixes' => array('fa', 'glyphicon'),
		'defaultIconVendorPrefix' => null,
	);

/**
 * Flag for active block rendering of components
 *
 * @var boolean
 */
	protected $_blockRendering = false;

/**
 * Current block rendering options
 *
 * @var array
 */
	protected $_blockOptions = array();

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$userConfig = Configure::check('Bs3.Html') ? Configure::read('Bs3.Html') : array();
		$this->_config = Hash::merge($this->_defaults, $userConfig);
	}


	public function cdnCss() {
		return $this->css('//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
	}


	public function cdnJs() {
		return $this->js('//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
	}

	public function cdnFontAwesome() {
		return $this->css('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css');
	}
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function icon($class, $options = array()) {
		$defaults = array();
		$options = array_merge($defaults, $options);

		$iconVendorPrefix = $this->getIconVendor($class);
		if ($iconVendorPrefix) {
			$class = $iconVendorPrefix . ' ' . $class;
		} else {
			$iconVendorPrefix = $this->_config['defaultIconVendorPrefix'];
			if ($iconVendorPrefix) {
				$class = $iconVendorPrefix . ' ' . $iconVendorPrefix . '-' . $class;
			}
		}

		$options['class'] = $class;
		return $this->tag('i', '', $options);
	}

/**
 * Returns icon vendor from class if found.
 */
	public function getIconVendor($class) {
		foreach ($this->_config['iconVendorPrefixes'] as $iconVendorPrefix) {
			$regex = sprintf('/^%s-|\s%s-/', $iconVendorPrefix, $iconVendorPrefix);
			if (preg_match($regex, $class)) {
				return $iconVendorPrefix;
			}
		}

		return null;
	}
        
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
        public function panelFooter($html, $options = array()) {
                $defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-footer');
		return $this->tag('div', $html, $options);
        }
        
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function panelHeading($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-heading');
		return $this->tag('div', $html, $options);
	}

/**
 * Render a panel body
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function panelBody($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-body');
		return $this->tag('div', $html, $options);
	}

/**
 * Render a panel
 *
 * @param string $headingHtml
 * @param string $bodyHtml
 * @param string $footerHtml
 * @param array $options
 * @return string
 */
	public function panel($headingHtml, $bodyHtml = null, $footerHtml = null, $options = array()) {
		$defaults = array(
			'class' => 'panel-default', 'headingOptions' => array(), 'footerOptions' => array(), 'bodyOptions' => array(),
			'wrapHeading' => true, 'wrapFooter' => true, 'wrapBody' => true
		);
		if ($this->_blockRendering) {
			$options = $bodyHtml;
		}
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel');

		if (!$this->_blockRendering) {
			$heading = $options['wrapHeading'] ? $this->panelHeading($headingHtml, $options['headingOptions']) : $headingHtml;
                        $footer = $options['wrapFooter'] && $footerHtml ? $this->panelFooter($footerHtml, $options['footerOptions']) : $footerHtml;
			$body = $options['wrapBody'] ? $this->panelBody($bodyHtml, $options['bodyOptions']) : $bodyHtml;
			$html = $heading . $body . $footer;
		} else {
			$html = $headingHtml;
		}

		unset($options['headingOptions'], $options['footerOptions'], $options['bodyOptions'], $options['wrapFooter'], $options['wrapHeading'], $options['wrapBody']);
		return $this->tag('div', $html, $options);
	}

/**
 * Render an accordion
 *
 * @param mixed $items
 * @param array $options
 * @return string
 */
	public function accordion($items = array(), $options = array()) {
		$defaults = array(
			'class' => '', 'id' => str_replace('.', '', uniqid('accordion_', true)),
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel-group');

		if (is_array($items)) {
			$html = '';
			foreach ($items as $itemHeading => $itemBody) {
				$html .= $this->accordionItem($itemHeading, $itemBody, array('accordionId' => $options['id']));
			}
		} else {
			$html= $items;
		}

		return $this->tag('div', $html, $options);
	}

/**
 * Render an accordion item
 *
 * @param mixed $titleHtml
 * @param mixed $bodyHtml
 * @param array $options
 * @return string
 */
	public function accordionItem($titleHtml, $bodyHtml = null, $options = array()) {
		$itemBodyId = str_replace('.', '', uniqid('accordion_body_', true));
		$titleLink = $this->link($titleHtml, '#' . $itemBodyId, array(
			'data-toggle' => 'collapse', 'data-parent' => '#' . $options['accordionId']
		));
		$heading = $this->tag('h4', $titleLink, array('class' => 'panel-title'));
		$body = $this->tag('div', $this->panelBody($bodyHtml), array(
			'class' => 'panel-collapse collapse in', 'id' => $itemBodyId
		));

		$blockRendering = $this->_blockRendering;
		$this->_blockRendering = false;
		$itemHtml = $this->panel($heading, $body, null, array('wrapBody' => false));
		$this->_blockRendering = $blockRendering;
		return $itemHtml;
	}

	public function dropdown($toggle, $links = array(), $options = array()) {
		$defaults = array(
			'class' => '',
			'toggleClass' => 'btn btn-default',
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'dropdown');

		if ($this->_blockRendering) {
			$itemsHtml = $toggle;
			$toggle = $links;
		} else {
			if (is_array($links)) {
				$itemsHtml = '';
				foreach ($links as $item => $itemOptions) {
					$itemHtml = $before = $after = '';
					$liOptions = array();
					if (is_array($itemOptions)) {
						if ($this->_extractOption('active', $itemOptions)) {
							$liOptions['class'] = 'active';
						}

						if ($divider = $this->_extractOption('divider', $itemOptions)) {
							if ($divider === true) {
								$liOptions['class'] = 'divider';
							} else {
								${$divider} = $this->tag('li', '', array('class' => 'divider'));
							}
						}
						$itemHtml = $this->_extractOption('html', $itemOptions, '');
					} else {
						$itemHtml = $itemOptions;
					}

					$itemsHtml .= $before . $this->tag('li', $itemHtml, $liOptions) . $after;
				}
			} else {
				$itemsHtml= $links;
			}
		}

		$toggleOptions = array(
			'type' => 'button',
			'class' => $options['toggleClass'],
			'data-toggle' => 'dropdown'
		);
		$toggleOptions = $this->addClass($toggleOptions, 'sr-only dropdown-toggle');
		$toggleHtml = $this->tag('button', $toggle . ' <span class="caret"></span>', $toggleOptions);
		unset($options['toggleClass']);
		$itemsHtml = $this->tag('ul', $itemsHtml, array('class'=>'dropdown-menu'));

		$html = $toggleHtml . $itemsHtml;

		return $this->tag('div', $html, $options);
	}

/**
 * Handles custom method calls, like findBy<field> for DB models,
 * and custom RPC calls for remote data sources.
 *
 * @param string $method Name of method to call.
 * @param array $params Parameters for the method.
 * @return mixed Whatever is returned by called method
 */
	public function __call($method, $params) {
		if (substr($method, -5) == 'Start') {
			$call = substr($method, 0, strlen($method) - 5);
			if (method_exists($this, $call)) {
				$this->_View->assign($call . '_block', null);
				$this->_blockOptions[$call . '_block_options'] = isset($params[0]) ? $params[0] : array();
				$this->_View->start($call . '_block');
				$this->_blockRendering = true;
			}
		} elseif (substr($method, -3) == 'End') {
			$call = substr($method, 0, strlen($method) - 3);
			if (method_exists($this, $call)) {
				$this->_View->end($call . '_block');
				$html = $this->_View->fetch($call . '_block');
				$generatedHtml = $this->$call($html, $this->_blockOptions[$call . '_block_options']);
				$this->_blockRendering = false;
				return $generatedHtml;
			}
		}
	}

/**
 * Extracts a single option from an options array.
 *
 * @param string $name The name of the option to pull out.
 * @param array $options The array of options you want to extract.
 * @param mixed $default The default option value
 * @return mixed the contents of the option or default
 */
	protected function _extractOption($name, $options, $default = null) {
		if (array_key_exists($name, $options)) {
			return $options[$name];
		}
		return $default;
	}
}
