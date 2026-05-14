import { Navigate, Outlet } from "react-router-dom";

function ProtectedRoute() {
    const isAuthenticated = true; // This line is for testing only, will replace when API is done
    return isAuthenticated ? <Outlet /> : <Navigate to="/admin/login" replace />;
}

export default ProtectedRoute;
