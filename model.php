<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='model';
  $brand = $db->query("SELECT * FROM brand WHERE deleted = '0' ORDER BY brand asc");
  $country = $db->query("SELECT * FROM country WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Model</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addModel">Add Model</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="modelTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No.</th>
                                    <th>Brand</th>
									<th>Model</th>
                                    <th>Make In</th>
									<th>Actions</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="modelModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="modelForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Models</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
    					<input type="hidden" class="form-control" id="id" name="id">
    				</div>
                    <div class="form-group">
                        <label for="code">Brand *</label>
                        <select class="form-control select2" id="brand" name="brand">
                            <option value="" selected disabled hidden>Please Select</option>
                            <?php while($rowCustomer2=mysqli_fetch_assoc($brand)){ ?>
                                <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['brand'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
    				<div class="form-group">
    					<label for="model">Model *</label>
    					<input type="text" class="form-control" name="model" id="model" placeholder="Enter Models" required>
    				</div>
                    <div class="form-group">
    					<label for="model">Make In *</label>
    					<select class="form-control select2" id="country" name="country" required>
                            <option value="" selected disabled hidden>Please Select</option>
                            <?php while($rowcountry=mysqli_fetch_assoc($country)){ ?>
                                <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
                            <?php } ?>
                        </select>
    				</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitLot">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
$(function () {
    $("#modelTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadModel.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'brand_name' },
            { data: 'model' },
            { data: 'iso3' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
        ],
        "rowCallback": function( row, data, index ) {

            $('td', row).css('background-color', '#E6E6FA');
        },
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/model.php', $('#modelForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#modelModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#modelTable').DataTable().ajax.reload();
                    $('#spinnerLoading').hide();
                }
                else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                    $('#spinnerLoading').hide();
                }
                else{
                    toastr["error"]("Something wrong when edit", "Failed:");
                    $('#spinnerLoading').hide();
                }
            });
        }
    });

    $('#addModel').on('click', function(){
        $('#modelModal').find('#id').val("");
        $('#modelModal').find('#brand').val("");
        $('#modelModal').find('#model').val("");
        $('#modelModal').find('#country').val("");
        $('#modelModal').modal('show');
        
        $('#modelForm').validate({
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
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getModel.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#modelModal').find('#id').val(obj.message.id);
            $('#modelModal').find('#brand').val(obj.message.brand);
            $('#modelModal').find('#model').val(obj.message.model);
            $('#modelModal').find('#country').val(obj.message.make);
            $('#modelModal').modal('show');
            
            $('#modelForm').validate({
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
        }
        else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
        else{
            toastr["error"]("Something wrong when activate", "Failed:");
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    if (confirm('Are you sure you want to cancel this item?')) {
        $('#spinnerLoading').show();
        $.post('php/deleteModel.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#modelTable').DataTable().ajax.reload();
                $('#spinnerLoading').hide();
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
                $('#spinnerLoading').hide();
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
                $('#spinnerLoading').hide();
            }
        });
    }
}
</script>