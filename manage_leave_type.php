<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM leave_type where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-leave_type">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<div class="form-group">
			<label for="" class="control-label">Name</label>
			<input type="text" class="form-control" name="name"  value="<?php echo isset($name) ? $name :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Description</label>
			<textarea name="description" id="description" class="form-control" cols="30" rows="2" required><?php echo isset($description) ? $description :'' ?></textarea>
		</div>
		<div class="form-group">
			<div class="form-check">
			  <input class="form-check-input" type="checkbox" value="1" id="is_payable" name="is_payable" <?php echo isset($is_payable) && $is_payable == 1 ? "checked" : '' ?>>
			  <label class="form-check-label" for="is_payable">
			    Payable Leave
			  </label>
			</div>
		</div>
	</form>
</div>
<script>
	$('#manage-leave_type').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_leave_type',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				
			}
		})
	})
</script>