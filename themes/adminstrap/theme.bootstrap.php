<?php
use Zend\Stdlib\ArrayUtils;

/**
 * @var $this \yimaTheme\Theme\Theme
 */

/**
 * Theme Resolver Run This Bootstrap And
 * Fall into Next Theme With Resolver Till Get
 * Into Final Theme
 *
 * By Default is True
 */
$this->isFinal = false;
//$this->setCaptureTo('body');

// Menu HTML tags
$this->setTemplate('partial/administrap/injected-script');

// ================================================================================================================================================

$sm = $this->getServiceLocator();

// ---- Attach Assets file into base template -----------------------------------------------------------------------------------------------------\
/** @var $viewRenderer \Zend\View\Renderer\PhpRenderer */
$viewRenderer = $sm->get('viewRenderer');

$viewRenderer->headScript()
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/jquery.navgoco.js')
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/main.js')
;

$viewRenderer->headLink()
    ->appendStylesheet($viewRenderer->basePath().'/adminstrap/css/main.css')
;

// ---- Register Assets File Into AssetManager Service --------------------------------------------------------------------------------------------\
/*
 * These Config must merged to application config at last
 * : see below
 */
$ovverideConfigs = array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__.DS.'www',
            ),
        ),
    ),
);

// ---- Merge new config to application merged config ---------------------------------------------------------------------------------------------\
$mergedConf = $sm->get('Config');
$config     = ArrayUtils::merge($mergedConf, $ovverideConfigs);

$sm->setAllowOverride(true);
$sm->setService('config', $config);
$sm->setAllowOverride(false);
