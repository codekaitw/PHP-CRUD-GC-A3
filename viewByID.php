<?php
// add values of variables of title and description
$title = "A base profile view by id page";
$description = "Assignment 3 view member by id page";
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

// try search >>> get id
if(isset($_GET['viewID'])){
	if(preg_match("/^\d$/", $_GET['viewID'])) {
		$conditions = array(
			'where' => array('id' => $_GET['viewID'])
		);
		$view_profile_datas = $db->displayData($tableName, $conditions);
	} else{
		echo '<p class="text-center">ID Must Be Numbers.</p>';
	}
}

// try search
if(isset($_POST['submit_search'])){
	if(isset($_POST['search_content']) && !empty($_POST['search_content'])) {
		header("Location:viewByID.php?viewID=" . $_POST['search_content']);
	}else{
		echo '<p class="text-center">Search content is required</p>';
	}
}

?>
    <section class="container">
        <form class="form-inline d-flex justify-content-center mt-2" action="viewByID.php" method="post">
            <div class="form-group row">
                <div class="col-xs-2">
                    <input class="form-control col-xs-2 mr-sm-2" type="text" name="search_content" placeholder="View By ID" aria-label="Search">
                </div>
            </div>
            <button class="btn btn-outline-success" type="submit" name="submit_search">Search</button>
        </form>
    </section>

<?php
if(isset($view_profile_datas) && $view_profile_datas){
	foreach($view_profile_datas as $value) {
		// close php here in order to foreach(travel) these codes below
		// Note: the start and close position will affect the CSS
		?>
		<section class="container">
			<div class="container mt-5">
				<div class="row d-flex justify-content-center">
					<div class="col-md-7">
						<div class="card p-3 py-4">
							<div class="text-center">
								<img src="<?php $value['profile_img_path']; ?>" width="100" class="rounded-circle" alt="<?php $value['profile_img_name']; ?>">
							</div>
							<div class="text-center mt-3">
						<span class="bg-secondary p-1 px-4 rounded text-white">
                            <?php echo "ID: " . $value['id']; ?>
                        </span>
								<h5 class="mt-2 mb-3 profile_name_text">
									<?php echo $value['profile_lname'] . " " . $value['profile_fname']; ?>
								</h5>
								<span class="profile_job_position_text">
                            <?php echo $value['profile_job_position']; ?>
                        </span>
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
										<div class="col-md-auto a_icon_text">
											<?php echo $value['profile_email']; ?>
										</div>
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
										<div class="col-md-auto a_icon_text">
											<?php echo $value['profile_phone']; ?>
										</div>
									</div>
								</div>
								<div class="px-4 mt-1" id="profile_bio_box">
									<p class="fonts profile_bio_text">
										<?php echo $value['profile_bio_text']; ?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	} // foreach display data
} else{
	?>
	<!-- no profile data section -->
	<section class="container no-data-section">
		<h1 class="text-center">PHP Web Team Profile</h1>
		<h2 class="text-center">No Profile Data Found</h2>
	</section>
	<?php
}
?>
<?php
require './reuse_file/footer.php';
?>