<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Ex-students - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body id="members-list">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>Ex-students</h1>
    </header>
    <div class="ws-container" style="overflow-x: auto;">
        <div id="head">
            <div><i class="fas fa-users"></i> <?= $exStudentCount; ?> Ex-students registered</div>
            <div id="search">
                <input type="text" class="form-control" name="search" id="search_members" placeholder="Search Ex-students" />
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <td><i class="fas fa-user"></i> Ex-student</td>
                    <td>Username</td>
                    <td>Batch year</td>
                    <td><i class="fas fa-phone"></i> Phone</td>
                    <td><i class="fas fa-map-marker-alt"></i> Address</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($exStudents as $exstudent) {
                    $exStudentId = $exstudent->getId();
                    $avatar = $exstudent->getAvatar();
                    $username = $exstudent->getUsername();
                    $name = '<a href="/exstudents/' . strtolower($username) . '" target="_blank">' . $exstudent->getName() . '</a>';
                    $batchYear = $exstudent->getBatchYear();
                    $phoneNumber = $exstudent->getPhoneNumber();
                    $address = $exstudent->getAddress();
                ?>
                    <tr>
                        <td><img src="<?= $avatar; ?>" alt="<?= $username; ?>'s profile picture"><?= $name; ?><span><?php if (isset($label)) echo '<span class="label">' . $label . '</span>'; ?></span></td>
                        <td><?= $username; ?></td>
                        <td><?= $batchYear; ?></td>
                        <td>(+237) <?= $phoneNumber; ?></td>
                        <td><?= $address; ?></td>
                    </tr>
                <?php
                    unset($label);
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>