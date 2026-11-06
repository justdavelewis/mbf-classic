# Markup contract — mbf-blank

CSS is half a component; this is the other half (docs/14). Each entry is the structure the
partial emits and the CSS relies on. Moves to `mbf-css/contract.md` once that package exists
(STATUS D5). Change a class or an element here → it's a **major** change for every fork.

Every layout is a `section` built by `mbf_section_attrs()`:

```
section.mbf-section.mbf-{layout}[.mbf-section--{ground}][.mbf-section--accent-{slot}][.mbf-section--{spacing}]
        aria-labelledby → its heading id (only when it has a heading)
  .mbf-section__inner          container-type: inline-size; capped at --wrap
```

`ground` ∈ tertiary | secondary | primary (base = no modifier). `accent` only when
`mbf_accent_colour()` lets an editor's choice through. `spacing` ∈ flush-top | flush-btm | flush.

## Shared partials (`template-parts/`)

```
card.php                     ($args: title, text, url, image, level, cta)
article.mbf-card
  img.mbf-card__image         optional; alt="" aria-hidden="true"; size 'card' (16:10)
  .mbf-card__body             required; carries the padding
    h{level}.mbf-card__title  h2 under an archive h1, h3 under a section h2
      a.mbf-card__hit         title text ONLY — the one link, stretched by ::before
    p.mbf-card__text          optional
    span.mbf-card__cta        aria-hidden="true", never a link; only when linked
  · no url → no a, no cta, no lift (lift is gated on :has(.mbf-card__hit))
  · in .mbf-grid: subgrid rows image | title | text | cta
  · surface is always light: re-scopes --section-text/heading/muted and --card-accent
    to the light-ground roles; keeps the section's --focus-ring (the ring is outside)

section-heading.php          ($args: heading, lead, id, level=2, size='h2')
header.mbf-section-heading
  h{level}.{size}#{id}        optional
  p.lead.mbf-section-heading__lead   optional

cta-buttons.php              ($args: buttons[ACF link], phone_fallback)
.mbf-buttons
  a.mbf-button                first
  a.mbf-button.mbf-button--secondary   any second; "Call {phone}" when phone_fallback
    span.sr-only "(opens in a new tab)"   only for target=_blank

page-hero.php                ($args: title, lead, meta, datetime, crumbs)
.mbf-section.mbf-section--flush-btm.mbf-page-hero > .mbf-section__inner
  nav.mbf-crumbs              via crumbs.php
  .mbf-page-hero__text
    h1.h1                     the page's only h1
    p.mbf-meta > time[datetime]   optional
    p.lead                    optional

crumbs.php                   ($args: items[[label, url]]) — hidden for a 1-item trail
nav.mbf-crumbs[aria-label=Breadcrumb] > ol.mbf-crumbs__list[role=list]
  li.mbf-crumbs__item > a.mbf-crumbs__link | span[aria-current=page] (last)

social-links.php             ($args: links[[network, url]], icons)
ul.mbf-social[role=list] > li > a.mbf-social__link[.mbf-social__link--icon]
  i.fa-brands.fa-{mark}.mbf-social__icon[aria-hidden] + span.sr-only {name}   icons on
  {name} as visible text                                icons off, or unmapped network
  · icons = Site Settings › Icons has a Font Awesome kit token (CSS build, Brands family)

enquiry-form.php             ($args: form_id)
.mbf-form > form.wsf-form     admin-only comment when WS Form or the ID is missing
  · styled through WS Form's own --wsf-* variables (src/vendor/wsform.css, unlayered):
    labels/required mark/errors read the section family; fields are a light surface
  · focus = our outline (WS Form's box-shadow is overridden); fields + submit ≥ --button-h
```

## Layouts (`template-parts/blocks/`)

```
hero.php        heading(req), lead, button_1, button_2, image, position
  section.mbf-hero[.mbf-hero--media] > __inner > .mbf-hero__grid
    .mbf-hero__text > h1|h2.display#id  p.lead  .mbf-buttons
    img.mbf-hero__image       library alt; eager + fetchpriority=high when first
  · h1 only when position = 1 (page.php then skips page-hero)

text.php        heading, body
  section.mbf-text > __inner > header.mbf-section-heading  .prose

split.php       heading, body, image(req), image_side(end|start), button_1
  section.mbf-split.mbf-split--image-{side} > __inner > .mbf-split__grid
    .mbf-split__text > header.mbf-section-heading  .prose  .mbf-buttons
    img.mbf-split__image      library alt
  · DOM is always text → image; image-start moves it with grid-column

cards.php       heading, lead, cards[title(req), text, url, image]
  section.mbf-cards > __inner > header.mbf-section-heading
    .mbf-grid[.mbf-grid--pairs] > article.mbf-card × n   (--pairs: exactly 2 or 4)

cta-band.php    heading(req), text, button_1, button_2
  section.mbf-cta-band > __inner > .mbf-cta-band__row
    .mbf-cta-band__text > h2.h2#id  p.lead
    .mbf-buttons              phone_fallback: no button_2 → "Call {phone}"

faq.php         heading, items[question(req), answer]
  section.mbf-faq > __inner > header.mbf-section-heading
    .mbf-faq__list > details.mbf-faq__item
      summary.mbf-faq__question  text + span.mbf-chevron[aria-hidden]
      .mbf-faq__answer.prose

stats.php       heading, items[value(req), label]
  section.mbf-stats > __inner > header  ul.mbf-stats__list[role=list]
    li.mbf-stat > span.mbf-stat__value  span.mbf-stat__label   (DOM = visual order)

quote.php       quote(req), name, role
  section.mbf-quote > __inner > figure.mbf-quote__figure
    blockquote.mbf-quote__text > p   figcaption.mbf-quote__cite (name, role)

steps.php       heading, lead, items[title(req), text]
  section.mbf-steps > __inner > header  ol.mbf-steps__list[role=list]
    li.mbf-steps__item > h3|h2.h4.mbf-steps__title  p
  · number = CSS counter in ::before with alt "" (the list position is announced)

logos.php       heading, logos[attachment IDs]
  section.mbf-logos > __inner > header  ul.mbf-logos__list[role=list] > li > img (library alt)

form.php        heading, text   (form ID: Site Settings form_enquiry)
  section.mbf-enquiry > __inner > .mbf-enquiry__grid
    header.mbf-section-heading  .mbf-form
```

Each layout returns nothing when its required content is missing, so an empty row never
prints an empty section.
