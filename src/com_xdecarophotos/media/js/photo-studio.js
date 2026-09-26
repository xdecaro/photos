(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  if (root) root.xdecaroPhotoStudio = api;
})(typeof window !== 'undefined' ? window : globalThis, function () {
  'use strict';
  async function requestCamera(nav) {
    if (!nav || !nav.mediaDevices || typeof nav.mediaDevices.getUserMedia !== 'function') return {available:false,uploadEnabled:true,error:'camera-unavailable',stream:null};
    try {
      const stream = await nav.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});
      return {available:true,uploadEnabled:true,error:null,stream};
    } catch (_) { return {available:false,uploadEnabled:true,error:'camera-unavailable',stream:null}; }
  }
  function stopStream(stream) { if (stream && typeof stream.getTracks === 'function') stream.getTracks().forEach(t => { if (t && typeof t.stop === 'function') t.stop(); }); }
  function bind(doc, nav) {
    const root = doc.querySelector('[data-photo-studio]'); if (!root) return;
    const file = root.querySelector('input[type=file]'), video=root.querySelector('[data-camera-video]'), canvas=root.querySelector('[data-photo-canvas]'), start=root.querySelector('[data-camera-start]'), capture=root.querySelector('[data-camera-capture]'), message=root.querySelector('[data-camera-message]');
    let stream=null; const drawImage=(img)=>{const ctx=canvas.getContext('2d');canvas.width=img.videoWidth||img.naturalWidth||640;canvas.height=img.videoHeight||img.naturalHeight||480;ctx.clearRect(0,0,canvas.width,canvas.height);ctx.drawImage(img,0,0,canvas.width,canvas.height);};
    file.addEventListener('change',()=>{const f=file.files&&file.files[0];if(!f)return;const img=new Image();img.onload=()=>{drawImage(img);URL.revokeObjectURL(img.src)};img.src=URL.createObjectURL(f);});
    start.addEventListener('click',async()=>{const state=await requestCamera(nav); if(!state.available){message.textContent='Fotocamera non disponibile. Puoi comunque caricare e ritagliare una foto.';capture.disabled=true;return;} stream=state.stream;video.srcObject=stream;video.hidden=false;capture.disabled=false;await video.play();message.textContent='Fotocamera pronta.';});
    capture.addEventListener('click',()=>{if(!stream)return;drawImage(video);stopStream(stream);stream=null;video.hidden=true;capture.disabled=true;message.textContent='Foto acquisita. Puoi correggere il ritaglio prima di salvare.';});
    root.querySelectorAll('input[type=range]').forEach(input=>input.addEventListener('input',()=>root.dataset[input.name]=input.value));
    root.closest('form')?.addEventListener('submit',()=>stopStream(stream));
  }
  if (typeof document !== 'undefined') document.addEventListener('DOMContentLoaded',()=>bind(document,navigator));
  return {requestCamera,stopStream,bind};
});
