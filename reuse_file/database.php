<?php

class database
{
	private $dbhost = 'localhost';
	private $schemaname = 'YuKai200465333';
	private $dbname = 'root';
	private $dbpassword = '';

	private $conn;

	// create and check connection
	public function __construct(){
		if(!isset($this->db)){
			// connect to database
			try{
				$dsn = "mysql:host=$this->dbhost;dbname=$this->schemaname";
				$conn = new PDO($dsn, $this->dbname, $this->dbpassword);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->conn = $conn;
			}catch (PDOException $e){
				die("Failed to connect with mySQL database" . $e->getMessage());
			}
		}
	}

	// insert data
	public function insertData($tableName, $profileData){
		if(!empty($profileData) && is_array($profileData)){
			// store created data timestamp
			if(!array_key_exists('created', $profileData)){
				$profileData['created'] = date("Y-m-d H:i:s");
			}
			// concat string in data to fit sql(PDO) statement
			$columns = implode(", " , array_keys($profileData));
			$values = ":" . implode(", :", array_keys($profileData));

			$sql = "INSERT INTO .$tableName (". $columns .") VALUES (". $values .")";
			$stmt = $this->conn->prepare($sql);
			foreach ($profileData as $key => $val){
				$stmt->bindValue(':' . $key, $val);
			}
			$insert = $stmt->execute();
			return $insert ? $this->conn->lastInsertId() : false;
		} else {
			return false;
		}
	}

	// read data
	public function displayData($tableName, $conditions=array()){
		$sql  = 'SELECT';
		$sql .= array_key_exists("select", $conditions) ? $conditions['select'] : ' *';
		$sql .= 'FROM ' . $tableName;

		// where
		if(array_key_exists("where", $conditions)){
			$sql .= ' WHERE ';
			$i = 0;
			foreach ($conditions['where'] as $key=> $value){
				$pre  = ($i>0)?' AND ':'';
				$sql .= $pre.$key."='".$value."'";
				$i++;
			}
		}
		// order by
		if(array_key_exists("order_by", $conditions)){
			$sql .= ' ORDER BY ' . $conditions['order_by'];
		}
		// limit >>> (Offset correspond ['start'], Limit correspond ['limit'], ex: (9,11) will retrieve 10th - 20th row)
		if(array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)){
			$sql .= ' LIMIT ' . $conditions['start'] . ','
				. $conditions['limit'];
		} elseif(!array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)){
			$sql .= ' LIMIT ' . $conditions['limit'];
		}

		$stmt = $this->conn->prepare($sql);
		$stmt->execute();

		if(array_key_exists('return_type', $conditions) && $conditions['return_type'] != 'all'){
			switch ($conditions['return_type']){
				case 'count':
					$data = $stmt->rowCount();
					break;
				case 'single':
					//PDO::FETCH_ASSOC: Return next row as an array indexed by column name(return the result as an associative array.)
					/*The array keys will match your column names. If your table contains columns 'email' and 'password',
					    the array will be structured like:
						Array
						(
						    [email] => 'youremail@yourhost.com'
						    [password] => 'yourpassword'
						)

						To read data from the 'email' column, do:
						$user['email'];

						and for 'password':
						$user['password'];
					 *
					 */
					$data = $stmt->fetch(PDO::FETCH_ASSOC);
					break;
				default:
					$data = '';
			}
		}else{
			if($stmt->rowCount() > 0){
				$data = $stmt->fetchAll();
			}
		}
		return !empty($data) ? $data : false;
	}

	// update profile data
	public function updateData($tableName, $profileData, $update_profileData_id){
		if(!empty($profileData) && is_array($profileData)){
			// store created data timestamp
			if(!array_key_exists('modified', $profileData)){
				$profileData['modified'] = date("Y-m-d H:i:s");
			}

			$sql = 'UPDATE ' . $tableName . ' SET ';

			// use positional placeholders
			// collect data columns
			$profileData_columns = array_keys($profileData);
			// collect data value (variable)
			$profileData_values = array_values($profileData);

			// add string to fit positional placeholders >>> =?
			$profileData_columns_positional_placeholder_string = implode('=?, ', $profileData_columns) . '=?';
			$sql .= $profileData_columns_positional_placeholder_string;
			$sql .= ' WHERE ';
			// process id to fit positional placeholders
			$profileData_column_id_column = array_keys($update_profileData_id);
			// collect id value (variable)
			$update_profileData_id_value = array_values($update_profileData_id);
			$profileData_column_id_positional_placeholder_string = implode('', $profileData_column_id_column) . '=?';
			$sql .= $profileData_column_id_positional_placeholder_string;
			$stmt = $this->conn->prepare($sql);
			return $stmt->execute(array_merge($profileData_values, $update_profileData_id_value)) ? 'Updated Successfully' : 'Not Updated Successfully';

			/*
			// try to use named placeholders method instead of positional placeholders
			$profileData_columns = array_keys($profileData);
			$set = array();
			foreach($profileData_columns as $k){
				$set[] .= $k . '=:' . $k;
			}
			//var_dump($set);
			$namedPlaceholdersString = implode(', ', $set);
			$sql .= $namedPlaceholdersString;
			$sql .= ' WHERE id=:id';
			$query = $this->conn->prepare($sql);
			$query->execute(array_merge($profileData, $update_profileData_id));
			*/
		}
		return false;
	}

	// display profile data via id
	public function displayRecordById($tableName, $id){
		$sql = 'SELECT * FROM ' . $tableName . ' WHERE ';
		// positional placeholder
		$sql .=  'id =?';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([$id]);
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row;
		} else{
			return false;
		}
	}

	// delete profile data by id
	public function deleteDataById($tableName, $id){
		$sql = 'DELETE FROM ' . $tableName;
		$sql .= ' WHERE id';
		// positional placeholder
		$sql .= '=?';
		$stmt = $this->conn->prepare($sql);
		$result = $stmt->execute([$id]);
		$result ? header('Location:index.php?delete=ID:' .$id.' delete_success') : header('Location:index.php?delete=ID:' .$id.' delete_failed');
		return $result;
	}

	// validate input
	public function inputErrorCheck($profileData){
		if(!empty($profileData) && is_array($profileData)){

			// check first name (Name)
			if(array_key_exists('profile_fname', $profileData) && empty($profileData['profile_fname'])){
				return 'Name is required!';
			}else{
				if(!preg_match("/^[a-zA-Z]*$/", $profileData['profile_fname'])){
					return 'Name only can input valid character a to z, A to Z!';
				}
			}
			// check last name(surname)
			if(array_key_exists('profile_lname', $profileData) && empty($profileData['profile_lname'])){
				return 'Surname is required!';
			}else{
				if(!preg_match("/^[a-zA-Z]*$/", $profileData['profile_lname'])){
					return 'Surname only can input valid character a to z, A to Z!';
				}
			}
			// check phone(Mobile Number)
			if(array_key_exists('profile_phone', $profileData) && empty($profileData['profile_phone'])){
				return 'Mobile Number is required!';
			}else{
				if(!preg_match("/^[\d]{10}$/", $profileData['profile_phone'])){
					return 'Mobile Number only can input valid pattern 123-456-7890, and must be numbers!';
				}
			}

			// check email(Email)
			if(array_key_exists('profile_email', $profileData) && empty($profileData['profile_email'])){
				return 'Email is required!';
			}else{
				if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/", $profileData['profile_email'])){
					return 'Email does not match the correct format';
				}
			}
			// check email(Email)
			if(array_key_exists('profile_job_position', $profileData) && empty($profileData['profile_job_position'])){
				return 'Job Position is required!';
			}
			return true;
		}
		return 'No input data!';
	}

	// insert image name to database
	public function insertImgName($tableName){
		// count the number of files
		$countfiles = count($_FILES['imageFiles']['name']);
		$sql = 'INSERT INTO '  . $tableName . ' (profile_img_name,profile_img_path) VALUES (?,?)';
		// valid file extensions
		$valid_extension = array('png', 'jpeg', 'jpg');
		// prepare statement
		$stmt = $this->conn->prepare($sql);
		// loop through all the files
		for($i = 0; $i < $countfiles; $i++){
			// filename
			$filename = $_FILES['imageFiles']['name'][$i];
			// Location(path)
			$target_file = "./img/upload_image_file/".$filename;
			// File extension(.jpg, .png ...)
			$file_extension = pathinfo($target_file, PATHINFO_EXTENSION);
			$file_extension = strtolower($file_extension);
			// valid file types
			if(in_array($file_extension, $valid_extension)){
				// Upload file
				if(move_uploaded_file($_FILES['imageFiles']['tmp_name'][$i], $target_file)) {
					return $stmt->execute(array($filename,$target_file));
				}
			}
		}
		return false;
	}

	// display Record By Button Clicked
	public function displayRecordByButtonClicked($tableName, $limit, $start){
		$conditions = array('start'=>$start, 'limit'=>$limit);
		$tem_db = $this->displayData($tableName, $conditions);
		echo '<div class="collapse" id="profile-pages'. $start .'">';
		echo '<div class="card p-3 py-4">
					<div class="text-center">
						<img src="'. $tem_db[0]['profile_img_path'] .'" width="100" class="rounded-circle" alt="'. $tem_db[0]['profile_img_name'] .'">
					</div>
					<div class="text-center mt-3">
						<span class="bg-secondary p-1 px-4 rounded text-white">'
                            . "ID: " . $tem_db[0]['id'] .
                        '</span>
						<h5 class="mt-2 mb-3 profile_name_text">'
                            . $tem_db[0]['profile_lname'] . " " . $tem_db[0]['profile_fname'] .
                        '</h5>
						<span class="profile_job_position_text">'
                            . $tem_db[0]['profile_job_position'] .
                        '</span>
                        <div class="fs-4 mb-3 mt-3 container">
                            <div class="row">
                                <div class="col-md-1 offset-md-3">
                                    <!-- reserve a href link and button function -->
                                    <a href="#" class="text-decoration-none">
                                        <!-- resource: bootstrap Icons -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
                                            <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z"/>
                                            <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648Zm-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="col-md-auto a_icon_text">'
                                    . $tem_db[0]['profile_email'] .
                                '</div>
                            </div>
                        </div>
                        <div class="fs-4 mb-3 container">
                            <div class="row">
                                <div class="col-md-1 offset-md-3">
                                    <!-- reserve a href link and button function -->
                                    <a href="#" class="text-decoration-none">
                                        <!-- resource: bootstrap Icons -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="col-md-auto a_icon_text">'
	                                . $tem_db[0]['profile_phone'] .
                                '</div>
                            </div>
                        </div>
						<div class="px-4 mt-1" id="profile_bio_box">
							<p class="fonts profile_bio_text">'
                                . $tem_db[0]['profile_bio_text'] .
                            '</p>
						</div>
					</div>
				</div>';
		echo '</div>';
	}
}
?>