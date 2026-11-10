# STATUS — mbf-blank (classic starter)

> Read this first, every session. Newest entry first. Dated headings. Quote decisions.

**Owners:** David (theme, content, scripts) · Andonette collaborates and reviews standards changes · no live writes (a starter never deploys)
**Environments:** local `https://mbf-classic.local` only (a starter never deploys)
**Repo:** `wp-content/` → `github.com/justdavelewis/mbf-classic` (stays here; Andonette is a collaborator, David 2026-11-06) · no CI

## Outstanding

### DECISION FIRST (don't build on these)
- (none open)
- Q2 **Resolved 2026-11-06:** both in the starter (David: *"yes do both"*). See the dated entry.
- Q3 **Resolved 2026-11-06:** palette off (`--color-*: initial;` in `tokens.css`), David:
  *"turn those tailwind colours off"*. See the dated entry.
### Resolved 2026-11-05 (David: *"Go with your recommendations on D1–D8"*)
- D1 `inc/`: `setup enqueue builder helpers options forms` only. No `woocommerce`,
  `post-types`, `admin-help`, `schema`. (`schema` added 2026-11-06, Q9.)
- D2 Header: single row; dropdowns `<details>`; no mega menu.
- D3 Breakpoint `64rem` (raised to `75rem`, 2026-11-09; see the dated entry).
- D4 Editor presets: fontSizes sm/body/lead/h4–h1; spacing fluid s/m/l/xl. Display stays
  in tokens.css only.
- D5 Components in `src/components/` until `mbf-css` exists.
- D6 Phase 4 provisions a placeholder WS Form by script; ID in Site Settings.
- D7 Gold-standard review recorded here only. Standards README untouched (outside repo).
- D8 Out: `photo` ground, `.mbf-actionbar`, `.mbf-subnav`, `comments.php`, `.mbf-dialog`.
- Q5 Accent control **kept** as the seam forks plug their measured pairs into, although
  it has no visible effect with the placeholder palette (David, 2026-11-05: *"Keep the
  accent field"*).
- Link fields: fixed in the standards (`mbf-site-standards` docs/05 + CHANGELOG, committed and pushed). Buttons use ACF `link`, card links `page_link`; `page_link` has no
  `allow_custom` (David, 2026-11-05: *"correct the bug here and do it in the source
  standards folder"*).

- Q4 Standards fixed in `mbf-site-standards` (committed and pushed), and mirrored here
  (David, 2026-11-05: *"fix those in the standards too"*): docs/12 bridge reads `--h-2` and
  bridges `--wrap`/`--measure`; docs/02 base drops `var(--header-h, 0)` (sticky headers set
  their own) and gains the plain-link rule; `templates/gitignore` gains `!**/src/vendor/`
  (tested in a scratch repo: keeps `src/vendor/`, still drops Composer `vendor/`). Each in
  the standards CHANGELOG.

- Q6 docs/03 card fixes made in the standards too (docs/03 + CHANGELOG, committed and pushed)
  (David, 2026-11-05: *"YES"*).
- Q7 Icons: Font Awesome Pro, the CSS build, ported from Mantech (David, 2026-11-05: *"Use
  font awesome, the snippet should be available in mantech"*). Kit token in Site Settings ›
  Icons (`fontawesome_token`); blank = not loaded, socials fall back to visible names.
  Socials use the Brands family. Mantech's token was not copied (domain-bound, that site's
  setting).

- Q8 Fixed in the standards (docs/02 + docs/14 `main.css` → `source(none)`, docs/02 rule,
  CHANGELOG 2026-11-06; committed and pushed) (David, 2026-11-06: *"fix Q8 in the standards"*).
- Q9 `inc/schema.php` added, **amending D1** (David, 2026-11-06: *"add schema.php"*).

- Q1 Editor checks done 2026-11-06 in Chrome (David logged in).

- Q10 Fixed 2026-11-06 with an editor-only build (`src/editor.css` → `dist/editor.css`) and
  `contentSize` in rem; both also fixed in the standards (David: *"yes do the editor
  stylesheet then tag v1.0"*).

- Site owner styling: **Style Settings** built 2026-11-06 (David: *"an area which enables
  users to have customisation options without having to fiddle with code… fonts (display +
  body), content spacing (fluid-m by default), global colours as a minimum"*; chose colour
  pickers + contrast check, section spacing + corners as extras, fonts bundled in the theme,
  admins only; tabs). Amends D1 again (`inc/style-settings.php`).

### Tasks
- [x] Local on PHP 8.4 (David checked, 2026-11-06)
- [x] ACF Pro + WS Form Pro licences entered (David, 2026-11-06)
- [x] **Before any fork goes live (down the line):** no agent/Claude files on the server
      (David, 2026-11-06). The standards' `templates/deploy.yml` already rsyncs only the theme
      folder (so `docs/` never ships) and excludes `CLAUDE.md`, `.claude/` and `src/`
      (`contract.md`). **Gap:** `theme.json.md` sits in the theme root and isn't excluded, so it
      would be publicly readable. **Fixed at the source 2026-11-06:** the standards' template
      now excludes `*.md` (docs/09: after a first deploy, request `…/theme.json.md` on staging
      and expect a 404). A fork copies that template, so every site gets it.
- [x] Font Awesome kit code in Site Settings › Icons (David, 2026-11-06). Verified: Pro 7.3.1
      CSS loads (200, 454KB), Brands + Pro 400/900 fonts load, LinkedIn/Facebook/Instagram/
      phone/envelope draw, 0 console errors. **The field takes the kit CODE** (the letters in
      `kit.fontawesome.com/<code>.js`), not the script: we load the CSS build with it.

## Live write log

| Date | Who | Command (`wp @live …`) | Backup file | Result / verified |
|---|---|---|---|---|

---

## 2026-11-10 — WordPress conventions: the top five fixes

David: *"Does this build follow proper WP conventions?"*, then *"yes fix the top five"*. Verdict:
mostly yes. The WordPress coding standards checker (WPCS 3, run from a scratch folder, not
installed here) found no escaping, sanitising, translation or PHP 8.4 problems; an independent
review agreed. Fixed:

1. **Site Settings is Editors and up** (`edit_others_pages`, was `edit_posts`, which let
   Contributors and Authors edit site-wide settings). First set to `manage_options`, then David:
   *"Let Editors keep access to Site Settings"*, so client staff can update contact details.
2. **Scripts stop if creating a page fails.** `(int)` of a `WP_Error` is 1, so later writes
   would have landed on post 1.
3. **Contrast messages are whole translatable strings** (`Headings on %s`, ground names
   translated). Same English text.
4. **Core conventions:** `automatic-feed-links`, `post_class()` on posts, `wp_link_pages()`.
5. **ACF switched off:** pages fall back to the page header and `the_content()` instead of only
   the header (tested by deactivating ACF: `/`, `/about/` and a product page return 200 with
   no fatal error). The MCP abilities return "ACF Pro is not active".

Verified: dry runs are no-ops; render fingerprint identical over 126 views (one transient
"failed to load" on `/services/` at 768, clean on 5 reloads). Left: 841 style hits, mostly `[]`
over `array()` (743 auto-fixable), and the 3-letter `mbf` prefix the checker calls short.

---

## 2026-11-10 — MCP adapter: proof of concept, local and read only

David: *"I want to introduce this to this build"* (the WordPress MCP Adapter), *"Local only"*,
*"Read only"*, and *"This is just to make sure it works but eventually i think we could set it
up the complete way"*.

- **`mu-plugins/mbf-mcp.php`** registers three read-only abilities (`mbf/get-site-settings`,
  `mbf/get-page-sections`, `mbf/check-contrast`) and an `mbf` server that exposes only those.
  It does nothing outside a local or development environment, and deploys copy the theme only.
- **The adapter's default server is off.** It serves every ability marked public, and WS Form
  1.12.19 marks 53 of its own public, including reading submissions and deleting forms.
- **No HTTP transport**: STDIO over WP-CLI only. `/wp-json/mcp/…` returns 404.
- **Verified** by speaking MCP over STDIO: 3 tools listed, each returns real data, and WS Form's
  submissions and the adapter's generic execute tool come back "Tool not found".

**The article's setup, run as written (same day, then fully undone).** David: *"run the as-is
test here"* (not on FA: its local copy holds 131 real Woo orders and 89 public abilities,
including `woocommerce/orders-query` and `order-update-status`).
- Default server + HTTP through `@automattic/mcp-wordpress-remote` (0.5.5) + an application
  password: **works**, once Node trusts Local's self-signed certificate:
  `NODE_EXTRA_CA_CERTS=~/Library/Application Support/Local/run/router/nginx/certs/<site>.local.crt`.
  Without it: `DEPTH_ZERO_SELF_SIGNED_CERT`. Don't turn TLS checking off instead.
- An agent then saw and could run 56 abilities: core's 3 and all 53 of WS Form's.
- The WP-CLI route as written also works, but WP-CLI's PHP 8.4 `Deprecated` line lands on stdout,
  inside the protocol stream. `display_errors=stderr` (as in `scripts/mcp-serve.sh`) fixes it.
- Without credentials the HTTP endpoint answers 401.
- Undone: test password revoked, the temporary filter removed, `mbf-mcp.php` restored
  (byte-identical), both endpoints 404 again, the `mbf` server back.

**Set up on a machine** (the plugin isn't in the repo):
```
wp plugin install https://github.com/WordPress/mcp-adapter/releases/download/v0.7.1/mcp-adapter.zip --activate
claude mcp add mbf-local -- "$PWD/scripts/mcp-serve.sh"     # from wp-content/
```

**Later, for "the complete way" (not built):** more read abilities (finder, menus, layouts list);
deciding whether writes go through MCP or stay as dry/force scripts; staging over HTTP with a
limited user and application password; whether the adapter belongs in the standards; pinning the
plugin in a fork script. Live stays out.

---

## 2026-11-09 — End of day: where things stand

**Three stacked PRs, all open. Merge in this order:** #4 (layout library), #5 (home page and page
types), #6 (finder and enquiry form). GitHub only retargets the next PR by itself if the merged
branch is deleted.

**Before merging, still to do:**
- Send a real form submission and try a file upload. Neither has been tested.
- VoiceOver through the finder and the form.
- In wp-admin, add a Hotspots section and the Solution finder settings, and save.
- Decide: does the hero background photo stay (it partly reverses D8), and do we keep `specs`
  (recommended: keep)?
- Andi to review.

**Local state, this database only:**
- The enquiry form is ID 4. The script only creates a form if none has its label, so after editing
  `scripts/data/enquiry-form.json`, trash the old one and re-run `build-sample-page.php force`.
  Forms 2 and 3 are trashed test imports; form 1 is the old placeholder, now unused.
- Two scene images (attachments 49 and 50) are copies of Fisher Alvin's, kept on purpose and
  gitignored. They're tagged `scene-rooftop` / `scene-warehouse`.
- Baseline: `.fingerprints/baseline-page-types.json`, current as of this entry.

**Done since the form entry below:** Previous now sits beside Submit, under the consent box.

**Next, once the PRs are in:** fork script, then a staging test fork, then the mega menu.

---

## 2026-11-09 — Mantech's finder and enquiry form, on our tokens

David: *"Can we take the finder from mantech? it has more options and i like the glassy effect"*,
and *"The default WS Form should be lifted from Mantech/S&A too - the only real difference should
be the site specific instances"*.

**Finder.** Kept our logic (no focus on load, links with no JS). Added from Mantech: six answers,
six site types with icons, three reasons per result, head row, progress bars, two-column tiles.
- **Glass** on a dark hero, the light card on a light one (`mbf_is_dark_ground()`). It keeps the
  ground's own tokens. Solid under `prefers-reduced-transparency`.
- **Hero background photo** (optional, dark grounds only, decorative). Partly reverses D8 ("photo
  ground out"): glass has nothing to blur without it.
- **Veil measured against a pure white photo:** Secondary 80% (accent text 4.78:1, was 3.66 at
  72%), Primary 88% (white text 5.01:1). Both pairs are in `mbf_contrast_pairs()`.

**Enquiry form.** `scripts/data/enquiry-form.json` is Mantech's two-step form, cleaned: placeholder
product list, `#blog_name` / `#blog_admin_email`, no webhook, no client details (scanned). It's
imported through WS Form's restore path, so it has no checksum: to import it in wp-admin, follow
`~/Workspace/ws-form/WS-FORM-MIGRATION-NOTES.md` §6.
- S&A compared from its export: its shared fields are all here; accreditation, locations and
  products are that site's own instances.
- An import gives fields new IDs but leaves old ones in action text, so the file says `{{Name}}`
  and the script fills it in.
- Styling ported to section tokens. Measured on 4 grounds, both steps: all text ≥ 4.5:1, Submit
  hover 4.6:1 at worst (plugin default 1.52:1).
- **KeeQualify stays out of the starter** (David: *"that webhook is unique for every single site
  - as is the code"*). The webhook, attribution fields and scripts, including the `kq-mailto`
  click code, are added per site. Don't add the `[channel]`-style hidden fields without the
  site's script: they submit that literal text.

---

## 2026-11-09 — Home page and one page of each type, as the baseline

David: *"Can we create a homepage and a sample page of each of the page types as a baseline?"*

- **`scripts/build-page-types.php`** (dry/force, idempotent) writes the sections. Run after
  `build-placeholder-content.php` and `build-sample-page.php`. Section order follows what the
  client sites' pages of the same type use.
- **Types:** home, about, product hub and detail, service hub and detail, industry hub and
  detail, case studies, FAQs, contact, blog. Post, legal page, search and 404 already existed.
- **Reading settings:** `/` is the new Home page, `/blog/` the posts page, and `/_sample/` is
  its own page again (it had become the front page, so `/` duplicated it).
- **Fingerprint:** 18 pages × 7 widths = 126 views, 0 overflow, 0 console errors, 0 PHP notices.
  New baseline: `.fingerprints/baseline-page-types.json`. Each type: one h1, no skipped heading
  levels, no two adjacent sections on the same ground.
- **Scene images:** David: *"Keep the FA images in as they are typical of KEE"*. Two local
  attachments tagged `_mbf_sample_asset` = `scene-rooftop` / `scene-warehouse`, in uploads only
  (gitignored). The scripts use them if present; a fork gets the flat placeholder.

- **Nav switch raised to 75rem** (David: *"Go with 75rem and open the PR"*; replaces D3's 64rem).
  Seven top-level items plus the button overflowed by 80px at 1024 and 11px at 1100. The
  placeholder menu stays at five, with Case studies and Blog linked from page content.

---

## 2026-11-09 — Smaller phone markers, equal finder options

David: *"Yeah lets do all of those please"*, after a check of a real client hotspot scene
(18 markers, 2 images) on `/_sample/`, and a read of Mantech's 9 November commits. The client
content was a throwaway local import, never in the repo; placeholders are restored.

- **Hotspot markers are 2rem in a container under 36rem.** At 44px, an 11-marker scene
  overlapped by up to 24% at 390 and 38% at 320. At 32px: none at 390, and at 320 only two
  pairs (3% and 15%), every number still readable. Wider containers are unchanged. 32px passes
  2.5.8, and the list stays the keyboard and screen-reader path.
- **Finder options are equal height** (from Mantech): `grid-auto-rows: 1fr`, and each option
  fills its `li`. Measured with a forced long label through both steps, at 7 widths: one height
  per step. The cost: in one column, a long label makes every option in that step taller.
- **Verified:** before/after fingerprint over 63 views: only the finder options and the markers
  at 390 and 320 changed; 0 overflow, 0 console errors, 0 PHP notices.
- **Standards (same day):** logged-in anchor gotcha corrected, `--delay-updates` on deploys.

---

## 2026-11-06 — Layout library: the sections all our height-safety clients use

David: *"look at all the ACF fields we have across Mantech, S&A & FA and make some very basic
scaffolding for them for each of the sections"*, and the Mantech hero's solution form *"as a
core feature across the board for new builds"*. Hotspots added: *"if we can do it ourselves we
should"*, replacing Image Map Pro.

**Decision (replaces "keep the set small", docs/12):** 11 → 17 layouts. Every client section
folded into one of them. Scaffolding only: fields, semantic markup, dials, contract, sample
row. The look stays in each fork.

- **Extended:** hero (eyebrow, badges, finder panel), text (lead), split (eyebrow, ticks, second
  button), cards (tag, icon, section link), steps (tag, button), stats (accreditations), faq
  (lead, side card), form (reassurance points, phone and email), cta_band (eyebrow).
- **New:** `finder`, `case_study`, `hotspots`, `specs`, `downloads`, `posts`.
- **Finder:** set once per site (my recommendation; not separately confirmed by David), shown
  in a hero or as a section. Fixes from Mantech's wizard: no focus jump on load, one small live
  region rather than the whole panel, and an option with no page goes straight to the form.
- **Hotspots:** one layout for a scene or a product. Fisher Alvin's accessibility model (the
  list is the content, markers aria-hidden, always visible), tabs built by JS, and a click-to-
  place picker in the editor (`assets/js/hotspots-admin.js`), which is what Image Map Pro had.
- **Left for later:** Mantech's checker and suitability (reuse the finder's engine), FA's sticky
  section nav, hero background photos (contrast needs its own answer), mega menu (header job).
  Hotspots on every product: David, *"eventually"*; needs the product pages first.
- **Left out:** S&A's pricing, newsletter, team, bento (agency layouts, not height safety),
  Mantech's Enzuzo legal layout.

**Verified**
- Fingerprint: the 8 pages other than `/_sample/` are IDENTICAL to the baseline (8,121 values).
  All 63 views: 0 overflow, 0 console errors, 0 PHP notices. New baseline:
  `.fingerprints/baseline-layout-library.json`.
- Finder by keyboard: steps, focus on each question, nothing moved on load, link carries
  `?need=roof-edge&site=industrial`. No JS: real links, "Not sure yet" → form.
- `#query_var("need")` on a real WS Form field: `?need=roof-edge` filled it, none left it empty.
  Temporary change to the placeholder form, removed and republished.
- Hotspots: tabs, arrow keys, marker ↔ list both ways, a 100%/100% spot stays inside the image.
  No JS: every scene shows under its heading, no tablist.
- Picker: a real click set Across/Down to 38.5/75.4 in the editor (not saved).
- Contrast: 186 text elements in the new components, all AA, on all 4 grounds.

- 200% zoom (640px at double density, and root font-size 200% at 1280): no overflow.
- Forced colours: markers, list highlight, options, ticks and selected tab all readable.
- Reduced motion: transitions off. Markers are never tab stops. 54 controls: all have a ring.

**Found by those checks and fixed**
- Forced colours paints a transparent border solid, so every tab looked selected. The tabs now
  use `Canvas` and `Highlight` there.
- Linked spot titles and the phone and email links were 20px tall, not inline in a sentence, so
  2.5.8 applied: `display: inline-block` makes them 27px.

**Learned**
- `get_field()` by key returns a field's DEFAULT, so "fill if empty" skipped never-saved fields,
  while `mbf_setting()` by name returned nothing. The script now checks the stored option. A real
  site is unaffected once Site Settings has been saved.
- WS Form's `db_checksum()` needs a user with form capabilities: `wp_set_current_user( 1 )` first.

## 2026-11-06 — Render fingerprint tool (before/after regression)

**Decision (David, 2026-11-06):** *"start on the regression tool"*: make today's ad-hoc
before/after check a tool anyone can run.

**Changed:** the tool lives in the standards (`tools/render-fingerprint.mjs`, run from that
checkout like `verify-tokens.sh`; Playwright pinned to 1.59.1 to reuse the cached Chromium
1217). Here: `docs/fingerprint.json` (base + 9 pages); `.fingerprints/` ignored (~10MB per
capture); theme CLAUDE.md points at both. Improvements over the scratch version: waits for
animations to finish (not a fixed 100ms), keys elements by tree path (not document order),
stores values (not hashes), reports health per view, fails on an empty capture, parallel (4).

**Verified (every claim tested, not assumed):** 63 views, 14,142 values, ~40s (was ~5 min).
Two captures with no change: IDENTICAL, exit 0. Deliberate change (`--layout-gap` fluid-l →
fluid-m, button hover 15% → 30%): exit 1, the hover colour and the gaps (64→40px, 32→24px)
listed first; card sizes summarised after. That ordering came from this test: the first
report buried the gap under 1,100+ size knock-ons. Reverted: IDENTICAL. One inserted
`<div>`: 63 additions, 0 changes. Baseline kept as `.fingerprints/baseline-2026-11-06.json`.

## 2026-11-06 — Header button + footer menu (Q2); global CLAUDE.md; branches

**Decisions (David, 2026-11-06):** *"yes do both, and delete the merged branches"*, i.e. Q2 in
the starter, and the global `~/.claude/CLAUDE.md` changed from importing the standards to
pointing at them.

**Changed:**
- **Header button:** Site Settings › Header › `header_cta` (ACF link; empty = none). Renders via
  `cta-buttons.php` beside the nav at ≥64rem and inside the drawer below it (server class
  `mbf-site-header__cta--drawer`, hidden in the one breakpoint block); with no Primary menu
  there's no drawer, so it stays in the header at every width. `flex-shrink: 0`. The brand's
  `margin-inline-end: auto` replaces `space-between`, so nav + button + toggle group at the end.
- **Footer menu:** `footer` location ("Footer (legal links)"), rendered flat (top level) in the
  legal row as `nav[aria-label=Legal] > ul.mbf-footer__menu`, links underlined in the ground's
  text colour, `padding-block: .25em` for a 24px+ target. Legal row `align-items: baseline`.
- **Placeholder script:** menus via one helper (Primary + Footer), items read from the term's
  raw objects so unpublished targets can't cause duplicates on re-run; publishes WP's draft
  Privacy Policy, adds a Terms page, sets `header_cta` = "Get in touch" → Contact. The empty
  check is `empty()`, not a string cast (a link field returns an array).
- Global `~/.claude/CLAUDE.md`: no import; read the standards in an MBF project that doesn't
  import them yet (outside this repo; standards §9 now holds without exception).
- Branches `feature/style-settings` and `release/v1.0` deleted locally and on GitHub (both
  fully merged into `main`). **The `v1.0` tag is local only: never pushed.**
- Standards: docs/03 (site-header row), docs/05 (Site Settings + two menu locations),
  CLAUDE.md §5, CHANGELOG.

**Verified:** script dry run listed only the new items; force; second dry run a no-op; Primary
still 5 items. 10 page types (incl. Privacy, Terms) × 1440/1280/1024/1023/768/390/320 = 70
views: 0 overflow, button/toggle flush right on the correct side of the switch, footer menu on
every page, 0 console errors, 0 PHP notices. Header button 47–48px, primary on base; in the
drawer accent on secondary (the measured pair). Keyboard: brand → nav → button, 2px rings; the
drawer's copy is not reachable while closed. Footer links white on secondary, 29px targets,
white focus ring. No-menu case (Primary unassigned, then restored by the script): button in
the header at 1440/390/320; at 320 it wrapped to two lines until `flex-shrink: 0`.

## 2026-11-06 — Palette off, deploy gap fixed at the source, housekeeping

**Decisions (David, 2026-11-06):** *"turn those tailwind colours off"*; deploy gap *"noted as
other sites will use this as a framework"*; repo stays at `justdavelewis/mbf-classic`
(Andonette is a collaborator); Andonette approved the component-dials rule.

**Changed:** `tokens.css` opens `@theme static` with `--color-*: initial;`. Standards: the
same line in the seed + docs/01; `templates/deploy.yml` excludes `*.md` instead of naming
files, docs/09 says why and to check `…/theme.json.md` 404s on staging; CHANGELOG (dials
marked approved). Theme CLAUDE.md: PHP 8.4.27, plugins licensed. STATUS header + stale
"not committed" notes fixed; Q3 closed.

**Verified:** a probe template with `text-red-500 bg-slate-900 text-primary bg-tertiary`
built to scratch before/after: before, both palette classes compiled; after, neither does
and both role classes still do. `dist/` has 0 palette variables, `--color-primary` intact.
Full fingerprint (8 page types × 5 widths, dropdown/drawer open, hovers): 15,915 values,
**0 diffs** against a fresh baseline. verify-tokens: none undefined. Font Awesome: see Tasks.

## 2026-11-06 — Component dials: the other 19 components + `--layout-gap`

**Decision (David, 2026-11-06):** *"commit it, then do the rest of the components"*.

**Changed:** dials blocks on page-hero, section-heading, enquiry, cta-band, logos,
grid, stats, quote, split, social, crumbs, search, steps, faq, pagination, footer, skip,
chevron, nav, site-header, drawer, each declared on the component's root (for section
layouts, the `<section>` carrying the block class). The column layouts gained a
`--{block}-columns` dial (the breakpoint itself can't be one).
New global **`--layout-gap`** (`fluid-l`, 43 globals), the gap between layout columns and
tracks, which seven components repeated: grid, split, hero, stats, steps, footer, enquiry.
`type.css` left alone: `.display`/`.h*` *are* the global type scale (its dials are theme.json
font sizes), and `.mbf-meta` is one two-value rule. Standards: docs/01 + seed + CLAUDE.md +
README (43 globals, `--layout-gap`), CHANGELOG.

**Verified:** full fingerprint of every element (+ `::before/::after`, 54 properties) on 8
page types (`/_sample/`, `/`, `/page/2/`, a post, `/contact/`, a child page, search, 404) at
1440/1024/1023/390/320, plus the nav dropdown open, the drawer open and 9 hover kinds:
15,915 values, 27 diffs, all the open dropdown's summary colour + chevron rotation read at
100ms, mid-transition (three identical runs gave 204–210°; settled = `#2d5ba8`, 225° exactly
as the rules say). Script wait raised to 500ms. One dial per component then turned, and
each block followed (e.g. `--layout-gap` 64→16px moved stats and hero together; crumbs
separator "/"→"›"; drawer 346→256px). 0 console errors (the 404 page's own status aside),
0 PHP notices, verify-tokens: none undefined. Screenshots 1440/390 unchanged.

**Removed `.mbf-stack`** (David, 2026-11-06: *"remove the stack"*): no template used it, so
it was just-in-case CSS every fork would inherit. docs/03's catalogue keeps the recipe for a
site that needs one. Rebuilt: no `mbf-stack` in either dist file; none in any served page.

## 2026-11-06 — Component dials: card, button, hero, section

**Decision (David, 2026-11-06):** blocks get "dials" so one value re-skins every instance,
*"sure it takes more set up but makes it infinitely easier to change things"*. Developer-only
(CSS, no wp-admin fields). Start with four components, then review. No utility-class layer
(*"thats more aimed at people doing my job"*). Andonette to review the docs/03 rule.

**Changed:** `card.css` (+ radius, pad, gap, title size, image ratio), `button.css` (dials
fill/label/fill-hover/label-hover/border/radius/font/size/weight; state `--button-bg/-text`
swapped on hover; secondary re-sets dials only, at `.mbf-button.mbf-button--secondary`),
`hero.css` (dials on `.mbf-hero`: gap, text gap, measure, image ratio and radius),
`section.css` (pad-y/-x, gap, wrap). Standards: docs/03 "Component dials", CLAUDE.md §3,
CHANGELOG.

**Verified:** computed styles for every section, card, button and hero on `/_sample/` and
`/` at 1440 and 390, plus 48 hover states: 504 values, **0 differences** before/after. Each
dial turned and the block followed (pad 40→5px, radius 12→0, title 24→40px, button radius
6→0 and weight 600→800, fill → test colour, hero gap 64→7px and ratio 4/3→1/1, section gap
64→9px and padding 88→11px). Two things measured: an ancestor's `--card-pad` is ignored (the
card's own declaration wins over inheritance), so overrides target the component; and a
later `.mbf-button { --button-fill }` turned the secondary solid until the variant went to
0-2-0. 0 console errors, 0 PHP notices, verify-tokens: none undefined.

**Next:** review the pattern, then the other ~21 components. Then `--grid-gap` (cards, logos,
stats and steps all repeat a grid gap).

## 2026-11-06 — Style Settings (site owners change the look without code)

**Changed (branch `feature/style-settings`, committed `0ce821b`):** Site Settings › **Style**
(admins only), tabs Colours / Typography / Spacing / Shape (`acf-json/group_mbf_style_settings`,
`ui_options_page_mbf_style_settings`). `inc/style-settings.php`: saved choices → theme.json via
`wp_theme_json_data_theme` (full palette + `custom.*`), cache cleared on save, font choices from
theme.json `fontFamilies`, and a **contrast check on save** (PHP OKLab/OKLCH port of the CSS
`color-mix` derivations + WCAG ratio over the theme.json.md matrix; three worst failures per
field, then "and N more"). `theme.json`: `serif` system stack; `settings.custom` defaults.
`tokens.css`: `--font-sans`/`--font-display`/`--content-gap`/`--section-y` scale/radii read
`--wp--custom--*` (42 globals). CSS: content-item gaps → `--content-gap`; headings, `.h*`,
brand text → `--font-display`. Standards: docs/01, 05, 12, 16, seed, CLAUDE.md, README, CHANGELOG.
**Verified:**
- PHP maths vs the 13 browser-measured ratios in theme.json.md: worst diff 0.005; default
  palette passes.
- Chrome (admin): failing primary `#7cc4ff` refused, field lists the pairs + ratios, nothing
  saved (CLI + front end still `#2d5ba8`). `#1b6b4a` (the fork-test green) **also refused**:
  a hovered light button on the primary ground at 4.49:1, which no earlier sweep measured.
  `#196645` saved with serif display, Large gap, Roomy, Round: front end primary/button
  `#196645`, serif headings with sans body, gaps 61.9px, section padding 110px, radius
  24px/999px; editor iframe the same (preset, tokens, fonts, picker palette). With those
  settings: 4 URLs × 5 widths, 0 contrast failures on all grounds, 0 overflow, 0 notices,
  0 console errors.
- **Found and fixed:** the first Update stored `font_body = sans` (a select's default) and
  froze it against later theme.json changes. Every select now defaults to "Theme default (…)",
  which overrides nothing; an untouched save → `mbf_style_overrides()` = `[]`.
- Fork test (theme.json only: purple primary, serif display, square corners, small gap):
  all applied. Restored. Access: Editor role lacks `manage_options`, Administrator has it.
- Back at defaults: identical to before (primary `#2d5ba8`, 88px section padding, 12/6px
  radius, Medium gap); 6 URLs × 7 widths clean; `verify-tokens.sh` none undefined.
**Deliberately not done:** colour presets/style variations (pickers chosen instead); Google
Fonts; buttons, links, borders and cards as separate settings (designer territory, per David).

---

## 2026-11-06 — Editor stylesheet (Q10) + v1.0

**Changed:** `src/editor.css` → `dist/editor.css`, loaded by `add_editor_style()` instead of
`dist/style.css`: Tailwind theme + utilities but **no Preflight**, Typography applied to the
editor root (`className: is-root-container`), Preflight's `border: 0 solid` kept for prose
(a quote's rule had width but `border-style: none`), root `max-width: none` so WordPress's
layout sets the width. `vendor/typography.css` maps `:is(.prose, .is-root-container)`: one
recipe for both. `package.json` builds both files. `theme.json` `contentSize` `68ch` →
**`45.5rem`** (`ch` resolved per element: the 34px post title went 1516px wide and pinned
left; 45.5rem = the same 728px for every element). Version 1.0.0 (`style.css`,
`package.json`). Standards: docs/01, 02, 05, 09, 10, 12, 16, `templates/deploy.yml`, CHANGELOG.
**Verified:** editor (Chrome): title, p, h2, ul all at left 348 / width 728; h2 44px/700 in the
heading colour; bullets; links underlined in primary; quote 4px solid; tokens resolve
(`--measure` 45.5rem); 0 console errors; `dist/style.css` no longer in the iframe. Front end
(Playwright), 6 URLs × 6 widths: prose still 728px (and narrower on small screens), quote 4px,
0 overflow, 0 contrast failures, 1 h1, 0 notices, 0 console errors. `verify-tokens.sh`: none
undefined. A brace slip while moving a block broke the editor build once; caught because only
one of the two builds reported "Done", rewritten and re-verified.
**Tagged:** `v1.0` (see the release notes in the tag).

---

## 2026-11-06 — Editor + ACF admin checks (Q1)

**Changed:** nothing in code.
**Verified (Claude in Chrome, logged in as the local admin):**
- Token bridge **in the editor iframe**: presets and layout sizes print on the iframe's
  `:root`; our tokens resolve there (`--color-primary` #2d5ba8, `--fs-h2` and
  `--space-fluid-m` keep their `cqi` clamps, `--wrap` 80rem, `--font-sans`); paragraph text
  gets `--color-text`. docs/12's three checks now pass in both places.
- Editor presets: colours = our 6 slots only, custom colour off; font sizes = sm/body/lead/
  h4–h1 (D4), custom off; spacing = fluid s/m/l/xl. WordPress keeps its default palette
  (12), gradients (12) and duotones (8) registered under the `default` origin, but
  `defaultPalette/Gradients/Duotone: false`, so the editor doesn't offer them.
- ACF admin: the Page layouts box on About (empty) and on /_sample/ (44 rows, in order,
  Content + Section tabs, controls holding `ground/accent/spacing`); Site Settings with
  Identity/Contact/Social/Icons/Forms tabs and the placeholder values. 0 console errors on
  both after a reload.
**Found:** editor content unstyled (Q10).

---

## 2026-11-06 — Standards brought level with mbf-blank

**Changed (mbf-site-standards, uncommitted, on `main`):** every fix and lesson from phases
0–5 is now in the source standards, not only in this theme (David: *"Make sure its fixed at
the source too so i can push the site build rules company wide"*): docs/05 (schema.php
pattern; WS Form allow-list gate, template provisioning, `--wsf-*` styling seam, outline +
44px fixes; Font Awesome CSS build + token + Brands; verified `get_row()` shape, composite
clone key, inactive clone group), docs/01 + 03 (section family + `-on-accent/-link/-error`,
card re-scope, `__inner` `minmax(0, 1fr)`, drawer forced-colours edge), docs/08 (prove
notices visible; screenshot limit), docs/10 (7 symptom rows), docs/12 (contract location),
README (gold-standard review detail), CHANGELOG 2026-11-06. Earlier the same day/yesterday:
docs/02 (`source(none)`, base), 03 (card), 05 (link fields), 12 (bridge), 14, gitignore.
**Verified:** grep for the superseded guidance (`allow_custom`, `--h2` preset,
`var(--header-h`, plain `@import "tailwindcss";`, the classic `VERIFY on first build`)
finds nothing outside CHANGELOG, except docs/02's explanation of the removal and docs/16's
block-field VERIFY (block track, not exercised here, correctly still open).
**Not done:** committing/pushing the standards (needs David's go, and a branch: it's `main`).

---

## 2026-11-06 — Q8 standards fix + `inc/schema.php` (Q9)

**Changed:** standards (not committed): docs/02 + docs/14 `main.css` snippets →
`@import "tailwindcss" source(none)`, with the reason in docs/02's rules; CHANGELOG
2026-11-06. Theme: `inc/schema.php` (loaded from `functions.php`): one `Organization` node in
`<head>` from Site Settings (name, url, logo, telephone, email, address as one line, socials
as `sameAs`; empty values omitted; `@id` `/#organization`). Off when Yoast, Rank Math,
SEOPress or AIOSEO is active; `mbf_print_business_schema` filter overrides per page.
`JSON_HEX_TAG` so a value can't close the script tag.
**Why:** David: *"fix Q8 in the standards and add schema.php"*. docs/05: exactly one
business node per page, from Site Settings, gated where an SEO plugin outputs one.
**Verified:** WP-CLI: default prints one node with no empty keys; a company name containing
`</script>` stays escaped and parses; with a stub `RankMath` class it prints nothing; the
filter forces it back on. Playwright, 14 URLs (incl. 404, search, archive, /_sample/):
exactly 1 JSON-LD block, parses, 1 business node, in `<head>`, name + phone match the
footer, 0 notices, 0 console errors.
**Deliberately not done:** `LocalBusiness` subtype, opening hours, geo (per fork: the
starter has no premises data); BreadcrumbList / FAQPage / Article (per fork).
**Noticed, not changed:** WordPress's `home` URL here is `http://mbf-classic.local` while
Local serves https, so `@id`/`url` are http. That's this Local site's options, not the theme.

---

## 2026-11-05 — Phase 5: verification sweep + fork test

**Changed:** `src/main.css` → `@import "tailwindcss" source(none)` (Q8; found by the fork
test). Nothing else: this phase is verification.
**Verified (Playwright, Chromium; Local `https://mbf-classic.local`):**
- **URL statuses** (15: home, page 2, /_sample/, pages, child pages, posts, category,
  search hit/miss, 404): all as expected; `?p=1` → 301 to its permalink; canonicals 200
  on-host. 0 PHP notices (notices proved visible in Phase 1). 0 console errors (only the
  404 page's own status), 0 failed requests. Landmarks 1/1/1 and one h1 on every URL. No
  adjacent sections share a ground. No leaks (staging host, test emails). No upscaled
  images. **0 business nodes (Q9).**
- **Widths** 1440/1280/1024/1023/768/390/320 × 12 URLs (84 checks): 0 horizontal overflow,
  header 1 row, every `__inner` edge = the header edge (above and below `--wrap`), nav ↔
  drawer exactly at 1024/1023, 0 margins between section children (all `gap`).
- **Keyboard** (/_sample/, 76 stops per pass): first stop the skip link, every stop has a
  ≥2px ring (cards on the card), 0 backwards jumps, 0 off-screen stops. Drawer, dropdown,
  FAQ and form keyboard behaviour verified in phases 1/3/4.
- **Forced colours:** same 76 stops all ringed; borders/outlines kept (phases 1/3/4).
- **Reduced motion:** 791 elements, 0 transitions > 1ms, 0 animations, scroll-behaviour
  auto; card lift none; drawer slide none. (The skip link reaches its focused position one
  frame late under `reduce`: docs/02's base rule gives every property a .01ms transition.
  Harmless; measured visible at 12px.)
- **200% zoom** (1280 → 640 CSS px): 5 templates, 0 overflow, drawer takes over; h1 69.9 →
  96.6 device px, body ×2. **Text spacing (1.4.12)** at 1280/390: 0 clipping, 0 fixed
  heights on text, 10/10 cards keep their text.
- **Contrast:** every text element on its real background ≥ AA, on 6 URLs × 5 widths
  (/_sample/ alone: 265 elements on all 4 grounds).
- **Tokens:** `verify-tokens.sh` → consumed-but-undefined: none. No colour literal in
  code outside `tokens.css`/`theme.json` (one in a comment).
- **Fork test:** changed ONLY `theme.json` (palette → green/aubergine/gold, font → Georgia)
  and `tokens.css` (radii → 0, error colour). On /_sample/, /about/, home and 404: **0
  computed colours left from the old palette** (header, footer, cards, buttons, WS Form
  included), all 6 new slots in use, serif on body and headings, card/button radius 0, WS
  Form fields followed the new base. Screenshot: cards on the new secondary. Restored both
  files byte-identical (`shasum`).
  The restore's `dist/` checksum didn't match the pre-test one: Tailwind's automatic
  source detection had scanned Markdown/JSON, so Phase 4's docs edits had changed the CSS.
  Fixed with `source(none)`; proved by adding words to `contract.md` (dist unchanged) and
  removing them (rebuilt identical). Re-ran contrast/overflow/h1/sr-only/prose on 6 URLs ×
  5 widths after the change: clean.
**NOT verified:** the editor half of the token bridge and the ACF admin screens (Q1: Chrome
still at the login screen); Font Awesome glyphs (no kit token); a real form submission end
to end; VoiceOver itself (the accessibility tree and names were checked, not a screen
reader); browsers other than Chromium.

### v1.0 done when (docs/12)
- [x] The build passes and `dist/style.css` (43.7KB) and `dist/editor.css` (21.7KB) aren't empty.
- [x] `/_sample/` renders every layout on every ground, with 0 console errors and 0 notices.
- [x] The full `08` sweep passes at all widths, plus keyboard, forced colours, reduced
      motion and 200% zoom. (Business node: 0 at first; `schema.php` added 2026-11-06 → 1
      on every URL.)
- [x] The token bridge is verified in the editor and on the front end (editor 2026-11-06,
      after David logged Chrome in). See Q10: the bridge works, but editor content is unstyled.
- [x] Fork test: change only `theme.json` and `tokens.css`, and the site fully re-skins.
- [x] `CLAUDE.md` (rules) and `docs/STATUS.md` (history) exist.

---

## 2026-11-05 — Phase 4: /_sample/

**Changed:** `scripts/build-sample-page.php` (dry/force, idempotent): draws 4 neutral
placeholder PNGs with GD (no binaries in the repo; matched by `_mbf_sample_asset` meta),
creates the placeholder WS Form **"MBF placeholder enquiry"** from WS Form's own Contact
Us template (Mantech's `db_create` → `db_update_from_object` → `db_publish` path) and sets
Site Settings › Forms to its ID (D6), and builds `/_sample/`: 11 layouts × 4 grounds = 44
sections, grounds cycling so no neighbours match, card counts 3/2/4/1 per ground (auto-fill,
pairs, pairs + unlinked, lone), split image side alternating, CTA bands with no second
button (phone fallback). Rows match by a hash in `_mbf_sample_hash`. ·
`src/vendor/wsform.css` (WS Form's `--wsf-*` variables → our families; outline focus and
44px targets) · `--section-error` added to the section family · contract + theme.json.md.
**Why:** brief phase 4; docs/08 (`/_sample/` during the build, deleted at launch).
**Fixed on the way (found by the sweep):** WS Form labels 1.24:1 on secondary and 3.17:1
on primary, required mark 1.02:1 on primary → variables; WS Form's `outline: 0` left
fields with **no visible focus** → unlayered 0-5-0 outline rule (seam #3, its selector is
0-4-1); fields/submit 43px → `min-block-size: var(--button-h)`.
**Verified:**
- Script: dry → force → force again no-op, and a final dry run lists nothing; exactly 1
  form (published), 4 attachments, 1 page.
- Action gate in this env (`local`): database/message/redirect run; email/webhook/hubspot
  don't.
- `/_sample/` × 7 widths (1440/1280/1024/1023/768/390/320): 44 sections, 0 adjacent same
  ground, 1 h1, 0 duplicate ids (4 form instances), 0 overflow, 0 notices, 265 text
  elements all ≥ AA, 0 console errors, 0 failed requests. Only small target: the GDPR
  checkbox ≈24px, which its clickable `<label>` enlarges.
- Forms on base/secondary/primary: outline on fields + submit in the ground's ring; 48px
  tall; empty submit shows "This field is required." in `--section-error`, linked by
  `aria-describedby`.
- Screenshots: 1280 + 390 full page (the 1280 capture repeats past Chromium's 16,384px
  limit; DOM confirmed 1 header, 44 sections), form on secondary, logos on tertiary.
**NOT verified:** a real submission end to end (would write a local submission; the gate
logic is verified directly); Font Awesome glyphs (no token yet); editor (Q1).
**Deliberately not done:** styling for WS Form field types the template doesn't use
(select, radio, file); per-row form choice (one site-wide enquiry form).

---

## 2026-11-05 — Phase 3: partials + 11 layouts

**Changed:** `template-parts/` card, section-heading, cta-buttons, page-hero, crumbs,
social-links, enquiry-form · `template-parts/blocks/` hero, text, split, cards, cta-band,
faq, stats, quote, steps, logos, form ($args only; each returns nothing without its
required content) · `src/contract.md` (one entry per partial and layout) · 16 component
CSS files + `.mbf-button--secondary`, `.mbf-buttons`, `.mbf-grid--pairs` · `inc/forms.php`
(WS Form: outside production only `database`/`message`/`redirect` actions run;
allow-list) · helpers `mbf_link()`, `mbf_grid_class()`, `mbf_crumbs()`;
`mbf_section_attrs()` takes the layout's own classes; builder passes `position` (a hero
owns the h1 only as row 1) · image size `card` 800×500 · page/single/index/search/404
use page-hero (+ crumbs); index/search list posts as h2 cards · footer socials.
**Why:** brief phase 3; docs/03 catalogue + linked-card rules; docs/05 partials.
**Standards fixes first** (David: *"fix those in the standards too"*): see Q4 in Resolved.
**Verified** on a throwaway page (all 11 layouts across all 4 grounds, two hero rows,
temporary images + socials; all deleted after, verify URL now 404):
- Outline: 1 h1 (first hero), second hero → h2; every `aria-labelledby` resolves; 0
  duplicate ids. Crumbs right on page/child page/post/search/404, none on the front page.
- Cards: one link per linked card, link name = title, CTA aria-hidden, image `alt=""` +
  aria-hidden; unlinked card has no link, no lift. Keyboard: one tab stop per linked card,
  ring on the card in the section's ring colour (white on secondary), link ring removed;
  clicking card text follows the link; subgrid rows align at 1440/1280/768 **after the
  fix** (Q6a); blog index = 10 single-link h2 cards.
- Contrast: 64 text elements on their real backgrounds, 0 below AA (incl. white cards on
  the dark ground, after Q6b). Primary-ground button hover 4.6:1 (tight; re-measure on a
  palette change).
- CTA band with no second button → "Call 01234 567890" (`tel:`). Split image-start: image
  left visually, DOM text first. FAQ opens/closes with Enter. Steps numbers are `::before`
  with alt "". Socials 48px targets.
- 9 URLs × 7 widths: 0 overflow, 1 h1, 0 notices, 0 console errors, 0 failed requests.
- Reduced motion: card lift was still 2px (**fixed**, Q6c), now none. Forced colours: card
  outline + border, step numbers, quote rule all kept. Screenshots 1280/390 full page.
**NOT verified:** WS Form rendering (no form until phase 4's script); editor + ACF admin
screens (Q1).
**Deliberately not done:** card modifiers `--feature/--compact/--flush-media` (no
consumer); FAQ `::details-content` animation (13 tier 2, optional); `FAQPage` /
`BreadcrumbList` schema (`schema.php` out per D1); WS Form variable styling
(`src/vendor/wsform.css`) until a real form renders in phase 4; social icons (Q7).

---

## 2026-11-05 — Phase 2: ACF (layouts, section controls, Site Settings)

**Changed:** `acf-json/` (readable keys, filename = key): `group_mbf_section_controls`
(ground/accent/spacing; inactive, clone-only), `group_mbf_page_layouts` (one
`page_layouts` Flexible Content, the 11 docs/12 layouts, each = Content tab + Section tab
with the seamless clone), `group_mbf_site_settings` (company name, logo, **white logo**,
phone, email, address, legal line, socials repeater, enquiry form ID),
`ui_options_page_mbf_site_settings`. · `inc/options.php` (`mbf_setting()`,
`mbf_company_name()`, `mbf_logo( $ground )`, `mbf_tel_href()`) · `inc/builder.php`
(`mbf_render_layouts()`, `mbf_row_args()`, `mbf_layout_names()`) · `inc/helpers.php`:
`MBF_GROUNDS`, `mbf_is_dark_ground()` (the one list), `mbf_ground_accent()`,
`mbf_accent_colour()` (the contrast guard, measured matrix in its comment),
`mbf_section_attrs()` (BEM modifiers only), heading-id registry, `mbf_rows()` ·
`section.css` accent + spacing modifiers · `footer.css` · header/drawer/footer read Site
Settings (logo by ground) · `page.php`: title unless the first section is a hero →
layouts → `the_content()` only when there are no sections · `index.php` renders the posts
page's sections after the list · placeholder script also fills empty Site Settings (keys
read from acf-json).
**Why:** brief phase 2; docs/05 + docs/12 §Builder, §FSE-ready (rows hold content only;
field names = future block attributes).
**Verified:**
- ACF loads all four from local JSON; the clone expands into every layout although its
  group is inactive; options page registers (`edit_posts`). Locations: page →
  page_layouts only; post → none; options page → site_settings only.
- `get_row( true )` (docs/05 "VERIFY on first build"): keyed by **name**, clone flattened
  to top-level `ground/accent/spacing`. Throwaway probe page, deleted after. The clone's
  `_page_layouts_N_ground` reference key is composite
  (`field_mbf_cards_controls_field_mbf_section_ground`); `update_field()` by name handles it.
- Guard: `accent: primary` on secondary → re-mapped, no modifier emitted; heading ids dedupe
  (`our-services`, `our-services-2`). Accent × ground matrix measured in Chromium.
- Missing template → `<!-- mbf: no template for "…" -->` for editors only (anonymous: empty).
- ACF deactivated: 3 URLs 200/404, 0 notices, footer falls back to the site title.
  Reactivated.
- Logo by ground with two temporary PNGs: header = main logo; drawer + footer = white
  logo; 44px tall; alt = company name. Test logos unset and attachments deleted after.
- Placeholder script: dry → force → second run no-op.
- Sweep 7 URLs × 7 widths (incl. 1024/1023): 0 overflow, 1 h1, 0 notices, footer NAP
  present, footer edges = header edges. 0 console errors, 0 failed requests. Footer
  contrast on secondary: links/address 16.9:1, legal (muted) 10.59:1. Screenshots
  1280/320 footer, 390 logos.
**NOT verified:** ACF admin screens rendered, and the editor token check (Q1).
**Deliberately not done:** layout partials (phase 3, so every layout currently prints the
admin-only comment); socials output (social-links partial, phase 3); `inc/forms.php`
(phase 3, with the form layout); `mbf_linked_text()`, `mbf_kses_svg()`,
`mbf_prose_classes()` (no consumer yet; prose already follows the ground through
`--tw-prose-*` → section vars, so `mbf_prose_classes()` may never be needed).

---

## 2026-11-05 — Phase 1: skeleton

**Changed:** `style.css` (header only) · `theme.json` v3 (placeholder palette, core presets
and custom values off, `fluid: false`) · `theme.json.md` (measured ratios) ·
`functions.php` → `inc/setup.php`, `inc/helpers.php` (menu tree + one renderer for nav and
drawer), `inc/enqueue.php` (mtime versions, `defer`, emoji off, block-library CSS off on
singular content without blocks) · `package.json` (Tailwind 4.3.4 CLI + Typography
0.5.20) · `src/main.css`, `base.css`, `wordpress.css`, `vendor/{typography,dialog}.css`,
`components/{type,section,stack,grid,button,chevron,skip,site-header,nav,drawer,pagination,search}.css`
· `src/tokens.css` bridged (19 lines → `var(--wp--preset--*)` / `--wp--style--global--*`) ·
templates `header footer index page single 404 search` + `searchform.php` ·
`assets/js/{nav,drawer}.js` · `scripts/build-placeholder-content.php` (dry/force; 5 pages,
12 posts, Primary menu) · theme CLAUDE.md (breakpoint, plugins, bridge) · theme activated.
**Why:** docs/12 skeleton; D1–D8 as resolved above. Header/drawer ported from Mantech's
pattern (menu rendered twice from one source; `<details>` dropdowns; `<dialog>` drawer),
with `commandfor`/`closedby` doing the open/close and JS only as fallback.
**Verified (Playwright, Chromium):**
- Token bridge, front end: presets **and** layout sizes print on `:root`; every bridged
  token resolves (h1 54.7px at 1440 = the cqi clamp); `cqi` clamps pass through unchanged;
  `static` kept: all 42 seed lines in `dist/`. Found: WP kebab-cases `h2` → `h-2` (Q4).
- Widths 1440/1280/1024/1023 (switch)/768/390/320 × 10 URLs: 0 horizontal overflow, 1
  `<h1>` each, header 1 row, edges aligned (header = main = footer), nav ↔ toggle swap
  exactly at 1024/1023. **Fixed on the way:** 11px overflow at 320 from `auto` grid tracks
  growing to a search field's intrinsic width → `minmax(0, 1fr)` on `__inner`, `.mbf-stack`,
  `.mbf-search`. Dropdown ran 12px from the viewport edge → end-aligned. Drawer left a
  6px backdrop sliver at 390 → a `--button-h` tap-to-close strip.
- 0 console errors (only the 404 page's own document status), 0 failed requests.
  0 PHP notices on 10 URLs, **proved** by a throwaway probe that a notice does appear in
  the HTML (probe deleted).
- Keyboard: skip link first + visible + moves focus to main; tab order brand → nav;
  `aria-current` on About; dropdown opens on Enter, Esc closes + focus to summary, closes on
  outside click and when focus tabs past. Drawer: modal, scroll lock, Esc/close
  button/backdrop all close, focus returns to toggle, closes when widened past 64rem.
- Contrast on 4 grounds × 7 roles: all ≥ AA (lowest muted on tertiary 5.36:1; border on
  tertiary 3.06:1). Table in `theme.json.md`.
- Reduced motion: drawer translate none, transitions ~0. Forced colours: focus outline,
  button/field borders, hamburger, chevron kept; **fixed** missing drawer edge
  (transparent border). 200% zoom: h1 56.5 → 83.4 device px, body ×2, no overflow.
- Screenshots: 1280 dropdown, 390 drawer, 320 404, 768 blog, forced-colours drawer.
**NOT verified:** the editor half of the token bridge (Q1). Generating a login cookie via
WP-CLI was blocked by the permission classifier, so no wp-admin session. JS fallbacks
for `commandfor`/`closedby` not exercised (this Chromium supports both).
**Deliberately not done:** builder, Site Settings, logo, footer NAP (phase 2); card +
count-aware grid (phase 3); header CTA, footer menu (Q2); `.mbf-stack--loose` (no
consumer yet). Local-only `mu-plugins/mbf-local-debug.php` shows notices; gitignored,
not part of the theme. Base adds one rule beyond the docs/02 list (plain-link underline, Q4).

---

## 2026-11-05 — Phase 0: gold-standard check + plan

**Changed:** No theme code. This STATUS entry + the DECISION FIRST list above.
**Why:** docs/12 requires a gold-standard check before building `mbf-blank`.
**Checked** (GitHub API, 2026-11-05):

| Starter | State | Fit |
|---|---|---|
| Underscores `Automattic/_s` | **archived**, last push 2025-09-05 | Dead |
| Twenty Twenty-One (last classic default) | repo archived 2020; ships with core | A theme, not a starter |
| `_tw` (gregsullivan) | active, last push 2026-04-04, Tailwind 4 since 2025-10 | Closest. `_s` + Tailwind utilities in markup, plain `@theme` + full TW default theme, PostCSS, no ACF, no `$args` partials, no strict_types/defer |
| Sage (roots) | active, 2026-10-07 | Blade + Acorn + Vite: not plain PHP `$args` partials |
| WP Rig | active, 2026-10-03 | Large framework (~470 files: components, customizer, PWA, Jetpack, AI tooling). Not lean |

**Recommendation:** none fits the stack. Keep `mbf-blank` as our own code (docs/12 step 3).
**Taken:** (1) `_tw` bridges `theme.json` palette → `@theme` via
`var(--wp--preset--color--*)`, the same pattern as our token bridge; it confirms the
approach, and we keep `static`. (2) `_tw` builds a separate editor CSS with Preflight
scoped, so check `add_editor_style( 'dist/style.css' )` doesn't break editor UI in phase 1.
(3) WP Rig's WP 7.1 note: the post editor is fully iframed in 7.1, so editor JS must not
query the top-level `document`, and the token-bridge editor check must look inside the
iframe.
**Verified:** repo states via api.github.com; `_tw` source read (tailwind.css,
tailwind-theme.css, functions.php, theme.json). Block-track starters not checked: that's
`mbf-block` (16).
**Deliberately not done:** any build, pending go-ahead and D1–D8.

---

## Conventions worth not relearning (project-specific)
- WP-CLI here needs Local's socket: `wp --exec="ini_set('mysqli.default_socket','<Local run dir>/mysql/mysqld.sock');" …`
  (Homebrew WP-CLI 2.12 on PHP 8.5 prints deprecations; filter them, they aren't ours).
- `mu-plugins/mbf-local-debug.php` (gitignored) is what makes PHP notices visible locally.
- Tailwind builds from the `@source` paths only (`source(none)`): adding a doc never changes `dist/`.
- Full-page screenshots over 16,384px repeat (Chromium limit): check the DOM, screenshot by element.

## Settings that do NOT travel (re-set per environment)
- `WP_ENVIRONMENT_TYPE` (dangerous when UNSET → production → leads fire)
- `blog_public` (dangerous when CARRIED OVER → live noindexed)
- Licences: ACF Pro, form plugin, SEO plugin — domain-bound
- …
