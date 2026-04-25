# Roadmap — AI Student Assistant on Portal

Scope: AI Feature MVP BRD (User Stories US-01..US-04)

Status: MVP Delivered Locally · Pre-Production Hardening In Progress

Version: 1.0 | Last Sync: Post-Submission Audit

---

## 📊 Executive Summary

The roadmap is organised into three phases. Phase 0 is the qualification MVP (delivered locally). Phases 1 and 2 cover pre-production hardening. Phase 3 contains post-launch evolution candidates, with native mobile integration as the primary next target.

Key Achievements:

* ✅ Streaming API client retries transient errors (429/5xx) up to 3 times with exponential backoff. Delta-emission guard prevents duplicate content.  
* ✅ Structured logging in the backend handler emits one JSON line per request with latency, context, and sanitized errors. Ready for dashboard integration.  
* ✅ Localization fixes: Urdu and Arabic banner text verified end-to-end. Chat-state localization now shares the same translation system references as the empty state.  
* ✅ Security polish: Provider identifiers and billing URLs are stripped from client-facing errors. Server logs retain full details for diagnostics.  
* ✅ Prompt consistency: Honest-fallback literals are fully localized and deduplicated. Both system prompt emission points now resolve to the same student-language value.  
* ✅ Data propagation: Assignments endpoint now accepts a language parameter, ensuring downstream consumers receive localized titles automatically.  
* ✅ Testing: Unit tests cover localization helpers with isolated, random-suffixed keys. Greeting logic verified across EN/AR/UR without surfacing overdue tasks on bare greetings.

Deployment Strategy: Feature branches are self-contained. Production rollout will merge frontend/migration changes directly and cherry-pick API updates to ensure stable integration with the current stable branch.

---

## Phase 0 — MVP (Qualification Build)

Goal: Demonstrable build satisfying US-01..US-04 within an 11-day timeline.

Status: 16/16 items delivered. Widget localization added to scope and shipped. TTFB reduced to 516ms after streaming buffer fix.

0.1 | Floating widget on all non-course pages | ✅ | US-01

0.2 | Panel UI: 4 states, context chip, clear-chat, banners | ✅ | US-01, US-02

0.3 | Page-context detection (14 URL patterns) | ✅ | Added settings/calendar sub-contexts

0.4 | 3 suggested prompts per page type | ✅ | US-02

0.5 | Session-scoped conversation history | ✅ | US-02

0.6 | Streaming response, first token \<3s | ✅ | TTFB: 516ms post-fix

0.7 | Overdue/upcoming assignments on open | ✅ | Endpoint now forwards student language

0.8 | Skill-gap data injected into prompt | ✅ | —

0.9 | FAQ content from live admin database | ✅ | \[Fix\] Migrated from legacy blob to structured system

0.10 | Platform knowledge sourced from real code values | ✅ | —

0.11 | Clickable course cards (structured format) | ✅ | \[Fix\] Extended to 6 fields for date/status localization

0.12 | Skills-based recommendations with progress | ✅ | US-03

0.13 | Honesty rule: no fabrication when data missing | ✅ | \[Fix\] Fully localized, deduplicated emission

0.14 | FAQ pulled in student's language | ✅ | —

0.15 | Course titles with default-language fallback | ✅ | \[Fix\] Endpoints now pass student language

0.16 | Widget chrome localized in 9 languages | ✅ | EN/AR/ES/FR/DE/PT/HI/BN/UR

MVP Acceptance Met: Reachable everywhere (except course page) · \<3s first token · context-aware · profile-based recommendations · overdue surfaced · independent backend.

---

## Phase 1 — Pre-production Hardening

### 1.1 Prompt Quality & Reliability

1.1.1 | 3 suggested prompts per page type | ✅ | Closes BRD requirement

1.1.2 | Tighten recommendation reasoning | ❌ | Avoids generic replies

1.1.3 | "This month statistics" for My Progress | ❌ | Enables real-number progress summaries

1.1.4 | "Saved courses" section | ❌ | Prevents honest-fallback triggers

1.1.5 | Trim system prompt (\~50% reduction) | ❌ | Halves cost at scale; provider free-tier limits apply to high volume

1.1.6 | Automated regression tests for prompt rules | ❌ | Prevents quality drift

1.1.7–1.1.10 | Per-tab prompts (Settings, Calendar, Progress, FAQ) | ✅ | Landed

1.1.11 | Trim history by token budget, not count | ❌ | Currently capped by message count

1.1.12 | PHPUnit coverage for AI endpoints | ✅ | 3 files, 11 tests

1.1.13 | Greeting behavior fix | ✅ | Context-aware greeting logic × 9 langs

1.1.14 | FAQ source migration | ✅ | Full category coverage restored

1.1.15 | Honest-fallback template localized | ✅ | Shared helper, deduplicated

### 1.2 UX Polish

1.2.1 | Tooltip on hover/focus | ✅ | Landed

1.2.2 | Error banner with cause \+ retry | ✅ | Localized ×9

1.2.3 | Preserve partial text on mid-stream error | ✅ | Landed

1.2.4 | Retry logic with backoff | ✅ | Exponential backoff, delta guard

1.2.5 | Accessibility audit (ARIA) | ✅ | All labels from translation system

1.2.6 | Dynamic primary-color branding | ✅ | Landed

1.2.7 | Symmetric card spacing | ✅ | Landed

1.2.8 | Stale breadcrumbs suppression | ⚠ | Re-verification pending API quota availability

1.2.9 | Universal panel subtitle | ✅ | Reads from dedicated translation key

1.2.10 | Input/prompt alignment | ✅ | Landed

1.2.11 | Mobile-responsive panel | ✅ | Mobile web only

1.2.12 | Portal corner-radius scale | ✅ | Landed

1.2.13 | "Try again" bubble duplication fix | ⚠ | Re-verification pending API quota availability

1.2.14 | RTL mirroring (FAB, panel, tooltip) | ✅ | Verified during localization testing

1.2.15 | RTL send-icon flip | ✅ | Live-verified

1.2.16 | Overdue banner localization fix | ✅ | Urdu/Arabic verified

1.2.17 | CARD left-align on RTL | ✅ | Landed

1.2.18 | Confidentiality refusal localized | ✅ | Translation key ×9 langs

1.2.19 | SSE TTFB fix | ✅ | 15.9s → 516ms via buffer-flush workaround

1.2.20 | Chat-state localization persistence | ✅ | Fixed hardcoded fallbacks

1.2.21 | CARD chrome i18n (date/status) | ✅ | Structured format \+ shared date utilities

### 1.3 Observability & Safety

1.3.1 | Stack-trace logging on exceptions | ✅ | —

1.3.2 | Console logging for SSE failures | ✅ | Always on

1.3.3 | Structured logging per request | ⚠ | Hook landed; dashboard wiring pending

1.3.4 | PII/output filter | ❌ | Provider identifiers stripped; full content filter pending

1.3.5 | Prompt-injection hardening | ✅ | 5-rule confidentiality block \+ localized refusal

---

## 🌍 Multi-Language Support Parity

Status: Widget chrome fully localized (9 languages). Three verbatim AI literals localized. Remaining gap: free-form AI prose around cards (L.4), gated on multilingual provider switch.

L.0 | Layout mirroring (RTL) | ✅

L.1 | FAQ content | ✅

L.2 | Course titles in cards | ✅

L.3 | Skill names | ❌ | \~½ day effort

L.4 | Free-form AI reply language | ❌ | Blocked on Phase 2.1 provider switch

L.5 | Navigation names in prompt | ❌ | \~1 day effort

L.6 | Suggested prompts | ✅

L.7 | Static UI strings | ✅

---

## Phase 2 — Production Readiness

### 2.1 Provider, Cost & Scale

Provider / Model | Quality | Prompt Caching | Multilingual

Google Gemini 2.0 Flash | Good, very fast | ✅ up to −75% | Excellent

DeepSeek V3 | Very good | ⚠ limited | Strong

OpenAI gpt-4o-mini | Excellent | ✅ −50% | Excellent

Current Provider (Open-Source) | Good | ❌ | Good

Self-Hosted Model | Good | Full control | Good

Recommendation: Start on Gemini 2.0 Flash with prompt caching.

All Phase 2.1 items 🔒 post-MVP

### 2.2 Anti-Abuse

* Per-student soft caps: ❌ Not planned  
* Abuse detection (\>500/day): 🔒  
* Per-IP burst limits: 🔒  
* Client-side content filter: 🔒

### 2.3 Infrastructure

* Real streaming replacement: 🔒 (Unlocks live tokens, removes padding workaround)  
* Horizontal scaling: 🔒  
* Redis conversation persistence: 🔒  
* Prometheus/Grafana dashboards: 🔒  
* Feature flags & gradual rollout: 🔒

### 2.4 Qualification Brief Coverage

Requirement | MVP | Production Plan

Platform questions | ✅ | Keep as-is

Video-content questions | ❌ | RAG pipeline over transcripts

Overdue surfacing | ✅ | Keep as-is

Push notifications | ❌ | Daily scan via platform notifications

Profile recommendations | ✅ | Extend with collaborative filtering

All pages coverage | ✅ | Course page excluded (AI Teacher)

\<3s response | ✅ | Monitor p95 ≤ 3s (Current: 516ms)

### 2.5 Compliance & Governance

* GDPR DPA: 🔒  
* Audit logs: 🔒  
* Student opt-out: 🔒  
* Red-team testing: 🔒  
* "AI-generated" disclaimer: ✅

---

## Phase 3 — Post-Launch Evolution

3.1 | Voice in/out | Whisper STT \+ native TTS

3.2 | Predictive recommendations | Proactive suggestions after hesitation

3.3 | Admin insights dashboard | Top questions, confusion topics, per-client

3.4 | Unified AI surface | Merge AI Assistant & AI Teacher entry points

3.5 | Agentic actions | "Mark started", "Remind tomorrow"

3.6 | Extended knowledge base | Learning paths, certifications, events

3.7 | Native reviewer sweep | Human nuance validation for 9 languages

3.8 | Mobile apps integration | Reuses existing endpoints & translation layer

