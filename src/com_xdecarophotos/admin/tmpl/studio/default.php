<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
?>
<div class="xdecaro-scope xdecarophotos-studio" data-photo-id="<?= (int) $this->photoId ?>">
  <section class="xdecaro-card">
    <div class="xdecaro-card__body">
      <h2>Photo Studio</h2>
      <p>Carica una foto oppure usa la fotocamera. Prima del salvataggio puoi correggere ritaglio, zoom e rotazione.</p>

      <?php if ((int) $this->photoId < 1): ?>
      <form action="<?= Route::_('index.php?option=com_xdecarophotos&task=photo.upload') ?>" method="post" enctype="multipart/form-data" id="xdecarophotos-upload-form">
        <div class="xdecarophotos-studio__source">
          <div class="xdecaro-field"><label for="owner_component">Componente proprietario</label><input id="owner_component" name="owner_component" value="com_xdecaropeople" required></div>
          <div class="xdecaro-field"><label for="owner_entity">Tipo entità</label><input id="owner_entity" name="owner_entity" value="person" required></div>
          <div class="xdecaro-field"><label for="owner_id">ID entità</label><input id="owner_id" name="owner_id" required></div>
          <input type="hidden" name="photo_type" value="original">
          <div class="xdecaro-field">
            <label for="xdecarophotos-file">Carica foto</label>
            <input id="xdecarophotos-file" name="photo" type="file" accept="image/jpeg,image/png,image/webp" required>
          </div>
          <button type="button" class="xdecaro-button" id="xdecarophotos-camera">Usa fotocamera</button>
          <button type="button" class="xdecaro-button" id="xdecarophotos-capture" hidden>Scatta foto</button>
          <button type="submit" class="xdecaro-button xdecaro-button--primary">Carica originale e continua</button>
          <div id="xdecarophotos-camera-status" class="xdecarophotos-studio__status" aria-live="polite"></div>
        </div>
        <?= HTMLHelper::_('form.token') ?>
      </form>
      <?php else: ?>
      <div class="xdecarophotos-studio__source">
        <div class="xdecaro-field"><label for="xdecarophotos-file">Sostituisci anteprima locale</label><input id="xdecarophotos-file" type="file" accept="image/jpeg,image/png,image/webp"></div>
        <button type="button" class="xdecaro-button" id="xdecarophotos-camera">Usa fotocamera</button>
        <button type="button" class="xdecaro-button" id="xdecarophotos-capture" hidden>Scatta foto</button>
        <div id="xdecarophotos-camera-status" class="xdecarophotos-studio__status" aria-live="polite"></div>
      </div>
      <?php endif; ?>

      <video id="xdecarophotos-video" class="xdecarophotos-studio__video" playsinline hidden></video>
      <div class="xdecarophotos-studio__canvas-wrap">
        <canvas id="xdecarophotos-canvas" width="600" height="800" aria-label="Anteprima e ritaglio della foto"></canvas>
      </div>

      <div class="xdecarophotos-studio__controls" aria-label="Controlli ritaglio">
        <div class="xdecaro-field">
          <label for="xdecarophotos-zoom">Zoom</label>
          <input id="xdecarophotos-zoom" type="range" min="1" max="3" step="0.05" value="1">
        </div>
        <div class="xdecarophotos-studio__buttons">
          <button type="button" class="xdecaro-button" data-move-x="-0.02">Sposta a sinistra</button>
          <button type="button" class="xdecaro-button" data-move-x="0.02">Sposta a destra</button>
          <button type="button" class="xdecaro-button" data-move-y="-0.02">Sposta in alto</button>
          <button type="button" class="xdecaro-button" data-move-y="0.02">Sposta in basso</button>
          <button type="button" class="xdecaro-button" data-rotate="-90">Ruota a sinistra</button>
          <button type="button" class="xdecaro-button" data-rotate="90">Ruota a destra</button>
        </div>
      </div>

      <form action="<?= Route::_('index.php?option=com_xdecarophotos&task=photo.saveVariant') ?>" method="post" id="xdecarophotos-studio-form">
        <input type="hidden" name="photo_id" value="<?= (int) $this->photoId ?>">
        <input type="hidden" name="crop_x" id="xdecarophotos-crop-x" value="0">
        <input type="hidden" name="crop_y" id="xdecarophotos-crop-y" value="0">
        <input type="hidden" name="crop_width" id="xdecarophotos-crop-width" value="1">
        <input type="hidden" name="crop_height" id="xdecarophotos-crop-height" value="1">
        <input type="hidden" name="rotation" id="xdecarophotos-rotation" value="0">
        <div class="xdecaro-field">
          <label for="xdecarophotos-preset">Formato</label>
          <select id="xdecarophotos-preset" name="preset">
            <option value="avatar">Avatar</option>
            <option value="passport">Fototessera</option>
            <option value="thumbnail">Miniatura</option>
          </select>
        </div>
        <button type="submit" class="xdecaro-button xdecaro-button--primary">Salva variante</button>
        <?= HTMLHelper::_('form.token') ?>
      </form>
    </div>
  </section>
</div>
