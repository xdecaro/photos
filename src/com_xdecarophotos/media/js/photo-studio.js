(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  if (root) root.xdecaroPhotosStudio = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';

  async function requestCamera(mediaDevices) {
    if (!mediaDevices || typeof mediaDevices.getUserMedia !== 'function') {
      return { ok: false, reason: 'unavailable', uploadAvailable: true };
    }
    try {
      const stream = await mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'user' } }, audio: false });
      return { ok: true, stream, uploadAvailable: true };
    } catch (error) {
      return { ok: false, reason: 'denied', error, uploadAvailable: true };
    }
  }

  function stopStream(stream) {
    if (!stream || typeof stream.getTracks !== 'function') return;
    stream.getTracks().forEach(track => { if (track && typeof track.stop === 'function') track.stop(); });
  }

  function init(doc, nav) {
    if (!doc) return;
    const file = doc.getElementById('xdecarophotos-file');
    const canvas = doc.getElementById('xdecarophotos-canvas');
    if (!file || !canvas) return;
    const ctx = canvas.getContext('2d');
    const video = doc.getElementById('xdecarophotos-video');
    const cameraButton = doc.getElementById('xdecarophotos-camera');
    const captureButton = doc.getElementById('xdecarophotos-capture');
    const status = doc.getElementById('xdecarophotos-camera-status');
    const zoom = doc.getElementById('xdecarophotos-zoom');
    const cropX = doc.getElementById('xdecarophotos-crop-x');
    const cropY = doc.getElementById('xdecarophotos-crop-y');
    const cropWidth = doc.getElementById('xdecarophotos-crop-width');
    const cropHeight = doc.getElementById('xdecarophotos-crop-height');
    const rotationField = doc.getElementById('xdecarophotos-rotation');
    let image = null;
    let stream = null;
    let state = { x: 0, y: 0, zoom: 1, rotation: 0 };

    function clamp(v) { return Math.max(0, Math.min(1, v)); }
    function sync() {
      const inverse = 1 / state.zoom;
      cropX.value = String(clamp(state.x));
      cropY.value = String(clamp(state.y));
      cropWidth.value = String(Math.min(1, inverse));
      cropHeight.value = String(Math.min(1, inverse));
      rotationField.value = String(state.rotation);
    }
    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      if (!image) return;
      ctx.save();
      ctx.translate(canvas.width / 2, canvas.height / 2);
      ctx.rotate(state.rotation * Math.PI / 180);
      const scale = Math.max(canvas.width / image.width, canvas.height / image.height) * state.zoom;
      ctx.drawImage(image, -image.width * scale / 2 - state.x * canvas.width, -image.height * scale / 2 - state.y * canvas.height, image.width * scale, image.height * scale);
      ctx.restore();
      sync();
    }
    function loadBlob(blob) {
      const url = URL.createObjectURL(blob);
      const img = new Image();
      img.onload = () => { URL.revokeObjectURL(url); image = img; state = { x: 0, y: 0, zoom: 1, rotation: 0 }; zoom.value = '1'; draw(); };
      img.src = url;
    }

    file.addEventListener('change', () => { if (file.files && file.files[0]) loadBlob(file.files[0]); });
    zoom.addEventListener('input', () => { state.zoom = Number(zoom.value) || 1; draw(); });
    doc.querySelectorAll('[data-move-x],[data-move-y],[data-rotate]').forEach(button => {
      button.addEventListener('click', () => {
        if (button.dataset.moveX) state.x = clamp(state.x + Number(button.dataset.moveX));
        if (button.dataset.moveY) state.y = clamp(state.y + Number(button.dataset.moveY));
        if (button.dataset.rotate) state.rotation = (state.rotation + Number(button.dataset.rotate)) % 360;
        draw();
      });
    });

    cameraButton.addEventListener('click', async () => {
      const result = await requestCamera(nav && nav.mediaDevices);
      if (!result.ok) { status.textContent = result.reason === 'unavailable' ? 'Fotocamera non disponibile. Usa Carica foto.' : 'Accesso alla fotocamera non consentito. Usa Carica foto.'; return; }
      stopStream(stream); stream = result.stream; video.srcObject = stream; video.hidden = false; captureButton.hidden = false; await video.play(); status.textContent = 'Fotocamera pronta.';
    });
    captureButton.addEventListener('click', () => {
      if (!video.videoWidth || !video.videoHeight) return;
      const temp = doc.createElement('canvas'); temp.width = video.videoWidth; temp.height = video.videoHeight; temp.getContext('2d').drawImage(video, 0, 0);
      temp.toBlob(blob => {
        if (!blob) return;
        loadBlob(blob);
        if (typeof DataTransfer !== 'undefined' && file) {
          const transfer = new DataTransfer();
          transfer.items.add(new File([blob], 'camera.jpg', { type: 'image/jpeg' }));
          file.files = transfer.files;
        }
      }, 'image/jpeg', 0.92);
      stopStream(stream); stream = null; video.srcObject = null; video.hidden = true; captureButton.hidden = true; status.textContent = 'Foto acquisita. Puoi correggere il ritaglio.';
    });
    sync();
  }

  if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => init(document, navigator));
    else init(document, navigator);
  }
  return { requestCamera, stopStream, init };
});
