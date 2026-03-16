<?php
session_start();
require "init.php";

$message = "";

// hláška ze session
if (isset($_SESSION["message"])) {
    $message = $_SESSION["message"];
    unset($_SESSION["message"]);
}

// PŘIDÁNÍ
if (isset($_POST["add"])) {

    $name = trim($_POST["name"]);

    if ($name == "") {
        $_SESSION["message"] = "Pole nesmí být prázdné.";
    } else {

        $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");

        try {
            $stmt->execute([$name]);
            $_SESSION["message"] = "Zájem byl přidán.";
        } catch (PDOException $e) {
            $_SESSION["message"] = "Tento zájem už existuje.";
        }
    }

    header("Location: index.php");
    exit;
}

// MAZÁNÍ
if (isset($_GET["delete"])) {

    $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
    $stmt->execute([$_GET["delete"]]);

    $_SESSION["message"] = "Zájem byl odstraněn.";

    header("Location: index.php");
    exit;
}

// EDITACE
if (isset($_POST["edit"])) {

    $id = $_POST["id"];
    $name = trim($_POST["name"]);

    if ($name == "") {
        $_SESSION["message"] = "Pole nesmí být prázdné.";
    } else {

        $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");

        try {
            $stmt->execute([$name, $id]);
            $_SESSION["message"] = "Zájem byl upraven.";
        } catch (PDOException $e) {
            $_SESSION["message"] = "Tento zájem už existuje.";
        }
    }

    header("Location: index.php");
    exit;
}

// načtení zájmů
$stmt = $db->query("SELECT * FROM interests");
$interests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>IT Profil</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

<h1>Moje zájmy</h1>

<?php if ($message): ?>
<div class="message"><?= $message ?></div>
<?php endif; ?>

<h3>Přidat zájem</h3>

<form method="post">
<input type="text" name="name" placeholder="Např. programování">
<button type="submit" name="add">Přidat</button>
</form>

<h3>Seznam zájmů</h3>

<?php if (empty($interests)): ?>
<p>Žádné zájmy zatím nejsou přidány.</p>
<?php endif; ?>

<?php foreach ($interests as $i): ?>

<div class="interest">

<form method="post">

<input type="hidden" name="id" value="<?= $i["id"] ?>">

<input type="text" name="name" value="<?= htmlspecialchars($i["name"]) ?>">

<button type="submit" name="edit">Upravit</button>

<a class="delete" href="?delete=<?= $i["id"] ?>">Smazat</a>

</form>

</div>

<?php endforeach; ?>

</div>

</body>
</html>