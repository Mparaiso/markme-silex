<?php

namespace App\DataAccessLayer {

    use App\DataTransferObjects\User;
    use Doctrine\DBAL\Connection;
    use Doctrine\DBAL\DBALException;

    class  UserProvider implements IUserProvider {

        /**
         * @var Doctrine\DBAL\Connection $connection
         */
        protected $connection;

        function __construct(Connection $connection){
            $this->connection = $connection;
        }

        /**
         * @return \App\DataTransferObjects\User
         */
        function create(User $user){
            if ($this->getByEmail($user->email) !== null){
                throw new DBALException("Email $this->email already used", 1);
            }
            if ($this->getByUsername($user->username) !== null){
                throw new DBALException("Username $user->username already taken",1);
            }
            /** @see http://www.richardlord.net/blog/dates-in-php-and-mysql * */
            $time = $this->_getCurrentDatetime();
            $affetchedRows = $this->connection->insert(
                'users', array('username'=>$user->username,
                    'email'=>$user->email, 'password'=>$user->password, 'created_at'=>$time,
                    'last_login'=>$time)
                );
            $LastInsertedId = intval($this->connection->lastInsertId());
            return $this->getById($LastInsertedId);
            
        }

        /**
         * @return \App\DataTransferObjects\User
         */
        function getByUserNameAndPassword($username,$password){
            $record = $this->connection->fetchAssoc(
                "SELECT id, username, email FROM users ".
                "WHERE username = :username AND password = :password", array(
                    "username"=>$username,
                    "password"=>$password,
                    )
                );
            return $this->_recordToUser($record);
        }


        function updateLastLoginDate($user_id){
            $affectedRows = $this->connection->update(
                "users",
                array("last_login"=>$this->_getCurrentDatetime()), 
                array("id"=>$user_id));
            return $affectedRows;
        }

        function update(User $user,$user_id){
            $username = $user->username;
            $email = $user->email;
            $affectedRows = $this->connection->update(
                "users",
                array("username"=>$username,"email"=>$email),
                array("id"=>$user_id)
                );
            return $affectedRows;
        }

        /**
         *  @return \App\DataTransferObjects\User
         */
        function getByUsername($username){
            $record = $this->connection->fetchAssoc("SELECT * FROM users WHERE username = ?", array($username));
            return $this->_recordToUser($record);
        }

        /**
         *  @return \App\DataTransferObjects\User
         */
        function getByEmail($email){
            $record = $this->connection->fetchAssoc("SELECT * FROM users WHERE email = ? ", array($email));
            return $this->_recordToUser($record);
        }


        
        /**
         *  @return \App\DataTransferObjects\User
         */
        function getById($user_id){
            $record = $this->connection->fetchAssoc(
                " SELECT id,username,password,email,created_at,last_login ".
                "FROM  users where id = ?",array($user_id));
            return $this->_recordToUser($record);
        }

        function _getCurrentDatetime(){
            return $time = date('Y-m-d H:i:s', time());
        }

        /**
         *  @return \App\DataTransferObjects\User
         */
        function _recordToUser($record,$getPassword=false){
            if($record!=null){
                $user = new User();
                $user->created_at = $record["created_at"];
                $user->email = $record["email"];
                $user->id = $record["id"];
                $user->last_login = $record["last_login"];
                $user->username = $record["username"];
                $getPassword AND $user->password = $record["password"];
                return $user;
            }
        }

        function _userToRecord(User $user){
            $record = get_object_vars($user);
            return $record;
        }

    }
}
