<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Check Cuisine</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>

<body>
    <?php
    include_once "servers.php";

    // check passwords
    // get info from post
    $username = $_POST['username'];
    $password = $_POST['password'];

    // login as given user
    $result = run_query(Prefix::LOGIN);
    list(, $is_success) = explode(" ", $result, 2);
    $is_success = $result === "true" ? true : false;

    if ($is_success) {
        // redirect to homepage
        session_start();
        $_SESSION['logged_user'] = $username;
        header("refresh:0; url=frontend.html");
    } else {
        // echo out a fail message
    ?>
    <h1>Error: incorrect username or password!</h1>
    <?php
    }
    ?>

    <div class="center">
        <form method="POST">
            <p>
                <label>Username </label><input type="text" name="username" />
            </p>
            <p>
                <label>Password </label><input type="password"
                    name="password" />
            </p>
            <input type="submit" name="submit" value="Log In" />
            <p> Not Registered? </p>
            <figcaption>
                <a href="register.html"> Register Here </a>
            </figcaption>
        </form>
    </div>
</body>

</html>