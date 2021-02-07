<?php

declare(strict_types=1);

class ProcessDBTransactions
{
    private $startDate;
    private $endDate;
    private $startTime;
    private $endTime;

    /*Instance variables for db*/
    private $reservationids;
    private $roomid;
    private $startTimestamp;
    private $endTimestamp;
    private $barcodes;

    private $validationError;

    public function __construct()
    {
        $this->startDate = '00/00';
        $this->endDate = '00/00';
        $this->startTime = '00:00';
        $this->endTime = '00:00';

        $this->reservationids = array();
        $this->roomid = 0;
        $this->startTimestamp = '0000-00-00 00:00:00';
        $this->endTimestamp = '0000-00-00 00:00:00';
        $this->barcodes = array();

        $this->validationError = '';
    }

    public function setReservationids(array $resids)
    {
        $this->reservationids = $resids;
    }
    public function setRoomid(int $roomid)
    {
        $this->roomid = $roomid;
    }
    public function setStartDate(string $date)
    {
        $this->startDate = $this->formatDate($date);
    }
    public function setEndDate(string $date)
    {
        $this->endDate = $this->formatDate($date);
    }
    public function setStartTime(string $startTime)
    {
        $this->startTime = $startTime;
    }
    public function setEndTime(string $endTime)
    {
        $this->endTime = $endTime;
    }
    public function setBarcodes(array $barcodes)
    {
        $this->barcodes = $barcodes;
    }

    public function getReservationids()
    {
        return $this->reservationids;
    }
    public function getRoomid()
    {
        return $this->roomid;
    }
    public function getStartDate()
    {
        return $this->startDate;
    }
    public function getEndDate()
    {
        return $this->endDate;
    }
    public function getStartTimestamp()
    {
        return $this->startTimestamp;
    }
    public function getEndTimestamp()
    {
        return $this->endTimestamp;
    }
    public function getBarcodes()
    {
        return $this->barcodes;
    }
    public function getValidationError()
    {
        return $this->validationError;
    }

    public function getResByBarcodeInRange()
    {
        $this->startDate .= ' 00:00:00';
        $this->endDate .= ' 23:59:59';
        if ($this->validateBarcodes()) {
            try {
                $reservation = new Reservation();
                $reservations = $reservation->get_resForBarcodeInRange($this->barcodes[0], $this->startDate, $this->endDate);
                if ($reservations !== '') {
                    $resWithRoomNames = $this->getReservedRooms($reservations);
                    //NOTE: Parse barcodes into usernames here
                    return $resWithRoomNames;
                }
            } catch (CustomException $e) {
                throw new CustomException($e->getMessage());
            }
        }
    }

    public function getResById()
    {
        if ($this->validateReservationid()) {
            try {
                $reservation = new Reservation($this->reservationids[0]);
                $reservations = $reservation->get_reservations();
                if ($reservations !== '') {
                    $resWithRoomNames = $this->getReservedRooms($reservations);
                    //NOTE: Parse barcodes into usernames here
                    return $resWithRoomNames;
                }
            } catch (CustomException $e) {
                throw new CustomException($e->getMessage());
            }
        }
    }

    private function getReservedRooms($reservations)
    {
        $resWithRoomNames = $reservations;

        try {
            $room = new Room();
            $rooms = $room->get_rooms();
            foreach ($reservations as $key => $room) {
                //get the index of the room array that holds the corresponding roomname for given the roomid
                $i = array_search($room['roomid'], array_column($rooms, 'roomid'));
                $resWithRoomNames[$key]['roomname'] = $rooms[$i]['roomname'];
            }
            return $resWithRoomNames;
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function insertReservation()
    {
        try {
            $this->startTimestamp = $this->formatDateTime($this->startDate, $this->startTime);
            $this->endTimestamp = $this->formatDateTime($this->startDate, $this->endTime);

            $canMakeReservation = $this->validateBarcodes() && $this->checkNumBarcodes() &&
                $this->roomid != 0 && $this->resForDateExists() &&
                $this->barcodeResExistsForDate();
                
            if ($canMakeReservation) {
                $resData = array(
                    'start' => $this->startTimestamp,
                    'end' => $this->endTimestamp,
                    'roomid' => $this->roomid,
                    'username' => $this->barcodes[0],
                    'numberingroup' => MAX_NUM_GROUP_ALOWED, //currently not implemented in front end
                    'username2' => $this->barcodes[1],
                    'username3' => $this->barcodes[2],
                );

                $reservation = new Reservation();
                $insertId = (int) $reservation->insert_reservation($resData);
                return ($insertId);
            }
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteReservation()
    {
        if ($this->validateReservationid()) {
            try {
                $reservation = new Reservation();
                $isDeleted = $reservation->delete_reservation($this->reservationids);
                return $isDeleted;
            } catch (CustomException $e) {
                throw new CustomException($e->getMessage());
            }
        } else {
            return false;
        }
    }

    private function barcodeResExistsForDate()
    {
        try {
            $reservation = new Reservation();
            $reservations = $reservation->get_resForBarcodesForDate($this->startDate, $this->barcodes);
            if (empty($reservations)) {
                return true;
            } else {
                $this->validationError = "One or more of the barcodes already has reservations. 
                Please, remove the existing reservation using Manage My Reservations or ask library staff for help.
                Refer to the study room policy for more information on reservation limits.";
                return false;
            }
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function resForDateExists()
    {
        $reservation = new Reservation();
        $reservations = $reservation->get_roomReservedByDate($this->startTimestamp, $this->roomid);
        if (empty($reservations)) {
            return true;
        } else {
            return false;
        }
    }

    private function validateReservationid()
    {
        $isValid = true;

        if (!empty($this->reservationids)) {
            $numOfRes = sizeof($this->reservationids);
            for ($i = 0; $i < $numOfRes; $i++) {
                if (!is_numeric($this->reservationids[$i])) {
                    $isValid = false;
                    break;
                }
            }
        } else {
            $isValid = false;
        }
        return $isValid;
    }

    private function validateBarcodes()
    {
        $isValid = true;
        $foundStrAtPos0 = 0;
        if (!empty($this->barcodes)) {
            $numOfBarcodes = sizeof($this->barcodes);
            for ($i = 0; $i < $numOfBarcodes; $i++) {

                $isCorrectLen = strlen($this->barcodes[$i]) == BARCODE_LEN; 
                $isCorrectPattern = strpos($this->barcodes[$i], BARCODE_PATTERN) === $foundStrAtPos0;
                $isNumeric = is_numeric($this->barcodes[$i]);
                
                if (!($isCorrectLen && $isCorrectPattern && $isNumeric)) {
                    $isValid = false;
                    break;
                }
            }
        } else {
            $isValid = false;
        }
        return $isValid;
    }

    private function checkNumBarcodes()
    {
        $uniqueBarcodes = array_unique($this->barcodes);
        $numUniqueBarcodes = sizeof($uniqueBarcodes);
        $numBarcodes = sizeof($this->barcodes);

        if (
            $numUniqueBarcodes == $numBarcodes &&
            $numBarcodes <= MAX_NUM_GROUP_ALOWED &&
            $numBarcodes >= MIN_NUM_GROUP_ALOWED
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function formatDate($date)
    {
        try {
            $dateTime = new DateTime($date);
            $date = $dateTime->format('Y-m-d');
            return $date;
        } catch (Exception $e) {
            throw new Exception('An unexpected error has occurred. Please try again, or ask library staff for help.');
        }
    }

    private function formatDateTime($date, $time)
    {
        try {
            $date = new DateTime($date . ' ' . $time);
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            throw new Exception('An unexpected error has occurred. Please try again, or ask library staff for help.');
        }
    }
}
