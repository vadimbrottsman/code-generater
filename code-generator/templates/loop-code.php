<tr>
  <td><?the_title()?></td>
  <td>
    <?php
      if ( get_post_meta( get_the_ID(), 'status', 1 ) == 'activated') echo 'Активирован'; 
      else  echo 'Активен';
    ?>
  </td>
  <td>
  <?php
      if ( get_post_meta( get_the_ID(), 'type', 1 ) == 'type_one') echo 'Одноразовый'; 
      else  echo 'Многоразовый';
  ?> 
  </td>
  <td><?=get_the_date()?></td>
  <td><a href="<?=$url?>pdf-generate.php?code=<?=get_the_title()?>" class="button button-primary download_template">Скачать шаблон</a></td>
</tr>