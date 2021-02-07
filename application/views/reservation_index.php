<div class="grid-container">

    <?php //echo validation_errors(); 
    ?>
    <div class="item-3 flex-container">

        <div class="flex-child">
            <div>
                <div class="reserve-title">
                    <h2>Make a Reservation</h2>
                </div>
                <div class="table-title">
                    <?php
                    $attributes = array('class' => 'reserve-form', 'id' => 'reserve-form');
                    $hidden = array('roomId' => '0');
                    echo form_open('/RoomReservation/createReservation', $attributes, $hidden);
                    ?>
                    <fieldset> 
                        <label>
                            <span class="step">1</span> Select a
                            <span class="emphasize">date</span>, and a
                            <span class="emphasize">time box</span> for a 15 minutes reservation. Select another
                            <span class="emphasize">time box</span> for more time
                        </label><br><br>
                        <input id="roomId-js" class="res-detail reserve-field" type="text" readonly="readonly" placeholder="Room" autocomplete="off" required />
                        <input id="starttime-js" class="res-detail reserve-field" name="starttime" type="text" readonly="readonly" placeholder="00:00" autocomplete="off" required />
                        <input id="endtime-js" class="res-detail reserve-field" name="endtime" type="text" readonly="readonly" placeholder="00:00" autocomplete="off" required />
                    </fieldset>
                    <br>
                    <fieldset>
                        <label for="reserve-field"><span class="step">2</span> Enter 3 different City Tech
                            barcodes</label><br><br>
                        <input id="reserve-field" class="barcode-input reserve-barcode-js reserve-field" type="text" name="barcode1" value="22477020979685" required pattern="[0-9]{14,14}" title="A City Tech barcode must be 14 degits long, no spaces and starts with 22477." autocomplete="off" />
                        <input class="barcode-input reserve-barcode-js reserve-field" type="text" name="barcode2" value="22477020979683" required pattern="[0-9]{14,14}" title="A City Tech barcode must be 14 degits long, no spaces and starts with 22477." autocomplete="off" />
                        <input class="barcode-input reserve-barcode-js reserve-field" type="text" name="barcode3" value="22477020979684" required pattern="[0-9]{14,14}" title="A City Tech barcode is 14 degits long, no spaces and starts with 22477." autocomplete="off" />
                    </fieldset>
                    <br>
                    <div class="form-error"></div>
                    <button type="submit" name="reserve" disabled autocomplete="off">Reserve</button>
                    <button class="reset-btn" type="button" disabled>Reset</button>
                    </form>
                </div>
                <div class="table-container content-container">
                    <div>
                        <div class="rooms-label-container">
                            <div class="room-label">Rooms</div>
                        </div>
                        <?php $this->load->view('reservations_tbl', $reservationsSchedule); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-child">
            <div class="my-reservations content-container">
                <div class="title-container">
                    <h2>Manage My Reservations</h2>
                </div>
                <div class="my-reservations_search">
                    <?php
                    $attributes = array('class' => 'my-reservations_form');
                    echo form_open('/RoomReservation/myReservations', $attributes);
                    ?>
                    <label for="reservation-search">Enter a City Tech barcode to search for
                        reservations</label><br><br>
                    <input class="barcode-input" id="reservation-search" type="text" name="barcode" value="22477" pattern="[0-9]{14,14}" title="A City Tech barcode is 14 degits long, no spaces and starts with 22477." autocomplete="off" required />
                    <button type="submit">Search</button>
                    </form>
                </div>
            </div>

            <div class="information content-container content-container-column">
                <div class="title-container">
                    <h2>Information</h2>
                </div>
                <div class="info-links">
                    <div class="info-link-technologies info-link-js">See technologies for each room</div><br>
                    <div class="info-link-id_sample info-link-js">Find my barcode</div><br>
                    <div class="info-link-policy info-link-js">Policy</div>
                </div>
            </div>

            <div class="policy info-link info-link-policy content-container content-container-column">
                <div>
                    <div class="title-container">
                        <h2>Policy</h2>
                    </div>
                    <div class="policy-text">
                        <?php $this->load->view('policy'); ?>
                    </div>
                </div>
            </div>

            <div class="technologies info-link info-link-technologies content-container content-container-column info-link-show">
                <div class="title-container">
                    <h2>Room Technologies</h2>
                </div>
                <div class="technologies-container">
                    <?php $this->load->view('roomTechnologies', $reservationsSchedule['rooms']); ?>
                </div>
            </div>

            <div class="id_sample info-link info-link-id_sample content-container content-container-column">
                <div class="title-container">
                    <h2>Find My Barcode</h2>
                </div>
                <div class="id_sample-content">
                    <?php $this->load->view('findBarcode'); ?>
                </div>
            </div>

        </div>
    </div>
</div> <!-- end grid -->

<div class="reveal">
    <div class="reveal-content">

        <div class="policy content-container">
            <div>
                <div class="title-container">
                    <h2>Policy</h2>
                </div>
                <div class="policy-text">
                    <?php $this->load->view('policy'); ?>
                </div>
            </div>
        </div>

        <div class="policy-confirm content-container">
            <p>I have <span class="emphasize">read and understood</span> the room reservation policy.</p><br>
            <div class="policy-button-container"><button class="policy-confirm-btn-js" type="submit">Submit</button></div>
        </div>

    </div>
</div>