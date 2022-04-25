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
    protected $table = 'members'; // Members' database table

    public function __construct(Connector $connector)
    {
        $this->setConnector($connector);
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

    public function is_logged_in()
    {
        if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] == true) {
            return true;
        } else {
            return false;
        }
    }

    public function login(RequestInterface $request)
    {
        $response = $this->Authenticate($request);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $userInfo = json_decode($response->getBody()->getContents());
            // Log in the user
            $_SESSION['id'] = $userInfo->ID;
            $_SESSION['firstname'] = $userInfo->firstname;
            $_SESSION['lastname'] = $userInfo->lastname;
            $_SESSION['username'] = $userInfo->username;
            $_SESSION['level'] = $userInfo->level;
            $_SESSION['LoggedIn'] = true;
            return true;
        } else {
            return "Invalid username or password";
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
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

    public function signup($userInfo)
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

    public function getInfo($id)
    {
        $userInfo = [];
        $res = $this->connector->getConnection()->query('SELECT * FROM members WHERE ID = ' . $id);
        $res = $res->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $key => $value) {
                if ($key == "password") {
                    continue;
                }
                $userInfo[$key] = $value;
            }
        }
        $res = $this->connector->getConnection()->query('SELECT name FROM profile_pictures WHERE memberID=' . $userInfo['ID']);
        $userInfo['picture'] = (count($res->fetchAll(\PDO::FETCH_ASSOC)) > 0) ? '/members/profile_pictures/' . $res->fetch(\PDO::FETCH_ASSOC)['name'] . '.jpg' : '/static/images/graphics/profile-placeholder.png';
        return $userInfo;
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
