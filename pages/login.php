<?php include_once('../assets/header.php'); ?>
<?php include_once('../assets/navbar.php'); ?>

<?php


if (isset($_POST['loginBtn'])) {

  $db = new Database;
  // Check if POST
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
      'user_name' => trim($_POST['user_name']),
      'user_pass' => trim($_POST['user_pass']),
      'user_name_error' => '',
      'user_pass_error' => ''
    ];

    if (empty($data['user_name'])) {
      $data['user_name_error'] = 'Insert Your User Name!';
    }

    if (empty($data['user_pass'])) {
      $data['user_pass_error'] = 'Insert Your Password!';
    }
  } else {

    $data = [
      'user_name' => '',
      'user_pass' => '',
      'user_name_error' => '',
      'user_pass_error' => ''
    ];
  }

  //Make sure errors are empty
  if (empty($data['user_name_error']) && empty($data['user_pass_error'])) {
    //Validate
    $username = $data['user_name'];
    $password = $data['user_pass'];

    $db->query("SELECT * FROM users WHERE username = :username");
    $db->bind(':username', $username);
    $db->execute();
    $row = $db->single();

    $user = new User($db);

    $ckusername = $user->checkUsernameIfInDB($username);
    $user_id = $row['id'];
    if ($ckusername == false) {
      $data['user_name_error'] = "Wrong username";
    }

    if (password_verify($password, $row['password'])) {

      session_start();
      //Assign session variables
      $_SESSION['user_name'] = $username;
      $_SESSION['user_pass'] = $password;
      $_SESSION['user_id'] = $user_id;
      //$_SESSION['logged_in']  = 1;

      header('Location: home.php');
    } else {

      $data['user_pass_error'] = "Wrong password";
    }
  }
}
?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <!--Card-->
      <div class="card mt-5">
        <!--Card content-->
        <div class="card-body">
          <!--Title-->
          <h4 class="card-title text-center">MyTaske Login</h4>
          <!--Text-->

          <!-- Default form login -->
          <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

            <!-- Default input email -->
            <i class="fa fa-user prefix grey-text"></i>
            <label for="defaultFormLogin" class="grey-text">Your username</label>
            <input type="text" name="user_name" class="form-control form-control-sm <?php echo (!empty($data['user_name_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST['user_name']) ? $_POST['user_name'] : '' ?>">
            <span class="invalid-feedback"><?php echo $data['user_name_error']; ?></span>

            <br>

            <!-- Default input password -->
            <i class="fa fa-lock prefix grey-text"></i>
            <label for="defaultFormLoginPasswordEx" class="grey-text">Your password</label>
            <input type="password" name="user_pass" class="form-control form-control-sm <?php echo (!empty($data['user_pass_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST['user_pass']) ? $_POST['user_pass'] : '' ?>">
            <span class="invalid-feedback"><?php echo $data['user_pass_error']; ?></span>

            <div class="text-center mt-4">
              <button class="btn btn-default btn-block" name="loginBtn" type="submit">Login</button>
            </div>
          </form>

          <div class="mt-2">
            <a class="btn btn-light-green btn-block btn-sm" href="<?php echo URLROOT ?>/pages/register.php">Register</a>
          </div>
        </div>
      </div>
      <!--/.Card-->
    </div>
  </div>
</div>
<!--LOGIN FORM-->

<?php include_once('../assets/footer.php'); ?>