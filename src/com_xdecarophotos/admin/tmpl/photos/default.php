<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
?>
<div class="xdecaro-scope xdecarophotos-admin">
  <form action="<?= Route::_('index.php?option=com_xdecarophotos&view=photos'); ?>" method="post" id="adminForm" name="adminForm">
    <section class="xdecaro-card">
      <div class="xdecaro-card__body">
        <div class="xdecaro-table-wrap">
          <table class="xdecaro-table">
            <thead><tr><th>ID</th><th>Foto</th><th>Proprietario</th><th>Tipo</th><th>Stato</th></tr></thead>
            <tbody>
            <?php foreach($this->items as $item): ?>
              <tr>
                <td><?= (int)$item->id ?></td>
                <td><?= htmlspecialchars((string)$item->title,ENT_QUOTES,'UTF-8') ?></td>
                <td><?= htmlspecialchars($item->owner_component.' / '.$item->owner_entity.' / '.$item->owner_id,ENT_QUOTES,'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)$item->photo_type,ENT_QUOTES,'UTF-8') ?></td>
                <td><span class="xdecaro-badge <?= (int)$item->status===1?'xdecaro-badge--success':'xdecaro-badge--warning' ?>"><?= (int)$item->status===1?'Attiva':'Non pubblicata' ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <?= HTMLHelper::_('form.token'); ?>
  </form>
</div>
