<?php

namespace Mithos\Item;

class Util {

	public static function getExcellentsName($section, $index) {
		$type = 0;
		if (in_array($section, array(0, 1, 2, 3, 4, 5))) {
			$type = 1;
		} elseif (in_array($section, array(6, 7, 8, 9, 10, 11))) {
            $type = 2;  
        } elseif($section == 12) {
            if ($index >= 3 && $index <= 6) {
            	$type = 3; //Asas level 2
            }
            if ($index >= 36 && $index <= 43) {
            	$type = 7; //Asas level 3
            }
            if ($index >= 130 && $index <= 134) {
            	$type = 3; //Small Asas
            }
        } elseif($section == 13) {
            if ($index >= 8 && $index <= 9) {
            	$type = 5; //Rings
            }
            if ($index >= 12 && $index <= 13) {
            	$type = 6; //Pendants
            }
            if ($index >= 20 && $index <= 24) {
            	$type = 5; //Rings
            }
            if ($index >= 25 && $index <= 28) {
            	$type = 6; //Pendants
            }
            if ($index >= 33) {
            	$type = 3; //Capa Dark Lord
            }
            if ($index >= 37) {
            	$type = 4; //Fenrir
            }
        }

        $options = array();
        switch($type) {
            case 0: 
                $options = array(
                    'name' => 'No effect',
                    'opt0' => 'No effect',
                    'opt1' => 'No effect',
                    'opt2' => 'No effect',
                    'opt3' => 'No effect',
                    'opt4' => 'No effect',
                    'opt5' => 'No effect'
                );
                break;
            case 1: 
                $options = array(
                    'name' => 'EXE WEAPONS',
                    'opt0' => 'Increases recovery rate of mana (Mana / 8)',
                    'opt1' => 'Increases recovery rate of life (Life / 8)',
                    'opt2' => 'Increase the speed of magical damage +7',
                    'opt3' => 'Increase magical damage +1%',
                    'opt4' => 'Increase magic damage + level/20',
                    'opt5' => 'Success in excellent damage +10%'
                );
                break; 
            case 2: 
                $options = array(
                    'name' => 'EXE SETS',
                    'opt0' => 'Increased rate of acquisition of Zen +40%',
                    'opt1' => 'Success of Defense +10%',
                    'opt2' => 'Mirrors in 5% Damage Received',
                    'opt3' => 'Lowers the damage by +4%',
                    'opt4' => 'Increase mana +4%',
                    'opt5' => 'Increases in life +4%'
                );
                break;
            case 3: 
                $options = array(
                    'name' => 'EXE WINGS',
                    'opt0' => 'Life increased by +125',
                    'opt1' => 'Mana increased by +125',
                    'opt2' => 'Defense of the opponent ignored by 3%',
                    'opt3' => 'Stamina increased by +50',
                    'opt4' => 'Increase the speed of magic damage to +5',
                    'opt5' => 'No effect'
                );
                break; 
            case 4: 
                $options = array(
                    'name' => 'EXE FENRIR',
                    'opt0' => '+Damage',
                    'opt1' => '+Defense',
                    'opt2' => '+Illusion',
                    'opt3' => 'No effect',
                    'opt4' => 'No effect',
                    'opt5' => 'No effect'
                );
                break;
            case 5: 
                $options = array(
                    'name' => 'RINGS',
                    'opt0' => 'Increases zens who fall into +40%',
                    'opt1' => '+10% Defensive success rank',
                    'opt2' => 'Returns the blow +5%',
                    'opt3' => 'Received low blow +4%',
                    'opt4' => 'Increase mana +4%',
                    'opt5' => 'Increases in life +4%'
                );
                break; 
            case 6: 
                $options = array(
                    'name' => 'PENDANTS',
                    'opt0' => 'Increase mana after killing monster + mana / 8',
                    'opt1' => 'Increases life after killing monster + life / 8',
                    'opt2' => 'Increases attack speed +7',
                    'opt3' => 'Adds +2% damage',
                    'opt4' => '+ Increases damage level/20',
                    'opt5' => '+10% Defensive success rank'
                );
                break; 
            case 7: 
                $options = array(
                    'name' => 'EXE WINGS S4',
                    'opt0' => 'Ignore the Power of Defensive Opponent 5%',
                    'opt1' => '5% Chance to return the damage',
                    'opt2' => '5% Chance of a lifetime to recover',
                    'opt3' => '5% Chance to recover all mana',
                    'opt4' => 'No effect',
                    'opt5' => 'No effect'
                );
                break; 
        }

        return $options;
    }

}