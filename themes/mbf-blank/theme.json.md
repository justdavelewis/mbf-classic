# theme.json — contrast notes (JSON has no comments)

The palette values in `theme.json` are **placeholders**, not a brand. `src/tokens.css` reads
every slot from here (`var(--wp--preset--color--<slot>)`), so a fork re-skins by editing
`theme.json` + `src/tokens.css` only. **Re-measure this whole table on every palette change**,
including the derived roles: changing `contrast` or `base` moves every mix.

Measured 2026-11-05 in Chromium from computed colours (canvas-resolved sRGB, WCAG formula).

## Slots

| Slot | Placeholder | Measured |
|---|---|---|
| `base` | `#ffffff` | page ground |
| `contrast` | `#0b1430` | 18.15:1 on base |
| `primary` | `#2d5ba8` | 6.62:1 on base (as text and as a fill behind white) |
| `secondary` | `#0e1b3d` | 16.9:1 behind white |
| `tertiary` | `#f3f4f6` | contrast on it 16.49:1 |
| `accent` | `#7cc4ff` | **dark grounds only**: 9.01:1 on secondary. Fails on base and primary, so those grounds don't use it |
| `error` (tokens.css only, not an editor choice) | `#b42318` | 6.57:1 on base |

## Grounds (`.mbf-section--*`): every role against its ground

| Ground | text | heading | muted | link | accent / button fill | label on fill | focus ring |
|---|---|---|---|---|---|---|---|
| base | 9.23 | 18.15 | 5.90 | 6.62 | 6.62 | 6.62 | 6.62 |
| tertiary | 8.39 | 16.49 | 5.36 | 6.01 | 6.01 | 6.62 | 6.01 |
| secondary | 16.9 | 16.9 | 10.59 (`#c8cdd6`) | 9.01 | 9.01 | 9.01 | 16.9 |
| primary | 6.62 | 6.62 | 5.68 (`#e9eef7`) | 6.62 | 6.62 | 6.62 | 6.62 |

Form errors (`--section-error`: WS Form's required mark + messages, always with text):
base 6.57, tertiary 5.97, secondary 12.4, primary 4.86 (`error` 20% into `base` on the dark
grounds; primary is the tight one). WS Form on every ground: labels = heading ratio; field
text 9.23 on its white fill; fill vs ground 16.9 (secondary) / 6.62 (primary) is the field's
edge there; border 3.37 / 3.06 on the light grounds; submit = the button row above.

Roles on light grounds: `--color-border` 3.37:1 on base, 3.06:1 on tertiary (meaningful edges:
field, toggle). Button hover (primary + 15% contrast, `#294f95`): white label 7.93:1.

## Token bridge facts (verified 2026-11-05, WP 7.1.4)

- Presets **and** `--wp--style--global--content-size/wide-size` print on `:root` (front end), so
  `--wrap` and `--measure` bridge too.
- WordPress kebab-cases font-size slugs: `h2` → `--wp--preset--font-size--h-2`. docs/12's
  example (`--font-size--h2`) would be undefined.
- `cqi` clamps pass through unchanged with `"fluid": false`.
- Core still prints its own default size/spacing variables (`--wp--preset--font-size--small`,
  `--wp--preset--spacing--20`…) even with the defaults off. Nothing here reads them.
