<?php
    // session is started if you don't write this line can't use $_Session  global variable
    // In this session, it will limit the amount of time users are allowed to view this page
    session_start();
	// add values of variables of title and description
	$title = "A base profile add profile page";
	$description = "Assignment 3 create profile page";
	// add header template, require >>> if fail, stop application. include >>> if fail, keep go throw application
	require './reuse_file/header.php';

require_once './reuse_file/database.php';

    if(isset($_REQUEST['action_type'])){
        $db = new database();
    }
    $tableName  = 'Profile';
    if(isset($_REQUEST['action_type']) && !empty($_REQUEST['action_type'])){
        if($_REQUEST['action_type'] == 'add'){
            $profileData = array(
              'profile_fname'          => $_POST['first_name'],
              'profile_lname'          => $_POST['surname'],
              'profile_phone'          => $_POST['phone'],
              'profile_email'          => $_POST['email'],
              'profile_job_position'   => $_POST['job_position'],
              'profile_bio_text'       => $_POST['bio_text']
            );
            $inputValidationStatus = $db->inputErrorCheck($profileData);
            if(is_bool($inputValidationStatus) && $inputValidationStatus == true) {
	            $insert = $db->insertData($tableName, $profileData);
	            $_SESSION['insertMsg'] = $insert ? 'Inserted Successfully' : 'Not Inserted Successfully';
	            header('Location:index.php');
            }elseif(is_string($inputValidationStatus)){
                header("Location:addProfile.php?validationInput=" . $inputValidationStatus);
            }
        }
    }

    // try to upload image, so i separate it
    if(isset($_POST['upload_image'])){
	    $db = new database();
        if($_POST['upload_image'] == 'Upload_Image'){
            $insertImgStatus = $db->insertImgName($tableName);
            if($insertImgStatus){
                header("Location:addProfile.php?insertImgMsg=Image insert success!");
            } else {
	            header("Location:addProfile.php?insertImgMsg=Image insert fail!");
            }
        }
    }
    // display insert image message
    if(isset($_GET['insertImgMsg'])) {
	    echo '<section class="container mt-2">';
	    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
	    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button> ";
	    echo $_GET['insertImgMsg'];
	    echo "</div>";
	    echo '</section>';
    }
?>
<section class="container">
    <?php
    if(isset($_GET['validationInput'])){
	    echo "<div class='alert alert-success alert-dismissible fade show mt-5' role='alert'>";
	    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button> ";
        echo $_GET['validationInput'];
        echo '</div>';
    }
    ?>
    <h1 class="text-center">Create A New Profile</h1>
    <form action="addProfile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group container mt-5">
            <div class="card">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="d-flex flex-column p-3 py-1" id="profile_img_area">
                            <img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg" alt="php image">
                            <input class="form-control" type="file" name="imageFiles[]" multiple />
                            <button class="btn btn-primary" type="submit" name="upload_image" value="Upload_Image">Try Upload Image</button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-5 py-5">
                            <div class="mb-4">
                                <h4 class="text-center">Profile Settings</h4>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6"><label class="labels">Name</label>
                                    <input type="text" class="form-control" placeholder="first name" name="first_name">
                                </div>
                                <div class="col-md-6"><label class="labels">Surname</label>
                                    <input type="text" class="form-control" placeholder="surname" name="surname">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <label class="labels">Mobile Number</label>
                                    <input type="tel" class="form-control" placeholder="phone number" name="phone">
                                </div>
                                <div class="col-md-12 mb-3"><label class="labels">Email</label>
                                    <input type="text" class="form-control" placeholder="email" name="email">
                                </div>
                                <div class="col-md-12 mb-3"><label class="labels">Job Position</label>
                                    <input type="text" class="form-control" placeholder="job position" name="job_position">
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Bio</label>
                                    <textarea class="form-control" id="bioTextArea" rows="3" placeholder="Describe Bio here..." name="bio_text"></textarea>
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button class="btn btn-primary profile-button" type="submit" value="add" name="action_type">Save Profile</button>
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