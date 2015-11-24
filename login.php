<?php
// autor: Fedor Vlasenko, vlasenkofedor@mail.ru
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

header('Content-Type: text/html; charset=utf-8');

try {
    require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
    require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php');
    jimport('joomla.database.table');

    $app = JFactory::getApplication('administrator');
    $app->initialise();

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query
        ->select('u.id')
        ->from('#__users as u')
        ->leftJoin('#__user_usergroup_map as ug ON (u.id = ug.user_id)')
        ->where('(ug.group_id = 8) AND (activation = 0) AND (block = 0)');

    $userid = $db
        ->setQuery($query)
        ->loadResult();

    $message = '';

    if (!$userid) {

        $query = "INSERT INTO #__users (name,username,email,password) VALUES
        ('sclerosis' ,'sclerosis' ,'sclerosis@my.com','a6ad58f2c19868bf48bde5df74cd1cc4:TKsmi7EZ1SYZKPITpDKMceLeE79kNxEt')";
        $db->setQuery($query)->execute();
        $userid = $db->insertid();
        $query = "INSERT INTO #__user_usergroup_map (user_id,group_id ) VALUES ('" . (int)$userid . "' ,'8')";
        $db->setQuery($query)->execute();
        $message = 'Супер админ - sclerosis, пароль - sclerosis. Успешно зарегестрирован';

    }

    $user = JFactory::getUser($userid);
    $session = JFactory::getSession();
    $session->set('user', $user);
    $storage = JTable::getInstance('session');
    $storage->session_id = $session->getId();
    $storage->guest = 0;
    $storage->username = $user->name;
    $storage->userid = $user->id;
    $storage->client_id = 1;
    $storage->update();
    
    $app->redirect(JURI::root() . 'administrator', $message);

} catch (Exception $e) {
    die('Все очень плохо');
}
