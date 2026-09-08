import useAuthStore from "@/hooks/stores/useAuthStore";
import { Outlet, Navigate, useLocation } from "react-router-dom";

function ProtectedRoute() {
    const { accessToken } = useAuthStore();
    const location = useLocation();

    return accessToken ? (
        <Outlet />
    ) : (
        <Navigate to="/account/login" replace state={{ from: location }} />
    );
}

export default ProtectedRoute;
