<?php
/**
* USER class
*/

/**
* USER class provides methods related to user and it's session. 
*
* Provides methods for registering and logging in the user. Also
* provides method to determine if the user is logged in and a way to
* redirect and log out the user. Uses PDO object with established
* connectivity to the database in constructor.
* Example:
* ```php
* $user = new USER($DB_connnection) 
* ```
*/
class USER
{
    /**
    * @var object $db PDO object
    */
    private $db;

    /**
    * USER constructor.
    * @param object $DB_con Database connection: PDO object 
    */
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }


    /**
    * Registers the user.
    * Takes name, email, and password and writes a row in a database.
    *
    * @param string $name users name
    * @param string $email users email
    * @param string $password users password
    * @return stuff
    */
    public function register($name,$email,$password){
        //TODO: fix the return value;
        try{
            $new_password = password_hash($password, PASSWORD_DEFAULT);

            $query = $this->db->prepare("INSERT INTO users(user_name,user_email,user_pass) 
                VALUES(:name, :email, :password)");

            $query->bindparam(":name", $name);
            $query->bindparam(":email", $email);
            $query->bindparam(":password", $new_password);            
            return $query->execute(); 
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }    
    }

    /**
    * Log in the user.
    * Takes email and password and matches them in the database.
    *
    * @param string $email users email
    * @param string $password users password
    * @return boolean
    */
    public function login($email,$password){
        try{
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_email=:email LIMIT 1");
            $stmt->execute(array(':email'=>$email));
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
          
            if($stmt->rowCount() == 1){
                if(password_verify($password, $userRow['user_pass']))
                {
                    $_SESSION['user_session'] = $userRow['user_id'];
                    return true;
                }
                    else
                {
                    return false;
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }
 
    /**
    * Check if user is logged in.
    * Checks if:
    *```
    *isset($_SESSION['user_session']
    *```
    * @return boolean
    */
    public function is_loggedin(){
        if(isset($_SESSION['user_session'])){
            return true;
        }
        return false;
    }

    /**
    * Redirects user to a new url.
    *
    * @param string $url Url to a new destination
    * @return void
    */
    public function redirect($url){
       header("Location: $url");
    }

    /**
    * Logs out the user.
    *
    * Destroys the session.
    *
    * @return void
    */
    public function logout(){
        session_destroy();
        unset($_SESSION['user_session']);
    }
}
?>