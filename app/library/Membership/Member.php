<?php

declare(strict_types=1);

namespace Application\Membership;

use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\Authentication\Authenticator;
use Application\MiddleWare\{Stream, Textstream, Response};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

class Member implements Authenticator, ConnectionAware
{
    protected string $dbtable = 'members'; // Members' database table
    protected array $sessionData; // Stores logged-in member's data

    public function __construct(Connector $connector)
    {
        $this->setConnector($connector);
        $this->sessionData = $_SESSION;
    }

    use ConnectionTrait;

    public function Authenticate(RequestInterface $request): ResponseInterface
    {
        $code = 401;
        $body = new TextStream('Authentication credentials incorrect');
        $contents = (string) $request->getBody();
        $params = json_decode($contents);
        $response = new Response();
        $username = $params->username ?? false;
        if ($username) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE username = ?';
            $stmt = $this->connector->getConnection()->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                if (password_verify($params->password, $row['password'])) {
                    unset($row['password']);
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
        if (count($this->sessionData) > 0) {
            if ($this->sessionData['LoggedIn'] == true) {
                // Checks online members records 
                $query = $this->connector->getConnection()->query("SELECT * FROM online_members WHERE memberID='" . $this->sessionData['ID'] . "'");
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
        } else {
            return false;
        }
    }

    /**
     * Logs in a visitor to his/her member account
     * 
     * @param RequestInterface $request A client-sent request to log in
     * 
     * @return bool|string
     **/
    public function login(RequestInterface $request)
    {
        $response = $this->Authenticate($request); // Authenticates the user 
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $userInfo = json_decode($response->getBody()->getContents());  // Stores the member in an object
            // Checks online members records 
            $query = $this->connector->getConnection()->query("SELECT * FROM online_members WHERE memberID='" . $userInfo->ID . "'");
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                // Log in the user
                $_SESSION['ID'] = $userInfo->ID;
                $_SESSION['firstname'] = $userInfo->firstname;
                $_SESSION['lastname'] = $userInfo->lastname;
                $_SESSION['username'] = $userInfo->username;
                $_SESSION['level'] = $userInfo->level;
                $_SESSION['LoggedIn'] = true;
                $this->sessionData = $_SESSION;
                // Insert the logged in member to the online users table
                $this->connector->getConnection()->query("INSERT INTO online_members (memberID) VALUES ('" . $userInfo->ID . "')");
                return true;
            } else {
                $this->connector->getConnection()->query("DELETE FROM online_members WHERE memberID='" . $userInfo->ID . "'");
                return "An error occured. Please try again";
            }
        } else {
            return "Invalid username or password";
        }
    }

    /**
     * Logs out a logged-in member
     * 
     * @throws \RuntimeException
     **/
    public function logout()
    {
        $delete_member = "DELETE FROM online_members WHERE memberID='" . $_SESSION['ID'] . "'";
        $update_last_connection = "UPDATE members SET last_connection = CURRENT_TIMESTAMP() WHERE ID='" . $_SESSION['ID'] . "'";
        if ($this->connector->getConnection()->query($update_last_connection) && $this->connector->getConnection()->query($delete_member)) {
            session_unset();
            session_destroy();
            $this->sessionData = array();
            return true;
        } else {
            throw new \RuntimeException("Error updating 'members' and 'online_members' tables respectively");
        }
    }

    public function validate(RequestInterface $request)
    {
        $userInfo = [];
        $params = json_decode($request->getBody()->getContents());
        $iterator = new \ArrayIterator($params);
        foreach ($iterator as $key => $value) {
            $userInfo[$key] = $value;
        }
        if (filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            if ($this->validateCountry($userInfo['country'])) {
                if ($this->validatePassword($userInfo['password'])) {
                    if ($userInfo['password'] == $userInfo['confirm-password']) {
                        foreach ($userInfo as $key => $value) {
                            if ($key == "confirm-password"  || $value == "") {
                                continue;
                            }
                            $userInfo1[$key] = $value;
                        }
                        return $userInfo1;
                    } else {
                        return "Passwords mismatch";
                    }
                } else {
                    return "Invalid password";
                }
            } else {
                return "Invalid country";
            }
        } else {
            return "Invalid email address";
        }
    }

    /**
     * Creates a new member account for a visitor
     * 
     * @param array $userInfo Array containing the data of the visitor wishing to sign in
     * 
     * @return bool|string
     **/
    public function signup(array $userInfo)
    {
        if (!$this->check_user_exist($userInfo['username'])) {
            $data = [];
            $password = $userInfo['password'];
            foreach ($userInfo as $key => $value) {
                if ($key == 'password') {
                    $value = password_hash($userInfo['password'], PASSWORD_DEFAULT);
                }
                $data[] = $value;
            }
            $fields = ['firstname', 'lastname', 'username', 'email', 'contact', 'password', 'batch_year', 'orientation', 'city', 'country', 'aboutme'];
            $sql = "INSERT INTO `" . $this->table . "` (`" . implode("`,`", $fields) . "`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->connector->getConnection()->prepare($sql);

            if ($stmt->execute($data)) {
                $tomail = $userInfo['email'];
                $from = "From: team@cadexsa.org" . "\r\n";
                $subject = "Welcome to La Cadenelle Ex-Students Association\n";
                $mail_msg = "Hi " . $userInfo['firstname'] . " " . $userInfo['lastname'] . ",\r\n\r\n" . "We are happy, you joined our network. These are your login credentials\r\n" . "username : " . $userInfo['username'] . "\r\n" . "password : " . $userInfo['password'];

                if (mail($tomail, $subject, $mail_msg, $from)) {
                    return true;
                }
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
     * @param int|null $member_id ID of the member
     * 
     * @return array
     **/
    public function getInfo(int $member_id = null)
    {
        $memberID = $member_id ?? (int) $this->sessionData['ID'];
        $res = $this->connector->getConnection()->query("SELECT * FROM members WHERE ID='$memberID'");
        $res = $res->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $key => $value) {
                if ($key == "password") {
                    continue;
                }
                $userInfo[$key] = $value;
            }
        }
        $query = $this->connector->getConnection()->query("SELECT name FROM profile_pictures WHERE memberID='" . $userInfo['ID'] . "'");
        if ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $userInfo['picture'] = '/members/profile_pictures/' . $row['name'] . '.jpg';
        } else {
            $userInfo['picture'] = '/static/images/graphics/profile-placeholder.png';
        }
        return $userInfo;
    }

    public function getState(int $member_id = null)
    {
        $memberID = $member_id ?? (int) $this->sessionData['ID'];
        $member = $this->getInfo($memberID);
        $status = "offline";
        $present = new \DateTime();
        $lastConnection = (isset($member['last_connection'])) ? new \DateTime($member['last_connection']) : $present;
        $interval = $present->diff($lastConnection);

        $res = $this->connector->getConnection()->query("SELECT last_activity FROM online_members WHERE memberID='" . $member['ID'] . "'");
        if ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $status = "online";
            $present = new \DateTime();
            $lastActivity = new \DateTime($row['last_activity']);
            $interval = $present->diff($lastActivity);
        }
        $last_seen = "";
        foreach ($interval as $key => $value) {
            if ($value !== 0) {
                switch ($key) {
                    case 'm':
                        $last_seen = $value . " months ago";
                        break;
                    case 'd':
                        $last_seen = $value . " days ago";
                        break;
                    case 'h':
                        $last_seen = $value . " hours ago";
                        break;
                    case 'i':
                        $last_seen = $value . " mins ago";
                        break;
                    case 's':
                        $last_seen = $value . " secs ago";
                        if ($value <= 5) $last_seen = "Active";
                        break;
                    case 'f':
                        $last_seen = "0 sec";
                        if ($value < 10) $last_seen = "Active";
                        break;
                    default:
                        $last_seen = $value . " " . $key;
                        break;
                }
                break;
            } else continue;
        }
        $state = array('status' => $status, 'lastSeen' => $last_seen);
        return $state;
    }

    /**
     * Retrieves all members's data into an array
     * 
     * @return array
     **/
    public function getMembers()
    {
        $memberIDs = $this->connector->getConnection()->query("SELECT ID FROM members");
        while ($row = $memberIDs->fetch(\PDO::FETCH_ASSOC)) {
            $members[] = $this->getInfo((int) $row['ID']);
        }
        return $members;
    }

    public function check_user_exist(string $username)
    {
        $stmt = $this->connector->getConnection()->prepare('SELECT * FROM members WHERE username = :username');
        $stmt->bindParam('username', $username);
        $stmt->execute();
        $rows = count($stmt->fetchAll(\PDO::FETCH_ASSOC));
        if ($rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determines the status of a member
     * 
     * @param Connector $conn Database connector
     * @param int $requestedUser ID of the member
     * 
     * @return string
     **/
    public static function getStatus(Connector $connector, int $requestedUser): string
    {
        $status = "Offline";
        $connection = $connector->getConnection();
        $res = $connection->query("SELECT * FROM online_members WHERE memberID='$requestedUser'");
        if ($res->fetch(\PDO::FETCH_ASSOC)) {
            $status = "Online";
        }
        return $status;
    }

    protected function validatePassword(string $password): bool
    {
        $validity = false;
        if (strlen($password) >= 8) {
            $validity = true;
        }
        return $validity;
    }

    protected function validateCountry(string $country): bool
    {
        $validity = false;
        $stream = new Stream(dirname(__DIR__, 2) . '/static/database/valid_countries.json');
        $valid_countries = json_decode($stream->getContents());
        $iterator = new \ArrayIterator($valid_countries);
        foreach ($iterator as $key) {
            foreach ($key as $value) {
                if ($value == $country) {
                    $validity = true;
                    break;
                }
            }
        }
        return $validity;
    }
}
