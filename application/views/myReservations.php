<div class="flex-child">
    <div class="my-reservations content-container">
        <div class="title-container">
            <h2>My Reservations</h2>
        </div>

        <?php if (isset($reservation[0])) :
            $attributes = array('class' => 'my-reservations_form');
            echo form_open('/RoomReservation/deleteReservation', $attributes);
        ?>

            <?php foreach ($reservation as $res) : ?>
                <ul>
                    <li><span class="bold">Reserved for</span>:
                        <ul class="names">
                            <li><?= $res['username'] ?></li>
                            <li><?= $res['username2'] ?></li>
                            <li><?= $res['username3'] ?></li>
                        </ul>
                    </li>
                    <li><span class="bold">When</span></span>: <?= date('m/d/Y H:i', strtotime($res['start'])) . " - " . date('H:i', strtotime($res['end'])) ?></li>
                    <li><span class="bold">Room</span>: Library - <?= $res['roomname'] ?></li>
                    <br>
                    <label class="boolInputCntnr">
                        Cancel reservation: <input type="checkbox" name="resID[]" value=<?=$res['reservationid']?> />
                        <span class="checkmark"></span>
                    </label>
                </ul>
                <hr>
            <?php endforeach; ?>
            
                <div class="backBtn-container">
                    <button type="submit" name="editReservation">Finish</button>
                </div>
            </form>

        <?php else : ?>

            <div class="my-reservations_search">
                <?php
                $attributes = array('class' => 'my-reservations_form');
                echo form_open('/RoomReservation/myReservations', $attributes);
                ?>
                <label for="reservation-search">
                    <span class="bold">No Reservations were found.</span><br><br>
                    Enter a City Tech barcode to search for
                    reservations:</label><br><br>
                <input class="barcode-input" id="reservation-search" type="text" name="barcode" value="22477" pattern="[0-9]{14,14}" title="A City Tech barcode is 14 degits long, no spaces and starts with 22477." autocomplete="off" required />
                <button type="submit">Search</button> <a href="<?= $url ?>" class="button backBtn">Cancel</a>

                </form>
            </div>

        <?php endif; ?>
    </div>
</div>