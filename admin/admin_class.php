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
		$data .= ", type = '$type' ";
		$data .= ", window_id = '$window_id' ";
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

	
	function save_transaction(){
		extract($_POST);
		$data = " name = '$name' ";
		$cwhere ='';
		if(!empty($id)){
			$cwhere = " and id != $id ";
		}
		$chk =  $this->db->query("SELECT * FROM transactions where ".$data.$cwhere)->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO transactions set ".$data);
		}else{
			$save = $this->db->query("UPDATE transactions set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_transaction(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM transactions where id = ".$id);
		if($delete)
			return 1;
	}
	
	function save_window(){
		extract($_POST);
		$data = " section_name = '$name' ";
		$data .= ", transaction_id = '$transaction_id' ";
		$cwhere ='';
		if(!empty($id)){
			$cwhere = " and id != $id ";
		}
		$chk =  $this->db->query("SELECT * FROM transaction_windows where section_name = '$name' and transaction_id = '$transaction_id' ".$cwhere)->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO transaction_windows set ".$data);
		}else{
			$save = $this->db->query("UPDATE transaction_windows set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_window(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM transaction_windows where id = ".$id);
		if($delete)
			return 1;
	}
	function save_uploads(){
		extract($_POST);
		$ids= array();
		for($i = 0 ; $i< count($img);$i++){
			list($type, $img[$i]) = explode(';', $img[$i]);
			list(, $img[$i])      = explode(',', $img[$i]);
			$img[$i] = str_replace(' ', '+', $img[$i]);
			$img[$i] = base64_decode($img[$i]);
			$fname = strtotime(date('Y-m-d H:i'))."_".$imgName[$i];
			// $upload = move_uploaded_file($fname,$img[$i],"assets/uploads/");
			$upload = file_put_contents("assets/uploads/".$fname,$img[$i]);
			$data = " file_path = '".$fname."' ";
			if($upload)
			$save[] = $this->db->query("INSERT INTO file_uploads set".$data);
			else{
				echo "INSERT INTO file_uploads set".$data;
				exit;
			}
		}
		if(isset($save)){
			return 1;
		}
	}
	function delete_uploads(){
		extract($_POST);
		$path = $this->db->query("SELECT file_path FROM file_uploads where id = ".$id)->fetch_array()['file_path'];
		$delete = $this->db->query("DELETE FROM file_uploads where id = ".$id);
		if($delete)
			unlink('assets/uploads/'.$path);
			return 1;
	}
	function save_window1(){
		extract($_POST);
		$data = " section_name = '$name' ";
		$data .= ", transaction_id = '$transaction_id' ";
		$cwhere ='';
		if(!empty($id)){
			$cwhere = " and id != $id ";
		}
		$chk =  $this->db->query("SELECT * FROM transaction_windows where section_name = '$name' and transaction_id = '$transaction_id' ".$cwhere)->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO transaction_windows set ".$data);
		}else{
			$save = $this->db->query("UPDATE transaction_windows set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	
	function save_queue(){
		extract($_POST);
		$queryStr = "SELECT name FROM transactions where id = $transaction_id";
		$name = $this->db->query($queryStr);
		$data = " name = '".$name->fetch_array()[0]."' ";
		$data .= ", transaction_id = '$transaction_id' ";
		$data .= ", vname = '$vname' ";
		$data .= ", position = '$position' ";
		$data .= ", office_school = '$office_school' ";
		$data .= ", purpose = '$purpose' ";

		$queue_no = 1001;
		$chk = $this->db->query("SELECT * FROM queue_list where date(date_created) = '".date("Y-m-d")."' ")->num_rows;
		$queue_no += $chk;
		$data .= ", queue_no = '$queue_no' ";
		$insertVar = "INSERT INTO queue_list set ".$data;

		$save = $this->db->query("INSERT INTO queue_list set ".$data);
		if($save)
			return $this->db->insert_id;
	}
	function save_queue1(){
	  extract($_POST);

	  // Get the name associated with the transaction_id
	  $name_query = $this->db->query("SELECT name FROM transaction WHERE id = $transaction_id");
	  $name = $name_query->fetch_assoc()['name'];
	  // Build the data string for the SQL query
	  $data = "name = '$name'";
	  $data .= ", transaction_id = $transaction_id";
	  
	  // Calculate the queue number
	  $queue_no = 1001;
	  $queue_count_query = $this->db->query("SELECT COUNT(*) as count FROM queue_list WHERE transaction_id = $transaction_id AND DATE(date_created) = '".date("Y-m-d")."'");
	  $queue_count = $queue_count_query->fetch_assoc()['count'];
	  $queue_no += $queue_count;
	  $data .= ", queue_no = $queue_no";

	  // Execute the SQL query to insert the data into the database
	  $insert_query = "INSERT INTO queue_list SET ".$data;
	  $save = $this->db->query($insert_query);
	  if($save){
		return $this->db->insert_id;
	  } else {
		// Handle the case where the SQL query fails
		error_log("Error inserting data into queue_list table: ".$this->db->error);
		return false;
	  }
	}
	function get_queueid($id){
    $queue = $this->db->query("SELECT * FROM queue_list WHERE id = $id")->fetch_assoc();
	//var_dump($queue);
    return $queue;
}

	function get_queue(){
		extract($_POST);
		$query = $this->db->query("SELECT q.*,t.section_name as wname FROM queue_list as q inner join transaction_windows as t on t.transaction_id = q.transaction_id and t.id = q.window_id where date(q.date_created) = curdate() and  q.status = 1");
		// var_dump($query);
		if($query->num_rows >= 0){
			$data = array(); //initialize an empty array
			while ($row = $query->fetch_assoc()) {
				$data[] = $row; //add fetched row to data array
			}
			return json_encode(array('status'=>1,"data"=>$data));
		}else{
			return json_encode(array('status'=>0));
		}
	}
	function get_queue_user(){
		extract($_POST);
		$query = $this->db->query("SELECT q.*,t.section_name as wname FROM queue_list as q inner join transaction_windows as t on t.transaction_id = q.transaction_id inner join users as u on u.id = t.user_id  where  date(q.date_created) = curdate() AND q.status='0' and u.id={$_SESSION['login_id']};");
		//var_dump($query);
		if($query->num_rows > 0){
			$data = array(); //initialize an empty array
			while ($row = $query->fetch_assoc()) {
				$data[] = $row; //add fetched row to data array
			}
			return json_encode(array('status'=>0,"data"=>$data));
		}else{
			return json_encode(array('status'=>0));
		}
	}
	function get_serve_user(){
		extract($_POST);
		$query = $this->db->query("SELECT q.*,t.section_name as wname FROM queue_list as q inner join transaction_windows as t on t.transaction_id = q.transaction_id inner join users as u on u.id = t.user_id  where  date(q.date_created) = curdate() AND q.status='1' and u.id={$_SESSION['login_id']};");
		//var_dump($query);
		if($query->num_rows > 0){
			$data = array(); //initialize an empty array
			while ($row = $query->fetch_assoc()) {
				$data[] = $row; //add fetched row to data array
			}
			return json_encode(array('status'=>1,"data"=>$data));
		}else{
			return json_encode(array('status'=>0));
		}
	}
	function get_wait(){
		extract($_POST);
		$query = $this->db->query("SELECT q.*,t.name as wname FROM queue_list as q inner join transactions as t on t.id = q.transaction_id where date(q.date_created) = '".date('Y-m-d')."' and  q.status = 0");
		//var_dump($query);
		if($query->num_rows > 0){
			$data = array(); //initialize an empty array
			while ($row = $query->fetch_assoc()) {
				$data[] = $row; //add fetched row to data array
			}
			return json_encode(array('status'=>0,"data"=>$data));
		}else{
			return json_encode(array('status'=>0));
		}
	}
	function update_queue(){
    $row_id = $_POST['row_id'];
    $tid = $this->db->query("SELECT * FROM transaction_windows WHERE id = ".$_SESSION['login_window_id'])->fetch_array()['transaction_id'];

    $query = $this->db->query("SELECT tw.id FROM transaction_windows AS tw INNER JOIN queue_list AS q ON tw.transaction_id = q.transaction_id WHERE tw.user_id = '".$_SESSION['login_id']."'");
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        $window_id = $row['id'];
		var_dump($window_id);
        if(!empty($row_id)){
            $update = $this->db->query("UPDATE queue_list SET status = 1, user_id = '".$_SESSION['login_id']."', window_id = '$window_id' WHERE id = '$row_id'");
            if($update){
                return 1;
            }
        }
    }
}

	function done_queue(){
		$row_id = $_POST['row_id'];
		$tid = $this->db->query("SELECT * FROM transaction_windows where id =".$_SESSION['login_window_id'])->fetch_array()['transaction_id'];
		
		if(!empty($row_id)){
		
			$update = $this->db->query("UPDATE queue_list set status = 2 ,user_id = '".$_SESSION['login_id']."' where id ='$row_id'");
		}
		if($update){
			return 1;
		}

	}
}