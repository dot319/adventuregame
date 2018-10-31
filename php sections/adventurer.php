<?php 

class Adventurer {
    public $name;
    public $hp;
    public $maxhp;
    public $currency;
    public $attack;
    public $defense;

    function __construct($name) {
        $this->name = $name;        
        require 'php sections/connect.php';
        $myQuery = "SELECT * FROM Adventurers WHERE `Name`='$name'";
        $myResult = $conn->query($myQuery);
        while ($row = $myResult->fetch_assoc()) {
            $this->hp = $row['HP'];
            $this->maxhp = $row['MaxHP'];
            $this->currency = $row['Currency'];
            $this->attack = $row['Attack'];
            $this->defense = $row['Defense'];
        }
        $conn->close();
    }

    public function saveStats() {
        require 'php sections/connect.php';
        $myQuery = "UPDATE Adventurers 
        SET `HP` = $this->hp, `MaxHP` = $this->maxhp, `Currency` = $this->currency, `Attack` = $this->attack, `Defense` = $this->defense
        WHERE `Name`='$this->name'";
        $conn->query($myQuery);
        $conn->close();
        echo("Gamestate saved.");
    }

    public function showStats() { ?>

<div id="playerstats">
    <p>Name: <?php echo("$this->name"); ?></p>
    <p>HP: <?php echo("$this->hp/$this->maxhp"); ?></p>
    <p>Currency: <?php echo($this->currency); ?></p>
    <p>Attack: <?php echo($this->attack); ?></p>
    <p>Defense: <?php echo($this->defense); ?></p>
</div>

    <?php }

    public function workAtFarm() {
        if ($this->hp >= 5) {
            $this->hp -= 5;
            $this->currency += 5;
            return "$this->name worked at the farm and earned 5 currency.<br />";
        } else {
            return "$this->name wanted to work at the farm, but HP is too low.<br />";
        }
        
    }

    public function restAtInn() {
        if ($this->currency >= 15 && $this->hp < $this->maxhp) {
            $this->hp = $this->maxhp;
            $this->currency -= 15;
            return "$this->name paid 15 currency to rest at the inn. HP is maxed out.<br />";
        } elseif ($this->hp == $this->maxhp) {
            return "$this->name wanted to rest at the inn but HP is already maxed out.<br />";
        } elseif ($this->currency < 15) {
            return "$this->name wanted to rest at the inn but doesn't have enough currency.<br />";
        } 
    }

}

?>