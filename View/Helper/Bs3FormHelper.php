<?php

App::uses('FormHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class Bs3FormHelper extends FormHelper {

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html'
	);


/**
 * Available options.
 *
 * @var array
 */
	protected $_availableCustomOptions = array(
		'wrap',
		'externalWrap',
		'checkboxLabel',
		'beforeInput',
		'afterInput',
		'help',
		'errorClass',
		'showError',
		'errorMessage',
		'errorsAlwaysAsList',
		'inputGroup',
		'feedback',
		'renderErrorBlockAlways',
		'renderHelpBlockAlways',
		'submitDiv',
	);

/**
 * Default options for form.
 *
 * @var array
 */
	protected $_formDefaults = array(
		'role' => 'form'
	);

/**
 * Default options for inputs.
 * Used by default FormHelper class.
 *
 * @var array
 */
	protected $_predefinedInputDefaults = array(
		'all' => array(
			'div' => array(
				'class' => 'form-group'
			),
			'class' => 'form-control',
			'error' => array(
				'attributes' => array(
					'externalWrap' => 'div',
					'class' => 'help-block error-block'
				)
			),

			// Default custom options for all inputs
			'externalWrap' => false,
			'wrap' => false,
			'beforeInput' => false,
			'afterInput' => false,
			'checkboxLabel' => false,
			'help' => false,
			'errorClass' => 'has-error',
			'showError' => true, // Si mostrar o no error
			'errorMessage' => false, // Mensaje de error manual
			'errorsAlwaysAsList' => false,
			'inputGroup' => false,
			'feedback' => false,
			'renderErrorBlockAlways' => false,
			'renderHelpBlockAlways' => false,
			'submitDiv' => false
		),
		'default' => array(
			'label' => array(
				'class' => 'control-label'
			),
		),
		'horizontal' => array(
			'label' => array(
				'class' => 'col-sm-2 control-label'
			),
			'wrap' => 'col-sm-10',
			'submitDiv' => 'col-sm-10 col-sm-offset-2'
		),
		'inline' => array(
			'label' => array(
				'class' => 'sr-only'
			),
		),
	);

/**
 * Handles custom options for current input.
 *
 * @var string
 */
	protected $_customInputDefaults = array();

/**
 * Handles custom options for current input.
 *
 * @var string
 */
	protected $_customInputOptions = array();

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
	protected $_formStyle = null;

	protected $_hasFeedback = false;

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
		// Get form style
		$options = $this->_getFormStyle($options);

		// Generate global input defaults
		$globalInputDefaults = Hash::merge($this->_predefinedInputDefaults, Configure::read('Bs3Form.inputDefaults'));
		// Generate form style defaults
		$styleInputDefaults = Hash::merge($globalInputDefaults['all'], $globalInputDefaults[$this->_formStyle]);
		// Merge with passed inputDefaults if any
		$passedInputDefaults = $this->_extractOption('inputDefaults', $options, array());
		// Set helper inputDefaults
		$inputDefaults = Hash::merge($styleInputDefaults, $passedInputDefaults);

		// Process custom input defaults and remove keys from passed options
		$this->_customInputDefaults = array();
		foreach ($this->_availableCustomOptions as $name) {
			if (isset($inputDefaults[$name])) {
				$this->_customInputDefaults[$name] = $inputDefaults[$name];
			}
			unset($inputDefaults[$name]);
		}
		$options['inputDefaults'] = $inputDefaults;

		// Process form defaults
		$formDefaults = Hash::merge($this->_formDefaults, Configure::read('Bs3Form.formDefaults'));
		$options = Hash::merge($formDefaults, $options);

		$out = parent::create($model, $options);
		return $out;
	}


	public function end($options = null, $secureAttributes = array()) {
		if (is_array($options) && !isset($options['div'])) {
			if ($this->_formStyle == 'horizontal') {
				$options['div'] = $this->_customInputOptions['submitDiv'];
			} else {
				$options['div'] = false;
			}
		}
		$out = parent::end($options);

		if ($this->_formStyle == 'horizontal') {
			$out = $this->Html->tag('div', $out, array('class' => 'form-group'));
		}

		$this->_customInputOptions = null;
		$this->_inputOptions = null;
		$this->_formStyle = null;

		return $out;
	}

/**
 * Redefine para permitir el uso de configuracion predeterminada y adicional
 * a traves del metodo _initOptions.
 * Ver las opciones adicionales soportadas por helper en $_customOptions.
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function input($fieldName, $options = array()) {
		$this->_inputType = null;
		$this->_fieldName = $fieldName;
		$options = $this->_initInputOptions($options);
		$html = parent::input($fieldName, $options);

		if ($this->_formStyle == 'inline') {
			$html .= ' ';
		}
		return $html;
	}

/**
 * Parsea opciones, incluyendo las propias de este helper.
 * Evita redefinir parseOptions, solo puede llamarse una vez por input y ademas
 * parseOptions no tiene Hash::merge.
 * Redefine label.
 * Saca las custom de inputDefaults.
 *
 * @param array $options
 * @return array
 */
	protected function _initInputOptions($options) {
		// If label is string, transform it to proper label options array
		if (isset($options['label']) && is_string($options['label'])) {
			$options['label'] = array(
				'text' => $options['label']
			);
		}

		// TODO: refactor
		if (empty($this->_formStyle)) {
			$inputDefaults = Hash::merge($this->_predefinedInputDefaults, Configure::read('Bs3Form.inputDefaults'));
			$typeInputDefaults = Hash::merge($inputDefaults['all'], $inputDefaults['default']);
			$passedInputDefaults = $this->_extractOption('inputDefaults', $options, array());
			$this->_inputDefaults = Hash::merge($typeInputDefaults, $passedInputDefaults);
			$this->_customInputDefaults = $this->_extractOption('custom', $this->_inputDefaults, array());
			unset($this->_inputDefaults['custom']);
		}

		$options = Hash::merge(
			array('before' => null, 'between' => null, 'after' => null, 'format' => null),
			$this->_inputDefaults,
			$options
		);

		$this->_customInputOptions = $this->_customInputDefaults;
		foreach ($this->_availableCustomOptions as $name) {
			if (isset($options[$name])) {
				$this->_customInputOptions[$name] = $options[$name];
			}
			unset($options[$name]);
		}

		$this->_inputOptions = $options;
		$this->_initInputGroup();
		$this->_initFeedback();
		return $options;
	}

/**
 * Overwrites parent method to generate a boostrap input.
 *
 * @param type $args
 * @return string
 */
	protected function _getInput($args) {
		// Obtiene opciones de BS
		$customOptions = $this->_customInputOptions;

		// Si es de tipo select multiple con checkbox, no setear clase en checkoxes
		if ($args['type'] == 'checkbox' ||
		(
			$args['type'] == 'select' &&
			isset($args['options']['multiple']) &&
			$args['options']['multiple'] == 'checkbox' &&
			$args['options']['class'] == 'form-control')) {

			$args['options']['class'] = '';
		}

		// Render input field via parent method
		$input = parent::_getInput($args);

		// beforeInput html
		$beforeInput = $this->_extractOption('beforeInput', $this->_customInputOptions);

		// afterInput html
		$afterInput = $this->_extractOption('afterInput', $this->_customInputOptions);

		// beforeInput + input + afterInput
		$inputHtml = $beforeInput . $input . $afterInput;

		// Checkbox label rendering
		if ($args['type'] == 'checkbox') {
			if ($customOptions['checkboxLabel']) {
				$inputHtml = $this->Html->tag('label', $inputHtml . ' ' . $customOptions['checkboxLabel']);
			}
			$inputHtml = $this->Html->tag('div', $inputHtml, array('class' => 'checkbox'));
		}

		// Error rendering, overwrites parent rendering
		$errorHtml = null;
		$errorOptions = $this->_extractOption('error', $this->_inputOptions, null);
		$showError = $this->_extractOption('showError', $this->_customInputOptions);
		if ($this->_inputType !== 'hidden' && $errorOptions !== false && $showError) {

			$customErrorMessage = $this->_extractOption('errorMessage', $this->_customInputOptions);
			if (!$customErrorMessage) {
				$errorHtml = $this->error($this->_fieldName, $errorOptions);
			} else {
				$errorHtml = $this->error($this->_fieldName, $errorOptions, array(), (array) $customErrorMessage);
			}

			if (empty($errorHtml) && $this->_extractOption('renderErrorBlockAlways', $this->_customInputOptions)) {

			}
		}

		// Help block rendering
		$help = $this->_extractOption('help', $this->_customInputOptions, '');
		$helpHtml = null;
		if ($help || $this->_extractOption('renderHelpBlockAlways', $this->_customInputOptions)) {
			$helpHtml = $this->Html->tag('div', $help, array('class' => 'help-block'));
		}

		// Set size of input if enabled, and get full html of input and block
		$wrap = $this->_extractOption('externalWrap', $this->_customInputOptions);
		$size = $this->_extractOption('wrap', $this->_customInputOptions);
		if ($size) {
			if ($wrap) {
				$inputHtml = $this->Html->tag('div', $inputHtml, array('class' => $size));
				$inputHtml = $this->Html->tag('div', $inputHtml, array('class' => 'row'));
			} else {
				$allHtml = $inputHtml . $errorHtml . $helpHtml;
				$allHtml = $this->Html->tag('div', $allHtml, array('class' => $size));
			}
		} else {
			$allHtml = $inputHtml . $errorHtml . $helpHtml;
		}

		// Wrap everything if enabled
		if ($wrap) {
			$allHtml = $inputHtml . $errorHtml . $helpHtml;
			$wrap = $this->_extractOption('externalWrap', (array) $wrap, $wrap);
			$allHtml = $this->Html->tag('div', $allHtml, array('class' => $wrap));
		}

		return $allHtml;
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
		if ($this->tagIsInvalid() !== false || $this->_customInputOptions['errorMessage']) {
			$divOptions = $this->addClass($divOptions, $this->_customInputOptions['errorClass']);
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
		if (isset($attributes['inline'])) {
			$inline = $attributes['inline'];
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

		return $out;
	}

/**
 * Generates a static from control.
 *
 * @param string $label
 * @param string $options
 * @param array $options
 * @return string
 */
	public function cstatic($label, $html, $options = array()) {
		$options['type'] = 'static';

		$options = $this->addClass($options, 'form-control-static');
		$options = $this->_initInputOptions($options);

		$html = $this->Html->tag('div', $html, array('class' => $options['class']));

		// If size is set (like in an horizontal form) wrap inside div.size
		if ($this->_customInputOptions['wrap']) {
			$html = $this->Html->tag('div', $html, array('class' => $this->_customInputOptions['wrap']));
		}

		if (!empty($options['label'])) {
			$label = $this->Html->tag('label', $label, $options['label']);
		} else {
			$label = null;
		}

		$divOptions = $this->_divOptions($options);
		unset($divOptions['tag']);
		$html = $this->Html->tag('div', $label . $html, $divOptions);

		return $html;
	}

/**
 * Genera input group.
 *
 * @param array $options
 * @return array
 */
	protected function _initInputGroup() {
		$options = $this->_customInputOptions;
		$inputGroupOptions = $this->_extractOption('inputGroup', $options);

		if (!$inputGroupOptions) {
			return;
		}
		unset($this->_customInputOptions['inputGroup']);

		// Check for prepend option
		// If option stats with 'fa-' or 'glyphicon-', it will automatically add the 'fa' or 'glyphicon' class
		$prepend = $this->_extractOption('prepend', $inputGroupOptions);
		if ($prepend) {
			if (substr($prepend, 0, 3) == 'fa-') {
				$prepend = $this->Html->tag('i', '', array('class' => 'fa ' . $prepend));
			}
			elseif (substr($prepend, 0, 10) == 'glyphicon-') {
				$prepend = $this->Html->tag('i', '', array('class' => 'glyphicon ' . $prepend));
			}
			$prepend = $this->Html->tag('span', $prepend, array('class' => 'input-group-addon'));
		}

		// Check for append option
		// If option stats with 'fa-' or 'glyphicon-', it will automatically add the 'fa' or 'glyphicon' class
		$append = $this->_extractOption('append', $inputGroupOptions);
		if ($append) {
			if (substr($append, 0, 3) == 'fa-') {
				$append = $this->Html->tag('i', '', array('class' => 'fa ' . $append));
			}
			elseif (substr($prepend, 0, 10) == 'glyphicon-') {
				$append = $this->Html->tag('i', '', array('class' => 'glyphicon ' . $append));
			}
			$append = $this->Html->tag('span', $append, array('class' => 'input-group-addon'));
		}

		// Generates div and sets beforeInput and afterInput options
		$divOptions = array('class' => 'input-group');
		$size = $this->_extractOption('size', $inputGroupOptions);
		if ($size) {
			$divOptions = $this->addClass($divOptions, 'input-group-' . $size);
		}
		$this->_customInputOptions['beforeInput'] .= $this->Html->tag('div', null, $divOptions) . $prepend;
		$this->_customInputOptions['afterInput'] = $append . '</div>' . $this->_customInputOptions['afterInput'];
	}

/**
 * Generates input feedback.
 *
 * @param array $options
 * @return array
 */
	protected function _initFeedback() {
		$options = $this->_customInputOptions;
		$feedback = $this->_extractOption('feedback', $options);

		$this->_hasFeedback = false;

		if (!$feedback) {
			return;
		}

		$this->_hasFeedback = true;

		$style = null;
		if ($this->_customInputOptions['externalWrap'] || $this->_customInputOptions['wrap']) {
			$style = 'top:0; right: 15px';
		} elseif ($this->_formStyle == 'inline') {
			$style = 'top:0;';
		}

		unset($this->_customInputOptions['feedback']);

		if (substr($feedback, 0, 3) == 'fa-') {
			$feedback = $this->Html->tag('i', '', array('style' => $style, 'class' => 'fa ' . $feedback . ' form-control-feedback'));
		}
		elseif (substr($feedback, 0, 10) == 'glyphicon-') {
			$feedback = $this->Html->tag('i', '', array('style' => $style, 'class' => 'glyphicon ' . $feedback . ' form-control-feedback'));
		} else {
			$feedback = $this->Html->tag('i', '', array('style' => $style, 'class' => $feedback . ' form-control-feedback'));
		}

		$this->_customInputOptions['afterInput'] = $this->_customInputOptions['afterInput'] . $feedback;
	}

/**
 * overwrites parent _getFormat() to remove error rendering after input and set in proper div.
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
 * Overwrite FormHelper::_selectOptions() to replace label with bootstrap style.
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

					$inline = !$class && isset($this->_inputOptions['inline']) && $this->_inputOptions['inline'];
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
 * Obtains form style from passed class option or formStyle option.
 * Sets $this->_formStyle property.
 *
 * @param array $options
 * @return array
 */
	protected function _getFormStyle($options) {
		$formStyle = $this->_extractOption('formStyle', $options);

		if (empty($formStyle)) {
			if (isset($options['class'])) {
				if (strpos($options['class'], 'form-horizontal') !== false) {
					$formStyle = 'horizontal';
				}
				elseif (strpos($options['class'], 'form-inline') !== false) {
					$formStyle = 'inline';
				}
			} elseif (in_array($formStyle, array('horizontal', 'inline'))) {
				$options['class'] = 'form-' . $formStyle;
			}
		}

		if (empty($formStyle)) {
			$formStyle = 'default';
		}
		if (empty($options['class'])) {
			$options['class'] = 'form-' . $formStyle;
		}

		$this->_formStyle = $formStyle;
		unset($options['formStyle']);
		return $options;
	}

/**
 * Overwrites parent method to allow setting a custom error message.
 *
 * @param string $field
 * @param string|array $text
 * @param array $options
 * @return string
 */
	public function error($field, $text = null, $options = array(), $customErrors = false) {
		$defaults = array('externalWrap' => true, 'class' => 'error-message', 'escape' => true);
		$options = array_merge($defaults, $options);
		$this->setEntity($field);

		if ($customErrors === false) {
			$error = $this->tagIsInvalid();
			if ($error === false) {
				return null;
			}
		} else {
			$error = $customErrors;
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
			if (count($error) > 1 || ($this->_customInputOptions['errorsAlwaysAsList'] && count($error) > 0)) {
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
		if ($options['externalWrap']) {
			$tag = is_string($options['externalWrap']) ? $options['externalWrap'] : 'div';
			unset($options['externalWrap']);
			return $this->Html->tag($tag, $error === null ? '' : $error, $options);
		}
		return $error;
	}
}
