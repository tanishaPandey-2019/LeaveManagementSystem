<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM position where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<div id="msg" class="from-group"></div>
	<form action="" id="manage-position">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<div class="form-group">
			<label for="" class="control-label">Name</label>
			<input type="text" class="form-control" name="name"  value="<?php echo isset($name) ? $name :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Department</label>
			<select name="department_id" id="department_id" class="custom-select select2">
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
	</form>
</div>
<script>
	$('.select2').select2({
		placeholder:"Please Select Here",
		width:"100%"
	})
	$('#manage-position').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_position',
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