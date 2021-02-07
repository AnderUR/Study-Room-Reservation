<?php

declare(strict_types=1);

class Reservation
{
    private $me;
    private $reservation;

    public function __construct()
    {
        $this->me = &get_instance();

        $args = func_get_args(); //get arguments passed to construct
        $numArgs = func_num_args(); //get num of args passed to construct

        if ($numArgs == 1) {
            if (is_int($args[0])) {
                if (method_exists($this, $func = '__constructInt')) {
                    call_user_func_array(array($this, $func), $args); //Call $func in this class with $args arguments
                }
            } else
            if ((date('Y-m-d', strtotime($args[0])) == $args[0])) {
                if (method_exists($this, $func = '__constructDate')) {
                    call_user_func_array(array($this, $func), $args); //Call $func in this class with $args arguments
                }
            }
        }
    }

    private function __constructDate(string $date)
    {
        $this->reservation = $this->GetReservationByDate($date);
    }

    private function __constructInt(int $resId)
    {
        $this->reservation = $this->GetReservationById($resId);
    }

    /*Getters*/
    private function GetReservationById(int $resId)
    {
        $query = $this->me->db->select('reservationid,roomid,start,end,username,username2,username3')
            ->where('reservationid =', $resId)
            ->get('openroom.reservations');
        $message = "Sorry, there was a problem getting the reservation. 
            If you were submitting one, it likely succeeded. Enter your barcode in Manage My Reservations
            to find it, or ask library staff for help.";
        return $this->checkQryResult($query, $message);
    }

    private function GetReservationsInRange($dateStart, $dateEnd)
    {
        $query = $this->me->db->select('roomid,start,end')
            ->where('start >=', $dateStart)
            ->where('start <=', $dateEnd)
            ->order_by('start ASC, roomid ASC')
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    private function GetResForBarcodeInRange($barcode, $dateStart, $dateEnd)
    {
        $query = $this->me->db->select('reservationid,roomid,start,end,username,username2,username3')
            ->where('start >=', $dateStart)
            ->where('start <=', $dateEnd)
            ->where('(username =' . $barcode . ' OR username2 =' . $barcode . ' OR username3 =' . $barcode . ')')
            ->order_by('start ASC, roomid ASC')
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    private function GetReservationByDate(string $date)
    {
        $query = $this->me->db->select('roomid,start,end')
            ->like('start', $date)
            ->order_by('start ASC, roomid ASC')
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    private function GetRoomReservedByDate(string $date, int $roomid)
    {
        $query = $this->me->db->select('reservationid,roomid,start,end,username,username2,username3')
            ->where('roomid', $roomid)
            ->where('start', $date)
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    private function GetReservationsByBarcode(string $barcode)
    {
        $query = $this->me->db->select('reservationid,roomid,start,end,username,username2,username3')
            ->where('username =', $barcode)
            ->or_where('username2 =', $barcode)
            ->or_where('username3 =', $barcode)
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    private function GetResForBarcodesForDate(string $date, array $barcodes)
    {
        $query = $this->me->db->select('roomid,start,end')
            ->like('start', $date)
            ->where('(username =' . $barcodes[0] . ' OR username2 =' . $barcodes[1] . ' OR username3 =' . $barcodes[2] . ')')
            ->get('openroom.reservations');
        return $this->checkQryResult($query);
    }

    /*Insert to db*/
    private function Insert(array $resData)
    {
        try {
            if (!$this->me->db->insert('openroom.reservations', $resData)) {
                throw new CustomException();
            } else {
                return $this->me->db->insert_id();
            }
        } catch (CustomException $e) {
            $e->logDBError("", $this->me->db->error());
            throw new CustomException("Sorry, there was a problem creating your reservation. Please try again or ask library staff for help.");
        }
    }

    /*Delete from db*/
    private function Delete(array $resIDs)
    {
        $query = $this->me->db->where_in('reservationid', $resIDs)
            ->delete('openroom.reservations');

        try {
            if (!$query) {
                throw new CustomException();
            } else {
                return 1;
            }
        } catch (CustomException $e) {
            $e->logDBError("", $this->me->db->error());
            throw new CustomException("Sorry, there was a problem removing your reservation. Please try again or ask library staff for help.");
        }
    }

    public function get_reservations()
    {
        return $this->reservation;
    }

    public function getResInRange($dateStart, $dateEnd)
    {
        return $this->GetReservationsInRange($dateStart, $dateEnd);
    }

    public function get_resForBarcodeInRange($barcode, $dateStart, $dateEnd)
    {
        return $this->GetResForBarcodeInRange($barcode, $dateStart, $dateEnd);
    }

    public function getResByBarcode(string $barcode)
    {
        return $this->GetReservationsByBarcode($barcode);
    }

    public function get_resForBarcodesForDate(string $date, array $barcode)
    {
        return $this->GetResForBarcodesForDate($date, $barcode);
    }

    public function get_roomReservedByDate(string $date, int $roomid)
    {
        return $this->GetRoomReservedByDate($date, $roomid);
    }

    public function insert_reservation(array $resData)
    {
        return $this->Insert($resData);
    }

    public function delete_reservation(array $resIDs)
    {
        return $this->Delete($resIDs);
    }

    private function checkQryResult($query, $message = "Sorry, there was a problem getting the reservations. Please try again or ask library staff for help.")
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
