import { Routes, Route , Navigate} from "react-router-dom";

// layouts
import HomeLayout from "@/layouts/HomeLayout";
import AccountLayout from "@/layouts/AccountLayout";

// pages
import HomePage from "@/pages/home/HomePage";
import AboutPage from "@/pages/about/AboutPage";
import TutorialPage from "@/pages/tutorial/TutorialPage";
import ContactPage from "@/pages/contact/ContactPage";
import TermsConditionsPage from "@/pages/terms&conditions/TermsConditionsPage";
import LoginPage from "@/pages/account/LoginPage";
import RegisterPage from "@/pages/account/RegisterPage";


const AppRoutes = () => {
    return (
        <Routes>
            <Route element={<HomeLayout />}>
                <Route path="/" element={<HomePage />}></Route>
                <Route path="/about">
                    <Route index element={<AboutPage />} />
                    <Route path="sub-page-1" />
                    <Route path="sub-page-2" />
                </Route>
                <Route path="/tutorial" element={<TutorialPage />} />
                <Route path="/contact" element={<ContactPage />} />
                <Route path="/terms" element={<TermsConditionsPage />} />
            </Route>
            <Route path="/account" element={<AccountLayout />}>
            <Route index element={<Navigate to="login"/>} />
                <Route path="login" element={<LoginPage />} />
                <Route path="register" element={<RegisterPage />} />
            </Route>
        </Routes>
    );
};

export default AppRoutes;
