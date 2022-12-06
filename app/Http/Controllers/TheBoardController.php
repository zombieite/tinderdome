<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Log;

class TheBoardController extends Controller
{
    public function the_board()
    {
        $number_minutes = isset($_GET['number_minutes']) ? $_GET['number_minutes'] : 110;
        $chapter_specifics =
        [
            0 => 
            [
                'title' => 'Norwood',
                'location' => 'Norwood',
                'summary' => "Building cars. But wait, we need to stop the lines again. Hundreds of people who would prefer to be working have to fight off the Hellies and Gillies. FB groans, is it the communists or the fascists? Laura says I can't remember which is which. FB mentions at least they hate each other so much that they will never team up. No DT to be seen, and DT is their best pilot, so the battles haven't been going so well lately. All the other pilots refuse to fly Easy Money, it's too big and dangerous. And they're almost out of ammo.",
                'answered_question' => '',
                'new_question' => '',
            ],
            1 => 
            [
                'title' => '', 
                'location' => 'Norwood',
                'summary' => "FB goes to bar with Laura. CD is there drunk and crippled. CD makes a pathetic attempt to kill FB and ends up unconscious choking on his puke. At first FB refuses to save him. Laura points out that she wanted to kill FB once too but now they are best friends. She demands help rolling CD over. FB helps grudgingly. Laura says they can't just leave him. FB says as soon as he wakes up he's gonna try to kill me again. They take him back to the factory and debate what do with him. FB doesn't want to lock him up as a prisoner but he also can't let him get sober enough to successfully kill him so he keeps giving him alcohol. FB is watching recording of the moon landing. He says he wants to go to the moon and needs DT to fly the rocket since she's his best pilot. Laura says the last person to say that got assassinated.",
                'answered_question' => '',
                'new_question' => '',
            ],
            2 => 
            [
                'title' => '', 
                'location' => 'Plane to Australia',
                'summary' => "Airplane allegory",
                'answered_question' => '',
                'new_question' => '',
            ],
            3 => 
            [
                'title' => '', 
                'location' => 'Unknown Pacific island',
                'summary' => "Island allegory",
                'answered_question' => '',
                'new_question' => '',
            ],
            4 => 
            [
                'title' => '', 
                'location' => 'Australia',
                'summary' => "Australia dystopia",
                'answered_question' => '',
                'new_question' => '',
            ],
            5 => 
            [
                'title' => '', 
                'location' => 'Australia',
                'summary' => "Australia",
                'answered_question' => '',
                'new_question' => '',
            ],
            6 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => "",
                'answered_question' => '',
                'new_question' => '',
            ],
            7 => 
            [
                'title' => 'Denouement', 
                'location' => '',
                'summary' => "",
                'answered_question' => '',
                'new_question' => '',
            ],
        ];
        $acts = 
        [
            [ 
                'name' => 'Act 1: Thesis',
                'chapters' => 
                [
                    0 => 
                    [
                        'name' => 'name',
                        'percent_start' => 0,
                        'percent_end' => 10,
                        'beats' => 'Opening Image, Mirror Of Final Image, Theme Hinted At, This Is Gonna Be Good, Everyday Life, 6 Things That Need Fixing, Introduce Running Gags, Introduce A-Story Characters Who Are Mirrors Of B-Story Characters, All Characters Have Primal Motivations, Save The Cat',
                    ],
                    1 => 
                    [
                        'name' => 'name',
                        'percent_start' => 11,
                        'percent_end' => 22,
                        'beats' => 'Catalyst & Debate',
                    ],
                ],
            ],
            [
                'name' => 'Act 2: Antithesis',
                'chapters' =>
                [
                    2 => 
                    [
                        'name' => 'name',
                        'percent_start' => 23,
                        'percent_end' => 26,
                        'beats' => 'Break Into Two, Hero Embarks On Adventure',
                    ],
                    3 => 
                    [
                        'name' => 'name',
                        'percent_start' => 27,
                        'percent_end' => 49,
                        'beats' => 'Narrative Digression, B-Story Or Love Story, Introduce B-Story Characters Who Are Mirrors Of A-Story Characters, Fun & Games, The Promise Of The Premise, Set Pieces, Then... Midpoint, Mirror Of All Is Lost, False Victory Or False Defeat',
                    ],
                ]
            ],
            [
                'name'   => 'Midpoint',
                'chapters' =>
                [
                    4 => 
                    [
                        'name' => 'name',
                        'percent_start' => 50,
                        'percent_end' => 67,
                        'beats' => 'Stakes Are Raised, Bad Guys Close In, Then... All Is Lost, Mirror Of Midpoint, False Defeat Or False Victory, Whiff Of Death',
                    ],
                    5 => 
                    [
                        'name' => 'name',
                        'percent_start' => 68,
                        'percent_end' => 76,
                        'beats' => 'Dark Night Of The Soul, Wallowing In Hopelessness And Sadness And Anger',
                    ],
                ]
            ],
            [
                'name'   => 'Act 3: Synthesis',
                'chapters' => 
                [
                    6 => 
                    [
                        'name' => 'name',
                        'percent_start' => 77,
                        'percent_end' => 98,
                        'beats' => 'Narrative Digression, Break Into Three, Fusion Of A And B Stories, Climax, Change The World',
                    ],
                    7 => 
                    [
                        'name' => 'name',
                        'percent_start' => 99,
                        'percent_end' => 100,
                        'beats' => 'Denouement, Final Image, Mirror Of Opening Image, All Arcs Are Complete, All Loose Ends Tied Up Except For The One New Question',
                    ],
                ]
            ],
        ];
        return view('the_board', [ 
            'number_minutes' => $number_minutes,
            'acts' => $acts, 
            'chapter_specifics' => $chapter_specifics,
        ]);
    }
}
