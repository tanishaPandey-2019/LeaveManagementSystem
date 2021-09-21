<?php include 'db_connect.php' ?>
<style>
	td p {
		margin:unset;
	}
</style>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">My leave Application List</div>
			<div class="card-body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Leave Info</th>
							<th>Status</th>
							<th>Action By</th>
							<th>Action Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$i = 1;
						$types = $conn->query("SELECT * FROM leave_type");
						while($row=$types->fetch_assoc()){
							$lt[$row['id']] = ucwords($row['name']);
						}
						$qry = $conn->query("SELECT * FROM leave_list where employee_id= ".$_SESSION['details']['id']." ");
						while($row=$qry->fetch_assoc()):
							$days = abs(strtotime($row['date_to'].' +1 day') - strtotime($row['date_from']));
							$days = floor($days / (60*60*24));
							$action_by = 'N/A';
							if($row['status'] > 0){
								$emp = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name from employee_details where id = ".$row['approved_by']);
								if($emp->num_rows > 0 ){
									$action_by = ucwords($emp->fetch_array()['name']);
								}
							}
						?>
						<tr>
							<td class="text-center"><?php echo $i++ ?></td>
							<td>
								<p>Leave Type: <b><?php echo $lt[$row['leave_type_id']] ?></b></p>
								<p><small>Type: <b><?php echo $row['type'] == 1 ? "Whole Day" :'Half Day' ?></b></small></p>
								<p><small>From: <b><?php echo date("M d,Y",strtotime($row['date_from'])) ?></b></small></p>
								<p><small>To: <b><?php echo date("M d,Y",strtotime($row['date_to'])) ?></b></small></p>
								<p><small>Days: <b><?php echo $days ?></b></small></p>
							</td>
							<td class="text-center">
								<?php if($row['status'] == 0): ?>
									<span class="badge badge-primary">Pending</span>
								<?php elseif($row['status'] == 1): ?>
									<span class="badge badge-success">Approved</span>
								<?php elseif($row['status'] == 2): ?>
									<span class="badge badge-success">Declined</span>
								<?php endif; ?>
							</td>
							<td>
								<?php echo $action_by ?>
							</td>
							<td><?php echo $row['status'] > 0 ? date("M d,Y",strtotime($row['date_approved'])) : 'N/A' ?></td>
							<td class="text-center">
								<button class="btn btn-sm btn-outline-primary edit_leave" type="button" data-id="<?php echo $row['id'] ?>" >Edit</button>
								<button class="btn btn-sm btn-outline-danger delete_leave" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
							</td>
						</tr>
					<?php endwhile;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$('.edit_leave').click(function(){
		uni_modal("Edit Leave","manage_leave.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_leave').click(function(){
		_conf("Are you sure to delete this leave application?","delete_leave",[$(this).attr('data-id')])
	})
	function delete_leave($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_leave',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>