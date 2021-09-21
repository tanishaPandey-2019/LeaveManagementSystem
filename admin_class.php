<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_type'] ==2){
				$emp = $this->db->query("SELECT * FROM employee_details where user_id =".$_SESSION['login_id']);
				foreach ($emp->fetch_array() as $key => $value) {
				if(!is_numeric($key))
					$_SESSION['details'][$key] = $value;
				}
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		if(isset($type))
		$data .= ", type = '$type' ";
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = 3";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_leave_type(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", description = '$description' ";
		if(isset($is_payable))
		$data .= ", is_payable = '$is_payable' ";
		else
		$data .= ", is_payable = 0 ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO leave_type set ".$data);
		}else{
			$save = $this->db->query("UPDATE leave_type set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_leave_type(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM leave_type where id = ".$id);
		if($delete)
			return 1;
	}
	
	function save_department(){
		extract($_POST);
		$data = " name = '$name' ";
		
		if(empty($id)){
			$chk = $this->db->query("SELECT * FROM department where name = '$name' ")->num_rows;
			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Deparment Name Already Exist.'));
				exit;
			}
			$save = $this->db->query("INSERT INTO department set ".$data);
		}else{
			$chk = $this->db->query("SELECT * FROM department where name = '$name' and id != $id ")->num_rows;
			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Deparment Name Already Exist.'));
				exit;
			}
			$save = $this->db->query("UPDATE department set ".$data." where id=".$id);
		}
		if($save)
			return json_encode(array("status" => 1));
	}
	function delete_department(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM department where id = ".$id);
		if($delete)
			return 1;
	}
	function save_position(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", department_id = '$department_id' ";
		

		if(empty($id)){
			$chk = $this->db->query("SELECT * FROM position where name = '$name' and department_id = $department_id ")->num_rows;
			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Position already exist in selected department.'));
				exit;
			}
			$save = $this->db->query("INSERT INTO position set ".$data);
			
		}else{
			$chk = $this->db->query("SELECT * FROM position where name = '$name' and department_id = $department_id and id != $id ")->num_rows;
			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Position already exist in selected department.'));
				exit;
			}
			$save = $this->db->query("UPDATE position set ".$data." where id=".$id);
		}
		if($save){

			return json_encode(array("status"=>1));
		}
	}
	function delete_position(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM position where id = ".$id);
		if($delete){
				return 1;
			}
	}
	function save_employee(){
		extract($_POST);
		$data = " lastname = '$lastname' ";
		$data .= ", firstname = '$firstname' ";
		$data .= ", middlename = '$middlename' ";
		$data .= ", department_id = '$department_id' ";
		$data .= ", position_id = '$position_id' ";
		$data .= ", type = '$type' ";
		$data .= ", address = '$address' ";
		$data .= ", contact = '$contact' ";
		if($type > 3 && isset($manager_id))
		$data .= ", manager_id = '$manager_id' ";
		else
		$data .= ", manager_id = 0 ";
		if($type > 4 && isset($supervisor_id))
		$data .= ", supervisor_id = '$supervisor_id' ";
		else
		$data .= ", supervisor_id = 0 ";
		if(empty($employee_id)){
			$i = 1;
			while($i == 1):
				$employee_id = mt_rand(1,99999999);
				$employee_id = sprintf("%'08d", $employee_id);
				$chk = $this->db->query("SELECT * FROM employee_details where employee_id = '$employee_id'");
				if($chk->num_rows <= 0){
					$i=0; 
				}
			endwhile;
			$employee_id = str_replace(" ", "", $employee_id);
		}
		$data .= ", employee_id = '$employee_id' ";
		if(empty($id)){
			$chk = $this->db->query("SELECT * FROM employee_details where employee_id = '$employee_id' and department_id = $department_id ")->num_rows;
			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Employee ID already exist.'));
				exit;
			}
			//echo "INSERT INTO employee_details set ".$data;
			$save = $this->db->query("INSERT INTO employee_details set ".$data);
			if($save){
				$id = $this->db->insert_id;
				$data = " name='".($firstname.' '.$lastname)."' ";
				$data .= ", password= '".md5($employee_id)."' ";
				$uname = substr($firstname, 0,1).$lastname."_".$employee_id;
				$data .= ", username= '$uname' ";
				$save2 = $this->db->query("INSERT INTO users set ".$data);
				if($save2){
					$user_id = $this->db->insert_id;
					$this->db->query("UPDATE employee_details set user_id = $user_id where id = $id ");
				}
				return json_encode(array("status"=>1));

			}
		}else{
			$chk = $this->db->query("SELECT * FROM employee_details where employee_id = '$employee_id' and department_id = $department_id and id != $id ")->num_rows;

			if($chk > 0){
				return json_encode(array("status" => 2,"msg"=>'Employee ID already exist.'));
				exit;
			}
			$save = $this->db->query("UPDATE employee_details set ".$data." where id=".$id);
			if($save){
				return json_encode(array("status"=>1));
			}
		}
		
	}
	function delete_employee(){
		extract($_POST);
		$user_id = $this->db->query("SELECT FROM employee_details where id = ".$id)->fetch_array()['user_id'];
		$delete = $this->db->query("DELETE FROM position where id = ".$id);
		$delete1 = $this->db->query("DELETE FROM users where id = ".$user_id);
		if($delete){
				return 1;
			}
	}
	function get_positions(){
		extract($_POST);
		$get = $this->db->query("SELECT * FROM position where department_id = $department_id ");
		$data = array();
		while($row = $get->fetch_assoc()){
			$row['name'] = ucwords($row['name']);
			$data[] = $row;
		}
		
		return json_encode($data);
		
	}
	function get_manager(){
		extract($_POST);
		$get = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name  FROM employee_details where department_id = $department_id  and `type` =3 ");
		$data = array();
		while($row = $get->fetch_assoc()){
			$row['name'] = ucwords($row['name']);
			$data[] = $row;
		}
		
		return json_encode($data);
		
	}

	function get_supervisor(){
		extract($_POST);
		$get = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name  FROM employee_details where department_id = $department_id   and `type` =4 ");
		$data = array();
		while($row = $get->fetch_assoc()){
			$row['name'] = ucwords($row['name']);
			$data[] = $row;
		}
		
		return json_encode($data);
		
	}
	function save_leave_c(){
		extract($_POST);

		foreach ($leave_type_id as $k => $val) {
			$data=" leave_type_id = $val ";
			$data.=", credits = '$credits[$k]' ";
			$data.=", employee_id = $eid ";
			$chk = $this->db->query("SELECT * FROM leave_credits where employee_id = $eid and leave_type_id = $val ");
			if($chk->num_rows > 0){
				$id = $chk->fetch_array()['id'];
				$save[] = $this->db->query("UPDATE leave_credits set $data where id = $id ");
			}else{
				$save[] = $this->db->query("INSERT INTO leave_credits set $data");
			}
		}
		if(isset($save)){
			return 1;
		}
	}
	function get_available(){
		extract($_POST);
		$credits = $this->db->query("SELECT * FROM leave_credits where leave_type_id = $leave_type_id and employee_id= ".$_SESSION['details']['id']);
		$credits = $credits->num_rows > 0 ? $credits->fetch_array()['credits'] : 0 ;
		$used = 0;
		$awhere = '';
		if(isset($id) && $id > 0)
		$awhere =" and id != $id ";

		$leave = $this->db->query("SELECT * FROM leave_list where leave_type_id = $leave_type_id and employee_id= ".$_SESSION['details']['id']." and date_format(date_from,'%Y') = '".date('Y')."' and date_format(date_to,'%Y') = '".date('Y')."' $awhere and status != 2 "); 
		while($row= $leave->fetch_array()){
				$days = abs(strtotime($row['date_to'].' +1 day') - strtotime($row['date_from']));
				$days = floor($days / (60*60*24));
				for($i = 1; $i <= $days; $i++){
					$used = $i / $row['type'];
				}
			}
			return $credits - $used;
		}
	function save_leave(){
		extract($_POST);
			$data=" leave_type_id = $leave_type_id ";
			$data.=", type = '$type' ";
			$data.=", employee_id = ".$_SESSION['details']['id']." ";
			$data.=", date_from = '$date_from' ";
			$data.=", date_to = '$date_to' ";
			$data.=", reason = '$reason' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO leave_list set $data");
			}else{
				$save = $this->db->query("UPDATE leave_list set $data where id = $id");
			}
			if($save)
				return 1;
	}
	function delete_leave(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM leave_list where id = ".$id);
		if($delete){
				return 1;
			}
	}
	function action_leave(){
		extract($_POST);
			$data =" approved_by = ".$_SESSION['details']['id']." ";
			$data .=", date_approved = '".date('Y-m-d')."' ";
			$data .=", status = '$status' ";
			$update = $this->db->query("UPDATE leave_list set $data where id= $id ");
			if($update)
				return 1;

	}
}