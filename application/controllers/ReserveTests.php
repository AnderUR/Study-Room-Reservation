<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class ReserveTests extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/New_York');
        $this->load->library('unit_test');
    }

    function index()
    {
        echo "using tests";
        $reservationHours = array(
            0 => array('roomid' => 28, 'start' => '2017-02-07 09:45:00', 'end' => '2017-02-07 11:15:00'), //fill times between
            1 => array('roomid' => 28, 'start' => '2017-02-07 12:15:00', 'end' => '2017-02-07 13:15:00'),
            2 => array('roomid' => 28, 'start' => '2017-02-07 13:15:00', 'end' => '2017-02-07 14:00:00'),
            3 => array('roomid' => 29, 'start' => '2017-02-07 14:45:00', 'end' => '2017-02-07 17:00:00'),
            4 => array('roomid' => 29, 'start' => '2017-02-07 15:00:00', 'end' => '2017-02-07 16:45:00'),
            5 => array('roomid' => 29, 'start' => '2017-02-07 15:45:00', 'end' => '2017-02-07 18:45:00')
        );

        foreach($reservationHours as $hours) {
            $startTime = new DateTime($hours['start']);
            $endTime = new DateTime($hours['end']);
            $test = $this->compareTimes($startTime->format('H:i'), $endTime->format('H:i'));
            echo $this->unit->run($test, true, "Assert start less than end time");
            $test = $this->divisibleMins($startTime->format('i'), $endTime->format('i'));
            echo $this->unit->run($test, true, "Assert minutes are divisible by 15");
        }
    }

    private function compareTimes($startTimeStr, $endTimeStr) {
        return  $startTimeStr < $endTimeStr;
    }

    private function divisibleMins($startTimeStr, $endTimeStr) {
        $startBool = ($startTimeStr % 15) == 0;
        $endBool = ($endTimeStr % 15) == 0;
        return ($startBool && $endBool);
    }
}
