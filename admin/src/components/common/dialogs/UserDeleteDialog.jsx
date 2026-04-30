import { Box, Button, CloseButton, Dialog, HStack, Portal, Text } from "@chakra-ui/react";

function UserDeleteDialog({ isOpen, onClose, onSave }) {
    return (
        <Dialog.Root
            open={isOpen}
            placement={"center"}
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Dialog.Backdrop />
                <Dialog.Positioner>
                    <Dialog.Content bg={"dark.900"}>
                        <Dialog.Header>
                            <Dialog.Title>Do you want to delete this record?</Dialog.Title>
                        </Dialog.Header>
                        <Dialog.Body>
                            <Text color={"brand.200"}>
                                Warning: If you delete this record, your record will be not restore.
                                Please consider carefully when you delete it!
                            </Text>
                        </Dialog.Body>
                        <Dialog.Footer>
                            <HStack w={200}>
                                <Button flex={1} bg={"brand.300"} onClick={() => onClose()}>
                                    Cancel
                                </Button>
                                <Button flex={1} bg={"skyblue.300"} onClick={onSave}>
                                    OK
                                </Button>
                            </HStack>
                        </Dialog.Footer>
                        <Dialog.CloseTrigger asChild>
                            <CloseButton size="sm" />
                        </Dialog.CloseTrigger>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
}

export default UserDeleteDialog;
