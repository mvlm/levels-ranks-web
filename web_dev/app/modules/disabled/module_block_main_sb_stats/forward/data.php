<?php
/**
 * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
 *
 * @link https://steamcommunity.com/profiles/76561198038416053
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */

empty( $Db->db_data['SourceBans'] ) && get_iframe( '012','Не найден мод - SourceBans  :: /storage/cache/sessions/db.php' );

// Получаем кэша данного модуля.
$data['module_block_main_sb_stats'] = $Modules->get_module_cache('module_block_main_sb_stats');

// Проверяем актуальность кэша.
if ( ( empty( $data['module_block_main_sb_stats'] ) ) || ( ! empty( $data['module_block_main_sb_stats']['time'] ) && time() > $data['module_block_main_sb_stats']['time'] ) ) {

    // Затираем страные данные которые могут помешать созданию кэша.
    $data['module_block_main_sb_stats']['count_admins'] = 0;
    $data['module_block_main_sb_stats']['count_bans'] = 0;
    $data['module_block_main_sb_stats']['count_comms'] = 0;
    $data['module_block_main_sb_stats']['time'] = 0;

    // Сохраняем текущее время и прибавляем к нему 1 час.
    $data['module_block_main_sb_stats']['time'] = time() + $Modules->array_modules['module_block_main_sb_stats']['setting']['cache_time'];
    $data['module_block_main_sb_stats']['count_admins'] += $Db->queryNum('SourceBans', $Db->db_data['SourceBans'][0]['USER_ID'], $Db->db_data['SourceBans'][0]['DB_num'], 'SELECT COUNT(1) FROM ' . $Db->db_data['SourceBans'][ 0 ]['Table'] . 'admins LIMIT 1' )[0]-1;
    $data['module_block_main_sb_stats']['count_bans'] += $Db->queryNum('SourceBans', $Db->db_data['SourceBans'][0]['USER_ID'], $Db->db_data['SourceBans'][ 0 ]['DB_num'], 'SELECT COUNT(1) FROM ' . $Db->db_data['SourceBans'][ 0 ]['Table'] . 'bans LIMIT 1' )[0];
    $data['module_block_main_sb_stats']['count_comms'] += $Db->queryNum('SourceBans', $Db->db_data['SourceBans'][0]['USER_ID'], $Db->db_data['SourceBans'][ 0 ]['DB_num'], 'SELECT COUNT(1) FROM ' . $Db->db_data['SourceBans'][ 0 ]['Table'] . 'comms LIMIT 1' )[0];
    
    // Сохраняем новый кэш для данного модуля.
    $Modules->set_module_cache( 'module_block_main_sb_stats', $data['module_block_main_sb_stats'] );
}