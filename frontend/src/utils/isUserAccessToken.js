import { jwtDecode } from "jwt-decode";

const USER_ROLES = new Set(["individual", "organization"]);

/**
 * The public frontend is only for individual and organization accounts.
 * Backend authorization remains the source of truth; this prevents the user
 * UI from presenting authenticated-only controls for an admin token.
 */
const isUserAccessToken = (accessToken) => {
    if (!accessToken) return false;

    try {
        const { role, exp } = jwtDecode(accessToken);
        const isExpired = exp && exp * 1000 <= Date.now();

        return USER_ROLES.has(role) && !isExpired;
    } catch {
        return false;
    }
};

export default isUserAccessToken;
