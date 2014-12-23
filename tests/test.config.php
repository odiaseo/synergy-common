<?php
return array(
	'controllers'  => array(
		'invokables' => array(
			'SynergyCommonTest\Controller\Test' => 'SynergyCommonTest\Controller\TestController',
		),
	),
	'router'       => array(
		'routes' => array(
			'api\home' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'controller' => 'SynergyCommonTest\Controller\Test',
						'action'     => 'index',
					),
				),
			),
		),
	),
);
