<table>
    <thead>
        <tr>
            <th>Room</th>
            <th>Technologies</th>
        </tr>
    </thead>
    <tbody class="tbody">
        <?php foreach($rooms as $room) :?>
        <tr>
            <td><?=$room['roomname']?></td>
            <td><?=$room['roomdescription']?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>