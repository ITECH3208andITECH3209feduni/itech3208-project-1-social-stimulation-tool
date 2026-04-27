import { Outlet } from "react-router-dom";
import Navbar from "@/components/common/Navbar";
import Footer from "@/components/common/footer/Footer";
import { Container } from "@chakra-ui/react";

function HomeLayout() {
    return (
        <Container fluid>
            <Navbar />
            <Outlet />
            <Footer />
        </Container>
    );
}

export default HomeLayout;
