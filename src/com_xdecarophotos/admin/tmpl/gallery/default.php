<?php
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
?>
<div class="xdecaro-scope xdecarophotos-admin">
  <div class="xdecarophotos-gallery" role="list" aria-label="Gallery foto">
    <?php foreach($this->items as $item):
      $src=Route::_('index.php?option=com_xdecarophotos&task=photo.file&id='.(int)$item->id);
      $title=trim((string)$item->title)!==''?(string)$item->title:('Foto '.(int)$item->id);
    ?>
      <article class="xdecaro-card xdecarophotos-gallery__item" role="listitem">
        <div class="xdecarophotos-gallery__media">
          <img src="<?= htmlspecialchars($src,ENT_QUOTES,'UTF-8') ?>" alt="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>" loading="lazy">
        </div>
        <div class="xdecaro-card__body">
          <strong><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?></strong>
          <div class="xdecarophotos-gallery__meta"><?= htmlspecialchars((string)$item->photo_type,ENT_QUOTES,'UTF-8') ?></div>
          <div class="xdecarophotos-gallery__actions">
            <a class="xdecaro-button" href="<?= Route::_('index.php?option=com_xdecarophotos&view=studio&id='.(int)$item->id) ?>" aria-label="Modifica <?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>">Modifica</a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>
