import useAuthStore from "@/hooks/stores/useAuthStore";
import { Outlet, Navigate } from "react-router-dom";

function ProtectedRoute() {
    const { accessToken } = useAuthStore();
    return accessToken ? <Outlet /> : <Navigate to="/account/login" replace />;
}

export default ProtectedRoute;
