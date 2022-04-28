<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Membership\MemberManager;

$AllMembers = MemberManager::Instance()->getMembers();
$number_members = count($AllMembers);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Members list - CADEXSA</title>
    <?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="members-list">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
    <div class="page-content">
        <div class="page-header">
            <div class="ws-container">
                <h1>Members</h1>
            </div>
        </div>
        <div class="ws-container" style="overflow-x: auto;">
            <div id="head">
                <div><i class="fas fa-users"></i> <?= $number_members; ?> members registered</div>
                <div id="search">
                    <input type="text" class="form-control" name="search" id="search_members" placeholder="Search members" />
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <td><i class="fas fa-user"></i> Member</td>
                        <td>Username</td>
                        <td>Batch</td>
                        <td><i class="fas fa-phone"></i> Phone</td>
                        <td><i class="fas fa-map-marker-alt"></i> Location</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($AllMembers as $member) {
                        $memberID = $member->getID();
                        $avatar = $member->getPicture();
                        $username = $member->getUserName();
                        $name = '<a href="/members/profiles/' . strtolower($username) . '" target="_blank">' . $member->getName() . '</a>';
                        $phone = $member->getContact();
                        $batch = $member->getBatch();
                        $location = $member->getCity() . ', ' . $member->getCountry();
                    ?>
                        <tr>
                            <td><img src="<?= $avatar; ?>" alt="member's profile picture"><?= $name; ?><span><?php if (isset($label)) echo '<span class="label">' . $label . '</span>'; ?></span></td>
                            <td><?= $username; ?></td>
                            <td><?= $batch; ?></td>
                            <td>(+237) <?= $phone; ?></td>
                            <td><?= $location; ?></td>
                        </tr>
                    <?php
                        unset($label);
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>