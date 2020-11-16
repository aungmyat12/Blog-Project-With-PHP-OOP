<?php
    require_once "inc/header.php";
    if(isset($_GET['category'])) {
        $slug = $_GET['category'];
        $post = Post::articleByCategory($slug);
    } elseif(isset($_GET['language'])) {
        $slug = $_GET['language'];
        $post = Post::articleByLanguage($slug);
    } elseif(isset($_GET['search'])) {
        $search = $_GET['search'];
        $post = Post::search($search);
    } elseif(isset($_GET['user'])) {
        $post = Post::articleByUser($_GET['user']);
    } else {
        $post = Post::all();
    }

?>

    <div class="card card-dark">
        <div class="card-body">
            <a href="<?php echo $post['prev_page']; ?>" class="btn btn-danger">Prev Posts</a>
            <a href="<?php echo $post['next_page']; ?>" class="btn btn-danger float-right">Next Posts</a>
        </div>
    </div>
    <div class="card card-dark">
        <div class="card-body">
            <div class="row">
                <!-- Loop this -->
                <?php
                if(!$post['total']) { ?>
                    <div class="w-100">
                        <h3 class="text-center text-danger">There is no articles</h3>
                    </div>
                    <?php
                } else {
                    foreach ($post['data'] as $article) { ?>
                        <div class="col-md-4 mt-2">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top" height="150px" style="object-fit: cover"
                                     src="<?php echo $article->image; ?>"
                                     alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="text-dark"><?php echo $article->title; ?></h5>
                                </div>
                                <div class="card-footer">
                                    <div class="row justify-content-between px-3">
                                        <?php
                                        $user_id = User::auth() ? User::auth()->id : 0;
                                        $article_id = $article->id;
                                        ?>
                                        <div
                                                class="text-center like" style="cursor: pointer;" user_id="<?php echo $user_id; ?>" article_id="<?php echo $article_id; ?>">
                                            <i
                                                    class="fas fa-heart text-warning font-weight-normal heart">
                                            </i>
                                            <small
                                                    class="text-muted" id="like_count"><?php echo $article->like_count; ?></small>
                                        </div>
                                        <div
                                                class="text-center">
                                            <i
                                                    class="far fa-comment text-dark"></i>
                                            <small
                                                    class="text-muted"><?php echo $article->comment_count; ?></small>
                                        </div>
                                        <div
                                                class="text-center">
                                            <a href="<?php echo "detail.php?slug=$article->slug" ?>"
                                               class="badge badge-success p-1">View</a>
                                            <?php
                                            if(User::auth()) {
                                                $user_id = User::auth()->id;
                                                $my_article = DB::table("articles")->where("user_id",$user_id)->get();
                                                foreach ($my_article as $myar) {
                                                    if($myar->id == $article->id) { ?>
                                                        <a href="<?php echo "editArticle.php?slug=$article->slug" ?>"
                                                           class="badge badge-warning p-1 no-wrap">Edit</a>
                                                        <a href="<?php echo "deleteArticle.php?slug=$myar->slug" ?>"
                                                           class="badge badge-danger p-1 no-wrap">Delete</a>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
require_once "inc/footer.php";

?>

<script>
    let like = document.querySelectorAll(".like");
    // var user_id,article_id,like_count;
    like.forEach(l => {
        // console.log(l);
        let like_count = l.querySelector("#like_count");
        let user_id = l.getAttribute("user_id");
        let heart = l.querySelector(".heart");
        let article_id = l.getAttribute("article_id");
        l.addEventListener("click",function () {
            if(user_id == 0) {
                location.href = "login.php";
                return;
            }
            axios.get(`api.php?like&user_id=${user_id}&article_id=${article_id}`)
                .then(res => {
                    if(res.data === "already like") {
                        toastr.warning("Like deleted");
                        like_count.classList.remove("like_color");
                        heart.classList.remove("full-heart");
                        like_count.innerHTML -= 1;
                        return;
                    }
                    if(res.data >= 1) {
                        like_count.classList.add("like_color");
                        heart.classList.add("full-heart");
                        toastr.success("Liked Success");
                        like_count.innerHTML = res.data;
                    }
                })
        })
        axios.get(`api.php?is_like&user_id=${user_id}&article_id=${article_id}`)
            .then(res => {
                if(res.data === "not") {
                    like_count.classList.remove("like_color");
                    heart.classList.remove("full-heart");
                } else {
                    like_count.classList.add("like_color");
                    heart.classList.add("full-heart");
                }
            })

        ;
    })




</script>
