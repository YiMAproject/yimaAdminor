<?php
use Zend\Stdlib\ArrayUtils;

/**
 * @var $this \yimaTheme\Theme\Theme
 */
$sm = $this->getServiceManager();

// Set AssetsManager Config ... {
$themeConf = array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__.DS.'www',
            ),
        ),
    ),
);

$mergedConf = $sm->get('Config');
$config     = ArrayUtils::merge($mergedConf, $themeConf);

$sm->setAllowOverride(true);
$sm->setService('config',$config);
$sm->setAllowOverride(false);
# ... }

// Attach Assets file into base template ... {
$viewRenderer = $sm->get('viewRenderer');

$viewRenderer->headScript()
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/jquery.navgoco.js')
;
$viewRenderer->inlineScript()
    ->appendFile($viewRenderer->basePath().'/adminstrap/js/main.js')
;

$viewRenderer->headLink()
    ->appendStylesheet($viewRenderer->basePath().'/adminstrap/css/main.css')
;
# ... }

// Attach Menu HTML tags into body ...{
$events = $sm->get('sharedEventManager');
$events->attach(
    'Zend\Mvc\Application',
    \Zend\Mvc\MvcEvent::EVENT_RENDER,
    function($e) use ($viewRenderer, $sm) {
        /** @var $e \Zend\Mvc\MvcEvent */
        $response    = $e->getResponse();
        $content     = $response->getContent();

        $adminorMenu = $viewRenderer->render('partial/bootstrap3/adminpanel-injected-script');
        $content = str_replace(
            '<body>',
            '<body>'.$adminorMenu,
            $content
        );
        $response->setContent($content);
    },
    -100000
);
# ... }
