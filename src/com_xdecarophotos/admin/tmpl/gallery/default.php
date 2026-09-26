<?php
defined('_JEXEC') or die;
?>
<div class="xdecaro-scope">
  <div class="xdecaro-toolbar">
    <a class="xdecaro-button xdecaro-button--primary" href="index.php?option=com_xdecarophotos&amp;view=studio">Nuova foto</a>
  </div>
  <?php if (empty($this->items)) : ?>
    <div class="xdecaro-empty"><p>Nessuna foto disponibile.</p></div>
  <?php else : ?>
    <div class="photos-gallery" role="list">
      <?php foreach ($this->items as $item) : $title=htmlspecialchars((string)($item->title ?? ''),ENT_QUOTES,'UTF-8'); ?>
      <article class="xdecaro-card photos-gallery__item" role="listitem">
        <div class="photos-gallery__preview"><img src="<?php echo htmlspecialchars((string)$item->preview_url,ENT_QUOTES,'UTF-8'); ?>" alt="<?php echo $title; ?>" loading="lazy"></div>
        <div class="xdecaro-card__body">
          <strong><?php echo $title !== '' ? $title : 'Foto'; ?></strong>
          <div class="photos-gallery__actions"><a class="xdecaro-button" aria-label="Modifica foto" href="<?php echo htmlspecialchars((string)$item->edit_url,ENT_QUOTES,'UTF-8'); ?>">Modifica</a></div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
