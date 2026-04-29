import { Routes, Route, Navigate, useLocation } from "react-router-dom";

// layouts
import BaseLayout from "@/layouts/BaseLayout";
import LoginLayout from "@/layouts/LoginLayout";

// pages
import ArticlePage from "@/pages/articles/ArticlePage";
import ContactPage from "@/pages/contacts/ContactPage";
import DashboardPage from "@/pages/dashboard/DashboardPage";
import LoginPage from "@/pages/login/LoginPage";
import TutorialPage from "@/pages/tutorials/TutorialPage";
import UploadVideoPage from "@/pages/upload_videos/UploadVideoPage";
import UserPage from "@/pages/users/UserPage";
import ProtectedRoute from "@/components/admin/ProtectedRoute";
import { useEffect } from "react";
import { publicRoutes } from "./routes.config";

const AppRoutes = () => {
    const location = useLocation();
    useEffect(() => {
        const publicPath = publicRoutes.find((route) => route.path === location.pathname);
        console.log(location.pathname);
        if (publicPath) {
            if (publicPath.title) {
                document.title = publicPath.title;
            }
        }
    }, [location]);

    return (
        <Routes>
            {/* Admin Login */}
            <Route element={<LoginLayout />}>
                <Route path="/admin/login" element={<LoginPage />} />
            </Route>

            {/* Protected Route for other pages */}
            <Route element={<ProtectedRoute />}>
                <Route path="/admin" element={<BaseLayout />}>
                    <Route index element={<DashboardPage />} />
                    <Route path="dashboard" element={<DashboardPage />} />
                    <Route path="article" element={<ArticlePage />} />
                    <Route path="contact" element={<ContactPage />} />
                    <Route path="tutorial" element={<TutorialPage />} />
                    <Route path="upload-video" element={<UploadVideoPage />} />
                    <Route path="users" element={<UserPage />} />
                </Route>
            </Route>

            {/* Redirect to the root */}
            <Route path="/" element={<Navigate to="/admin" replace />} />
        </Routes>
    );
};

export default AppRoutes;
