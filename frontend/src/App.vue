<template>
  <router-view />
</template>

<script setup>
//
</script>

<style>
/* Brand variables */
:root {
  --brand-primary: #6c5ce7; /* electric purple */
  --brand-accent: #00d4ff;  /* cyan */
  --brand-gradient: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
}
/* Global dark mode base */
body.body--dark, .q-dark {
  color: #e6e7eb;
  background: #121316;
}

/* Override common utility classes that force light styles */
body.body--dark .bg-white { background: #1e1f24 !important; }
body.body--dark .text-dark { color: #e6e7eb !important; }
body.body--dark .text-grey-7 { color: #c7c8cd !important; }
body.body--dark .q-separator { background: #2a2b31 !important; }
body.body--dark .bg-grey-1 { background: #17181c !important; }
body.body--dark .bg-grey-2 { background: #121316 !important; }
body.body--dark .bg-grey-3 { background: #1b1c21 !important; }

/* Card/base surfaces */
body.body--dark .q-card { background: #1e1f24; color: #e6e7eb; }
body.body--dark .q-toolbar { color: inherit; }

/* Links */
body.body--dark a { color: #9bb4ff; }

/* Tables and paginations */
body.body--dark .q-table, body.body--dark .q-table__container { background: #1e1f24; }
body.body--dark .q-table__bottom { background: #1b1c21; border-top: 1px solid #2a2b31; }
body.body--dark .q-pagination { color: #e6e7eb; }

/* Header/Footer generic surfaces if not overridden */
body.body--dark .q-header:not(.header-gradient) { background: #1b1c21; color: #e6e7eb; }
body.body--dark .q-footer { background: #1b1c21; color: #cfd1d6; }

/* Drawer */
body.body--dark .q-drawer { background: #1b1c21 !important; color: #e6e7eb; }

/* Menu & popovers */
body.body--dark .q-menu, body.body--dark .q-popup { background: #1e1f24; color: #e6e7eb; }

/* Chips & badges in dark mode */
body.body--dark .q-chip { background: #24262c; color: #e6e7eb; border: 1px solid #2a2b31; }
body.body--dark .q-badge { background: #2a2b31; color: #e6e7eb; }
body.body--dark .q-chip--colored { color: #e6e7eb; }

/* Buttons & interactive elements transitions */
:root { --ease-quick: 0.18s ease; }
.q-btn, .q-field, .q-card, .q-item { transition: background-color var(--ease-quick), color var(--ease-quick), box-shadow var(--ease-quick), border-color var(--ease-quick); }

/* Subtle hover on cards/lists */
body.body--dark .q-card:hover { box-shadow: 0 10px 28px rgba(0,0,0,0.22); }

/* Focus outlines for accessibility */
*:focus { outline: none; }
.focus-visible, .q-focusable:focus, .q-btn:focus, .q-field__inner:focus-within, a:focus {
  box-shadow: 0 0 0 2px rgba(155,180,255,0.55);
  border-radius: 6px;
}

/* Distinctive primary style: pill buttons with gradient */
.q-btn.q-btn--standard.q-btn--rectangle.q-btn--actionable.q-focusable.q-hoverable.q-btn--no-uppercase.q-btn--dense,
.q-btn.q-btn--standard.q-btn--rectangle.q-btn--actionable.q-focusable.q-hoverable {
  border-radius: 9999px;
}
.q-btn.q-btn--standard.q-btn--rectangle.q-focusable.q-hoverable.q-btn--primary:not(.q-btn--outline):not(.q-btn--flat) {
  background-image: var(--brand-gradient) !important;
  border: none;
}
.q-btn.q-btn--standard.q-btn--rectangle.q-focusable.q-hoverable.q-btn--primary:not(.q-btn--outline):hover {
  box-shadow: 0 6px 18px rgba(108,92,231,0.35);
}

/* Inputs: softer, rounded */
.q-field__control, .q-field--filled .q-field__control {
  border-radius: 12px !important;
}

/* Cards: rounded and elevated slightly in light mode */
.q-card { border-radius: 14px; }
body:not(.body--dark) .q-card:hover { box-shadow: 0 10px 24px rgba(23,24,28,0.08); }

/* Chips: rounded-pill look */
.q-chip { border-radius: 9999px; }

/* Table headers: bold and distinct */
.q-table thead th { font-weight: 600; }

/* Background accent: subtle vignette to avoid flat look */
body:not(.body--dark) .q-page-container { background: radial-gradient(1200px 600px at 90% -10%, rgba(108,92,231,0.06), transparent 60%) !important; }
body.body--dark .q-page-container { background: radial-gradient(1200px 600px at 90% -10%, rgba(108,92,231,0.10), transparent 60%) !important; }
</style>
