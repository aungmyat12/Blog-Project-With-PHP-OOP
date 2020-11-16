<?php

require_once "inc/header.php";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = Post::create($_POST);
//    print_r($post);
//    die();
    if(User::auth()) {
        $user_id = User::auth()->id;
        $user = DB::table("users")->where("id",$user_id)->getOne();
    }
}

?>
<div class="card card-dark">
    <div class="card-header">
        <h3>Create New Article</h3>
    </div>
    <div class="card-body">
        <?php
        if(isset($post) && is_array($post)) {
            foreach ($post as $p) { ?>
                <div class="alert alert-danger">
                    <?php echo $p ?>
                </div>
                <?php
            }
        }
        if(isset($post) && $post== "success"){
            Helper::redirect("index.php?user=$user->slug");
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="" class="text-white">Enter Title</label>
                <input type="text" name="title" class="form-control"
                       placeholder="Enter Title">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Choose Category</label>
                <select name="category_id" id="" class="form-control">
                    <?php
                        $cat = DB::table("category")->get();
                        foreach ($cat as $c) { ?>
                            <option value="<?php echo $c->id ?>"><?php echo $c->name ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <div class="form-check form-check-inline">
                <?php
                $lan = DB::table("languages")->get();
                foreach ($lan as $l) { ?>
                    <span class="mr-2">
                        <input class="form-check-input" id="<?php echo $l->name ?>" type="checkbox"
                               name="language[]" value="<?php echo $l->id ?>">
                        <label class="form-check-label"
                               for="<?php echo $l->name ?>"><?php echo $l->name ?></label>
                    </span>
                    <?php
                }
                ?>
            </div>
            <br><br>
            <div class="form-group">
                <label for="">Choose Image</label>
                <input type="file" class="form-control" name="image">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Articles</label>
                <textarea name="description" class="form-control" id=""
                          cols="30" rows="10"></textarea>
            </div>
            <input type="submit" value="Create"
                   class="btn  btn-outline-warning">
        </form>
    </div>
</div>


<?php
require_once "inc/footer.php";
?>

