<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title><?= ucfirst(strtolower($exstudent->getUsername())) ?> - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/pages/account.js"></script>
    <script src="/js/picture-upload.js" defer></script>
</head>

<body id="account">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <div class="ws-container">
        <div id="account-nav">
            <div class="user-heading">
                <a href="#"><img src="<?= $exstudent->getAvatar(); ?>" alt="<?= $exstudent->getUsername(); ?>'s profile picture" /></a>
                <h3><?= (string) $exstudent->getName(); ?></h3>
                <p>
                    <span><?= $exstudent->getEmailAddress(); ?></span>
                    <span>(+237) <?= $exstudent->getPhoneNumber(); ?></span>
                </p>
            </div>
            <nav>
                <ul>
                    <li class="tablink"><span id="tabBtn1"><i class="fas fa-user"></i>Profile</span></li>
                    <?php if (!$guest) : ?><li class="tablink"><span id="tabBtn2"><i class="fas fa-envelope-open-text"></i>Messages</span></li><?php endif; ?>
                    <?php if (!$guest) : ?><li class="tablink"><span id="tabBtn3"><i class="fas fa-user-cog"></i>Settings</span></li><?php endif; ?>
                </ul>
            </nav>
        </div>
        <div>
            <div class="tabcontent" id="profile">
                <div class="panel" id="profile-details">
                    <h3>Details</h3>
                    <div>
                        <label>Name</label>
                        <span><?= (string) $exstudent->getName(); ?></span>
                    </div>
                    <div>
                        <label>Username</label>
                        <span><?= $exstudent->getUserName(); ?></span>
                    </div>
                    <div>
                        <label>Email</label>
                        <span><?= $exstudent->getEmailAddress(); ?></span>
                    </div>
                    <div>
                        <label>Residing country</label>
                        <span><?= $exstudent->getAddress()->getCountry(); ?></span>
                    </div>
                    <div>
                        <label>Residing city</label>
                        <span><?= $exstudent->getAddress()->getCity(); ?></span>
                    </div>
                    <div>
                        <label>Phone number</label>
                        <span><?= $exstudent->getPhoneNumber(); ?></span>
                    </div>
                    <div>
                        <label>Batch year</label>
                        <span><?= $exstudent->getBatchYear(); ?></span>
                    </div>
                    <div>
                        <label>Orientation</label>
                        <span><?= $exstudent->getOrientation()->value; ?></span>
                    </div>
                </div>
                <div class=" panel">
                    <h3>About me</h3>
                    <p><?= $exstudent->getDescription(); ?></p>
                </div>
                <div class="panel-group">
                    <div class="panel">
                        <h3><i class="fas fa-user-cog"></i>Account</h3>
                        <ul class="list">
                            <li><label>Status</label><span><?= $exstudent->getStatus()->label(); ?></span></li>
                            <?php if ($guest && isset($lastSession)) : ?><li><label>Last session at</label><span><?= $lastSession; ?></span></li><?php endif; ?>
                            <li><label>Member since</label><span><?= $exstudent->getRegistrationDate()->format("l j F") . " at " . $exstudent->getRegistrationDate()->format("g:m a"); ?></span></li>
                        </ul>
                    </div>
                    <div class="panel">
                        <h3><i class="fas fa-award"></i>Education</h3>
                        <ul class="list">
                            <?php $twoYearsBack = (int) $exstudent->getBatchYear() - 2; ?>
                            <li>
                                <label>Studied at</label>
                                <span>La Cadenelle Bilingual High School</span>
                            </li>
                            <li>
                                <label>Studied from</label>
                                <span>September <?= $twoYearsBack; ?> - June <?= $exstudent->getBatchYear(); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Chats tab -->
            <?php if (!$guest) : ?>
                <div class="tabcontent" id="messages">
                    <div class="panel">
                        <div class="chat-window">
                            <div class="users-panel">
                                <div class="exstudent-search-field">
                                    <input type="text" class="exstudent-search" placeholder="Search Ex-students by names" />
                                </div>
                                <ul class="users">
                                    <?php foreach ($chat_users as $chat_user) : ?>
                                        <li class="user" data-requestingExstudentId="<?= $exstudent->getId(); ?>" data-targetExstudentId="<?= $chat_user['id']; ?>">
                                            <div>
                                                <img src="<?= $chat_user['avatar']; ?>" />
                                                <span class="state <?= strtolower($chat_user['state']); ?>"></span>
                                            </div>
                                            <div>
                                                <span class="username"><?= (string) $chat_user['name']; ?></span>
                                                <span class="time"><?= $chat_user['lastSeen']; ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="chat-room">
                                <div class="correspondent">
                                    <div class="hamburger-icon">
                                        <div class="bars"></div>
                                    </div>
                                    <img src="/images/graphics/profile-placeholder.png" />
                                    <div>
                                        <span></span>
                                        <span class="state"></span>
                                    </div>
                                </div>
                                <div class="chat">
                                    <div id="alert"><span>No chat</span></div>
                                </div>
                                <form class="input">
                                    <input type="hidden" name="action" value="postMessage" />
                                    <input type="hidden" name="chat-message-sender" id="chat-message-sender" value="<?= $exstudent->getId(); ?>" />
                                    <input type="hidden" name="chat-message-receiver" id="chat-message-receiver" value="" />
                                    <textarea name="chat-message" placeholder="Type a message"></textarea>
                                    <button><img src="/images/graphics/send.png" alt="send"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Profile settings tab -->
            <?php if (!$guest) : ?>
                <div class="tabcontent" id="settings">
                    <div class="panel">
                        <h3><i class="fas fa-cog"></i>Profile settings</h3>
                        <button>Update account picture</button>
                        <form action="/services/profile.php" method="POST" id="profile-editor">
                            <div>
                                <label for="firstname">First name</label>
                                <input type="text" id="firstname" name="firstname" value="<?= $exstudent->getName()->getFirstname(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="lastname">Last name</label>
                                <input type="text" id="lastname" name="lastname" value="<?= $exstudent->getName()->getLastname(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="<?= $exstudent->getUserName(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?= $exstudent->getEmailAddress(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" value="<?= $exstudent->getAddress()->getCountry(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" value="<?= $exstudent->getAddress()->getCity(); ?>" class="form-control" />
                            </div>
                            <div>
                                <label for="contact">Phone number</label>
                                <input class="form-control phone_number" value="<?= $exstudent->getPhoneNumber(); ?>" name="contact" />
                            </div>
                            <div>
                                <label for="batch_year">Batch year</label>
                                <input type="number" id="batch_year" name="batch_year" value="<?= $exstudent->getBatchYear(); ?>" class="form-control" disabled />
                            </div>
                            <div>
                                <label for="biography">Biography</label>
                                <textarea id="biography" name="biography" class="form-control"></textarea>
                            </div>
                            <input type="hidden" name="exStudentId" value="<?= $exstudent->getId(); ?>" />
                            <input type="hidden" name="action" value="editProfile" />
                            <div><button type="submit" class="full-width">Save</button></div>
                        </form>
                    </div>
                    <div class="panel">
                        <h3>Change password</h3>
                        <div>
                            <label for="password">New password</label>
                            <input type="password" id="password" name="password" class="form-control" />
                        </div>
                        <div>
                            <label for="password">Confirm password</label>
                            <input type="password" id="password" name="password" class="form-control" />
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!$guest) : ?>
        <!-- Upload profile picture -->
        <div class="modal-window blurred-background" id="picture-upload-modal">
            <span class="fas fa-times exit"></span>
            <div class="modal-panel picture-upload-panel">
                <div class="header">
                    <h3>Change Profile Picture</h3>
                </div>
                <div>
                    <div id="picture-preview">
                        <p><i class="fas fa-upload"></i><br />Drop your picture here<br />or<br /><span>browse</span><br /><span style="color: black; font-size: 0.5em; cursor:auto;">Size limit: 3 MB</span></p>
                    </div>
                    <p>Accepted file types: .jpeg and .jpg only</p>
                </div>
                <div>
                    <button>Upload</button>
                    <button>Cancel</button>
                    <form>
                        <input type="file" accept=".jpeg, .jpg" name="picture" />
                        <input type="hidden" name="action" id="action" value="updateAvatar" />
                        <input type="hidden" name="exStudentId" id="exStudentId" value="<?= $exstudent->getId(); ?>" />
                        <input type="hidden" name="username" value="<?= $exstudent->getUsername(); ?>" />
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>