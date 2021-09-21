<?php include('db_connect.php');?>

<div class="container-fluid">
<style>
	input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  transform: scale(1.5);
  padding: 10px;
}
</style>
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>List of Employees</b>
						<span class="">

							<button class="btn btn-primary btn-block btn-sm col-sm-2 float-right" type="button" id="new_employee">
					<i class="fa fa-plus"></i> Add Employee</button>
				</span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Employee ID</th>
									<th class="">Name</th>
									<th class="">Department</th>
									<th class="">Postion</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$dname[0] = "Unset";
								$department = $conn->query("SELECT * FROM department");
								while($row=$department->fetch_assoc()):
									$dname[$row['id']] = ucwords($row['name']);
								endwhile;
								$pname[0] = "Unset";
								$position = $conn->query("SELECT * FROM position");
								while($row=$position->fetch_assoc()):
									$pname[$row['id']] = ucwords($row['name']);
								endwhile;
								$employee = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM employee_details order by name asc");
								while($row=$employee->fetch_assoc()):
									$department = isset($dname[$row['department_id']]) ? $dname[$row['department_id']] : "Department has been deleted.";
									$position = isset($pname[$row['position_id']]) ? $pname[$row['position_id']] : "Position has been deleted.";

								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										 <p> <b><?php echo $row['employee_id'] ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo ucwords($row['name']) ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo $department ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo $position ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary view_employee" type="button" data-id="<?php echo $row['id'] ?>" >View</button>
										<button class="btn btn-sm btn-outline-primary edit_employee" type="button" data-id="<?php echo $row['id'] ?>" >Edit</button>
										<button class="btn btn-sm btn-outline-danger delete_employee" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	$('#new_employee').click(function(){
		uni_modal("New employee","manage_employee.php","mid-large")
	})
	
	$('.edit_employee').click(function(){
		uni_modal("Edit employee","manage_employee.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.view_employee').click(function(){
		uni_modal("Employee Details","view_employee.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_employee').click(function(){
		_conf("Are you sure to delete this employee?","delete_employee",[$(this).attr('data-id')])
	})
	$('#check_all').click(function(){
		if($(this).prop('checked') == true)
			$('[name="checked[]"]').prop('checked',true)
		else
			$('[name="checked[]"]').prop('checked',false)
	})
	$('[name="checked[]"]').click(function(){
		var count = $('[name="checked[]"]').length
		var checked = $('[name="checked[]"]:checked').length
		if(count == checked)
			$('#check_all').prop('checked',true)
		else
			$('#check_all').prop('checked',false)
	})
	$('#print_selected').click(function(){
		var checked = $('[name="checked[]"]:checked').length
		if(checked <= 0){
			alert_toast("Check atleast one individual details row first.","danger")
			return false;
		}
		var ids = [];
		$('[name="checked[]"]:checked').each(function(){
			ids.push($(this).val())
		})
		start_load()
		$.ajax({
			url:"print_employees.php",
			method:"POST",
			data:{ids : ids},
			success:function(resp){
				if(resp){
					var nw = window.open("","_blank","height=600,width=900")
					nw.document.write(resp)
					nw.document.close()
					nw.print()
					setTimeout(function(){
						nw.close()
						end_load()
					},700)
				}
			}
		})
	})

	function delete_employee($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_employee',
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