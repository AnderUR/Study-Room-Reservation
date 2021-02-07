<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class RoomReservation extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/New_York');
    }

    function index()
    {
        $today = new DateTime();
        $isNotClosed = ROOM_CLOSE_HOUR > $today->format('H:i:s');
        if ($isNotClosed) {
            $routingDate = Reservation_Utility::processURISegmentDate($this->uri->segment(3));

            $data['reservationsSchedule']['options'] = Reservation_Utility::createDateOptions();
            $data['selectedDate'] = date('m/d', strtotime($routingDate));

            $availableHours = Reservation_Utility::getAvailableHours($routingDate);

            try {
                $data = Reservation_Utility::createResTemplateForHours($availableHours, $data);
                $data = Reservation_Utility::setReservedRooms($data, $availableHours, $routingDate);
            } catch (CustomException $e) {
                $data['error'] = $e->getMessage();
                $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
            }
            /*Load the view*/
            $this->web->get_content('reservation_index', $data);
        } else {
            $data['error'] = 'Currently, all rooms are closed. Rooms are open from ' .
            ROOM_OPEN_HOUR . ' to ' . ROOM_CLOSE_HOUR . '. Please contact library staff if there is a problem.';
            $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
        }
    }

    function myReservations()
    {
        $post = $this->input->post();
        $data['url'] = base_url();

        if (isset($post['barcode'])) {
            $range = Reservation_Utility::getDateRange();
            try {
                $barcode = [$post['barcode']];
                $processDBTrans = new ProcessDBTransactions();
                $processDBTrans->setBarcodes($barcode);
                $processDBTrans->setStartDate($range['start']);
                $processDBTrans->setEndDate($range['end']);

                $data['reservation'] = $processDBTrans->getResByBarcodeInRange();

                $data['url'] = base_url();

                $this->web->get_content('reservationStatus', $data, 'headerBasic', 'footerBasic');
            } catch (CustomException $e) {
                $data['error'] = $e->getMessage();
                $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
            }
        } else {
            $data['error'] = "Incorrect barcode submitted. Please try again, or ask library staff for help.";
            $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
        }
    }

    function createReservation()
    {
        $resPost = $this->input->post();
        $data['url'] = base_url();

        if (isset($resPost['roomId'])) {
            try {
                $processDBTrans = new ProcessDBTransactions();

                $barcodes = [trim($resPost['barcode1']), trim($resPost['barcode2']), trim($resPost['barcode3'])];
                $processDBTrans->setRoomid((int)$resPost['roomId']);
                $processDBTrans->setStartDate($resPost['date']);
                $processDBTrans->setStartTime($resPost['starttime']);
                $processDBTrans->setEndTime($resPost['endtime']);
                $processDBTrans->setBarcodes($barcodes);

                $insertId = $processDBTrans->insertReservation();

                if (!empty($insertId)) {
                    $processDBTrans->setReservationids([$insertId]);
                    $data['reservation'] = $processDBTrans->getResById();

                    $this->web->get_content('reservationStatus', $data, 'headerBasic', 'footerBasic');
                } else {
                    $defaultErrorMessage = "Sorry, there was a problem creating your reservation. Please try again or ask library staff for help.";
                    $validationError = $processDBTrans->getValidationError();
                    $data['error'] = (empty($validationError)) ? $defaultErrorMessage : $validationError;
                    $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
                }
            } catch (CustomException $e) {
                $data['error'] = $e->getMessage();
                $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
            } catch (Exception $e) {
                $data['error'] = $e->getMessage();
                $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
            }
        } else {
            redirect(base_url());
        }
    }

    function deleteReservation()
    {
        $myResPost = $this->input->post();
        $data['url'] = base_url();

        if (isset($myResPost['resID'])) {
            try {
                $processDBTrans = new ProcessDBTransactions();
                $processDBTrans->setReservationids($myResPost['resID']);
                $isDeleted = $processDBTrans->deleteReservation();

                if ($isDeleted) {
                    $data['successMessage'] = "Your reservation has been removed.";
                    $this->web->get_content('successView', $data, 'headerBasic', 'footerBasic');
                } else {
                    $data['error'] = "There was a problem deleting your reservations. Please try again, or ask library staff for help.";
                    $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
                }
            } catch (CustomException $e) {
                $data['error'] = $e->getMessage();
                $this->web->get_content('errorView', $data, 'headerBasic', 'footerBasic');
            }
        } else {
            redirect(base_url());
        }
    }
} //end of file