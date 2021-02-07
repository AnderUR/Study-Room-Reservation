<?php

declare(strict_types=1);

class CustomException extends Exception
{
    private $logPath = "application\logs\StudyRoomReservationLogs\log.php";
    private $customMessage = 'Sorry, this action caused an error';

    /*
  * Argument: String error message to display to user and if a database error occurred, a db error array in order to log more details
  * Purpose: Can be called to handle database error. Log the error to custom log file directory
  * Return: The message received. If an empty message was received, default message will be used.
  */
    public function logDBError(String $message, $dbError = 0)
    {
        $date = date('y-m-d h:i:sa');
        $error =
            "\n" . $date
            . "\nError in " . $this->getFile()
            . "\nCode: " . $dbError['code']
            . "\nMessage: " . $dbError['message']
            . "\nTrace: \n" . $this->getTraceAsString() . "\n";

        error_log($error, 3, $this->logPath);

        if (empty($message)) {
            return $this->customMessage;
        } else {
            return $message;
        }
    }
}
