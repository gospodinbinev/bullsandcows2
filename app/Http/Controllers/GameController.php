<?php

namespace App\Http\Controllers;

use App\Models\Score;

use Response;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function gameScreen()
    {
        $leaderboard = Score::orderBy('score', 'desc')->take('10')->get();

        return view('welcome', compact('leaderboard'));
    }

    public function start(Request $request)
    {
        if ($request->ajax() && $request->start == "yes") {
            // generates a random 4 digit unique number
            $fourRandomDigit = substr(str_shuffle("0123456789"), 0, 4);
            $request->session()->put('generatedNumber', $fourRandomDigit);

            // If generates a number with the following digits 1,8,4,5 restart the game
            // Because there's no way 1 and 8 can be next to each other 
            // in the same time until 4 and 5 are placed on odd indexes
            if (preg_match('/[1]/', $fourRandomDigit) && preg_match('/[8]/', $fourRandomDigit)
            && preg_match('/[4]/', $fourRandomDigit) && preg_match('/[5]/', $fourRandomDigit)) {
                GameController::start($request);
            }

            // if in use, digits 1 and 8 should be right next to each other
            if (preg_match('/[1]/', $fourRandomDigit) && preg_match('/[8]/', $fourRandomDigit)) {
                $digitsNextToEachOther = [1, 8];
                $otherDigits = [];
                $fourRandomDigitsArray = array_map('intval', str_split($fourRandomDigit));
                $digitsAllTogether = [];

                for ($i = 0; $i < count($fourRandomDigitsArray); $i++) {
                    if ($fourRandomDigitsArray[$i] != 1 && $fourRandomDigitsArray[$i] != 8) {
                        array_push($otherDigits, $fourRandomDigitsArray[$i]);
                        array_push($digitsAllTogether, $fourRandomDigitsArray[$i]);
                    }
                }
                
                shuffle($digitsNextToEachOther);
                $digitsNextToEachOtherShuffled = implode("",$digitsNextToEachOther);  
                array_push($digitsAllTogether, intval($digitsNextToEachOtherShuffled));
                // all digits together last shuffle
                shuffle($digitsAllTogether);
                $digitsAllTogetherLastShuffled = implode("",$digitsAllTogether);

                $request->session()->put('generatedNumber', $digitsAllTogetherLastShuffled);
                return Response($digitsAllTogetherLastShuffled);
            }

            // if in use, digits 4 and 5 shouldn't be on even index / position
            if (preg_match('/[4]/', $fourRandomDigit) && preg_match('/[5]/', $fourRandomDigit)) {
                $oddIndexDigits = [4, 5];
                $otherDigits = [];
                $fourRandomDigitsArray = array_map('intval', str_split($fourRandomDigit));
                $digitsAllTogether = [];

                for ($i = 0; $i < count($fourRandomDigitsArray); $i++) {
                    if ($fourRandomDigitsArray[$i] != 4 && $fourRandomDigitsArray[$i] != 5) {
                        array_push($otherDigits, $fourRandomDigitsArray[$i]);
                    }
                }

                shuffle($oddIndexDigits);
                $allDigitsTogether = $oddIndexDigits[0].''.$otherDigits[0].''.$oddIndexDigits[1].''.$otherDigits[1];
                
                $request->session()->put('generatedNumber', $allDigitsTogether);
                return Response($allDigitsTogether);
            }

            return Response($fourRandomDigit);
            
        }
    }

    public function matchNumber(Request $request)
    {
        if ($request->ajax() && isset($request->enteredNumber)) {
            $enteredNumber = $request->enteredNumber;
            $generatedNumber = session('generatedNumber');

            $output = "";

            $enteredNumberArray = array_map('intval', str_split($enteredNumber));
            $generatedNumberArray = array_map('intval', str_split($generatedNumber));

            $bulls = [];
            $cows = [];

            // calculate bulls
            for ($i = 0; $i < 4; $i++) {
                if ($generatedNumberArray[$i] == $enteredNumberArray[$i]) {
                    array_push($bulls, $generatedNumberArray[$i]);
                }
            }

            // calculate cows 
            for ($c = 0; $c < count($generatedNumberArray); $c++) {
                for ($x = 0; $x < count($enteredNumberArray); $x++) {
                    if ($generatedNumberArray[$c] == $enteredNumberArray[$x]) {
                        array_push($cows, $enteredNumberArray[$x]);
                    }
                }
            }
            
            // take out the bulls :)
            $cows = array_diff($cows, $bulls);

            if (count($bulls) == 4) {
                
                $output.="<tr class='table-success'><td class='roundNum'>".$request->rowNumber."</td><td>".$enteredNumberArray[0].$enteredNumberArray[1].$enteredNumberArray[2].$enteredNumberArray[3]."</td><td><img height='25' src='/images/bull.png'>: ".count($bulls)." | <img height='25' src='/images/cow.png'>: ".count($cows)."</td></tr>";
                
                // save the score in DB
                $score = 1000 - $request->rowNumber;
                $newScore = new Score();
                $newScore->score = $score;
                $newScore->save();

            } else {
                $output.="<tr><td class='roundNum'>".$request->rowNumber."</td><td>".$enteredNumberArray[0].$enteredNumberArray[1].$enteredNumberArray[2].$enteredNumberArray[3]."</td><td><img height='25' src='/images/bull.png'>: ".count($bulls)." | <img height='25' src='/images/cow.png'>: ".count($cows)."</td></tr>";
            }

            return Response($output);
        }
    }
}
