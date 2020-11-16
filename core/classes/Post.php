<?php

class Post
{

    public static function all() {
        $data = DB::table("articles")->orderBy("id","DESC")->paginate(6);
        foreach ($data['data'] as $k=>$d) {
            $d->comment_count = DB::table("article_comments")->where('article_id',$d->id)->count();
            $d->like_count = DB::table("article_likes")->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function articleByUser($slug) {
        $user_id = User::auth()->id;
        $data = DB::table("articles")->where("user_id",$user_id)->orderBy("id","DESC")->paginate(6,"user=$slug");
        foreach ($data['data'] as $k=>$d) {
            $d->comment_count = DB::table("article_comments")->where('article_id',$d->id)->count();
            $d->like_count = DB::table("article_likes")->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function detail($slug) {
        $data = DB::table("articles")->where("slug",$slug)->getOne();

        // try to get languages
        $data->languages = DB::raw("SELECT languages.id,languages.slug,languages.name FROM article_language LEFT JOIN languages on languages.id = article_language.language_id WHERE article_id={$data->id}")->get();
        // try to get comments
        $data->comments = DB::table("article_comments")->where("article_id",$data->id)->orderBy("id","DESC")->get();
        // try to get category
        $data->category = DB::table("category")->where("id",$data->category_id)->getOne();
        // try to get like_count
        $data->like_count = DB::table("article_likes")->where("article_id",$data->id)->count();
        // try to get comment_count
        $data->comment_count = DB::table("article_comments")->where("article_id",$data->id)->count();
        return $data;
    }

    public static function delete($table,$user_id,$article_id) {
        $dbh = new PDO("mysql:host=localhost;dbname=php_course","root","");
        $sql = "DELETE FROM $table WHERE user_id=? AND article_id=?";
        $res = $dbh->prepare($sql);
        $res->execute([$user_id,$article_id]);
        return true;
    }

    public static function articleByCategory($slug) {
        $category_id = DB::table("category")->where("slug",$slug)->getOne()->id;
        $data = DB::table("articles")->where("category_id",$category_id)->orderBy("id","DESC")->paginate(6,"category=$slug");
        foreach ($data['data'] as $k=>$d) {
            $d->comment_count = DB::table("article_comments")->where('article_id',$d->id)->count();
            $d->like_count = DB::table("article_likes")->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function articleByLanguage($slug) {
        $language_id = DB::table("languages")->where("slug",$slug)->getOne()->id;
        $data = DB::raw("SELECT * FROM article_language LEFT JOIN articles ON articles.id=article_language.article_id WHERE article_language.language_id={$language_id}")->orderBy("articles.id","DESC")->paginate(6,"language=$slug");
        foreach ($data['data'] as $k=>$d) {
            $d->comment_count = DB::table("article_comments")->where('article_id',$d->id)->count();
            $d->like_count = DB::table("article_likes")->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function create($request) {
//        return Helper::slug($request['title']);
        $error = [];
        if(isset($request)) {
            if (empty($request['title'])) {
                $error[] = "Title Field is required";
            }
            if (empty($request['category_id'])) {
                $error[] = "Category Field is required";
            }
            if (empty($request['language'])) {
                $error[] = "Language Field is required";
            }
            if (empty($request['description'])) {
                $error[] = "Article Field is required";
            }
            if (count($error)) {
                return $error;
            } else {
                // image upload
                $image = $_FILES['image'];
                $image_name = $image['name'];
                $path = "assets/article/$image_name";
                $tmp_name = $image['tmp_name'];
                if(move_uploaded_file($tmp_name,$path)) {
                    $article = DB::create("articles",[
                       "user_id" => User::auth()->id,
                       "category_id" => $request['category_id'],
                       "slug" => Helper::slug($request['title']),
                       "title" => $request['title'],
                       "image" => $path,
                        "description" => $request['description']
                    ]);
                    if($article) {
                        foreach ($request['language'] as $id) {
                            DB::create("article_language",[
                                "article_id" => $article->id,
                                "language_id" => $id
                            ]);
                        }
                        return "success";
                    }
                }
            }
        }
        // insert into article table

        // insert many to many

        // return success
    }

    public static function search($search) {
        $data = DB::table("articles")->where('title','like',"%$search%")->orderBy("id","DESC")->paginate(6,"search=$search");
        foreach ($data['data'] as $k=>$d) {
            $d->comment_count = DB::table("article_comments")->where('article_id',$d->id)->count();
            $d->like_count = DB::table("article_likes")->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function update($request) {
        $error = [];
        if(isset($request)) {
            if (empty($request['title'])) {
                $error[] = "Title Field is required";
            }
            if (empty($request['category_id'])) {
                $error[] = "Category Field is required";
            }
            if (empty($request['language'])) {
                $error[] = "Language Field is required";
            }
            if (empty($request['description'])) {
                $error[] = "Article Field is required";
            }
            if (count($error)) {
                return $error;
            } else {
                $article = DB::table("articles")->where("slug",$request['article_slug'])->getOne();
                $articleLanguage = DB::table("article_language")->where('article_id',$article->id)->get();
                // image upload
                if($_FILES['image']['size'] !== 0) {
                    $image = $_FILES['image'];
                    $image_name = $image['name'];
                    $path = "assets/article/" . $image_name;
                    $tmp_name = $image['tmp_name'];
                    move_uploaded_file($tmp_name,$path);
                } else {
                    $path = $article->image;
                }
                $article = DB::update("articles",[
                    "category_id" => $request['category_id'],
                    "title" => $request['title'],
                    "image" => $path,
                    "description" => $request['description']
                ],$article->id);
                if($article) {
                    foreach ($articleLanguage as $art) {
                        DB::delete("article_language",$art->id);
                    }
                    foreach ($request['language'] as $id) {
                        DB::create("article_language",[
                            "article_id" => $article->id,
                            "language_id" => $id
                        ]);
                    }
                    return "success";
                }
            }
        }
    }
}














