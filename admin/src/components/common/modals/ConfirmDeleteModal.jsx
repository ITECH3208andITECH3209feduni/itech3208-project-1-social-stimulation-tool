import { Button, CloseButton, Dialog, HStack, Portal, Text } from "@chakra-ui/react";

function ConfirmDeleteModal({ isOpen, onClose, onConfirm, isLoading, title, message }) {
    return (
        <Dialog.Root
            open={isOpen}
            placement="center"
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Dialog.Backdrop />
                <Dialog.Positioner>
                    <Dialog.Content bg="dark.900" color="whiteAlpha.900" maxW="450px">
                        <Dialog.Header borderBottomWidth="1px" borderColor="whiteAlpha.200">
                            <Dialog.Title fontSize="lg" fontWeight="bold">
                                {title || "Confirm Deletion"}
                            </Dialog.Title>
                        </Dialog.Header>
                        <Dialog.Body py={5}>
                            <Text color="gray.300">
                                {message || "Are you sure you want to delete this record? This action cannot be undone."}
                            </Text>
                        </Dialog.Body>
                        <Dialog.Footer borderTopWidth="1px" borderColor="whiteAlpha.100">
                            <HStack justify="flex-end" gap={3} w="full">
                                <Button
                                    size="sm"
                                    fontSize="xs"
                                    px={4}
                                    variant="outline"
                                    borderColor="whiteAlpha.300"
                                    color="whiteAlpha.800"
                                    _hover={{ bg: "whiteAlpha.100", color: "white" }}
                                    onClick={onClose}
                                    disabled={isLoading}
                                >
                                    Cancel
                                </Button>
                                <Button
                                    size="sm"
                                    fontSize="xs"
                                    px={4}
                                    bg="red.600"
                                    _hover={{ bg: "red.500" }}
                                    color="white"
                                    fontWeight="semibold"
                                    onClick={onConfirm}
                                    loading={isLoading}
                                >
                                    Delete
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

export default ConfirmDeleteModal;
