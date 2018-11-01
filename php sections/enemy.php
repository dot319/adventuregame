<?php

class Enemy {
    public $enemyid;
    public $name;
    public $level;
    public $hp;
    public $maxhp;
    public $attack;
    public $defense;
    public $exp;

    function __construct($enemyid) {
        require 'php sections/connect.php';
        $myQuery = "SELECT * FROM Enemies WHERE `EnemyID`=$enemyid";
        $myResult = $conn->query($myQuery);
        while ($row = $myResult->fetch_assoc()) {
            $this->enemyid = $enemyid;
            $this->name = $row['Name'];
            $this->level = $row['Level'];
            $this->hp = $row['HP'];
            $this->maxhp = $row['MaxHP'];
            $this->attack = $row['Attack'];
            $this->defense = $row['Defense'];
            $this->exp = $row['Exp'];
        }
        $conn->close();
    }

    public function saveStats() {
        require 'php sections/connect.php';
        $myQuery = "UPDATE Enemies 
        SET `HP` = $this->hp, `MaxHP` = $this->maxhp, `Attack` = $this->attack, `Defense` = $this->defense
        WHERE `EnemyID`='$this->enemyid'";
        $conn->query($myQuery);
        $conn->close();
    }

    public function showStats() { ?>

        <div id="enemystats">
            <p>Name: <?php echo("$this->name"); ?></p>
            <p>Level: <?php echo("$this->level"); ?></p>
            <p>HP: <?php echo("$this->hp/$this->maxhp"); ?></p>
            <p>Attack: <?php echo($this->attack); ?></p>
            <p>Defense: <?php echo($this->defense); ?></p>
        </div>
        
    <?php }

    public function attack($player) {
        $defense = 1 * pow(0.98, $player->defense - 1);
        $attack = round($defense * $this->attack);
        if ($attack < 1) {
            $attack = 1;
        }
        $player->hp -= $attack;
        return "$this->name attacked $player->name and dealt $attack damage.<br />";
    }

    public function delete() {
        require 'php sections/connect.php';
        $myQuery = "SELECT * FROM Enemies WHERE `EnemyID` = $this->enemyid";
        $myResult = $conn->query($myQuery);
        if ($myResult->num_rows > 0) {
            $myQuery = "DELETE FROM Enemies WHERE `EnemyID` = $this->enemyid";
            $conn->query($myQuery);
        }
    }

}

?>