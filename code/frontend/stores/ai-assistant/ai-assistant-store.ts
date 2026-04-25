import { writable } from 'svelte/store';
import { initialState } from './constants';
import type { IAiAssistantMessage, IAiAssistantOverdueItem, IAiAssistantPageContext, AiAssistantPanelState, ISettingsCapabilities } from './types';

const createAiAssistantStore = () => {
  const { subscribe, update } = writable({ ...initialState });

  return {
    subscribe,

    open: () => update((s) => ({ ...s, isOpen: true })),

    close: () => update((s) => ({ ...s, isOpen: false })),

    pushMessage: (message: IAiAssistantMessage) =>
      update((s) => ({
        ...s,
        panelState: 'chat',
        inlineError: false,
        inlineErrorMessage: null,
        messages: [...s.messages, message]
      })),

    appendToLastMessage: (text: string) =>
      update((s) => {
        const messages = [...s.messages];
        const last = messages[messages.length - 1];
        if (last && last.role === 'assistant') {
          messages[messages.length - 1] = {
            ...last,
            content: last.content + text,
            streaming: true
          };
        }
        return { ...s, messages };
      }),

    replaceLastMessageContent: (text: string) =>
      update((s) => {
        const messages = [...s.messages];
        const last = messages[messages.length - 1];
        if (last && last.role === 'assistant') {
          messages[messages.length - 1] = {
            ...last,
            content: text,
            streaming: true
          };
        }
        return { ...s, messages };
      }),

    finalizeLastMessage: () =>
      update((s) => {
        const messages = [...s.messages];
        const last = messages[messages.length - 1];
        if (last) {
          messages[messages.length - 1] = { ...last, streaming: false };
        }
        return { ...s, messages, panelState: 'chat' };
      }),

    removeLastMessage: () =>
      update((s) => {
        const messages = s.messages.slice(0, -1);
        return { ...s, messages };
      }),

    clearMessages: () =>
      update((s) => ({ ...s, messages: [], panelState: 'empty', inlineError: false, inlineErrorMessage: null })),

    setState: (panelState: AiAssistantPanelState) =>
      update((s) => ({ ...s, panelState })),

    setInlineError: (inlineError: boolean, inlineErrorMessage: string | null = null) =>
      update((s) => ({ ...s, inlineError, inlineErrorMessage: inlineError ? inlineErrorMessage : null })),

    setOverdueItems: (overdueItems: IAiAssistantOverdueItem[]) =>
      update((s) => ({ ...s, overdueItems })),

    setPageContext: (pageContext: IAiAssistantPageContext) =>
      update((s) => ({ ...s, pageContext })),

    setSettingsCapabilities: (settingsCapabilities: ISettingsCapabilities | null) =>
      update((s) => ({ ...s, settingsCapabilities }))
  };
};

export const aiAssistantStore = createAiAssistantStore();
