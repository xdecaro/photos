<?php defined('_JEXEC') or die; ?>
<div class="xdecaro-scope">
  <section class="xdecaro-card"><div class="xdecaro-card__body">
    <h2>Diagnostica</h2>
    <dl>
      <dt>Versione Photos</dt><dd><?= htmlspecialchars($this->report['component_version'],ENT_QUOTES,'UTF-8') ?></dd>
      <dt>Versione Core</dt><dd><?= htmlspecialchars($this->report['core_version'],ENT_QUOTES,'UTF-8') ?></dd>
      <dt>EntityReference</dt><dd><?= $this->report['entity_reference']?'Disponibile':'Non disponibile' ?></dd>
      <dt>AssetService</dt><dd><?= $this->report['asset_service']?'Disponibile':'Non disponibile' ?></dd>
      <dt>Storage scrivibile</dt><dd><?= $this->report['storage_writable']?'Sì':'No' ?></dd>
      <dt>Formati server</dt><dd><?= htmlspecialchars(implode(', ',$this->report['image_formats']) ?: 'Nessuno',ENT_QUOTES,'UTF-8') ?></dd>
      <dt>Limite upload PHP</dt><dd><?= htmlspecialchars($this->report['php_upload_limit'],ENT_QUOTES,'UTF-8') ?></dd>
    </dl>
    <p>Fotocamera e funzioni browser sono capacità opzionali e non costituiscono errori del server.</p>
  </div></section>
</div>
