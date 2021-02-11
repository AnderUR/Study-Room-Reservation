<?php

declare(strict_types=1);

class Room
{
    private $me;

    public function __construct()
    {
        $this->me = &get_instance();
    }

    private function GetRooms()
    {
        $query = $this->me->db->select('barcode,name,capacity,equipment,description')
            ->order_by('name ASC')
            ->get('room_reservation.room');
        return $this->checkQryResult($query);
    }

    private function GetRoomById(int $barcode)
    {
        $query = $this->me->db->select('barcode, name')
            ->where('barcode =', $barcode)
            ->get('room_reservation.room');
        $message = "Sorry, there was a problem getting the reservation. 
            If you were submitting one, it likely succeeded. Enter your barcode in Manage My Reservations
            to find it, or ask library staff for help.";
        return $this->checkQryResult($query, $message);
    }

    public function get_roomById(int $barcode)
    {
        return $this->GetRoomById($barcode);
    }

    public function get_rooms()
    {
        return $this->GetRooms();
    }

    private function checkQryResult($query, $message = "Sorry, there was a problem getting the rooms. Please try again or ask library staff for help.")
    {
        try {
            if (!$query) {
                throw new CustomException();
            } else {
                return $query->result_array();
            }
        } catch (CustomException $e) {
            $e->logDBError("", $this->me->db->error());
            throw new CustomException($message);
        }
    }
}
