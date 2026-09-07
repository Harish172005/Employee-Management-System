async function loadDashboard() {
    const summaryContainer = document.getElementById('dashboardSummary');
    const departmentContainer = document.getElementById('departmentEmployeeCounts');

    if (!summaryContainer || !departmentContainer) {
        return;
    }

    try {
        const response = await fetch('/api/dashboard', {
            method: 'GET',
            credentials: 'include'
        });
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Unable to load dashboard data.');
        }

        renderDashboard(result.data);
    } catch (error) {
        console.error('Dashboard loading error:', error);
        summaryContainer.innerHTML = `
            <div class="col-12 text-danger text-center">
                ${escapeDashboardHtml(error.message)}
            </div>
        `;
        departmentContainer.innerHTML = '';
    }
}

function renderDashboard(data) {
    const summaryContainer = document.getElementById('dashboardSummary');
    const departmentContainer = document.getElementById('departmentEmployeeCounts');
    const summaryCards = [
        ['Total Employees', data.totalEmployees, 'primary'],
        ['Active Employees', data.activeEmployees, 'success'],
        ['Inactive Employees', data.inactiveEmployees, 'secondary'],
        ['Departments', data.totalDepartments, 'info']
    ];

    summaryContainer.innerHTML = summaryCards.map(([label, count, color]) => `
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-${color} shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-2">${label}</p>
                    <p class="display-6 fw-semibold mb-0 text-${color}">${count}</p>
                </div>
            </div>
        </div>
    `).join('');

    const departments = Array.isArray(data.departments) ? data.departments : [];

    departmentContainer.innerHTML = departments.length
        ? departments.map(department => `
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <span class="badge bg-${department.status === 'active' ? 'success' : 'secondary'} float-end">
                            ${escapeDashboardHtml(department.status)}
                        </span>
                        <h3 class="h6 pe-4">${escapeDashboardHtml(department.name)}</h3>
                        <p class="mb-0 text-muted">
                            <span class="fs-4 fw-semibold text-dark">${department.employeeCount}</span>
                            employee${department.employeeCount === 1 ? '' : 's'}
                        </p>
                    </div>
                </div>
            </div>
        `).join('')
        : '<div class="col-12 text-muted">No departments found.</div>';
}

function escapeDashboardHtml(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
}

document.addEventListener('DOMContentLoaded', loadDashboard);
