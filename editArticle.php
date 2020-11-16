<?php

require_once "inc/header.php";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postUpdate = Post::update($_POST);
//    echo "<pre>";
//    print_r($postUpdate);
//    die();
}
if(isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $post = Post::detail($slug);
    $user = DB::table("users")->where('slug',$slug)->getOne();
} else {
    Helper::redirect('404.php');
}

?>
<div class="card card-dark">
    <div class="card-header">
        <h3>Create New Article</h3>
    </div>
    <div class="card-body">
        <?php
        if(isset($postUpdate) && is_array($postUpdate)) {
            foreach ($postUpdate as $p) { ?>
                <div class="alert alert-danger">
                    <?php echo $p ?>
                </div>
                <?php
            }
        }
        if(isset($postUpdate) && $postUpdate == "success"){
            Helper::redirect("index.php?user=$user->slug");
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden"  name="article_slug" value="<?php echo $post->slug; ?>">
            <div class="form-group">
                <label for="" class="text-white">Enter Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $post->title; ?>"
                       placeholder="Enter Title">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Choose Category</label>
                <select name="category_id" id="" class="form-control">
                    <?php
                    $cat = DB::table("category")->get();
                    foreach ($cat as $c) { ?>
                        <option value="<?php echo $c->id ?>" <?php echo $c->id === $post->category->id ? 'selected' : '' ?> ><?php echo $c->name ?></option>
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
                               name="language[]" value="<?php echo $l->id ?>" <?php
                            foreach ($post->languages as $pl) {
                               echo $pl->id === $l->id ? "checked" : '';
                            }
                        ?>>
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
                <img src="<?php echo $post->image ?>" width="200px" alt="">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Articles</label>
                <textarea name="description" class="form-control" id=""
                          cols="30" rows="10"><?php echo $post->description ?></textarea>
            </div>
            <input type="submit" value="Update"
                   class="btn  btn-outline-warning">
        </form>
    </div>
</div>


<?php
require_once "inc/footer.php";
?>

