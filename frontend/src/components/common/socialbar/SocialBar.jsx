import { Box, HStack } from "@chakra-ui/react";
import { FaYoutube, FaFacebook, FaInstagram, FaLinkedin, FaTiktok } from "react-icons/fa";

function SocialBar() {
    return (
        <HStack fontSize={25} gap={2}>
            <Box color="navy.500">
                <FaYoutube />
            </Box>
            <Box color="navy.500">
                <FaFacebook />
            </Box>
            <Box color="navy.500">
                <FaInstagram />
            </Box>
            <Box color="navy.500">
                <FaLinkedin />
            </Box>
            <Box color="navy.500">
                <FaTiktok />
            </Box>
        </HStack>
    );
}

export default SocialBar;
