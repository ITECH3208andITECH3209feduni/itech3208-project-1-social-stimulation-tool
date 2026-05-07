import PropTypes from "prop-types";
import { Menu, Avatar, Text, Portal, Button, Box, Image } from "@chakra-ui/react";
import { useNavigate } from "react-router-dom";

function UserMenu({ displayName }) {
    const navigate = useNavigate();

    return (
        <Box w="40px" h="40px" borderRadius="full" bg="brand.500" color="white">
            <Menu.Root>
                <Menu.Trigger asChild>
                    <Image
                        src="/images/user_account.png"
                        alt="User"
                        w="100%"
                        h="100%"
                        objectFit="cover"
                    />
                </Menu.Trigger>
                <Portal>
                    <Menu.Positioner>
                        <Menu.Content bg={"white"}>
                            <Menu.Item value="export">Logout</Menu.Item>
                        </Menu.Content>
                    </Menu.Positioner>
                </Portal>
            </Menu.Root>
        </Box>
    );
}

UserMenu.propTypes = {
    displayName: PropTypes.string.isRequired,
};

export default UserMenu;
