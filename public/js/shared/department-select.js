
async function loadActiveDepartments(selectTarget, options = {}) {
    const departmentSelect =
        typeof selectTarget === 'string'
            ? document.getElementById(selectTarget)
            : selectTarget;

    if (!departmentSelect) {
        return;
    }

    const {
        placeholder = 'Select Department',
        selectedId = ''
    } = options;

    try {
        const response = await fetch('/api/departments', {
            method: 'GET',
            credentials: 'include'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to load departments.'
            );
        }

        const departments = Array.isArray(result.data)
            ? result.data
            : [];

        departmentSelect.replaceChildren(
            new Option(placeholder, '')
        );

        departments
            .filter(department => department.status === 'active')
            .forEach(department => {
                departmentSelect.add(
                    new Option(
                        department.department_name,
                        String(department.id)
                    )
                );
            });

        departmentSelect.value = String(selectedId);
    } catch (error) {
        departmentSelect.replaceChildren(
            new Option('Unable to load departments', '')
        );

        throw error;
    }
}

window.loadActiveDepartments = loadActiveDepartments;
