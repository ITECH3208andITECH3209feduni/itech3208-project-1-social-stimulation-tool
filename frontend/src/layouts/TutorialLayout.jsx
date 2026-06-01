import { Outlet } from "react-router-dom";
import Navbar from "@/components/common/Navbar";
import Footer from "@/components/common/footer/Footer";
import { Box, Center, Container, Flex, Spacer } from "@chakra-ui/react";

function TutorialLayout() {
    return (
        <Flex direction={"column"} minH={"100vh"}>
            <Navbar />
            <Box flex={"1"}>
                <Outlet />
            </Box>
            <Footer />
        </Flex>
    );
}

export default TutorialLayout;
