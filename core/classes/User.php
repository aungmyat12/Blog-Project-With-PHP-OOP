<?php
class User
{
    public static function auth()
    {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            return DB::table("users")->where('id', $user_id)->getOne();
        }
        return false;
    }

    public function register($request)
    {
        $error = [];
        if (isset($request)) {
            if (empty($request['name'])) {
                $error[] = "Name Field is required";
            }
            if (empty($request['email'])) {
                $error[] = "Email Field is required";
            } else if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $error[] = "Invalid Email Format";
            }
            if (empty($request['password'])) {
                $error[] = "Password Field is required";
            }
            // check emaili already exist
            $user = DB::table("users")->where("email", $request['email'])->getOne();
            if ($user) {
                $error[] = "Email already exists";
            }
            if (count($error)) {
                return $error;
            } else {
                // Insert Data
                $user = DB::create("users", [
                    "name" => Helper::filter($request['name']),
                    "slug" => Helper::slug(Helper::filter($request['name'])),
                    "email" => Helper::filter($request['email']),
                    "password" => password_hash($request['password'], PASSWORD_BCRYPT)
                ]);
                // Session userid
                $_SESSION['user_id'] = $user->id;
                // header index
                Helper::redirect("index.php");
                return "success";
            }
        }
    }

    public function login($request)
    {
        if (isset($request)) {
            $error = [];
            $email = Helper::filter($request['email']);
            $password = $request['password'];
            if (empty($request['email'])) {
                $error[] = "Email field is required";
            } else if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $error[] = "Invalid Email Format";
            }
            if (empty($request['password'])) {
                $error[] = "Password field is required";
            }
            $user = DB::table("users")->where("email", $email)->getOne();
            if ($user) {
                $db_password = $user->password;
                if (password_verify($password, $db_password)) {
                    $_SESSION['user_id'] = $user->id;
                    return "success";
                } else {
                    $error[] = "Email and Password do not match!";
                }
            } else {
                $error[] = "Email and Password do not match!";
            }
            if (count($error)) {
                return $error;
            }
        }
    }

    public static function update($request)
    {
        $user = DB::table("users")->where("slug",$request['user_slug'])->getOne();
        if($request['password']) {
            // new Password
            $password =password_hash($request['password'],PASSWORD_BCRYPT);
        } else {
            // old password
            $password = $user->password;
        }
        if($_FILES['image']['size'] !== 0) {
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $path = "assets/user/" . $image_name;
            $tmp_name = $image['tmp_name'];
            move_uploaded_file($tmp_name,$path);
        } else {
            $path = $user->image;
        }
        $user = DB::update("users",[
            "name" => $request['name'],
            "image" => $path,
            "email" => $request['email'],
            "password" => $password
        ],$user->id);
        if($user) {
            return "success";
        }
    }
}
