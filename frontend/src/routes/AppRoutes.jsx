import { Routes, Route, Navigate } from "react-router-dom";
import useDocumentTitle from "@/hooks/useDocumentTitle";

// layouts
import HomeLayout from "@/layouts/HomeLayout";
import AccountLayout from "@/layouts/AccountLayout";
import TutorialLayout from "@/layouts/TutorialLayout";

// pages
import HomePage from "@/pages/home/HomePage";
import AboutPage from "@/pages/about/AboutPage";
import TutorialPage from "@/pages/tutorial/TutorialPage";
import ContactPage from "@/pages/contact/ContactPage";
import TermsConditionsPage from "@/pages/terms&conditions/TermsConditionsPage";
import LoginPage from "@/pages/account/LoginPage";
import RegisterPage from "@/pages/account/RegisterPage";

const DocumentTitle = ({ title, children }) => {
    useDocumentTitle(title);
    return children;
};

const AppRoutes = () => {
    return (
        <Routes>
            <Route element={<HomeLayout />}>
                <Route path="/" element={<DocumentTitle title="Home"><HomePage /></DocumentTitle>}></Route>
                <Route path="/about">
                    <Route index element={<DocumentTitle title="About Us"><AboutPage /></DocumentTitle>} />
                    <Route path="sub-page-1" />
                    <Route path="sub-page-2" />
                </Route>
                <Route path="/contact" element={<DocumentTitle title="Contact"><ContactPage /></DocumentTitle>} />
                <Route path="/terms" element={<DocumentTitle title="Terms & Conditions"><TermsConditionsPage /></DocumentTitle>} />
            </Route>
            <Route element={<TutorialLayout />}>
                <Route path="/tutorial" element={<DocumentTitle title="Tutorial"><TutorialPage /></DocumentTitle>} />
            </Route>
            <Route path="/account" element={<AccountLayout />}>
                <Route index element={<Navigate to="login" />} />
                <Route path="login" element={<DocumentTitle title="Login"><LoginPage /></DocumentTitle>} />
                <Route path="register" element={<DocumentTitle title="Register"><RegisterPage /></DocumentTitle>} />
            </Route>
        </Routes>
    );
};

export default AppRoutes;
