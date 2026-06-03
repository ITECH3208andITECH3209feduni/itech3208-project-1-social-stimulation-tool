import PropTypes from "prop-types";
import { Menu, Avatar, Text, Portal, Button, Box, Image, Spinner } from "@chakra-ui/react";
import { useNavigate } from "react-router-dom";
import useAuthStore from "@/hooks/stores/useAuthStore";
import { toaster } from "@/components/ui/toaster";
import useUserProfile from "@/hooks/custom-hooks/useUserProfile";

function UserMenu({ displayName }) {
    const navigate = useNavigate();
    const { clearAuth } = useAuthStore();
    const { data: user } = useUserProfile();

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
                    <Box position="relative" w="100%" h="100%" rounded="full" overflow="hidden">
                        <Image src={user?.avatar?.url} width="100%" height="100%" rounded={"full"}/>
                        {user?.isUploading && (
                            <Box
                                position="absolute"
                                top={0}
                                left={0}
                                right={0}
                                bottom={0}
                                bg="blackAlpha.500"
                                display="flex"
                                justifyContent="center"
                                alignItems="center"
                                rounded="full"
                            >
                                <Spinner size="sm" color="white" />
                            </Box>
                        )}
                    </Box>
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
