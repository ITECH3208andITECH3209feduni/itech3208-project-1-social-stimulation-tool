import { Outlet } from "react-router-dom";
import Navbar from "@/components/common/Navbar";
import Footer from "@/components/common/footer/Footer";
import { Box, Flex } from "@chakra-ui/react";

function HomeLayout() {
    return (
        <Flex direction={"column"} minH={"100vh"}>
            <Navbar />
            <Box px={"100px"} flex={"1"}>
                <Outlet />
            </Box>
            <Footer />
        </Flex>
    );
}

export default HomeLayout;
