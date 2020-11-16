<?php
require_once "inc/header.php";
if(isset($_GET['user'])) {
    $slug = $_GET['user'];
    $user = DB::table("users")->where('slug',$slug)->getOne();
    if(!$user) {
        Helper::redirect('404.php');
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = User::update($_POST);
        Helper::redirect("edit.php?user=" . $slug);

    }
} else {
    Helper::redirect('404.php');
}
?>
<div class="card card-dark">
    <div class="card-header bg-warning">
        <h3>Edit User</h3>
    </div>
    <div class="card-body">
        <?php
        if(isset($user) && $user== "success"){ ?>
        <div class="alert alert-success">
            Article created successfully
        </div>
        <?php
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="user_slug" value="<?php echo $user->slug ?>">
            <div class="form-group">
                <label for="" class="text-white">Enter Username</label>
                <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control"
                       placeholder="enter username">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Email</label>
                <input type="email" name="email" value="<?php echo $user->email; ?>" class="form-control"
                       placeholder="enter username">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="enter password">
            </div>
            <div class="form-group">
                <label for="">Choose Image</label>
                <input type="file" name="image" class="form-control">
                <img src="<?php echo $user->image; ?>" style="width: 200px;border-radius: 20px" alt="">
            </div>
            <input type="submit" value="Update"
                   class="btn  btn-outline-warning">
        </form>
    </div>
</div>
<?php
require_once "inc/footer.php";

?>
