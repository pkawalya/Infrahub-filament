@extends('mobile.layout', ['active' => 'notifications'])
@section('title', 'Field Notifications & Alerts — InfraHub Mobile')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <h1 class="m-page-title" style="margin-bottom:0;">Notifications</h1>
        <button type="button" class="m-btn m-btn-outline" style="width:auto;padding:0.4rem 0.85rem;font-size:0.75rem;" onclick="NotificationsUI.markAllAsRead()">
            Mark all as read
        </button>
    </div>
    <p class="m-page-subtitle">Site updates, approval requests & system alerts</p>

    {{-- Category Filters --}}
    <div class="m-category-tabs" style="margin-bottom:1rem;">
        <button type="button" class="m-category-tab active" data-filter="all" onclick="NotificationsUI.filter('all', this)">
            All Notifications (<span id="notif-total-cnt">0</span>)
        </button>
        <button type="button" class="m-category-tab" data-filter="unread" onclick="NotificationsUI.filter('unread', this)">
            Unread (<span id="notif-unread-cnt">0</span>)
        </button>
        <button type="button" class="m-category-tab" data-filter="safety" onclick="NotificationsUI.filter('safety', this)">
            HSE & Safety
        </button>
        <button type="button" class="m-category-tab" data-filter="tasks" onclick="NotificationsUI.filter('tasks', this)">
            Tasks & RFIs
        </button>
    </div>

    {{-- Notifications List Container --}}
    <div id="notifications-container">
        {{-- Skeleton Loader --}}
        <div class="m-card">
            <div class="m-skeleton" style="height:20px;width:60%;margin-bottom:0.5rem;"></div>
            <div class="m-skeleton" style="height:14px;width:90%;margin-bottom:0.4rem;"></div>
            <div class="m-skeleton" style="height:12px;width:40%;"></div>
        </div>
        <div class="m-card">
            <div class="m-skeleton" style="height:20px;width:70%;margin-bottom:0.5rem;"></div>
            <div class="m-skeleton" style="height:14px;width:85%;margin-bottom:0.4rem;"></div>
            <div class="m-skeleton" style="height:12px;width:30%;"></div>
        </div>
    </div>

    <div style="text-align:center;margin-top:2rem;font-size:0.72rem;color:var(--text-dim);font-weight:600;">
        InfraHub Field Dispatch · Real-time System Telemetry
    </div>
@endsection

@push('scripts')
    <script>
        const NotificationsUI = {
            data: [],

            init() {
                this.loadNotifications();
            },

            loadNotifications() {
                const stored = localStorage.getItem('m_notifications_data');
                if (stored) {
                    try {
                        this.data = JSON.parse(stored);
                    } catch (e) { this.data = []; }
                }

                if (!this.data || this.data.length === 0) {
                    this.data = [
                        {
                            id: 'n1',
                            title: 'HSE Safety Triage Required',
                            message: 'High severity hazard reported on Section B Tower Crane site.',
                            category: 'safety',
                            status: 'critical',
                            read: false,
                            time: '10 mins ago',
                            link: '/mobile/safety'
                        },
                        {
                            id: 'n2',
                            title: 'Daily Site Diary Submitted',
                            message: 'Concrete gang completed Pour 4B. Awaiting PM sign-off.',
                            category: 'tasks',
                            status: 'in_progress',
                            read: false,
                            time: '45 mins ago',
                            link: '/mobile/diaries'
                        },
                        {
                            id: 'n3',
                            title: 'Technical RFI #104 Answered',
                            message: 'Structural engineer uploaded revised foundation layout drawing.',
                            category: 'tasks',
                            status: 'completed',
                            read: true,
                            time: '2 hours ago',
                            link: '/mobile/rfis'
                        },
                        {
                            id: 'n4',
                            title: 'Subcontractor IPC Certificate Issued',
                            message: 'BuildCorp IPC #08 approved for payment processing.',
                            category: 'system',
                            status: 'active',
                            read: true,
                            time: 'Yesterday',
                            link: '/mobile/financials'
                        }
                    ];
                    this.saveData();
                }

                this.render();
            },

            saveData() {
                localStorage.setItem('m_notifications_data', JSON.stringify(this.data));
                this.updateHeaderBadge();
            },

            updateHeaderBadge() {
                const unreadCount = this.data.filter(n => !n.read).length;
                const badge = document.getElementById('notif-count');
                if (badge) {
                    badge.textContent = unreadCount;
                    badge.style.display = unreadCount > 0 ? 'flex' : 'none';
                }
            },

            render(filter = 'all') {
                const container = document.getElementById('notifications-container');
                if (!container) return;

                let items = this.data;
                if (filter === 'unread') {
                    items = items.filter(n => !n.read);
                } else if (filter !== 'all') {
                    items = items.filter(n => n.category === filter);
                }

                document.getElementById('notif-total-cnt').textContent = this.data.length;
                document.getElementById('notif-unread-cnt').textContent = this.data.filter(n => !n.read).length;

                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="m-empty">
                            <div class="m-empty-icon">🔔</div>
                            <div class="m-empty-title">No notifications</div>
                            <div class="m-empty-text">You are all caught up on site alerts & tasks.</div>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = items.map(n => `
                    <div class="m-card ${!n.read ? 'unread-card' : ''}" style="${!n.read ? 'border-left: 3px solid var(--accent); background: rgba(30,41,59,0.7);' : ''}">
                        <div class="m-card-header">
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;gap:0.4rem;">
                                    <div class="m-card-title">${this.escapeHtml(n.title)}</div>
                                    ${!n.read ? '<span style="width:8px;height:8px;border-radius:50%;background:var(--accent);display:inline-block;"></span>' : ''}
                                </div>
                                <div class="m-card-subtitle" style="margin-top:0.25rem;">${this.escapeHtml(n.message)}</div>
                            </div>
                            <span class="m-pill ${n.status}">${n.status.replace('_', ' ')}</span>
                        </div>
                        <div class="m-card-footer" style="justify-content:space-between;">
                            <span>🕒 ${n.time}</span>
                            <div style="display:flex;gap:0.5rem;">
                                ${!n.read ? `<button type="button" onclick="NotificationsUI.markRead('${n.id}')" style="background:none;border:none;color:var(--accent-light);font-size:0.75rem;font-weight:700;cursor:pointer;">Mark read</button>` : ''}
                                ${n.link ? `<a href="${n.link}" class="m-section-link">View Details →</a>` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');

                this.updateHeaderBadge();
            },

            filter(type, btn) {
                document.querySelectorAll('.m-category-tab').forEach(b => b.classList.remove('active'));
                if (btn) btn.classList.add('active');
                this.render(type);
            },

            markRead(id) {
                const item = this.data.find(n => n.id === id);
                if (item) {
                    item.read = true;
                    this.saveData();
                    const activeFilter = document.querySelector('.m-category-tab.active')?.getAttribute('data-filter') || 'all';
                    this.render(activeFilter);
                }
            },

            markAllAsRead() {
                this.data.forEach(n => n.read = true);
                this.saveData();
                const activeFilter = document.querySelector('.m-category-tab.active')?.getAttribute('data-filter') || 'all';
                this.render(activeFilter);
                MobileUI.toast('All notifications marked as read ✓');
            },

            escapeHtml(str) {
                return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            NotificationsUI.init();
        });
    </script>
@endpush
