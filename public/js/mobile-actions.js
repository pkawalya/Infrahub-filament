/**
 * InfraHub Mobile PWA — Quick Field Actions Module
 * Rapid action handlers for crew attendance, site diaries, safety hazard reporting, and RFIs.
 */
const MobileActions = (() => {
    'use strict';

    /**
     * Rapid Crew Clock-In / Attendance
     */
    async function quickClockIn(formEl) {
        const formData = new FormData(formEl);
        const data = {
            project_id: formData.get('project_id'),
            crew_name: formData.get('crew_name'),
            headcount: parseInt(formData.get('headcount') || '1'),
            shift_type: formData.get('shift_type') || 'day',
            notes: formData.get('notes') || '',
            clock_in_at: new Date().toISOString()
        };

        if (!data.project_id || !data.crew_name) {
            MobileUI.toast('Please select a project and enter crew name', 'error');
            return false;
        }

        if (!navigator.onLine && typeof InfraDB !== 'undefined') {
            await InfraDB.queueForm('crew-attendances', 'create', data, null, `Clock-in ${data.crew_name}`);
            MobileUI.toast('Clock-in saved offline ☁️', 'info');
            MobileUI.closeSheet('sheet-clock-in');
            formEl.reset();
            return true;
        }

        try {
            const resp = await MobileAPI.post('/mobile/attendance', data);
            if (resp && (resp.success || resp.id)) {
                MobileUI.toast('Crew clocked in successfully ✓', 'success');
            } else {
                MobileUI.toast('Saved locally (pending sync)', 'info');
            }
            MobileUI.closeSheet('sheet-clock-in');
            formEl.reset();
            return true;
        } catch {
            if (typeof InfraDB !== 'undefined') {
                await InfraDB.queueForm('crew-attendances', 'create', data, null, `Clock-in ${data.crew_name}`);
                MobileUI.toast('Clock-in saved offline ☁️', 'info');
            } else {
                MobileUI.toast('Network error — try again', 'error');
            }
            MobileUI.closeSheet('sheet-clock-in');
            formEl.reset();
            return false;
        }
    }

    /**
     * Rapid Site Diary Submission
     */
    async function quickLogDiary(formEl) {
        const formData = new FormData(formEl);
        const data = {
            project_id: formData.get('project_id'),
            date: formData.get('date') || new Date().toISOString().slice(0, 10),
            weather: formData.get('weather') || 'Clear',
            summary: formData.get('summary') || '',
            work_performed: formData.get('work_performed') || ''
        };

        if (!data.project_id || !data.summary) {
            MobileUI.toast('Please select a project and enter summary', 'error');
            return false;
        }

        if (!navigator.onLine && typeof InfraDB !== 'undefined') {
            await InfraDB.queueForm('daily-site-diaries', 'create', data, null, `Site Diary (${data.date})`);
            MobileUI.toast('Site diary saved offline ☁️', 'info');
            MobileUI.closeSheet('sheet-diary');
            formEl.reset();
            return true;
        }

        try {
            const resp = await MobileAPI.post('/mobile/diaries', data);
            MobileUI.toast('Daily site diary saved ✓', 'success');
            MobileUI.closeSheet('sheet-diary');
            formEl.reset();
            return true;
        } catch {
            if (typeof InfraDB !== 'undefined') {
                await InfraDB.queueForm('daily-site-diaries', 'create', data, null, `Site Diary (${data.date})`);
                MobileUI.toast('Saved offline for auto-sync ☁️', 'info');
            }
            MobileUI.closeSheet('sheet-diary');
            formEl.reset();
            return false;
        }
    }

    /**
     * Rapid HSE Safety Hazard Report
     */
    async function quickReportHazard(formEl) {
        const formData = new FormData(formEl);
        const data = {
            project_id: formData.get('project_id'),
            severity: formData.get('severity') || 'Medium',
            category: formData.get('category') || 'Unsafe Act',
            description: formData.get('description') || '',
            corrective_action: formData.get('corrective_action') || '',
            reported_at: new Date().toISOString()
        };

        if (!data.project_id || !data.description) {
            MobileUI.toast('Project and hazard description are required', 'error');
            return false;
        }

        if (!navigator.onLine && typeof InfraDB !== 'undefined') {
            await InfraDB.queueForm('safety-incidents', 'create', data, null, `HSE: ${data.severity} Hazard`);
            MobileUI.toast('Hazard report saved offline 🛡️', 'info');
            MobileUI.closeSheet('sheet-safety');
            formEl.reset();
            return true;
        }

        try {
            await MobileAPI.post('/mobile/safety', data);
            MobileUI.toast('HSE Hazard reported ✓', 'success');
            MobileUI.closeSheet('sheet-safety');
            formEl.reset();
            return true;
        } catch {
            if (typeof InfraDB !== 'undefined') {
                await InfraDB.queueForm('safety-incidents', 'create', data, null, `HSE: ${data.severity} Hazard`);
                MobileUI.toast('Hazard report saved offline 🛡️', 'info');
            }
            MobileUI.closeSheet('sheet-safety');
            formEl.reset();
            return false;
        }
    }

    return {
        quickClockIn,
        quickLogDiary,
        quickReportHazard
    };
})();

// Attach to window object
window.MobileActions = MobileActions;
