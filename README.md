# xdecaro Photos

`com_xdecarophotos` is the shared photo management component for the xdecaro Joomla ecosystem.

## Requirements

- Joomla 6.1.3 or later
- Core by xdecaro 2.1.0 or later
- PHP version supported by the installed Joomla 6.1.3+ runtime

Development version: `0.1.0`.

## Development build

```bash
bash build/build.sh
```

The resulting package is written to `dist/pkg_xdecarophotos_0.1.0.zip`.

### v0.1.0 scope

- secure JPEG/PNG/WebP upload validation;
- Core `EntityReference` owner/context boundary;
- Avatar/Fototessera/Thumbnail variants;
- Core UI-backed administrator list and Gallery;
- Photo Studio manual crop controls with optional camera capture;
- diagnostics for Core/UI/storage/runtime image support.

No Joomla 4/5 compatibility is provided. The supported baseline is Joomla 6.1.3+.
