<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['name'] = $row['name'];
                $message['range_type'] = $row['range_type'];
                $message['capacity'] = $row['capacity'];
                $message['units'] = $row['units'];
                $message['division'] = $row['division'];
                $message['division_unit'] = $row['division_unit'];
                $message['capacity2'] = $row['capacity2'];
                $message['units2'] = $row['units2'];
                $message['division2'] = $row['division2'];
                $message['division_unit2'] = $row['division_unit2'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>