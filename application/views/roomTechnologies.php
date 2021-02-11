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
            <td><?=$room['name']?></td>
            <td><?=$room['equipment']?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>