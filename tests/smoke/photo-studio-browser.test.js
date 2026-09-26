'use strict';
const assert = require('assert');
const studio = require('../../src/com_xdecarophotos/media/js/photo-studio.js');
(async () => {
  let state = await studio.requestCamera(undefined);
  assert.equal(state.ok, false); assert.equal(state.reason, 'unavailable'); assert.equal(state.uploadAvailable, true);
  state = await studio.requestCamera({ getUserMedia: async () => { throw new Error('denied'); } });
  assert.equal(state.ok, false); assert.equal(state.reason, 'denied'); assert.equal(state.uploadAvailable, true);
  const track={stopped:false,stop(){this.stopped=true;}}; const stream={getTracks(){return [track];}};
  state = await studio.requestCamera({ getUserMedia: async () => stream });
  assert.equal(state.ok, true); assert.equal(state.stream, stream); studio.stopStream(stream); assert.equal(track.stopped,true);
  console.log('PASS photo-studio-browser.test.js');
})().catch(err => { console.error(err); process.exit(1); });
