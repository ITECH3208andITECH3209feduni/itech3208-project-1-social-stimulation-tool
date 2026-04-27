import { Box, HStack, Link } from "@chakra-ui/react";
import { FaYoutube, FaFacebook, FaInstagram, FaLinkedin, FaTiktok } from "react-icons/fa";
import { fedYoutube, fedFacebook, fedInstagram, fedLinkin, fedTikTok } from "@/assets/fedUrl";

const socials = [
    { icon: FaYoutube, url: fedYoutube },
    { icon: FaFacebook, url: fedFacebook },
    { icon: FaInstagram, url: fedInstagram },
    { icon: FaLinkedin, url: fedLinkin },
    { icon: FaTiktok, url: fedTikTok },
];

function SocialBar() {
    return (
        <HStack fontSize={25} gap={2}>
            {socials.map(({ icon: Icon, url }, index) => (
                <Link key={index} href={url} isExternal>
                    <Box
                        color="navy.500"
                        _hover={{
                            color: "brand.500",
                            transform: "scale(1.2)",
                        }}
                        transition="0.2s"
                    >
                        <Icon />
                    </Box>
                </Link>
            ))}
        </HStack>
    );
}

export default SocialBar;
