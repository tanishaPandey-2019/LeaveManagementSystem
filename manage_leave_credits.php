<?php include "db_connect.php" ?>
<?php

extract($_GET);
$qry = $conn->query("SELECT * FROM leave_credits where employee_id =".$id);
while($row= $qry->fetch_assoc()):
	$lc[$row['leave_type_id']] = $row['credits'];
endwhile;

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<form id="manage-leave_c">
				<input type="hidden" name="eid" value="<?php echo $id ?>">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Leave Type</th>
							<th>Credits</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$qry = $conn->query("SELECT * FROM leave_type order by name asc");
						while($row=$qry->fetch_assoc()):
					?>
						<tr>
							<td><?php echo $row['name'] ?></td>
							<td class="text-center">
								<input type="hidden" name="leave_type_id[]" value="<?php echo $row['id'] ?>">
								<input type="number" class="form-control text-right" step="any" name="credits[]" value="<?php echo isset($lc[$row['id']]) ? $lc[$row['id']] : 0 ?>">
							</td>
						</tr>
				<?php endwhile; ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div> 

<script>
	$('#manage-leave_c').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_leave_c',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved",'success');
					uni_modal("Employee Details","view_employee.php?id=<?php echo $id ?>","mid-large")
				}
			}
		})
	})
</script>