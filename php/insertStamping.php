<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['type'], $_POST['customerType'], $_POST['newRenew'], $_POST['brand'], $_POST['validator'], $_POST['machineType'], $_POST['jenisAlat'], $_POST['address1']
, $_POST['model'], $_POST['capacity'], $_POST['serial'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$newRenew = filter_input(INPUT_POST, 'newRenew', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
	$address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);

	$product = null;
	$dealer = null;
	$branch = null;
	$address2 = null;
	$address3 = null;
	$address4 = null;
	$dueDate = null;
	$stamping = null;
	$stampDate = null;
	$noDaftar = null;
	$pinKeselamatan = null;
	$attnTo = null;
	$siriKeselamatan = null;
	$borangD = null;
	$cashBill = null;
	$invoice = null;
	$pic = null;
	$followUpDate = null;
	$quotation = null;
	$quotationDate = null;
	$remark = null;
	$customer = "";
	$includeCert = "NO";
	$poNo = null;
	$poDate = null;
	$unitPrice = '0.00';
	$certPrice = '0.00';
	$totalPrice = '0.00';
	$sst = '0.00';
	$subtoalPrice = '0.00';

	$logs = array();

	if(isset($_POST['product']) && $_POST['product']!=null && $_POST['product']!=""){
		$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
	}
	else{
		if ($select_stmtP = $db->prepare("SELECT id FROM products WHERE machine_type=? AND jenis_alat=? AND capacity=? AND validator=?")) {
			$select_stmtP->bind_param('ssss', $machineType, $jenisAlat, $capacity, $validator);
			$select_stmtP->execute();
			$resultP = $select_stmtP->get_result();
        
			if ($rowP = $resultP->fetch_assoc()) {
				$product = $rowP['id'];
			} 
			else {
				if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!="" && $_POST['unitPrice']!="0.00"){
					// Customer does not exist, create a new customer
					if ($insert_stmtP = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, price) VALUES (?, ?, ?, ?, ?, ?)")) {
						$pname = 'product'.$machineType.$jenisAlat.$capacity.$validator;
						$insert_stmtP->bind_param('ssssss', $pname , $machineType, $jenisAlat, $capacity, $validator, $_POST['unitPrice']);
						
						if ($insert_stmtP->execute()) {
							$product = $insert_stmtP->insert_id;
						} 
					}
				}
			}
		}
	}

	if(isset($_POST['dealer']) && $_POST['dealer']!=null && $_POST['dealer']!=""){
		$dealer = $_POST['dealer'];
	}

	if(isset($_POST['address2']) && $_POST['address2']!=null && $_POST['address2']!=""){
		$address2 = $_POST['address2'];
	}

	if(isset($_POST['address3']) && $_POST['address3']!=null && $_POST['address3']!=""){
		$address3 = $_POST['address3'];
	}

	if(isset($_POST['address4']) && $_POST['address4']!=null && $_POST['address4']!=""){
		$address4 = $_POST['address4'];
	}

	if(isset($_POST['branch']) && $_POST['branch']!=null && $_POST['branch']!=""){
		$branch = $_POST['branch'];
	}

	if($customerType == "NEW"){
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=?")) {
			$select_stmt->bind_param('s', $_POST['companyText']);
			$select_stmt->execute();
			$result = $select_stmt->get_result();
        
			if ($row = $result->fetch_assoc()) {
				$customer = $row['id'];
				$customerType = 'EXISTING';
			} 
			else {
				$email = null;
				$phone = null;
				$dealer = null;
				$branchName = '';
				$mapUrl = '';

				if(isset($_POST['dealer'] ) && $_POST['dealer'] != null && $_POST['dealer'] != "" && $type == 'DEALER'){
					$dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
				}

				// Customer does not exist, create a new customer
				if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, dealer) VALUES (?, ?)")) {
					$insert_stmt->bind_param('ss', $_POST['companyText'], $dealer);
					
					if ($insert_stmt->execute()) {
						$customer = $insert_stmt->insert_id;
						$customerType = 'EXISTING';

						if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_name, map_url) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                            $insert_stmt2->bind_param('sssssss', $customer, $address1, $address2, $address3, $address4, $branchName, $mapUrl);
                            $insert_stmt2->execute();
							$branch = $insert_stmt2->insert_id;
                            $insert_stmt2->close();
                        } 
					} 
				}
			}
		}
	}
	else{
		$customer = $_POST['company'];
		$customerType = 'EXISTING';
	}

	if(isset($_POST['stamping']) && $_POST['stamping']!=null && $_POST['stamping']!=""){
		$stamping = $_POST['stamping'];
	}

	if(isset($_POST['stampDate']) && $_POST['stampDate']!=null && $_POST['stampDate']!=""){
		$stampDate = $_POST['stampDate'];
		$stampDate = DateTime::createFromFormat('d/m/Y', $stampDate)->format('Y-m-d H:i:s');
	}
	
	if(isset($_POST['noDaftar']) && $_POST['noDaftar']!=null && $_POST['noDaftar']!=""){
		$noDaftar = $_POST['noDaftar'];
	}

	if(isset($_POST['pinKeselamatan']) && $_POST['pinKeselamatan']!=null && $_POST['pinKeselamatan']!=""){
		$pinKeselamatan = $_POST['pinKeselamatan'];
	}

	if(isset($_POST['attnTo']) && $_POST['attnTo']!=null && $_POST['attnTo']!=""){
		$attnTo = $_POST['attnTo'];
	}

	if(isset($_POST['siriKeselamatan']) && $_POST['siriKeselamatan']!=null && $_POST['siriKeselamatan']!=""){
		$siriKeselamatan = $_POST['siriKeselamatan'];
	}
	
	if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
		$pic = $_POST['pic'];
	}

	if(isset($_POST['borangD']) && $_POST['borangD']!=null && $_POST['borangD']!=""){
		$borangD = $_POST['borangD'];
	}

	if(isset($_POST['remark']) && $_POST['remark']!=null && $_POST['remark']!=""){
		$remark = $_POST['remark'];
	}

	if(isset($_POST['dueDate']) && $_POST['dueDate']!=null && $_POST['dueDate']!=""){
		$dueDate = $_POST['dueDate'];
		$dueDate = DateTime::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['quotation']) && $_POST['quotation']!=null && $_POST['quotation']!=""){
		$quotation = $_POST['quotation'];
	}

	if(isset($_POST['quotationDate']) && $_POST['quotationDate']!=null && $_POST['quotationDate']!=""){
		$quotationDate = $_POST['quotationDate'];
		$quotationDate = DateTime::createFromFormat('d/m/Y', $quotationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['includeCert']) && $_POST['includeCert']!=null && $_POST['includeCert']!=""){
		$includeCert = $_POST['includeCert'];
	}

	if(isset($_POST['poNo']) && $_POST['poNo']!=null && $_POST['poNo']!=""){
		$poNo = $_POST['poNo'];
	}

	if(isset($_POST['poDate']) && $_POST['poDate']!=null && $_POST['poDate']!=""){
		$poDate = $_POST['poDate'];
		$poDate = DateTime::createFromFormat('d/m/Y', $poDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['cashBill']) && $_POST['cashBill']!=null && $_POST['cashBill']!=""){
		$cashBill = $_POST['cashBill'];
	}

	if(isset($_POST['invoice']) && $_POST['invoice']!=null && $_POST['invoice']!=""){
		$invoice = $_POST['invoice'];
	}

	if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!=""){
		$unitPrice = $_POST['unitPrice'];
	}

	if(isset($_POST['certPrice']) && $_POST['certPrice']!=null && $_POST['certPrice']!=""){
		$certPrice = $_POST['certPrice'];
	}

	if(isset($_POST['totalAmount']) && $_POST['totalAmount']!=null && $_POST['totalAmount']!=""){
		$totalPrice = $_POST['totalAmount'];
	}

	if(isset($_POST['sst']) && $_POST['sst']!=null && $_POST['sst']!=""){
		$sst = $_POST['sst'];
	}

	if(isset($_POST['subAmount']) && $_POST['subAmount']!=null && $_POST['subAmount']!=""){
		$subtotalPrice = $_POST['subAmount'];
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if ($update_stmt = $db->prepare("UPDATE stamping SET type=?, dealer=?, customers=?, address1=?, address2=?, address3=?, brand=?, machine_type=?, model=?
		, capacity=?, serial_no=?, validate_by=?, jenis_alat=?, no_daftar=?, pin_keselamatan=?, siri_keselamatan=?, include_cert=?, borang_d=?
		, invoice_no=?, cash_bill=?, stamping_date=?, due_date=?, pic=?, customer_pic=?, quotation_no=?, quotation_date=?, purchase_no=?, purchase_date=?
		, remarks=?, unit_price=?, cert_price=?, total_amount=?, sst=?, subtotal_amount=?, log=?, products=?, stamping_type=?, updated_datetime=?, branch=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssss', $type, $dealer, $customer, $address1, $address2, $address3, $brand, $machineType, $model, $capacity, $serial, 
			$validator, $jenisAlat, $noDaftar, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD, $invoice, $cashBill, $stampDate, $dueDate, $uid, $pic, 
			$quotation, $quotationDate, $poNo, $poDate, $remark, $unitPrice, $certPrice, $totalPrice, $sst, $subtotalPrice, $data, $product, $newRenew, $currentDateTime, $branch, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 
			else{
				$update_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO stamping (type, dealer, customer_type, customers, address1, address2, address3, brand, machine_type, model, capacity, serial_no, 
		validate_by, jenis_alat, no_daftar, pin_keselamatan, siri_keselamatan, include_cert, borang_d, invoice_no, cash_bill, stamping_date, due_date, pic, customer_pic, 
		quotation_no, quotation_date, purchase_no, purchase_date, remarks, unit_price, cert_price, total_amount, sst, subtotal_amount, log, products, stamping_type, branch) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$data = json_encode($logs);
			$insert_stmt->bind_param('sssssssssssssssssssssssssssssssssssssss', $type, $dealer, $customerType, $customer, $address1, $address2, $address3, $brand, $machineType, $model, $capacity, $serial, 
			$validator, $jenisAlat, $noDaftar, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD, $invoice, $cashBill, $stampDate, $dueDate, $uid, $pic, 
			$quotation, $quotationDate, $poNo, $poDate, $remark, $unitPrice, $certPrice, $totalPrice, $sst, $subtotalPrice, $data, $product, $newRenew, $branch);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $insert_stmt->error
					)
				);
			} 
			else{
				$insert_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			); 
		}
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

?>