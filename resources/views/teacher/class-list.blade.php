@extends('layouts.app')

@section('title', 'Class List — School Information System')

@section('page-title')
<h2>Class List</h2>
@endsection

@section('content')

{{-- Filters --}}
<div class="dashboard-widgets mb-6">
    <div style="display:flex; gap:16px; flex-wrap:wrap; align-items:flex-end;">
        {{-- Class Filter --}}
        <div style="display:flex; flex-direction:column;">
            <label class="filter-label mb-1">Select Class</label>
            <select id="classFilter" class="form-select">
                <option value="all">All Classes</option>
                <option value="math">Math</option>
                <option value="science">Science</option>
                <option value="english">English</option>
            </select>
        </div>

        {{-- Day Filter --}}
        <div style="display:flex; flex-direction:column;">
            <label class="filter-label mb-1">Select Day</label>
            <select id="dayFilter" class="form-select">
                <option value="all">All Days</option>
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
            </select>
        </div>

        {{-- Apply Filter Button --}}
        <div style="display:flex; flex-direction:column;">
            <!-- No top margin here; will align bottom with selects -->
            <button class="btn btn-primary" id="applyFiltersBtn">Apply Filters</button>
        </div>
    </div>
</div>


{{-- Class Table --}}
<div class="card card-body dashboard-widget-card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>Class / Subject</th>
                <th># of Students</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr class="hover:bg-gray-50">
                <td>Math</td>
                <td>30</td>
                <td>
                    <button class="btn btn-primary" onclick="openClassModal('Math')">View Sections</button>
                </td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td>Science</td>
                <td>25</td>
                <td>
                    <button class="btn btn-primary" onclick="openClassModal('Science')">View Sections</button>
                </td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td>English</td>
                <td>28</td>
                <td>
                    <button class="btn btn-primary" onclick="openClassModal('English')">View Sections</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- CLASS DETAILS MODAL --}}
<div id="classModal" style="display:none; position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:var(--radius-lg); width:450px; max-height:80vh; overflow-y:auto; padding:20px; box-shadow:var(--shadow-md);">
        <h3 class="section-title mb-3" id="modalClassTitle">Class Details</h3>
        <div id="modalClassContent" style="margin-bottom:15px;">
            {{-- Sections will be injected here --}}
        </div>
        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn btn-outline" onclick="closeClassModal()">Close</button>
        </div>
    </div>
</div>


<script>
    const classModal = document.getElementById('classModal');
    const modalClassTitle = document.getElementById('modalClassTitle');
    const modalClassContent = document.getElementById('modalClassContent');

    // Dummy data for class sections + students
    const classData = {
        'Math': [
            { section: 'Section A', students: 15 },
            { section: 'Section B', students: 15 }
        ],
        'Science': [
            { section: 'Section A', students: 12 },
            { section: 'Section B', students: 13 }
        ],
        'English': [
            { section: 'Section A', students: 14 },
            { section: 'Section B', students: 14 }
        ]
    };

    function openClassModal(className) {
        document.getElementById('classModal').style.display = 'flex';
        modalClassTitle.textContent = className + ' - Sections';
        modalClassContent.innerHTML = ''; // clear previous content

        const sections = classData[className];
        sections.forEach(s => {
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.justifyContent = 'space-between';
            div.style.alignItems = 'center';
            div.style.marginBottom = '8px';

            const sectionInfo = document.createElement('span');
            sectionInfo.textContent = `${s.section} (${s.students} students)`;

            const viewBtn = document.createElement('button');
            viewBtn.textContent = 'View Students';
            viewBtn.className = 'btn btn-primary';
            viewBtn.onclick = () => alert(`Viewing students for ${s.section}`);

            div.appendChild(sectionInfo);
            div.appendChild(viewBtn);

            modalClassContent.appendChild(div);
        });

        classModal.style.display = 'flex';
    }

    function closeClassModal() {
        classModal.style.display = 'none';
    }
</script>

@endsection
