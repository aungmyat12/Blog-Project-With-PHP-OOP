<?php
require_once "inc/header.php";
if(!isset($_GET['slug'])) {
    Helper::redirect("404.php");
} else {
    $slug = $_GET['slug'];
    $article = Post::detail($slug);
}
?>
<div class="card card-dark">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <a class="nav-link btn d-inline-block mb-3 pa-4 btn-sm bg-secondary text-white btn-warning pa-2" href="index.php">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </a>
                <div class="card card-dark">
                    <div class="card-body">
                        <div class="row">
                            <!-- icons -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div
                                        class="col-md-4 text-center">
                                        <?php
                                            $user_id = User::auth() ? User::auth()->id : 0;
                                            $article_id = $article->id;
                                        ?>
                                        <i id="like" class="fa text-warning font-weight-normal" user_id="<?php echo $user_id ?>" article_id="<?php echo $article_id ?>">
                                        </i>
                                        <small id="like_count"
                                               class="text-muted"><?php echo $article->like_count ?></small>
                                    </div>
                                    <div
                                        class="col-md-4 text-center">
                                        <i
                                            class="far fa-comment text-dark"></i>
                                        <small
                                            class="text-muted" id="cmt_count"><?php echo $article->comment_count ?></small>
                                    </div>

                                </div>
                            </div>
                            <!-- Icons -->

                            <!-- Category -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div
                                        class="col-md-12">
                                        <a href=""
                                           class="badge badge-primary"><?php echo $article->category->name ?></a>

                                    </div>
                                </div>
                            </div>
                            <!-- Category -->


                            <!-- Category -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div
                                        class="col-md-12">
                                        <?php
                                            foreach($article->languages as $language) { ?>
                                                <a href=""
                                                   class="badge badge-success"><?php echo $language->name; ?>
                                                </a>
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Category -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="col-md-12">
            <h3><?php echo $article->title; ?></h3>
            <p>
                <?php echo $article->description; ?>
            </p>
        </div>

        <!-- Create Comments -->
        <div class="card card-dark">
            <div class="card-body">
                <form method="POST" action="" id="frmCmt">
                    <input type="text" id="comment" placeholder="Enter Comment" class="form-control">
                    <input type="submit" value="Create" class="btn btn-outline-warning float-right mt-3">
                </form>
            </div>
        </div>
        <!-- Comments -->
        <div class="card card-dark">
            <div class="card-header">
                <h4>Comments</h4>
            </div>
            <div class="card-body">
                <div id="comment_list">
                    <!-- Loop Comment -->
                    <?php
                    foreach ($article->comments as $comment) { ?>
                        <div class="card-dark mt-1">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-1">
                                        <img src="<?php echo DB::table("users")->where('id',$comment->user_id)->getOne()->image; ?>"
                                             style="width:50px;border-radius:50%"
                                             alt="">
                                    </div>
                                    <div
                                            class="col-md-4 d-flex align-items-center">
                                        <?php echo DB::table("users")->where('id',$comment->user_id)->getOne()->name; ?>
                                    </div>
                                </div>
                                <hr>
                                <p><?php echo $comment->comment; ?></p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "inc/footer.php";

?>

<script>
    let like = document.querySelector("#like");
    let article_id = like.getAttribute("article_id");
    // comment
    var frmCmt = document.getElementById("frmCmt");
    let cmt_count = document.getElementById("cmt_count");
    let commentList = document.getElementById("comment_list");
    frmCmt.addEventListener('submit',function (e) {
        e.preventDefault();
        var data = new FormData();
        data.append("comment",document.getElementById("comment").value);
        data.append("article_id",<?php echo $article->id; ?>)
        axios.post('api.php',data)
        .then(function (res) {
            commentList.innerHTML = res.data;
        })
        cmt_count.innerHTML = Number(cmt_count.innerHTML) + 1;
        console.log(cmt_count.innerHTML);
        document.getElementById("comment").value = "";
    })

    // like
    let like_count = document.getElementById("like_count");
    let user_id = like.getAttribute("user_id");
    like.innerHTML = "&#xf004";
    axios.get(`api.php?is_like&user_id=${user_id}&article_id=${article_id}`)
        .then(res => {
        if(res.data === "not") {
            like_count.classList.remove("like_color");
            like.classList.remove("full-heart");
        } else {
            like_count.classList.add("like_color");
            like.classList.add("full-heart");
        }
    })
    like.addEventListener("click",function () {
        if(user_id == 0) {
            location.href = "login.php";
            return;
        }
        axios.get(`api.php?like&user_id=${user_id}&article_id=${article_id}`)
        .then(res => {
            if(res.data === "already like") {
                toastr.warning("Like deleted");
                like_count.classList.remove("like_color");
                like.classList.remove("full-heart");
                like_count.innerHTML -= 1;
                return;
            }
            if(res.data >= 1) {
                like_count.classList.add("like_color");
                like.classList.add("full-heart");
                toastr.success("Liked Success");
                like_count.innerHTML = res.data;
            }
        })
    })
</script>
