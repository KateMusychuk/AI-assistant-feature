import type { AiAssistantPageContextType, IAiAssistantStore, ISuggestedPrompt } from './types';

export const initialState: IAiAssistantStore = {
  isOpen: false,
  panelState: 'loading',
  messages: [],
  overdueItems: [],
  pageContext: { type: 'general', url: '' },
  inlineError: false,
  inlineErrorMessage: null,
  settingsCapabilities: null
};

/**
 * Page-context descriptor. `labelKey` resolves to a label via `$muiStore[labelKey]`
 * (group: `ai_assistant`). Fallback copy lives in the component.
 */
export interface IPageTypeDescriptor {
  type: AiAssistantPageContextType;
  labelKey: string;
  pattern?: RegExp;
}

export const DEFAULT_PAGE_TYPE: IPageTypeDescriptor = {
  type: 'general',
  labelKey: 'ai_assistant_context_general'
};

export const PAGE_TYPE_MAP: (IPageTypeDescriptor & { pattern: RegExp })[] = [
  { pattern: /\/assignments/,                      type: 'assignments',       labelKey: 'ai_assistant_context_assignments' },
  { pattern: /\/library/,                          type: 'library',           labelKey: 'ai_assistant_context_library' },
  { pattern: /\/my-learning/,                      type: 'my-learning',       labelKey: 'ai_assistant_context_my_learning' },
  { pattern: /\/my-progress/,                      type: 'my-progress',       labelKey: 'ai_assistant_context_my_progress' },
  { pattern: /\/search/,                           type: 'search',            labelKey: 'ai_assistant_context_search' },
  // Settings sub-tabs — must come before the generic /settings/ catch-all below.
  { pattern: /\/settings\/personal-information/,   type: 'settings-personal', labelKey: 'ai_assistant_context_settings_personal' },
  { pattern: /\/settings\/privacy-and-security/,   type: 'settings-privacy',  labelKey: 'ai_assistant_context_settings_privacy' },
  { pattern: /\/settings\/license-info/,           type: 'settings-license',  labelKey: 'ai_assistant_context_settings_license' },
  { pattern: /\/settings/,                         type: 'settings',          labelKey: 'ai_assistant_context_settings' },
  { pattern: /\/faq/,                              type: 'faq',               labelKey: 'ai_assistant_context_faq' },
  { pattern: /\/webinar/,                          type: 'webinar',           labelKey: 'ai_assistant_context_webinar' },
  { pattern: /\/my-events/,                        type: 'calendar',          labelKey: 'ai_assistant_context_calendar' },
  { pattern: /\/saved/,                            type: 'saved',             labelKey: 'ai_assistant_context_saved' },
  { pattern: /^\/[a-z]{2}\/?$/,                    type: 'home',              labelKey: 'ai_assistant_context_home' }
];

export const SUGGESTED_PROMPTS: Partial<Record<AiAssistantPageContextType, ISuggestedPrompt[]>> = {
  assignments: [
    { textKey: 'ai_assistant_prompt_assignments_1' },
    { textKey: 'ai_assistant_prompt_assignments_2' },
    { textKey: 'ai_assistant_prompt_assignments_3' }
  ],
  library: [
    { textKey: 'ai_assistant_prompt_library_1' },
    { textKey: 'ai_assistant_prompt_library_2' },
    { textKey: 'ai_assistant_prompt_library_3' }
  ],
  'my-learning': [
    { textKey: 'ai_assistant_prompt_my_learning_1' },
    { textKey: 'ai_assistant_prompt_my_learning_2' },
    { textKey: 'ai_assistant_prompt_my_learning_3' }
  ],
  'my-progress': [
    { textKey: 'ai_assistant_prompt_my_progress_1' },
    { textKey: 'ai_assistant_prompt_my_progress_2' },
    { textKey: 'ai_assistant_prompt_my_progress_3' }
  ],
  search: [
    { textKey: 'ai_assistant_prompt_search_1' },
    { textKey: 'ai_assistant_prompt_search_2' },
    { textKey: 'ai_assistant_prompt_search_3' }
  ],
  saved: [
    { textKey: 'ai_assistant_prompt_saved_1' },
    { textKey: 'ai_assistant_prompt_saved_2' },
    { textKey: 'ai_assistant_prompt_saved_3' }
  ],
  webinar: [
    { textKey: 'ai_assistant_prompt_webinar_1' },
    { textKey: 'ai_assistant_prompt_webinar_2' },
    { textKey: 'ai_assistant_prompt_webinar_3' }
  ],
  calendar: [
    { textKey: 'ai_assistant_prompt_calendar_1' },
    { textKey: 'ai_assistant_prompt_calendar_2' },
    { textKey: 'ai_assistant_prompt_calendar_3' }
  ],
  faq: [
    { textKey: 'ai_assistant_prompt_faq_1' },
    { textKey: 'ai_assistant_prompt_faq_2' },
    { textKey: 'ai_assistant_prompt_faq_3' }
  ],
  home: [
    { textKey: 'ai_assistant_prompt_home_1' },
    { textKey: 'ai_assistant_prompt_home_2' },
    { textKey: 'ai_assistant_prompt_home_3' }
  ],
  general: [
    { textKey: 'ai_assistant_prompt_general_1' },
    { textKey: 'ai_assistant_prompt_general_2' },
    { textKey: 'ai_assistant_prompt_general_3' }
  ],
  // Settings sub-tabs. Prompts with `requires` are filtered by the
  // portal_user_profile form fields + mfa/methods returned for this account —
  // so suggestions never point at a field the admin has disabled.
  'settings-personal': [
    { textKey: 'ai_assistant_prompt_settings_personal_1', requires: 'time_zone' },
    { textKey: 'ai_assistant_prompt_settings_personal_2', requires: 'lang' },
    { textKey: 'ai_assistant_prompt_settings_personal_3' }
  ],
  'settings-privacy': [
    { textKey: 'ai_assistant_prompt_settings_privacy_1' },
    { textKey: 'ai_assistant_prompt_settings_privacy_2', requires: 'pin' },
    { textKey: 'ai_assistant_prompt_settings_privacy_3' }
  ],
  'settings-license': [
    { textKey: 'ai_assistant_prompt_settings_license_1' },
    { textKey: 'ai_assistant_prompt_settings_license_2' },
    { textKey: 'ai_assistant_prompt_settings_license_3' }
  ],
  // Generic fallback for bare `/settings` (no sub-tab), intentionally minimal.
  settings: [
    { textKey: 'ai_assistant_prompt_settings_1' },
    { textKey: 'ai_assistant_prompt_settings_2', requires: 'time_zone' },
    { textKey: 'ai_assistant_prompt_settings_3' }
  ]
};
