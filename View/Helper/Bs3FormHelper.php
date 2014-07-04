<?php
/**
 * Bs3FormHelper file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/cakephp-bootstrap3-helpers
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('FormHelper', 'View/Helper');
App::uses('Hash', 'Utility');

/**
 * Bs3FormHelper class.
 */
class Bs3FormHelper extends FormHelper {

/**
 * Available options.
 *
 * @var array
 */
	protected $_myInputDefaults = array(
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

/**
 * Default options for form.
 *
 *  - wrap:  HTML4 Strict.
 *
 * @var array
 */
	protected $_myFormDefaults = array(
		'role' => 'form',
		'custom' => array(
			'submitDiv' => null
		)
	);

/**
 * Handles custom options for current form.
 *
 * @var string
 */
	public $formOptions = null;

/**
 * Handles custom options for inputs.
 *
 * @var string
 */
	public $inputOptions = null;

/**
 * Handles custom options for inputs.
 *
 * @var string
 */
	public $currentInputOptions = null;

/**
 * Current input type
 *
 * @var string
 */
	protected $_inputType = null;

/**
 * Current field name
 *
 * @var string
 */
	protected $_fieldName = null;

/**
 * Current form type.
 * Can be 'horizontal', 'inline', 'default' or any type you configure.
 *
 * @var boolean
 */
	public $formStyle = null;

	protected $_hasFeedback = false;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->Html = $this->_View->loadHelper('Bs3Helpers.Bs3Html');
	}

/**
 * Redefine el metodo padre para aceptar configuraciones globales de forms, definidas en
 * bootstrap.php o por ej. en beforeRender de helper de app.
 * Configs. disponibles:
 * - Form.formDefaults
 * - Form.inputDefaults
 *
 * TODO: configs separadas por tipo de form
 *
 * @param mixed $model
 * @param array $options An array of html attributes and options.
 * @return string An formatted opening FORM tag.
 */
	public function create($model = null, $options = array()) {
		$this->_processConfig($options);

		$optionsForCreate = Hash::merge(
			$this->formOptions,
			array('inputDefaults' => $this->inputOptions)
		);
		unset($optionsForCreate['custom'], $optionsForCreate['inputDefaults']);

		return parent::create($model, $optionsForCreate);
	}

/**
 * Overrides parent method to add
 *
 * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden
 * input fields where appropriate.
 *
 * If $options is set a form submit button will be created. Options can be either a string or an array.
 *
 * {{{
 * array usage:
 *
 * array('label' => 'save'); value="save"
 * array('label' => 'save', 'name' => 'Whatever'); value="save" name="Whatever"
 * array('name' => 'Whatever'); value="Submit" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => 'good') <div class="good"> value="save" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => array('class' => 'good')); <div class="good"> value="save" name="Whatever"
 * }}}
 *
 * @param string|array $options as a string will use $options as the value of button,
 * @return string a closing FORM tag optional submit button.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#closing-the-form
 */
	public function end($options = null, $secureAttributes = array()) {
		$options = is_string($options) ? array('value' => $options) : $options;

		if (!empty($this->formOptions['custom']['submitDiv']) && !isset($options['div'])) {
			$options['div'] = $this->formOptions['custom']['submitDiv'];
		}

		$out = null;

		if (!empty($options['button'])) {

			$label = isset($options['label']) ? $options['label'] : __d('cake', 'Submit');
			unset($options['button'], $options['label']);

			$out .= $this->Html->tag('button', $label, $options);

		} else {
			$submit = null;
			if ($options !== null) {
				$submitOptions = array();
				if (is_string($options)) {
					$submit = $options;
				} else {
					if (isset($options['label'])) {
						$submit = $options['label'];
						unset($options['label']);
					}
					$submitOptions = $options;
				}
				$out .= $this->submit($submit, $submitOptions);
			}
		}

		if (
			$this->requestType !== 'get' &&
			isset($this->request['_Token']) &&
			!empty($this->request['_Token'])
		) {
			$out .= $this->secure($this->fields);
			$this->fields = array();
		}
		$this->setEntity(null);
		$out .= $this->Html->useTag('formend');

		$this->_View->modelScope = false;
		$this->requestType = null;

		$this->_inputOptions = null;
		$this->formStyle = null;

		return $out;
	}

/**
 * Overrides parent method to allow the use of aditional options by calling
 * the _initOptions() method. Supported options are listed in $_customOptions property.
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function input($fieldName, $options = array()) {
		$this->_inputType = null;
		$this->_fieldName = $fieldName;
		$options = $this->_initInputOptions($options);

		return parent::input($fieldName, $options);
	}

/**
 * Parses current input configuration, extracted from general, form and passed options.
 * Does not make use of _parseOptions() to prevent parent class to avoid overridings.
 *
 * @param array $options
 * @return array
 */
	protected function _initInputOptions($options) {
		$options = $this->_initLabel($options);
		$options = Hash::merge($this->inputOptions, $options);
		$options = $this->currentInputOptions = $this->_processCustomConfig('input', $options);

		$this->_initInputGroup();
		$this->_initFeedback();
		unset($options['custom']);

		return $options;
	}

/**
 * Overwrites parent method to generate a custom input.
 *
 * @param type $args
 * @return string
 */
	protected function _getInput($args) {
		$type = $this->_currentInputType = $args['type'];
		$options = $this->currentInputOptions;
		$customOptions = $this->currentInputOptions['custom'];

		// TODO: ver esto... Si es de tipo select multiple con checkbox, no setear clase en checkoxes
		if (in_array($type, array('checkbox', 'hidden')) ||
			($args['type'] == 'select' &&
				isset($args['options']['multiple']) &&
				$args['options']['multiple'] == 'checkbox' &&
				$args['options']['class'] == 'form-control')) {
			unset($args['options']['class']);
		}

		// Render input field via parent method
		$input = parent::_getInput($args);

		if ($type == 'hidden') {
			return $input;
		}

		// Prepend beforeInput and append afterInput to generated input field
		$html['input'] = $this->_getCustom('beforeInput') . $input . $this->_getCustom('afterInput');

		// Checkbox label rendering
		if ($type == 'checkbox') {
			if ($this->_getCustom('checkboxLabel')) {
				$options = $this->domId($args);
				$html['input'] .= ' ' . $customOptions['checkboxLabel'];
				$html['input'] = $this->Html->tag('label', $html['input'], array('for' => $options['id']));
			}
			$html['input'] = $this->Html->tag('div', $html['input'], array('class' => 'checkbox'));
		}

		// Error rendering, overwrites parent rendering
		$html['error'] = null;
		$errorOptions = $this->_extractOption('error', $options, null);
		if ($this->_inputType !== 'hidden' && $errorOptions !== false) {
			$html['error'] = $this->error($this->_fieldName, $errorOptions);
		}

		// Help block rendering
		$html['help'] = null;
		if ($customOptions['help']) {
			$html['help'] = $this->Html->tag('div', $customOptions['help'], array('class' => 'help-block'));
		}

		// Set size of input if enabled, and get full html of input and block
		if ($customOptions['wrap']) {
			if ($customOptions['externalWrap']) {
				$html['input'] = $this->Html->tag('div', $html['input'], array('class' => $customOptions['wrap']));
				$html['input'] = $this->Html->tag('div', $html['input'], array('class' => 'row'));

				if ($customOptions['externalWrap']) {
					$fullHtml = $html['input'] . $html['error'] . $html['help'];
					$fullHtml = $this->Html->tag('div', $fullHtml, array('class' => $customOptions['externalWrap']));
				}
			} else {
				$fullHtml = $html['input'] . $html['error'] . $html['help'];
				$fullHtml = $this->Html->tag('div', $fullHtml, array('class' => $customOptions['wrap']));
			}
		} else {
			$fullHtml = $html['input'] . $html['error'] . $html['help'];
		}

		return $fullHtml;
	}

/**
 * Redefine div options para soportar seteo de clase de error en clase contenedora,
 * ademas del agregado del div de mensaje de error que se hace en _getInput().
 *
 * @param array $options
 * @return array
 */
	protected function _divOptions($options) {
		$divOptions = parent::_divOptions($options);
		if ($this->tagIsInvalid() !== false && $this->_getCustom('errorClass')) {
			$divOptions = $this->addClass($divOptions, $this->_getCustom('errorClass'));
		}
		if ($this->_hasFeedback) {
			$divOptions = $this->addClass($divOptions, 'has-feedback');
		}

		return $divOptions;
	}

/**
 * Parent method redefined to support control labels on radio groups.
 *
 * @param string $fieldName
 * @param array $options
 * @return boolean|string false or Generated label element
 */
	protected function _getLabel($fieldName, $options) {
		$label = null;
		if (isset($options['label'])) {
			$label = $options['label'];
		}

		if ($label === false) {
			return false;
		}
		return $this->_inputLabel($fieldName, $label, $options) . ' ';
	}

/**
 * Radio groups rendering overwriten, generates no legend/fieldset.
 * By setting inline => true, will generate inline radio inputs.
 *
 * @param string $fieldName
 * @param array $options
 * @param array $attributes
 * @return string
 */
	public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);
		$showEmpty = $this->_extractOption('empty', $attributes);
		if ($showEmpty) {
			$showEmpty = ($showEmpty === true) ? __d('cake', 'empty') : $showEmpty;
			$options = array('' => $showEmpty) + $options;
		}
		unset($attributes['empty']);

		$legend = false;
		if (isset($attributes['legend'])) {
			if ($attributes['legend'] === true) {
				$legend = __(Inflector::humanize($this->field()));
			} else {
				$legend = $attributes['legend'];
			}
			unset($attributes['legend']);
		}

		$label = true;
		if (isset($attributes['label'])) {
			$label = $attributes['label'];
			unset($attributes['label']);
		}

		$separator = null;
		if (isset($attributes['separator'])) {
			$separator = $attributes['separator'];
			unset($attributes['separator']);
		}

		$between = null;
		if (isset($attributes['between'])) {
			$between = $attributes['between'];
			unset($attributes['between']);
		}

		// Opcion inline
		$inline = null;
		if ($this->_getCustom('inline')) {
			$inline = $this->_getCustom('inline');
			unset($attributes['inline']);
		}

		$value = null;
		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		} else {
			$value = $this->value($fieldName);
		}

		$disabled = array();
		if (isset($attributes['disabled'])) {
			$disabled = $attributes['disabled'];
		}

		$out = array();

		$hiddenField = isset($attributes['hiddenField']) ? $attributes['hiddenField'] : true;
		unset($attributes['hiddenField']);

		if (isset($value) && is_bool($value)) {
			$value = $value ? 1 : 0;
		}

		$this->_domIdSuffixes = array();
		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue, 'disabled' => false);

			if (isset($value) && strval($optValue) === strval($value)) {
				$optionsHere['checked'] = 'checked';
			}
			$isNumeric = is_numeric($optValue);
			if ($disabled && (!is_array($disabled) || in_array((string)$optValue, $disabled, !$isNumeric))) {
				$optionsHere['disabled'] = true;
			}
			$tagName = $attributes['id'] . $this->domIdSuffix($optValue);

			if (is_array($between)) {
				$optTitle .= array_shift($between);
			}
			$allOptions = array_merge($attributes, $optionsHere);

			$allOptions['class'] = null;
			if (isset($options['class'])) {
				$allOptions['class'] = $options['class'];
			}
			$optHtml = $this->Html->useTag('radio', $attributes['name'], $tagName,
				array_diff_key($allOptions, array('name' => null, 'type' => null, 'id' => null)),
				''
			);

			// Wraps radio inside label
			$labelOpts = array();
			$labelOpts += array('for' => $tagName);

			if ($inline) {
				$labelOpts['class'] = 'radio-inline';
			}
			$optLabel = $this->label($tagName, $optHtml . ' ' . $optTitle, $labelOpts);

			// If it is not an inline group, wrap input inside div.radio
			if (!$inline) {
				$optLabel = $this->Html->tag('div', $optLabel, array('class' => 'radio'));
			}

			$out[] = $optLabel;
		}
		$hidden = null;

		if ($hiddenField) {
			if (!isset($value) || $value === '') {
				$hidden = $this->hidden($fieldName, array(
					'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
				));
			}
		}
		$out = $hidden . implode($separator, $out);

		if (is_array($between)) {
			$between = '';
		}
		if ($legend) {
			$out = $this->Html->useTag('fieldset', '', $this->Html->useTag('legend', $legend) . $between . $out);
		}

		return $out;
	}

/**
 * Generates a static from control with the passed 'label' and 'html' options.
 *
 * @param string $label
 * @param string $options
 * @param array $options
 * @return string
 */
	public function staticControl($label, $html, $options = array()) {
		$options['type'] = 'static';
		$options = $this->addClass($options, 'form-control-static');
		$options = $this->_initInputOptions($options);

		$html = $this->Html->tag('div', $html, array('class' => $options['class']));

		// If size is set (like in an horizontal form) wrap inside div.size
		if ($this->inputOptions['custom']) {
			$html = $this->Html->tag('div', $html, array('class' => $this->inputOptions['custom']['wrap']));
		}

		if (!empty($options['label'])) {
			$label = $this->Html->tag('label', $label, $options['label']);
		}

		$divOptions = $this->_divOptions($options);
		unset($divOptions['tag']);
		$html = $this->Html->tag('div', $label . $html, $divOptions);

		return $html;
	}

/**
 * If label is a string, transform it to proper label options array with the text option
 */
	protected function _initLabel($options) {
		if (isset($options['label']) && is_string($options['label'])) {
			$options['label'] = array(
				'text' => $options['label']
			);
		}

		return $options;
	}

/**
 * Generates proper input group rendering. Checks for the existence of 'inputGroup' option
 * and determines if it must prepend and/or append , and the group size.
 * If feedback class starts with a registered icon vendor prefix it automatically adds the vendor class
 *
 * @return array
 */
	protected function _initInputGroup() {
		$inputGroupOptions = $this->_getCustom('inputGroup');
		if (!$inputGroupOptions) {
			return;
		}

		// Check for prepend option. If option stats with a registered icon vendor prefix,
		// it will automatically add vendor class
		$prepend = $this->_extractOption('prepend', $inputGroupOptions);
		if ($prepend) {
			if ($prependIcon = $this->Html->getIconVendor($prepend)) {
				$prepend = $this->Html->icon($prepend);
			}
			$prepend = $this->Html->tag('span', $prepend, array('class' => 'input-group-addon'));
		}

		// Check for append option. If option stats with a registered icon vendor prefix,
		// it will automatically add the vendor class
		$append = $this->_extractOption('append', $inputGroupOptions);
		if ($append) {
			if ($appendIcon = $this->Html->getIconVendor($append)) {
				$append = $this->Html->icon($append);
			}
			$append = $this->Html->tag('span', $append, array('class' => 'input-group-addon'));
		}

		// Generates div and sets 'beforeInput' and 'afterInput' options
		$divOptions = array('class' => 'input-group');
		$size = $this->_extractOption('size', $inputGroupOptions);
		if ($size) {
			$divOptions = $this->addClass($divOptions, 'input-group-' . $size);
		}

		$this->_setCustom('beforeInput' , $this->_getCustom('beforeInput') .$this->Html->tag('div', null, $divOptions) . $prepend);
		$this->_setCustom('afterInput' , $append . '</div>' . $this->_getCustom('afterInput'));
	}

/**
 * Generates input feedback rendering if custom option 'feedback' is passed.
 * If feedback class starts with a registered icon vendor prefix it automatically
 * adds the vendor class (See Bs3Html::icon() for more info).
 *
 * @param array $options
 * @return array
 */
	protected function _initFeedback() {
		$this->_hasFeedback = false;

		$feedback = $this->_getCustom('feedback');
		if (!$feedback) {
			return;
		}

		// Set proper style when form is not horizontal
		$style = null;
		if ($this->_getCustom('externalWrap') || $this->_getCustom('wrap')) {
			$style = 'top:0; right: 15px';
		} elseif ($this->formStyle == 'inline') {
			$style = 'top:0;';
		}

		$iconClass = null;
		if ($feedbackIconVendor = $this->Html->getIconVendor($feedback)) {
			$iconClass = $feedbackIconVendor . ' ' . $feedback;
		} else {
			$iconClass = $feedback;
		}
		$feedback = $this->Html->tag('i', '', array('style' => $style, 'class' => $iconClass . ' form-control-feedback'));

		// Set feedback html after input
		$this->_setCustom('afterInput', $this->_getCustom('afterInput') . $feedback);
		$this->_hasFeedback = true;
	}

/**
 * Overrides parent method to remove the error rendering right after input.
 * Error is rendered in _getInput() method to support proper html structure.
 *
 * @param array $options
 * @return array
 */
	protected function _getFormat($options) {
		if ($options['type'] === 'hidden') {
			return array('input');
		}
		if (is_array($options['format']) && in_array('input', $options['format'])) {
			return $options['format'];
		}

		if ($options['type'] === 'checkbox') {
			return array('before', 'label', 'between', 'input', 'after');
		}
		return array('before', 'label', 'between', 'input', 'after');
	}

/**
 * Overwrites parent method to replace label with bootstrap style.
 * Extracted from https://github.com/slywalker/cakephp-plugin-boost_cake
 *
 * @param array $elements
 * @param array $parents
 * @param boolean $showParents
 * @param array $attributes
 * @return array
 */
	protected function _selectOptions($elements = array(), $parents = array(), $showParents = null, $attributes = array()) {
		$selectOptions = parent::_selectOptions($elements, $parents, $showParents, $attributes);
		if ($attributes['style'] === 'checkbox') {
			foreach ($selectOptions as $key => $option) {
				$option = preg_replace('/<div.*?>/', '', $option);
				$option = preg_replace('/<\/div>/', '', $option);
				if (preg_match('/>(<label.*?>)/', $option, $match)) {
					$class = $attributes['class'];

					$inline = !$class && $this->_getCustom('inline');
					if ($inline) {
						$class = 'checkbox-inline';
					}

					if (preg_match('/.* class="(.*)".*/', $match[1], $classMatch)) {
						$class = $classMatch[1] . ' ' . $attributes['class'];
						$match[1] = str_replace(' class="' . $classMatch[1] . '"', '', $match[1]);
					}
					$option = $match[1] . preg_replace('/<label.*?>/', ' ', $option);
					$option = preg_replace('/(<label.*?)(>)/', '$1 class="' . $class . '"$2', $option);

					if (!$inline) {
						$option = $this->Html->tag('div', $option, array('class' => 'checkbox'));
					}
				}
				$selectOptions[$key] = $option;
			}
		}

		return $selectOptions;
	}

/**
 * Overrides parent method to allow:
 * - Enable rendering always a <ul> element even if only one error is present by setting
 *   'errorsAlwaysAsList' => true in global inputDefaults options
 *
 * @param string $field
 * @param mixed $text
 * @param array $options
 * @return string
 */
	public function error($field, $text = null, $options = array()) {
		$defaults = array(
			'wrap' => true,
			'class' => 'error-message',
			'escape' => true,
		);
		$options = array_merge($defaults, $options);
		$this->setEntity($field);

		$error = $this->tagIsInvalid();
		if ($error === false) {
			return null;
		}

		if (is_array($text)) {
			if (isset($text['attributes']) && is_array($text['attributes'])) {
				$options = array_merge($options, $text['attributes']);
				unset($text['attributes']);
			}
			$tmp = array();
			foreach ($error as &$e) {
				if (isset($text[$e])) {
					$tmp[] = $text[$e];
				} else {
					$tmp[] = $e;
				}
			}
			$text = $tmp;
		}

		if ($text !== null) {
			$error = $text;
		}
		if (is_array($error)) {
			foreach ($error as &$e) {
				if (is_numeric($e)) {
					$e = __d('cake', 'Error in field %s', Inflector::humanize($this->field()));
				}
			}
		}
		if ($options['escape']) {
			$error = h($error);
			unset($options['escape']);
		}
		if (is_array($error)) {
			if (count($error) > ($this->_getCustom('errorsAlwaysAsList') ? 0 : 1)) {
				$listParams = array();
				if (isset($options['listOptions'])) {
					if (is_string($options['listOptions'])) {
						$listParams[] = $options['listOptions'];
					} else {
						if (isset($options['listOptions']['itemOptions'])) {
							$listParams[] = $options['listOptions']['itemOptions'];
							unset($options['listOptions']['itemOptions']);
						} else {
							$listParams[] = array();
						}
						if (isset($options['listOptions']['tag'])) {
							$listParams[] = $options['listOptions']['tag'];
							unset($options['listOptions']['tag']);
						}
						array_unshift($listParams, $options['listOptions']);
					}
					unset($options['listOptions']);
				}
				array_unshift($listParams, $error);
				$error = call_user_func_array(array($this->Html, 'nestedList'), $listParams);
			} else {
				$error = array_pop($error);
			}
		}

		if ($options['wrap']) {
			$tag = is_string($options['wrap']) ? $options['wrap'] : 'div';
			unset($options['wrap']);
			return $this->Html->tag($tag, $error, $options);
		}

		return $error;
	}

/**
 * Returns available form styles configured via 'Bs3.Form.styles'.
 *
 * @return array
 */
	public function listFormStyles() {
		return array_keys(Configure::read('Bs3.Form.styles'));
	}

/**
 * Processes registered configuration options for a form.
 * Configuration available levels are:
 * 1. Default options defined in $this->myFormDefaults and $this->myInputDefaults
 * 2. Form style options (if any) defined in Bs3.Form.styles.STYLE.formDefaults/inputDefaults
 * 3. inputDefaults passed on Form::create() options
 * 4. (For input) Options passed on Form::input()
 *
 * @param array $options
 * @return array
 */
	protected function _processConfig($options) {
		// Get form style
		$options = $this->_detectFormStyle($options);

		$styleFormDefaults = $styleInputDefaults = array();
		if ($this->formStyle) {
			$styleFormDefaults = (array) Configure::read('Bs3.Form.styles.' . $this->formStyle . '.formDefaults');
			$styleInputDefaults = (array) Configure::read('Bs3.Form.styles.' . $this->formStyle . '.inputDefaults');
		}

		// Process input configuration
		$this->inputOptions = Hash::merge(
			$this->_myInputDefaults,
			Configure::check('Bs3.Form.inputDefaults') ? Configure::read('Bs3.Form.inputDefaults') : array(),
			$this->_processCustomConfig('input', $styleInputDefaults),
			$this->_processCustomConfig('input', $this->_extractOption('inputDefaults', $options, array()))
		);

		// Process form configuration
		unset($options['inputDefaults']);
		$this->formOptions = Hash::merge(
			$this->_myFormDefaults,
			Configure::check('Bs3.Form.formDefaults') ? Configure::read('Bs3.Form.formDefaults') : array(),
			$this->_processCustomConfig('form', $styleFormDefaults),
			$this->_processCustomConfig('form', $options)
		);

		//dd($this->formOptions);
		if ($this->formStyle && empty($this->formOptions['class'])) {
			$this->formOptions['class'] = 'form-' . $this->formStyle;
		}
	}

	function _processCustomConfig($type, $options) {
		$typeDefaults = '_my' . ucfirst($type) . 'Defaults';
		$customKeys = array_keys($this->{$typeDefaults}['custom']);

		$processed = array('custom' => array());
		if (isset($options['custom'])) {
			$processed['custom'] = $options['custom'];
			unset($options['custom']);
		}
		$processed = Hash::merge($processed, $options);

		foreach ($options as $key => $value) {
			if (in_array($key, $customKeys)) {
				$processed['custom'][$key] = $options[$key];
				unset($processed[$key]);
			}
		}

		return $processed;
	}

	function _getCustom($key) {
		return $this->_extractOption($key, $this->currentInputOptions['custom'], null);
	}

	function _setCustom($key, $value) {
		$this->currentInputOptions['custom'][$key] = $value;
	}

/**
 * Obtains form style from passed custom 'formStyle' option.
 * Style can also be detected by the the 'class' option when it is horizontal or inline.
 * Sets $this->formStyle property.
 *
 * @param array $options
 * @return array
 */
	protected function _detectFormStyle($options) {
		$this->formStyle = $this->_extractOption('formStyle', $options);
		$class = $this->_extractOption('class', $options);

		if (!$this->formStyle) {
			if (!$class) {
				return $options;
			}

			$registeredStyles = $this->listFormStyles();
			foreach ($registeredStyles as $style) {
				$regex = sprintf('/^form-%1$s|\sform-%1$s/', $style);
				if (preg_match($regex, $class)) {
					$this->formStyle = $style;
					break;
				}
			}
		}
		unset($options['formStyle']);

		return $options;
	}
}

