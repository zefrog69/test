<?php

// Fichier d'amorçage // bootstrap
// Definition du chemin du dossier de l'application
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Définition de l'environnement de l'application
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?
getenv('APPLICATION_ENV') : 'production'));

// Ajout du chemin de la bibilothèque de ZF à l'include path

if(!defined('ROOT_DIR')) define('ROOT_DIR', dirname(__FILE__));

set_include_path('.'
    . PATH_SEPARATOR . ROOT_DIR . '/'
    . PATH_SEPARATOR . ROOT_DIR . '/application'
    . PATH_SEPARATOR . ROOT_DIR . '/library/'
    . PATH_SEPARATOR . get_include_path()
);


echo 'preProot';
echo 'pr00t';

// on charge et on initialise l'autoloader de Zend
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();


$sampleConfig = APPLICATION_PATH.'/config.sample.ini';
$customConfig = APPLICATION_PATH.'/config.ini';
$usedConfig = is_file($customConfig) ? $customConfig : $sampleConfig;



error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 1);
ini_set('magic_quotes_gpc', 'off');

// relance la session afin de traiter les objets mis en session par ZEND (sinon erreur PHP définition de classe)
// + autorise l'autoload Joomla (1.5)

// TODO, mettre dans le Bootstrap
if(defined('_JEXEC')){
    $version = new JVersion();
    if($version->RELEASE < 1.6){
        spl_autoload_register('__autoload');
    }
    
    session_write_close();
    session_start();
}


try {
    // Création de l'application et du bootstrap puis démarrage.
    if(!Zend_Registry::isRegistered('Zend_Application')) {
        $application = new Zend_Application(APPLICATION_ENV, $usedConfig);
        $application->bootstrap();
        Zend_Registry::set('Zend_Application', $application);
    }
    else {
        $application = Zend_Registry::get('Zend_Application');
    }

    
	echo 'ezhkerhr';
    $application->run();

     // on vide le body
    $newResponse = new Zend_Controller_Response_Http();
    $application->getBootstrap()->getResource('frontController')->setResponse($newResponse);

    // on vide le head
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->view->headScript()->exchangeArray(array());
    $viewRenderer->view->headStyle()->exchangeArray(array());
    $viewRenderer->view->headLink()->exchangeArray(array());
    $viewRenderer->view->inlineScript()->exchangeArray(array());

/*
    $db = Zend_Registry::get('db');
    $profileur = $db->getProfiler();
    
    $tempsTotal       = $profileur->getTotalElapsedSecs();
    $nombreRequetes   = $profileur->getTotalNumQueries();
    $tempsLePlusLong  = 0;
    $requeteLaPlusLongue = null;

    foreach ($profileur->getQueryProfiles() as $query) {
        if ($query->getElapsedSecs() > $tempsLePlusLong) {
            $tempsLePlusLong  = $query->getElapsedSecs();
            $requeteLaPlusLongue = $query->getQuery();
        }
    }

    echo 'Exécution de '
       . $nombreRequetes
       . ' requêtes en '
       . $tempsTotal
       . ' secondes' . "<br >";
    echo 'Temps moyen : '
       . $tempsTotal / $nombreRequetes
       . ' secondes' . "<br >";
    echo 'Requêtes par seconde: '
       . $nombreRequetes / $tempsTotal
       . ' seconds' . "<br >";
    echo 'Requête la plus lente (secondes) : '
       . $tempsLePlusLong . "<br >";
    echo "Requête la plus lente (SQL) : <br >"
       . $requeteLaPlusLongue . "<br >";
*/

	echo 'BLABLABLA';



} catch (Zend_Exception $e) {

    // mode débug
    echo $e->getMessage() . "<br /><br />";
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";
    die();


}



ini_set('display_errors', 0);
