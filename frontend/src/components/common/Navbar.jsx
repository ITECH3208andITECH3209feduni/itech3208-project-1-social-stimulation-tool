import { Flex, HStack, Box, Image } from "@chakra-ui/react";
import { Link, NavLink } from "react-router-dom";
import { federationLogo } from "@/assets";
import useAuthStore from "@/hooks/stores/useAuthStore";
import isUserAccessToken from "@/utils/isUserAccessToken";
import UserMenu from "./menu/UserMenu";

const Navbar = () => {
    const navStyle = ({ isActive }) => ({
        color: isActive ? "#0032FF" : "black",
        fontweight: isActive ? "600" : "800",
        borderBottom: isActive ? "2px solid #0032FF" : "none",
        paddingBottom: "4px",
    });

    const accessToken = useAuthStore((state) => state.accessToken);
    const isUserLoggedIn = isUserAccessToken(accessToken);

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
                {/* MARK: Home */}
                <NavLink to="/" style={navStyle}>
                    Home
                </NavLink>

                {/* MARK: Scenarios Videos — available after login */}
                {isUserLoggedIn && (
                    <NavLink to="/scenarios" style={navStyle}>
                        Scenarios
                    </NavLink>
                )}

                {/* MARK: About */}
                <NavLink to="/about" style={navStyle}>
                    About
                </NavLink>

                {/* MARK: Tutorial */}
                <NavLink to="/tutorial" style={navStyle}>
                    Tutorial
                </NavLink>

                {/* MARK: Contact */}
                <NavLink to="/contact" style={navStyle}>
                    Contact
                </NavLink>

                {/* MARK: Terms and Conditions */}
                <NavLink to="/terms" style={navStyle}>
                    Terms & Conditions
                </NavLink>

                {/* MARK: Account -> Login / Register */}
                {isUserLoggedIn ? (
                    <> </>
                ) : (
                    <NavLink to="/account" style={navStyle}>
                        Account
                    </NavLink>
                )}

                {/* MARK: Show User Menu */}
                {isUserLoggedIn && <UserMenu />}
            </HStack>
        </Flex>
    );
};

export default Navbar;
