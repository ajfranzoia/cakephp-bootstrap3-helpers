CakePHP Bootstrap 3 FormHelper
=============================

Yet another Cake 2.x form helper, but with minimal coding and highly configurable.

Feel free to make any code/docs contributions or post any issues.

[![Build Status](https://travis-ci.org/Codaxis/cakephp-bootstrap3-helpers.svg?branch=master)](https://travis-ci.org/Codaxis/cakephp-bootstrap3-helpers)

## Installation

1. Install with composer by running `composer require codaxis/cakephp-bootstrap3-helpers:1.1` or clone/copy files into `app/Plugin/Bs3Helpers`

2. Include the plugin in your bootstrap.php with `CakePlugin::load('Bs3Helpers')` or `CakePlugin::load('Bs3Helpers', array('bootstrap' => true))` to load included default Bootstrap form styles.

3. Load helper in your ```AppController```. Use classname option if you want to keep your helper alias as Form.

	```php
	// In AppController.php
	public $helpers = array('Bs3Helpers.Bs3Form');
	// or
	public $helpers = array('Form' => array('className' => 'Bs3Helpers.Bs3Form'));
	```

4. Define your custom form styles at wish in your bootstrap.php


## Form helper usage options

**BsFormHelper::create() custom options:**

- ```formStyle``` => shortcut for custom form styles. E.g.: ```formStyle => inline``` will add 'form-inline' class to form tag

- ```submitDiv``` => if set, will enable end() method div option and set passed div class. Useful to be used gloabally or with a defined form style

**Bs3FormHelper::input() default base options:**

- ```class``` => ```'form-control'```
- ```div``` => ```array('class' => 'form-group')```
- ```label``` => ```array('class' => 'control-label')```
- ```error``` => ```array('attributtes' => 'array('class' => 'help-block'))```


**Bs3FormHelper::input() custom options:**

- ```beforeInput``` => html code to prepend right before the input.
- ```afterInput``` => html code to append right after the input.
- ```wrap``` => if set, will wrap the form input inside a div with the css class passed.
Useful for input sizing.
- ```help``` => help text to show after input. Will be rendered inside a div with .help-block class.
- ```errorClass``` => Error class for .form-group div. Defaults to 'has-error'.
- ```errorsAlwaysAsList``` => if set to true, will render error messages always inside as list, no matter if there's only one error. Useful to ensure ui consistency.
- ```feedback``` => allows feedback icons in text inputs, passing ```fa-icon-name``` or ```glyphicon-icon-name``` will render the full ```<i>``` tag.
- ```inputGroup``` => array options that supports the following params:
   - ```size``` => can be ```sm``` or ```lg```
   - ```prepend```: html code to prepend. If it starts with ```fa``` or ```glyphicon```, will be interpreted as an icon and the full icon tag will be rendered.
   - ```append```: html code to prepend. If it starts with ```fa``` or ```glyphicon```, will be interpreted as an icon and the full icon tag will be rendered.
- ```externalWrap``` => if set, the whole input div (without taking into account .help-block) will be wrapped inside another div with the given class will be applied, preventing unnecessary shrinking in some cases. Solves this issue https://github.com/twbs/bootstrap/issues/9694.
- ```checkboxLabel```: if set, will wrap a single checkbox inside a ```div.checkbox``` and a ```label``` with the passed text.
- ```inline``` => used in conjuntion with checkbox and radio groups to allow inline display.


## Global form styles

Global form styles can be defined in bootstrap.php and used anywhere by passing the ```formStyle``` option in create() method.

Inbuilt styles horizontal and inline included are defined like:

```php
Configure::write('Bs3.Form.styles', array(
	'horizontal' => array(
		'formDefaults' => array(
			'submitDiv' => 'col-sm-10 col-sm-offset-2'
		),
		'inputDefaults' => array(
			'label' => array(
				'class' => 'col-sm-2 control-label'
			),
			'wrap' => 'col-sm-10',
		)
	),
	'inline' => array(
		'inputDefaults' => array(
			'label' => array(
				'class' => 'sr-only'
			),
		)
	)
));
```

## Html helper usage options

TODO

## License

Licensed under [MIT License](http://www.opensource.org/licenses/mit-license.php)
