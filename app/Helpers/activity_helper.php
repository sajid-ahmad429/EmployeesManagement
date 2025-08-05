<?php

/**
 * This file is part of the CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * CodeIgniter Array Helpers
 */
if (!function_exists('track_activity')) {

    function track_activity($model, $data, $id, $table_name) {
        $previousUpdateData = array();
        $select_array = array_keys($data);
        $previousUpdateData = $model->select("'" . implode(',', $select_array) . "'")->where('id', $id)->findAll();
        $resultDiffUpdate = array();
        $resultDiffUpdate = array_diff($previousUpdateData[0], $data);

        if ($resultDiffUpdate != NULL) {
            // Activity Track Starts
            $trans_OldData = $update_data = $update_where_to_array = array();
            $trans_OldData = $previousUpdateData[0];
            $update_data = $data;
            $update_where_to_array['id'] = $id;
            $userRole = $_SESSION['role'];
            $userName = $_SESSION['firstname'] . " " . $_SESSION['lastname'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_text = $userRole . ", " . $userName . ' updated cms data from ' . $ip;
            // $method = 0=insert, 1=update, 2=activate, 3=deactivate, 4=permanently_deactivate    
            $method = 0;
            activity_log_update($method, $log_text, $table_name, $trans_OldData, $update_data, $update_where_to_array);
        }
    }

}


