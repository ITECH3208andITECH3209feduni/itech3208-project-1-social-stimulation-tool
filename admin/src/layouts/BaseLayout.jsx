import { useState } from "react";
import PropTypes from "prop-types";
import { Outlet, Link } from "react-router-dom";
import { Box, Flex, Image, Text } from "@chakra-ui/react";
import { MdLogout } from "react-icons/md";
import { federationLogo } from "@/assets";
import Sidebar from "@/components/common/sidebar/Sidebar";

function BaseLayout() {
        const [displayName, setDisplayName] = useState("User".slice(0, 2).toUpperCase());
    return (
        <Flex minH="100vh" bg="dark.900">
            {/* Sidebar */}
            <Box
                flex={"1"}
                bg="dark.800"
                color="white"
                p={4}
                display="flex"
                flexDirection="column"
                justifyContent="space-between"
            >
                {/* Logo */}
                <Box mb={4}>
                    <Link to={"/Home"}>
                        <Image
                            src={federationLogo}
                            alt="Federation University"
                            h="60px"
                            cursor={"pointer"}
                        />
                    </Link>
                </Box>
                <Sidebar />
                {/* Logout button */}
                <Flex align="center" py={2} px={2} _hover={{ cursor: "pointer" }} borderRadius="md">
                    <MdLogout />
                    <Text
                        ml={2}
                        onClick={() => {
                            // logout();
                            navigate("/auth/login", {
                                replace: true,
                            });
                        }}
                    >
                        Logout
                    </Text>
                </Flex>
            </Box>

            {/* Main content area */}
            <Flex direction="column" flex="4" overflowX="hidden">
                {/* Header */}
                <Flex justify="flex-end" bg="dark.900" p={4}>
                    {/* Avatar */}
                    <Box
                        w="40px"
                        h="40px"
                        borderRadius="full"
                        bg="brand.500"
                        color="white"
                        display="flex"
                        alignSelf="center"
                        alignItems="center"
                        justifyContent="center"
                        fontWeight="bold"
                    >
                        {displayName}
                    </Box>
                </Flex>

                {/* Page content */}
                <Box p={6} flex="1" overflow="auto">
                    <Outlet />
                </Box>
            </Flex>
        </Flex>
    );
}

BaseLayout.propTypes = {
    displayName: PropTypes.string,
};

export default BaseLayout;
