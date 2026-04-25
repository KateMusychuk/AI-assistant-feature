<script lang="ts">
  import { aiAssistantStore } from '$lib/stores/ai-assistant';
  import { muiStore } from '$lib/stores';

  export let hasOverdue = false;

  $: fabLabel   = $muiStore['ai_assistant_fab_label'] || 'AI Assistant';
  $: badgeLabel = $muiStore['ai_assistant_fab_badge_label'] || 'Overdue items';

  const toggle = () => {
    if ($aiAssistantStore.isOpen) {
      aiAssistantStore.close();
    } else {
      aiAssistantStore.open();
    }
  };
</script>

<div class="asa-fab-wrap">
  <button
    class="asa-fab"
    class:asa-fab--active={$aiAssistantStore.isOpen}
    on:click={toggle}
    aria-label={fabLabel}
  >
    <!-- Neural Spark icon -->
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="12" r="2.2" fill="currentColor"/>
      <line x1="12" y1="3" x2="12" y2="7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="3" y1="12" x2="7" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="17" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="5.6" y1="5.6" x2="8.4" y2="8.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="15.6" y1="15.6" x2="18.4" y2="18.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      <line x1="18.4" y1="5.6" x2="15.6" y2="8.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      <line x1="5.6" y1="18.4" x2="8.4" y2="15.6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
    </svg>

    {#if hasOverdue}
      <span class="asa-fab__badge" aria-label={badgeLabel} />
    {/if}

    {#if !$aiAssistantStore.isOpen}
      <span class="asa-fab__tooltip" aria-hidden="true">{fabLabel}</span>
    {/if}
  </button>
</div>

<style>
  .asa-fab-wrap {
    position: fixed;
    bottom: 24px;
    inset-inline-end: 24px;
    z-index: 1000;
  }

  .asa-fab {
    width: 56px;
    height: 56px;
    border-radius: calc(50% * var(--ki-border-radius-scale, 1));
    background: var(--kc-color-primary, #ff881a);
    /* SVG strokes inherit via currentColor so the icon always matches whatever
       foreground the portal picked for text on primary buttons (white on
       KC orange / blue / most brands; dark on light-cream brands). */
    color: var(--kc-button-primary-text-color, #fff);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
  }

  .asa-fab:hover,
  .asa-fab:focus-visible {
    transform: scale(1.08);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }

  .asa-fab--active {
    background: #1f2023;
    /* Active FAB has a fixed dark background, so the icon must stay white
       regardless of the portal's button-text override (which can be dark for
       light-cream brand palettes). */
    color: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
    transform: scale(1);
  }

  .asa-fab__badge {
    position: absolute;
    top: 4px;
    inset-inline-end: 4px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #f0213e;
    border: 2px solid white;
  }

  .asa-fab__tooltip {
    position: absolute;
    inset-inline-end: calc(100% + 12px);
    top: 50%;
    transform: translateY(-50%);
    background: #1f2023;
    color: #fff;
    font-size: 13px;
    line-height: 1;
    padding: 8px 12px;
    border-radius: calc(8px * var(--ki-border-radius-scale, 1));
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
  }
  .asa-fab__tooltip::after {
    content: '';
    position: absolute;
    top: 50%;
    inset-inline-start: 100%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-inline-start-color: #1f2023;
  }
  .asa-fab:hover .asa-fab__tooltip,
  .asa-fab:focus-visible .asa-fab__tooltip {
    opacity: 1;
  }

  @media (max-width: 480px) {
    .asa-fab--active {
      display: none;
    }
  }
</style>
