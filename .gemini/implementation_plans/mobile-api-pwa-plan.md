# Mobile API & PWA App — Implementation Plan

## Current State (What Already Exists)

### ✅ API Infrastructure
- **Auth**: Sanctum token-based (`POST /api/v1/auth/login`, `/register`, `/logout`, `/me`)
- **Projects**: Full CRUD + stats (`/api/v1/projects`)
- **Documents**: CRUD + review workflow (scoped to project)
- **Tasks**: CRUD + progress updates (scoped to project)
- **RFIs & Submittals**: Full lifecycle (scoped to project)
- **Work Orders**: Full CRUD (scoped to project)
- **Site Diaries**: CRUD + approval
- **Crew Attendance**: CRUD + today view
- **Equipment**: Allocations + fuel logs
- **Safety Incidents**: CRUD
- **Offline Sync**: Generic queue + per-module sync endpoints
- **Permission middleware**: `module:{code}.{action}` pattern

### ✅ PWA Infrastructure
- **manifest.json**: Icons, shortcuts, standalone display
- **Service Worker (v3)**: Cache-first for assets, network-first for pages/API, background sync
- **IndexedDB**: Offline form queue for diaries, attendance, safety
- **Offline page**: Fallback when no cache exists

### ❌ What's Missing (Gaps to Fill)

| Gap | Impact |
|-----|--------|
| No company profile API | Mobile can't show company info/logo |
| No company members/team API | Can't list/invite team on mobile |
| No project members API | Can't view/manage project team |
| No project invitations API | Can't invite people from mobile |
| No suggestion box API | Can't submit/view suggestions on mobile |
| No notifications API | Can't show in-app notifications |
| No dashboard summary API | No unified "home" data for mobile |
| No file download tokens | Documents can't be viewed offline |
| PWA is basically the Filament web app | No dedicated mobile-first UI |

---

## Implementation Plan

### Phase 1: Complete the REST API (Backend Only)
> **Goal**: Every feature accessible from the Filament panel must also be available via API.
> **Estimated files**: ~8 new controllers + route registration

#### 1.1 Company API
```
GET    /api/v1/company              → company profile, logo, settings
PUT    /api/v1/company              → update company profile
GET    /api/v1/company/members      → list team members
POST   /api/v1/company/members      → add a team member
DELETE /api/v1/company/members/{id} → remove a team member
GET    /api/v1/company/modules      → list enabled modules
```

#### 1.2 Dashboard Summary API
```
GET /api/v1/dashboard → unified stats for mobile home screen
```
Returns:
- Active projects count + list (name, code, status)
- Open tasks count (assigned to current user)
- Overdue tasks count
- Recent notifications (5)
- Pending offline items hint
- Today's attendance status

#### 1.3 Project Members API
```
GET    /api/v1/projects/{project}/members           → list members
POST   /api/v1/projects/{project}/members           → add member
DELETE /api/v1/projects/{project}/members/{userId}  → remove member
POST   /api/v1/projects/{project}/invitations       → invite by email
GET    /api/v1/projects/{project}/invitations       → list pending invites
DELETE /api/v1/projects/{project}/invitations/{id}  → revoke invite
```

#### 1.4 Suggestion Box API
```
GET    /api/v1/projects/{project}/suggestions       → list suggestions
POST   /api/v1/projects/{project}/suggestions       → submit (anonymous toggle)
POST   /api/v1/projects/{project}/suggestions/{id}/upvote   → upvote
POST   /api/v1/projects/{project}/suggestions/{id}/respond  → admin response
DELETE /api/v1/projects/{project}/suggestions/{id}  → delete
```

#### 1.5 Notifications API
```
GET   /api/v1/notifications         → paginated notifications
POST  /api/v1/notifications/read    → mark as read (bulk)
GET   /api/v1/notifications/unread-count → badge count
```

#### 1.6 User Profile API
```
GET  /api/v1/profile                → full profile with company info
PUT  /api/v1/profile                → update name, avatar
POST /api/v1/profile/change-password → change password
```

#### 1.7 Contracts API (project-scoped)
```
GET    /api/v1/projects/{project}/contracts       → list contracts
GET    /api/v1/projects/{project}/contracts/{id}  → contract detail
POST   /api/v1/projects/{project}/contracts       → create contract
```

#### 1.8 Financials API (project-scoped)
```
GET /api/v1/projects/{project}/financials/summary → income/expense totals
GET /api/v1/projects/{project}/invoices           → list invoices
GET /api/v1/projects/{project}/expenses           → list expenses
```

---

### Phase 2: Mobile-First PWA Shell
> **Goal**: A lightweight, mobile-optimized UI that works as a standalone PWA.
> **Approach**: Build as Blade views (not React/Vue) — keeps the stack unified, works with existing PWA infra.
> **Route prefix**: `/mobile`

#### 2.1 Mobile App Shell (`/mobile`)
- Bottom tab navigation: **Home**, **Projects**, **Forms**, **Alerts**, **Profile**
- Minimal CSS (no Filament overhead) — fast load for 3G connections
- Dark theme matching InfraHub brand (#0f172a background, #6366f1 accent)
- Pull-to-refresh via JS
- Install prompt banner

#### 2.2 Mobile Pages

| Page | Route | Features |
|------|-------|----------|
| **Login** | `/mobile/login` | Email + password, "Remember me", biometric prompt (WebAuthn) |
| **Home Dashboard** | `/mobile` | Project count, my tasks, overdue alerts, quick action buttons |
| **My Projects** | `/mobile/projects` | Filterable list, tap to drill into project |
| **Project Detail** | `/mobile/projects/{id}` | Stats, team, recent activity, module shortcuts |
| **My Tasks** | `/mobile/tasks` | Assigned to me, swipe to complete, tap to view |
| **Task Detail** | `/mobile/tasks/{id}` | Description, status update, progress slider, comments |
| **Site Diary Form** | `/mobile/diary/create` | Offline-ready form with GPS auto-fill |
| **Attendance** | `/mobile/attendance` | Clock in/out, today's crew list |
| **Safety Report** | `/mobile/safety/create` | Quick incident form with camera capture |
| **Suggestion Box** | `/mobile/projects/{id}/suggestions` | Submit + browse suggestions |
| **Notifications** | `/mobile/notifications` | Filterable notification list |
| **Profile** | `/mobile/profile` | Edit name, change password, logout |

#### 2.3 Offline-First Architecture
- Use existing IndexedDB (`infrahub-offline`) stores
- Every form saves to IndexedDB first, syncs when online
- Project list & task list cached in IndexedDB for offline browsing
- Background sync via existing service worker
- "Offline mode" indicator in the nav bar

#### 2.4 Mobile-Specific Features
- **Camera integration**: Attach photos to safety incidents, site diaries
- **GPS auto-detection**: Auto-fill location for field logs
- **Push notifications**: Task assignments, safety alerts, deadline reminders
- **Haptic feedback**: On form submission, task completion
- **Swipe gestures**: Swipe task to mark done, swipe notification to dismiss

---

### Phase 3: Enhanced PWA Capabilities
> **Goal**: Bridge the gap between a website and a native app.

#### 3.1 Push Notification Registration
```
POST /api/v1/push/subscribe    → save push subscription
POST /api/v1/push/unsubscribe  → remove subscription
```
- Server-side: Use `web-push` PHP library or `laravel-notification-channels/webpush`
- Trigger on: task assignment, safety incident, deadline approaching, suggestion response

#### 3.2 Media Upload API
```
POST /api/v1/media/upload → multipart upload (photos, docs)
```
- Returns a media_id that can be attached to diaries, incidents, etc.
- Compress images client-side before upload (canvas resize to 1200px max)
- Queue uploads when offline, sync when online

#### 3.3 Share Target
- Register as a Web Share Target in manifest.json
- Allow sharing photos directly from the phone's gallery to create a site diary entry

---

## File Structure

```
app/Http/Controllers/Api/
├── AuthController.php          ← EXISTS
├── BaseApiController.php       ← EXISTS
├── CompanyController.php       ← NEW
├── DashboardController.php     ← NEW
├── NotificationController.php  ← NEW
├── ProfileController.php       ← NEW
├── ProjectController.php       ← EXISTS (extend with members/invites)
├── SuggestionController.php    ← NEW
├── MediaController.php         ← NEW (Phase 3)
├── PushController.php          ← NEW (Phase 3)
└── ...existing controllers...

app/Http/Controllers/Mobile/
├── MobileController.php        ← Main shell + all mobile page rendering
└── MobileAuthController.php    ← Login/logout for mobile

resources/views/mobile/
├── layout.blade.php            ← App shell with bottom nav
├── login.blade.php
├── home.blade.php
├── projects/
│   ├── index.blade.php
│   └── show.blade.php
├── tasks/
│   ├── index.blade.php
│   └── show.blade.php
├── forms/
│   ├── diary.blade.php
│   ├── attendance.blade.php
│   └── safety.blade.php
├── suggestions.blade.php
├── notifications.blade.php
└── profile.blade.php

public/
├── css/mobile.css              ← Mobile-specific styles
├── js/mobile-app.js            ← Mobile app logic + API client
├── sw.js                       ← EXISTS (update for mobile routes)
└── manifest.json               ← EXISTS (add mobile shortcuts)

routes/
├── api.php                     ← EXISTS (extend with new routes)
└── web.php                     ← EXISTS (add /mobile routes)
```

---

## Implementation Order

| Step | What | Est. Effort |
|------|------|-------------|
| **1** | Phase 1.2: Dashboard API | 30 min |
| **2** | Phase 1.6: Profile API | 20 min |
| **3** | Phase 1.1: Company API | 30 min |
| **4** | Phase 1.3: Project Members API | 30 min |
| **5** | Phase 1.4: Suggestion Box API | 25 min |
| **6** | Phase 1.5: Notifications API | 20 min |
| **7** | Phase 2.1: Mobile app shell (layout + CSS) | 45 min |
| **8** | Phase 2.2: Login page | 20 min |
| **9** | Phase 2.2: Home dashboard | 30 min |
| **10** | Phase 2.2: Projects list + detail | 40 min |
| **11** | Phase 2.2: Tasks list + detail | 35 min |
| **12** | Phase 2.2: Offline forms (diary, attendance, safety) | 40 min |
| **13** | Phase 2.2: Suggestions, Notifications, Profile | 30 min |
| **14** | Phase 2.3: Offline caching + sync | 30 min |
| **15** | Phase 2.4: Camera, GPS, push prompts | 30 min |
| **16** | Phase 3: Push registration + media upload | 40 min |

**Total estimated: ~8 hours**

---

## Key Design Decisions

1. **Blade views (not SPA)** — Stays in the Laravel ecosystem, no separate build toolchain, works with existing service worker caching. Each page is a lightweight HTML document that calls the API via `fetch()`.

2. **Separate `/mobile` routes** — Doesn't interfere with the existing Filament `/app` panel. Users can bookmark `/mobile` as their PWA entry point for phones.

3. **API-first** — Every mobile page loads its data from the API. This means third-party apps or future native apps can use the same endpoints.

4. **Offline by default** — Every form saves to IndexedDB first, then syncs. The mobile UI shows data from cache when offline. This is critical for construction field workers with spotty connectivity.

5. **No new JS framework** — Using vanilla JS with `fetch()` for API calls. Keeps bundle size tiny (<50KB) and load time fast.

---

## Ready to Start?

Say **"go"** and I'll start with **Phase 1** (completing the API), then move to **Phase 2** (mobile PWA shell). Or tell me which phase/step to prioritize.
