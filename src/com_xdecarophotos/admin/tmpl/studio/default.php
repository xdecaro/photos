<?php defined('_JEXEC') or die; use Joomla\CMS\HTML\HTMLHelper; ?>
<div class="xdecaro-scope photo-studio" data-photo-studio>
  <form action="index.php?option=com_xdecarophotos&amp;task=photo.upload" method="post" enctype="multipart/form-data" class="xdecaro-card">
    <div class="xdecaro-card__body">
      <input type="hidden" name="owner_component" value="<?php echo htmlspecialchars($this->ownerComponent, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="owner_entity" value="<?php echo htmlspecialchars($this->ownerEntity, ENT_QUOTES, 'UTF-8'); ?>">
      <div class="xdecaro-field"><label for="owner_id">ID proprietario</label><input id="owner_id" name="owner_id" type="text" required value="<?php echo htmlspecialchars($this->ownerId, ENT_QUOTES, 'UTF-8'); ?>"></div>
      <input type="hidden" name="context_component" value="">
      <input type="hidden" name="context_entity" value="">
      <input type="hidden" name="context_id" value="">
      <input type="hidden" name="photo_type" value="original">
      <input type="hidden" name="is_primary" value="1">
      <div class="xdecaro-field"><label for="photo_image">Foto</label><input id="photo_image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required></div>
      <div class="photo-studio__camera">
        <button type="button" class="xdecaro-button" data-camera-start aria-label="Apri fotocamera">Apri fotocamera</button>
        <button type="button" class="xdecaro-button" data-camera-capture aria-label="Scatta foto" disabled>Scatta foto</button>
        <video data-camera-video playsinline muted hidden></video><canvas data-photo-canvas aria-label="Anteprima e ritaglio foto"></canvas>
        <p data-camera-message role="status"></p>
      </div>
      <fieldset class="photo-studio__controls"><legend>Ritaglio</legend>
        <label>Posizione X <input type="range" name="crop_x" min="0" max="1" step="0.01" value="0" aria-label="Posizione orizzontale ritaglio"></label>
        <label>Posizione Y <input type="range" name="crop_y" min="0" max="1" step="0.01" value="0" aria-label="Posizione verticale ritaglio"></label>
        <label>Larghezza <input type="range" name="crop_width" min="0.1" max="1" step="0.01" value="1" aria-label="Larghezza ritaglio"></label>
        <label>Altezza <input type="range" name="crop_height" min="0.1" max="1" step="0.01" value="1" aria-label="Altezza ritaglio"></label>
        <label>Zoom <input type="range" name="zoom" min="1" max="3" step="0.05" value="1" aria-label="Zoom foto"></label>
        <label>Rotazione <input type="range" name="rotation" min="-180" max="180" step="90" value="0" aria-label="Rotazione foto"></label>
        <label>Preset <select name="preset"><option value="avatar">Avatar</option><option value="passport">Fototessera</option></select></label>
      </fieldset>
    </div>
    <div class="xdecaro-card__footer"><button class="xdecaro-button xdecaro-button--primary" type="submit">Salva foto</button></div>
    <?php echo HTMLHelper::_('form.token'); ?>
  </form>
</div>
