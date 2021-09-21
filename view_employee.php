<?php
include 'db_connect.php';
extract($_POST);
$etype = array("","Principal","Department Head","Manager","Supervisor","Regular");
$qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM employee_details where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
$department = $conn->query("SELECT * FROM department where id = $department_id ");
$department = $department->num_rows > 0 ? $department->fetch_array()['name'] : "Unknown";
$position = $conn->query("SELECT * FROM position where id = $position_id ");
$position = $position->num_rows > 0 ? $position->fetch_array()['name'] : "Unknown";
$emp = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM employee_details where id in ($manager_id,$supervisor_id) ");
while($row=$emp->fetch_assoc()){
	$ename[$row['id']] = ucwords($row['name']);
}
$qry = $conn->query("SELECT * FROM leave_credits where employee_id =".$id);
while($row= $qry->fetch_assoc()):
	$lc[$row['leave_type_id']] = $row['credits'];
endwhile;
?>
<style type="text/css">
	#dfield p{
		margin: unset
	}
	#uni_modal .modal-footer{
		display: none;
	}
	#uni_modal .modal-footer.display{
		display: block;
	}
	.text-center{
		text-align:center;
	}
</style>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-6">
				<p>Employee ID: <b><?php echo $employee_id ?></b></p>
				<p>Name: <b><?php echo ucwords($name) ?></b></p>
				<p>Address: <b><?php echo ucwords($address) ?></b></p>
				<p>Contact #: <b><?php echo $contact ?></b></p>
			</div>
			<div class="col-md-6">
				<p>Department: <b><?php echo ucwords($department) ?></b></p>
				<p>Position: <b><?php echo ucwords($position) ?></b></p>
				<p>Manager: <b><?php echo $manager_id > 0 ? ucwords($ename[$manager_id]) : "N/A" ?></b></p>
				<p>Supervisor: <b><?php echo $supervisor_id > 0 ? ucwords($ename[$supervisor_id]) : "N/A" ?></b></p>
				<p>Type: <b><?php echo ucwords($etype[$type]) ?></b></p>
			</div>
		</div>
		<hr>
		<div class="row">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th>Leave Type</th>
						<th>Total Credits</th>
						<th>Used</th>
						<th>Available Credits</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$qry = $conn->query("SELECT * FROM leave_type order by name asc");
						while($row=$qry->fetch_assoc()):
							$credits = isset($lc[$row['id']]) ? $lc[$row['id']] : 0 ;
							$used = 0;
						$leave = $conn->query("SELECT * FROM leave_list where leave_type_id = '".$row['id']."' and employee_id= ".$id." and date_format(date_from,'%Y') = '".date('Y')."' and date_format(date_to,'%Y') = '".date('Y')."'and status != 2 "); 
								while($lrow= $leave->fetch_array()){
										$days = abs(strtotime($lrow['date_to'].' +1 day') - strtotime($lrow['date_from']));
										$days = floor($days / (60*60*24));
										for($i = 1; $i <= $days; $i++){
											$used = $i / $lrow['type'];
										}
									}
							$available = $credits -$used;
					?>
						<tr>
							<td><?php echo $row['name'] ?></td>
							<td class="text-center"><?php echo $credits ?></td>
							<td class="text-center"><?php echo $used ?></td>
							<td class="text-center"><?php echo $available ?></td>
						</tr>
				<?php endwhile; ?>
				</tbody>	
			</table>
		</div>
	</div>
</div>
<div class="modal-footer display">
	<div class="row">
		<div class="col-lg-12">
			<button class="btn btn-sm btn-secondary col-md-3 float-right" type="button" data-dismiss="modal">Close</button>
			<button class="btn btn-sm btn-primary col-md-3 float-right mr-2" type="button" id="manage_leave_credits"><i class="fa fa-edit"></i> Manage Leave Credits</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#manage_leave_credits').click(function(){
		uni_modal("Manage <?php echo ucwords($name)." ($employee_id)" ?> Leave Credits","manage_leave_credits.php?id=<?php echo $id ?>","")
	})
</script>