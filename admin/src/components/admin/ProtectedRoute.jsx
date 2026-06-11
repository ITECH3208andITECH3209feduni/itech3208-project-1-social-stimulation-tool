import useAuthStore from "@/hooks/stores/useAuthStore";
import { Navigate, Outlet } from "react-router-dom";

function ProtectedRoute() {
    const { accessToken } = useAuthStore();
    return accessToken ? <Outlet /> : <Navigate to="/admin/login" replace />;
}

export default ProtectedRoute;
