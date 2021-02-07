var first_checked = null;
var second_checked = null;
var activeColIndex = -1;
var active_td = [];

var incremented_endTime = '';
var checkboxVal_endTime = '';

/*Routing with selected date*/
DATEPICKER.onchange = function () {
    const baseUrl = document.location.origin + "/StudyRoomReservation/RoomReservation";

    let today = new Date();
    let yyyy = today.getFullYear();

    let selectedDate = new Date(yyyy + "/" + this.value);
    let mm = selectedDate.getMonth() + 1; //getMonth starts at 0
    let dd = selectedDate.getDate();
    //convert to two degits
    if (dd < 10) { dd = '0' + dd; }
    if (mm < 10) { mm = '0' + mm; }

    let routingDate = yyyy + '-' + mm + '-' + dd;

    document.location.href = baseUrl + "/index/" + routingDate;
}

RESET_BTN.onclick = resetTable;

/*Handle checkboxes on/off state*/
CHECKBOXES.forEach(function (checkbox) {

    checkbox.addEventListener('click', function () {
        let td = checkbox.parentNode.parentNode;
        activeColIndex = td.cellIndex; //get index of the td of the checkbox clicked

        if (first_checked == null) {
            first_checked = checkbox;
            let tr = td.parentNode;
            active_td.push(td);

            setSelectedStyle(first_checked, true);
            setActiveBoxes(activeColIndex, tr.nextElementSibling);
            setHidden();
            setReserveTimes();
            setRoom();
            RESERVE_BTN.disabled = false;
            RESET_BTN.disabled = false;
        } else if (checkbox == first_checked) { //show all checkboxes whenever the first checkbox is unchecked.
            resetTable(); //show all checkboxes 
        } else if (second_checked == null || second_checked.checked == false) { //the event of checking and unchecking second box (first checkbox is checked)
            if (second_checked != null) {
                setSelectedStyle(second_checked, false);
                second_checked = null; //reset second checkbox to await new value  
                toggleDisabled(false);
                clearReserveTimes();
            } else {
                second_checked = checkbox;
                setSelectedStyle(second_checked, true);
                RESERVE_BTN.disabled = false;

                //Second checkbox is now checked, disable active_td checkboxes
                let countChecked = 0;
                for (let tdEl of active_td) {
                    let checkbox = tdEl.firstElementChild.children[0]; //the checkboxes are the first el inside a td, inside a label tag

                    let secondBoxNotChecked = countChecked <= 1;
                    if (secondBoxNotChecked) {
                        if (checkbox.checked == false) { //checkbox checked not encountered
                            checkbox.checked = true;
                            checkbox.disabled = true;
                        } else {
                            countChecked++;
                        }
                    } else {
                        if (checkbox.checked == false) {
                            checkbox.disabled = true;
                        }
                    }
                }

                setReserveTimes();
                setRoom();
            }
        }
    });
});

/*Set the input fields for room, start and end times after two checkboxes are selected*/
function setReserveTimes() {
    if (ROOM_NAME.value == '' && first_checked != null) {
        START_INPUT.value = first_checked.value;

        let today = new Date();
        let time = START_INPUT.value.split(':');
        today.setHours(time[0])
        let min = parseInt(time[1]) + MAX_INCREMENT_MINS;
        today.setMinutes(min);
        let hh = today.getHours()
        let mm = today.getMinutes();
        if (mm < 10) { mm = '0' + mm; }

        let endtime =  hh + ':' + mm;
        END_INPUT.value = endtime;
        incremented_endTime = endtime;

        START_INPUT.style.visibility = 'visible';
        END_INPUT.style.visibility = 'visible';
    } else {
        let endtime = second_checked.value;
        END_INPUT.value = endtime;
        checkboxVal_endTime = endtime;
    }
}

function setRoom() {
    let room = (ROOMS_TR.children[activeColIndex].textContent).split("|"); //note that activeColIndex is set when the first checkbox is checked
    ROOM_NAME.value = room[0];
    ROOM_ID.value = room[1];
    ROOM_NAME.style.visibility = 'visible';
}

/*Reset the room, start and end times input fields*/
function clearReserveTimes() {
    if (first_checked != null) {
        END_INPUT.value = incremented_endTime;
    } else {
        START_INPUT.value = '';
        START_INPUT.style.visibility = 'hidden';
        END_INPUT.value = '';
        END_INPUT.style.visibility = 'hidden';
        ROOM_NAME.value = '';
        ROOM_NAME.style.visibility = 'hidden';
    }

}

/*Fills the active_td array, the specific checkboxes that can be selected in the active column*/
function setActiveBoxes(activeColIndex, nextSibling_tr) {
    for (let i = 0; i < TOTAL_INCREMENTS; i++) {
        if (nextSibling_tr) {
            let nextSibling_td = nextSibling_tr.cells[activeColIndex];
            if (nextSibling_td.firstElementChild.children[0].checked != true) {
                active_td.push(nextSibling_td);

                nextSibling_tr = nextSibling_tr.nextElementSibling;
            } else {
                nextSibling_tr == null;
            }
        } else {
            break;
        }
    }
}

/*Hide tds/checkboxes that are NOT part of the active_td array*/
function setHidden() {
    let numOfCells;
    let tableCell;
    for (let i = 1; i < TABLE.length; i++) {
        numOfCells = TABLE[i].cells.length;
        for (let j = 1; j < numOfCells; j++) {
            tableCell = TABLE[i].cells[j];
            if ((active_td.indexOf(tableCell) == -1) && (tableCell.className != "reserved")) { //if not in the active_td array
                TABLE[i].cells[j].classList.add('inactive');
            }
        }
    }
}

/*Show all tds/checkboxes*/
function resetTable() {
    if (first_checked != null) {
        setSelectedStyle(first_checked, false);
        first_checked = null;
        if (second_checked != null) {
            setSelectedStyle(second_checked, false);
            second_checked = null;
        }
        active_td = [];
        activeColIndex = -1;
        RESERVE_BTN.disabled = true;

        let numOfCells;
        let cell;
        for (let i = 1; i < TABLE.length; i++) {
            numOfCells = TABLE[i].cells.length;
            for (let j = 1; j < numOfCells; j++) {
                cell = TABLE[i].cells[j];
                cell.classList.remove('inactive');
                if ((cell.className != "reserved")) {
                    cell.firstElementChild.children[0].checked = false
                    cell.firstElementChild.children[0].disabled = false
                }
            }
        }
        clearReserveTimes();
        RESET_BTN.disabled = true;
    }
}

/*enable/disable checkboxes ONLY from the active list*/
function toggleDisabled(boolVal) {
    for (let tdEl of active_td) {
        tdEl.firstElementChild.children[0].disabled = boolVal;
        tdEl.firstElementChild.children[0].checked = false;
    }
    first_checked.checked = true; //re-check first checkbox
}

/*Set the style of the checkmark (the box visible to the user)*/
function setSelectedStyle(checkbox, bool) {
    if (bool) {
        checkbox.nextElementSibling.classList.add('selected');
    } else {
        checkbox.nextElementSibling.classList.remove('selected');
    }
}