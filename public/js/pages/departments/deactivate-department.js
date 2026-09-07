async function deactivateDepartment(departmentId) {
    if (!Number.isInteger(Number(departmentId)) || Number(departmentId) <= 0) {
        alert('Invalid department ID.');
        return;
    }

    if (!confirm('Are you sure you want to deactivate this department?')) {
        return;
    }

    try {
        const response = await fetch(`/api/departments/${departmentId}`, {
            method: 'DELETE',
            credentials: 'include'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message || 'Unable to deactivate department.'
            );
        }

        alert(result.message || 'Department deactivated successfully.');

        if (typeof fetchDepartments === 'function') {
            await fetchDepartments();
        }
    } catch (error) {
        console.error('Department deactivation error:', error);
        alert(`Error: ${error.message}`);
    }
}
