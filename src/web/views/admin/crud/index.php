<?php if (count($entities) == 0) {
  echo 'Table empty';
  return;
}
?>
<style>
  th {
    min-width: 40px;
  }
  table, th, td {
      border: 1px solid black;
    }
    td {
        text-align:center;
        vertical-align: middle;
    }
</style>
<table>
  <thead>
    <tr>
      <?php foreach ($fields as $field) { ?>
          <?php if ($field['Type'] == 'text') continue;?>
      <th><?=$field['Field']?></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
<?php
foreach ($entities as $entity) {
?>
    <tr>
      <?php foreach ($fields as $field) { ?>
           <?php if ($field['Type'] == 'text') continue;?>
      <td><?=$entity[$field['Field']]?></td>
      <?php } ?>
    </tr>
<?php
}
?>
  </tbody>
</table>
