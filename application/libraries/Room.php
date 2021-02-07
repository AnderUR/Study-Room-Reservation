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
        $query = $this->me->db->select('roomid, roomname,roomposition,roomcapacity,roomgroupid,roomdescription,barcode')
            ->order_by('roomid ASC')
            ->get('openroom.rooms');
        return $this->checkQryResult($query);
    }

    private function GetRoomById(int $roomid)
    {
        $query = $this->me->db->select('roomid, roomname')
            ->where('roomid =', $roomid)
            ->get('openroom.rooms');
        $message = "Sorry, there was a problem getting the reservation. 
            If you were submitting one, it likely succeeded. Enter your barcode in Manage My Reservations
            to find it, or ask library staff for help.";
        return $this->checkQryResult($query, $message);
    }

    public function GetRoomSchedule()
    {
        $query = $this->me->db->select('roomhoursid,roomid,dayofweek,start,end')
            ->order_by('roomid ASC')
            ->get('openroom.roomhours');
        return $this->checkQryResult($query);
    }

    public function get_roomById(int $roomId)
    {
        return $this->GetRoomById($roomId);
    }

    public function get_rooms()
    {
        return $this->GetRooms();
    }

    public function get_roomSchedule()
    {
        return $this->GetRoomSchedule();
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
