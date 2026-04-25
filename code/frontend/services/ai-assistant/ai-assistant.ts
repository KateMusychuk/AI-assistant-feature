import { get } from 'svelte/store';
import { userStore, muiStore } from '$lib/stores';
import { profileSettingsStore } from '$lib/stores/profile-settings';
import { aiAssistantStore } from '$lib/stores/ai-assistant';
import type { IAiAssistantMessage, IAiAssistantOverdueItem, IAiAssistantPageContext } from '$lib/stores/ai-assistant';
import userApi from '$lib/api/user/user';
import multiFactorAuthenticationUsersApi from '$lib/api/mfa/user/user';

interface IChatRequest {
  message: string;
  history: IAiAssistantMessage[];
  pageContext: IAiAssistantPageContext;
  lang: string;
}

const API_BASE    = import.meta.env.VITE_API_URL;
const API_VERSION = import.meta.env.VITE_API_VERSION;
const CHAT_URL    = `${API_BASE}/${API_VERSION}/ai_assistant/chat`;
const OVERDUE_URL = `${API_BASE}/${API_VERSION}/ai_assistant/overdue`;

const MIN_THINKING_MS = 800;

const sleep = (ms: number) => new Promise<void>((resolve) => setTimeout(resolve, ms));

/**
 * Classify a raw provider / transport error into an MUI translation key + any
 * variables to interpolate (e.g. `{wait}`). Keeping classification separate
 * from rendering lets the banner display the message in the student's UI
 * language. The raw text still lands in console.error for diagnostics.
 *
 * English-fallback copy is kept in sync with the `ai_assistant_error_*` keys
 * in the `ai_assistant` MUI group (migration PORTAL-2441).
 */
interface IErrorClassification {
  key: string;
  fallback: string;
  vars?: Record<string, string>;
}

const classifyError = (raw: string | null): IErrorClassification => {
  if (!raw) {
    return { key: 'ai_assistant_error_connection_lost', fallback: 'Connection was lost.' };
  }
  const text = raw.toLowerCase();

  // Groq / OpenAI-style rate limit — the backend forwards provider.error.message
  // which typically contains "rate limit reached ... please try again in Xm Ys".
  if (text.includes('rate limit') || text.includes('http 429') || text.includes('tokens per day')) {
    const waitMatch = raw.match(/try again in\s+([0-9]+m[0-9.]*s?|[0-9]+(?:\.[0-9]+)?s)/i);
    const wait = waitMatch ? waitMatch[1].replace(/\.[0-9]+s/, 's') : null;
    if (wait) {
      return {
        key: 'ai_assistant_error_rate_limited_wait',
        fallback: `AI is temporarily rate-limited. Available in ${wait}.`,
        vars: { wait }
      };
    }
    return { key: 'ai_assistant_error_rate_limited', fallback: 'AI is temporarily rate-limited.' };
  }
  if (text.includes('context length') || text.includes('too long') || text.includes('maximum context')) {
    return { key: 'ai_assistant_error_too_long', fallback: 'Your message is too long for the AI. Shorten it.' };
  }
  if (text.includes('timeout') || text.includes('timed out')) {
    return { key: 'ai_assistant_error_timeout', fallback: 'The AI took too long to respond.' };
  }
  if (text.match(/\bhttp (5\d{2})\b/) || text.includes('service unavailable') || text.includes('bad gateway')) {
    return { key: 'ai_assistant_error_unavailable', fallback: 'AI service is temporarily unavailable.' };
  }
  if (text.includes('networkerror') || text.includes('failed to fetch')) {
    return { key: 'ai_assistant_error_network', fallback: 'Network connection was lost. Check your connection.' };
  }
  return { key: 'ai_assistant_error_connection_lost', fallback: 'Connection was lost.' };
};

/**
 * Resolve a classified error through MUI, interpolating any `{var}` tokens.
 * Falls back to English when MUI hasn't loaded yet — better than an empty
 * banner.
 */
const renderError = (raw: string | null): string => {
  const { key, fallback, vars } = classifyError(raw);
  const mui = get(muiStore) as Record<string, string>;
  let message = mui[key] || fallback;
  if (vars) {
    for (const [k, v] of Object.entries(vars)) {
      message = message.replace(`{${k}}`, v);
    }
  }
  return message;
};

/**
 * Typewriter effect: reveals text word-by-word to simulate streaming
 * even when the backend sends the full response at once (PHP-FPM buffering).
 */
const typewriterReveal = async (fullText: string): Promise<void> => {
  const words = fullText.split(/(\s+)/); // keep whitespace
  let revealed = '';
  for (let i = 0; i < words.length; i++) {
    revealed += words[i];
    aiAssistantStore.replaceLastMessageContent(revealed);
    // Faster for short texts, slower for long ones
    if (i % 3 === 0) await sleep(20);
  }
};

/**
 * Loads the two signals the settings-tab suggested prompts depend on:
 *   - portal_user_profile form fields (what profile inputs the admin enabled)
 *   - MFA methods incl. PIN (whether PIN security is available)
 * Result is cached on the AI store; on failure we clear to null so the UI
 * falls back to showing all suggestions rather than an empty panel.
 */
export const fetchSettingsCapabilities = async (): Promise<void> => {
  const token = get(userStore)?.token;
  const accountId = get(userStore)?.user?.account_id;
  if (!token || !accountId) return;

  try {
    // Reuse the store populated by the Settings page when the student is
    // already there — avoids a duplicate round-trip.
    let fields: string[] = [];
    const existing = get(profileSettingsStore)?.fields ?? [];
    if (existing.length > 0) {
      fields = existing.map((f) => f.key).filter(Boolean);
    } else {
      const portalFormFields = await userApi.getPortalFormFields({
        accountId: String(accountId),
        lang: 'en'
      });
      // Trust the admin's form configuration — any field the account returns
      // is potentially visible to the student. A stricter `show_in_portal`
      // filter here was hiding prompts for fields that were actually rendered.
      fields = Object.values(portalFormFields)
        .map((f) => f.name)
        .filter(Boolean);
    }

    let hasPin = false;
    try {
      const mfa = await multiFactorAuthenticationUsersApi.getMethodsListForUser();
      // Presence of the PIN method in the list is enough — account-level
      // `is_active` was too strict: PIN stays usable even when broader MFA is
      // not marked active for the account.
      hasPin = (mfa?.methods ?? []).some((m) => m.method === 'pin');
    } catch {
      // MFA endpoint is optional — if it fails, assume no PIN rather than hanging the panel.
    }

    aiAssistantStore.setSettingsCapabilities({ fields, hasPin });
  } catch {
    aiAssistantStore.setSettingsCapabilities(null);
  }
};

export const fetchOverdue = async (lang?: string): Promise<void> => {
  const token = get(userStore)?.token;
  if (!token) return;

  // Forward the student's UI language so the backend returns localized titles
  // via the same lang-aware path the /chat endpoint already uses. Falls back
  // to the backend default when the caller hasn't resolved the lang yet.
  const url = lang ? `${OVERDUE_URL}?lang=${encodeURIComponent(lang)}` : OVERDUE_URL;

  try {
    const response = await fetch(url, {
      headers: { 'Accept': `Token/${token}` }
    });

    if (!response.ok) return;

    const data = await response.json();
    const items: IAiAssistantOverdueItem[] = (data?.response?.items ?? []).map(
      (item: IAiAssistantOverdueItem) => item
    );
    aiAssistantStore.setOverdueItems(items);
  } catch {
    // non-critical
  }
};

export const chat = async (request: IChatRequest): Promise<void> => {
  const token = get(userStore)?.token;
  if (!token) return;

  const startedAt = Date.now();

  aiAssistantStore.setState('thinking');
  aiAssistantStore.setInlineError(false);
  aiAssistantStore.pushMessage({ role: 'assistant', content: '', streaming: true });

  let collectedText = '';
  let isError = false;
  let rawErrorText: string | null = null;

  try {
    const response = await fetch(CHAT_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': `Token/${token}`
      },
      body: JSON.stringify({
        message:      request.message,
        history:      request.history.map(({ role, content }) => ({ role, content })),
        page_context: request.pageContext.type,
        lang:         request.lang
      })
    });

    if (!response.ok || !response.body) {
      throw new Error(`HTTP ${response.status}`);
    }

    const reader  = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer    = '';

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;

      buffer += decoder.decode(value, { stream: true });
      const lines = buffer.split('\n');
      buffer = lines.pop() ?? '';

      for (const line of lines) {
        if (!line.startsWith('data: ')) continue;
        try {
          const chunk = JSON.parse(line.slice(6));

          if (chunk.delta !== undefined) {
            collectedText += chunk.delta;
          } else if (chunk.error !== undefined) {
            isError = true;
            rawErrorText = typeof chunk.error === 'string' ? chunk.error : JSON.stringify(chunk.error);
            // Always log — helps diagnose intermittent backend issues (rate limits,
            // upstream timeouts). The banner then surfaces a friendly version.
            console.error('[AI Assistant] SSE error from server:', chunk.error);
          }
        } catch {
          // malformed SSE line
        }
      }
    }
  } catch (err) {
    isError = true;
    rawErrorText = err instanceof Error ? err.message : String(err);
    console.error('[AI Assistant] chat request failed:', err);
  }

  if (isError) {
    const friendly = renderError(rawErrorText);

    // Preserve whatever text arrived before the failure so the student doesn't
    // lose partial content (e.g. rate limit triggered mid-response).
    if (collectedText.trim().length > 0) {
      const elapsed = Date.now() - startedAt;
      const remaining = MIN_THINKING_MS - elapsed;
      if (remaining > 0) await sleep(remaining);
      await typewriterReveal(collectedText);
      aiAssistantStore.finalizeLastMessage();
    } else {
      aiAssistantStore.removeLastMessage();
    }

    aiAssistantStore.setInlineError(true, friendly);
    aiAssistantStore.setState('chat');
    return;
  }

  // Typewriter reveal — shows text word-by-word for smooth UX.
  // PHP-FPM buffers the SSE stream, so real-time streaming isn't possible
  // without infrastructure changes. Typewriter gives a similar feel.
  if (collectedText) {
    const elapsed = Date.now() - startedAt;
    const remaining = MIN_THINKING_MS - elapsed;
    if (remaining > 0) await sleep(remaining);
    await typewriterReveal(collectedText);
  }

  aiAssistantStore.finalizeLastMessage();
  aiAssistantStore.setState('chat');
};
