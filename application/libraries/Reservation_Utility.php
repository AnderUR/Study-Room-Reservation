<?php

class Reservation_Utility
{
    public static function processURISegmentDate($segmentDate)
    {
        $dateRange = Reservation_Utility::getDateRange();
        $dateStart = date('Y-m-d', strtotime($dateRange['start']));
        $dateEnd = date('Y-m-d', strtotime($dateRange['end']));

        $isCorrectFormat = date('Y-m-d', strtotime($segmentDate)) === $segmentDate;
        $isInRange = ($segmentDate >= $dateStart) && ($segmentDate <= $dateEnd);

        if ($isCorrectFormat && $isInRange) {
            $routingDate = $segmentDate;
        } else {
            $today = new DateTime();
            $routingDate = $today->format('Y-m-d');
        }

        return $routingDate;
    }

    public static function getDateRange()
    {
        $addDays = 'P1D';
        $range = array();
        $date = new DateTime();

        $range['start'] = $date->format('m/d');
        $date->add(new DateInterval($addDays));
        $range['end'] = $date->format('m/d');

        return $range;
    }

    public static function createDateOptions()
    {
        $dates = Reservation_Utility::getDateRange();
        $day1 = $dates['start'];
        $day2 = $dates['end'];

        $optionDates = array($day1 => $day1, $day2 => $day2);

        return $optionDates;
    }

    public static function getAvailableHours($routingDate)
    {
        $room_open_hour = new DateTime(ROOM_OPEN_HOUR);
        $room_close_hour = new DateTime(ROOM_CLOSE_HOUR);
        $today = new DateTime();

        $seconds = time();
        $secondsIn15Minutes = MINUTE_INCREMENT * 60;
        $secondsRoundedNearest15 = round($seconds / $secondsIn15Minutes) * $secondsIn15Minutes;
        $currentHourRounded = date('H:i', $secondsRoundedNearest15);

        $isToday = $today->format('Y-m-d') == $routingDate;
        if ($isToday) {
            $isAfterOpenHour = $currentHourRounded > $room_open_hour->format('H:i');
            if ($isAfterOpenHour) {
                $room_open_hour = new DateTime($currentHourRounded);
            }
        }

        while ($room_open_hour <= $room_close_hour) {
            $hours[] = array('hour' => $room_open_hour->format('H:i')); //room hour shown to user

            $room_open_hour->add(new DateInterval("PT" . MINUTE_INCREMENT . "M"));
        }

        return $hours;
    }

    public static function createResTemplateForHours($availableHours, $data)
    {
        try {
            $room = new Room();
            $data['reservationsSchedule']['rooms'] = $room->get_rooms();
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }

        $roomsReserveStatus = array();
        $notReserved = '0';
        foreach ($data['reservationsSchedule']['rooms'] as $room) {
            $roomsReserveStatus[$room['roomid']] = $notReserved;
        }

        $data['reservationsSchedule']['reservations']['hours'] = array();

        foreach ($availableHours as $hours) {
            //add hour to the view data array. array_push returns the new number of elements in the array.
            $key = array_push($data['reservationsSchedule']['reservations']['hours'], $hours) - 1;
            //add all rooms reservation status for this hour, which currently are all 0
            $data['reservationsSchedule']['reservations']['hours'][$key]['isReserved'] =  $roomsReserveStatus;
        }

        return $data;
    }

    public static function setReservedRooms($data, $availableHours, $routingDate)
    {
        $dateStart = new DateTime($routingDate . ' ' . $availableHours[0]['hour']);
        $dateStart->sub(new DateInterval("PT" . MAX_RESERVE_HOURS . "H"));

        $startTimestamp = $dateStart->format('Y-m-d H:i:s');
        $endTimestamp = $routingDate . ' ' . ROOM_CLOSE_HOUR;
        try {
            $reservation = new Reservation();
            $reservations = $reservation->getResInRange($startTimestamp, $endTimestamp);
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }
        /*Look for reserved rooms and set them in the data array*/
        //Get each hours into a new array
        $column = array_column($availableHours, 'hour');
        foreach ($reservations as $res) {
            $resStart = new DateTime($res['start']);
            $resEnd = new DateTime($res['end']);
            $resEnd->sub(new DateInterval('PT' . MINUTE_INCREMENT . 'M'));
            while ($resStart->format('H:i') <= $resEnd->format('H:i')) {
                //get the key for the hour that is to be reserved
                $columnKey = array_search($resStart->format('H:i'), $column);
                if ($columnKey !== false) {
                    //set the room that is reserved, ie. to 1
                    $data['reservationsSchedule']['reservations']['hours'][$columnKey]['isReserved'][$res['roomid']] = '1';
                }
                $resStart->add(new DateInterval('PT' . MINUTE_INCREMENT . 'M'));
            }
        }

        return $data;
    }
}//end of file
