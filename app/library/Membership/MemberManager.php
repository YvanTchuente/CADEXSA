<?php

declare(strict_types=1);

namespace Application\Membership;

use Application\Database\{
    Connector,
    Connection,
    ConnectionTrait,
    ConnectionAware
};
use Application\Security\Securer;
use PHPMailer\PHPMailer\PHPMailer;
use Application\Authentication\Authenticator;
use Application\DateTime\TimeDurationInterface;
use Application\MiddleWare\{Response, TextStream};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Application\Security\{Decrypter, SecurerAware, SecurerAwareTrait};

/**
 * Manages members and site visitors
 */
class MemberManager implements Authenticator, ConnectionAware, SecurerAware
{
    /**
     * Member's database table name
     */
    private const TABLE = 'members';
    
    /**
     * @var MemberBuilder
     */
    private $builder;
    
    /**
     * @var MemberBuildDirector
     */
    private $director;

    private static $instance;

    private function __construct(Connector $connector)
    {
        $this->setConnector($connector);
    }

    public static function Instance(): self
    {
        if (!isset(self::$instance)) {
            $MemberBuilder = new MemberBuilder();
            $MemberBuildDirector = new MemberBuildDirector($MemberBuilder);
            self::$instance = new self(Connection::Instance());
            (self::$instance)->builder = $MemberBuilder;
            (self::$instance)->director = $MemberBuildDirector;
            (self::$instance)->setEncrypter(new Securer());
            (self::$instance)->setDecrypter(new Securer());
        }
        return self::$instance;
    }

    use ConnectionTrait;

    use SecurerAwareTrait;

    public function Authenticate(RequestInterface $request, Decrypter $decrypter = null): ResponseInterface
    {
        $code = 401;
        $body = new TextStream('Authentication credentials incorrect');
        $contents = (string) $request->getBody();
        $params = json_decode($contents);
        $response = new Response();
        $username = $params->username ?? false;
        if ($username) {
            $sql = 'SELECT * FROM ' . self::TABLE . ' WHERE username = ?';
            $stmt = $this->connector->getConnection()->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                if (!isset($decrypter)) {
                    throw new \RuntimeException("Authentication process needs to decrypt some data");
                }
                $db_pwd = $decrypter->decrypt($row['password'], $row['password_key'], $row['iv']);
                $db_pwd = rtrim($db_pwd);
                if ($db_pwd == $params->password) {
                    unset($row['password']);
                    unset($row['password_key']);
                    unset($row['iv']);
                    $body = new TextStream(json_encode($row));
                    $response = $response->withBody($body);
                    $code = 200;
                }
            }
        }
        $response = $response->withBody($body)->withStatus($code);
        return $response;
    }

    /**
     * Finds out whether the visitor is logged-in to an account
     * 
     * @return bool
     **/
    public function is_logged_in()
    {
        if (
            isset($_SESSION['ID']) &&
            isset($_SESSION['firstname']) &&
            isset($_SESSION['lastname']) &&
            isset($_SESSION['username'])
        ) {
            // Checks online members records 
            $query = $this->connector->getConnection()->query("SELECT * FROM online_members WHERE memberID='" . $_SESSION['ID'] . "'");
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return true;
            } else {
                $this->logout();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Logs in a member to their account
     * 
     * @param RequestInterface $request A request to log in
     * 
     * @return true|string true on success or message describing an error that has occured
     **/
    public function login(RequestInterface $request)
    {
        $response = $this->Authenticate($request, $this->decrypter);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $member = json_decode($response->getBody()->getContents());  // Stores the member in an object
            // Checks online members records 
            $query = $this->connector->getConnection()->query("SELECT * FROM online_members WHERE memberID='" . $member->ID . "'");
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                // Log in the user
                $_SESSION['ID'] = $member->ID;
                $_SESSION['firstname'] = $member->firstname;
                $_SESSION['lastname'] = $member->lastname;
                $_SESSION['fullname'] = $member->lastname . " " . $member->firstname;
                $_SESSION['username'] = $member->username;
                $_SESSION['level'] = $member->level;
                // Insert the logged in member to the online users table
                $uid = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
                $this->connector->getConnection()->query("INSERT INTO online_members (memberID, unique_identifier) VALUES ('" . $member->ID . "', '$uid')");
                return true;
            } else if ($member->ID == $row['memberID']) {
                return "Access denied";
            }
        } else {
            return "Invalid username and/or password";
        }
    }

    /**
     * Logs out a member from their account
     * 
     * @return true
     * 
     * @throws \RuntimeException
     **/
    public function logout()
    {
        $delete_from_online_members = "DELETE FROM online_members WHERE memberID='" . $_SESSION['ID'] . "'";
        $update_last_connection_date = "UPDATE members SET last_connection = CURRENT_TIMESTAMP() WHERE ID='" . $_SESSION['ID'] . "'";
        $has_updated_last_connection_date = $this->connector->getConnection()->query($update_last_connection_date);
        $has_deleted_from_online_members = $this->connector->getConnection()->query($delete_from_online_members);
        if (!$has_updated_last_connection_date && !$has_deleted_from_online_members) {
            throw new \RuntimeException("Error updating 'members' and 'online_members' tables respectively");
        }
        session_unset();
        session_destroy();
        return true;
    }

    /**
     * Creates a member account for a visitor
     * 
     * Creates a new member account and returns true on success or a message describing an error that has occured
     * 
     * @param array $visitor The array containing the data (firstname, lastname, username, email, contact, password, batch year,      oritentation, city, country and a description of themselves 'aboutme') of the visitor wishing to sign up
     * 
     * @return bool|string
     * 
     * @throws \InvalidArgumentException For an invalid visitor argument
     **/
    public function signup(array $visitor, PHPMailer $mail)
    {
        $isArrayValid = array_key_exists('firstname', $visitor) && array_key_exists('lastname', $visitor) && array_key_exists('username', $visitor) && array_key_exists('email', $visitor) && array_key_exists('contact', $visitor) && array_key_exists('password', $visitor) && array_key_exists('batch_year', $visitor) && array_key_exists('orientation', $visitor) && array_key_exists('city', $visitor) && array_key_exists('country', $visitor) && array_key_exists('aboutme', $visitor);
        if (!$isArrayValid) {
            throw new \InvalidArgumentException("Invalid user data array");
        }
        if (!$this->check_member_exist($visitor['username'])) {
            $encryption = $this->encrypter->encrypt($visitor['password']);
            foreach ($visitor as $key => $value) {
                if ($key == 'password') {
                    $data[$key] = $encryption['cipher'];
                    $data['password_key'] = $encryption['key'];
                    $data['iv'] = $encryption['iv'];
                    continue;
                }
                $data[$key] = $value;
            }

            $fields = ['firstname', 'lastname', 'username', 'email', 'contact', 'password', 'password_key', 'iv', 'batch_year', 'orientation', 'city', 'country', 'aboutme'];
            foreach ($fields as $field) {
                $placeholders[] = ":$field";
            }

            $sql = "INSERT INTO `" . self::TABLE . "` (`" . implode("`,`", $fields) . "`) VALUES (" . implode(",", $placeholders) . ")";
            $stmt = $this->connector->getConnection()->prepare($sql);
            if ($stmt->execute($data)) {
                // Configuring the welcoming mail
                $tomail = $visitor['email'];
                $subject = "Welcome to La Cadenelle Ex-Students Association";
                $recipientName = $visitor['firstname'] . " " . $visitor['lastname'];
                $msg = "Hi " . $recipientName . ",\r\n\r\n" . "We are happy, you joined our network. These are your login credentials\r\n" . "username : " . $visitor['username'] . "\r\n" . "password : " . $visitor['password'];

                // Mail Recipients
                $mail->setFrom('admin@cadexsa.org', 'CADEXSA Administration');
                $mail->addAddress($tomail, $recipientName);     //Add a recipient

                // Mail Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $msg;

                // Send the mail
                $mail->send();

                return true;
            } else {
                return "An error occured, please try again";
            }
        } else {
            return "Member already exists";
        }
    }

    /**
     * Retrieves the data of a member from the database
     * 
     * @param int $memberID The ID of the member
     * 
     * @return Member
     **/
    public function getMember(int $memberID)
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM members WHERE ID='$memberID'");
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $key => $value) {
                if ($key == 'password' || $key == 'password_key' || $key == 'iv') {
                    continue;
                }
                $memberData[$key] = $value;
            }
        }
        $query = $this->connector->getConnection()->query("SELECT name FROM profile_pictures WHERE memberID='" . $memberData['ID'] . "'");
        if ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $profile_picture = '/static/images/profile_pictures/' . $row['name'] . '.jpg';
        } else {
            $profile_picture = '/static/images/graphics/profile-placeholder.png';
        }
        $memberData['avatar'] = $profile_picture;
        $this->director->construct($memberData);
        $member = $this->builder->getMember();
        return $member;
    }

    /**
     * Retrieves the ID of a member
     *
     * @param string $username The username of the member
     * 
     * @return int
     */
    public function getIDByName(string $username)
    {
        $stmt = $this->connector->getConnection()->prepare('SELECT ID FROM ' . self::TABLE . ' WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        $memberID = (int) $res['ID'];
        return $memberID;
    }

    /**
     * Retrieves members's data into an array
     * 
     * @param int $n The number of members to retrieve. 
     *               It Defaults to 0 which means to retrieve all members
     * 
     * @return Member[]
     **/
    public function getMembers(int $n = 0)
    {
        $members = [];
        $sql = "SELECT ID FROM " . self::TABLE;
        if ($n > 0) {
            $sql .= " LIMIT $n";
        }
        $query = $this->connector->getConnection()->query($sql);
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $column) {
                $members[] = $this->getMember((int)$column);
            }
        }
        return $members;
    }

    /**
     * Determines the connection status of a member
     * 
     * Determines whether a member is online or offline. If a member is offline
     * it determines for how long they disconnected
     * 
     * @param int $memberID The ID of the member
     * 
     * @return array The array contains the keys 'status and 'lastSeen'
     **/
    public function getState(int $memberID, TimeDurationInterface $timeDuration)
    {
        $member = $this->getMember($memberID);
        $status = "offline";
        $last_connection = $member->getLastConnection();
        $lastConnection = (isset($last_connection)) ? new \DateTime($member->getLastConnection()) : new \DateTime();
        $timeDuration->setReferenceTime($lastConnection);
        $timeDuration->setTargetTime(new \DateTime());
        $last_seen = $timeDuration->getLongestDuration();

        $res = $this->connector->getConnection()->query("SELECT last_activity FROM online_members WHERE memberID='" . $member->getID() . "'");
        if ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $status = "online";
            $lastActivity = new \DateTime($row['last_activity']);
            $timeDuration->setReferenceTime(new \DateTime());
            $timeDuration->setTargetTime($lastActivity);
            $last_seen = $timeDuration->getLongestDuration();
        }
        $state = array('status' => $status, 'lastSeen' => $last_seen);
        return $state;
    }

    /**
     * Checks whether a member account exists
     *
     * @param string $username The username of the member
     * 
     * @return bool
     */
    public function check_member_exist(string $username)
    {
        $stmt = $this->connector->getConnection()->prepare('SELECT * FROM ' . self::TABLE . ' WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $rows = count($stmt->fetchAll(\PDO::FETCH_ASSOC));
        $member_exists = ($rows === 0) ? false : true;
        return $member_exists;
    }

    /**
     * Determines the status of a member
     * 
     * @param int $userID The ID of the member
     * 
     * @return string
     **/
    public function getStatus(int $userID)
    {
        $status = "Offline";
        $stmt = $this->connector->getConnection()->query("SELECT * FROM online_members WHERE memberID='$userID'");
        if ($stmt->fetch(\PDO::FETCH_ASSOC)) {
            $status = "Online";
        }
        return $status;
    }
}
