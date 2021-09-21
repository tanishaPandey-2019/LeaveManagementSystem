<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM employee_details where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	if(!is_numeric($k))
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<div id="msg" class="form-group"></div>
	<form action="" id="manage-employee">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<div class="row form-group">
			<div class="col-md-5">
				<label for="" class="control-label">Employee ID</label>
				<input type="text" class="form-control" name="employee_id"  value="<?php echo isset($employee_id) ? $employee_id :'' ?>" required>
				<small><i>Leave blank to auto generate when saving</i></small>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-4">
				<label for="" class="control-label">Last Name</label>
				<input type="text" class="form-control" name="lastname"  value="<?php echo isset($lastname) ? $lastname :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">First Name</label>
				<input type="text" class="form-control" name="firstname"  value="<?php echo isset($firstname) ? $firstname :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Middle Name</label>
				<input type="text" class="form-control" name="middlename"  value="<?php echo isset($middlename) ? $middlename :'' ?>" required>
			</div>
		</div>
		<hr>
			
			<div class="row form-group">
				<div class="col-md-4">
					<label for="" class="control-label">Address</label>
					<textarea name="address" id="address" class="form-control" cols="30" rows="2" required=""><?php echo isset($address) ? $address :'' ?></textarea>
				</div>
				<div class="col-md-4">
					<label for="" class="control-label">Contact No.</label>
					<textarea name="contact" id="contact" class="form-control" cols="30" rows="2" required=""><?php echo isset($contact) ? $contact :'' ?></textarea>
				</div>
			</div>
		<hr>

			<div class="row form-group">
				<div class="col-md-4">
					<label for="" class="control-label">Department</label>
					<select name="department_id" id="department_id" class="custom-select select2" required="">
						<option value="">
						<?php 
						$dept = $conn->query("SELECT * FROM department order by name asc");
						while($row = $dept->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($department_id) && $department_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
						<?php endwhile; ?>
						</option>
					</select>
				</div>
				<div class="col-md-4">
					<label for="" class="control-label">Position</label>
					<select name="position_id" id="position_id" class="custom-select select2" required="">
						<option value="" disabled="" selected="">Select Department First</option>
					</select>
				</div>
				<div class="col-md-4">
					<label for="" class="control-label">Type</label>
					<select name="type" id="type" class="custom-select" required="">
						<option value="1" <?php echo isset($type) && $type == 1 ? "selected" :'' ?>>Principal</option>
						<option value="2" <?php echo isset($type) && $type == 2 ? "selected" :'' ?>>Department Head</option>
						<option value="3" <?php echo isset($type) && $type == 3 ? "selected" :'' ?>>Manager</option>
						<option value="4" <?php echo isset($type) && $type == 4 ? "selected" :'' ?>>Supervisor</option>
						<option value="5" <?php echo isset($type) ? ($type == 5 ? "selected" : "") :'selected' ?>>Regular</option>
					</select>
				</div>
			</div>
			<div class="row form-group" id="man_sup">
				<div class="col-md-4" id="man-field">
					<label for="" class="control-label">Manager</label>
					<select name="manager_id" id="manager_id" class="custom-select select2" required="">
						<option value="" disabled="" selected="">Select Department First</option>
					</select>
				</div>
				<div class="col-md-4"  id="sup-field">
					<label for="" class="control-label">Supervisor</label>
					<select name="supervisor_id" id="supervisor_id" class="custom-select select2" required="">
						<option value="" disabled="" selected="">Select Department First</option>
					</select>
				</div>
			</div>
		</>
	</form>
</div>
<script>
	$(document).ready(function(){
		if('<?php echo isset($id) ?>' == 1){
			$('#department_id , #type').trigger('change')
		}
	})
	$('.select2').select2({
		placeholder:"Please Select Here",
		width:"100%"
	})
	$('#type').change(function(){
		if($(this).val() <= 2){
			$('#man_sup').hide()
		}else{
			$('#man_sup').show()
				if($(this).val() > 3){
					$('#man-field').show()
				}else{
					$('#man-field').hide()
				}
				if($(this).val() > 4){
					$('#sup-field').show()
				}else{
					$('#sup-field').hide()
				}
		}
	})
	$('#department_id').change(function(){
		start_load()
		var department_id = $(this).val()
		$.ajax({
			url:'ajax.php?action=get_positions',
			method:"POST",
			data:{department_id : department_id},
			success:function(resp){
				if(typeof resp != undefined){
					resp = JSON.parse(resp)
					if(Object.keys(resp).length > 0){
						$("#position_id").html('<option value=""></option>')
						Object.keys(resp).map(k=>{
							var selected = "<?php echo isset($position_id) ? $position_id : '' ?>";
						$("#position_id").append('<option value="'+resp[k].id+'" '+(selected != '' && selected == resp[k].id ? 'selected' : '')+'>'+resp[k].name+'</option>')
						})
					}else{
						$("#position_id").html('<option value="" disabled="" selected="">No position is listed under selected department</option>')
					}
				}
			},
			complete:function(){
				get_manager_supervisor(department_id)
			}
		})
	})
	function get_manager_supervisor($department_id){
		$.ajax({
			url:'ajax.php?action=get_manager',
			method:"POST",
			data:{department_id : $department_id},
			success:function(resp){
				if(typeof resp != undefined){
					resp = JSON.parse(resp)
					if(Object.keys(resp).length > 0){
						$("#manager_id").html('<option value=""></option>')
						Object.keys(resp).map(k=>{
							var selected = "<?php echo isset($manager_id) ? $manager_id : '' ?>";
						$("#manager_id").append('<option value="'+resp[k].id+'" '+(selected != '' && selected == resp[k].id ? 'selected' : '')+'>'+resp[k].name+'</option>')
						})
					}else{
						$("#manager_id").html('<option value="" disabled="" selected="">No Manager is listed under selected department</option>')
					}
				}
			},
		})
		$.ajax({
			url:'ajax.php?action=get_supervisor',
			method:"POST",
			data:{department_id : $department_id},
			success:function(resp){
				if(typeof resp != undefined){
					resp = JSON.parse(resp)
					if(Object.keys(resp).length > 0){
						$("#supervisor_id").html('<option value=""></option>')
						Object.keys(resp).map(k=>{
							var selected = "<?php echo isset($supervisor_id) ? $supervisor_id : '' ?>";
						$("#supervisor_id").append('<option value="'+resp[k].id+'" '+(selected != '' && selected == resp[k].id ? 'selected' : '')+'>'+resp[k].name+'</option>')
						})
					}else{
						$("#supervisor_id").html('<option value="" disabled="" selected="">No Supervisor is listed under selected department</option>')
					}
				}
			},
			complete:function(){
				end_load()
			}
		})
	}
	$('#manage-employee').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_employee',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp){
					resp = JSON.parse(resp);
					if(resp.status == 1){
						alert_toast("Data successfully saved",'success')
						setTimeout(function(){
							location.reload()
						},1500)
					}else{
						$('#msg').html('<div class="alert alert-danger">'+resp.msg+'</div>')
						end_load()
					}
					
				}
			}
		})
	})
</script>