<?php include 'db_connect.php' ?>
<?php 
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM leave_list  where id=".$_GET['id']);
	foreach($qry->fetch_array() as $k =>$v){
		$$k = $v;
	}
}

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<form action="" id="manage-leave">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group">
				<label for="" class="control-label">Leave Type</label>
				<select name="leave_type_id" id="leave_type_id" class="custom-select" required>
					<option value=""></option>
					<?php 
					$lt = $conn->query("SELECT * FROM leave_type order by name asc");
					while($row=$lt->fetch_array()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($leave_type_id) && $leave_type_id == $row['id'] ? "selected" : '' ?>><?php echo $row['name'] ?></option>
					<?php endwhile; ?>
				</select>
				<small><i>Your available credits for selected leave type id: <span id="credits">0</span></i></small>
			</div>
			<div class="on-ltchange" <?php echo isset($id) ? '' : 'style="display: none"' ?>>
				<div class="form-group">
					<label for="" class="control-label">Date FROM</label>
					<input type="date" class="form-control" name="date_from" value="<?php echo isset($date_from) ? date("Y-m-d",strtotime($date_from)) : '' ?>">
				</div>
				<div class="form-group">
					<label for="" class="control-label">Date To</label>
					<input type="date" class="form-control" name="date_to"  value="<?php echo isset($date_to) ? date("Y-m-d",strtotime($date_to)) : '' ?>">
				</div>
				<div class="form-group">
					<label for="" class="control-label">Type</label>
					<select name="type" id="" class="custom-select">
						<option value="1" <?php echo isset($type) && $type == 1 ? "selected" : '' ?>>Whole Day</option>
						<option value="2" <?php echo isset($type) && $type == 2 ? "selected" : '' ?>>Half Day</option>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Reason</label>
					<textarea name="reason" cols="30" rows="2" class="form-control"><?php echo isset($reason) ? $reason : '' ?></textarea>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function(){
		if('<?php echo isset($id) ?>' == 1){
			$('#leave_type_id').trigger('change')
		}
	})
	$('#leave_type_id').change(function(){
		start_load()
		if($('.err-msg').length > 0)
			$('.err-msg').remove()
		var leave_type_id = $(this).val()
		$.ajax({
			url:'ajax.php?action=get_available',
			method:"POST",
			data:{leave_type_id : leave_type_id, id:'<?php echo isset($id) ? $id : '' ?>'},
			success:function(resp){
					$('#credits').html(resp)
					if(resp <=  1){
						$('#leave_type_id').closest('.form-group').append('<div class="alert alert-danger err-msg">You dont have an available credits with the selected leave type.</div>')
						$('.on-ltchange').hide()
					}else{
						$('.on-ltchange').show()
					}
					
			},
			complete:function(){
				end_load()
			}
		})
	})
	$('[name="date_from"],[name="date_to"],[name="type"]').change(function(){
		if($('[name="date_from"]').val() == '' || $('[name="date_to"]').val() == ''){
			return false;
		}
		if($('.err-msg').length > 0)
			$('.err-msg').remove()
		var from = $('[name="date_from"]').val()
		var to = $('[name="date_to"]').val()
		from = new Date(from);
		to = new Date(to)
		if(from.getFullYear() != to.getFullYear()){
			$('[name="date_to"]').closest('.form-group').append('<div class="alert alert-danger err-msg">Date From and To must be the same year.</div>')
			return false;
		}
		// from = from.getFullYear() +'-'+from.getMonth()+'-'+from.getDay();
		// to = to.getFullYear() +'-'+to.getMonth()+'-'+to.getDay();
		// console.log(from,to)
		if(from > to){
			$('[name="date_to"]').closest('.form-group').append('<div class="alert alert-danger err-msg">Selected dates are incorrect.</div>')
		}

		var days = Math.abs(to - from);
			days = Math.ceil(days / (1000*60*60*24));
		var credits = $('#credits').html();
		var type = $('[name="type"]').val();
			days = days / type;
			console.log(days,credits)
			if(credits < days){
				$('[name="date_to"]').closest('.form-group').append('<div class="alert alert-danger err-msg">Selected dates difference is greater that available credits with the selected type.</div>')
			}

	})
	$('#manage-leave').submit(function(e){
		e.preventDefault()
		if($('.err-msg').length > 0)
			return false;
		start_load()
		$.ajax({
			url:'ajax.php?action=save_leave',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Leave application successfully saved",'success');
					setTimeout(function(){
						location.reload()
					},1000)
				}
			}
		})

	})
</script>