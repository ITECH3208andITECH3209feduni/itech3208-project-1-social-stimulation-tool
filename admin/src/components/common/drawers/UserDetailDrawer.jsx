import { Button, CloseButton, Drawer, Portal, HStack, VStack, Text, Box, Flex } from "@chakra-ui/react";
import SelectionField from "../fields/SelectionField";
import useUserDetailDrawerForm from "@/hooks/users/useUserDetailDrawerForm";
import UserAvatar from "@/components/common/avatar/UserAvatar";

const STATUS_OPTIONS = [
    { id: "active", name: "Active" },
    { id: "inactive", name: "Inactive" },
    { id: "banned", name: "Banned" },
    { id: "suspended", name: "Suspended" }
];

function UserDetailDrawer({ isOpen, onClose, onSave, user }) {
    const {
        status,
        setStatus,
        handleSave,
    } = useUserDetailDrawerForm(user, isOpen, onSave);

    return (
        <Drawer.Root
            size={"lg"}
            open={isOpen}
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Drawer.Backdrop />
                <Drawer.Positioner>
                    <Drawer.Content bg={"dark.900"} color="whiteAlpha.900">
                        <Drawer.Header borderBottomWidth="1px" borderColor="whiteAlpha.200">
                            <Drawer.Title>Moderate User</Drawer.Title>
                        </Drawer.Header>
                        <Drawer.Body py={6}>
                            <VStack gap={8} align="stretch">
                                {/* Read-only User Info Section */}
                                <Flex gap={5} align="center" bg="whiteAlpha.50" p={4} rounded="md" borderWidth="1px" borderColor="whiteAlpha.100">
                                    <Box
                                        p="3px"
                                        borderRadius="full"
                                        bgGradient="to-br"
                                        gradientFrom="brand.500"
                                        gradientTo="blue.500"
                                    >
                                        <UserAvatar
                                            src={user?.avatar?.url}
                                            name={user?.username}
                                            size="xl"
                                            border="3px solid"
                                            borderColor="dark.900"
                                        />
                                    </Box>
                                    <VStack align="start" gap={1}>
                                        <Text fontSize="2xl" fontWeight="bold" color="white">
                                            {user?.fullName}
                                        </Text>
                                        <Text fontSize="md" color="brand.300">
                                            @{user?.username || user?.firstName?.toLowerCase()}
                                        </Text>
                                        <Text fontSize="sm" color="gray.400">
                                            {user?.email}
                                        </Text>
                                        {user?.phone && (
                                            <Text fontSize="sm" color="gray.400">
                                                {user?.phone}
                                            </Text>
                                        )}
                                    </VStack>
                                </Flex>

                                {/* Editable Moderation Section */}
                                <Box>
                                    <Text fontSize="lg" fontWeight="semibold" mb={4} color="brand.200">
                                        Account Settings
                                    </Text>
                                    <SelectionField
                                        items={STATUS_OPTIONS}
                                        fieldLabel="Account Status"
                                        value={status}
                                        onChange={(e) => setStatus(e.target.value)}
                                    />
                                    <Text fontSize="sm" color="gray.400" mt={2}>
                                        Updating the status will immediately affect the user's ability to access the platform.
                                    </Text>
                                </Box>
                            </VStack>
                        </Drawer.Body>
                        <Drawer.Footer borderTopWidth="1px" borderColor="whiteAlpha.200">
                            <HStack w="full" justify="flex-end" gap={3}>
                                <Button variant="ghost" onClick={() => onClose()}>
                                    Cancel
                                </Button>
                                <Button bg={"brand.500"} onClick={handleSave}>
                                    Save Changes
                                </Button>
                            </HStack>
                        </Drawer.Footer>
                        <Drawer.CloseTrigger asChild>
                            <CloseButton size="sm" />
                        </Drawer.CloseTrigger>
                    </Drawer.Content>
                </Drawer.Positioner>
            </Portal>
        </Drawer.Root>
    );
}

export default UserDetailDrawer;
