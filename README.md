CakePHP Bootstrap 3 FormHelper
=============================

[![Build Status](https://travis-ci.org/Codaxis/cakephp-bootstrap3-helpers.svg?branch=master)](https://travis-ci.org/Codaxis/cakephp-bootstrap3-helpers)

Yet another Cake 2.x form helper, but with minimal coding and highly configurable.

Feel free to make any code/docs contributions or post any issues.

## Usage options


**BsFormHelper::create() custom options:**

- ```formStyle```: shortcut for custom form styles. E.g.: ```formStyle => inline``` will add 'form-inline' class to form tag

- ```submitDiv```: if set, will enable end() method div option and set passed div class. Useful to be used gloabally or with a defined form style



**Bs3FormHelper::input() default base options:**

```php
class => 'form-control',
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
)
```

**Bs3FormHelper::input() default custom options:**

- ```beforeInput```: html code to prepend right before the input.
- ```afterInput```: html code to append right after the input.
- ```wrap```: if set, will wrap the form input inside a div with the css class passed.
Useful for input sizing.
- ```help```: help text to show after input. Will be rendered inside a div with .help-block class.
- ```errorClass```: Error class for .form-group div. Defaults to 'has-error'.
- ```errorsAlwaysAsList```: if set to true, will render error messages always inside as list, no matter if there's only one error. Useful to ensure ui consistency.
- ```feedback``` => false,
- ```inputGroup``` => false,
- ```externalWrap``` => false,
- ```checkboxLabel```: if set, will wrap a single checkbox inside a label with the passed text.
- ```inline``` => false,

## License

Licensed under [MIT License](http://www.opensource.org/licenses/mit-license.php)
