<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales()
    {
        return view('admin.reports.sales');
    }

    public function products()
    {
        return view('admin.reports.products');
    }

    public function customers()
    {
        return view('admin.reports.customers');
    }
}