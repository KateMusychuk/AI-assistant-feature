<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { aiAssistantStore, PAGE_TYPE_MAP, DEFAULT_PAGE_TYPE } from '$lib/stores/ai-assistant';
  import { fetchOverdue, fetchSettingsCapabilities } from '$lib/services/ai-assistant';
  import AiAssistantFab from './AiAssistantFab.svelte';
  import AiAssistantPanel from './AiAssistantPanel.svelte';

  $: isOpen     = $aiAssistantStore.isOpen;
  $: overdueItems = $aiAssistantStore.overdueItems;
  $: hasOverdue = overdueItems.length > 0;

  // Hide widget on course pages (AI Teacher is used there)
  $: isCourseRoute = $page.routeId?.includes('library/course') ?? false;

  // Update page context in store whenever route changes
  $: {
    const pathname = $page.url.pathname;
    const match = PAGE_TYPE_MAP.find((m) => m.pattern.test(pathname));
    const type = match?.type ?? DEFAULT_PAGE_TYPE.type;
    aiAssistantStore.setPageContext({ type, url: pathname });
  }

  // Settings-tab suggests are filtered by the account's portal_user_profile
  // form fields and available MFA methods. Load those on entry to any
  // /settings/* page so the panel has the data ready when the student opens it.
  $: currentType = $aiAssistantStore.pageContext.type;
  $: if (typeof window !== 'undefined' && currentType.startsWith('settings')) {
    if (!$aiAssistantStore.settingsCapabilities) {
      fetchSettingsCapabilities();
    }
  }

  // Forward the student's URL-prefix language so `/overdue` returns localized
  // item titles (parallel to /chat). Falls back to backend default if the
  // path prefix is missing.
  $: currentLang = $page.url.pathname.split('/')[1] || 'en';

  onMount(async () => {
    // Fetch overdue items for FAB badge; panel transitions to empty after
    await fetchOverdue(currentLang);
    aiAssistantStore.setState('empty');
  });
</script>

{#if !isCourseRoute}
  {#if isOpen}
    <AiAssistantPanel />
  {/if}
  <AiAssistantFab {hasOverdue} />
{/if}
