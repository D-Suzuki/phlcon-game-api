<?php
error_reporting(E_ALL);

use \Phalcon\Mvc\Application;
use \Phalcon\Di\FactoryDefault;
use \Phalcon\Loader;
use \Phalcon\Mvc\Router;
use \Phalcon\Mvc\Dispatcher;

use \Phalbase\Config\Manager    as ConfigManager;
use \Phalbase\Db\Manager        as DbManager;

define('BASE_PATH',   dirname(__DIR__));
define('APPS_PATH',   BASE_PATH . '/apps');
define('CONFIG_PATH', BASE_PATH . '/config');
define('LIBS_PATH',   BASE_PATH . '/libs');
define('SYSTEM_PATH', BASE_PATH . '/system');
define('TASKS_PATH',  BASE_PATH . '/tasks');

try {

    /**
     * App定数をロード
     */
    require_once APPS_PATH . '/AppConst.php';

    /**
     * Appレジストリを初期化
     */
    require_once APPS_PATH . '/AppRegistry.php';
    AppRegistry::intialize();

    /**
     * ルーティング設定
     */
    $Di = new FactoryDefault;
    $Di->set('router', function() {
        return require_once APPS_PATH . '/routes.php';
    });
    $Router = $Di->get('router');
    $Router->handle($_SERVER['REQUEST_URI']);

    /**
     * ディスパッチャー
     */
    $Di->set(
        'dispatcher',
        function () use($Router) {
            $Dispatcher = new Dispatcher();
            $Dispatcher->setDefaultNamespace('Controllers\\' . $Router->getModuleName());
            return $Dispatcher;
        }
    );

    /**
     * オートローダー登録
     */
    $Loader = new Loader();
    $Loader->registerNamespaces(
        [
            'Phalbase'     => LIBS_PATH . '/Phalbase/',
            'Util'         => LIBS_PATH . '/Util',
            'Config'       => APPS_PATH . '/config/',
            'Controllers'  => APPS_PATH . '/controllers/',
            'Logger'       => APPS_PATH . '/logger/',
            'Logics'       => APPS_PATH . '/logics/',
            'Beans'        => APPS_PATH . '/models/beans/',
            'Cache'        => APPS_PATH . '/models/cache/',
            'Db'           => APPS_PATH . '/models/db',
            'GameObject'   => APPS_PATH . '/models/game_object/',
            'Master'       => APPS_PATH . '/models/master/',
            'PlayerObject' => APPS_PATH . '/models/player_object/',
            'Traits'       => APPS_PATH . '/models/traits',
        ]
    );
    $Loader->register();

    var_dump(\Config\Db\DbWrite::load());exit;

    DbManager::addConfig(\Config\Db\DbWrite::load());
    DbManager::addConfig(\Config\Db\DbRead::load());

    LoggerManager::addConfig(\Config\Log\LogApp::load());
    LoggerManager::addConfig(\Config\Log\LogPayment::load());

    DbManager::useCustomListener();


echo 'test';exit;




    /*$Di->set(
        "view",
        function () {
            $view = new View();
            // $view->setViewsDir("../apps/backend/views/");
            return $view;
        }
    );*/

    /**
     * 設定クラスセット
     */
    ConfigManager::setClass('app',      Config\App::class);
    ConfigManager::setClass('db',       Config\Db::class);
    ConfigManager::setClass('di',       Config\Di::class);
    ConfigManager::setClass('log',      Config\Log::class);
    ConfigManager::setClass('memcache', Config\Memcache::class);
    ConfigManager::setClass('redis',    Config\Redis::class);

var_dump(new Config\Db);exit;

    /**
     * DIコンテナセット
     */
	//$Config  = ConfigManager::getConfig('di');
    //$Service = new Service();
    //$Di      = $Service->createDefaultDi($Config);

    /**
     * ディスパッチ
     */
    $Dispatcher = $Di->getShared('dispatcher');
    $Dispatcher->setModuleName($Router->getModuleName());
    $Dispatcher->setControllerName($Router->getControllerName());
    $Dispatcher->setActionName($Router->getActionName());
    $Dispatcher->setParams($Router->getParams());
    $Dispatcher->dispatch();

} catch (\Exception $e) {
        echo $e->getMessage();exit;
    AppLogger::error($e->getMessage());

    /**
     * トランザクションロールバック
     */
    /*if (DbManager::hasBeginedConnection()) {
        DbManager::allRollback();
    }*/
    echo $e->getMessage();

}
