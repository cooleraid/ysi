<?php
require_once __DIR__ . "/vendor/autoload.php";

use josegonzalez\Dotenv\Loader as Dotenv;

//load the local environment data (.env) when $_ENV['environment'] == development
if ($_ENV['environment'] == 'development') {
    $appDir = __DIR__;
    Dotenv::load([
        'filepath' => $appDir . '/.env',
        'toEnv' => true,
    ]);
}
//create a session or resume the current session
session_start();
//set the database configurations as constants
define('DB_SERVER', $_ENV['MYSQL_SERVER']);
define('DB_USERNAME', $_ENV['MYSQL_USERNAME']);
define('DB_PASSWORD', $_ENV['MYSQL_PASSWORD']);
define('DB_DATABASE', $_ENV['MYSQL_DATABASE']);

/**
 * Class Server
 */
class Server
{
    protected $db;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //open a new connection to the MySQL server
        $this->db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        //check for connection errors
        if ($this->db->connect_error) {
            die('Database error -> ' . $this->db->connect_error);
        }
    }

    /**
     * register
     *
     * User account creation
     *
     * @param  mixed $email
     * @param  mixed $username
     * @param  mixed $password
     * @param  mixed $confirm_password
     * @param  mixed $profile_image
     *
     * @return void
     */
    public function register($email, $username, $password, $confirm_password, $profile_image)
    {
        //check if $email, $username, $password, $confirm_password, or $profile_image parameters are empty
        if (empty($email)) {
            return $this->response(400, "Email is required", false);
        }
        if (empty($username)) {
            return $this->response(400, "Username is required", false);
        }
        if (empty($password)) {
            return $this->response(400, "Password is required", false);
        }
        if (empty($confirm_password)) {
            return $this->response(400, "Confirm passwird is required", false);
        }
        if (empty($profile_image)) {
            return $this->response(400, "Profile image is required", false);
        }
        //secure input values with the clean_input() method
        $email = $this->clean_input($email);
        $username = $this->clean_input($username);
        $password = $this->clean_input($password);
        $confirm_password = $this->clean_input($confirm_password);
        //check if the password matches the confirm_password parameter
        if ($password !== $confirm_password) {
            return $this->response(400, "password mismatch", false);
        }
        //validate image size. (size is calculated in bytes)
        if ($profile_image['size'] > 500000) {
            return $this->response(400, "Image size should not be greated than 500Kb", false);
        }
        //check if uploaded file is a supported file type (image)
        if (!in_array($profile_image["type"], array("image/jpeg", "image/jpg", "image/gif", "image/png"))) {
            return $this->response(400, "Only jpg, gif and png files upload are allowed", false);
        }
        //assign a name to the uploaded image based on the current time
        $profile_image_name = time() . '-' . $profile_image["name"];
        //initialize the image upload location
        $target_dir = "images/";
        $target_image = $target_dir . basename($profile_image_name);
        //check if the image exists
        if (file_exists($target_image)) {
            return $this->response(400, "Image name already exists", false);
        }
        // Upload image only if there are no errors
        if (move_uploaded_file($profile_image["tmp_name"], $target_image)) {
            //hash password
            $password = md5($password);
            //check if the username or email already exists in the database
            $query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $result = $this->db->query($query) or die($this->db->error);
            $count_row = $result->num_rows;
            //if the username/email is not in database then insert to the users table
            if ($count_row == 0) {
                $query = "INSERT INTO users SET `username`='$username', `password`='$password', email='$email', profile_image='$profile_image_name'";
                $result = $this->db->query($query) or die($this->db->error);
                return $this->response(201, "User created successfully", true);
            } else {
                return $this->response(400, "Username or email already exists", false);
            }
        } else {
            return $this->response(400, "There was an error while uploading profile image", false);
        }
    }

    /**
     * login
     *
     * Authenticate a user
     *
     * @param  mixed $email
     * @param  mixed $password
     *
     * @return void
     */
    public function login($email, $password)
    {
        //check if $email or $password parameters are empty
        if (empty($email)) {
            return $this->response(400, "Email is required", false);
        }
        if (empty($password)) {
            return $this->response(400, "Password is required", false);
        }
        //secure input values with the clean_input() method
        $email = $this->clean_input($email);
        $password = $this->clean_input($password);
        //hash password
        $password = md5($password);
        //check if the user exists in the database
        $query = "SELECT * FROM users WHERE `password`='$password' AND `email`='$email'";
        $result = $this->db->query($query) or die($this->db->error);
        $user_data = $result->fetch_array(MYSQLI_ASSOC);
        $count_row = $result->num_rows;
        //generate session data if the user exists
        if ($count_row == 1) {
            //delete the user's password from the array
            unset($user_data['password']);
            //set login status as true
            $user_data['login'] = true;
            //set session data
            $_SESSION = $user_data;
            return $this->response(200, "Login Successful", $_SESSION);
        } else {
            return $this->response(400, "Invalid Email or Password", false);
        }
    }

    /**
     * retrieve_session
     *
     * Retrieve a logged in user's session
     *
     * @return void
     */
    public function retrieve_session()
    {
        //fetch the session data
        return $this->response(200, "Session retrieved successfully", $_SESSION);
    }

    /**
     * logout
     *
     * Destroy a user's session
     *
     * @return void
     */
    public function logout()
    {
        $_SESSION['login'] = false;
        //delete the session data
        unset($_SESSION);
        session_destroy();
        return $this->response(200, "Logout Successful", true);
    }

    /**
     * response
     *
     * Response Helper
     *
     * @param  mixed $code
     * @param  mixed $message
     * @param  mixed $data
     *
     * @return void
     */
    public function response($code = 200, $message = null, $data = null)
    {
        //set the response code
        http_response_code($code);
        //set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        //treat as json
        header('Content-Type: application/json');
        //array of status codes
        $status = array(
            200 => '200 OK',
            201 => '201 Created',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            500 => '500 Internal Server Error',
        );
        header('Status: ' . $status[$code]);
        //return the encoded json response
        return json_encode(array(
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ));
    }

    /**
     * clean_input
     *
     * Secure Strings for security
     *
     * @param  mixed $input
     *
     * @return void
     */
    public function clean_input($input)
    {
        //remove the white spaces from the input string
        $input = trim($input);
        //remove backslashes
        $input = stripslashes($input);
        //remove html characters
        $input = htmlspecialchars($input);
        return $input;
    }
}

//instance of class server
$server = new Server();
//check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //check if the request contains a request_type
    if (isset($_POST['request_type'])) {
        //check if the request_type == login || register || logout || retrieve_session
        if ($_POST['request_type'] == 'login') {
            //sign in a user
            $response = $server->login($_POST['email'], $_POST['password']);
            echo $response;
        } elseif ($_POST['request_type'] == 'register') {
            //create a user
            $response = $server->register($_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirm_password'], $_FILES['profile_image']);
            echo $response;
        } elseif ($_POST['request_type'] == 'logout') {
            //logout a user
            $response = $server->logout();
            echo $response;
        } elseif ($_POST['request_type'] == 'retrieve_session') {
            //retrieve a logged in user's session
            $response = $server->retrieve_session();
            echo $response;
        }
    } else {
        $response = $server->response(400, "Request must contain a request type form post data", false);
        echo $response;
    }
} else {
    $response = $server->response(400, "Request must be a POST request", false);
    echo $response;
}
