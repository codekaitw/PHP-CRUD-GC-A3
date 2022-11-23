<?php
    // session is started if you don't write this line can't use $_Session  global variable
    // In this session, it will limit the amount of time users are allowed to view this page
    session_start();
	// add values of variables of title and description
	$title = "A base profile homepage";
    $description = "Assignment 3 index page";
	// add header template, require >>> if fail, stop application. include >>> if fail, keep go throw application
	require './reuse_file/header.php';
    require_once './reuse_file/database.php';

$tableName  = 'Profile';

if(!isset($db)){
	$db = new database();

	$_SESSION['view_db_data'] = $db->displayData($tableName);

}
if(!isset($_SESSION['view_db_data'])){
	$db = null;
	header('Location:index.php');
}


?>
	<main class="container">
		<section class="container">
            <h1 class="index-h1">Welcome to our PHP Web Team</h1>
            <div class="container img-fluid index-image-area">
            </div>
		</section>
        <section class="container">
            <?php
            if(isset($_SESSION['insertMsg']) || isset($_SESSION['updateMsg_invalidId']) || isset($_SESSION['update_timeout']) || isset($_GET['update']) || isset($_GET['delete'])){
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
                echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button> ";
                if(isset($_SESSION['insertMsg'])) {
	                echo $_SESSION['insertMsg'];
                }elseif (isset($_SESSION['updateMsg_invalidId'])){
                    echo $_SESSION['updateMsg_invalidId'];
                }elseif (isset($_SESSION['update_timeout'])){
                    echo $_SESSION['update_timeout'];
                }elseif (isset($_GET['update'])){
	                echo $_GET['update'];
                }elseif (isset($_GET['delete'])){
	                echo $_GET['delete'];
                }
                echo "</div>";
                // set to null let the msg not always display, I think it's not a good way to use this method to show msg
                $_SESSION['insertMsg'] = null;
                $_SESSION['updateMsg_invalidId'] = null;
	            $_SESSION['update_timeout'] = null;
            }
            ?>

        </section>
		<section class="container">
			<div class="index-h2-area">
				<h2>View Our Team Members</h2>
			</div>
            <div class="d-flex justify-content-center">
	            <?php
	            $view_profile_datas = $db->displayData($tableName); ?>
                <button type="button" class="btn btn-primary">
                    Profile <span class="badge badge-info">
                        <?php
                        if(is_bool($view_profile_datas)){
                            echo '0';
                        } else {
	                        echo count($view_profile_datas);
                        }
                        ?>
                    </span>
                </button>
            </div>
                    <?php
	                if(isset($view_profile_datas) && $view_profile_datas) {
                        echo "<div class='container profile-page-toolbar'>";
		                echo '<div class="btn-group" role="group">';
		                for ($i = 0; $i < count($view_profile_datas); $i++) {
			                if ($i < 9) {
				                echo '<button type="button" class="btn btn-secondary" data-toggle="collapse" aria-expanded="false" data-target="#profile-pages' . ($i) . '">' . ($i + 1) . '</button>';
			                } elseif ($i == 9) {
				                echo '<div class="btn-group">
                                        <button class="btn btn-secondary" type="button">
                                        More...
                                        </button>
                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu">';
				                echo '<a class="dropdown-item" href="#profile-pages' . ($i) . '"><button type="button" class="btn btn-secondary" data-toggle="collapse" aria-expanded="false" data-target="#profile-pages' . ($i) . '">' . ($i + 1) . '</button></a>';
			                } else {
				                echo '<a class="dropdown-item" href="#profile-pages' . ($i) . '"><button type="button" class="btn btn-secondary" data-toggle="collapse" aria-expanded="false" data-target="#profile-pages' . ($i) . '">' . ($i + 1) . '</button></a>';
			                }
		                } // end of for loop
                        if(count($view_profile_datas) >= 9){
                            echo '</div>';
	                        echo '</div>';
                        }
		                echo '</div>';
		                echo '</div>';
		                for ($i = 0; $i < count($view_profile_datas); $i++) {
			                $db->displayRecordByButtonClicked($tableName, 1, ($i));
		                }

	                } // end of if statement
                    ?>
		</section>
	</main>
<?php
	// add footer template
	require './reuse_file/footer.php';
?>