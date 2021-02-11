<table id="booking-tbl-js" class="reserve-tbl">
    <thead class="thead">
        <tr class="rooms-tr-js">
            <th class="room-header sticky">
                <?php
                $attr = array('class' => "date-picker", 'form' => "reserve-form");
                echo form_dropdown('date', $reservationsSchedule['options'], $selectedDate, $attr); 
                ?>
            </th>
            <?php foreach ($reservationsSchedule['rooms'] as $room) : ?>
                <th class="room-header sticky"><?= $room['name']?><span style="display: none;"><?= '|'.$room['barcode']?></span></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody class="tbody">
        <?php foreach ($reservationsSchedule['reservations']['hours'] as $hour) : ?>
            <tr>
                <th class="th"><?= $hour['hour'] ?></th>
                <?php foreach ($hour['isReserved'] as $isReserved) :
                    $td_isReserved = "";
                    $checkboxData = 
                    [
                        'value' => $hour['hour'],
                        'checked' => $isReserved,
                        'autocomplete' => 'off'
                    ];
                    if ($isReserved) :
                        $checkboxData += ['disabled' => 'disabled'];
                        $td_isReserved = "reserved";
                    endif;
                ?>
                    <td class=<?= $td_isReserved ?>>
                        <label class="boolInputCntnr">
                            <?= form_checkbox($checkboxData); ?>
                            <span class="checkmark"></span>
                        </label>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>