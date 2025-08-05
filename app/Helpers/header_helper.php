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
setlocale(LC_MONETARY, 'en_IN');

function bd_nice_number1($num) {
    $num = explode('.', $num);
    $dec = (count($num) == 2) ? '.' . $num[1] : '.00';
    $num = (string) $num[0];
    if (strlen($num) < 4)
        return $num;
    $tail = substr($num, -3);
    $head = substr($num, 0, -3);
    $head = preg_replace("/\B(?=(?:\d{2})+(?!\d))/", ",", $head);
    return $head . "," . $tail . $dec;
}

function getHeaderData() {
    $db = \Config\Database::connect();
    $buildersocialData = $db->table('social_data');
    $siteData = $buildersocialData->get()->getResultArray();
    return $siteData;
}

function getGlobalSeoData($pageName) {
    $db = \Config\Database::connect();

    $buildersocialData = $db->table('social_data');
    $data['seoData'] = $buildersocialData->get()->getResultArray();
    return $data;
}

function IPtoLocation($ip) {
    $apiURL = 'https://freegeoip.app/json/' . $ip;

    // Make HTTP GET request using cURL 
    $ch = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $apiResponse = curl_exec($ch);
    if ($apiResponse === FALSE) {
        $msg = curl_error($ch);
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    // Retrieve IP data from API response 
    $ipData = json_decode($apiResponse, true);
    // Return geolocation data 
    return !empty($ipData) ? $ipData : false;
}

if (!function_exists('activity_log_update')) {

    function activity_log_update($method, $log_text, $table_name, $trans_cmsOldData, $update_cms_data, $update_where_to_array) {
        //Activity Track Starts

        $ip = $_SERVER['REMOTE_ADDR'];
        $log_data['method'] = $method;
        $log_data['tableName'] = $table_name;
        $log_data['logText'] = $log_text;
        $log_data['address'] = $ip;
        $log_data['employee_id'] = $_SESSION['employee_id'];
        $log_data['employee_name'] = $_SESSION['employee_name'];
        $log_data['timestamp'] = date('Y-m-d H:i:s');
        $log_data['old_data'] = json_encode($trans_cmsOldData);
        $log_data['updated_data'] = json_encode($update_cms_data);
        $log_data['where_to'] = json_encode($update_where_to_array);

        $db = \Config\Database::connect();
        $builder1 = $db->table('activitymaster');
        $result = $builder1->insert($log_data); // corrected to use insert()

        return $result; // return the result of the insert operation
    }

}


if (!function_exists('track_activity')) {

    function track_activity($previousUpdateData,$model, $data, $id, $table_name, $method) {
        $resultDiffUpdate = array();
        if (count($previousUpdateData) > 0) {
            $resultDiffUpdate = array_diff($previousUpdateData[0], $data); // use array_diff_assoc to handle associative arrays
        }
        
        if (!empty($resultDiffUpdate)) {
            // Activity Track Starts
            $trans_OldData = $update_data = $update_where_to_array = array();
            $trans_OldData = $previousUpdateData[0];
            $update_data = $data;
            $update_where_to_array['id'] = $id;
            $userRole = $_SESSION['employee_type'];
            $userName = $_SESSION['employee_name'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_text = $userRole . ", " . $userName . ' updated cms data from ' . $ip;
            // $method = 0=insert, 1=update, 2=activate, 3=deactivate, 4=permanently_deactivate    
            // changed method to 1 for update
            activity_log_update($method, $log_text, $table_name, $trans_OldData, $update_data, $update_where_to_array);
        }
    }

}

