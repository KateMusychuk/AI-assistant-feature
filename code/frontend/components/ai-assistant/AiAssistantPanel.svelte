<script lang="ts" context="module">
  interface CardData {
    id: string;
    type: string;
    title: string;
    description: string;
    /** ISO date `YYYY-MM-DD` for overdue / upcoming CARDs (QA-NEW-14.d).
     *  Empty for recommendation CARDs. */
    dueIso?: string;
    /** Backend progress_status: `not_started` | `in_progress` | `viewed` |
     *  `passed` (QA-NEW-14.e). Empty if not applicable. */
    statusKey?: string;
  }

  interface MessagePart {
    kind: 'text' | 'card';
    html?: string;
    card?: CardData;
  }

  // CARD tokens accept both legacy 4-field (prose description only) and
  // 6-field QA-NEW-14.c/d/e tokens with structured due_iso + status_key.
  // See parseCardToken — single regex scans the token span, pipe-split.

  /** Scan `[CARD:...]` token at a given position and return its fields.
   *  Accepts 4- or 6-pipe variants; fields past the 4th default to empty. */
  function parseCardToken(inner: string): CardData {
    const fields = inner.split('|').map((f) => f.trim());
    return {
      id: fields[0] ?? '',
      type: fields[1] ?? '',
      title: fields[2] ?? '',
      description: fields[3] ?? '',
      dueIso: fields[4] ?? '',
      statusKey: fields[5] ?? ''
    };
  }

  /** Parse assistant message into text parts and card parts */
  function parseMessage(text: string): MessagePart[] {
    const parts: MessagePart[] = [];
    let lastIndex = 0;
    let lastWasCard = false;

    // When the AI writes a sentence that ends with a CARD ("... assignment:
    // [CARD:...]. It's recommended..."), the trailing punctuation after the
    // token ends up at the start of the next text block and reads as an
    // orphan period. Trim leading .,;: + whitespace when a text block comes
    // right after a card.
    const stripLeadingOrphan = (s: string) => s.replace(/^[\s.,;:!?]+/, '');

    // Walk every `[CARD:...]` span; content between opening `[CARD:` and
    // closing `]` is pipe-split by parseCardToken (handles both 4 and 6
    // fields, so legacy history keeps working).
    const tokenRe = /\[CARD:([^\]]+)\]/g;
    for (const match of text.matchAll(tokenRe)) {
      if (match.index! > lastIndex) {
        let textBefore = text.slice(lastIndex, match.index!);
        if (lastWasCard) textBefore = stripLeadingOrphan(textBefore);
        if (textBefore.trim()) {
          parts.push({ kind: 'text', html: formatText(textBefore) });
        }
      }

      parts.push({ kind: 'card', card: parseCardToken(match[1]) });

      lastIndex = match.index! + match[0].length;
      lastWasCard = true;
    }

    // Remaining text after last card
    if (lastIndex < text.length) {
      let remaining = text.slice(lastIndex);
      if (lastWasCard) remaining = stripLeadingOrphan(remaining);
      if (remaining.trim()) {
        parts.push({ kind: 'text', html: formatText(remaining) });
      }
    }

    // No cards found — format entire text
    if (parts.length === 0) {
      parts.push({ kind: 'text', html: formatText(text) });
    }

    return parts;
  }

  /** Simple markdown-like formatter */
  function formatText(text: string): string {
    let html = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');

    const lines = html.split('\n');
    const result: string[] = [];
    let listType: 'ul' | 'ol' | null = null;

    const closeList = () => {
      if (listType) {
        result.push(`</${listType}>`);
        listType = null;
      }
    };

    for (const line of lines) {
      const trimmed = line.trim();

      if (/^[-*]\s+/.test(trimmed)) {
        if (listType !== 'ul') {
          closeList();
          result.push('<ul class="asa-md-list">');
          listType = 'ul';
        }
        result.push(`<li>${trimmed.replace(/^[-*]\s+/, '')}</li>`);
        continue;
      }
      if (/^\d+\.\s+/.test(trimmed)) {
        if (listType !== 'ol') {
          closeList();
          result.push('<ol class="asa-md-list">');
          listType = 'ol';
        }
        result.push(`<li>${trimmed.replace(/^\d+\.\s+/, '')}</li>`);
        continue;
      }
      closeList();
      if (trimmed === '') {
        result.push('<br/>');
      } else {
        result.push(`<span>${line}</span>`);
      }
    }
    closeList();

    return result.join('\n');
  }

  function cardButtonLabel(type: string, mui: Record<string, string>): string {
    switch (type) {
      case 'learning_path': return mui['ai_assistant_card_cta_learning_path'] || 'Go to learning path';
      case 'skill_path':    return mui['ai_assistant_card_cta_skill_path']    || 'Go to skill path';
      default:              return mui['ai_assistant_card_cta_course']        || 'Go to course';
    }
  }

  function cardUrl(lang: string, card: CardData): string {
    // ?from=ai marks that the navigation originated from the AI widget;
    // destination pages use it to suppress stale breadcrumbs (BUG-8).
    switch (card.type) {
      case 'learning_path':
      case 'skill_path':
        return `/${lang}/assignments/learning-path/${card.id}?from=ai`;
      default:
        return `/${lang}/library/course/${card.id}?from=ai`;
    }
  }
</script>

<script lang="ts">
  import { tick } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { aiAssistantStore, PAGE_TYPE_MAP, DEFAULT_PAGE_TYPE, SUGGESTED_PROMPTS } from '$lib/stores/ai-assistant';
  import { muiStore } from '$lib/stores';
  import { chat } from '$lib/services/ai-assistant';
  import datetimeHelper from '$lib/helpers/datetime';

  $: state       = $aiAssistantStore.panelState;
  $: messages    = $aiAssistantStore.messages;
  $: inlineError = $aiAssistantStore.inlineError;
  $: inlineErrorText = $aiAssistantStore.inlineErrorMessage
    || $muiStore['ai_assistant_error_connection_lost']
    || 'Connection was lost.';
  $: overdueItems = $aiAssistantStore.overdueItems;
  $: hasOverdue  = overdueItems.length > 0;

  // Localized chrome — each falls back to English if MUI is not loaded yet.
  $: t = {
    title:           $muiStore['ai_assistant_title']                 || 'AI Student Assistant',
    panelAria:       $muiStore['ai_assistant_panel_aria_label']      || 'AI Student Assistant',
    subtitle:        $muiStore['ai_assistant_subtitle']              || "Ask me anything — I'm here to help.",
    promptsHeading:  $muiStore['ai_assistant_prompts_heading']       || 'Suggested for you',
    loading:         $muiStore['ai_assistant_loading']               || 'Loading your assistant…',
    thinking:        $muiStore['ai_assistant_thinking']              || 'Thinking',
    inputPlaceholder:$muiStore['ai_assistant_input_placeholder']     || 'Ask anything…',
    inputAria:       $muiStore['ai_assistant_input_aria_label']      || 'Message input',
    sendAria:        $muiStore['ai_assistant_send_aria_label']       || 'Send',
    closeAria:       $muiStore['ai_assistant_close_aria_label']      || 'Close',
    moreOptionsAria: $muiStore['ai_assistant_more_options_aria_label']|| 'More options',
    clearConv:       $muiStore['ai_assistant_clear_conversation']    || 'Clear conversation',
    retry:           $muiStore['ai_assistant_retry']                 || 'Try again',
    footerNote:      $muiStore['ai_assistant_footer_note']           || 'This is powered by AI and may make mistakes.',
    overdueYouHave:  $muiStore['ai_assistant_overdue_you_have']      || 'You have',
    overdueItemOne:  $muiStore['ai_assistant_overdue_item_one']      || 'overdue item',
    overdueItemOther:$muiStore['ai_assistant_overdue_item_other']    || 'overdue items',
    overdueView:     $muiStore['ai_assistant_overdue_view']          || 'View',
    // CARD chrome (QA-NEW-14.c/d/e). Due-date label comes from the existing
    // P2MyLearning group; status labels are reused from the `page` group's
    // mylearning-* keys that the MyLearning page already uses.
    cardDueLabel:    $muiStore['due_date']                                    || 'Due date',
    cardStatus_not_started: $muiStore['mylearning-content-courses-notStarted']   || 'not started',
    cardStatus_in_progress: $muiStore['mylearning-content-courses-inprogress']   || 'In Progress',
    cardStatus_viewed:      $muiStore['mylearning-contetn-chosenCourses-viewed'] || 'viewed',
    cardStatus_passed:      $muiStore['mylearning-content-chosenCourses-passed'] || 'Passed'
  };

  $: lang = $page.url.pathname.split('/')[1] || 'en';

  $: pageContext = (() => {
    const pathname = $page.url.pathname;
    const match = PAGE_TYPE_MAP.find((m) => m.pattern.test(pathname));
    return match ?? DEFAULT_PAGE_TYPE;
  })();

  // Resolve the context label via MUI; blank fallback keeps the pill hidden
  // until MUI has loaded rather than flashing an English word in a non-EN UI.
  $: pageContextLabel = $muiStore[pageContext.labelKey] || '';

  // Resolve each prompt's display text from MUI and keep the raw textKey for
  // de-duping against already-sent messages.
  $: rawPrompts = SUGGESTED_PROMPTS[pageContext.type] ?? SUGGESTED_PROMPTS['general'] ?? [];
  $: normalizedPrompts = rawPrompts.map((p) => ({
    textKey: p.textKey,
    requires: p.requires,
    text: $muiStore[p.textKey] || ''
  }));

  // Settings-tab prompts drop out when the required capability is missing.
  // Outside settings: `requires` is never set, so the filter is a no-op.
  // On settings pages before capabilities load: show everything (better than
  // flashing an empty panel); once capabilities arrive we re-filter reactively.
  $: settingsCaps = $aiAssistantStore.settingsCapabilities;
  $: isSettingsContext = pageContext.type.startsWith('settings');
  $: filteredByCapability = normalizedPrompts.filter((p) => {
    if (!p.text) return false;
    if (!p.requires) return true;
    if (!isSettingsContext) return true;
    if (!settingsCaps) return true;
    if (p.requires === 'pin') return settingsCaps.hasPin;
    return settingsCaps.fields.includes(p.requires);
  });

  // Filter out prompts the user already sent
  $: usedTexts = new Set(messages.filter(m => m.role === 'user').map(m => m.content));
  $: chatPrompts = filteredByCapability.filter(p => !usedTexts.has(p.text));

  let inputValue = '';
  let lastSentMessage = '';
  let messagesEl: HTMLElement;
  let dropdownOpen = false;
  let dropdownEl: HTMLElement;
  let dropdownTriggerEl: HTMLButtonElement;
  let dropdownMenuEl: HTMLElement;

  const handleDocumentClick = (e: MouseEvent) => {
    if (dropdownOpen && dropdownEl && !dropdownEl.contains(e.target as Node)) {
      dropdownOpen = false;
    }
  };

  const handleDocumentKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') {
      if (dropdownOpen) {
        // Escape first closes the dropdown (nested focus trap),
        // returning focus to the trigger.
        dropdownOpen = false;
        dropdownTriggerEl?.focus();
      } else {
        // When no nested overlay is open, Escape closes the whole panel —
        // standard a11y for modal / side-panel widgets.
        aiAssistantStore.close();
      }
    }
  };

  /**
   * Open the dropdown and move focus into the first menuitem. Called from both
   * a mouse click on the trigger and ArrowDown / Enter / Space keyboard
   * activation — so keyboard users reach "Clear conversation" without having
   * to Tab past it.
   */
  const openDropdownAndFocus = async () => {
    dropdownOpen = true;
    await tick();
    const firstItem = dropdownMenuEl?.querySelector<HTMLElement>('[role="menuitem"]');
    firstItem?.focus();
  };

  const handleDropdownTriggerKeydown = (e: KeyboardEvent) => {
    // ArrowDown / Enter / Space open the menu AND focus the first item.
    // On a native <button> Enter/Space also fires click, so we pre-empt only
    // ArrowDown here; click-open is handled in the on:click handler, which
    // we extend below to move focus when the menu opens.
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      openDropdownAndFocus();
    }
  };

  const handleDropdownItemKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') {
      e.preventDefault();
      dropdownOpen = false;
      dropdownTriggerEl?.focus();
    }
  };

  const scrollToBottom = async () => {
    await tick();
    // messagesEl is inside .asa-pb (the scrollable container)
    const scrollParent = messagesEl?.parentElement;
    if (scrollParent) scrollParent.scrollTop = scrollParent.scrollHeight;
  };

  $: messages, scrollToBottom();
  $: {
    const last = messages[messages.length - 1];
    if (last?.streaming) scrollToBottom();
  }

  const sendMessage = async (text?: string) => {
    const msg = (typeof text === 'string' ? text : null) ?? inputValue.trim();
    if (!msg || state === 'thinking') return;

    inputValue = '';
    lastSentMessage = msg;
    aiAssistantStore.pushMessage({ role: 'user', content: msg });

    await chat({
      message: msg,
      history: messages.slice(0, -1),
      pageContext: $aiAssistantStore.pageContext,
      lang
    });
  };

  const retryLastMessage = async () => {
    // QA-8: retry must NOT push a duplicate user-bubble — the original user message
    // is still in `messages` (only the assistant response errored), so reuse it.
    if (!lastSentMessage || state === 'thinking') return;
    aiAssistantStore.setInlineError(false);
    await chat({
      message: lastSentMessage,
      history: messages.slice(0, -1),
      pageContext: $aiAssistantStore.pageContext,
      lang
    });
  };

  const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  const sendSuggested = (prompt: string) => sendMessage(prompt);

  const viewOverdue = () => {
    // Use the exact URL param format the Assignments page expects:
    // filter prefix `filter_by_` + group key `due_date` + value `overdue`.
    goto(`/${lang}/assignments?filter_by_due_date=overdue`);
    aiAssistantStore.close();
  };

  const goToCard = (card: CardData) => {
    goto(cardUrl(lang, card));
    aiAssistantStore.close();
  };

  const clearConversation = () => {
    aiAssistantStore.clearMessages();
    lastSentMessage = '';
    dropdownOpen = false;
  };
</script>

<svelte:window on:click={handleDocumentClick} on:keydown={handleDocumentKeydown} />

<div class="asa-panel" role="dialog" aria-label={t.panelAria} aria-modal="false">

  <!-- ── Header ── -->
  <div class="asa-ph">
    <div class="asa-ph__left">
      <div class="asa-dropdown" bind:this={dropdownEl}>
        <button
          type="button"
          class="asa-ph__menu"
          bind:this={dropdownTriggerEl}
          on:click={() => (dropdownOpen ? (dropdownOpen = false) : openDropdownAndFocus())}
          on:keydown={handleDropdownTriggerKeydown}
          aria-label={t.moreOptionsAria}
          aria-haspopup="menu"
          aria-expanded={dropdownOpen}
        >
          <div class="asa-ph__dot"></div>
          <div class="asa-ph__dot"></div>
          <div class="asa-ph__dot"></div>
        </button>
        {#if dropdownOpen}
          <div class="asa-dropdown__menu" role="menu" bind:this={dropdownMenuEl}>
            <button
              class="asa-dropdown__item"
              role="menuitem"
              on:click={clearConversation}
              on:keydown={handleDropdownItemKeydown}
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
              {t.clearConv}
            </button>
          </div>
        {/if}
      </div>

      <div class="asa-ph__icon">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="2.2" fill="currentColor"/>
          <line x1="12" y1="3" x2="12" y2="7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <line x1="12" y1="16.5" x2="12" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <line x1="16.5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <line x1="3" y1="12" x2="7.5" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <line x1="15.3" y1="8.7" x2="17.7" y2="6.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="8.7" y1="15.3" x2="6.3" y2="17.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="8.7" y1="8.7" x2="7" y2="7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
          <line x1="15.3" y1="15.3" x2="17" y2="17" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
      </div>

      {#if pageContextLabel}
        <div class="asa-ph__context">{pageContextLabel}</div>
      {/if}
    </div>

    <button class="asa-ph__close" on:click={() => aiAssistantStore.close()} aria-label={t.closeAria}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <!-- ── Body ── -->
  <div class="asa-pb">

    {#if state === 'loading'}
      <div class="asa-loading-body">
        <div class="asa-loading-icon">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="2.2" fill="currentColor"/>
            <line x1="12" y1="3" x2="12" y2="7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="12" y1="16.5" x2="12" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="16.5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="3" y1="12" x2="7.5" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          <div class="asa-loading-ring"></div>
        </div>
        <div class="asa-loading-text">{t.loading}</div>
      </div>

    {:else if state === 'empty'}

      {#if hasOverdue}
        <div class="asa-overdue-banner">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="12" y1="8" x2="12" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <circle cx="12" cy="16.5" r="1" fill="currentColor"/>
          </svg>
          <span class="asa-overdue-text">
            {t.overdueYouHave} {overdueItems.length} {overdueItems.length === 1 ? t.overdueItemOne : t.overdueItemOther}.
            <button type="button" class="asa-overdue-link" on:click={viewOverdue}>{t.overdueView}</button>
          </span>
        </div>
      {/if}

      <div class="asa-empty-body">
        <div class="asa-empty-icon">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="2.2" fill="currentColor"/>
            <line x1="12" y1="3" x2="12" y2="7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="12" y1="16.5" x2="12" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="16.5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="3" y1="12" x2="7.5" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="15.3" y1="8.7" x2="17.7" y2="6.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="8.7" y1="15.3" x2="6.3" y2="17.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="8.7" y1="8.7" x2="7" y2="7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <line x1="15.3" y1="15.3" x2="17" y2="17" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="asa-empty-name">{t.title}</div>
        <div class="asa-empty-sub">{t.subtitle}</div>
        <div class="asa-prompts-heading">{t.promptsHeading}</div>
        <div class="asa-prompts">
          {#each filteredByCapability as prompt}
            <button class="asa-prompt-btn" on:click={() => sendSuggested(prompt.text)}>
              {prompt.text}
            </button>
          {/each}
        </div>
      </div>

    {:else}
      <div class="asa-messages" bind:this={messagesEl}>
        {#if hasOverdue}
          <div class="asa-overdue-banner asa-overdue-banner--chat">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
              <line x1="12" y1="8" x2="12" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <circle cx="12" cy="16.5" r="1" fill="currentColor"/>
            </svg>
            <span class="asa-overdue-text">
              {t.overdueYouHave} {overdueItems.length} {overdueItems.length === 1 ? t.overdueItemOne : t.overdueItemOther}.
              <button type="button" class="asa-overdue-link" on:click={viewOverdue}>{t.overdueView}</button>
            </span>
          </div>
        {/if}

        {#each messages as msg}
          {#if msg.role === 'user'}
            <div class="asa-msg asa-msg--user">{msg.content}</div>
          {:else if msg.content === '' && msg.streaming}
            <div class="asa-msg asa-msg--thinking">
              <div class="asa-thinking-row">
                <span class="asa-thinking-label">{t.thinking}</span>
                <span class="asa-thinking-dots">
                  <span></span><span></span><span></span>
                </span>
              </div>
            </div>
          {:else}
            {@const parts = parseMessage(msg.content)}
            {@const hasCards = parts.some(p => p.kind === 'card')}
            {#if hasCards}
              <!-- Message with cards: no outer bubble, each text part gets its own bubble -->
              <div class="asa-msg-group">
                {#each parts as part}
                  {#if part.kind === 'text'}
                    <div class="asa-msg asa-msg--assistant">
                      {@html part.html}
                    </div>
                  {:else if part.card}
                    <button class="asa-card" on:click={() => part.card && goToCard(part.card)}>
                      <div class="asa-card__title">{part.card.title}</div>
                      {#if part.card.dueIso || part.card.statusKey}
                        <div class="asa-card__meta">
                          {#if part.card.dueIso}{t.cardDueLabel}: {datetimeHelper.getReadableFullDate(part.card.dueIso)}{/if}{#if part.card.dueIso && part.card.statusKey}{' — '}{/if}{#if part.card.statusKey}<span class="asa-card__status">{t['cardStatus_' + part.card.statusKey] || part.card.statusKey}</span>{/if}
                        </div>
                      {/if}
                      {#if part.card.description}
                        <div class="asa-card__desc">{part.card.description}</div>
                      {/if}
                      <div class="asa-card__action">{cardButtonLabel(part.card.type, $muiStore)}</div>
                    </button>
                  {/if}
                {/each}
              </div>
            {:else}
              <!-- Plain text message: single bubble -->
              <div class="asa-msg asa-msg--assistant">
                {@html parts[0]?.html ?? ''}
              </div>
            {/if}
          {/if}
        {/each}

        {#if inlineError}
          <div class="asa-error-banner" role="alert">
            <ki-icon name="alert" />
            <span class="asa-error-banner-text">
              {inlineErrorText}
              <button type="button" class="asa-error-banner-link" on:click={retryLastMessage}>{t.retry}</button>
            </span>
          </div>
        {/if}

        {#if state === 'chat' && !inlineError && chatPrompts.length > 0}
          <div class="asa-chat-prompts">
            {#each chatPrompts.slice(0, 2) as prompt}
              <button class="asa-chat-prompt-btn" on:click={() => sendSuggested(prompt.text)}>
                {prompt.text}
              </button>
            {/each}
          </div>
        {/if}
      </div>
    {/if}
  </div>

  <!-- ── Footer ── -->
  <div class="asa-pf">
    <div class="asa-pf__input-row">
      <div class="asa-pf__input-wrap" class:asa-pf__input-wrap--disabled={state === 'thinking'}>
        <textarea
          class="asa-pf__input"
          aria-label={t.inputAria}
          placeholder={t.inputPlaceholder}
          rows="1"
          bind:value={inputValue}
          on:keydown={handleKeydown}
          disabled={state === 'thinking'}
        ></textarea>
      </div>
      <button
        class="asa-pf__send"
        on:click={() => sendMessage()}
        disabled={!inputValue.trim() || state === 'thinking'}
        aria-label={t.sendAria}
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="22" y1="2" x2="11" y2="13"/>
          <polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
      </button>
    </div>
    <div class="asa-pf__note">{t.footerNote}</div>
  </div>
</div>

<style>
  .asa-panel {
    position: fixed;
    bottom: 92px;
    inset-inline-end: 24px;
    width: 360px;
    max-height: calc(100vh - 130px);
    background: #fff;
    border-radius: calc(20px * var(--ki-border-radius-scale, 1));
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    display: flex;
    flex-direction: column;
    z-index: 999;
    font-family: inherit;
    overflow: hidden;
    animation: asa-panel-in 0.25s ease-out;
    transform-origin: bottom right;
  }

  .asa-ph {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px 10px;
    border-bottom: 1px solid #EAEAEA;
    flex-shrink: 0;
  }
  .asa-ph__left { display: flex; align-items: center; gap: 8px; }

  .asa-ph__menu {
    display: flex; gap: 3px; padding: 5px 6px; border-radius: calc(7px * var(--ki-border-radius-scale, 1));
    cursor: pointer; border: none; background: transparent; align-items: center;
  }
  .asa-ph__menu:hover,
  .asa-ph__menu:focus-visible { background: #EAEAEA; }
  .asa-ph__dot { width: 4px; height: 4px; border-radius: 50%; background: #1f2023; }

  .asa-ph__icon {
    width: 28px; height: 28px; border-radius: calc(8px * var(--ki-border-radius-scale, 1));
    background: var(--kc-color-primary, #ff881a);
    /* Inherit portal's button-text color so the symbol stays legible on any
       primary shade (white on KC orange / blue, dark on light-cream brands). */
    color: var(--kc-button-primary-text-color, #fff);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .asa-ph__icon svg { width: 16px; height: 16px; }

  .asa-ph__context {
    background: var(--kc-color-primary-100, #FFF3E8); border-radius: calc(99px * var(--ki-border-radius-scale, 1)); padding: 3px 10px;
    font-size: 10px; color: #1F2023; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  .asa-ph__close {
    width: 32px; height: 32px; border-radius: calc(7px * var(--ki-border-radius-scale, 1)); border: none;
    background: transparent; cursor: pointer; color: #1f2023;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .asa-ph__close:hover,
  .asa-ph__close:focus-visible { background: #EAEAEA; }
  .asa-ph__close svg { width: 20px; height: 20px; }

  .asa-dropdown { position: relative; }
  .asa-dropdown__menu {
    position: absolute; top: 44px; inset-inline-start: 0; background: #fff;
    border: 1px solid #e5e7eb; border-radius: calc(10px * var(--ki-border-radius-scale, 1));
    box-shadow: 0 4px 16px rgba(0,0,0,0.10); overflow: hidden; z-index: 10; min-width: 160px;
  }
  .asa-dropdown__item {
    padding: 9px 14px; font-size: 13px; color: #374151; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    border: none; background: transparent; width: 100%; text-align: left; font-family: inherit;
  }
  .asa-dropdown__item svg { width: 14px; height: 14px; color: #1f2023; flex-shrink: 0; }
  .asa-dropdown__item:hover,
  .asa-dropdown__item:focus-visible { background: #f8f9fa; }
  /* .asa-dropdown__menu has overflow:hidden (for rounded corners), which
     clips the browser's native focus ring. Force an inset outline so the
     focus cue stays visible for keyboard users. */
  .asa-dropdown__item:focus-visible { outline: 2px solid var(--kc-color-primary, #1a73e8); outline-offset: -2px; }

  .asa-pb { flex: 1; overflow-y: auto; min-height: 0; overscroll-behavior: contain; }
  .asa-pb::-webkit-scrollbar { width: 3px; }
  .asa-pb::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 3px; }

  .asa-loading-body { display: flex; flex-direction: column; align-items: center; padding: 36px 24px; gap: 14px; }
  .asa-loading-icon {
    width: 60px; height: 60px; border-radius: calc(18px * var(--ki-border-radius-scale, 1)); background: var(--kc-color-primary-100, #FFF3E8);
    display: flex; align-items: center; justify-content: center;
    color: var(--kc-color-primary, #ff881a); position: relative;
  }
  .asa-loading-icon svg { width: 32px; height: 32px; opacity: 0.4; }
  .asa-loading-ring {
    position: absolute; inset: -4px; border-radius: calc(22px * var(--ki-border-radius-scale, 1));
    border: 2.5px solid transparent;
    border-top-color: var(--kc-color-primary, #ff881a);
    border-right-color: var(--kc-color-primary-200, rgba(255,136,26,0.3));
    animation: asa-spin 1s linear infinite;
  }
  .asa-loading-text { font-size: 13px; color: #9ca3af; }

  .asa-overdue-banner {
    margin: 10px 14px 0; display: flex; align-items: flex-start; gap: 8px; flex-shrink: 0;
  }
  .asa-overdue-banner--chat { margin: 0 0 4px; }
  .asa-overdue-banner svg { color: var(--kc-color-primary, #ff881a); flex-shrink: 0; margin-top: 2px; width: 14px; height: 14px; }
  .asa-overdue-text { font-size: 14px; color: var(--kc-color-primary, #ff881a); line-height: 20px; }
  .asa-overdue-link { cursor: pointer; text-decoration: underline; background: none; border: 0; padding: 0; font: inherit; color: inherit; }
  .asa-overdue-link:focus-visible { outline: 2px solid var(--kc-color-primary, #ff881a); outline-offset: 2px; border-radius: 2px; }

  .asa-empty-body { display: flex; flex-direction: column; align-items: center; padding: 28px 20px 20px; }
  .asa-empty-icon {
    width: 64px; height: 64px; border-radius: calc(18px * var(--ki-border-radius-scale, 1)); background: var(--kc-color-primary-100, #FFF3E8);
    display: flex; align-items: center; justify-content: center;
    color: var(--kc-color-primary, #ff881a); margin-bottom: 16px;
  }
  .asa-empty-icon svg { width: 32px; height: 32px; }
  .asa-empty-name { font-size: 18px; font-weight: 700; color: #1f2023; margin-bottom: 6px; line-height: 28px; }
  .asa-empty-sub { font-size: 14px; color: #9A9A9A; text-align: center; margin-bottom: 24px; line-height: 20px; }
  .asa-prompts-heading {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: #9A9A9A; align-self: flex-start; margin-bottom: 8px; width: 100%;
  }
  .asa-prompts { display: flex; flex-direction: column; gap: 8px; width: 100%; }
  .asa-prompt-btn {
    display: flex; align-items: center; background: var(--kc-color-primary-100, #FFF3E8); border: none;
    border-radius: calc(16px * var(--ki-border-radius-scale, 1)); padding: 12px 16px; font-size: 14px; color: #1f2023;
    text-align: left; cursor: pointer; transition: background 0.15s; font-family: inherit; line-height: 20px;
  }
  .asa-prompt-btn:hover,
  .asa-prompt-btn:focus-visible { background: var(--kc-color-primary-200, #FFDBBA); }

  .asa-messages { padding: 14px 12px; display: flex; flex-direction: column; gap: 10px; }
  .asa-msg {
    max-width: 88%; padding: 10px 14px; font-size: 14px; line-height: 20px;
    border-radius: calc(16px * var(--ki-border-radius-scale, 1)); word-break: break-word;
  }
  .asa-msg--user {
    align-self: flex-end; background: var(--kc-color-primary-100, #FFF3E8); color: #1f2023; border-bottom-right-radius: 4px;
  }
  .asa-msg--assistant {
    align-self: flex-start; background: transparent; color: #1f2023;
  }
  .asa-msg--assistant :global(.asa-md-list) { margin: 6px 0 2px; padding-left: 18px; }
  .asa-msg--assistant :global(li) { margin-bottom: 4px; }
  .asa-msg--assistant :global(strong) { font-weight: 600; }

  /* Group wrapper for messages with cards */
  .asa-msg-group {
    display: flex; flex-direction: column; gap: 8px; max-width: 88%; align-self: flex-start;
  }
  .asa-msg-group .asa-msg--assistant {
    max-width: 100%; padding: 0 14px;
  }
  /* Symmetry: tighten the gap AFTER cards so it matches the gap BEFORE cards (BUG-9) */
  .asa-msg-group .asa-card + .asa-msg--assistant {
    margin-top: -2px;
  }

  /* ── Course / LP card ── */
  .asa-card {
    display: block;
    width: 100%;
    background: #fff;
    border: 1.5px solid #EAEAEA;
    border-radius: calc(12px * var(--ki-border-radius-scale, 1));
    padding: 12px 14px;
    cursor: pointer;
    /* `start` = left in LTR, right in RTL — keeps card title/desc/CTA aligned
       with the reading direction instead of locking to left (QA-16). */
    text-align: start;
    font-family: inherit;
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .asa-card:hover,
  .asa-card:focus-visible {
    border-color: var(--kc-color-primary, #ff881a);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    outline: none;
  }
  .asa-card__title {
    font-size: 14px; font-weight: 600; color: #1f2023; line-height: 20px; margin-bottom: 4px;
  }
  /* Meta row holds "Due date: …  — <status>" or a lone status chip (QA-NEW-14.c/d/e). */
  .asa-card__meta {
    font-size: 13px; color: #6b7280; line-height: 18px; margin-bottom: 6px;
  }
  /* Some MUI status values are stored lowercase in the DB ("not started",
     "viewed") while others are title-case ("In Progress", "Passed"). Force
     sentence-case via first-letter transform so the CARD meta row reads
     consistently without mutating the shared mylearning-* source strings
     (those also feed the MyLearning page). No-op for non-cased scripts
     (AR/HI/UR/BN). */
  .asa-card__status { display: inline-block; }
  .asa-card__status::first-letter { text-transform: uppercase; }
  .asa-card__desc {
    font-size: 13px; color: #6b7280; line-height: 18px; margin-bottom: 10px;
  }
  .asa-card__action {
    font-size: 13px; font-weight: 600; color: var(--kc-color-primary, #ff881a);
  }

  .asa-msg--thinking {
    align-self: flex-start; background: transparent; color: #1f2023; padding: 10px 14px;
  }
  .asa-thinking-row { display: flex; align-items: center; gap: 6px; padding: 4px 0; }
  .asa-thinking-label { font-size: 14px; color: #9A9A9A; line-height: 20px; }
  .asa-thinking-dots { display: flex; gap: 4px; align-items: center; }
  .asa-thinking-dots span {
    width: 5px; height: 5px; border-radius: 50%;
    background: var(--kc-color-primary, #ff881a); animation: asa-bounce 1.2s infinite;
  }
  .asa-thinking-dots span:nth-child(2) { animation-delay: 0.2s; }
  .asa-thinking-dots span:nth-child(3) { animation-delay: 0.4s; }

  .asa-error-banner { margin: 0; display: flex; align-items: flex-start; gap: 6px; flex-wrap: nowrap; }
  .asa-error-banner ki-icon { color: #F0213E; flex-shrink: 0; font-size: 14px; width: 14px; height: 14px; margin-top: 3px; }
  .asa-error-banner-text { font-size: 13px; color: #F0213E; line-height: 20px; }
  .asa-error-banner-link { cursor: pointer; text-decoration: underline; background: none; border: 0; padding: 0; font: inherit; color: inherit; white-space: nowrap; margin-left: 2px; }
  .asa-error-banner-link:focus-visible { outline: 2px solid #F0213E; outline-offset: 2px; border-radius: 2px; }

  .asa-chat-prompts {
    display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;
    padding-top: 8px; border-top: 1px solid #f0f0f0;
  }
  .asa-chat-prompt-btn {
    background: var(--kc-color-primary-100, #FFF3E8); border: none; border-radius: calc(20px * var(--ki-border-radius-scale, 1));
    padding: 6px 12px; font-size: 12px; color: #1f2023;
    cursor: pointer; transition: background 0.15s; font-family: inherit; line-height: 16px;
  }
  .asa-chat-prompt-btn:hover,
  .asa-chat-prompt-btn:focus-visible { background: var(--kc-color-primary-200, #FFDBBA); }

  .asa-pf { flex-shrink: 0; }
  .asa-pf__input-row { display: flex; align-items: flex-end; gap: 7px; padding: 10px 20px 8px; }
  .asa-pf__input-wrap {
    flex: 1; background: #fff; border: 1.5px solid #EAEAEA;
    border-radius: calc(16px * var(--ki-border-radius-scale, 1)); padding: 8px 12px; transition: border-color 0.15s;
  }
  .asa-pf__input-wrap:focus-within { border-color: var(--kc-color-primary, #ff881a); }
  .asa-pf__input-wrap--disabled { opacity: 0.6; pointer-events: none; }
  .asa-pf__input {
    width: 100%; border: none; background: transparent;
    font-size: 14px; font-family: inherit; color: #1f2023; resize: none; line-height: 20px;
  }
  .asa-pf__input::placeholder { color: #9A9A9A; }

  .asa-pf__send {
    width: 40px; height: 40px; border-radius: calc(14px * var(--ki-border-radius-scale, 1)); border: none;
    background: var(--kc-color-primary, #ff881a); color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: background 0.15s;
  }
  .asa-pf__send:hover,
  .asa-pf__send:focus-visible { filter: brightness(0.88); }
  .asa-pf__send:disabled { background: var(--kc-color-primary-100, #FFF3E8); color: var(--kc-color-primary, #ff881a); opacity: 0.4; cursor: default; }
  .asa-pf__send svg { width: 16px; height: 16px; }
  /* Paper-plane SVG geometry is fixed LTR; flip on RTL portals so the arrow
     still points "outward" in the direction of reading (QA-11). */
  :global([dir="rtl"]) .asa-pf__send svg { transform: scaleX(-1); }
  .asa-pf__note { text-align: center; font-size: 10px; color: #9A9A9A; padding: 0 20px 10px; line-height: 1.4; }

  @keyframes asa-bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-4px); }
  }
  @keyframes asa-spin { to { transform: rotate(360deg); } }
  @keyframes asa-panel-in {
    from { opacity: 0; transform: scale(0.92) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
  }

  @media (max-width: 480px) {
    .asa-panel {
      left: 50%;
      inset-inline-end: auto;
      top: 50%;
      bottom: auto;
      width: calc(100vw - 24px);
      max-width: 360px;
      max-height: calc(100dvh - 48px);
      transform: translate(-50%, -50%);
      transform-origin: center;
      animation: asa-panel-in-mobile 0.25s ease-out;
    }
  }
  @keyframes asa-panel-in-mobile {
    from { opacity: 0; transform: translate(-50%, -50%) scale(0.92); }
    to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
  }
</style>
