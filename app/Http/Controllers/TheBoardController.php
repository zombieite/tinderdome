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
                'summary' => "Christmas eve. Snow. Firebird oversees the process of building cars in a postapocalyptic Pontiac Firebird factory. Capitalism channels the innate human desire for status out of politics and into making money. Firebird interviews a mechanic and pilot named Greg Gory. Firebird's friend and pilot Dorktown is missing. Greg Gory refuses the job but gives Firebird a piece of gum that is a clue to Dorktown's whereabouts.", 
                'answered_question' => '',
                'new_question' => '',
            ],
            1 => 
            [
                'title' => '', 
                'location' => 'Norwood',
                'summary' => "Firebird has to stop the assembly line again. Hundreds of people who would prefer to be working have to fight off the Hellies (fascists) or the Gillies (communists). No Dorktown to be seen, and Dorktown is the only one who can pilot the war helicopter Easy Money. The factory owners and workers are victorious, but it is a pyrrhic victory. It is clear one more battle of this scale will destroy the factory for good.", 
                'answered_question' => '',
                'new_question' => '',
            ],
            2 => 
            [
                'title' => '', 
                'location' => 'Route 66',
                'summary' => 'Firebird discovers Dorktown may have been kidnapped. She may be in Australia. They tell him Australia is a big country but he heads to LA via Route 66.',
                'answered_question' => '',
                'new_question' => '',
            ],
            3 => 
            [
                'title' => '', 
                'location' => 'Unknown Pacific island',
                'summary' => "Firebird gets on a jet in LA. Airplane allegory. Like Snowpiercer. The people on the airplane do a communist takeover and only manage to crash the plane. It turns out it takes skill to fly a plane and accomplish the other tasks necessary to successfully complete a flight.",
                'answered_question' => '',
                'new_question' => '',
            ],
            4 => 
            [
                'title' => '', 
                'location' => 'Australia',
                'summary' => "Island allegory. Finding food and fresh water. Building shelters. The people on the deserted island propose communism. Firebird proposes capitalism. They debate and vote. The people vote for communism and it fails. How does Firebird get off the island and to Australia? Maybe a passing boat. Maybe they were actually really close to Australia all along and a canoe can get them the rest of the way.",
                'answered_question' => '',
                'new_question' => '',
            ],
            5 => 
            [
                'title' => '', 
                'location' => 'Australia',
                'summary' => "Firebird discovers that Australia is now a dystopia. What's Left O'Sydney is now run by Dorktown! It is now a high-tech-neo-cyber-crony-capitalist hellscape called Dorktowntown. In Dorktowntown they manufacture Gumtreegum. Gumtreegum is an addictive gum that causes people to behave like good little exploited capitalist slaves.",
                'answered_question' => '',
                'new_question' => '',
            ],
            6 => 
            [
                'title' => '', 
                'location' => 'Australia',
                'summary' => "Firebird uses the rhetorical tricks he learned on the plane and on the island to get people off their gum addictions and rile them up into a communist revolution that destroys Dorktown's dystopia. This brings him face-to-face with Dorktown, before, during, or after a battle. She says 'I'm not your girlfriend. I'm not your daughter. I'm not your doll. I'm not your protege. Stop trying to save me or fix me or reinvent me.'",
                'answered_question' => '',
                'new_question' => '',
            ],
            7 => 
            [
                'title' => 'Denouement', 
                'location' => 'USA',
                'summary' => "Somehow, Firebird and Dorktown reconcile and head off into the sunset on their next adventure.",
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
                        'beats' => "Firebird's Narrative Digression, B-Story Or Love Story, Introduce B-Story Characters Who Are Mirrors Of A-Story Characters, Fun & Games, The Promise Of The Premise, Set Pieces, Leading Up To... Midpoint, Mirror Of All Is Lost, False Victory Or False Defeat",
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
                        'beats' => 'Stakes Are Raised, Bad Guys Close In, Leading Up To... All Is Lost, Mirror Of Midpoint, False Defeat Or False Victory, Whiff Of Death',
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
                        'beats' => "Firebird's Narrative Digression, Break Into Three, Fusion Of A And B Stories, Climax, Change The World",
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
