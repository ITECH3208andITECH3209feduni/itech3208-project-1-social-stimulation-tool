import { Button, CloseButton, Drawer, Portal, Text, HStack } from "@chakra-ui/react";

function UserDetailDrawer({ isOpen, onClose, onSave }) {
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
                    <Drawer.Content bg={"dark.900"}>
                        <Drawer.Header>
                            <Drawer.Title>Update Record</Drawer.Title>
                        </Drawer.Header>
                        <Drawer.Body>
                            <Text>This drawer for showing user form in updating</Text>
                        </Drawer.Body>
                        <Drawer.Footer>
                            <HStack w={200}>
                                <Button flex={1} bg={"brand.300"} onClick={() => onClose()}>
                                    Cancel
                                </Button>
                                <Button flex={1} bg={"skyblue.300"} onClick={onSave}>
                                    Save
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
