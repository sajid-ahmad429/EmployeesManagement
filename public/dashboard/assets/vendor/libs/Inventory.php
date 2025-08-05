<?php

/**
 * --------------------------------------------------------------------
 * CI4- ACF- Mahalaxmi SIlks
 * --------------------------------------------------------------------
 *
 * This content is released under the MIT License (MIT)
 *
 * @package    Mahalaxmi Silk ACF
 * @author     Altctrlfix private limited
 * @license     
 * @link        
 * @since      Version 1.0
 * 
 */

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VarsModel;
use App\Models\BrandColourConeModel;
use App\Models\BrandJariJariModel;
use App\Models\BrandZariThreadModel;
use App\Models\MasterInventoryModel;
use App\Models\RelInventoryProducts;
use App\Models\RelInventoryProductsBatch;
use App\Models\ColourModel;
use App\Models\MasterInventoryDataTblModel;
use App\Models\MasterReturnProductModel;
use App\Models\RelReturnProductQuantityModel;
use App\Models\RelBeamBatchReelMachine;
use App\Models\RelSareeProductionModel;
use CodeIgniter\I18n\Time;

class Inventory extends BaseController {

    public function __construct() {

        $this->VarsModel = new VarsModel();
        $this->ColourModel = new ColourModel();
        $this->BrandColourConeModel = new BrandColourConeModel();
        $this->BrandJariJariModel = new BrandJariJariModel();
        $this->BrandZariThreadModel = new BrandZariThreadModel();
        $this->MasterInventoryModel = new MasterInventoryModel();
        $this->RelInventoryProducts = new RelInventoryProducts();
        $this->RelInventoryProductsBatch = new RelInventoryProductsBatch();
        $this->MasterInventoryDataTblModel = new MasterInventoryDataTblModel();
        $this->MasterReturnProductModel = new MasterReturnProductModel();
        $this->RelReturnProductQuantityModel = new RelReturnProductQuantityModel();
        $this->RelBeamBatchReelMachine = new RelBeamBatchReelMachine();
        $this->RelSareeProductionModel = new RelSareeProductionModel();
        $this->Session = session();
        $request = \Config\Services::request();
    }

    public function index() {
        $data = [];
        $data['inventoryData'] = $this->MasterInventoryModel->getAllAdminRecords();
        $data['varsData'] = $this->VarsModel->getAllAdminRecords();
        $data['colourData'] = $this->ColourModel->getAllAdminRecords();
        $select = "master_brand_colour_cone.*,mc.name as colour_name";
        $joinArray[0]['tablename'] = 'master_colour as mc';
        $joinArray[0]['joinCondition'] = 'mc.id=master_brand_colour_cone.colour_id';
        $joinArray[0]['joinType'] = 'left';
        $tableName = "master_brand_colour_cone";
        $whereArray['master_brand_colour_cone.status !='] = 2;
        $whereArray['master_brand_colour_cone.trash'] = 0;
        $orderByArray[0]['orderKey'] = 'master_brand_colour_cone.id';
        $orderByArray[0]['orderBy'] = 'DESC';
        $data['colourConeData'] = $this->BrandColourConeModel->getListAllJoinWhere($select, $joinArray, $whereArray, $orderByArray, $tableName);
        $selectTJ = "master_brand_zari_thread_cone.*,mc.name as colour_name";
        $joinArrayTJ[0]['tablename'] = 'master_colour as mc';
        $joinArrayTJ[0]['joinCondition'] = 'mc.id=master_brand_zari_thread_cone.thread_colour_id';
        $joinArrayTJ[0]['joinType'] = 'left';
        $tableNameTJ = "master_brand_zari_thread_cone";
        $whereArrayTJ['master_brand_zari_thread_cone.status !='] = 2;
        $whereArrayTJ['master_brand_zari_thread_cone.trash'] = 0;
        $orderByArrayTJ[0]['orderKey'] = 'master_brand_zari_thread_cone.id';
        $orderByArrayTJ[0]['orderBy'] = 'DESC';
        $data['jariThreadConeData'] = $this->BrandZariThreadModel->getListAllJoinWhere($selectTJ, $joinArrayTJ, $whereArrayTJ, $orderByArrayTJ, $tableNameTJ);
        $selectZZ = "master_brand_jari_jari_cone.*,mc.name as colour_name";
        $joinArrayZZ[0]['tablename'] = 'master_colour as mc';
        $joinArrayZZ[0]['joinCondition'] = 'mc.id=master_brand_jari_jari_cone.jari_colour_id';
        $joinArrayZZ[0]['joinType'] = 'left';
        $tableNameZZ = "master_brand_jari_jari_cone";
        $whereArrayZZ['master_brand_jari_jari_cone.status !='] = 2;
        $whereArrayZZ['master_brand_jari_jari_cone.trash'] = 0;
        $orderByArrayZZ[0]['orderKey'] = 'master_brand_jari_jari_cone.id';
        $orderByArrayZZ[0]['orderBy'] = 'DESC';
        $data['jariJariConeData'] = $this->BrandJariJariModel->getListAllJoinWhere($selectZZ, $joinArrayZZ, $whereArrayZZ, $orderByArrayZZ, $tableNameZZ);
        $data['BrandColourCone'] = $this->BrandColourConeModel->getAllActiveRecordsByColour();
        $data['BrandZariThread'] = $this->BrandZariThreadModel->getAllActiveRecordsByColour();
        $data['BrandJariConeData'] = $this->BrandJariJariModel->getAllActiveRecordsByColour();
        // echo "<pre>";
        // print_r($data['BrandJariConeData']);
        // echo "<pre>";
        // exit;
        echo view('admin/templates/header', $data);
        echo view('admin/inventory/list');
        echo view('admin/templates/footer');
    }

    public function viewInventoryProductDetails() {
        $data = [];
        $data['varsData'] = $this->VarsModel->getAllAdminRecords();
        $data['colourData'] = $this->ColourModel->getAllAdminRecords();
        $select = "master_brand_colour_cone.*,mc.name as colour_name";
        $joinArray[0]['tablename'] = 'master_colour as mc';
        $joinArray[0]['joinCondition'] = 'mc.id=master_brand_colour_cone.colour_id';
        $joinArray[0]['joinType'] = 'left';
        $tableName = "master_brand_colour_cone";
        $whereArray['master_brand_colour_cone.status !='] = 2;
        $whereArray['master_brand_colour_cone.trash'] = 0;
        $orderByArray[0]['orderKey'] = 'master_brand_colour_cone.id';
        $orderByArray[0]['orderBy'] = 'DESC';
        $data['colourConeData'] = $this->BrandColourConeModel->getListAllJoinWhere($select, $joinArray, $whereArray, $orderByArray, $tableName);
        $selectTJ = "master_brand_zari_thread_cone.*,mc.name as colour_name";
        $joinArrayTJ[0]['tablename'] = 'master_colour as mc';
        $joinArrayTJ[0]['joinCondition'] = 'mc.id=master_brand_zari_thread_cone.thread_colour_id';
        $joinArrayTJ[0]['joinType'] = 'left';
        $tableNameTJ = "master_brand_zari_thread_cone";
        $whereArrayTJ['master_brand_zari_thread_cone.status !='] = 2;
        $whereArrayTJ['master_brand_zari_thread_cone.trash'] = 0;
        $orderByArrayTJ[0]['orderKey'] = 'master_brand_zari_thread_cone.id';
        $orderByArrayTJ[0]['orderBy'] = 'DESC';
        $data['jariThreadConeData'] = $this->BrandZariThreadModel->getListAllJoinWhere($selectTJ, $joinArrayTJ, $whereArrayTJ, $orderByArrayTJ, $tableNameTJ);
        $selectZZ = "master_brand_jari_jari_cone.*,mc.name as colour_name";
        $joinArrayZZ[0]['tablename'] = 'master_colour as mc';
        $joinArrayZZ[0]['joinCondition'] = 'mc.id=master_brand_jari_jari_cone.jari_colour_id';
        $joinArrayZZ[0]['joinType'] = 'left';
        $tableNameZZ = "master_brand_jari_jari_cone";
        $whereArrayZZ['master_brand_jari_jari_cone.status !='] = 2;
        $whereArrayZZ['master_brand_jari_jari_cone.trash'] = 0;
        $orderByArrayZZ[0]['orderKey'] = 'master_brand_jari_jari_cone.id';
        $orderByArrayZZ[0]['orderBy'] = 'DESC';
        $data['jariJariConeData'] = $this->BrandJariJariModel->getListAllJoinWhere($selectZZ, $joinArrayZZ, $whereArrayZZ, $orderByArrayZZ, $tableNameZZ);
        $data['BrandColourCone'] = $this->BrandColourConeModel->getAllActiveRecordsByColour();
        $data['BrandZariThread'] = $this->BrandZariThreadModel->getAllActiveRecordsByColour();
        $data['BrandJariConeData'] = $this->BrandJariJariModel->getAllActiveRecordsByColour();
        echo view('admin/templates/header');
        echo view('admin/inventory/inventoryProducts', $data);
        echo view('admin/templates/footer');
    }

    public function viewAvailableInventoryData() {
        $data = [];
        $data['inventoryProductsData'] = $this->RelInventoryProducts->where('available_quantity > 0')->where('status !=',2)->where('trash',0)->findAll();
        $data['varsData'] = $this->VarsModel->getAllAdminRecords();
        $data['colourConeData'] = $this->BrandColourConeModel->getAllAdminRecords();
        $data['jariJariConeData'] = $this->BrandJariJariModel->getAllAdminRecords();
        $data['jariThreadConeData'] = $this->BrandZariThreadModel->getAllAdminRecords();

        echo view('admin/templates/header', $data);
        echo view('admin/inventory/inventoryProducts');
        echo view('admin/templates/footer');
    }

    // public function viewInventoryBatchDetails($inventoryIde) {
    //     $data = [];
    //     $inventoryId = base64_decode($inventoryIde);
    //     $data['inventoryProductsData'] = $this->RelInventoryProducts->getAllAdminRecordsColourById($inventoryId);
    //     $data['inventoryProductsBatchData'] = $this->RelInventoryProductsBatch->getAllAdminRecordsColour($inventoryId);
    //     echo view('admin/templates/header');
    //     echo view('admin/inventory/inventoryBatchProducts', $data);
    //     echo view('admin/templates/footer');
    // }

    public function getRecordDetails() {
        $returnData['status'] = 0;
        $returnData['message'] = "Failed";
        $returnData['acftkn']['acftkname'] = csrf_token();
        $returnData['acftkn']['acftknhs'] = csrf_hash();
        if (isset($_SESSION['role']) && isset($_SESSION['id']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) && $_SESSION['id'] != 0) {
            if (isset($_POST['id'])) {
                $id = base64_decode($_POST['id']);
                $userData = $this->MasterInventoryModel->where('id', $id)->findAll();
                $inventoryProductsData = $this->RelInventoryProducts->where('parent_id', $id)->where('status !=',2)->where('trash',0)->findAll();
                $inventoryData = $this->MasterInventoryModel->where('id', $id)->where('status !=',2)->where('trash',0)->findAll();
                $inventoryProductsBatchData = $this->RelInventoryProducts->getAllAdminRecordsInventoryProduct($id);
                if ($userData != NULL) {
                    $responseData['inventoryData'] = $userData[0];
                    $responseData['inventoryProductsData'] = $inventoryProductsData;
                    $responseData['inventoryProductsBatchData'] = $inventoryProductsBatchData;
                    $responseData['acftkn']['acftkname'] = csrf_token();
                    $responseData['acftkn']['acftknhs'] = csrf_hash();
                    echo json_encode($responseData);
                } else {
                    $returnData['status'] = 2;
                    $returnData['message'] = "Failed Invalid Data...!";
                    echo json_encode($returnData);
                }
            } else {
                $returnData['status'] = 2;
                $returnData['message'] = "Failed Invalid Request...!";
                echo json_encode($returnData);
            }
        } else {
            echo json_encode($returnData);
        }
    }

    public function returnProductDetails() {
        $returnData['status'] = 0;
        $returnData['message'] = "Failed";
        $returnData['acftkn']['acftkname'] = csrf_token();
        $returnData['acftkn']['acftknhs'] = csrf_hash();
        if (isset($_SESSION['role']) && isset($_SESSION['id']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) && $_SESSION['id'] != 0) {
            if (isset($_POST['id'])) {
                $id = base64_decode($_POST['id']);
                $userData = $this->MasterInventoryModel
                ->select('master_inventory.*, master_return_product.return_reason,master_return_product.id as returnId') // Select the columns you need
                ->join('master_return_product', 'master_return_product.inventory_vendor_id = master_inventory.id', 'left') // Perform the join
                ->where('master_inventory.id', $id) // Apply the where clause
                ->findAll(); // Retrieve the results            
                $inventoryProductsData = $this->RelInventoryProducts->getAllAdminRecordsInventoryProduct($id);
                // $inventoryProductsData = $this->RelInventoryProducts->where('parent_id', $id)->where('status !=',2)->where('trash',0)->findAll();
                $inventoryData = $this->MasterInventoryModel->where('id', $id)->where('status !=',2)->where('trash',0)->findAll();
                if ($userData != NULL) {
                    $responseData['inventoryData'] = $userData[0];
                    $responseData['inventoryProductsData'] = $inventoryProductsData;
                    $responseData['acftkn']['acftkname'] = csrf_token();
                    $responseData['acftkn']['acftknhs'] = csrf_hash();
                    echo json_encode($responseData);
                } else {
                    $returnData['status'] = 2;
                    $returnData['message'] = "Failed Invalid Data...!";
                    echo json_encode($returnData);
                }
            } else {
                $returnData['status'] = 2;
                $returnData['message'] = "Failed Invalid Request...!";
                echo json_encode($returnData);
            }
        } else {
            echo json_encode($returnData);
        }
    }

    public function viewInventoryDetails() {
        $returnData['status'] = 0;
        $returnData['message'] = "Failed";
        $returnData['acftkn']['acftkname'] = csrf_token();
        $returnData['acftkn']['acftknhs'] = csrf_hash();
        if (isset($_SESSION['role']) && isset($_SESSION['id']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) && $_SESSION['id'] != 0) {
            if (isset($_POST['id'])) {
                $id = base64_decode($_POST['id']);
                $userData = $this->MasterInventoryModel->where('id', $id)->findAll();
                if ($userData != NULL) {
                    $responseData['inventoryData'] = $userData[0];
                    $responseData['inventoryProductsData'] = $this->RelInventoryProducts->getAllAdminRecordsColour($id); 
                    $responseData['acftkn']['acftkname'] = csrf_token();
                    $responseData['acftkn']['acftknhs'] = csrf_hash();
                    echo json_encode($responseData);
                } else {
                    $returnData['status'] = 2;
                    $returnData['message'] = "Failed Invalid Data...!";
                    echo json_encode($returnData);
                }
            } else {
                $returnData['status'] = 2;
                $returnData['message'] = "Failed Invalid Request...!";
                echo json_encode($returnData);
            }
        } else {
            echo json_encode($returnData);
        }
    }

    public function viewInventoryBatchDetails() {
        $returnData['status'] = 0;
        $returnData['message'] = "Failed";
        $returnData['acftkn']['acftkname'] = csrf_token();
        $returnData['acftkn']['acftknhs'] = csrf_hash();
        if (isset($_SESSION['role']) && isset($_SESSION['id']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) && $_SESSION['id'] != 0) {
            if (isset($_POST['id'])) {
                $id = base64_decode($_POST['id']);
                $userData = $this->RelInventoryProducts->getAllAdminRecordsColourById($id);
                if ($userData != NULL) {
                    $responseData['inventoryProductsData'] = $userData;
                    $responseData['inventoryProductsBatchData'] = $this->RelInventoryProductsBatch->getAllAdminRecordsColour($id);
                    $responseData['acftkn']['acftkname'] = csrf_token();
                    $responseData['acftkn']['acftknhs'] = csrf_hash();
                    echo json_encode($responseData);
                } else {
                    $returnData['status'] = 2;
                    $returnData['message'] = "Failed Invalid Data...!";
                    echo json_encode($returnData);
                }
            } else {
                $returnData['status'] = 2;
                $returnData['message'] = "Failed Invalid Request...!";
                echo json_encode($returnData);
            }
        } else {
            echo json_encode($returnData);
        }
    }

    public function add() {
        // echo "<pre>";
        // print_r($_POST);
        // echo "<pre>";
        // exit;
        $dataRetrun['status'] = 0;
        $dataRetrun['message'] = "Failed";
        $dataRetrun['acftkn']['acftkname'] = csrf_token();
        $dataRetrun['acftkn']['acftknhs'] = csrf_hash();
        helper(['form', 'url']);
        $data = [];
        // is submited
        if ($this->request->getPost() != null && isset($_POST['vendor_name'])) {
            $id = $this->request->getVar('user_id');
            $inputData['inputSelect_productType'] = $this->request->getVar('inputSelect_productType');
            $inputData['update_id'] = $this->request->getVar('update_id');
            $inputData['colour_name'] = $this->request->getVar('colour_name');
            $inputData['quantity'] = $this->request->getVar('net_quantity');
            $inputData['hidden_u_code'] = $this->request->getVar('hidden_u_code'); 
            $inputData['inputSelect_vars'] = $this->request->getVar('inputSelect_vars');
            $inputData['gross_weight'] = $this->request->getVar('gross_weight');
            $inputData['net_weight'] = $this->request->getVar('net_weight');
            $inputData['cone_weight'] = $this->request->getVar('cone_weight');
            $inputData['inputSelect_bobbin_type'] = $this->request->getVar('inputSelect_bobbin_type');
            $inputData['inputSelect_reel_type'] = $this->request->getVar('inputSelect_reel_type');
            $inputData['master_brand_select_colorCone'] = $this->request->getVar('master_brand_select_colorCone');
            $inputData['master_brand_select_jariThreadCone'] = $this->request->getVar('master_brand_select_jariThreadCone');
            $inputData['master_brand_select_ZariZariColorCone'] = $this->request->getVar('master_brand_select_ZariZariColorCone');
            if ($inputData['inputSelect_productType'] != NULL) {
                $masterInventoryData['date'] =  $this->request->getVar('stock_date');
                $masterInventoryData['time'] = date('H:i:s');
                $masterInventoryData['vendor_name'] = $this->request->getVar('vendor_name');
                if ($this->request->getVar('user_id') != '' && $this->request->getVar('user_id') != 0) {
                    $this->MasterInventoryModel->updateData($masterInventoryData,$id);
                    $masterInventoryData_id =  $this->request->getVar('user_id');
                } else {
                    $this->MasterInventoryModel->set($masterInventoryData)->insert();
                    $masterInventoryData_id = $this->MasterInventoryModel->getInsertID();
                }
                foreach ($inputData['inputSelect_productType'] as $key => $value) {
                    // if ($this->request->getVar('user_id') != '' && $this->request->getVar('user_id') != 0 && isset($inputData['update_id']) && !empty($inputData['update_id'])) {
                    //     $updateData = array('status' => 2,'trash' => 1,);
                    //     $whereArray = array('trash' => 0,'id' => ($inputData['update_id'] != NULL && isset($inputData['update_id'][$key]) && $inputData['update_id'][$key] != '') ? $inputData['update_id'][$key] : 0,'parent_id' => $id);
                    //     $this->RelInventoryProducts->updateDataWhere($updateData, $whereArray);  
                    // } 
                    $insertData = array();
                    $insertData['parent_id'] = $masterInventoryData_id;
                    $insertData['date'] = date('Y-m-d');
                    $insertData['time'] = date('H:i:s');
                    $insertData['vendor_name'] = $this->request->getVar('vendor_name');
                    $insertData['product_type'] = $inputData['inputSelect_productType'][$key];
                    if ($insertData['product_type'] == 1) {
                        $insertData['product_id'] = $inputData['master_brand_select_colorCone'][$key];
                    } else if($insertData['product_type'] == 2) {
                        $insertData['product_id'] = $inputData['master_brand_select_jariThreadCone'][$key];
                    } else if($insertData['product_type'] == 3) {
                        $insertData['product_id'] = $inputData['master_brand_select_ZariZariColorCone'][$key];
                    } else if($insertData['product_type'] == 6) {
                        $insertData['product_id'] = $inputData['inputSelect_reel_type'][$key];
                    } else {
                        $insertData['product_id'] = $inputData['inputSelect_bobbin_type'][$key];
                    }
                    
                    // $insertData['shade_name'] = $inputData['shade_name'][$key];
                    // $insertData['denier_name'] = $inputData['denier_name'][$key];
                    $insertData['colour_id'] = $inputData['colour_name'][$key];
                    $insertData['quantity'] = $inputData['quantity'][$key];
                    $insertData['available_quantity'] = $inputData['quantity'][$key];
                    $insertData['var'] = $inputData['inputSelect_vars'][$key];
                    $insertData['net_weight'] = $inputData['gross_weight'][$key];
                    $insertData['gross_weight'] = $inputData['net_weight'][$key];
                    $insertData['cone_weight'] = $inputData['cone_weight'][$key];
                    

                    if(isset($inputData['update_id'][$key]) && !empty($inputData['update_id'][$key])){
                        $RelInventoryProductsID = ($inputData['update_id'] != NULL && isset($inputData['update_id'][$key]) && $inputData['update_id'][$key] != '') ? $inputData['update_id'][$key] : 0;
                        $whereArray = array('trash' => 0,'id' => ($inputData['update_id'] != NULL && isset($inputData['update_id'][$key]) && $inputData['update_id'][$key] != '') ? $inputData['update_id'][$key] : 0,'parent_id' => $id);
                        $this->RelInventoryProducts->updateDataWhere($insertData, $whereArray);
                    } else {
                        $this->RelInventoryProducts->set($insertData)->insert();
                        $RelInventoryProductsID = $this->RelInventoryProducts->getInsertId();
                    }

                    // if ($insertData['quantity'] != 0) {
                    //     for ($i = 1; $i <= $insertData['quantity']; $i++) {
                    //         // if ($this->request->getVar('user_id') != '' && $this->request->getVar('user_id') != 0 && isset($inputData['update_id']) && !empty($inputData['update_id'])) {
                    //         //     $updateData1 = array('status' => 2, 'trash' => 1);
                    //         //     $whereArray1 = array(
                    //         //         'trash' => 0,
                    //         //         'inventory_product_id' => ($inputData['update_id'] != NULL && isset($inputData['update_id']) && $inputData['update_id'] != '') ? $inputData['update_id'] : 0,
                    //         //         'batch_id' => $id
                    //         //     );
                    //         //     $this->RelInventoryProductsBatch->updateDataWhere($updateData1, $whereArray1);
                    //         // }

                    //         $insertBatchData = array();
                    //         $insertBatchData['batch_id'] = $masterInventoryData_id;
                    //         $insertBatchData['inventory_product_id'] = $RelInventoryProductsID;
                    //         $insertBatchData['u_code'] = date('ymd') . '-' . $masterInventoryData_id . '-' . $RelInventoryProductsID . '-' . $insertData['product_type'] . '-' . $i;
                    //         $insertBatchData['colour_id'] = $insertData['colour_id']; 
                    //         $insertBatchData['product_id'] = $insertData['product_id']; 

                    //         $getucode = $this->RelInventoryProductsBatch->where('status',1)->where('trash',0)->where('u_code',$inputData['hidden_u_code'][$i - 1]);
                    //         if (isset($inputData['hidden_u_code']) && !empty($inputData['hidden_u_code'])) {
                    //             $whereArray1 = array('trash' => 0,'inventory_product_id' => ($inputData['update_id'] != NULL && isset($inputData['update_id']) && $inputData['update_id'] != '') ? $inputData['update_id'] : 0,'batch_id' => $masterInventoryData_id);
                    //             $this->RelInventoryProductsBatch->updateDataWhere($insertBatchData, $whereArray1);
                    //         } else {
                    //             $this->RelInventoryProductsBatch->set($insertBatchData)->insert();
                    //         }
                    //     }
                    // }

                    if ($insertData['quantity'] != 0) {
                        if (isset($inputData['hidden_u_code']) && is_array($inputData['hidden_u_code'])) {
                            for ($i = 0; $i < $insertData['quantity']; $i++) {
                                $insertBatchData = array();
                                $insertBatchData['batch_id'] = $masterInventoryData_id;
                                $insertBatchData['inventory_product_id'] = $RelInventoryProductsID;
                                $insertBatchData['u_code'] = date('ymd') . '-' . $masterInventoryData_id . '-' . $RelInventoryProductsID . '-' . $insertData['product_type'] . '-' . ($i + 1);
                                $insertBatchData['colour_id'] = $insertData['colour_id']; 
                                $insertBatchData['product_id'] = $insertData['product_id']; 
                                
                                if (isset($inputData['hidden_u_code'][$i])) {
                                    $whereArray1 = array(
                                        'trash' => 0,
                                        'id' => $inputData['hidden_u_code'][$i],
                                        'batch_id' => $masterInventoryData_id
                                    );
                                    $this->RelInventoryProductsBatch->updateDataWhere($insertBatchData, $whereArray1);
                                } else {
                                    $this->RelInventoryProductsBatch->set($insertBatchData)->insert();
                                }
                            }
                        } else {
                            for ($i = 1; $i <= $insertData['quantity']; $i++) {
                                // if ($this->request->getVar('user_id') != '' && $this->request->getVar('user_id') != 0 && isset($inputData['update_id']) && !empty($inputData['update_id'])) {
                                //     $updateData1 = array('status' => 2, 'trash' => 1);
                                //     $whereArray1 = array(
                                //         'trash' => 0,
                                //         'inventory_product_id' => ($inputData['update_id'] != NULL && isset($inputData['update_id']) && $inputData['update_id'] != '') ? $inputData['update_id'] : 0,
                                //         'batch_id' => $id
                                //     );
                                //     $this->RelInventoryProductsBatch->updateDataWhere($updateData1, $whereArray1);
                                // }
    
                                $insertBatchData = array();
                                $insertBatchData['batch_id'] = $masterInventoryData_id;
                                $insertBatchData['inventory_product_id'] = $RelInventoryProductsID;
                                $insertBatchData['u_code'] = date('ymd') . '-' . $masterInventoryData_id . '-' . $RelInventoryProductsID . '-' . $insertData['product_type'] . '-' . $i;
                                $insertBatchData['colour_id'] = $insertData['colour_id']; 
                                $insertBatchData['product_id'] = $insertData['product_id']; 
                                $this->RelInventoryProductsBatch->set($insertBatchData)->insert();
                            }
                        }
                    }
                    
                }
                
                if ($masterInventoryData_id !== null) {
                    $session = \Config\Services::session();
                    $session->setFlashdata('msg', ' New Inventory Data Added Successfully');
                    $dataRetrun['status'] = 1;
                    $dataRetrun['message'] = 'Inventory Data Added Successfully';
                    echo json_encode($dataRetrun);
                } else {
                    $dataRetrun['status'] = 2;
                    $dataRetrun['message'] = 'Failed To Add Inventory Data Please Check All Fields Are Filled Properly ...!';
                    echo json_encode($dataRetrun);
                }
            }
        } else {
            $data['validation'] = $this->validator;

            $dataRetrun['status'] = 2;
            $dataRetrun['message'] = 'Failed To Add Inventory Data Please Check All Fields Are Filled Properly ...!';
            echo json_encode($dataRetrun);
        }
    }

    public function returnproductadd() {
        $dataRetrun['status'] = 0;
        $dataRetrun['message'] = "Failed";
        $dataRetrun['acftkn']['acftkname'] = csrf_token();
        $dataRetrun['acftkn']['acftknhs'] = csrf_hash();
        helper(['form', 'url']);
        $data = [];
        // is submited
        if ($this->request->getPost() != null && isset($_POST['vendor_name'])) {
            $id = $this->request->getVar('returnId');
            $inputData['hidden_inputSelect_productType'] = $this->request->getVar('hidden_inputSelect_productType');
            $inputData['hidden_unique_code'] = $this->request->getVar('hidden_unique_code');
            $inputData['hidden_inventory_batch_id'] = $this->request->getVar('hidden_inventory_batch_id');
            $inputData['checkbox_quantity'] = $this->request->getVar('checkbox_quantity');
            $inputData['return_reasons'] = $this->request->getVar('return_reasons');
            $inputData['update_id'] = $this->request->getVar('update_id');
            $inputData['return_update_id'] = $this->request->getVar('return_update_id');
            $inputData['hidden_colour_name'] = $this->request->getVar('hidden_colour_name');
            $inputData['totalquantity'] = $this->request->getVar('total_net_quantity');
            $inputData['returnquantity'] = $this->request->getVar('return_net_quantity');
            $inputData['hidden_already_return_quantity'] = $this->request->getVar('hidden_already_return_quantity');
            $inputData['hidden_inputSelect_bobbin_type'] = $this->request->getVar('hidden_inputSelect_bobbin_type');
            $inputData['hidden_inputSelect_reel_type'] = $this->request->getVar('hidden_inputSelect_reel_type');
            $inputData['hidden_master_brand_select_colorCone'] = $this->request->getVar('hidden_master_brand_select_colorCone');
            $inputData['hidden_master_brand_select_jariThreadCone'] = $this->request->getVar('hidden_master_brand_select_jariThreadCone');
            $inputData['hidden_master_brand_select_ZariZariColorCone'] = $this->request->getVar('hidden_master_brand_select_ZariZariColorCone');
            if ($inputData['hidden_inputSelect_productType'] != null) {
                $masterInventoryData['inventory_stock_date'] =  $this->request->getVar('stock_date');
                $masterInventoryData['inventory_vendor_id'] = $this->request->getVar('user_id');
                $masterInventoryData['inventory_vendor_name'] = $this->request->getVar('vendor_name');
                $masterInventoryData['return_reason'] = $this->request->getVar('reason_return');
                
                if ($this->request->getVar('returnId') != '' && $this->request->getVar('returnId') != 0) {
                    $this->MasterReturnProductModel->updateData($masterInventoryData,$id);
                    $masterInventoryData_id =  $this->request->getVar('returnId');
                } else {
                    $this->MasterReturnProductModel->set($masterInventoryData)->insert();
                    $masterInventoryData_id = $this->MasterReturnProductModel->getInsertID();
                }

                foreach ($inputData['hidden_inputSelect_productType'] as $key => $value) {
                    if($inputData['return_reasons'][$key] != "" && $inputData['checkbox_quantity'][$key] == 'on' && $inputData['checkbox_quantity'][$key] != 0){
                        $getRecordsById = $this->RelInventoryProducts->getAllAdminRecordsInventoryProductId($inputData['update_id'][$key]);  
                        if($getRecordsById != NULL && !empty($getRecordsById)){
                                if($getRecordsById['consumed_quantity'] == 0){
                                    $totalReturnQuantity =  $getRecordsById['available_quantity'] - 1;
                                    $plusConsumedQuantity = $getRecordsById['return_quantity'] + 1;
                                } else {
                                    $plusConsumedQuantity = $getRecordsById['return_quantity'] + 1;
                                    $totalReturnQuantity =  $getRecordsById['available_quantity'] - 1;
                                }
                                $updateRecordsById['master_return_id'] =  $masterInventoryData_id;
                                $updateRecordsById['return_quantity'] =   $plusConsumedQuantity;
                                $updateRecordsById['retutn_flag'] =  1;
                                $updateRecordsById['available_quantity'] = $totalReturnQuantity;

                                $whereArray = array('trash' => 0,'id' => ($inputData['update_id'] != NULL && isset($inputData['update_id'][$key]) && $inputData['update_id'][$key] != '') ? $inputData['update_id'][$key] : 0,'parent_id' => $this->request->getVar('user_id'));
                                $this->RelInventoryProducts->updateDataWhere($updateRecordsById, $whereArray);
                        }

                        $getbatchdetails = $this->RelInventoryProductsBatch->getAllAdminRecordsInventoryBatchProductIdAndBacthID($inputData['update_id'][$key],$inputData['hidden_inventory_batch_id'][$key]);
                        if ($getbatchdetails != null && !empty($getbatchdetails)) {
                            // Set the ID for the next batch update
                            // Update batch details with additional information
                            $updatebatchdetails['master_return_id'] =  $masterInventoryData_id;
                            $updatebatchdetails['return_quantity'] =  1; // Assuming return_quantity is always 1 for each iteration
                            $updatebatchdetails['return_flag'] =  1; // Corrected 'retutn_flag' to 'return_flag'
                            $updatebatchdetails['total_return_quantity'] = $getRecordsById['return_quantity'] + 1;
                            $updatebatchdetails['return_reason'] = $inputData['return_reasons'][$key];
                            // Update data in RelInventoryProductsBatch
                            $this->RelInventoryProductsBatch->updateData($updatebatchdetails, $inputData['hidden_inventory_batch_id'][$key]);
                        }

                        $insertData = array();
                        $insertData['master_returnid'] = $masterInventoryData_id;
                        $insertData['date'] = date('Y-m-d');
                        $insertData['time'] = date('H:i:s');
                        $insertData['vendor_id'] =  $this->request->getVar('user_id');
                        $insertData['vendor_name'] = $this->request->getVar('vendor_name');
                        $insertData['product_type'] = $inputData['hidden_inputSelect_productType'][$key];
                        if ($insertData['product_type'] == 1) {
                            $insertData['product_id'] = $inputData['hidden_master_brand_select_colorCone'][$key];
                        } else if($insertData['product_type'] == 2) {
                            $insertData['product_id'] = $inputData['hidden_master_brand_select_jariThreadCone'][$key];
                        } else if($insertData['product_type'] == 3) {
                            $insertData['product_id'] = $inputData['hidden_master_brand_select_ZariZariColorCone'][$key];
                        } else if($insertData['product_type'] == 6) {
                            $insertData['product_id'] = $inputData['hidden_inputSelect_reel_type'][$key];
                        } else {
                            $insertData['product_id'] = $inputData['hidden_inputSelect_bobbin_type'][$key];
                        }
    
                        $insertData['colour_id'] = $inputData['hidden_colour_name'][$key];
                        $insertData['master_rel_product_id'] = $inputData['update_id'][$key];
                        $insertData['product_quantity'] = $inputData['totalquantity'][$key];
                        $insertData['return_product_quantity'] = $getRecordsById['return_quantity'] + 1;

                        if(isset($inputData['return_update_id'][$key]) && !empty($inputData['return_update_id'][$key])){
                            $whereArray = array('trash' => 0,'id' => ($inputData['return_update_id'] != NULL && isset($inputData['return_update_id'][$key]) && $inputData['return_update_id'][$key] != '') ? $inputData['return_update_id'][$key] : 0,'master_returnid' => $id);
                            $this->RelReturnProductQuantityModel->updateDataWhere($insertData, $whereArray);
                        } else {
                            $this->RelReturnProductQuantityModel->set($insertData)->insert();
                        }
                    }  
                }
                
                if ($masterInventoryData_id !== null) {
                    $session = \Config\Services::session();
                    $session->setFlashdata('msg', ' New Inventory Data Added Successfully');
                    $dataRetrun['status'] = 1;
                    $dataRetrun['message'] = 'Inventory Data Added Successfully';
                    echo json_encode($dataRetrun);
                } else {
                    $dataRetrun['status'] = 2;
                    $dataRetrun['message'] = 'Failed To Add Inventory Data Please Check All Fields Are Filled Properly ...!';
                    echo json_encode($dataRetrun);
                }
            }
        } else {
            $data['validation'] = $this->validator;
            $dataRetrun['status'] = 2;
            $dataRetrun['message'] = 'Failed To Add Inventory Data Please Check All Fields Are Filled Properly ...!';
            echo json_encode($dataRetrun);
        }
    }

    // public function getinventoryList() {
    //     helper(['form', 'url']);
    //     $data = [];
    //     $returndata['acftkn']['acftkname'] = csrf_token();
    //     $returndata['acftkn']['acftknhs'] = csrf_hash();

    //     $datatable_where['rvp.status !='] = 2;
    //     $list = $this->MasterInventoryDataTblModel->get_datatables($datatable_where);
    //     $no = $_POST['start']; 
    //     if ($list != NULL) {
    //         foreach ($list as $dept) {
    //             $avtarData['inventoryDataDetails'] = $dept;
    //             $statusDiv = view('admin/inventory/statusDiv', $avtarData);
    //             $actionDiv = view('admin/inventory/actionDiv', $avtarData);
    //             $no++;
    //             $row = array();
    //             $row[] = "<td>" . $no . "</td>";
    //             $row[] = "<td>" .  date('d F Y', strtotime($dept['date'])) . "</td>";
    //             $row[] = "<td>" .  date('h:i:A',strtotime($dept['time'])) . "</td>";
    //             $row[] = "<td>" . $dept['vendor_name'] . "</td>";
    //             $row[] = "<td>" . $statusDiv . "</td>";
    //             $row[] = "<td>" . $actionDiv . "</td>";
    //             $data[] = $row;
    //         }
    //     }

    //     $output = array(
    //         "draw" => $_POST['draw'],
    //         "recordsTotal" => $this->MasterInventoryDataTblModel->count_all($datatable_where),
    //         "recordsFiltered" => $this->MasterInventoryDataTblModel->count_filtered($datatable_where),
    //         "totalActiveRecods" => $this->MasterInventoryDataTblModel->count_ActiveRecordsfiltered($datatable_where),
    //         "totalInActiveRecods" => $this->MasterInventoryDataTblModel->count_InActiveRecordsfiltered($datatable_where),
    //         "data" => $data,
    //     );

    //     echo json_encode($output);
    // }

    public function getinventoryList() {
        helper(['form', 'url']);
        $data = [];
        $returndata['acftkn']['acftkname'] = csrf_token();
        $returndata['acftkn']['acftknhs'] = csrf_hash();
    
        $datatable_where['rvp.status !='] = 2;
    
        // Check if 'start' is set in $_POST
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
    
        $list = $this->MasterInventoryDataTblModel->get_datatables($datatable_where);
        $no = $start;
    
        if ($list != NULL) {
            foreach ($list as $dept) {
                $avtarData['inventoryDataDetails'] = $dept;
                $statusDiv = view('admin/inventory/statusDiv', $avtarData);
                $actionDiv = view('admin/inventory/actionDiv', $avtarData);
                $no++;
                $row = array();
                $row[] = "<td>" . $no . "</td>";
                $row[] = "<td>" .  date('d F Y', strtotime($dept['date'])) . "</td>";
                $row[] = "<td>" .  date('h:i:A',strtotime($dept['time'])) . "</td>";
                $row[] = "<td>" . $dept['vendor_name'] . "</td>";
                $row[] = "<td>" . $statusDiv . "</td>";
                $row[] = "<td>" . $actionDiv . "</td>";
                $data[] = $row;
            }
        }
    
        $output = array(
            "draw" => isset($_POST['draw']) ? $_POST['draw'] : 1, // Set a default value if 'draw' is not set
            "recordsTotal" => $this->MasterInventoryDataTblModel->count_all($datatable_where),
            "recordsFiltered" => $this->MasterInventoryDataTblModel->count_filtered($datatable_where),
            "totalActiveRecods" => $this->MasterInventoryDataTblModel->count_ActiveRecordsfiltered($datatable_where),
            "totalInActiveRecods" => $this->MasterInventoryDataTblModel->count_InActiveRecordsfiltered($datatable_where),
            "data" => $data,
        );
    
        echo json_encode($output);
    }

    public function getinventoryReturnProduct() {
        helper(['form', 'url']);
        $data = [];
        $returndata['acftkn']['acftkname'] = csrf_token();
        $returndata['acftkn']['acftknhs'] = csrf_hash();
    
        $datatable_where['rvp.status !='] = 2;
        $list = $this->MasterInventoryDataTblModel->get_datatables($datatable_where);
        $no = $_POST['start'];
    
        if ($list != NULL) {
            foreach ($list as $dept) {
                $avtarData['inventoryDataDetails'] = $dept;
                $actionDiv = view('admin/inventory/returproduct', $avtarData);
                $no++;
                $row = array();
                $row[] = "<td>" . $no . "</td>";
                $row[] = "<td>" .  date('d F Y', strtotime($dept['date'])) . "</td>";
                $row[] = "<td>" .  date('h:i:A',strtotime($dept['time'])) . "</td>";
                $row[] = "<td>" . $dept['vendor_name'] . "</td>";
                $row[] = "<td>" . $actionDiv . "</td>";
                $data[] = $row;
            }
        }
    
        $output = array(
            "draw" => $_POST['draw'], // Set a default value if 'draw' is not set
            "recordsTotal" => $this->MasterInventoryDataTblModel->count_all($datatable_where),
            "recordsFiltered" => $this->MasterInventoryDataTblModel->count_filtered($datatable_where),
            "totalActiveRecods" => $this->MasterInventoryDataTblModel->count_ActiveRecordsfiltered($datatable_where),
            "totalInActiveRecods" => $this->MasterInventoryDataTblModel->count_InActiveRecordsfiltered($datatable_where),
            "data" => $data,
        );
    
        echo json_encode($output);
    }

    public function checkConnectionWithMachine() {
        extract($_POST);
        $dataRetrun['acftkn']['acftkname'] = csrf_token();
        $dataRetrun['acftkn']['acftknhs'] = csrf_hash();
        $inventoryId = $this->request->getPost('ID');
        $inventoryBatchId = $this->request->getPost('IID');
    
        $status = $this->RelBeamBatchReelMachine->checkConnectionWithMachine($inventoryId, $inventoryBatchId);
        $status1 = $this->RelSareeProductionModel->checkConnectionWithSareeProduction($inventoryId, $inventoryBatchId);
    
        if (!empty($status)) {
            $dataRetrun['message'] = 'This product is currently connected to a machine. Please disconnect it first.';
            $dataRetrun['status'] = 2;
        } else if(!empty($status1)) {
            $dataRetrun['message1'] = 'This product is currently connected to a saree production. Please disconnect it first.';
            $dataRetrun['status1'] = 2;
        } else {
            $dataRetrun['status1'] = 1;
            $dataRetrun['checkRecords'] = $status1."-".$status1;
        }
        echo json_encode($dataRetrun);
    }

    // public function checkConnectionWithSareeProduction(){
    //     extract($_POST);
    //     $dataRetrun['acftkn']['acftkname'] = csrf_token();
    //     $dataRetrun['acftkn']['acftknhs'] = csrf_hash();
    //     $inventoryId = $this->request->getPost('ID');
    //     $inventoryBatchId = $this->request->getPost('IID');

    //     $status = $this->RelSareeProductionModel->checkConnectionWithSareeProduction($inventoryId,$inventoryBatchId);
    //     if (!empty($status)) {
    //         $dataRetrun['message'] = 'Duplicate Colour entry';
    //         $dataRetrun['status'] = 2;
    //     } else {
    //         $dataRetrun['status'] = 1;
    //         $dataRetrun['checkRecords'] = $status;
    //     }
    //     echo json_encode($dataRetrun);
    // }
}
