<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and stamping_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and stamping_date <= '".$toDateTime."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customers = '".$_POST['customer']."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and status = '".$_POST['status']."'";
}

if($searchValue != ''){
  $searchQuery = " and (purchase_no like '%".$searchValue."%' OR
  quotation_no like '%".$searchValue."%' OR
  invoice_no like '%".$searchValue."%' OR
  cash_bill like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping WHERE status = 'Complete'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping WHERE status = 'Complete'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM stamping WHERE status = 'Complete'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no" => $counter,
    "id" => $row['id'],
    "customers" => $row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
    "address1" => $row['address1'] ?? '',
    "address2" => $row['address2'] ?? '',
    "address3" => $row['address3'] ?? '',
    "brand" => $row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machine_type" => $row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
    "model" => $row['model'] != null ? searchModelNameById($row['model'], $db) : '',
    "capacity" => $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "serial_no" => $row['serial_no'] ?? '',
    "validator" => $row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "jenis_alat" => $row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '',
    "cash_bill" => $row['cash_bill'] ?? '',
    "invoice_no" => $row['invoice_no'] ?? '',
    "stamping_date" => $row['stamping_date'] ?? '',
    "due_date" => $row['due_date'] ?? '',
    "pic" => $row['pic'] != null ? searchStaffNameById($row['pic'], $db) : '',
    "customer_pic" => $row['customer_pic'] ?? '',
    "quotation_date" => $row['quotation_date'] ?? '',
    "quotation_no" => $row['quotation_no'] ?? '',
    "purchase_no" => $row['purchase_no'] ?? '',
    "purchase_date" => $row['purchase_date'] ?? '',
    "unit_price" => $row['unit_price'] ?? '',
    "cert_price" => $row['cert_price'] ?? '',
    "total_amount" => $row['total_amount'] ?? '',
    "sst" => $row['sst'] ?? '',
    "subtotal_amount" => $row['subtotal_amount'] ?? '',
    "log" => json_decode($row['log'], true),
    "status" => $row['status'],
    "remarks" => $row['remarks'] ?? '',
    "no_daftar" => $row['no_daftar'] ?? '',
    "pin_keselamatan" => $row['pin_keselamatan'] ?? '',
    "siri_keselamatan" => $row['siri_keselamatan'] ?? '',
    "borang_d" => $row['borang_d'] ?? '',
    "updated_datetime" => $row['updated_datetime'] ?? ''
  );

  // Check if additional data from stamping_ext is needed
  if(($row['validate_by'] == '10' || $row['validate_by'] == '9') && $row['jenis_alat'] == '1') {
    if ($update_stmt2 = $db->prepare("SELECT * FROM stamping_ext WHERE stamp_id = ?")) {
      $update_stmt2->bind_param('s', $row['id']);
  
      if($update_stmt2->execute()) {
        $result2 = $update_stmt2->get_result();
  
        if($row2 = $result2->fetch_assoc()) {
          // Update the last item in the $data array with additional fields from stamping_ext
          $data[$counter - 1] = array_merge($data[$counter - 1], [
            'penentusan_baru' => $row2['penentusan_baru'] ?? '',
            'penentusan_semula' => $row2['penentusan_semula'] ?? '',
            'kelulusan_mspk' => $row2['kelulusan_mspk'] ?? '',
            'no_kelulusan' => $row2['no_kelulusan'] ?? '',
            'indicator_serial' => $row2['indicator_serial'] ?? '',
            'platform_country' => $row2['platform_country'] ?? '',
            'platform_type' => $row2['platform_type'] ?? '',
            'size' => $row2['size'] ?? '',
            'jenis_pelantar' => $row2['jenis_pelantar'] ?? '',
            'other_info' => $row2['other_info'] ?? '',
            'load_cell_country' => $row2['load_cell_country'] ?? '',
            'load_cell_no' => $row2['load_cell_no'] ?? '',
            'load_cells_info' => json_decode($row2['load_cells_info'], true)
          ]);
        }
      }
    }
  }

  $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>