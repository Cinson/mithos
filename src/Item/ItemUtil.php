<?php

namespace Mithos\Item;

class ItemUtil {

	public static function getExcellentsName($section, $index) {
		$type = 0;
		if (in_array($section, [0, 1, 2, 3, 4, 5])) {
			$type = 1;
		} elseif (in_array($section, [6, 7, 8, 9, 10, 11])) {
            $type = 2;  
        } elseif ($section == 12) {
            if ($index >= 3 && $index <= 6) {
            	$type = 3; //Asas level 2
            }
            if ($index >= 36 && $index <= 43) {
            	$type = 7; //Asas level 3
            }
            if ($index >= 130 && $index <= 134) {
            	$type = 3; //Small Asas
            }
        } elseif ($section == 13) {
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
                $options = [
                    'name' => 'No effect',
                    'options' => [
                        'No effect',
                        'No effect',
                        'No effect',
                        'No effect',
                        'No effect',
                        'No effect'
                    ]
                ];
                break;
            case 1: 
                $options = [
                    'name' => 'EXE WEAPONS',
                    'options' => [
                        'Increases recovery rate of mana (Mana / 8)',
                        'Increases recovery rate of life (Life / 8)',
                        'Increase the speed of magical damage +7',
                        'Increase magical damage +1%',
                        'Increase magic damage + level/20',
                        'Success in excellent damage +10%'
                    ]
                ];
                break; 
            case 2: 
                $options = [
                    'name' => 'EXE SETS',
                    'options' => [
                        'Increased rate of acquisition of Zen +40%',
                        'Success of Defense +10%',
                        'Mirrors in 5% Damage Received',
                        'Lowers the damage by +4%',
                        'Increase mana +4%',
                        'Increases in life +4%'
                    ]
                ];
                break;
            case 3: 
                $options = [
                    'name' => 'EXE WINGS',
                    'options' => [
                        'Life increased by +125',
                        'Mana increased by +125',
                        'Defense of the opponent ignored by 3%',
                        'Stamina increased by +50',
                        'Increase the speed of magic damage to +5',
                        'No effect'
                    ]
                ];
                break; 
            case 4: 
                $options = [
                    'name' => 'EXE FENRIR',
                    'options' => [
                        '+Damage',
                        '+Defense',
                        '+Illusion',
                        'No effect',
                        'No effect',
                        'No effect'
                    ]
                ];
                break;
            case 5: 
                $options = [
                    'name' => 'RINGS',
                    'options' => [
                        'Increases zens who fall into +40%',
                        '+10% Defensive success rank',
                        'Returns the blow +5%',
                        'Received low blow +4%',
                        'Increase mana +4%',
                        'Increases in life +4%'
                    ]
                ];
                break; 
            case 6: 
                $options = [
                    'name' => 'PENDANTS',
                    'options' => [
                        'Increase mana after killing monster + mana / 8',
                        'Increases life after killing monster + life / 8',
                        'Increases attack speed +7',
                        'Adds +2% damage',
                        '+ Increases damage level/20',
                        '+10% Defensive success rank'
                    ]
                ];
                break; 
            case 7: 
                $options = [
                    'name' => 'EXE WINGS S4',
                    'options' => [
                        'Ignore the Power of Defensive Opponent 5%',
                        '5% Chance to return the damage',
                        '5% Chance of a lifetime to recover',
                        '5% Chance to recover all mana',
                        'No effect',
                        'No effect'
                    ]
                ];
                break; 
        }

        return $options;
    }

    public static function getRefineName($index) {
        if ($index >= 0 && $index <= 6) {
            $type = 6;
        } elseif ($index == 7) {
            $type = 1;
        } elseif ($index == 8) {
            $type = 2;
        } elseif ($index == 9) {
            $type = 3;
        } elseif ($index == 10) {
            $type = 4;
        } elseif ($index == 11) {
            $type = 5;
        } else {
            $type = 0;
        }

        $options = [];
        switch ($type) {
            case 0:
                $options = [
                    'name' => 'No option',
                    'options' => [
                        'No Refinery 1&deg; Options',
                        'No Refinery 2&deg; Options'
                    ]
                ];
                break;
            case 1:
                $options = [
                    'name' => 'Helms',
                    'options' => [
                        'SD Recovery Rate +20',
                        'Defense Success Rate +10'
                    ]
                ];
                break;
            case 2:
                $options = [
                    'name' => 'Armors',
                    'options' => [
                        'SD Auto Recovery',
                        'Def Success Rate +10'
                    ]
                ];
                break;
            case 3:
                $options = [
                    'name' => 'Pants',
                    'options' => [
                        'Def Skill +200',
                        'Def Success Rate +10'
                    ]
                ];
                break;
            case 4:
                $options = [
                    'name' => 'Gloves',
                    'options' => [
                        'Max HP +200',
                        'Def Success Rate +10',
                    ]
                ];
                break;
            case 5:
                $options = [
                    'name' => 'Boots',
                    'options' => [
                        'Max SD +700',
                        'Def Success Rate +10'
                    ]
                ];
                break;
            case 6:
                $options = [
                    'name' => 'Weapons',
                    'options' => [
                        'Additional Dmg +200',
                        'Pow Success Rate +10'
                    ]
                ];
                break;
        }

        return $options;
    }
}