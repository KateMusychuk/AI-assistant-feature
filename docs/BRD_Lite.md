**This is a sanitized version of the original BRD, prepared for public portfolio purposes. Internal references, company-specific processes, and confidential details have been removed.**  
**Overview**  
The AI Student Assistant (ASA) is an AI-powered assistant embedded into the KnowledgeCity Portal. It is available on every page of the portal except the course page and helps learners in three ways:

1. Answers questions about available courses in the catalogue  
2. Recommends next courses based on the learner's profile and progress  
3. Surfaces upcoming and overdue assignments proactively when the learner opens the assistant

This BRD describes the **MVP** scope that will be delivered as a working, demonstrable build within the 2-week qualification project. A full production rollout is out of scope for this document. Post-MVP roadmap candidates are listed below under Next iterations.

## **Current Situation**

Today the Portal does not provide a unified AI assistant that is available across all pages. Students must navigate the portal on their own: figure out which course to start next, keep track of assignment deadlines manually, and search the catalogue without conversational guidance. Administrators have no visibility into the concrete questions students have about the content.

## **Problem Statement**

Students in the Portal experience three related problems:

1. **Navigation friction** — they do not always know what to learn next or how their progress maps to recommended content.  
2. **Deadline awareness** — overdue and upcoming assignments are easy to miss without proactive reminders.  
3. **Lack of conversational guidance**— students have no way to find relevant courses or navigate the portal through dialogue.

## 

## **JTBD**

* **For Students:**  
  * **When I** am on any page of the portal and I need help,      
     **I want** to ask a question and get a useful, grounded answer quickly,      
     **so that** I do not get stuck or lose momentum in my learning.

## **Success criteria**

MVP is considered successful when all of the following are demonstrable in the final demo:

* The assistant is reachable from every page of the portal except the course page via a floating widget  
* The assistant responds to a user question in under 3 seconds (first token)  
* The assistant uses page context (current URL) to tailor its answer  
* The assistant can recommend courses based on the student's profile and skill gaps  
* The assistant can list upcoming/overdue assignments on request  
* All interactions happen against a dedicated backend service (independent of AI Teacher)  
* **Widget chrome (labels, buttons, error messages, suggested prompts) renders in the portal's UI language** — EN / AR / ES / FR / DE / PT / HI / BN / UR. 

# **User stories**

1. User Story      
    **As an authorized user**,     
    **I want** to access an AI assistant from any page of the portal except course page,     
    **so that** I can get help without losing my place or navigating away.

| ID – № | Requirement | Comments |
| :---- | :---- | :---- |
| 01-1 | A floating button is rendered on every route of the portal, except the course page\_, where AI Teacher already serves as the in-context assistant. |   |
| 01-2 | Clicking the button opens a chat panel overlaying the page, without page reload. |   |

2. User Story      
    **As an authorized user**,     
    **I want** the assistant to understand which page I am on when I ask a question,     
    **so that** I get relevant, context-specific answers instead of generic ones.

| ID – № | Requirement | Comments |
| :---- | :---- | :---- |
| 02-1 | The assistant receives the current URL and page type (library, assignments) on every request. Course page is excluded. | supporting 14 context types: assignments, library, my-learning, my-progress, search, saved, webinar, home, calendar, faq, settings-personal, settings-privacy, settings-license, settings, \+ general fallback. |
| 02-2 | When the panel opens, 3 suggested prompts are displayed, chosen based on page type ( e.g. on assignments page: "What are my upcoming deadlines?"; on library: "What should I learn next?") | Suggested prompts are localized into 9 languages (EN/AR/ES/FR/DE/PT/HI/BN/UR) via the ai\_assistant MUI group. |
| 02-3 | First token arrives in under 3 seconds |   |
| 02-4 | Conversation history is retained within the open session. |   |

   

   3\. User Story      
       **As an authorized user**,     
        **I want** to ask the assistant what to learn next,     
       **so that** I can make progress without having to search the catalogue on my    own.

| ID – № | Requirement | Comments |
| :---- | :---- | :---- |
| 03-1 | The user can ask "What should I learn next?" or similar. |   |
| 03-2 | The assistant responds with 1–3 course recommendations, each with a one-sentence reason. |   |
| 03-3 | Recommendations are based on skills covered by the student's completed courses compared against skills of available courses in the catalogue. Courses covering skills not yet acquired by the student are prioritized. |   |
| 03-4 | Each recommendation includes a clickable link that navigates to the course/LP page. | Card CTA label ("Go to course" / "Go to learning path" / "Go to skill path") localized into 9 languages. |

4. User Story      
    **As an authorized user**,     
    **I want** to see my upcoming and overdue assignments when I open the  assistant,     
    **so that** I do not miss deadlines.

| ID – № | Requirement | Comments |
| :---- | :---- | :---- |
| 04-1 | The user can ask "What are my upcoming deadlines?" or similar. |   |
| 04-2 | The assistant returns a list of assignments with due date and status (upcoming / overdue). | Overdue banner text (with singular / plural split) localized into 9 languages. |

‌

# **Scope**

The following items are **in scope** for the MVP:

1. Floating AI widget rendered on all portal routes except the course page  
2. The assistant tailors its responses based on where the student is in the portal except the course page  
3. Course recommendations based on skill-gap analysis using existing course skills data  
4. Deadline listing — the assistant surfaces upcoming and overdue assignments on request  
5. The assistant operates as an independent service, separate from existing portal features  
6. **Widget chrome localization into 9 languages (EN / AR / ES / FR / DE / PT / HI / BN / UR)** — all static UI labels, suggested prompts, card CTAs, error messages, overdue banner with plural forms, aria-labels — pulled from a dedicated MUI group. 

**Out of Scope**

The following items are explicitly **out of scope** for the MVP:

1. Integration with or reuse of the existing AI Teacher service. The assistant does not appear on course pages — AI Teacher remains the sole assistant in that context.  
2. Answers grounded in full course video content or transcripts  
3. Advanced skill-gap modelling based on formal competency frameworks or assessment results  
4. Proactive push notifications via the existing notifications-center  
5. Guided walkthrough inside individual assignment flows  
6. Voice input / voice output  
7. Widget chrome localization into 9 languages (EN / AR / ES / FR / DE / PT / HI / BN / UR) is **now in scope** and delivered. Remaining language layers still out of MVP scope: AI reply language (conversational responses remain in English), localized skill names inside AI answers, and localized navigation words inside the AI system prompt — all tracked under the L.3 / L.4 / L.5 rows of the Roadmap page.  
8. Full GDPR / compliance workflow, data retention policies, audit logs  
9. Mobile-specific UI optimization (web). Note: **native mobile apps (iOS / Android)** are out of MVP scope and tracked separately below under Next iterations.

# **Next iterations — post-MVP scope candidates**

This BRD scopes the MVP delivered in the 2-week qualification project. The items below are the product's planned next iterations once MVP is validated. Each will be scoped as its own BRD before work starts. Detailed engineering status and effort estimates are tracked in the [Roadmap page](https://knowledgecity.atlassian.net/wiki/spaces/PRODUCT/pages/3901751347).

1. **Mobile apps integration (iOS / Android)** — embed the AI Assistant inside the KC mobile apps. The portal-side backend (/v2/ai\_assistant/chat, /v2/ai\_assistant/overdue) and the ai\_assistant MUI group are **already usable as-is** by the mobile apps — no duplicate translations, no duplicate business logic. Remaining work is mobile-side:  
   * Native UI surface for the widget (FAB-equivalent in mobile UX — likely tab-bar icon, overflow menu, or long-press gesture)  
   * Native rendering of each panel state (empty / chat / error / overdue banner / suggested prompts)  
   * Session continuity across device and web (requires Redis-backed conversation persistence — Roadmap 2.3.3)  
   * Push notifications for overdue items via the existing KC notifications-center (Roadmap 2.4.4)  
   * Offline handling — queue messages when offline, surface a clear offline state  
   * Platform-specific accessibility (VoiceOver / TalkBack) using the same aria semantics already delivered for the web widget  
   * Localized chrome in all 9 languages consumed directly from the ai\_assistant MUI group via the mobile app's existing translation layer  
2. **Full multi-language parity** — complete the remaining localization layers: **L.3** (skill names inside AI answers), **L.4** (AI reply language — the conversation itself), **L.5** (navigation words inside the AI system prompt so the AI references UI elements by the words the student actually sees). Estimated \~2 engineering days once product confirms the target language list and the AI-reply-language question (follow portal UI lang vs. profile lang).  
3. **Native-reviewer sweep** for all 9 languages — MVP translations are first-pass machine-consistent with existing portal terminology but may contain idiom awkwardness. One review pass per language before production (AR / UR / HI / BN especially). Tracked as Roadmap 3.7.  
4. **Production readiness** — cost optimisations (prompt caching / trimming), provider hardening (failover, retries), observability (structured logging, Grafana dashboards), compliance (GDPR DPA with chosen AI vendor, audit logs, student opt-out). Tracked in Roadmap Phase 2\.  
5. **Course-content RAG** — AI answers grounded in course video transcripts and quizzes, not just metadata. Tracked as Roadmap 2.4.2.  
6. **Proactive push notifications** — overdue deadlines via email / browser / mobile push. Dependency for the Mobile apps item above. Tracked as Roadmap 2.4.4.  
7. **Unified AI surface with AI Teacher** — currently the course page uses AI Teacher (in-course learning context), all other pages use AI Assistant (portal navigation context). Consolidate into one entry point with two modes. Discovery needed before scoping. Tracked as Roadmap 3.4.

# **Constraints**

* **Timeline:** \~11 working days, solo contributor, with AI assistance (Claude Code) as development partner  
* **Stack:** SvelteKit frontend (existing), TypeScript; backend built on existing PHP ResourceV3 framework (kc-apps/api), reusing project auth, DB, and deployment pipeline  
* **AI provider:** OpenAI-compatible streaming (Groq Llama 3.3 70B for MVP; client is provider-agnostic and can switch to Anthropic/OpenAI/OpenRouter via config)  
* **Data:** no real production data access; mock data or local dev API at best  
* **Scope discipline:** anything that cannot be demonstrated in the 5–10 min demo does not belong in MVP  
* **Deployment:** local only; no production hosting

# 

# **Potential risks**

| Risk | Impact | Mitigation |
| :---- | :---- | :---- |
| AI provider latency or quota limits break the "under 3s" criterion | High | Use streaming; switch to a smaller/faster model (e.g. Llama 3.1 8B via Groq) for simple intents if needed; cache common prompts |
| Scope creep eats the demo window | High | Hard-freeze scope at day 7; day 8–11 is integration, polish, demo recording |
| Portal is hard to run locally (config, env, auth) | Medium | Day 1 spent exclusively on local bootstrap; mock user if auth blocks progress |
| Hallucinated course recommendations undermine credibility | Medium | Ground every recommendation on a fetched, real course object; if no course matches, say so |
| AI Teacher coexistence confusion | Low | Explicit naming in UI ("AI Assistant" vs "AI Teacher"); separate entry points |
| Machine-quality of non-English translations (BN / UR / HI especially) misses native nuance | Low | First-pass translations consistent with existing P2Faq / P2MyLearning terminology; a native-reviewer sweep is scheduled before prod rollout. Widget falls back to English if a key is missing or MUI hasn't loaded yet. |

### 