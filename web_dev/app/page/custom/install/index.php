<?php
/**
 * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
 *
 * @link https://steamcommunity.com/profiles/76561198038416053
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */

// задаём основную кодировку страницы.
header('Content-Type: text/html; charset=utf-8');

// Отключаем показ ошибок.
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Ограничиваем время выполнения скрипта.
set_time_limit(3);

// Нахожение в пространстве LR.
define('IN_LR', true);

// Директория содержащая дополнительные блоки вэб-приложения.
define('PAGE_CUSTOM', '../../../../app/page/custom/');

// Директория с основными конфигурационными файлами.
define('INCLUDES', '../../../../app/includes/');

// Директория с CSS шаблонами.
define('ASSETS_CSS', '../../../../storage/assets/css/');

// Директория с JS библиотеками.
define('ASSETS_JS', '../../../../storage/assets/js/');

// Директория с основными кэш-файлами.
define('SESSIONS', '../../../../storage/cache/sessions/');

// Директория содержащая графические кэш-файлы.
define('CACHE', '../../../../storage/cache/');

// Директория содержащая кэш-файлы модулей.
define('MODULES_CACHE', '../../../../storage/cache/modules/');

// Регистраниция основных функций.
require INCLUDES . 'functions.php';

// Проверка на PDO
class_exists('PDO') || get_iframe('001','Для работы нужна поддержка PDO');

// Проверка на BCMath
extension_loaded('bcmath') == 0 && get_iframe('001','Расширение для PHP не было найдено :: BCMath');

// Проверка на cURL
extension_loaded('curl') == 0 && get_iframe('001','Расширение для PHP не было найдено :: cURL');

// Проверка на json
extension_loaded('json') == 0 && get_iframe('001','Расширение для PHP не было найдено :: json');

// Проверка на Zip
extension_loaded('zip') == 0 && get_iframe('001','Расширение для PHP не было найдено :: Zip');

// Проверка на GMP
extension_loaded('gmp') == 0 && get_iframe('001','Расширение для PHP не было найдено :: GMP');

// Проверка прав доступа каталога кэша ( 0777 )
substr( sprintf( '%o', fileperms( SESSIONS ) ), -4) !== '0777' && get_iframe( '002','Не установлены права доступа 777 на директорию :: /storage/cache/sessions/' );

// Создание папки - avatars
! file_exists( CACHE . 'img/avatars/' ) && mkdir( CACHE . 'img/avatars/', 0777, true );

// Проверка прав доступа на кэш аватарок ( 0777 )
substr( sprintf( '%o', fileperms( CACHE . 'img/avatars/' ) ), -4) !== '0777' && get_iframe( '002','Не установлены права доступа 777 на директорию :: /storage/cache/img/avatars/' );

// Создание папки - slim
! file_exists( CACHE . 'img/avatars/slim/' ) && mkdir( CACHE . 'img/avatars/slim/', 0777, true );

// Проверка прав доступа на кэш слим - аватарок ( 0777 )
substr( sprintf( '%o', fileperms( CACHE . 'img/avatars/slim/' ) ), -4) !== '0777' && get_iframe( '002','Не установлены права доступа 777 на директорию :: /storage/cache/img/avatars/slim/' );

// Создание папки - css
! file_exists( ASSETS_CSS ) && mkdir( ASSETS_CSS, 0777, true );

// Проверка на существование каталога с генерируемыми файлами
! file_exists( ASSETS_CSS . 'generation/' ) && mkdir( ASSETS_CSS . 'generation/', 0777, true );

// Проверка прав доступа на ассеты - CSS ( 0777 )
substr( sprintf( '%o', fileperms( ASSETS_CSS ) ), -4) !== '0777' && get_iframe( '002','Не установлены права доступа 777 на директорию :: /storage/assets/css/' );

// Создание папки - js
! file_exists( ASSETS_JS ) && mkdir( ASSETS_JS, 0777, true );

// Проверка на существование каталога с генерируемыми файлами
! file_exists( ASSETS_JS . 'generation/' ) && mkdir( ASSETS_JS . 'generation/', 0777, true );

// Проверка прав доступа на ассеты - JS ( 0777 )
substr( sprintf( '%o', fileperms( ASSETS_JS ) ), -4) !== '0777' && get_iframe( '002','Не установлены права доступа 777 на директорию :: /storage/assets/js/' );

// Проверка на существование файла с настройками
if ( ! file_exists( SESSIONS . '/options.php' ) ):
    $options['theme'] = 'mainstream_white';
    $options['enable_css_cache'] = 0;
    $options['enable_js_cache'] = 0;
    $options['white_palette'] = 'original_palette';
    $options['dark_palette'] = 'dark_mode_palette';
    $options['graphics_container'] = 'stretch';
    $options['disable_sidebar_change'] = 0;
    $options['disable_palettes_change'] = 0;
    $options['graphics.sidebar_blur'] = 0;
    $options['graphics.blocks_blur'] = 0;
    $options['background_image'] = 'null';
    $options['session_check'] = 1;
    $options['avatars_cache_time'] = 259200;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min( $options ) . ";\n");
endif;

// Проверка на существование файла с базой данных
! file_exists( SESSIONS . '/db.php' ) && file_put_contents( SESSIONS . '/db.php', '<?php return []; ' );

// Создание/возобновление сессии.
session_start();

// Получение информации из конфигурационного файла.
$options = require SESSIONS . '/options.php';

// Получение информации о базе данных.
$db = require SESSIONS . '/db.php';

if ( ! empty( $db ) ):
    $mysqli = new mysqli( $db['Core'][0]['HOST'], $db['Core'][0]['USER'], $db['Core'][0]['PASS'], $db['Core'][0]['DB'][0]['DB'], $db['Core'][0]['PORT'] );
    $result = $mysqli->query('SELECT `steamid`, `group`, `flags`, `access` FROM lvl_web_admins');
    $row = $result->fetch_assoc();
    $mysqli->close();
    if( empty( $row ) ):
        $admins = 1;
    endif;
endif;

// Язык
if ( empty( $options['language'] ) && isset( $_POST['EN'] ) || isset( $_POST['RU'] ) ):
    $options['language'] = isset( $_POST['EN'] ) ? 'EN' : 'RU';
    file_put_contents( SESSIONS . '/options.php', '<?php return '.var_export_min( $options ).";\n" );
    header_fix( get_url(2) );
endif;

// Информация о серверах

if ( empty( $options['short_name'] ) && empty( $options['full_name'] ) && empty( $options['info'] ) && empty( $options['site'] ) && isset( $_POST['servers_info_save'] ) ) {
    $options['short_name'] = $_POST['servers_name'];
    $options['full_name'] = $_POST['servers_full_name'];
    $options['info'] = $_POST['servers_info'];
    $URL = '//' . $_SERVER["HTTP_HOST"] . explode('/app/',$_SERVER['REQUEST_URI'])[0];
    $options['site'] = substr( $URL, -1 ) == '/' ? $URL : $URL . '/';
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min( $options ) . ";\n");
    header_fix( get_url(2) );
}

// WEB KEY API

if ( empty( $options['web_key'] ) && ! empty( $_POST['web_key'] ) ) {
    $result = curl_init( 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $_POST['web_key'] . '&steamids=76561198038416053' );
    curl_setopt($result, CURLOPT_RETURNTRANSFER, 1);
    $url = curl_exec($result);
    $data = json_decode( $url, true )['response']['players'];
    if( $data[0]['steamid'] == 76561198038416053 ) {
        $options['web_key'] = $_POST['web_key'];
        $options['steam_only_authorization'] = 1;
        $options['steam_auth'] = 1;
        $options['only_steam_64'] = 0;
        $options['avatars'] = 1;
        file_put_contents( SESSIONS . '/options.php', '<?php return '.var_export_min( $options ).";\n" );
        header_fix( get_url(2) );
    } else {
        $error = true;
    }
} elseif ( empty( $options['web_key'] ) && isset( $_POST['nope'] ) ) {
    $options['web_key'] = 1;
    $options['steam_only_authorization'] = 0;
    $options['steam_auth'] = 0;
    $options['only_steam_64'] = 0;
    $options['avatars'] = 2;
    file_put_contents( SESSIONS . '/options.php', '<?php return '.var_export_min( $options ).";\n" );
    header_fix( get_url(2) );
}

// Sidebar

if ( empty( $options['sidebar_open'] ) && ! is_int ( $options['sidebar_open'] ) && isset( $_POST['sidebar_open'] ) || isset( $_POST['sidebar_close'] ) ) {
    $options['sidebar_open'] = isset( $_POST['sidebar_open'] ) ? (int) 1 : (int) 0;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min($options) . ";\n");
    header_fix( get_url(2) );
}

// Бэйджи

if ( empty( $options['badge_type'] ) && ! is_int ( $options['badge_type'] ) && isset( $_POST['badge_type_1'] ) || isset( $_POST['badge_type_2'] ) ) {
    $options['badge_type'] = isset( $_POST['badge_type_1'] ) ? (int) 1 : (int) 2;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min($options) . ";\n");
    header_fix( get_url(2) );
}

// Form Border

if ( empty( $options['form_border'] ) && ! is_int ( $options['form_border'] ) && isset( $_POST['form_border_0'] ) || isset( $_POST['form_border_1'] ) ) {
    $options['form_border'] = isset( $_POST['form_border_1'] ) ? (int) 1 : (int) 0;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min($options) . ";\n");
    header_fix( get_url(2) );
}

// animation

if ( empty( $options['animations'] ) && ! is_int ( $options['animations'] ) && isset( $_POST['animations_on'] ) || isset( $_POST['animations_off'] ) ) {
    $options['animations'] = isset( $_POST['animations_on'] ) ? (int) 1 : (int) 0;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min($options) . ";\n");
    header_fix( get_url(2) );
}

// dark_mode

if ( ! is_numeric( $options['dark_mode'] ) && ( isset( $_POST['dark_mode_on'] ) || isset( $_POST['dark_mode_off'] ) ) ) {
    $options['dark_mode'] = isset( $_POST['dark_mode_on'] ) ? (int) 1 : (int) 0;
    file_put_contents(SESSIONS . '/options.php', '<?php return ' . var_export_min($options) . ";\n");
    header_fix( get_url(2) );
}

// admin

if ( isset( $_POST['check_admin_steam'] ) ) {
    $_SESSION['admin'] = con_steam32to64( $_POST['admin'] );
    $result = curl_init( 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $options['web_key'] . '&steamids=' . $_SESSION['admin'] . ' ' );
    curl_setopt($result, CURLOPT_RETURNTRANSFER, 1);
    $url = curl_exec($result);
    $data = json_decode( $url, true )['response']['players'][0];
}

if ( ! empty( $admins ) && isset( $_POST['check_admin_steam_da'] ) && isset( $_SESSION['admin'] ) ) {
    $mysqli = new mysqli( $db['Core'][0]['HOST'], $db['Core'][0]['USER'], $db['Core'][0]['PASS'], $db['Core'][0]['DB'][0]['DB'], $db['Core'][0]['PORT'] );
    $mysqli->query("INSERT INTO lvl_web_admins (steamid, `user`, `password`, `ip`, `group`, `flags`, `access`) VALUES ( '{$_SESSION['admin']}', '', '', '{$_SERVER['REMOTE_ADDR']}', '1', 'z', '100' )");
    $mysqli->close();
    refresh();
}

if ( isset( $_POST['check_admin_steam_net'] ) && isset( $_SESSION['admin'] ) ) {
    unset( $_SESSION['admin'] );
    header_fix( get_url(2) );
}

if ( ! empty( $admins ) && isset( $_POST['admin_nosteam_save'] ) ) {
    $login = action_text_clear($_POST['admin_login']);
    $pass = action_text_clear($_POST['admin_pass']);
    $steam = action_text_clear($_POST['admin_steam']);
    $mysqli = new mysqli( $db['Core'][0]['HOST'], $db['Core'][0]['USER'], $db['Core'][0]['PASS'], $db['Core'][0]['DB'][0]['DB'], $db['Core'][0]['PORT'] );
    $mysqli->query("INSERT INTO lvl_web_admins (steamid, `user`, `password`, `ip`, `group`, `flags`, `access`) VALUES ( '{$steam}', '{$login}', '{$pass}', '{$_SERVER['REMOTE_ADDR']}', '1', 'z', '100' )");
    $mysqli->close();
    refresh();
}

// Проверка соединения с базой данных

if( empty( $db ) && isset( $_POST['db_check'] ) ) {

    $mysqli = new mysqli( $_POST['HOST'], $_POST['USER'], $_POST['PASS'], $_POST['DATABASE'], $_POST['PORT'] );

    if ( empty( $_POST['TABLE'] ) ):
        if( $_POST['STATS'] == 'LevelsRanks'):
            $db_table = 'lvl_base';
        elseif( $_POST['STATS'] == 'FPS' ):
            $db_table = 'fps_servers_stats';
        elseif( $_POST['STATS'] == 'RankMeKento' ):
            $db_table = 'rankme';
        endif;
    else:
        $db_table = $_POST['TABLE'];
    endif;

    $result = $mysqli->query('SELECT kills FROM ' . $db_table . ' ORDER BY kills DESC LIMIT 1');
    $row = $result->fetch_assoc();

    if ( ! empty( $row ) ) {

        $db_check = 2;

        $mysqli->query('CREATE TABLE IF NOT EXISTS lr_web_notifications (
                  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                  steam VARCHAR(128) NOT NULL,
                  text VARCHAR(256) NOT NULL,
                  values_insert VARCHAR(512) NOT NULL,
                  url VARCHAR(128) NOT NULL,
                  icon VARCHAR(64) NOT NULL,
                  seen int(11) NOT NULL,
                  status int(11) NOT NULL,
                  date TIMESTAMP NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $mysqli->query('CREATE TABLE IF NOT EXISTS lvl_web_admins (
                  `steamid` VARCHAR(32) PRIMARY KEY, 
                  `user` VARCHAR(32) NOT NULL,
                  `password` VARCHAR(64) NOT NULL,
                  `ip` VARCHAR(16) NOT NULL,
                  `group` VARCHAR(11) NOT NULL,
                  `flags` VARCHAR(32) NOT NULL,
                  `access` int(3) NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $mysqli->query('CREATE TABLE IF NOT EXISTS lvl_web_servers (
                            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
							`ip` VARCHAR(64) NOT NULL,
                            `fakeip` VARCHAR(64) NOT NULL,
                            `name` VARCHAR(64) NOT NULL,
                            `name_custom` VARCHAR(128) NOT NULL,
                            `rcon` VARCHAR(64) NOT NULL,
                            `server_stats` VARCHAR(64) NOT NULL,
                            `server_vip` VARCHAR(64) NOT NULL,
                            `server_vip_id` INT(11) NOT NULL,
                            `server_sb` VARCHAR(64) NOT NULL,
                            `server_shop` VARCHAR(64) NOT NULL,
                            `server_warnsystem` VARCHAR(64) NOT NULL,
                            `server_lk` VARCHAR(64) NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $mysqli->query('CREATE TABLE IF NOT EXISTS lvl_web_settings ( `name` VARCHAR(64) PRIMARY KEY, `value` VARCHAR(256) NOT NULL ) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $db = [$_POST['STATS'] => [
            ['HOST' => $_POST['HOST'],
                'PORT' => $_POST['PORT'],
                'USER' => $_POST['USER'],
                'PASS' => $_POST['PASS'],
                'DB' => [
                    ['DB' => $_POST['DATABASE'],
                        'Prefix' => [
                            ['table' => $db_table,
                                'name' => $_POST['NAME'],
                                'mod' => empty($_POST['game_mod']) ? 730 : $_POST['game_mod'],
                                'ranks_pack' => 'default',
                                'steam' => (int) $_POST['steam_mod']
                            ]
                        ]
                    ]
                ]
            ]
        ], 'Core' => [
            ['HOST' => $_POST['HOST'],
                'PORT' => $_POST['PORT'],
                'USER' => $_POST['USER'],
                'PASS' => $_POST['PASS'],
                'DB' => [
                    ['DB' => $_POST['DATABASE'],
                        'Prefix' => [
                            ['table' => 'lvl_']
                        ]
                    ]
                ]
            ]
        ]
        ];
        $mysqli->close();
        file_put_contents( SESSIONS . '/db.php', '<?php return '.var_export_opt( $db, true ).";" );
        header_fix( get_url(2) );
    } else {
        $db_check = 1;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Добро пожаловать в мастер установки LR!</title>
</head>
<link rel="stylesheet" href="../../../../storage/assets/css/themes/mainstream_white/style.css">
<link rel="stylesheet" href="../../../../app/page/custom/install/assets/css/style.css">
<style>
    :root <?php echo str_replace( '"', '', str_replace( '",', ';', file_get_contents_fix ( '../../../../storage/assets/css/themes/mainstream_white/palettes/dark_mode_palette.json' ) ) )?>
</style>
<body>
<div class="container-fluid">
    <div class="row">
<?php
// Проверка на язык страницы
if ( empty( $options['language'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/language.php';
elseif ( empty( $db ) ):
    require PAGE_CUSTOM . 'install/includes/options/db.php';
elseif ( empty( $options['web_key'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/webkey.php';
elseif ( ! empty( $admins ) && $options['web_key'] == 1 ):
    require PAGE_CUSTOM . 'install/includes/options/admin_no_steam.php';
elseif ( ! empty( $admins ) && $options['web_key'] !== 1 ):
    require PAGE_CUSTOM . 'install/includes/options/admin_steam.php';
elseif ( empty( $options['full_name'] ) || empty( $options['short_name'] ) || empty( $options['info'] ) || empty( $options['site'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/name.php';
elseif ( empty( $options['sidebar_open'] ) && ! is_int ( $options['sidebar_open'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/sidebar.php';
elseif ( empty( $options['badge_type'] ) && ! is_int ( $options['badge_type'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/badge_type.php';
elseif ( empty( $options['form_border'] ) && ! is_int ( $options['form_border'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/form_border.php';
elseif ( empty( $options['animations'] ) && ! is_int ( $options['animations'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/animations.php';
elseif ( empty( $options['dark_mode'] ) && ! is_int ( $options['dark_mode'] ) ):
    require PAGE_CUSTOM . 'install/includes/options/dark_mode.php';
else:
    header_fix( '//' . $_SERVER["HTTP_HOST"] . explode('/app/',$_SERVER['REQUEST_URI'])[0] );
    die();
endif?>
</div>
</div>
</body>
</html>
