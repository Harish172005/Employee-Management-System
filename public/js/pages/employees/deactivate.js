// ============================================================
// Deactivate Employee
// ============================================================

async function deactivateEmployee(employeeId) {

    if (
        !confirm(
            'Are you sure you want to deactivate this employee?'
        )
    ) {
        return;
    }

    try {

        const response = await fetch(
            `/api/employees/${employeeId}`,
            {
                method: 'DELETE',
                credentials: 'include'
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.message ||
                'Unable to deactivate employee.'
            );
        }

        alert(
            result.message ||
            'Employee deactivated successfully.'
        );

        await fetchEmployees();

    } catch (error) {

        alert('Error: ' + error.message);

    }
}
