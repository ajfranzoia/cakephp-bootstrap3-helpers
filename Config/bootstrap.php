<?php

/**
 * Default options for inputs.
 * Used by default FormHelper class.
 *
 * @var array
 */

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
