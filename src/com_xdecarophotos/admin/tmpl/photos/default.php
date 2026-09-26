<?php defined('_JEXEC') or die; ?>
<div class="xdecaro-scope">
  <div class="xdecaro-toolbar"><a class="xdecaro-button xdecaro-button--primary" href="index.php?option=com_xdecarophotos&amp;view=studio">Nuova foto</a></div>
  <div class="xdecaro-table-wrap"><table class="xdecaro-table"><thead><tr><th>ID</th><th>Tipo</th><th>Proprietario</th><th>Stato</th></tr></thead><tbody><?php foreach($this->items as $item): ?><tr><td><?php echo (int)$item->id; ?></td><td><?php echo htmlspecialchars((string)$item->photo_type,ENT_QUOTES,'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)$item->owner_component.'/'.$item->owner_entity.'/'.$item->owner_id,ENT_QUOTES,'UTF-8'); ?></td><td><?php echo (int)$item->status; ?></td></tr><?php endforeach; ?></tbody></table></div>
</div>
