
<style>
	.collapse a{
		text-indent:10px;
	}
</style>
<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">

				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
				<?php if($_SESSION['login_type'] == 1): ?>
				<a href="index.php?page=all_applications" class="nav-item nav-all_applications"><span class='icon-field'><i class="fa fa-list-alt">	</i></span> Leave Applications</a>
				<?php endif; ?>
				<?php if(isset($_SESSION['details']['type']) && $_SESSION['details']['type'] < 5 ): ?>
				<a href="index.php?page=applications" class="nav-item nav-applications"><span class='icon-field'><i class="fa fa-list-alt">	</i></span> Leave Application</a>
				<?php endif; ?>
				<?php if(isset($_SESSION['details']['type']) && $_SESSION['details']['type'] > 1): ?>
				<a href="javascript:void(0)" class="nav-item" id="add_leave"><span class='icon-field'><i class="fa fa-plus">	</i></span> New Leave Application</a>
				<a href="index.php?page=my_applications" class="nav-item nav-my_applications"><span class='icon-field'><i class="fa fa-th-list">	</i></span> My Leave Application</a>
				<?php endif; ?>
				<?php if($_SESSION['login_type'] == 1): ?>
				<a href="index.php?page=leave_type" class="nav-item nav-leave_type"><span class='icon-field'><i class="fa fa-th-list"></i></span> Leave Type</a>
				<a href="index.php?page=department" class="nav-item nav-department"><span class='icon-field'><i class="fa fa-list"></i></span> Department</a>
				<a href="index.php?page=position" class="nav-item nav-position"><span class='icon-field'><i class="fa fa-list"></i></span> Position</a>
				<a href="index.php?page=employee" class="nav-item nav-employee"><span class='icon-field'><i class="fa fa-user-friends"></i></span> Employee List</a>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
				
			<?php endif; ?>
		</div>

</nav>
<script>
	$('.nav_collapse').click(function(){
		console.log($(this).attr('href'))
		$($(this).attr('href')).collapse()
	})
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
	$('#add_leave').click(function(){
		uni_modal("New Leave Application","manage_leave.php","mid-large")
	})
</script>
