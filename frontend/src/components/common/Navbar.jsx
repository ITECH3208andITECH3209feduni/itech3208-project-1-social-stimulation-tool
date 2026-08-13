import { Flex, HStack, Box, Image } from "@chakra-ui/react";
import { Link, NavLink } from "react-router-dom";
import { federationLogo } from "@/assets";
import useAuthStore from "@/hooks/stores/useAuthStore";
import UserMenu from "./menu/UserMenu";

const Navbar = () => {
    const navStyle = ({ isActive }) => ({
        color: isActive ? "#0032FF" : "black",
        fontweight: isActive ? "600" : "800",
        borderBottom: isActive ? "2px solid #0032FF" : "none",
        paddingBottom: "4px",
    });

    const accessToken = useAuthStore((state) => state.accessToken);

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
                <NavLink to="/" style={navStyle}>
                    Home
                </NavLink>
                <NavLink to="/about" style={navStyle}>
                    About
                </NavLink>
                <NavLink to="/tutorial" style={navStyle}>
                    Tutorial
                </NavLink>
                <NavLink to="/contact" style={navStyle}>
                    Contact
                </NavLink>
                <NavLink to="/terms" style={navStyle}>
                    Terms & Conditions
                </NavLink>
                {accessToken ? (
                    <> </>
                ) : (
                    <NavLink to="/account" style={navStyle}>
                        Account
                    </NavLink>
                )}

                {accessToken && <UserMenu />}
            </HStack>
        </Flex>
    );
};

export default Navbar;
