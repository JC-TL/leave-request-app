/**
 * Safe accessors for Laravel relations that may be serialized as snake_case.
 * Use these when displaying nested relation data from Inertia props.
 */

/**
 * Get a relation from an object, trying both camelCase and snake_case keys.
 */
export function getRelation(obj, camelKey) {
    if (!obj) return null;
    const snakeKey = camelKey.replace(/([A-Z])/g, '_$1').toLowerCase().replace(/^_/, '');
    return obj[camelKey] ?? obj[snakeKey] ?? null;
}

/**
 * Get nested relation name (e.g. manager name, department name).
 */
export function getRelationName(obj, relationKey) {
    const rel = getRelation(obj, relationKey);
    if (rel == null) return null;
    if (typeof rel === 'string') return rel;
    return rel.name ?? rel.leave_type ?? null;
}
