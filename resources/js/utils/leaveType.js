/**
 * Safely extract leave type name from various data structures.
 * Handles Laravel's snake_case serialization where leave_type can be the full object.
 * Prefers leave_type_name (appended from backend) when available.
 */
export function getLeaveTypeName(data) {
    if (data?.leave_type_name && typeof data.leave_type_name === 'string') {
        return data.leave_type_name;
    }
    const lt = data?.leaveType ?? data?.leave_type;
    if (typeof lt === 'string') return lt;
    if (lt && typeof lt === 'object' && lt.leave_type) return lt.leave_type;
    return 'Unknown';
}
