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

// ================================================================================================================================================

$sm = $this->getServiceLocator();

if (!$sm->get('ModuleManager')->getModule('yimaJquery'))
    throw new \Exception('Administrap Theme Need YimaJquery Module Enabled And Loaded.');

// ---- Attach Assets file into base template -----------------------------------------------------------------------------------------------------\
$viewRenderer = $sm->get('viewRenderer');

/**
 * print it in your viewScript layout
 *
 * <?php echo $this->jQuery() ?>
 */
$viewRenderer->jQuery()
    ->enable()
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/jquery.navgoco.js')
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/main.js')
;

$viewRenderer->headLink()
    ->appendStylesheet($viewRenderer->basePath().'/adminstrap/css/main.css')
;

// ---- Attach Menu HTML tags into body -----------------------------------------------------------------------------------------------------------\
$events = $sm->get('sharedEventManager');
$events->attach(
    'Zend\Mvc\Application',
    \Zend\Mvc\MvcEvent::EVENT_RENDER,
    function($e) use ($viewRenderer, $sm) {
        $permissionsManager = $sm->get('yimaAuthorize.PermissionsManager');
        /** @var $permission \yimaAuthorize\Permission\PermissionInterface */
        $permission = $permissionsManager->get('yima_adminor');
        if (!$permission->getIdentity()) {
            // user not authorized to adminor
            return 0;
        }

        /** @var $e \Zend\Mvc\MvcEvent */
        $response    = $e->getResponse();
        $content     = $response->getContent();

        $adminorMenu = $viewRenderer->render('partial/administrap/injected-script');
        $content = str_replace(
            '<body>',
            '<body>'.$adminorMenu,
            $content
        );
        $response->setContent($content);
    },
    -100000
);

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
