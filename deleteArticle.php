<?php
require_once "core/autoload.php";
if(isset($_GET['slug'])) {
    $user_id = User::auth()->id;
    $user = DB::table("users")->where("id",$user_id)->getOne();
    $article = DB::table("articles")->where("slug",$_GET['slug'])->getOne();
    $art_lan = DB::table("article_language")->where("article_id",$article->id)->get();
    $art_comments = DB::table("article_comments")->where("article_id",$article->id)->get();
    $art_likes = DB::table("article_likes")->where("article_id",$article->id)->get();
    $delar = DB::delete("articles",$article->id);
    foreach ($art_lan as $al) {
        $delarLan = DB::delete("article_language",$al->id);
    }
    foreach ($art_likes as $al) {
        $delal = DB::delete("article_likes",$al->id);
    }
    foreach ($art_comments as $ac) {
        $delcmt = DB::delete("article_comments",$ac->id);
    }
    if($delar) {
        Helper::redirect("index.php?user=$user->slug");
        echo "success";
    }
} else {
    Helper::redirect("404.php");
}
