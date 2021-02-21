# Study-Room-Reservation (SRR)

## About

This is a basic room reservation application with a dynamic and clean user-interface. It was designed with college students in mind, as they need to reserve study rooms in a college library setting. The application allows for viewing available room time slots, reserving rooms by time range through checkboxes, reservation date selection, managing reservations made, and more.

## Prerequisite

1. Install a server, such as WAMP, or XAMP and run it. This project was built using PHP 7.2.
2. Download a copy of CodeIgniter 3 into your servers' web folder and rename it StudyRoomReservation.
3. Make sure you can access your installation of Codeigniter in your browser by running your server, then going to 'yourDomain'/StudyRoomReservation.

## Installation And Deployment

1.	Drop the SRR assets folder in the StudyRoomReservation folder.
2.	Place all other SRR files and folders in their respective StudyRoomReservation/application folders. 
3.	Load the SRR sql file into your database, through the command line, MySQL Workbench or other, making sure to include both structure and data.
4.	Head to 'yourDomain'/StudyRoomReservation and use the application!

#### Other notes

- I inserted some rooms in the database for you for testing. The SRR does not currently allow for managing rooms, so you will need to work with the database directly to make room changes.
- Please see the constants.php file. It holds some important values, including the open/close hours for the SRR, which are used to display possible reservation hours in the reservation table.
- Please also see the constants.js file.
- The SRR allows for a minimum reservation time of 15 minutes and increments of 15 minutes.

## Built With

- [CodeIgniter PHP framework](https://www.codeigniter.com/)

## Authors

- [Anderson Uribe-Rodriguez](https://andersonuribe.com)

## Acknowledgments

#### People or Insitution
- Ursula C. Schwerin Library at the New York City College of Technology

## License
- This project is licensed under the MIT License - see the [LICENSE.md](/LICENSE) file for details
