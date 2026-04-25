export type AiAssistantPanelState = 'loading' | 'empty' | 'chat' | 'thinking' | 'error';

export interface IAiAssistantMessage {
  role: 'user' | 'assistant';
  content: string;
  streaming?: boolean;
}

export interface IAiAssistantOverdueItem {
  id: string;
  title: string;
  type: string;
  is_mandatory: boolean;
  pass_due_date: string | null;
  progress: number;
}

export type AiAssistantPageContextType =
  | 'assignments' | 'library' | 'my-learning' | 'my-progress'
  | 'search' | 'saved' | 'webinar' | 'home' | 'calendar'
  | 'settings' | 'settings-personal' | 'settings-privacy' | 'settings-license'
  | 'faq'
  | 'general';

/**
 * Feature flags the settings-tab suggests depend on. Populated on demand when
 * the student lands on a `/settings/*` page so that irrelevant suggests
 * (e.g. "How do I change my email language?" when the account admin removed
 * the `lang` form field) are hidden.
 */
export interface ISettingsCapabilities {
  /** Field keys present in the portal user profile form for this account. */
  fields: string[];
  /** True when the account exposes PIN as an MFA method. */
  hasPin: boolean;
}

/**
 * A suggested prompt. `textKey` is a MUI key resolved via `$muiStore[textKey]`
 * at render time. When `requires` is present the prompt is shown only when the
 * listed settings capability is satisfied (field key in `fields`, or the
 * special literal `'pin'` gated on `hasPin`).
 */
export interface ISuggestedPrompt {
  textKey: string;
  requires?: string;
}

export interface IAiAssistantPageContext {
  type: AiAssistantPageContextType;
  url: string;
}

export interface IAiAssistantStore {
  isOpen: boolean;
  panelState: AiAssistantPanelState;
  messages: IAiAssistantMessage[];
  overdueItems: IAiAssistantOverdueItem[];
  pageContext: IAiAssistantPageContext;
  inlineError: boolean;
  inlineErrorMessage: string | null;
  settingsCapabilities: ISettingsCapabilities | null;
}

export type TAiAssistantStore = {
  subscribe: (run: (value: IAiAssistantStore) => void) => () => void;
  open: () => void;
  close: () => void;
  pushMessage: (message: IAiAssistantMessage) => void;
  appendToLastMessage: (text: string) => void;
  replaceLastMessageContent: (text: string) => void;
  finalizeLastMessage: () => void;
  removeLastMessage: () => void;
  clearMessages: () => void;
  setState: (state: AiAssistantPanelState) => void;
  setInlineError: (value: boolean, message?: string | null) => void;
  setSettingsCapabilities: (capabilities: ISettingsCapabilities | null) => void;
  setOverdueItems: (items: IAiAssistantOverdueItem[]) => void;
  setPageContext: (ctx: IAiAssistantPageContext) => void;
};
