<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileController extends Controller
{
    /**
     * Mobile PWA Suite Gate Controller
     * Client-side hydrated Blade views for field operations.
     */

    public function home()
    {
        return view('mobile.home', ['active' => 'home']);
    }

    public function login()
    {
        return view('mobile.login');
    }

    public function projects()
    {
        return view('mobile.projects.index', ['active' => 'projects']);
    }

    public function projectShow(int $id)
    {
        return view('mobile.projects.show', ['active' => 'projects', 'id' => $id]);
    }

    public function tasks()
    {
        return view('mobile.tasks', ['active' => 'tasks']);
    }

    public function forms()
    {
        return view('mobile.forms', ['active' => 'forms']);
    }

    public function profile()
    {
        return view('mobile.profile', ['active' => 'profile']);
    }

    public function notifications()
    {
        return view('mobile.profile', ['active' => 'notifications']);
    }

    public function diaries()
    {
        return view('mobile.modules.diaries', ['active' => 'diaries']);
    }

    public function attendance()
    {
        return view('mobile.modules.attendance', ['active' => 'attendance']);
    }

    public function safety()
    {
        return view('mobile.modules.safety', ['active' => 'safety']);
    }

    public function equipment()
    {
        return view('mobile.modules.equipment', ['active' => 'equipment']);
    }

    public function drawings()
    {
        return view('mobile.modules.drawings', ['active' => 'drawings']);
    }

    public function documents()
    {
        return view('mobile.modules.drawings', ['active' => 'drawings']);
    }

    public function financials()
    {
        return view('mobile.modules.financials', ['active' => 'financials']);
    }

    public function subcontractors()
    {
        return view('mobile.modules.subcontractors', ['active' => 'subcontractors']);
    }

    public function tenders()
    {
        return view('mobile.modules.tenders', ['active' => 'tenders']);
    }

    public function rfis()
    {
        return view('mobile.modules.rfis', ['active' => 'rfis']);
    }

    public function materials()
    {
        return view('mobile.modules.materials', ['active' => 'materials']);
    }

    public function changeOrders()
    {
        return view('mobile.modules.change-orders', ['active' => 'change-orders']);
    }

    public function workOrders()
    {
        return view('mobile.modules.work-orders', ['active' => 'work-orders']);
    }

    public function quality()
    {
        return view('mobile.modules.quality', ['active' => 'quality']);
    }

    public function approvals()
    {
        return view('mobile.modules.approvals', ['active' => 'approvals']);
    }

    public function boq()
    {
        return view('mobile.modules.boq', ['active' => 'boq']);
    }

    public function planning()
    {
        return view('mobile.modules.planning', ['active' => 'planning']);
    }

    public function suggestions()
    {
        return view('mobile.modules.suggestions', ['active' => 'suggestions']);
    }

    public function reporting()
    {
        return view('mobile.modules.reporting', ['active' => 'reporting']);
    }
}
