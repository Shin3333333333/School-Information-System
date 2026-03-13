@extends('layouts.app')

@section('title', 'Class Schedule — School Information System')

@section('page-title')
<h2>Class Schedule</h2>
@endsection

@section('content')
<div class="filter-bar">
    <div class="filter-group">
        <label class="filter-label">Filter by Subject</label>
        <select class="form-select">
            <option value="">All Subjects</option>
            <option>Math</option>
            <option>Science</option>
            <option>History</option>
        </select>
    </div>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Time</th>
            <th>Day</th>
            <th>Teacher</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Math</td>
            <td>10:00 AM - 11:00 AM</td>
            <td>Monday</td>
            <td>Mr. Santos</td>
        </tr>
        <tr>
            <td>Science</td>
            <td>11:00 AM - 12:00 PM</td>
            <td>Monday</td>
            <td>Ms. Reyes</td>
        </tr>
    </tbody>
</table>
@endsection