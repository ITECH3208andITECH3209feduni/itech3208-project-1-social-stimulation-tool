import PropTypes from "prop-types";
import { Menu, Avatar, Text, Portal, Button, Box, Image } from "@chakra-ui/react";
import { useNavigate } from "react-router-dom";
import useAuthStore from "@/hooks/stores/useAuthStore";
import { toaster } from "@/components/ui/toaster";

function UserMenu({ displayName }) {
    const navigate = useNavigate();
    const { clearAuth } = useAuthStore();

    const handleMenuAction = ({ value }) => {
        switch (value) {
            case "profile-update":
                navigate("/account/update-profile");
                break;

            case "send-feedback":
                // navigate("/feedback");
                break;

            case "logout":
                clearAuth();
                toaster.create({
                    description: "Logout successfully",
                    type: "success",
                });
                break;
        }
    };

    return (
        <Box
            w="9"
            h="9"
            borderRadius="full"
            bg="brand.500"
            color="white"
            display="flex"
            alignItems="center"
            justifyContent="center"
            cursor="pointer"
        >
            <Menu.Root
                positioning={{ placement: "bottom-end" }}
                bg="blue"
                onSelect={handleMenuAction}
            >
                <Menu.Trigger asChild>
                    <span
                        style={{
                            width: "100%",
                            height: "100%",
                            color: "white",
                            fontSize: "12px",
                            fontWeight: "bold",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                        }}
                    >
                        AP
                    </span>
                </Menu.Trigger>
                <Portal>
                    <Menu.Positioner>
                        <Menu.Content bg={"white"}>
                            <Menu.Item value="profile-update">Update Your Profile</Menu.Item>
                            <Menu.Item value="send-feeback">Send Feeback</Menu.Item>
                            <Menu.Item
                                value="logout"
                                color="fg.error"
                                _hover={{ bg: "bg.error", color: "fg.error" }}
                            >
                                Logout
                            </Menu.Item>
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
