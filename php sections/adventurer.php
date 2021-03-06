<?php 

class Adventurer {
    public $advid;
    public $name;
    public $experience;
    public $nlExperience;
    public $level;
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
            $this->experience = $row['Experience'];
            // $this->level = $this->calculateLevel();
            $this->hp = $row['HP'];
            $this->maxhp = $row['MaxHP'];
            $this->currency = $row['Currency'];
            $this->attack = $row['Attack'];
            $this->defense = $row['Defense'];
            $this->current_enemy = $row['Current_enemy'];
        }
        $this->calculateLevel();
        $conn->close();
    }

    public function calculateLevel() {
        $exp = $this->experience;
        switch (true) {
            case ($exp < 100):
            $this->level = 1;
            $this->nlExperience = 100;
            break;
            case ($exp < 250):
            $this->level = 2;
            $this->nlExperience = 250;
            break;
            case ($exp < 600):
            $this->level = 3;
            $this->nlExperience = 600;
            break;
            case ($exp < 1500):
            $this->level = 4;
            $this->nlExperience = 1500;
            break;
            case ($exp < 4000):
            $this->level = 5;
            $this->nlExperience = 4000;
            break;
            case ($exp < 10000):
            $this->level = 6;
            $this->nlExperience = 10000;
            break;
            case ($exp < 25000):
            $this->level = 7;
            $this->nlExperience = 25000;
            break;
            case ($exp < 60000):
            $this->level = 8;
            $this->nlExperience = 60000;
            break;
            case ($exp < 150000):
            $this->level = 9;
            $this->nlExperience = 150000;
            break;
            case ($exp >= 150000):
            $this->level = 10;
            $this->nlExperience = "Maxlevel";
            break;
        }
    }

    public function saveStats() {
        require 'php sections/connect.php';
        $myQuery = "UPDATE Adventurers 
        SET `HP` = $this->hp, `MaxHP` = $this->maxhp, `Currency` = $this->currency, `Attack` = $this->attack, `Defense` = $this->defense, `Current_enemy` = $this->current_enemy, `Experience` = $this->experience
        WHERE `Name`='$this->name'";
        $conn->query($myQuery);
        $conn->close();
        echo("$this->name's gamestate saved.<br />");
    }

    public function showStats() { ?>

        <div id="playerstats">
            <p>Name: <?php echo("$this->name"); ?></p>
            <p>Level: <?php echo("$this->level"); ?></p>
            <p>HP: <?php echo("$this->hp/$this->maxhp"); ?></p>
            <p>Exp: <?php echo("$this->experience/$this->nlExperience"); ?></p>
            <p>Currency: <?php echo($this->currency); ?></p>
            <p>Attack: <?php echo($this->attack); ?></p>
            <p>Defense: <?php echo($this->defense); ?></p>
        </div>

    <?php }

    public function youDied() {
        $this->hp = 100;
        $this->maxhp = 100;
        $this->currency = 0;
        $this->attack = 1;
        $this->defense = 1;
        $this->current_enemy = 0;
        $_SESSION['message'] = "$this->name died. Game over.<br />";
    }

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
        $defense = 1 * pow(0.98, $enemy->defense - 1);
        $attack = round($defense * $this->attack);
        if ($attack < 1) {
            $attack = 1;
        }
        $enemy->hp -= $attack;
        if ($enemy->hp <= 0) {
            $enemy->delete();
            $this->current_enemy = 0;
            $this->experience += $enemy->exp;
            return "$this->name killed $enemy->name. Gained $enemy->exp XP.<br />";
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