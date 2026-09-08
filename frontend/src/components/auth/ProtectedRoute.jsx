import useAuthStore from "@/hooks/stores/useAuthStore";
import isUserAccessToken from "@/utils/isUserAccessToken";
import { Outlet, Navigate, useLocation } from "react-router-dom";

function ProtectedRoute() {
    const { accessToken } = useAuthStore();
    const location = useLocation();

    return isUserAccessToken(accessToken) ? (
        <Outlet />
    ) : (
        <Navigate to="/account/login" replace state={{ from: location }} />
    );
}

export default ProtectedRoute;
