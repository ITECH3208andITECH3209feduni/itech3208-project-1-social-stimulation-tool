import { Flex, HStack, Box, Image } from "@chakra-ui/react";
import { Link, NavLink } from "react-router-dom";
import { federationLogo } from "@/assets";

const Navbar = () => {
        const navStyle = ({isActive}) => ({
        color: isActive ? "#0032FF" : "black",
        fontweight: isActive? "600":"800",
        borderBottom: isActive ? "2px solid #0032FF" : "none",
  paddingBottom: "4px",
    })
    return (
        <Flex
            as="nav"
            align="center"
            justify="space-between"
            px="100px"
            h="80px"
            borderBottom="1px solid"
            borderColor="gray.200"
            bg="white"
            gap="6"
            color="black"
            fontSize={16}
        >
            <Link to="/">
                <Image
                    src={federationLogo}
                    alt="Federation University"
                    h="60px"
                    cursor={"pointer"}
                />
            </Link>
            <HStack gap="8">
                <NavLink to="/" style={navStyle}>Home</NavLink>
                <NavLink to="/about"style={navStyle}>About</NavLink>
                <NavLink to="/tutorial"style={navStyle}>Tutorial</NavLink>
                <NavLink to="/contact"style={navStyle}>Contact</NavLink>
                <NavLink to="/terms"style={navStyle}>Terms & Conditions</NavLink>
                <NavLink to="/account"style={navStyle}>Account</NavLink>

                <Box
                    w="9"
                    h="9"
                    borderRadius="full"
                    bg="red.500"
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    cursor="pointer"
                >
                    <span style={{ color: "white", fontSize: "12px", fontWeight: "bold" }}>AP</span>
                </Box>
            </HStack>
        </Flex>
    );
};

export default Navbar;
