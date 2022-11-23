<?php
// add values of variables of title and description
$title = "A base profile edit profile page";
$description = "Assignment 3 update profile page";
// add header template, require >>> if fail, stop application. include >>> if fail, keep go throw application
require './reuse_file/header.php';

require_once './reuse_file/database.php';

// session is started if you don't write this line can't use $_Session  global variable
// In this session, it will limit the amount of time users are allowed to update this page
session_start();

$tableName  = 'Profile';
// Edit a profile data
// create an DB object if DB connection isn't exist
if (!isset($db)) {
	$db = new database();
}

// some explanation about $_GET and undefined array key, why need use isset($_GET['array key']) or empty($GET_['array key'])
// source: https://stackoverflow.com/questions/4261133/notice-undefined-variable-notice-undefined-index-warning-undefined-arr
if(isset($_GET['updateId']) && !empty($_GET['updateId'])) {
    $recordById = $db->displayRecordById($tableName, $_GET['updateId']);
    $_SESSION['update_db_data'] = $recordById;

    // send the msg if record not found
    if (!$recordById) {
        $db = null;
        $_SESSION['updateMsg_invalidId'] = 'Invalid id. No record found';
        header('Location:index.php');
    }
}
// update timeout
if(!isset($_SESSION['update_db_data'])){
	$db = null;
    $_SESSION['update_timeout'] = 'Update Timeout';
	header('Location:index.php');
}
if(isset($_REQUEST['action_type']) && !empty($_REQUEST['action_type'])){
	if($_REQUEST['action_type'] == 'update'){
		$update_profileData = array(
			'profile_fname'          => $_POST['update_first_name'],
			'profile_lname'          => $_POST['update_surname'],
			'profile_phone'          => $_POST['update_phone'],
			'profile_email'          => $_POST['update_email'],
			'profile_job_position'   => $_POST['update_job_position'],
			'profile_bio_text'       => $_POST['update_bio_text']
		);
		$update_profileData_id = array(
			'id' => $_POST['update_id']
		);
		$update = $db->updateData($tableName, $update_profileData, $update_profileData_id);
		header("Location:index.php?update=$update");
	}

}
?>
	<section class="container">
		<h1 class="text-center">Edit A Profile</h1>
		<form action="updateProfile.php" method="POST">
			<div class="form-group container mt-5">
				<div class="card">
                    <h2 class="d-flex justify-content-center">EDIT</h2>
					<div class="form-group">
						<div class="col-md-12">
							<div class="d-flex flex-column p-3 py-1" id="profile_img_area">
								<img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
								<span class="font-weight-bold p-3"><?php echo $recordById['profile_fname'] . ' ' . $recordById['profile_lname']; ?></span>
                                <span class="bg-secondary p-1 px-4 rounded text-white"><?php echo 'ID: ' . $recordById['id']; ?></span>
							</div>
						</div>
						<div class="col-md-12">
							<div class="p-5 py-5">
								<div class="mb-4">
									<h4 class="text-center">Profile Edit</h4>
								</div>
								<div class="row mt-2">
									<div class="col-md-6"><label class="labels">Name</label>
										<input type="text" class="form-control" placeholder="first name" value="<?php echo $recordById['profile_fname']; ?>" name="update_first_name">
									</div>
									<div class="col-md-6"><label class="labels">Surname</label>
										<input type="text" class="form-control" placeholder="surname" value="<?php echo $recordById['profile_lname']; ?>" name="update_surname">
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-md-12 mb-3">
										<label class="labels">Mobile Number</label>
										<input type="tel" class="form-control" placeholder="phone number" value="<?php echo $recordById['profile_phone']; ?>" name="update_phone">
									</div>
									<div class="col-md-12 mb-3"><label class="labels">Email</label>
										<input type="text" class="form-control" placeholder="email" value="<?php echo $recordById['profile_email']; ?>" name="update_email">
									</div>
									<div class="col-md-12 mb-3"><label class="labels">Job Position</label>
										<input type="text" class="form-control" placeholder="job position" value="<?php echo $recordById['profile_job_position']; ?>" name="update_job_position">
									</div>
									<div class="form-group">
										<label for="exampleFormControlTextarea1">Bio</label>
										<textarea class="form-control" id="bioTextArea" rows="3" placeholder="Describe Bio here..." name="update_bio_text"><?php echo $recordById['profile_bio_text']; ?></textarea>
									</div>
								</div>
								<div class="mt-5 text-center">
                                    <input type="hidden" name="update_id" value="<?php echo $recordById['id']; ?>">
									<button class="btn btn-primary profile-button" type="submit" value="update" name="action_type">Update Profile</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
<?php
require './reuse_file/footer.php';
?>