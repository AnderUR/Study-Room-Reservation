const MAX_RES_MINS = 180; //Reservation mins must be set in relation with maximum mins allowed. Use whole number
const MAX_INCREMENT_MINS = 15; //Reservation mins is incremented based on this value. Use whole number
const TOTAL_INCREMENTS = Math.floor(MAX_RES_MINS / MAX_INCREMENT_MINS); //How many cells will be added to reach max_res_hrs. Ex. If max_res is 180 and max_min is 15, TOTAL_MINS = 12

const DATEPICKER = document.querySelector(".date-picker");
const START_INPUT = document.getElementById('starttime-js');
const END_INPUT = document.getElementById('endtime-js');
const ROOM_NAME = document.getElementById('roomId-js');
const CHECKBOXES = document.querySelectorAll("input[type='checkbox'");
const TABLE = document.getElementById('booking-tbl-js').rows;
const ROOMS_TR = document.querySelector('.rooms-tr-js');
const ROOM_ID = document.querySelector("input[name='roomId']");
const RESERVE_BTN = document.querySelector("button[name='reserve']");
const RESET_BTN = document.querySelector('.reset-btn');
const RES_FORM = document.getElementById("reserve-form");
const FORM_ERROR = document.querySelector(".form-error");

const POLICY_BTN = document.querySelector('.policy-confirm-btn-js');
const INFO_LINKS = document.querySelectorAll('.info-link-js');
const LINK_TECHNOLOGIES = document.querySelector('.technologies.info-link-technologies');
const LINK_POLICY = document.querySelector('.policy.info-link-policy');
const LINK_ID_SAMPLE = document.querySelector('.id_sample.info-link-id_sample');

const BARCODE_LENGTH = 14;