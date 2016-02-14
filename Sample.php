<?php

//Include the timeline API
require_once 'TimelineAPI/Timeline.php';

//Import the required classes
use TimelineAPI\Pin;
use TimelineAPI\PinLayout;
use TimelineAPI\PinLayoutType;
use TimelineAPI\PinIcon;
use TimelineAPI\PinReminder;
use TimelineAPI\Timeline;

$id = 'PTP158';
$detail = 'For time:
10 Runs
40 Lateral Burpee (Over Barbell)s
30 Power Snatches, 135/95 lbs
20 Squat Cleans, 155/105 lbs
10 Bar Muscle-ups

10 Indoor Laps Should NOT be TNG weight unless you are using Rx Athletes responsible for adding there own weight for Squat Cleans Scale Bar MU with CTB, Pull-Up, Band, Ring Row or JPU Scale Run with Airdyne (40 Calories)';

$key = 'SB71wj6mt3dqxeobo079vbhww9tym09x';
$title = 'B.E. Chipper: '. $id;

//Create some layouts which our pin will use
$reminderlayout = new PinLayout(PinLayoutType::GENERIC_REMINDER, $title, null, null, $detail, PinIcon::REACHED_FITNESS_GOAL);
$pinlayout = new PinLayout(PinLayoutType::GENERIC_PIN, $title, null, null, $detail, PinIcon::REACHED_FITNESS_GOAL);

//echo date_default_timezone_get() . "\n";
$date = new DateTime('now');
//echo $date -> getTimezone() -> getName(). "\n";
//echo $date -> format('Y-m-d-H-i-s') . "\n";
$date -> setTimezone(new DateTimeZone('UTC'));
//echo $date -> format('Y-m-d-H-i-s') . "\n";

//Create a reminder which our pin will push before the event
$reminder = new PinReminder($reminderlayout, $date) ;

//Create the pin
$date = new DateTime('now');
$date -> setTime(6,0);
$date -> add(new DateInterval('P1D'));
//echo $date-> format('Y-m-d-H-i-s') . "\n";
$date -> setTimezone(new DateTimeZone('UTC'));
//echo $date-> format('Y-m-d-H-i-s') . "\n";
$pin = new Pin($id, $date, $pinlayout);

//Attach the reminder
$pin -> addReminder($reminder);

//Push the pin to the timeline
//Timeline::pushPin('sample-userToken', $pin);
//var_dump (Timeline::pushSharedPin($key, ['WOD'], $pin));
var_dump (Timeline::deleteSharedPin($key,$id));
?>
