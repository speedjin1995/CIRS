<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.html";</script>';
}
else{
    $id = '1';
    $_SESSION['page']='company';
    $stmt = $db->prepare("SELECT * from companies where id = ?");
	$stmt->bind_param('s', $id);
	$stmt->execute();
	$result = $stmt->get_result();
    $name = '';
	$address = '';
	$phone = '';
	$email = '';
	
	if(($row = $result->fetch_assoc()) !== null){
        $data = $row;
        // $name = $row['name'];
        // $address = $row['address'];
        // $phone = $row['phone'];
        // $email = $row['email'];
    }
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark"><b>Company Profile</b></h1>
			</div>
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
	<div class="card">
		<form role="form" id="profileForm" novalidate="novalidate">
			<div class="card-body">
				<div class="form-group">
					<label for="new_roc">Company NEW. ROC. *</label>
					<input type="text" class="form-control" id="new_roc" name="new_roc" value="<?=$data['new_roc'] ?>" placeholder="Enter Company NEW. ROC." required="">
				</div>
                
				<div class="form-group">
					<label for="old_roc">Company OLD. ROC.</label>
					<input type="text" class="form-control" id="old_roc" name="old_roc" value="<?=$data['old_roc'] ?>" placeholder="Enter Company OLD. ROC.">
				</div>

				<div class="form-group">
					<label for="name">Company Name *</label>
					<input type="text" class="form-control" id="name" name="name" value="<?=$data['name'] ?>" placeholder="Enter Company Name" required="">
				</div>
				
				<div class="form-group">
					<label for="address">Company Address *</label>
                    <textarea class="form-control" name="address" id="address" rows="3" placeholder="Enter Address" required=""><?=$data['address'] ?></textarea>
				</div>

                <div class="form-group">
					<label for="phone">Company Phone </label>
					<input type="text" class="form-control" id="phone" name="phone" value="<?=$data['phone'] ?>" placeholder="Enter Phone">
				</div>

                <div class="form-group">
					<label for="fax">Company Fax </label>
					<input type="text" class="form-control" id="fax" name="fax" value="<?=$data['fax'] ?>" placeholder="Enter Fax">
				</div>

                <div class="form-group">
					<label for="person_incharge">Person Incharge </label>
					<input type="text" class="form-control" id="person_incharge" name="person_incharge" value="<?=$data['person_incharge'] ?>" placeholder="Enter Person Incharge">
				</div>

                <div class="form-group">
					<label for="contact_no">Contact No </label>
					<input type="text" class="form-control" id="contact_no" name="contact_no" value="<?=$data['contact_no'] ?>" placeholder="Enter Contact No">
				</div>

                <div class="form-group">
					<label for="email">Email address</label>
					<input type="email" class="form-control" id="email" name="email" value="<?=$data['email'] ?>" placeholder="Enter Email">
				</div>
                
                <br>

                <div class="form-group">
					<label for="lesen_type">Lesen Type </label>
                    <select class="form-control select2" name="lesen_type" id="lesen_type">
                        <option value="" selected disabled hidden>Please Select</option>
                        <option value="membuat/membaiki/menjual" <?php if ($data['lesen_type'] == 'membuat/membaiki/menjual') echo 'selected'; ?>>Membuat/Membaiki/Menjual</option>
                        <option value="membaiki/menjual" <?php if ($data['lesen_type'] == 'membaiki/menjual') echo 'selected'; ?>>Membaiki/Menjual</option>
                        <option value="membuat" <?php if ($data['lesen_type'] == 'membuat') echo 'selected'; ?>>Membuat</option>
                        <option value="membaiki" <?php if ($data['lesen_type'] == 'membaiki') echo 'selected'; ?>>Membaiki</option>
                    </select>
				</div>

                <div class="form-group">
					<label for="certno_lesen">Certificate No.Lesen </label>
					<input type="text" class="form-control" id="certno_lesen" name="certno_lesen" value="<?=$data['certno_lesen'] ?>" placeholder="Enter Certificate No.Lesen">
				</div>

                <div class="form-group">
					<label for="certats_serialno">Certificate ATS Serial No. </label>
					<input type="text" class="form-control" id="certats_serialno" name="certats_serialno" value="<?=$data['certats_serialno'] ?>" placeholder="Enter Certificate ATS Serial No.">
				</div>

                <div class="form-group">
					<label for="failno">No.Fail </label>
					<input type="text" class="form-control" id="failno" name="failno" value="<?=$data['failno'] ?>" placeholder="Enter No.Fail">
				</div>

                <div class="form-group">
					<label for="bless_serahanno">No. Serahan BLESS </label>
					<input type="text" class="form-control" id="bless_serahanno" name="bless_serahanno" value="<?=$data['bless_serahanno'] ?>" placeholder="Enter No. Serahan BLESS">
				</div>

                <div class="form-group">
					<label for="resitno">No. Resit </label>
					<input type="text" class="form-control" id="resitno" name="resitno" value="<?=$data['resitno'] ?>" placeholder="Enter No. Resit">
				</div>

                <div class="form-group">
					<label for="tarikh_kuatkuasa">Tarikh Kuatkuasa </label>
					<input type="date" class="form-control" id="tarikh_kuatkuasa" name="tarikh_kuatkuasa" value="<?=$data['tarikh_kuatkuasa'] ?>">
				</div>

                <div class="form-group">
					<label for="tarikh_luput">Tarikh Luput </label>
					<input type="date" class="form-control" id="tarikh_luput" name="tarikh_luput" value="<?=$data['tarikh_luput'] ?>">
				</div>

                <div class="form-group">
					<label for="tarikh_dikeluarkan">Tarikh Dikeluarkan </label>
					<input type="date" class="form-control" id="tarikh_dikeluarkan" name="tarikh_dikeluarkan" value="<?=$data['tarikh_dikeluarkan'] ?>">
				</div>
                
			</div>
			
			<div class="card-footer">
				<button class="btn btn-success" id="saveProfile"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>

	<div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-10"><p><b>Certificate No.Lesen</b></p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="addLesenCert" onclick="newLesenCert('<?php echo $id; ?>');"><b>ADD Details</b></button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="lesenCertTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
				<th>No</th>
				<th>Details</th>
				<th>Serial No</th>
				<th>Approval Date</th>
				<th>Expire Date</th>
				<th>Attach PDF</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

	<div class="modal fade" id="addLesenCertModal">
		<div class="modal-dialog modal-xl" style="max-width: 40%;">
			<div class="modal-content">
				<form role="form" id="addLesenCertForm" enctype="multipart/form-data">
					<div class="modal-header bg-gray-dark color-palette">
						<h4 class="modal-title"><b>Add Certificate No.Lesen</b></h4>
						<button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<input type="hidden" class="form-control" id="id" name="id">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Details *</label>
									<input type="text" class="form-control" id="lesenCertDetail" name="lesenCertDetail" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Serial No *</label>
									<input type="text" class="form-control" id="lesenCertSerialNo" name="lesenCertSerialNo" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Approval Date *</label>
									<input type="date" class="form-control" id="lesenCertApprDt" name="lesenCertApprDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Expire Date *</label>
									<input type="date" class="form-control" id="lesenCertExpDt" name="lesenCertExpDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Upload PDF *</label>
									<input type="file" class="form-control" id="lesenCertPdf" name="lesenCertPdf" required>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between bg-gray-dark color-palette">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</section>

<script>
$(function () {
	var isModalOpen = false; // Flag to track modal visibility
    var id = '<?php echo $id; ?>';

	var table = $("#lesenCertTable").DataTable({
		"responsive": true,
		"autoWidth": false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'searching': false,
		'order': [[ 1, 'asc' ]],
		'columnDefs': [ { orderable: false, targets: [0] }],
		'ajax': {
			'type': 'POST',
			'url':'php/getLesenCert.php',
			'data': {
				companyId: id,
			}, 
			'dataSrc': function (json) {
				// console.log(json); // Debugging: Check JSON response
				if (json.status === "success") {
					return json.message;
				} else {
					return [];
				}
			}
		},
		'columns': [
			{
				data: null, // No data for this column, Auto increment Column
				className: 'dt-center',
				render: function (data, type, row, meta) {
					// meta.row gives you the index of the row (0-based)
					return meta.row + 1; // Auto-incrementing value starts from 1
				},
			},
			{ data: 0 },  
            { data: 1 }, 
            { data: 2 }, 
            { data: 3 },
            {
				data: 4,    // Attach PDF
				render: function (data, type, row) {
					return data; // Render the HTML as is
				}
			}

		],
	});



	// Bind form submission handler once
	$('#addLesenCertForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/addLesenCert.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#addLesenCertModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					$('#lesenCertTable').DataTable().ajax.reload();
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});


    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
			if (!isModalOpen) {
				$.post('php/updateCompany.php', $('#profileForm').serialize(), function(data){
					var obj = JSON.parse(data); 
					
					if(obj.status === 'success'){
						toastr["success"](obj.message, "Success:");
						
						$.get('company.php', function(data) {
							$('#mainContents').html(data);
							$('#spinnerLoading').hide();
						});
					}
					else if(obj.status === 'failed'){
						toastr["error"](obj.message, "Failed:");
						$('#spinnerLoading').hide();
					}
					else{
						toastr["error"]("Failed to update profile", "Failed:");
						$('#spinnerLoading').hide();
					}
				});
			}
        }
    });
    
    $('#profileForm').validate({
        rules: {
            text: {
                required: true
            }
        },
        messages: {
            text: {
                required: "Please fill in this field"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});

function newLesenCert(id){
	$('#spinnerLoading').show();
	$('#addLesenCertModal').find('#id').val(id);
	$('#addLesenCertModal').modal('show');
	isModalOpen = true; // Set flag to true when modal is shown
	// $('#addLesenCertForm').validate({
	// 	errorElement: 'span',
	// 	errorPlacement: function (error, element) {
	// 	error.addClass('invalid-feedback');
	// 	element.closest('.form-group').append(error);
	// 	},
	// 	highlight: function (element, errorClass, validClass) {
	// 	$(element).addClass('is-invalid');
	// 	},
	// 	unhighlight: function (element, errorClass, validClass) {
	// 	$(element).removeClass('is-invalid');
	// 	}
	// });
	$('#spinnerLoading').hide();
}
</script>