<?php
require_once "inc/header.php";
if(User::auth()) {
    Helper::redirect("index.php");
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $user = $user->register($_POST);
    if($user == "success") {
        echo "Success";
    }
}
?>
<div class="card card-dark">
    <div class="card-header bg-warning">
        <h3>Register</h3>
    </div>
    <div class="card-body">
        <?php
        if(isset($user) && is_array($user)) {
            foreach ($user as $u) { ?>
                <div class="alert alert-danger">
                    <?php echo $u ?>
                </div>
                <?php
            }
        }
        ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="" class="text-white">Enter Username</label>
                <input type="text" name="name" class="form-control"
                       placeholder="enter username">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="enter username">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="enter username">
            </div>
            <input type="submit" value="Register"
                   class="btn  btn-outline-warning">
        </form>
    </div>
</div>
<?php
require_once "inc/footer.php";

?>
