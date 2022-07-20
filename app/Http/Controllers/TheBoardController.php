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
                'title' => '',
                'location' => '',
                'summary' => '',
                'answered_question' => '',
                'new_question' => '',
            ],
            1 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => '',
                'answered_question' => '',
                'new_question' => '',
            ],
            2 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => 'Airplane allegory',
                'answered_question' => '',
                'new_question' => '',
            ],
            3 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => 'Island allegory',
                'answered_question' => '',
                'new_question' => '',
            ],
            4 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => 'Australia dystopia',
                'answered_question' => '',
                'new_question' => '',
            ],
            5 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => 'Australia',
                'answered_question' => '',
                'new_question' => '',
            ],
            6 => 
            [
                'title' => '', 
                'location' => '',
                'summary' => '',
                'answered_question' => '',
                'new_question' => '',
            ],
            7 => 
            [
                'title' => 'Denouement', 
                'location' => '',
                'summary' => '',
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
