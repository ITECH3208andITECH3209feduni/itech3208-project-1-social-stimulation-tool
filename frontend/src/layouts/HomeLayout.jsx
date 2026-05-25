import { Outlet } from "react-router-dom";
import Navbar from "@/components/common/Navbar";
import Footer from "@/components/common/footer/Footer";
import FeedbackPopup from "@/components/common/FeedbackPopup";
import { Container } from "@chakra-ui/react";

function HomeLayout() {
    return (
        <Container fluid>
            <Navbar />
            <Outlet />
            <Footer />
            <FeedbackPopup />
        </Container>
    );
}

export default HomeLayout;
