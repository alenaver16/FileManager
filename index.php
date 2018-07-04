<?php
require_once('ManagerFile.php');

$manager = ManagerFile::getInstance();
$manager->managing();

?>

<table border='1'>
    <thead>
    <tr>
        <th><a href="index.php?sortname=<?php echo isset($_GET['sortname']) ? !$_GET['sortname'] : 1;
            $manager->sortByName(); ?>">Name</a></th>
        <th>Size</th>
        <th>Last modified</th>
<!--        <th><a href="index.php?sortsize=--><?php //echo isset($_GET['sortsize']) ? !$_GET['sortsize'] : 1; ?><!--">Size</a></th>-->
<!--        <th><a href="index.php?sortdate=--><?php //echo isset($_GET['sortdate']) ? !$_GET['sortdate'] : 1; ?><!--">Last-->
<!--                modified</a></th>-->
    </tr>
    </thead>
    <tbody>
    <?php
    $manager->drawTableData();
    ?>
    </tbody>
</table>