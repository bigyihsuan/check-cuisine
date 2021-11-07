<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Check Cuisine Register Page</title>
</head>
<script>
function checkPasswords(form) {
    let match = form.password.value == form.confirm.value;
    if (!match) {
        alert("Passwords don't match.");
    }
    return match;
}
</script>

<body>
    <style>
    * {
        text-align: center;
    }

    input {
        border: 1px solid black;
        break-after: auto;
    }
    </style>
    <p> Registration Page </p>

    <?php
    include_once "servers.php";

    // check passwords
    // get info from post
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (strcmp($password, $confirm) !== 0) { ?>
    <h1>Passwords don't match!</h1>
    <?php }

    // passwords match, send to the backend
    $_POST['password'] = password_hash($password . $username, null);
    include "./frontsend.php";
    $result = run_query(Prefix::REGISTER);
    list(, $is_success) = explode(" ", $result, 2);
    $is_success = $result === "true" ? true : false;

    if ($is_success) {
        // registration happened goodly
        // login as new user
        $result = run_query(Prefix::LOGIN);
        list(, $is_success) = explode(" ", $result, 2);
        $is_success = $result === "true" ? true : false;
        if ($is_success) {
            // redirect to homepage
            session_start();
            $_SESSION['logged_user'] = $username;
            header("refresh:0; url=frontend.html");
        }
    } else { ?>
    <h1>Error: Error while registering new user</h1>
    <?php
    }
    ?>

    <form method="POST">

        <input name="username" type="text" placeholder="Enter your username"
            required /><br>

        <input type="password" name="password" placeholder="Enter password"
            required /><br>

        <input type="password" name="confirm" placeholder="Re-Enter password"
            required /><br>

        <input type="submit" value="Register" />

        <figcaption><a href="login.html"> Log In Here </a>
            <figcaption>
    </form>

</body>

</html>
