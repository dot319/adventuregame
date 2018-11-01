<?php 

class Adventurer {
    public $advid;
    public $name;
    public $hp;
    public $maxhp;
    public $currency;
    public $attack;
    public $defense;
    public $current_enemy;

    function __construct($name) {
        $this->name = $name;        
        require 'php sections/connect.php';
        $myQuery = "SELECT * FROM Adventurers WHERE `Name`='$name'";
        $myResult = $conn->query($myQuery);
        while ($row = $myResult->fetch_assoc()) {
            $this->advid = $row['AdvID'];
            $this->hp = $row['HP'];
            $this->maxhp = $row['MaxHP'];
            $this->currency = $row['Currency'];
            $this->attack = $row['Attack'];
            $this->defense = $row['Defense'];
            $this->current_enemy = $row['Current_enemy'];
        }
        $conn->close();
    }

    public function saveStats() {
        require 'php sections/connect.php';
        $myQuery = "UPDATE Adventurers 
        SET `HP` = $this->hp, `MaxHP` = $this->maxhp, `Currency` = $this->currency, `Attack` = $this->attack, `Defense` = $this->defense, `Current_enemy` = $this->current_enemy
        WHERE `Name`='$this->name'";
        $conn->query($myQuery);
        $conn->close();
        echo("$this->name's gamestate saved.<br />");
    }

    public function showStats() { ?>

        <div id="playerstats">
            <p>Name: <?php echo("$this->name"); ?></p>
            <p>HP: <?php echo("$this->hp/$this->maxhp"); ?></p>
            <p>Currency: <?php echo($this->currency); ?></p>
            <p>Attack: <?php echo($this->attack); ?></p>
            <p>Defense: <?php echo($this->defense); ?></p>
            <p>Current enemy id: <?php echo($this->current_enemy); ?></p>
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

    public function attack($enemy) {
        // Get enemy defense
        $defense = 1 * pow(0.09, $enemy->defense - 1);
        $attack = round($defense * $this->attack);
        if ($attack < 1) {
            $attack = 1;
        }
        $enemy->hp -= $attack;
        if ($enemy->hp <= 0) {
            $this->current_enemy = 0;
            return "$this->name killed $enemy->name. Current enemy id is $this->current_enemy.<br />";
        } else {
            return "$this->name attacked $enemy->name and dealt $this->attack damage.<br />";
        }        
    }

    public function meetNewEnemy($name, $level) {
        //Connect to database
        require 'php sections/connect.php';

        //Use $name to get data from Enemy_Templates
        $myQuery = "SELECT * FROM Enemy_Templates WHERE `Name`='$name'";
        $myResult = $conn->query($myQuery);
        while ($row = $myResult->fetch_assoc()) {
            $tempAttack = $row['Attack'];
            $tempDefense = $row['Defense'];
            $tempHP = $row['HP'];
        }

        //Use data + level to calculate HP, Attack, Defense and Exp
        $multiplier = 1 * pow(1.3, $level - 1);
        $enAttack = round($multiplier * $tempAttack);
        $enDefense  = round($multiplier * $tempDefense);
        $enHP = round($multiplier * $tempHP);
        $enExp = $enAttack + $enDefense + $enHP;

        //Store in database
        $myQuery = "INSERT INTO Enemies (`UserID`, `Name`, `Level`, `HP`, `MaxHP`, `Attack`, `Defense`, `Exp`)
        VALUES ('$this->advid', '$name', '$level', '$enHP', '$enHP', '$enAttack', '$enDefense', '$enExp')";
        $conn->query($myQuery);

        //Get enemy ID from database and set as current_enemy
        $myQuery = "SELECT `EnemyID` FROM Enemies 
        WHERE `UserID`='$this->advid' 
        ORDER BY `EnemyID` DESC
        LIMIT 1";
        $myResult = $conn->query($myQuery);
        while ($row = $myResult->fetch_assoc()) {
            $enID = $row['EnemyID'];
        }
        $this->current_enemy = $enID;

        //Close connection
        $conn->close();
        return "Met $name level $level.<br /> Stat multiplier is " . $multiplier . "<br /> New enemy with id $enID uploaded to database.<br />";
    }

}

?>