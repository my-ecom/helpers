<form action="/sus/admin/<?=$table?>" method="POST">
<?php
foreach ($fields as $field) {
  if ($field['Extra'] == 'auto_increment') {
    continue;
  }
  switch ($field['Type']) {
    case 'text':
    case 'longtext':
?>
  <label for="<?=$field['Field']?>"><?=$field['Field']?></label>
  <textarea  name="<?=$field['Field']?>" id="<?=$field['Field']?>" value="<?=$field['Default']?>" rows="20" cols="30"></textarea>
  <br/>
<?php
      break;
    default:
?>
  <label for="<?=$field['Field']?>"><?=$field['Field']?></label>
  <input type="text" name="<?=$field['Field']?>" id="<?=$field['Field']?>" value="<?=$field['Default']?>"/>
  <br/>
<?php
  }
}
?>
<input type="submit"/>
</form>
