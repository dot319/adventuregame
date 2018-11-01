<?php 

require 'php sections/adventurer.php';
require 'php sections/enemy.php';

// Create player adventurer from database stats
$name = $_SESSION['Username']; 
$$name = new Adventurer($name);

// Create enemy (if player is currently battling one)
if (isset(${$name}->current_enemy)) {
    if (${$name}->current_enemy != 0) {
        $enemy = new Enemy(${$name}->current_enemy);
    }
}

?>

<!---------------------------------- Header ---------------------------------------------->

<div id="header">
    Welcome <?php echo($name); ?> | 
    <a href="loggedout.php">Log out</a>
</div>

<!---------------------------------- Combat ---------------------------------------------->

<div id="combat">

<?php
// If an enemy is lured, stored it in Session
if (isset($_POST['enemy'])) {
    if ($_POST['enemy'] != "") {
        $_SESSION['newEnemy'] = $_POST['enemy'];
    }
}

// If an enemy is stored in Session, create it unless player is already in battle 
if (isset($_SESSION['newEnemy'])) {;
    if ($_SESSION['newEnemy'] != "") {
        if (!isset($enemy)) {
            $_SESSION['message'] = ${$name}->meetNewEnemy("Rat", 1);
            $_SESSION['newEnemy'] = "";
        } else {
            $_SESSION['message'] = "You're already in battle.<br />";
            $_SESSION['newEnemy'] = "";
        }
    }
}

// If player is currently battling an enemy, show its stats
if (isset($enemy)) {
    $enemy->showStats();
}

?>

</div>

<!-------------------------------- Game screen ------------------------------------------->

<div id="game-screen">

<?php 

// Check if button was clicked and call appropriate function, store log in $_SESSION['message']
if (isset($_GET['button'])) {
    if (isset($_POST['button'])) {
        switch ($_POST['button']) {
            case 'work_at_farm':
            $_SESSION['message'] = ${$name}->workAtFarm();
            break;
            case 'rest_at_inn':
            $_SESSION['message'] = ${$name}->restAtInn();
            break;
        }
    }
}

// Print to screen what happened
if (isset($_SESSION['message'])) {
    echo($_SESSION['message']);
}

// Save stats to database
${$name}->saveStats();
if (isset($enemy)) {
    $enemy->saveStats();
}

// Unset $_GET['button'] so refreshing the page won't repeat the last action
if (isset($_GET['button'])) {
    header("location: " . $_SERVER['PHP_SELF']);
}

?>

</div>

<!-------------------------------- Interface ------------------------------------------->

<div id="interface">
    <?php ${$name}->showStats()?>
    <div id="buttons">
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>?button=true" method="POST">
            <input type="hidden" name="button" value="work_at_farm">
            <input type="submit" value="Work at farm">
        </form>
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>?button=true" method="POST">
            <input type="hidden" name="button" value="rest_at_inn">
            <input type="submit" value="Rest at inn">
        </form>
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>?button=true" method="POST">
            <input type="hidden" name="button" value="lure_rat">
            <input type="hidden" name="enemy" value="lvl1_rat">
            <input type="submit" value="Lure rat">
        </form>
    </div>
</div>