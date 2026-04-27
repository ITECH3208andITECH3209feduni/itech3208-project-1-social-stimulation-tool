import { Link, Outlet, useLocation } from "react-router-dom";
import { MdKeyboardDoubleArrowLeft } from "react-icons/md";
import { Box, Center, Flex, HStack, Image, Text } from "@chakra-ui/react";
import { accountImage, registerImage } from "@/assets";

function AccountLayout() {
    const location = useLocation();
    const isRegister = location.pathname.includes("register");
    return (
        <Flex h="100vh" backgroundColor="white">
            {/* Left — image + icon */}
            <Box position="relative" flex="2">
                <Image
                    src={isRegister ? registerImage : accountImage}
                    alt="background"
                    w="100%"
                    h="100vh"
                    objectPosition="center"
                    objectFit="cover"
                />

                {/* Back icon on the top left*/}
                <Box position="absolute" top="20px" left="20px">
                    <Link to="/">
                        <HStack gap="-2">
                            <MdKeyboardDoubleArrowLeft
                                size={30}
                                color={isRegister ? "black" : "white"}
                            />
                            <Text fontFamily="Sora" color={isRegister ? "black" : "white"}>
                                Back
                            </Text>
                        </HStack>
                    </Link>
                </Box>
            </Box>

            {/* Right — form */}
            <Box flex={"1"}>
                <Flex w={"100%"} h={"100%"} justify={"center"} alignItems={"center"}>
                    <Outlet />
                </Flex>
            </Box>
        </Flex>
    );
}

export default AccountLayout;
