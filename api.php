<?php
require_once "core/autoload.php";
$request = $_GET;
if(isset($request['like'])) {
    $user_id = $request['user_id'];
    $article_id = $request['article_id'];
    $u = DB::table("article_likes")->where('user_id',$user_id)->andWhere("article_id",$article_id)->getOne();
    if($u) {
       $user = Post::delete("article_likes",$user_id,$article_id);
//       if($user) {
//           $count = DB::table("article_likes")->where('article_id',$article_id)->count();
//       }
        $count = "already like";
    } else {
        $user = DB::create('article_likes',[
            "user_id"=>$user_id,
            'article_id' => $article_id
        ]);
        if($user) {
            $count = DB::table("article_likes")->where('article_id',$article_id)->count();
        }
    }
    echo $count;
}
if(isset($request['is_like'])) {
    $user_id = $request['user_id'];
    $article_id = $request['article_id'];
    $u = DB::table("article_likes")->where('user_id',$user_id)->andWhere("article_id",$article_id)->getOne();
    if($u) {
        $count = "liked";
    } else {
        $count = "not";
    }
    echo $count;
}

if(isset($_POST['comment'])) {
    $user_id = User::auth()->id;
    $article_id = $_POST['article_id'];
    $comment = $_POST['comment'];
    DB::create("article_comments",[
       'user_id' => $user_id,
       "article_id" => $article_id,
       "comment" => $comment
    ]);
    if($comment) {
        $cmt = DB::table("article_comments")->where("article_id",$article_id)->orderBy("id","DESC")->get();
        $html = '';
        foreach ($cmt as $c) {
            $user = DB::table("users")->where("id",$c->user_id)->getOne();
            $html .= "
                <div class='card-dark mt-1'>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-md-1'>
                                <img src='{$user->image}'
                                     style='width:50px;border-radius:50%'
                                     alt=''>
                            </div>
                            <div
                                    class='col-md-4 d-flex align-items-center'>
                                {$user->name}
                            </div>
                        </div>
                        <hr>
                        <p>{$c->comment}</p>
                    </div>
                </div>
            ";
        }
        echo $html;
    }
}

if(isset($_POST['language'])) {
    $error = [];
    if($_POST['language'] === '') {
        $error[] = "Language field is required";
    }
    if(count($error)) {
        echo json_encode($error);
    } else {
        $language = DB::create("languages",[
            "slug" => Helper::slug($_POST['language']),
            "name" => $_POST['language']
        ]);
        if($language) {
            echo json_encode($language);
        }
    }
//    echo json_encode($_POST);
}
if(isset($_POST['category'])) {
    $error = [];
    if($_POST['category'] === '') {
        $error[] = "Language field is required";
    }
    if(count($error)) {
        echo json_encode($error);
    } else {
        $category = DB::create("category",[
            "slug" => Helper::slug($_POST['category']),
            "name" => $_POST['category']
        ]);
        echo json_encode($category);
    }
}

if(isset($_GET["category_list"])) {
    showList("category","editCate","delCate");
}

if(isset($_GET["language_list"])) {
    showList("languages","editLan","delLan");
}

function showList($table,$editName,$delName) {
    $row = DB::table("$table")->get();
    $html = '';
    $id = 1;
    $html .= "
        <div class=\"card card-dark\">
            <div class=\"card-body\">
                <table class=\"table table-hover\">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Control</th>
                        </tr>
                    </thead>
                    <tbody class=\"output\">
    ";
    if(!$row) {
        $html .= "
            <tr>
                <td class='text-center text-warning' colspan='3'>There is no row</td>
            </tr>
        ";
    } else {
        foreach ($row as $r) {
            $html .= "
        <tr>
            <td>{$id}</td>
            <td>{$r->name}</td>
            <td>
                <button class='btn btn-outline-info mr-4 $editName' data-id=\"{$r->id}\"><i class='fas fa-pencil-alt'></i> Edit</button>
                <button class='btn btn-outline-warning $delName' data-id=\"{$r->id}\"><i class=\"fas fa-pencil-alt\"></i> Delete</button>
            </td>
        </tr>
    ";
            $id++;
        }
    }
    $html .= "
                </tbody>
                </table>
            </div>
        </div>";

    echo $html;
}

if(isset($_GET['delete_category'])) {
    $article = DB::table("articles")->where("category_id",$_GET['delete_category'])->get();
    foreach ($article as $a) {
        $art_lan = DB::table("article_language")->where("article_id",$a->id)->get();
        $art_comments = DB::table("article_comments")->where("article_id",$a->id)->get();
        $art_likes = DB::table("article_likes")->where("article_id",$a->id)->get();
        foreach ($art_lan as $alan) {
            $del_arLan = DB::delete("article_language",$alan->id);
        }
        foreach ($art_likes as $al) {
            $del_al = DB::delete("article_likes",$al->id);
        }
        foreach ($art_comments as $ac) {
            $del_cmt = DB::delete("article_comments",$ac->id);
        }
    }
    foreach ($article as $a) {
        $del_art = DB::delete("articles",$a->id);
    }
    $result = DB::delete("category",$_GET['delete_category']);
    if($result) {
        echo "success";
    }
//    echo json_encode($article);
//    echo json_encode($art_comments);
//    echo json_encode($art_likes);
}

if(isset($_GET['delete_language'])) {
    $delId = $_GET['delete_language'];
    $article_id = DB::raw("SELECT article_id FROM article_language RIGHT JOIN articles on articles.id=article_language.article_id WHERE article_language.language_id={$_GET['delete_language']}")->get();
    foreach ($article_id as $id) {
        $article_lanId = DB::table("article_language")->where("language_id",$_GET['delete_language'])->get();
    }
    $article_count = DB::table("article_language")->where("language_id",$_GET['delete_language'])->count();
    if($article_count === 1) {
        echo json_encode($article_lanId);
        foreach ($article_lanId as $arlanId) {
            DB::raw("DELETE FROM articles")->where('id',$arlanId->article_id)->get();
            DB::raw("DELETE FROM article_likes")->where('id',$arlanId->article_id)->get();
            DB::raw("DELETE FROM article_comments")->where('id',$arlanId->article_id)->get();
            DB::raw("DELETE FROM article_language")->where("language_id",$delId)->get();
        }

    } else {
        $del_arLan = DB::raw("DELETE FROM article_language")->where("language_id",$delId)->get();
    }
    $result = DB::delete("languages",$_GET['delete_language']);
}

if(isset($_GET["edit_category"])) {
    $currentId = $_GET["edit_category"];
    $result = DB::table("category")->where("id",$currentId)->getOne();
    if($result) {
        echo json_encode($result);
    }
}

if(isset($_GET["edit_language"])) {
    $currentId = $_GET["edit_language"];
    $result = DB::table("languages")->where("id",$currentId)->getOne();
    if($result) {
        echo json_encode($result);
    }
}

if(isset($_GET['all_language'])) {
    $language = DB::raw("SELECT *,(SELECT COUNT(id) FROM article_language WHERE article_language.language_id=languages.id) as article_count FROM languages")->get();
    $html = '';
    foreach ($language as $l) {
        $html .= "
            <a href='index.php?language={$l->slug}'>
                <li
                    class='list-group-item d-flex justify-content-between align-items-center'>
                    {$l->name}
                    <span class='badge badge-primary badge-pill'>{$l->article_count}</span>
                </li>
            </a>
        ";
    }
    echo $html;
}
if(isset($_POST['updateCategory']) && isset($_POST['updateId'])) {
    $result = DB::update("category",[
        "slug" => Helper::slug($_POST['updateCategory']),
        "name" => $_POST['updateCategory']
    ],$_POST['updateId']);
    echo "success";
}

if(isset($_POST['updateLanguage']) && isset($_POST['updateId'])) {
    $result = DB::update("languages",[
        "slug" => Helper::slug($_POST['updateLanguage']),
        "name" => $_POST['updateLanguage']
    ],$_POST['updateId']);
    echo "success";
}
//echo json_encode($_POST);
if(isset($_GET['all_category'])) {
    $html = '';
    $category = DB::raw("SELECT *,(SELECT COUNT(id) FROM articles WHERE articles.category_id=category.id) as article_count FROM category")->get();
    foreach ($category as $c) {
        $html .= "
            <a href='index.php?category={$c->slug}'>
                <li class='list-group-item d-flex justify-content-between align-items-center'>
                    {$c->name}
                    <span class='badge badge-primary badge-pill'>
                    {$c->article_count}
                </span>
                </li>
            </a>
        ";
    }
    echo $html;
}

if(isset($_GET['user_auth'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        echo "success";
    } else {
        echo "fail";
    }
}
